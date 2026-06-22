<?php
// ============================================================
// config.php - Konfigurasi Koneksi Database (Versi Hosting)
// ============================================================

// ============================================================
// Database Configuration (InfinityFree)
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', ''); // Masukkan password vPanel Anda di sini
define('DB_NAME', 'biotrop'); 

// ============================================================
// Path & URL Configuration
// ============================================================
// Auto-detect project root URL
if (!defined('BASE_URL')) {
    // 1. Detect Protocol (with proxy support for hosting like InfinityFree)
    $protocol = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
        ($_SERVER['SERVER_PORT'] == 443) || 
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    ) ? "https://" : "http://";

    // 2. Detect Host
    $host = $_SERVER['HTTP_HOST'];

    // 3. Detect App Root (Subdirectory)
    // We use __DIR__ (location of config.php) relative to DOCUMENT_ROOT
    $config_dir = str_replace('\\', '/', __DIR__);
    $doc_root   = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
    
    $app_root = '';
    if (!empty($doc_root) && strpos($config_dir, $doc_root) === 0) {
        $app_root = substr($config_dir, strlen($doc_root));
    } else {
        // Fallback to SCRIPT_NAME method if DOCUMENT_ROOT is not helpful
        $script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $sub_dirs = ['/admin', '/user', '/supervisor', '/api', '/reception', '/includes'];
        $app_root = $script_path;
        foreach ($sub_dirs as $dir) {
            if (substr($app_root, -strlen($dir)) === $dir) {
                $app_root = substr($app_root, 0, -strlen($dir));
                break;
            }
        }
    }
    
    // Cleanup: ensure it starts with / and has no trailing slash
    $app_root = '/' . ltrim(str_replace('\\', '/', $app_root), '/');
    $app_root = rtrim($app_root, '/');

    define('BASE_URL', $protocol . $host . $app_root);
}

// Buat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$conn->set_charset('utf8mb4');

// Set Timezone ke WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');
$conn->query("SET time_zone = '+07:00'");

if ($conn->connect_error) {
    // Jika koneksi gagal, sistem akan mengirimkan error JSON
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false, 
        'message' => 'Database Error: ' . $conn->connect_error 
    ]));
}

// ============================================================
// Helper: Kembalikan JSON response
// ============================================================
function jsonResponse(bool $success, string $message = '', $data = null): void {
    header('Content-Type: application/json');
    $resp = ['success' => $success, 'message' => $message];
    if ($data !== null) $resp['data'] = $data;
    echo json_encode($resp);
    exit;
}

// ============================================================
// System Settings (DB)
// ============================================================
$sysSettings = [];
if (isset($conn) && !$conn->connect_error) {
    $res = $conn->query("SELECT setting_key, setting_value FROM system_settings");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $sysSettings[$r['setting_key']] = $r['setting_value'];
        }
    }
}

// ============================================================
// Maintenance Mode
// ============================================================
define('APP_NAME', $sysSettings['APP_NAME'] ?? 'SILATAS');
define('MAINTENANCE_MODE', ($sysSettings['MAINTENANCE_MODE'] ?? '0') === '1');

// ============================================================
// Telegram Config
// ============================================================
define('TELE_TOKEN', $sysSettings['TELE_TOKEN'] ?? '8680128600:AAH_uuititcIn83IEKFwOTAxUqrHBP-nrxw');
define('TELE_GROUP_ID', $sysSettings['TELE_GROUP_ID'] ?? '-4997587400');

function sendTelegramPHP(string $message, ?string $chatId = null): bool {
    $targetChatId = $chatId ?: TELE_GROUP_ID;
    $url = "https://api.telegram.org/bot" . TELE_TOKEN . "/sendMessage";
    $payload = ['chat_id' => $targetChatId, 'text' => $message, 'parse_mode' => 'HTML'];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return true; // Simple return for brevity
}

// ============================================================
// WhatsApp Config (Fonnte)
// ============================================================
define('FONNTE_TOKEN', $sysSettings['FONNTE_TOKEN'] ?? 'PsfPFiD5cUyvoT9eiyow'); // Token Fonnte Admin

function sendWhatsAppFonnte(string $message, string $phone): bool {
    $token = FONNTE_TOKEN;
    if (empty($token) || empty($phone)) return false;
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'target' => $phone,
        'message' => $message,
      ),
      CURLOPT_HTTPHEADER => array(
        "Authorization: $token"
      ),
    ));

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
    }
    curl_close($curl);
    
    // Add delay to prevent Fonnte rate limits on consecutive API calls
    sleep(2);
    
    return true; 
}

// ============================================================
// Maintenance Mode Blocker
// ============================================================
if (MAINTENANCE_MODE) {
    $current_uri = $_SERVER['REQUEST_URI'] ?? '';
    // Izinkan login, logout, dan file statis
    if (strpos($current_uri, 'login.php') === false && strpos($current_uri, 'logout.php') === false) {
        $role = strtolower($_SESSION['role'] ?? '');
        $is_superadmin = ($role === 'superadmin' || $role === 'super admin');
        
        if (!$is_superadmin) {
            // Jika request API, balas dengan JSON
            if (strpos($current_uri, '/api/') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sistem sedang dalam mode pemeliharaan (Maintenance Mode).']);
                exit;
            }
            // Jika halaman web, tampilkan halaman maintenance
            die('<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Maintenance Mode</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>body{font-family:"Inter",sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;background:#f8fafc;color:#475569;text-align:center;margin:0;}h1{color:#1e293b;margin-bottom:0.5rem;}p{max-width:400px;margin:0 auto;line-height:1.5;}</style></head><body><div><svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:1rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><h1>Sedang Pemeliharaan</h1><p>Mohon maaf, sistem SILATAS sedang dalam proses pemeliharaan atau peningkatan fitur. Silakan kembali lagi nanti.</p></div></body></html>');
        }
    }
}
