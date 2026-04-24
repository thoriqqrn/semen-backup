<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OcrController extends Controller
{
    public function processKtp(Pendaftar $pendaftar)
    {
        try {
            // Validasi file KTP ada
            if (!$pendaftar->dokumen || !$pendaftar->dokumen->file_ktp_path) {
                return response()->json(['error' => 'File KTP pendaftar tidak ditemukan.'], 404);
            }

            $imagePath = $pendaftar->dokumen->file_ktp_path;

            if (!Storage::disk('public')->exists($imagePath)) {
                return response()->json(['error' => 'File KTP tidak ada di storage.'], 404);
            }

            // Baca dan encode gambar
            $imageBytes = Storage::disk('public')->get($imagePath);
            $imageBase64 = base64_encode($imageBytes);
            $mimeType = Storage::disk('public')->mimeType($imagePath);

            // Validasi API Key
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                throw new \Exception('GEMINI_API_KEY tidak ditemukan di file .env');
            }

            Log::info('Starting OCR process with Gemini 2.5 Flash', [
                'image_path' => $imagePath,
                'mime_type' => $mimeType
            ]);

            // ===============================================
            // GUNAKAN GEMINI 2.5 FLASH (MODEL TERBARU)
            // ===============================================
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";
            
            $prompt = "Analisis gambar Kartu Tanda Penduduk (KTP) Indonesia ini dengan teliti.

Ekstrak informasi berikut:
1. NIK (16 digit angka)
2. Nama Lengkap (sesuai tertulis di KTP)
3. Tanggal Lahir (format: DD-MM-YYYY)

PENTING:
- Jika informasi tidak jelas atau tidak ditemukan, isi dengan 'Tidak Ditemukan'
- Berikan jawaban HANYA dalam format JSON yang valid
- JANGAN tambahkan markdown, penjelasan, atau teks lain
- Format: {\"nik\": \"3471...\", \"nama\": \"NAMA LENGKAP\", \"ttl\": \"02-09-1979\"}

Jawab dalam format JSON murni:";

            // Kirim request dengan header x-goog-api-key (sesuai dokumentasi terbaru)
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $imageBase64
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topK' => 1,
                        'topP' => 1,
                    ]
                ]);

            if ($response->failed()) {
                $errorData = $response->json();
                Log::error('Gemini API request failed:', $errorData);
                
                $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
                throw new \Exception('Gagal berkomunikasi dengan Gemini API: ' . $errorMessage);
            }

            // Parse response
            $responseData = $response->json();
            Log::info('Gemini API Response received', [
                'status' => 'success',
                'has_candidates' => isset($responseData['candidates'])
            ]);

            $responseText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$responseText) {
                throw new \Exception('Gemini tidak memberikan response text');
            }

            // Bersihkan response dari markdown
            $jsonResponse = trim($responseText);
            $jsonResponse = preg_replace('/```json\s*/', '', $jsonResponse);
            $jsonResponse = preg_replace('/```\s*/', '', $jsonResponse);
            $jsonResponse = trim($jsonResponse);

            Log::info('Cleaned JSON Response:', ['json' => substr($jsonResponse, 0, 200)]);

            // Decode JSON
            $ocrData = json_decode($jsonResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gemini JSON Parse Error:', [
                    'error' => json_last_error_msg(),
                    'response' => $responseText
                ]);
                throw new \Exception("AI gagal memberikan format JSON yang valid. Error: " . json_last_error_msg());
            }

            // Validasi struktur data
            if (!isset($ocrData['nik']) || !isset($ocrData['nama']) || !isset($ocrData['ttl'])) {
                Log::error('Invalid OCR data structure:', $ocrData);
                throw new \Exception('Format data dari AI tidak lengkap');
            }
            
            // --- Proses Perbandingan ---
            $namaFromForm = $pendaftar->nama_lengkap;
            $ttlFromForm = \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d-m-Y');
            
            $namaOcr = $ocrData['nama'] ?? 'Tidak Ditemukan';
            $ttlOcr = $ocrData['ttl'] ?? 'Tidak Ditemukan';

            // Normalisasi untuk perbandingan
            $normalizedNamaForm = strtoupper(trim($namaFromForm));
            $normalizedNamaOcr = strtoupper(trim($namaOcr));

            // Fuzzy matching untuk nama
            $isNamaMatch = false;
            if ($namaOcr !== 'Tidak Ditemukan') {
                $isNamaMatch = Str::contains($normalizedNamaOcr, $normalizedNamaForm) 
                            || Str::contains($normalizedNamaForm, $normalizedNamaOcr)
                            || similar_text($normalizedNamaForm, $normalizedNamaOcr) > (strlen($normalizedNamaForm) * 0.7);
            }

            // Perbandingan tanggal
            $isTtlMatch = false;
            if ($ttlOcr !== 'Tidak Ditemukan') {
                try {
                    $isTtlMatch = ($ttlOcr === $ttlFromForm) 
                               || (\Carbon\Carbon::parse($ttlOcr)->format('d-m-Y') === $ttlFromForm);
                } catch (\Exception $e) {
                    Log::warning('Date comparison failed: ' . $e->getMessage());
                }
            }

            $finalResult = [
                'ocr' => [
                    'nik' => $ocrData['nik'] ?? 'Tidak Ditemukan',
                    'nama' => $namaOcr,
                    'ttl' => $ttlOcr,
                ],
                'form' => [
                    'nama' => $namaFromForm,
                    'ttl' => $ttlFromForm
                ],
                'comparison' => [
                    'nama' => $isNamaMatch,
                    'ttl' => $isTtlMatch
                ],
                'method' => 'AI Vision (Gemini 2.5 Flash)'
            ];

            $pendaftar->update(['ocr_result' => $finalResult]);

            Log::info('OCR Process Success:', $finalResult);

            return response()->json($finalResult);

        } catch (\Exception $e) {
            Log::error('OCR Process Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ===============================================
    //           METHOD BARU UNTUK PROSES KK
    // ===============================================
    public function processKk(Pendaftar $pendaftar)
    {
        try {
            // Validasi file KK ada
            if (!$pendaftar->dokumen || !$pendaftar->dokumen->file_kk_path) {
                return response()->json(['error' => 'File Kartu Keluarga (KK) pendaftar tidak ditemukan.'], 404);
            }

            $imagePath = $pendaftar->dokumen->file_kk_path;

            if (!Storage::disk('public')->exists($imagePath)) {
                return response()->json(['error' => 'File KK tidak ada di storage.'], 404);
            }

            // Baca dan encode gambar
            $imageBytes = Storage::disk('public')->get($imagePath);
            $imageBase64 = base64_encode($imageBytes);
            $mimeType = Storage::disk('public')->mimeType($imagePath);

            // Validasi API Key
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                throw new \Exception('GEMINI_API_KEY tidak ditemukan di file .env');
            }

            Log::info('Starting KK OCR process with Gemini 2.5 Flash', [
                'pendaftar' => $pendaftar->nama_lengkap,
                'image_path' => $imagePath,
                'mime_type' => $mimeType
            ]);

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";
            
            // Prompt untuk extract SEMUA anggota keluarga
            $prompt = "Analisis gambar Kartu Keluarga (KK) Indonesia ini dengan sangat teliti.

Ekstrak informasi SEMUA anggota keluarga yang terlihat di KK. Untuk setiap anggota keluarga, ekstrak:
1. NIK (16 digit)
2. Nama Lengkap
3. Tanggal Lahir (format: DD-MM-YYYY)
4. Status dalam keluarga (Kepala Keluarga, Istri, Anak, dll)

PENTING:
- Extract SEMUA anggota keluarga yang terlihat
- Jika ada data yang tidak jelas, isi dengan 'Tidak Ditemukan'
- Berikan jawaban dalam format JSON array
- JANGAN tambahkan markdown atau penjelasan
- Format: {\"anggota\": [{\"nik\": \"...\", \"nama\": \"...\", \"ttl\": \"...\", \"status\": \"...\"}, ...]}

Contoh:
{
  \"anggota\": [
    {\"nik\": \"3471012345678901\", \"nama\": \"BUDI SANTOSO\", \"ttl\": \"15-08-1975\", \"status\": \"Kepala Keluarga\"},
    {\"nik\": \"3471012345678902\", \"nama\": \"SITI AMINAH\", \"ttl\": \"20-03-1980\", \"status\": \"Istri\"},
    {\"nik\": \"3471012345678903\", \"nama\": \"ANDI SANTOSO\", \"ttl\": \"10-05-2005\", \"status\": \"Anak\"}
  ]
}

Jawab dalam format JSON murni:";

            // Kirim request
            $response = Http::timeout(45) // Timeout lebih lama karena extract banyak data
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $imageBase64
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topK' => 1,
                        'topP' => 1,
                    ]
                ]);

            if ($response->failed()) {
                $errorData = $response->json();
                Log::error('Gemini API request failed (KK):', $errorData);
                throw new \Exception('Gagal berkomunikasi dengan Gemini API: ' . ($errorData['error']['message'] ?? 'Unknown error'));
            }

            $responseData = $response->json();
            $responseText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;
            
            if (!$responseText) {
                throw new \Exception('Gemini tidak memberikan response text');
            }

            // Bersihkan response
            $jsonResponse = trim($responseText);
            $jsonResponse = preg_replace('/```json\s*/', '', $jsonResponse);
            $jsonResponse = preg_replace('/```\s*/', '', $jsonResponse);
            $jsonResponse = trim($jsonResponse);

            Log::info('Cleaned JSON Response (KK):', ['json' => substr($jsonResponse, 0, 300)]);

            $ocrData = json_decode($jsonResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Parse Error (KK):', [
                    'error' => json_last_error_msg(),
                    'response' => $responseText
                ]);
                throw new \Exception("AI gagal memberikan format JSON yang valid.");
            }

            if (!isset($ocrData['anggota']) || !is_array($ocrData['anggota'])) {
                throw new \Exception('Format data KK tidak valid - tidak ada array anggota');
            }

            // --- CARI ANGGOTA YANG COCOK DENGAN PENDAFTAR ---
            $namaFromForm = strtoupper(trim($pendaftar->nama_lengkap));
            $ttlFromForm = \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d-m-Y');
            
            $matchedMember = null;
            $highestScore = 0;
            
            foreach ($ocrData['anggota'] as $anggota) {
                $namaOcr = strtoupper(trim($anggota['nama'] ?? ''));
                $ttlOcr = $anggota['ttl'] ?? '';
                
                if (empty($namaOcr) || $namaOcr === 'TIDAK DITEMUKAN') {
                    continue;
                }
                
                // Hitung similarity score
                $score = 0;
                
                // Score dari nama (70% bobot)
                $namaSimilarity = similar_text($namaFromForm, $namaOcr);
                $nameScore = ($namaSimilarity / max(strlen($namaFromForm), 1)) * 70;
                
                // Score dari tanggal lahir (30% bobot)
                $ttlScore = 0;
                if ($ttlOcr !== 'Tidak Ditemukan') {
                    try {
                        $ttlNormalized = \Carbon\Carbon::parse($ttlOcr)->format('d-m-Y');
                        if ($ttlNormalized === $ttlFromForm) {
                            $ttlScore = 30;
                        }
                    } catch (\Exception $e) {
                        // Tanggal tidak valid
                    }
                }
                
                $score = $nameScore + $ttlScore;
                
                Log::info("Comparing with member:", [
                    'nama' => $namaOcr,
                    'score' => $score,
                    'name_similarity' => $namaSimilarity
                ]);
                
                if ($score > $highestScore) {
                    $highestScore = $score;
                    $matchedMember = $anggota;
                }
            }

            // --- HASIL ---
            $isFound = $matchedMember !== null && $highestScore >= 50; // Threshold 50%
            
            $finalResult = [
                'success' => true,
                'total_anggota' => count($ocrData['anggota']),
                'anggota_keluarga' => $ocrData['anggota'], // Kirim semua anggota
                'pendaftar_ditemukan' => $isFound,
                'match_score' => round($highestScore, 2),
                'matched_data' => $matchedMember ? [
                    'nik' => $matchedMember['nik'] ?? 'Tidak Ditemukan',
                    'nama' => $matchedMember['nama'] ?? 'Tidak Ditemukan',
                    'ttl' => $matchedMember['ttl'] ?? 'Tidak Ditemukan',
                    'status' => $matchedMember['status'] ?? 'Tidak Ditemukan',
                ] : null,
                'form_data' => [
                    'nama' => $pendaftar->nama_lengkap,
                    'ttl' => $ttlFromForm
                ],
                'comparison' => [
                    'nama' => $isFound,
                    'ttl' => $isFound,
                    'message' => $isFound 
                        ? "Pendaftar ditemukan di KK dengan tingkat kemiripan {$highestScore}%" 
                        : "Pendaftar tidak ditemukan di KK atau kemiripan terlalu rendah"
                ],
                'method' => 'AI Vision (Gemini 2.5 Flash)',
                'document_type' => 'Kartu Keluarga'
            ];

            Log::info('KK OCR Process Success:', [
                'total_anggota' => count($ocrData['anggota']),
                'pendaftar_ditemukan' => $isFound,
                'match_score' => $highestScore
            ]);

            return response()->json($finalResult);

        } catch (\Exception $e) {
            Log::error('KK OCR Process Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint untuk verifikasi Gemini API
     */
    public function testGemini()
    {
        try {
            $apiKey = env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                throw new \Exception('GEMINI_API_KEY tidak ditemukan');
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";
            
            $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Respond with just "OK" if you can read this']
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'error' => $response->json()
                ], $response->status());
            }

            $responseText = $response->json('candidates.0.content.parts.0.text');

            return response()->json([
                'success' => true,
                'message' => 'Gemini API berfungsi dengan baik!',
                'response' => $responseText,
                'model' => 'gemini-2.5-flash'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}