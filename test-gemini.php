<?php
// Test Gemini API
// Akses: php test-gemini.php

$apiKey = 'AIzaSyBgZUhhKuZE8uk1AflOgczFiEpiuGxlWdM'; // Ganti dengan API key Anda

echo "Testing Gemini API...\n\n";

// Test 1: List Models v1
echo "=== Test 1: List Models (v1) ===\n";
$url = "https://generativelanguage.googleapis.com/v1/models?key={$apiKey}";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "Available models:\n";
    foreach ($data['models'] ?? [] as $model) {
        if (strpos($model['name'], 'gemini') !== false) {
            echo "  - {$model['name']}\n";
            echo "    Methods: " . implode(', ', $model['supportedGenerationMethods'] ?? []) . "\n";
        }
    }
} else {
    echo "Error: {$response}\n";
}

echo "\n";

// Test 2: List Models v1beta
echo "=== Test 2: List Models (v1beta) ===\n";
$url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "Available models:\n";
    foreach ($data['models'] ?? [] as $model) {
        if (strpos($model['name'], 'gemini') !== false) {
            echo "  - {$model['name']}\n";
            echo "    Methods: " . implode(', ', $model['supportedGenerationMethods'] ?? []) . "\n";
        }
    }
} else {
    echo "Error: {$response}\n";
}

echo "\n";

// Test 3: Simple Text Generation
echo "=== Test 3: Simple Text Generation ===\n";
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key={$apiKey}";
$data = json_encode([
    'contents' => [
        [
            'parts' => [
                ['text' => 'Hello, respond with just "OK"']
            ]
        ]
    ]
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
if ($httpCode === 200) {
    $result = json_decode($response, true);
    $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No text';
    echo "Response: {$text}\n";
} else {
    echo "Error: {$response}\n";
}