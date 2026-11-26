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

// Buat koneksi dengan try-catch agar mysqli_sql_exception tertangani dengan baik
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $conn->set_charset('utf8mb4');
    date_default_timezone_set('Asia/Jakarta');
    $conn->query("SET time_zone = '+07:00'");
} catch (\Throwable $e) {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
    }
    die(json_encode([
        'success' => false, 
        'message' => 'Koneksi database gagal. Pastikan service MySQL di XAMPP sudah berjalan.'
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
define('TELE_TOKEN', trim($sysSettings['TELE_TOKEN'] ?? '8680128600:AAH_uuititcIn83IEKFwOTAxUqrHBP-nrxw'));
define('TELE_GROUP_ID', trim($sysSettings['TELE_GROUP_ID'] ?? '-4997587400'));

function sendTelegramPHP(string $message, ?string $chatId = null): bool {
    $token = trim(TELE_TOKEN);
    $targetChatId = trim($chatId ?: TELE_GROUP_ID);
    if (empty($token) || empty($targetChatId)) return false;

    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $payload = ['chat_id' => $targetChatId, 'text' => $message, 'parse_mode' => 'HTML'];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        error_log("[Telegram] CURL Error: $curlErr");
        return false;
    }
    $result = json_decode($response, true);
    if (empty($result['ok'])) {
        error_log("[Telegram] API Error: " . ($result['description'] ?? $response));
        return false;
    }
    return true;
}

// ============================================================
// WhatsApp Config (Fonnte) — Anti-Ban Staggered Queue
// ============================================================
define('FONNTE_TOKEN', $sysSettings['FONNTE_TOKEN'] ?? 'PsfPFiD5cUyvoT9eiyow'); // Token Fonnte Admin

/**
 * Normalisasi & filter daftar nomor WhatsApp (mendukung comma-separated).
 * Mengabaikan ID grup (@g.us), broadcast, dan nomor dummy/tidak valid.
 */
function normalizeWhatsAppTargets(string $rawPhones): array {
    $parts = explode(',', $rawPhones);
    $valid = [];

    foreach ($parts as $raw) {
        $raw = trim($raw);
        if ($raw === '' || stripos($raw, '@g.us') !== false || stripos($raw, 'broadcast') !== false) {
            continue;
        }

        $digits = preg_replace('/\D+/', '', $raw);
        if ($digits === '') {
            continue;
        }

        if (strpos($digits, '620') === 0) {
            $digits = '62' . substr($digits, 3);
        } elseif (strpos($digits, '0') === 0) {
            $digits = '62' . substr($digits, 1);
        } elseif (strpos($digits, '8') === 0) {
            $digits = '62' . $digits;
        }

        $len = strlen($digits);
        if ($len < 10 || $len > 15) {
            continue;
        }
        // Abaikan nomor dummy umum (misal 08123456789 / 0000000000)
        if (preg_match('/^(0+|628123456789\d*)$/', $digits)) {
            continue;
        }

        $valid[$digits] = $digits;
    }

    return array_values($valid);
}

/**
 * Hitung jeda pengiriman (detik) secara bertingkat lintas request agar:
 * - Balasan langsung ke chat masuk ('reply') tetap responsif manusiawi (6-15 detik).
 * - Info ke pemohon/driver/teknisi ('user') berjeda wajar (15-35 detik saat kosong, atau +60-95 detik setelah pesan sebelumnya).
 * - Pesan ke PIC/Approver ('approver') diberi jeda aman 1-3 menit (+75-135 detik antar antrean).
 */
function reserveWhatsAppDelaySeconds(string $mode = 'normal'): int {
    static $memNextSlot = 0;

    $now = time();
    $lockPath = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'silatas_wa_queue_state.json';
    $fp = @fopen($lockPath, 'c+');
    $lastSlot = $memNextSlot;

    if ($fp) {
        @flock($fp, LOCK_EX);
        $raw = stream_get_contents($fp);
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded['next_slot'])) {
                $lastSlot = max($lastSlot, (int)$decoded['next_slot']);
            }
        }
    }

    // Jika state lama tidak valid (misal jam sistem sempat dimundurkan), reset aman
    if ($lastSlot > $now + 900) {
        $lastSlot = $now;
    }

    $remaining = max(0, $lastSlot - $now);

    if ($mode === 'reply') {
        // Balasan langsung atas pesan WA yang baru saja dikirim oleh user/approver
        $delay = random_int(6, 15);
        $newSlot = max($lastSlot, $now + $delay + random_int(15, 25));
    } elseif ($mode === 'approver') {
        // Notifikasi yang membutuhkan tindak lanjut PIC / Manajer (1 - 3 menit)
        if ($remaining === 0) {
            $delay = random_int(55, 110);
        } else {
            $delay = $remaining + random_int(75, 135);
        }
        $delay = min($delay, 600) + ($delay > 600 ? random_int(10, 30) : 0);
        $newSlot = $now + $delay;
    } else {
        // Mode 'user' / 'normal' (info pengajuan masuk, update status pemohon, tugas driver/teknisi)
        if ($remaining === 0) {
            $delay = random_int(15, 35);
        } else {
            $delay = $remaining + random_int(60, 100);
        }
        $delay = min($delay, 540) + ($delay > 540 ? random_int(8, 22) : 0);
        $newSlot = $now + $delay;
    }

    $memNextSlot = $newSlot;

    if ($fp) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(['next_slot' => $newSlot, 'updated_at' => $now]));
        fflush($fp);
        @flock($fp, LOCK_UN);
        fclose($fp);
    }

    return $delay;
}

/**
 * Merapikan pesan WA agar aman dari filter anti-spam/phishing nomor baru:
 * - Mengubah skema link mentah https:// menjadi domain bersih (tanpa merusak instruksi balasan).
 * - Memberikan variasi sapaan alami sesuai waktu setempat.
 */
function humanizeWhatsAppMessage(string $message, string $mode = 'normal'): string {
    // Hindari clickable URL mentah di nomor baru agar tidak terdeteksi link-spam/phishing
    $clean = preg_replace('#https?://(silatas\.biotrop\.org)/?#i', '$1', $message);

    if ($mode !== 'reply') {
        $hour = (int)(new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('G');
        if ($hour >= 4 && $hour < 11) {
            $timeGreeting = 'Selamat pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $timeGreeting = 'Selamat siang';
        } elseif ($hour >= 15 && $hour < 19) {
            $timeGreeting = 'Selamat sore';
        } else {
            $timeGreeting = 'Selamat malam';
        }

        $greetings = ['Halo', 'Yth.', $timeGreeting];
        $picked = $greetings[random_int(0, count($greetings) - 1)];

        // Variasikan sapaan awal "Halo " jika ada (hanya kemunculan pertama)
        $clean = preg_replace('/\bHalo\s+/u', $picked . ' ', $clean, 1);

        // Tambahkan penanda waktu mikro di bawah agar hash pesan tidak identik berulang
        $refStamp = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('d/m H:i');
        $clean = rtrim($clean) . "\n\n_Info Sistem SILATAS • {$refStamp}_";
    }

    return $clean;
}

function sendWhatsAppFonnte(string $message, string $phone, array $options = []): bool {
    $token = trim((string)FONNTE_TOKEN);
    if ($token === '' || trim($phone) === '' || trim($message) === '') {
        return false;
    }

    $targets = normalizeWhatsAppTargets($phone);
    if (empty($targets)) {
        return false;
    }

    $mode = $options['mode'] ?? 'normal';
    $allOk = true;

    foreach ($targets as $targetPhone) {
        $delaySec = isset($options['delay'])
            ? max(5, (int)$options['delay'])
            : reserveWhatsAppDelaySeconds($mode);

        $finalMessage = humanizeWhatsAppMessage($message, $mode);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target'      => $targetPhone,
                'message'     => $finalMessage,
                'delay'       => (string)$delaySec,
                'typing'      => true,
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token"
            ),
        ));

        $response = curl_exec($curl);
        $curlErr  = curl_errno($curl) ? curl_error($curl) : '';
        curl_close($curl);

        if ($curlErr !== '') {
            error_log("[Fonnte] CURL Error to {$targetPhone}: {$curlErr}");
            $allOk = false;
            continue;
        }

        $decoded = json_decode((string)$response, true);
        if (is_array($decoded) && isset($decoded['status']) && $decoded['status'] === false) {
            $reason = $decoded['reason'] ?? $response;
            error_log("[Fonnte] API Warning to {$targetPhone} (delay={$delaySec}s): {$reason}");
            $allOk = false;
        }
    }

    return $allOk;
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
