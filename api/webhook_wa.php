<?php
// ============================================================
// api/webhook_wa.php — Fonnte WhatsApp Chatbot Webhook
// Menerima balasan SETUJU/TOLAK dari Approver
// ============================================================

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/notifications.php';

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

// Hanya proses jika pesan dimulai dengan SETUJU, TOLAK, SELESAI, atau COMPLETED
if (!preg_match('/^(SETUJU|TOLAK|SELESAI|COMPLETED)\s+([A-Z]+)-(\d+)(?:\s*([a-zA-Z])(?:\s*(\d+))?)?$/i', trim($message), $matches)) {
    http_response_code(200); // Ignore non-command messages
    exit;
}

$actionTypeRaw = strtoupper($matches[1]);
$actionType = ($actionTypeRaw === 'SELESAI' || $actionTypeRaw === 'COMPLETED') ? 'SETUJU' : $actionTypeRaw; // SETUJU / TOLAK / (SELESAI -> SETUJU)
$typeCode   = strtoupper($matches[2]); // VEH
$reqId      = (int)$matches[3]; // 15
$optLetter  = isset($matches[4]) ? strtoupper($matches[4]) : ''; // A
$optNumber  = isset($matches[5]) && $matches[5] !== '' ? (int)$matches[5] : -1; // -1 means not provided

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
            $resV = $conn->query("SELECT id, name, license_plate FROM master_vehicles ORDER BY id ASC LIMIT $idx, 1");
            if ($resV && $v = $resV->fetch_assoc()) {
                $selectedVehicleId = $v['id'];
                $vehicleNameStr = $v['name'] . ' - ' . $v['license_plate'];
            }
        }
        if ($optNumber === 0) {
            $selectedDriverName = 'TANPA_SUPIR';
        } else if ($optNumber > 0) {
            $idx = $optNumber - 1;
            $resD = $conn->query("SELECT full_name FROM employees WHERE position LIKE '%driver%' OR position LIKE '%pengemudi%' ORDER BY full_name ASC LIMIT $idx, 1");
            if ($resD && $d = $resD->fetch_assoc()) $selectedDriverName = $d['full_name'];
        }
    } else if ($typeCode === 'ROM') {
        if ($optLetter) {
            $idx = ord($optLetter) - 65;
            $resR = $conn->query("SELECT id, name, capacity FROM master_rooms ORDER BY id ASC LIMIT $idx, 1");
            if ($resR && $r = $resR->fetch_assoc()) {
                $selectedRoomId = $r['id'];
                $roomNameStr = $r['name'] . ' (' . $r['capacity'] . ' org)';
            }
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
    'ROM' => ['199008092025212052', '198902222025211044', '16268300055'], 
    'DRM' => ['199008092025212052', '198902222025211044', '16268300055'], 
    'REP' => ['198605082025211053', '197212162014091003']
];
$isPIC = in_array($username, $picMap[$typeCode] ?? []);

// Logika Transisi Status
if ($currentStatus === 'pending') {
    if ($isPIC) {
        if ($typeCode === 'REP') {
            sendWhatsAppFonnte("Maaf, pengajuan Perbaikan (Repair) tidak dapat diproses via WhatsApp karena memerlukan pemilihan Metode Penanganan dan pengeluaran Sparepart/Barang. Silakan login ke Dashboard Web SILATAS untuk memprosesnya.", $sender);
            http_response_code(200);
            exit;
        }
        if ($actionType === 'TOLAK') {
            sendWhatsAppFonnte("Maaf, PIC tidak dapat menolak pengajuan. Semua pengajuan harus diteruskan ke Manager FMD. Balas SETUJU untuk meneruskan.", $sender);
            http_response_code(200);
            exit;
        }
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
} else if ($currentStatus === 'approved' || $currentStatus === 'ready_for_user') {
    $isApplicant = false;
    if (isset($requestData['user_id']) && $requestData['user_id'] == $user['id']) {
        $isApplicant = true;
    } else {
        $stmtA = $conn->prepare("SELECT user_id FROM `$table` WHERE id = ?");
        $stmtA->bind_param("i", $reqId);
        $stmtA->execute();
        $resA = $stmtA->get_result()->fetch_assoc();
        $stmtA->close();
        if ($resA && $resA['user_id'] == $user['id']) {
            $isApplicant = true;
        }
    }

    if ($isApplicant) {
        $canProcess = true;
        $nextStatusApprove = 'completed';
    } else if ($currentStatus === 'approved' && $isPIC) {
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

// Generate dynamic note similar to web
$actionNote = "Pengajuan diproses melalui WhatsApp";
if ($actionType === 'SETUJU') {
    if ($currentStatus === 'pending' && $nextStatusApprove === 'waiting_manager_fmd') {
        if ($typeCode === 'VEH' && !empty($vehicleNameStr) && !empty($selectedDriverName)) {
            $actionNote = "{$vehicleNameStr} tersedia, diteruskan kepada Manager FMD untuk approval permohonan. Driver: {$selectedDriverName}";
        } else if ($typeCode === 'ROM' && !empty($roomNameStr)) {
            $actionNote = "{$roomNameStr} tersedia, diteruskan kepada Manager FMD untuk approval permohonan";
        } else if ($typeCode === 'ZOM') {
            $actionNote = "Akun Zoom/Link tersedia, diteruskan kepada Manager FMD untuk approval permohonan";
        } else if ($typeCode === 'ITM') {
            $actionNote = "Barang Pinjaman tersedia, diteruskan kepada Manager FMD untuk approval permohonan";
        }
    } else if ($currentStatus === 'waiting_manager_fmd' && $nextStatusApprove === 'approved') {
        if ($typeCode === 'VEH') {
            $actionNote = "Disetujui oleh Manager FMD. Silakan PIC Kendaraan (Unit & Driver) menyiapkan permintaan dan memberikan laporan Check & Recheck.";
        } else if ($typeCode === 'ROM') {
            $actionNote = "Disetujui oleh Manager FMD. Silakan PIC Ruangan & Fasilitas menyiapkan permintaan dan memberikan laporan Check & Recheck.";
        } else if ($typeCode === 'ZOM') {
            $actionNote = "Disetujui oleh Manager FMD. Silakan PIC Akun Zoom/Link menyiapkan permintaan dan memberikan laporan Check & Recheck.";
        } else if ($typeCode === 'ITM') {
            $actionNote = "Disetujui oleh Manager FMD. Silakan PIC Peminjaman Barang menyiapkan permintaan dan memberikan laporan Check & Recheck.";
        } else if ($typeCode === 'REP') {
             $actionNote = "Disetujui oleh Manager FMD. Silakan PIC Perbaikan menyiapkan permintaan dan memberikan laporan Check & Recheck.";
        }
    } else if ($currentStatus === 'approved' && $nextStatusApprove === 'ready_for_user') {
        if ($typeCode === 'VEH') {
            $actionNote = "PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan Kendaraan telah siap untuk diserahkan/dilaksanakan.";
        } else if ($typeCode === 'ROM') {
            $actionNote = "PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan Ruangan telah siap untuk diserahkan/dilaksanakan.";
        } else if ($typeCode === 'ZOM') {
            $actionNote = "PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan Zoom/Virtual telah siap untuk diserahkan/dilaksanakan.";
        } else if ($typeCode === 'ITM') {
            $actionNote = "PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan Barang Pinjaman telah siap untuk diserahkan/dilaksanakan.";
        } else if ($typeCode === 'REP') {
             $actionNote = "PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan Perbaikan telah siap untuk diserahkan/dilaksanakan.";
        }
    } else if ($currentStatus === 'ready_for_user' && $nextStatusApprove === 'completed') {
        $actionNote = "PIC mengkonfirmasi: seluruh kebutuhan telah terpenuhi. Permintaan selesai dilaksanakan.";
        if (isset($requestData['user_id']) && $requestData['user_id'] == $user['id']) {
            $actionNote = "Pengajuan diselesaikan oleh Pemohon.";
        }
    }
} else if ($actionType === 'TOLAK') {
    $actionNote = "Pengajuan ditolak melalui WhatsApp";
}
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
    
    // Panggil fungsi notifikasi agar dikirim ke Manager FMD / User / Approver berikutnya
    notifyStatusUpdate($conn, $table, $reqId, $finalStatus, $actionNote, $user['full_name']);
} else {
    sendWhatsAppFonnte("❌ Gagal mengupdate database server.", $sender);
}

$stmt->close();
http_response_code(200);
