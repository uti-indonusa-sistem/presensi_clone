<?php
/**
 * Database Connection - SECURE & FAST VERSION
 * Load configuration from .env file (cached)
 */

// Simple .env parser (works when parse_ini_file is disabled on shared hosting)
if (!function_exists('loadEnvFile')) {
    function loadEnvFile($filePath) {
        $env = [];
        if (!file_exists($filePath)) {
            return $env;
        }
        
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($lines)) {
            return $env;
        }
        
        foreach ($lines as $line) {
            // Skip comments and empty lines
            $trimmed = trim($line);
            if (empty($trimmed) || strpos($trimmed, '#') === 0) {
                continue;
            }
            
            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove surrounding quotes (single or double)
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                if (!empty($key)) {
                    $env[$key] = $value;
                }
            }
        }
        
        return $env;
    }
}

// Load .env file ONCE (cache to static variable)
static $env_loaded = false;
if (!$env_loaded && file_exists(__DIR__ . '/.env')) {
    $env = loadEnvFile(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
    $env_loaded = true;
}

// Require security classes (auto-loaded once)
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/Auth.php';
require_once __DIR__ . '/config/Security.php';
// Provide legacy mysql_* compatibility for older files
require_once __DIR__ . '/config/compat_mysql.php';

// Initialize database connection (singleton-like usage)
// IMPORTANT: Connection persists for duration of request
static $db = null;
static $connection = null;

if ($db === null) {
    try {
        $db = new Database();
        $connection = $db->getConnection();
    } catch (Exception $e) {
        error_log('Fatal database connection error: ' . $e->getMessage());
        die('Database connection failed. Contact the administrator.');
    }
}

// Load dateline from cache (avoid repeated queries)
static $simpreskulV2_dateline = null;
if ($simpreskulV2_dateline === null) {
    $query = "SELECT tanggal FROM presensi_dateline LIMIT 1";
    $result = $connection->query($query);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $simpreskulV2_dateline = $row['tanggal'] ?? null;
    } else {
        $simpreskulV2_dateline = null;
    }
}

// Application settings
// Determine base URL dynamically (works with HTTP/HTTPS and different hosts)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'presensiv2.poltekindonusa.ac.id';
$base_url = $scheme . '://' . $host . '';

// Security headers (only send once)
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    // Note: `X-XSS-Protection` is deprecated in modern browsers, keep if desired
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Session security
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => intval(getenv('SESSION_LIFETIME')) ?: 86400,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? 'presensiv2.poltekindonusa.ac.id',
        // Correct HTTPS detection compatible with PHP 8.1
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Validate session on every request (if authenticated)
if (isset($_SESSION['user_id'])) {
    if (!Auth::validateSession()) {
        Auth::logout();
        header('Location: ' . $base_url);
        exit;
    }
}
?>


