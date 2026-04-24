<?php
// gemini_debug.php - debug GEMINI_API_KEY visibility
header('Content-Type: text/plain; charset=utf-8');

function mask($s){
    if ($s === null) return '(null)';
    $len = strlen($s);
    if ($len <= 4) return str_repeat('*', $len);
    return substr($s,0,2) . str_repeat('*', max(1,$len-4)) . substr($s,-2);
}

$cwd = __DIR__ . '/../'; // project root assumption (public/..)
$env_file = realpath($cwd . '/.env');

echo "=== GEMINI DEBUG ===\n";
echo "Request time: " . date('Y-m-d H:i:s') . "\n";
echo "Working dir (assumed project root): " . $cwd . "\n";
echo ".env path found: " . ($env_file ? $env_file : '(NOT FOUND)') . "\n\n";

if ($env_file && is_readable($env_file)) {
    echo "---- First 80 chars of .env (for visual check, not full file) ----\n";
    $f = fopen($env_file,'r');
    $first = fread($f, 800);
    fclose($f);
    echo $first . "\n\n";
} else {
    echo ".env belum dapat dibaca oleh PHP user\n\n";
}

// getenv / $_ENV / $_SERVER
echo "getenv('GEMINI_API_KEY'): ";
$g = @getenv('GEMINI_API_KEY');
echo ($g === false ? '(false)' : mask($g)) . "\n";

echo "isset(\$_ENV['GEMINI_API_KEY']): ";
echo (isset($_ENV['GEMINI_API_KEY']) ? mask($_ENV['GEMINI_API_KEY']) : '(not set)') . "\n";

echo "isset(\$_SERVER['GEMINI_API_KEY']): ";
echo (isset($_SERVER['GEMINI_API_KEY']) ? mask($_SERVER['GEMINI_API_KEY']) : '(not set)') . "\n\n";

// If Laravel is available, show config() and env()
if (file_exists($cwd . '/artisan')) {
    echo "Laravel detected (artisan exists).\n";
    // try to bootstrap minimal to call config() safely
    try {
        // Try to include bootstrap/app.php and read config('services.gemini') if possible
        $app = require $cwd . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        // bootstrap minimal to allow config() usage
        $kernel->bootstrap();

        echo "env('GEMINI_API_KEY'): ";
        $val = env('GEMINI_API_KEY');
        echo ($val === null ? '(null)' : mask($val)) . "\n";

        echo "config('services.gemini'): ";
        $cfg = config('services.gemini');
        if ($cfg === null) {
            echo "(null)\n";
        } else {
            // print key if exists
            if (is_array($cfg)) {
                echo "array keys: " . implode(',', array_keys($cfg)) . "\n";
                echo "config('services.gemini.key'): " . (isset($cfg['key']) ? mask($cfg['key']) : '(not set)') . "\n";
            } else {
                echo strval($cfg) . "\n";
            }
        }
    } catch (Throwable $e) {
        echo "Failed to bootstrap Laravel: " . $e->getMessage() . "\n";
    }
} else {
    echo "Laravel NOT detected (artisan not found at expected path).\n";
}

echo "\n=== ENV FILE SUGGESTIONS ===\n";
echo "1) Pastikan baris di .env ada: GEMINI_API_KEY=\"isi_api_key_kompleks\"\n";
echo "2) Jika ada karakter # atau spasi atau ; gunakan kutip. Contoh:\n   GEMINI_API_KEY=\")Um#Mhjis}g7W,c-\"\n";
echo "3) Setelah ubah .env jangan lupa: php artisan config:clear && php artisan cache:clear && php artisan config:cache\n";
echo "4) Restart php-fpm / web server / queue workers agar FPM melihat .env terbaru.\n";
echo "5) Hapus file ini setelah debug.\n";
