<?php
// ============================================================
// api/webhook_wa.php — Fonnte WhatsApp Chatbot Webhook
// Menerima balasan SETUJU/TOLAK dari Approver
// ============================================================

require_once __DIR__ . '/../config.php';

// Fonnte payload can be JSON or Form Data
$rawInput = file_get_contents('php://input');
// Log incoming webhook for debugging
file_put_contents(__DIR__ . '/webhook_log.txt', date('Y-m-d H:i:s') . " - SENDER: " . ($_POST['sender'] ?? 'N/A') . " - MSG: " . ($_POST['message'] ?? 'N/A') . " - RAW: " . $rawInput . "\n", FILE_APPEND);

$contentType = $_SERVER["CONTENT_TYPE"] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $data = json_decode($rawInput, true);
} else {
    $data = $_POST;
}

$sender = $data['sender'] ?? '';
$message = trim($data['message'] ?? '');

if (!$sender || !$message) {
    http_response_code(200);
    exit;
}

// Hanya proses jika pesan dimulai dengan SETUJU atau TOLAK
if (!preg_match('/^(SETUJU|TOLAK)\s+([A-Z]+)-(\d+)(?:\s+([a-zA-Z])(?:\s*(\d+))?)?$/i', trim($message), $matches)) {
    http_response_code(200); // Ignore non-command messages
    exit;
}

$actionType = strtoupper($matches[1]); // SETUJU / TOLAK
$typeCode   = strtoupper($matches[2]); // VEH
$reqId      = (int)$matches[3]; // 15
$optLetter  = isset($matches[4]) ? strtoupper($matches[4]) : ''; // A
$optNumber  = isset($matches[5]) ? (int)$matches[5] : 0; // 1

// Normalisasi nomor WA (Hapus 62 atau 0 di depan untuk pencarian yang fleksibel)
$cleanSender = preg_replace('/^(62|0)/', '', $sender);
if (strlen($cleanSender) < 8) {
    http_response_code(200);
    exit;
}

// 1. Cari user di DB berdasarkan nomor WA
$stmt = $conn->prepare("SELECT u.id, u.full_name, u.role, e.nip_nik as username FROM users u LEFT JOIN employees e ON u.employee_id = e.id WHERE u.whatsapp_number LIKE ?");
$likeSender = "%$cleanSender%";
$stmt->bind_param("s", $likeSender);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    sendWhatsAppFonnte("Maaf, nomor Anda tidak terdaftar sebagai PIC/Approver di sistem SILATAS.", $sender);
    http_response_code(200);
    exit;
}

// 2. Tentukan tabel
$tableMap = [
    'VEH' => 'vehicle_requests',
    'ROM' => 'room_requests',
    'ZOM' => 'zoom_requests',
    'REP' => 'repair_requests',
    'ITM' => 'item_loan_requests'
];

$table = $tableMap[$typeCode] ?? null;
if (!$table) {
    sendWhatsAppFonnte("Maaf, format tipe pengajuan ($typeCode) tidak valid.", $sender);
    http_response_code(200);
    exit;
}

// 3. Ambil data pengajuan
$resReq = $conn->query("SELECT status, note FROM `$table` WHERE id = $reqId");
$requestData = $resReq ? $resReq->fetch_assoc() : null;

if (!$requestData) {
    sendWhatsAppFonnte("Maaf, pengajuan dengan ID #$reqId tidak ditemukan.", $sender);
    http_response_code(200);
    exit;
}

$currentStatus = $requestData['status'];

// Resolve options if needed
$selectedVehicleId = null;
$selectedDriverName = null;
$selectedRoomId = null;

if ($currentStatus === 'pending' && $actionType === 'SETUJU') {
    if ($typeCode === 'VEH') {
        if ($optLetter) {
            $idx = ord($optLetter) - 65;
            $resV = $conn->query("SELECT id FROM master_vehicles ORDER BY id ASC LIMIT $idx, 1");
            if ($resV && $v = $resV->fetch_assoc()) $selectedVehicleId = $v['id'];
        }
        if ($optNumber > 0) {
            $idx = $optNumber - 1;
            $resD = $conn->query("SELECT full_name FROM employees WHERE position LIKE '%driver%' OR position LIKE '%pengemudi%' ORDER BY full_name ASC LIMIT $idx, 1");
            if ($resD && $d = $resD->fetch_assoc()) $selectedDriverName = $d['full_name'];
        }
    } else if ($typeCode === 'ROM') {
        if ($optLetter) {
            $idx = ord($optLetter) - 65;
            $resR = $conn->query("SELECT id FROM master_rooms ORDER BY id ASC LIMIT $idx, 1");
            if ($resR && $r = $resR->fetch_assoc()) $selectedRoomId = $r['id'];
        }
    }
}

// 4. Cek Hak Akses
$canProcess = false;
$nextStatusApprove = '';
$nextStatusReject  = 'rejected';

$role = $user['role'];
$username = $user['username'];
$isManagerFMD = ($role === 'managerFMD' || $username === '197707072025211067');

// Cek PIC Map
$picMap = [
    'VEH' => ['198605082025211053'], 
    'ITM' => ['198902222025211044'], 
    'ZOM' => ['198902222025211044'], 
    'ROM' => ['199008092025212052', '198902222025211044'], 
    'REP' => ['198605082025211053', '197212162014091003']
];
$isPIC = in_array($username, $picMap[$typeCode] ?? []);

// Logika Transisi Status
if ($currentStatus === 'pending') {
    if ($isPIC) {
        $canProcess = true;
        $nextStatusApprove = 'waiting_manager_fmd'; // Default PIC forward
    }
} else if ($currentStatus === 'waiting_manager_fmd') {
    if ($isManagerFMD) {
        $canProcess = true;
        $nextStatusApprove = 'approved'; 
    }
} else if ($currentStatus === 'waiting_manager_fad') {
    if ($role === 'managerFAD') {
        $canProcess = true;
        $nextStatusApprove = 'approved';
    }
} else if ($currentStatus === 'waiting_ppk') {
    if ($role === 'ppk') {
        $canProcess = true;
        $nextStatusApprove = 'approved_waiting_fund';
    }
} else if ($currentStatus === 'waiting_bod') {
    if ($role === 'bod') {
        $canProcess = true;
        $nextStatusApprove = 'approved';
    }
} else if ($currentStatus === 'approved_waiting_fund') {
    if ($role === 'bendahara') {
        $canProcess = true;
        $nextStatusApprove = 'approved';
    }
} else if ($currentStatus === 'approved') {
    if ($isPIC) {
        $canProcess = true;
        $nextStatusApprove = 'ready_for_user'; // PIC marks as ready
    }
}

if (!$canProcess) {
    sendWhatsAppFonnte("Anda tidak memiliki hak akses atau pengajuan *$typeCode-$reqId* saat ini berada di tahap ('$currentStatus') yang bukan tanggung jawab Anda.", $sender);
    http_response_code(200);
    exit;
}

// 5. Eksekusi Perubahan
$finalStatus = ($actionType === 'SETUJU') ? $nextStatusApprove : $nextStatusReject;
$actionNote = "Direspon otomatis via WhatsApp oleh " . $user['full_name'];

// Logging ke Note
$ts = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('d M Y H:i');
$newLog = "[$ts] [" . $user['full_name'] . " (WA)] - " . strtoupper($finalStatus) . ": $actionNote";

$dbPrevNote = $requestData['note'] ?? '';
$finalNote = $dbPrevNote ? $dbPrevNote . "\n" . $newLog : $newLog;

$stmtStr = "UPDATE `$table` SET status = ?, note = ? WHERE id = ?";
$params = ["ssi", $finalStatus, $finalNote, $reqId];

if ($currentStatus === 'pending' && $typeCode === 'VEH' && $actionType === 'SETUJU') {
    if (!$selectedVehicleId || !$selectedDriverName) {
         sendWhatsAppFonnte("❌ Gagal!\nMohon sertakan kode kendaraan & supir yang valid.\nContoh balas: *SETUJU VEH-$reqId A1*", $sender);
         exit;
    }
    $stmtStr = "UPDATE `$table` SET status = ?, note = ?, vehicle_id = ?, driver_name = ? WHERE id = ?";
    $params = ["ssssi", $finalStatus, $finalNote, $selectedVehicleId, $selectedDriverName, $reqId];
} else if ($currentStatus === 'pending' && $typeCode === 'ROM' && $actionType === 'SETUJU') {
    if (!$selectedRoomId) {
         sendWhatsAppFonnte("❌ Gagal!\nMohon sertakan kode ruangan yang valid.\nContoh balas: *SETUJU ROM-$reqId A*", $sender);
         exit;
    }
    $stmtStr = "UPDATE `$table` SET status = ?, note = ?, room_id = ? WHERE id = ?";
    $params = ["sssi", $finalStatus, $finalNote, $selectedRoomId, $reqId];
}

$stmt = $conn->prepare($stmtStr);
if (count($params) === 4) {
    $stmt->bind_param($params[0], $params[1], $params[2], $params[3]);
} else if (count($params) === 5) {
    $stmt->bind_param($params[0], $params[1], $params[2], $params[3], $params[4]);
} else if (count($params) === 6) {
    $stmt->bind_param($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
}

if ($stmt->execute()) {
    $reply = "✅ Berhasil!\nPengajuan *$typeCode-$reqId* telah diubah statusnya menjadi *$finalStatus*.";
    sendWhatsAppFonnte($reply, $sender);
    
    // Notifikasi ke Supir (jika Vehicle dan ada supir yang ditugaskan)
    if ($typeCode === 'VEH' && $selectedDriverName) {
        $stmtDriver = $conn->prepare("SELECT u.whatsapp_number FROM users u INNER JOIN employees e ON u.employee_id = e.id WHERE e.full_name = ?");
        $stmtDriver->bind_param("s", $selectedDriverName);
        $stmtDriver->execute();
        $drv = $stmtDriver->get_result()->fetch_assoc();
        $stmtDriver->close();

        if ($drv && !empty($drv['whatsapp_number'])) {
            // Ambil detail jadwal dari requestData (kita perlu query ulang untuk date_start)
            $resDetails = $conn->query("SELECT applicant_name, DATE_FORMAT(date_start,'%d %b %Y') as ds, time_start FROM vehicle_requests WHERE id = $reqId");
            $dtl = $resDetails ? $resDetails->fetch_assoc() : null;
            
            // Ambil nama kendaraan
            $vName = $selectedVehicleId;
            $resV = $conn->query("SELECT name FROM master_vehicles WHERE id = '$selectedVehicleId'");
            if ($resV && $v = $resV->fetch_assoc()) $vName = $v['name'];

            $app_name = $dtl ? $dtl['applicant_name'] : '-';
            $tgl = $dtl ? $dtl['ds'] : '-';
            $jam = $dtl ? substr($dtl['time_start'], 0, 5) : '-';

            $msgDriver = "🚗 *TUGAS BARU (DRIVER)*\n\nHalo *$selectedDriverName*,\nAnda telah ditugaskan sebagai pengemudi untuk pengajuan kendaraan *VEH-$reqId*.\n\n*Pemohon:* $app_name\n*Kendaraan:* $vName\n*Jadwal:* $tgl jam $jam\n\nMohon cek Dashboard Anda untuk detail lengkap.";
            sendWhatsAppFonnte($msgDriver, $drv['whatsapp_number']);
        }
    }
} else {
    sendWhatsAppFonnte("❌ Gagal mengupdate database server.", $sender);
}

$stmt->close();
http_response_code(200);
