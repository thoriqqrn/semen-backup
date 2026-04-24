<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "🔧 Clearing config cache...\n";
$kernel->call('config:clear');
echo "✅ Config cleared\n\n";

echo "🔧 Clearing cache...\n";
$kernel->call('cache:clear');
echo "✅ Cache cleared\n\n";

echo "🔍 Checking GEMINI_API_KEY...\n";
$geminiKey = env('GEMINI_API_KEY');

if ($geminiKey) {
    echo "✅ GEMINI_API_KEY found: " . substr($geminiKey, 0, 10) . "...\n";
} else {
    echo "❌ GEMINI_API_KEY NOT FOUND!\n";
    echo "Make sure it's in .env file\n";
}

echo "\n🎉 Done! Refresh and try again.\n";
echo "</pre>";
?>