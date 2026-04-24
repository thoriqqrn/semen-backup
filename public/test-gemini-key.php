<?php
// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

echo "<pre>";
echo "🔍 Testing GEMINI_API_KEY...\n\n";

// Test 1: env()
$keyFromEnv = env('GEMINI_API_KEY');
echo "1. From env(): ";
if ($keyFromEnv) {
    echo "✅ Found: " . substr($keyFromEnv, 0, 15) . "...\n";
} else {
    echo "❌ NOT FOUND\n";
}

// Test 2: config()
$keyFromConfig = config('services.gemini.api_key');
echo "2. From config(): ";
if ($keyFromConfig) {
    echo "✅ Found: " . substr($keyFromConfig, 0, 15) . "...\n";
} else {
    echo "❌ NOT FOUND\n";
}

// Test 3: Direct file read
$envContent = file_get_contents(__DIR__.'/.env');
echo "\n3. Checking .env file:\n";
if (strpos($envContent, 'GEMINI_API_KEY') !== false) {
    echo "✅ GEMINI_API_KEY exists in .env\n";
    
    // Extract the line
    preg_match('/GEMINI_API_KEY=(.*)/', $envContent, $matches);
    if (isset($matches[1])) {
        $value = trim($matches[1]);
        echo "   Value: " . substr($value, 0, 15) . "...\n";
    }
} else {
    echo "❌ GEMINI_API_KEY NOT in .env file\n";
}

echo "\n💡 If env() returns NULL but file has the key,\n";
echo "   run: php artisan config:clear\n";
echo "</pre>";
?>