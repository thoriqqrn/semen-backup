<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class OcrService
{
    public function getTextFromImage(string $imagePath): ?string
    {
        try {
            $fullPath = storage_path('app/public/' . $imagePath);

            if (!File::exists($fullPath)) {
                Log::warning('OCR Service: File not found at ' . $fullPath);
                return null;
            }

            $apiKey = config('services.google.vision_api_key');
            if (empty($apiKey)) {
                Log::error('OCR Service: GOOGLE_VISION_API_KEY is not set in .env file.');
                return null;
            }

            // Baca gambar dan encode ke base64
            $imageContent = base64_encode(file_get_contents($fullPath));

            // Kirim request ke Google Vision REST API
            $response = Http::timeout(30)->post(
                "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}",
                [
                    'requests' => [
                        [
                            'image' => [
                                'content' => $imageContent
                            ],
                            'features' => [
                                [
                                    'type' => 'TEXT_DETECTION'
                                ]
                            ]
                        ]
                    ]
                ]
            );

            if (!$response->successful()) {
                Log::error('Google Vision API Error: ' . $response->body());
                return null;
            }

            $result = $response->json();

            // Cek apakah ada error dari API
            if (isset($result['responses'][0]['error'])) {
                Log::error('Google Vision API Error: ' . json_encode($result['responses'][0]['error']));
                return null;
            }

            // Ambil text dari response
            $textAnnotations = $result['responses'][0]['textAnnotations'] ?? [];
            
            if (empty($textAnnotations)) {
                Log::info('OCR Service: No text found in image');
                return null;
            }

            // Text pertama adalah full text
            $fullText = $textAnnotations[0]['description'] ?? null;

            return $fullText;

        } catch (\Exception $e) {
            Log::error('Google Vision API Error: ' . $e->getMessage(), [
                'trace' => substr($e->getTraceAsString(), 0, 2000)
            ]);
            return null;
        }
    }
}