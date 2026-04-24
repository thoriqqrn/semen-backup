<?php
// db_debug.php
// Letakkan file ini sementara di server (public/ atau di luar webroot dan akses via CLI).
// Hapus kembali setelah selesai debugging.

// --------- helper ----------
function mask($s) {
    if ($s === null) return null;
    $len = strlen($s);
    if ($len <= 2) return str_repeat('*', $len);
    return substr($s,0,1) . str_repeat('*', max(1,$len-2)) . substr($s,-1);
}

function read_env($path) {
    if (!file_exists($path)) return null;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $out = [];
    foreach ($lines as $l) {
        $l = trim($l);
        if ($l === '' || $l[0] === '#') continue;
        if (!strpos($l, '=')) continue;
        list($k,$v) = explode('=', $l, 2);
        $v = trim($v);
        // remove surrounding quotes
        $v = preg_replace('/^([\'"])(.*)\1$/', '$2', $v);
        $out[trim($k)] = $v;
    }
    return $out;
}

// --------- paths ----------
$cwd = __DIR__;
$env_paths = [
    $cwd . '/.env',
    dirname($cwd) . '/.env',
    dirname(dirname($cwd)) . '/.env'
];

// try to locate .env
$env = null;
$found = null;
foreach ($env_paths as $p) {
    if (file_exists($p)) {
        $env = read_env($p);
        $found = $p;
        break;
    }
}

// --------- gather config ----------
$db_host = $env['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$db_port = $env['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$db_database = $env['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: null;
$db_username = $env['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: null;
$db_password = $env['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: null;
$db_socket = $env['DB_SOCKET'] ?? getenv('DB_SOCKET') ?: null;
$driver = $env['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: 'mysql';

header('Content-Type: text/plain; charset=utf-8');

// --------- output basic info ----------
echo "=== DB DEBUGGER ===\n";
echo "Working dir: " . $cwd . "\n";
echo ".env file found: " . ($found ? $found : 'NOT FOUND') . "\n\n";

echo "Detected DB config (masked where appropriate):\n";
echo "DB_CONNECTION: " . $driver . "\n";
echo "DB_HOST: " . $db_host . "\n";
echo "DB_PORT: " . $db_port . "\n";
echo "DB_DATABASE: " . ($db_database ?? '(null)') . "\n";
echo "DB_USERNAME: " . ($db_username ?? '(null)') . "\n";
echo "DB_PASSWORD: " . ($db_password ? mask($db_password) : '(null)') . "\n";
echo "DB_SOCKET: " . ($db_socket ?? '(null)') . "\n\n";

// --------- attempt mysqli via TCP ----------
echo "-> Attempting mysqli (TCP) to {$db_host}:{$db_port} ...\n";
ini_set('display_errors', 0);
mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = @new mysqli($db_host, $db_username, $db_password, $db_database, (int)$db_port);
if ($mysqli->connect_errno) {
    echo "mysqli connect error: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
} else {
    echo "mysqli connected OK (TCP). Server info: " . $mysqli->server_info . "\n";
    $mysqli->close();
}
echo "\n";

// --------- attempt mysqli via socket (if socket provided) ----------
if ($db_socket) {
    echo "-> Attempting mysqli via socket: {$db_socket} ...\n";
    $mysqli2 = @new mysqli('localhost', $db_username, $db_password, $db_database, null, $db_socket);
    if ($mysqli2->connect_errno) {
        echo "mysqli socket connect error: (" . $mysqli2->connect_errno . ") " . $mysqli2->connect_error . "\n";
    } else {
        echo "mysqli connected OK (socket). Server info: " . $mysqli2->server_info . "\n";
        $mysqli2->close();
    }
    echo "\n";
}

// --------- attempt PDO ----------
echo "-> Attempting PDO ...\n";
try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_database}";
    if ($db_socket) $dsn .= ";unix_socket={$db_socket}";
    $pdo = new PDO($dsn, $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $ver = $pdo->query('select version()')->fetchColumn();
    echo "PDO connected OK. MySQL version: " . $ver . "\n";
    $pdo = null;
} catch (PDOException $e) {
    echo "PDO error: (" . $e->getCode() . ") " . $e->getMessage() . "\n";
}
echo "\n";

// --------- try to run 'SELECT CURRENT_USER()' via CLI mysql (if accessible) ----------
echo "-> Attempting to run 'mysql' CLI command (if available) to show user/host - this uses NON-PHP route.\n";
$mysql_cli = trim(shell_exec('which mysql 2>/dev/null'));
if ($mysql_cli) {
    $cmd = escapeshellcmd($mysql_cli) . " -h " . escapeshellarg($db_host) . " -P " . escapeshellarg($db_port) .
           " -u " . escapeshellarg($db_username) . " -p" . escapeshellarg($db_password) .
           " -e \"SELECT CURRENT_USER(), USER(), VERSION();\" " . escapeshellarg($db_database) . " 2>&1";
    echo "Running: " . $cmd . "\n";
    $out = shell_exec($cmd);
    echo "Output:\n" . ($out ?: "(no output or access denied)") . "\n";
} else {
    echo "mysql CLI not found in PATH, skipping CLI test.\n";
}
echo "\n";

// --------- quick checklist for user ----------
echo "=== QUICK TROUBLESHOOT CHECKLIST ===\n";
echo "1) Pastikan DB_USERNAME dan DB_PASSWORD di .env benar.\n";
echo "2) Jika pakai cPanel/managed hosting: user MySQL biasanya dibuat di panel and username prefiks 'account_user_...'\n";
echo "   -> DB user di .env harus cocok persis (contoh: aswajasi_aswaja).\n";
echo "3) Jika host = localhost tapi MySQL hanya menerima koneksi TCP (127.0.0.1) coba ubah DB_HOST=127.0.0.1\n";
echo "4) Jalankan di server: php artisan config:clear && php artisan cache:clear && php artisan config:cache\n";
echo "5) Cek apakah user memiliki GRANT: login ke MySQL sebagai root (atau user admin) dan jalankan:\n";
echo "   SHOW GRANTS FOR '" . ($db_username ?? 'user') . "'@'localhost';\n";
echo "6) Jika akses denied: jalankan (sebagai root MySQL):\n";
echo "   GRANT ALL PRIVILEGES ON `{$db_database}`.* TO '" . ($db_username ?? 'user') . "'@'localhost' IDENTIFIED BY 'your_password';\n";
echo "   FLUSH PRIVILEGES;\n";
echo "7) Pastikan tidak ada spasi/karakter tak terlihat di .env (gunakan editor raw), dan env benar dimuat oleh PHP-FPM/Apache.\n";
echo "8) Jika pake socket, periksa lokasi socket (my.cnf) dan cocokkan DB_SOCKET.\n";
echo "9) Jika pake remote DB, pastikan user diizinkan dari host server (bukan hanya 'localhost').\n\n";

echo "NOTE: Hapus file ini setelah selesai debugging!\n";
