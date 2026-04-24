<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OcrKtpController extends Controller
{
    public function processKTP(Request $request)
    {
        $request->validate([
            'ktp_image' => 'required|image|mimes:jpeg,jpg,png|max:5120' // Max 5MB
        ]);

        try {
            // Get Gemini API Key
            $apiKey = env('GEMINI_API_KEY');
            if (empty($apiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API Key tidak ditemukan'
                ], 500);
            }

            // Read and encode image
            $image = $request->file('ktp_image');
            $imageContent = base64_encode(file_get_contents($image->getRealPath()));
            $mimeType = $image->getMimeType();

            // Prepare prompt untuk Gemini
            $prompt = "Baca KTP Indonesia pada gambar ini dan ekstrak informasi berikut dalam format JSON yang STRICT (tanpa markdown, tanpa backtick, hanya JSON murni):\n\n" .
                "{\n" .
                "  \"nama_lengkap\": \"nama lengkap sesuai KTP\",\n" .
                "  \"tempat_lahir\": \"kota/kabupaten tempat lahir\",\n" .
                "  \"tanggal_lahir\": \"YYYY-MM-DD\",\n" .
                "  \"alamat\": \"alamat lengkap dengan jalan, RT/RW\"\n" .
                "}\n\n" .
                "PENTING:\n" .
                "- Format tanggal harus YYYY-MM-DD (contoh: 1990-08-15)\n" .
                "- Nama harus dalam huruf kapital semua\n" .
                "- Jika ada informasi yang tidak terbaca, isi dengan string kosong \"\"\n" .
                "- Response HANYA JSON, tidak boleh ada teks lain, markdown, atau backtick";

            // Send request to Gemini API (sesuai format Admin/OcrController)
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent";
            
            $response = Http::timeout(30)
                ->withOptions(['verify' => false]) // Disable SSL verification for development
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $imageContent
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topK' => 1,
                        'topP' => 1,
                        'maxOutputTokens' => 1024
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal terhubung ke Gemini API'
                ], 500);
            }

            $result = $response->json();

            // Extract text from Gemini response
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$text) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat membaca teks dari KTP'
                ], 400);
            }

            // Clean response (remove markdown if any)
            $text = preg_replace('/```json\s*/', '', $text);
            $text = preg_replace('/```\s*/', '', $text);
            $text = trim($text);

            // Parse JSON
            $data = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Parse Error: ' . json_last_error_msg() . ' | Raw text: ' . $text);
                return response()->json([
                    'success' => false,
                    'message' => 'Format response tidak valid dari AI'
                ], 400);
            }

            // Validate required fields
            $requiredFields = ['nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'alamat'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $data[$field] = '';
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'KTP berhasil dibaca',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('OCR KTP Error: ' . $e->getMessage(), [
                'trace' => substr($e->getTraceAsString(), 0, 2000)
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
