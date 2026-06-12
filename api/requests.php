<?php
// ============================================================
// api/requests.php — CRUD Semua Pengajuan (Vehicle, Room, Zoom, Repair, Item)
// Setara dengan: lib/action.ts (semua fungsi get/submit/update)
// ============================================================

session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

set_exception_handler(function ($e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
    exit;
});
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (error_reporting() === 0) return false;
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Session tidak valid. Silakan login kembali.');
}

$action  = $_GET['action']  ?? $_POST['action'] ?? '';
$userId  = (int)$_SESSION['user_id'];
$actorName = $_SESSION['full_name'] ?? 'Admin';

// ============================================================
function makeNoteLog(string $actor, string $status, string $note): string {
    $ts = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('d M Y H:i');
    $noteClean = str_replace(["\r\n", "\r", "\n"], "<br>", $note);
    $content = $noteClean ?: "Mengubah status menjadi $status";
    return "[$ts] [$actor] - " . strtoupper($status) . ": $content";
}

// ============================================================
// ===== TABLE MAP =====
// ============================================================
$tableMap = [
    'Vehicle' => 'vehicle_requests',
    'Room'    => 'room_requests',
        'Dormitory'=> 'dormitory_requests',
    'Dormitory'=> 'dormitory_requests',
    'Zoom'    => 'zoom_requests',
    'Repair'  => 'repair_requests',
    'Item'    => 'item_loan_requests',
];

/**
 * Kirim notifikasi WA ke Approver berdasarkan status
 */
function notifyApprovers($conn, $newStatus, $type, $id, $msg) {
    if (!function_exists('sendWhatsAppFonnte')) return;

    $waMsg = str_replace(['<b>','</b>','<i>','</i>'], ['*','*','_','_'], $msg);
    $waMsg = strip_tags($waMsg);
    
    $tgMsg = $msg; // Telegram uses HTML

    $typeCodes = [
        'Vehicle' => 'VEH',
        'Room'    => 'ROM',
        'Dormitory'=> 'DRM',
        'Zoom'    => 'ZOM',
        'Repair'  => 'REP',
        'Item'    => 'ITM'
    ];
    $code = $typeCodes[$type] ?? 'REQ';
    $approvableStatuses = ['pending', 'waiting_manager_fmd', 'waiting_manager_fad', 'waiting_ppk', 'waiting_bod', 'approved_waiting_fund', 'approved', 'ready_for_user'];
    
    if (in_array($newStatus, $approvableStatuses)) {
        if ($newStatus === 'pending' && $type === 'Vehicle') {
            $waMsg .= "\n\n---\n*PILIHAN KENDARAAN:*\n";
            $tgMsg .= "\n\n---\n<b>PILIHAN KENDARAAN:</b>\n";
            $resV = $conn->query("SELECT id, name FROM master_vehicles ORDER BY id ASC");
            if ($resV) {
                $vCount = 0;
                while($row = $resV->fetch_assoc()) {
                    $waMsg .= chr(65 + $vCount) . ". " . $row['name'] . "\n";
                    $tgMsg .= chr(65 + $vCount) . ". " . $row['name'] . "\n";
                    $vCount++;
                }
            }
            
            $waMsg .= "\n*PILIHAN SUPIR:*\n";
            $resD = $conn->query("SELECT id, full_name FROM employees WHERE position LIKE '%driver%' OR position LIKE '%pengemudi%' ORDER BY full_name ASC");
            if ($resD) {
                $dCount = 1;
                while($row = $resD->fetch_assoc()) {
                    $waMsg .= $dCount . ". " . $row['full_name'] . "\n";
                    $dCount++;
                }
            }
            $waMsg .= "\n*Untuk menyetujui, balas:*\nSETUJU {$code}-{$id} A1\n_(Ganti A & 1 sesuai pilihan)_\n\n*Untuk menolak:*\nTOLAK {$code}-{$id}";
            $tgMsg .= "\n<b>Untuk menyetujui, balas:</b>\nSETUJU {$code}-{$id} A1\n<i>(Ganti A & 1 sesuai pilihan)</i>\n\n<b>Untuk menolak:</b>\nTOLAK {$code}-{$id}";
        } else if ($newStatus === 'pending' && $type === 'Room') {
            $waMsg .= "\n\n---\n*PILIHAN RUANGAN:*\n";
            $tgMsg .= "\n\n---\n<b>PILIHAN RUANGAN:</b>\n";
            $resR = $conn->query("SELECT id, name FROM master_rooms ORDER BY id ASC");
            if ($resR) {
                $rCount = 0;
                while($row = $resR->fetch_assoc()) {
                    $waMsg .= chr(65 + $rCount) . ". " . $row['name'] . "\n";
                    $tgMsg .= chr(65 + $rCount) . ". " . $row['name'] . "\n";
                    $rCount++;
                }
            }
            $waMsg .= "\n*Untuk menyetujui, balas:*\nSETUJU {$code}-{$id} A\n_(Ganti A sesuai pilihan)_\n\n*Untuk menolak:*\nTOLAK {$code}-{$id}";
            $tgMsg .= "\n<b>Untuk menyetujui, balas:</b>\nSETUJU {$code}-{$id} A\n<i>(Ganti A sesuai pilihan)</i>\n\n<b>Untuk menolak:</b>\nTOLAK {$code}-{$id}";
        } else if ($newStatus === 'pending' && $type === 'Dormitory') {
            $waMsg .= "\n\n---\n*PILIHAN DORMITORY:*\n";
            $tgMsg .= "\n\n---\n<b>PILIHAN DORMITORY:</b>\n";
            $resR = $conn->query("SELECT id, name FROM master_dormitories ORDER BY id ASC");
            if ($resR) {
                $rCount = 0;
                while($row = $resR->fetch_assoc()) {
                    $waMsg .= chr(65 + $rCount) . ". " . $row['name'] . "\n";
                    $tgMsg .= chr(65 + $rCount) . ". " . $row['name'] . "\n";
                    $rCount++;
                }
            }
            $waMsg .= "\n*Untuk menyetujui, balas:*\nSETUJU {$code}-{$id} A\n_(Ganti A sesuai pilihan)_\n\n*Untuk menolak:*\nTOLAK {$code}-{$id}";
            $tgMsg .= "\n<b>Untuk menyetujui, balas:</b>\nSETUJU {$code}-{$id} A\n<i>(Ganti A sesuai pilihan)</i>\n\n<b>Untuk menolak:</b>\nTOLAK {$code}-{$id}";
        } else {
            $waMsg .= "\n\n---\n*Untuk menyetujui/lanjut, balas:*\nSETUJU {$code}-{$id}\n\n*Untuk menolak, balas:*\nTOLAK {$code}-{$id}";
            $tgMsg .= "\n\n---\n<b>Untuk menyetujui/lanjut, balas:</b>\nSETUJU {$code}-{$id}\n\n<b>Untuk menolak, balas:</b>\nTOLAK {$code}-{$id}";
        }
    }
    $targetNumbers = [];
    $targetTelegramIds = [];
    $picMap = [
        'Vehicle' => ['198605082025211053'], // Alfi
        'Item'    => ['198902222025211044'], // Indra
        'Zoom'    => ['198902222025211044'], // Indra
        'Room'    => ['199008092025212052', '16268300055'], // Lastiah, Dani
        'Dormitory'=> ['199008092025212052', '16268300055'], // Lastiah, Dani
        'Repair'  => ['16268000027', '197212162014091003', '198902222025211044'] // Alfi, Agus Sujadi, Indra
    ];

    if ($newStatus === 'pending' || $newStatus === 'approved') {
        if (isset($picMap[$type])) {
            $usernames = $picMap[$type];
            $placeholders = implode(',', array_fill(0, count($usernames), '?'));
            $stmt = $conn->prepare("SELECT u.whatsapp_number, u.telegram_chat_id FROM users u INNER JOIN employees e ON u.employee_id = e.id WHERE e.nip_nik IN ($placeholders)");
            if ($stmt) {
                $types = str_repeat('s', count($usernames));
                $stmt->bind_param($types, ...$usernames);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    if (!empty($row['whatsapp_number'])) $targetNumbers[] = $row['whatsapp_number'];
                    if (!empty($row['telegram_chat_id'])) $targetTelegramIds[] = $row['telegram_chat_id'];
                }
                $stmt->close();
            }
        }
    }

    $roleMap = [
        'waiting_manager_fmd'   => 'managerFMD',
        'waiting_manager_fad'   => 'managerFAD',
        'waiting_ppk'           => 'ppk',
        'waiting_bod'           => 'bod',
        'approved_waiting_fund' => 'bendahara'
    ];
    $targetRole = $roleMap[$newStatus] ?? null;
    
    if ($targetRole) {
        $sql = "SELECT u.whatsapp_number, u.telegram_chat_id FROM users u LEFT JOIN employees e ON u.employee_id = e.id WHERE u.role = ?";
        if ($newStatus === 'waiting_manager_fmd') {
            $sql = "SELECT u.whatsapp_number, u.telegram_chat_id FROM users u LEFT JOIN employees e ON u.employee_id = e.id WHERE (u.role = ? OR e.nip_nik = '197707072025211067')";
        }
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $targetRole);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                if (!empty($row['whatsapp_number'])) $targetNumbers[] = $row['whatsapp_number'];
                if (!empty($row['telegram_chat_id'])) $targetTelegramIds[] = $row['telegram_chat_id'];
            }
            $stmt->close();
        }
    }

    $targetNumbers = array_unique($targetNumbers);
    if (!empty($targetNumbers) && function_exists('sendWhatsAppFonnte')) {
        $targets = implode(',', $targetNumbers);
        sendWhatsAppFonnte($waMsg, $targets);
    }

    $targetTelegramIds = array_unique($targetTelegramIds);
    if (!empty($targetTelegramIds) && function_exists('sendTelegramPHP')) {
        foreach ($targetTelegramIds as $tgId) {
            sendTelegramPHP($tgMsg, $tgId);
        }
    }
}

/**
 * Kirim notifikasi ke Admin Group saat ada pengajuan baru
 */
function notifyNewRequest($type, $id, $applicant, $unit, $purpose) {
    global $conn;
    if (!function_exists('sendTelegramPHP')) return;

    $emoji = [
        'Vehicle' => '🚗',
        'Room'    => '🏢',
        'Zoom'    => '📹',
        'Repair'  => '🛠️',
        'Item'    => '📦'
    ][$type] ?? '🔔';
    
    $typeLabel = [
        'Vehicle' => 'KENDARAAN DINAS',
        'Room'    => 'RUANGAN',
        'Zoom'    => 'ZOOM MEETING',
        'Repair'  => 'PERBAIKAN',
        'Item'    => 'PEMINJAMAN BARANG'
    ][$type] ?? strtoupper($type);

    // Fetch details for better formatting
    $detailTxt = "";
    $table = [
        'Vehicle' => 'vehicle_requests',
        'Room'    => 'room_requests',
        'Dormitory'=> 'dormitory_requests',
    'Dormitory'=> 'dormitory_requests',
        'Zoom'    => 'zoom_requests',
        'Repair'  => 'repair_requests',
        'Item'    => 'item_loan_requests'
    ][$type] ?? '';

    if ($table) {
        $res = $conn->query("SELECT * FROM `$table` WHERE id = $id");
        if ($res && $row = $res->fetch_assoc()) {
            if ($type === 'Vehicle') {
                $detailTxt .= "<b>Lokasi Tujuan:</b> " . htmlspecialchars($row['destination'] ?? '-') . "\n";
                $detailTxt .= "<b>Item:</b> Operasional\n";
                $detailTxt .= "<b>Waktu:</b> " . $row['date_start'] . " " . substr($row['time_start'], 0, 5) . " s/d " . $row['date_end'] . " " . substr($row['time_end'], 0, 5) . "\n";
            } elseif ($type === 'Room') {
                $detailTxt .= "<b>Ruangan:</b> " . htmlspecialchars($row['room_id'] ?? '') . "\n";
                $detailTxt .= "<b>Waktu:</b> " . $row['date_start'] . " " . substr($row['time_start'], 0, 5) . " s/d " . $row['date_end'] . " " . substr($row['time_end'], 0, 5) . "\n";
            } elseif ($type === 'Dormitory') {
                $detailTxt .= "<b>Dormitory:</b> " . htmlspecialchars($row['dormitory_id'] ?? '') . "\n";
                $detailTxt .= "<b>Penghuni:</b> " . htmlspecialchars($row['occupant_name'] ?? '-') . "\n";
                $detailTxt .= "<b>Waktu:</b> " . $row['date_start'] . " " . substr($row['time_start'], 0, 5) . " s/d " . $row['date_end'] . " " . substr($row['time_end'], 0, 5) . "\n";
            } elseif ($type === 'Zoom') {
                $detailTxt .= "<b>Akun Zoom:</b> " . htmlspecialchars($row['zoom_account_id'] ?? '') . "\n";
                $detailTxt .= "<b>Permintaan Tambahan:</b> " . htmlspecialchars($row['request_type'] ?? '-') . "\n";
                $detailTxt .= "<b>Kebutuhan Khusus:</b> " . htmlspecialchars($row['special_needs'] ?? '-') . "\n";
                $detailTxt .= "<b>Waktu:</b> " . $row['date_start'] . " " . substr($row['time_start'], 0, 5) . " s/d " . $row['date_end'] . " " . substr($row['time_end'], 0, 5) . "\n";
            } elseif ($type === 'Repair') {
                $detailTxt .= "<b>Lokasi:</b> " . htmlspecialchars($row['location_detail'] ?? '') . "\n";
                $detailTxt .= "<b>Prioritas:</b> " . strtoupper($row['priority'] ?? 'MEDIUM') . "\n";
            } elseif ($type === 'Item') {
                $detailTxt .= "<b>Barang:</b> " . htmlspecialchars($row['item_name'] ?? '') . "\n";
                $detailTxt .= "<b>Waktu:</b> " . $row['loan_date'] . " " . substr($row['loan_time'], 0, 5) . " s/d " . $row['return_date'] . " " . substr($row['return_time'], 0, 5) . "\n";
            }
        }
    }

    $msg = "<b>$emoji PENGAJUAN BARU: " . $typeLabel . "</b>\n\n";
    $msg .= "<b>Pemohon:</b> " . htmlspecialchars($applicant) . "\n";
    $msg .= "<b>Unit:</b> " . htmlspecialchars($unit) . "\n";
    if ($type === 'Repair') {
        $msg .= "<b>Masalah:</b> " . htmlspecialchars($purpose) . "\n";
    } else {
        $msg .= "<b>Keperluan:</b> " . htmlspecialchars($purpose) . "\n";
    }
    
    if ($detailTxt) {
        $msg .= $detailTxt;
    }

    $msg .= "\n<i>ID Pengajuan: #$id</i>\n";
    $msg .= "<i>Silakan cek dashboard FMD untuk tindak lanjut.</i>";

    // Kirim ke Group Admin (Telegram)
    sendTelegramPHP($msg);
    
    // Kirim ke Approver PIC (WhatsApp)
    notifyApprovers($conn, 'pending', $type, $id, $msg);
}

/**
 * Kirim notifikasi ke Telegram user jika status berubah
 */
function notifyStatusUpdate($conn, $table, $id, $newStatus, $noteInput, $actorName) {
    if (!function_exists('sendTelegramPHP')) return;

    // 1. Ambil info request & user_id
    $stmt = $conn->prepare("SELECT user_id, applicant_name FROM `$table` WHERE id = ?");
    if (!$stmt) return;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($request) {
        // 2. Ambil kontak user
        $uId = $request['user_id'];
        $stmtU = $conn->prepare("SELECT telegram_chat_id, whatsapp_number, callmebot_apikey FROM users WHERE id = ?");
        $stmtU->bind_param("i", $uId);
        $stmtU->execute();
        $user = $stmtU->get_result()->fetch_assoc();
        $stmtU->close();

        $statusLabelMap = [
            'pending'             => '⌛ MENUNGGU VERIFIKASI',
            'approved'            => '✅ DISETUJUI',
            'rejected'            => '❌ DITOLAK',
            'verified'            => '🔍 DIVERIFIKASI',
            'completed'           => '🏁 SELESAI',
            'returned'            => '📦 DIKEMBALIKAN',
            'in-progress'         => '🛠️ SEDANG DIKERJAKAN',
            'waiting_manager_fmd' => '⌛ MENUNGGU APPROVAL MGR FMD',
            'waiting_manager_fad' => '⌛ MENUNGGU APPROVAL MGR FAD',
            'waiting_ppk'         => '⌛ MENUNGGU APPROVAL PPK',
            'waiting_bod'         => '⌛ MENUNGGU APPROVAL BOD',
        ];
        
        $statusLabel = $statusLabelMap[strtolower($newStatus)] ?? strtoupper(str_replace('_', ' ', $newStatus));

        $msg = "<b>📢 UPDATE PENGAJUAN</b>\n\n";
        $msg .= "Halo " . htmlspecialchars($request['applicant_name']) . ",\n";
        $msg .= "Status pengajuan Anda <b>#$id</b> telah diperbarui.\n\n";
        $msg .= "<b>Status Baru:</b> $statusLabel\n";
        if ($noteInput) {
            $msg .= "<b>Catatan Admin:</b> " . htmlspecialchars($noteInput) . "\n";
        }
        $msg .= "<b>Diproses Oleh:</b> " . htmlspecialchars($actorName) . "\n\n";
        $msg .= "<i>Silakan cek Dashboard Bioform untuk detail.</i>";

        // Kirim ke Telegram User (jika ada chat_id)
        if (!empty($user['telegram_chat_id'])) {
            sendTelegramPHP($msg, $user['telegram_chat_id']);
        }
        
        // Kirim ke WhatsApp User via Fonnte (jika ada)
        if (!empty($user['whatsapp_number']) && function_exists('sendWhatsAppFonnte')) {
            // Sesuaikan format HTML ke WA Markdown (*bold*, _italic_)
            $waMsg = str_replace(['<b>','</b>','<i>','</i>'], ['*','*','_','_'], $msg);
            // Hapus sisa tag HTML jika ada
            $waMsg = strip_tags($waMsg);
            sendWhatsAppFonnte($waMsg, $user['whatsapp_number']);
        }
        
        // Kirim notifikasi WA ke Approver
        $tableToType = [
            'vehicle_requests' => 'Vehicle',
            'room_requests'    => 'Room',
            'dormitory_requests' => 'Dormitory',
            'zoom_requests'    => 'Zoom',
            'repair_requests'  => 'Repair',
            'item_loan_requests' => 'Item'
        ];
        $type = $tableToType[$table] ?? 'Unknown';
        
        $msgApprover = "<b>📢 UPDATE PENGAJUAN (" . strtoupper($type) . ")</b>\n\n";
        $msgApprover .= "<b>ID:</b> #$id\n";
        $msgApprover .= "<b>Pemohon:</b> " . htmlspecialchars($request['applicant_name']) . "\n";
        $msgApprover .= "<b>Status Baru:</b> $statusLabel\n";
        $msgApprover .= "<i>Mohon cek Dashboard Admin untuk review/tindakan.</i>";
        
        notifyApprovers($conn, $newStatus, $type, $id, $msgApprover);
    }
}

switch ($action) {

    // ============================================================
    // 1. GET ALL REQUESTS (Admin melihat semua)
    // ============================================================
    case 'get_vehicle':
        $res = $conn->query("SELECT id, user_id, vehicle_id, applicant_name, applicant_unit, destination, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, status, note, driver_name, created_at FROM vehicle_requests ORDER BY created_at DESC LIMIT 100");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'get_room':
        $res = $conn->query("SELECT id, user_id, room_id, applicant_name, applicant_unit, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, participants, special_needs, status, note, created_at FROM room_requests ORDER BY created_at DESC LIMIT 100");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'get_dormitory':
        $res = $conn->query("SELECT id, user_id, dormitory_id, applicant_name, applicant_unit, occupant_name, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, participants, special_needs, status, note, created_at FROM dormitory_requests ORDER BY created_at DESC LIMIT 100");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'get_zoom':
        $res = $conn->query("SELECT id, user_id, zoom_account_id, applicant_name, applicant_unit, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, participants, request_type, special_needs, status, note, created_at FROM zoom_requests ORDER BY created_at DESC LIMIT 100");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'get_repair':
        $res = $conn->query("SELECT id, user_id, applicant_name, applicant_unit, location_detail, DATE_FORMAT(incident_date,'%Y-%m-%d') as incident_date, incident_time, issue_description, priority, status, note, created_at FROM repair_requests ORDER BY created_at DESC LIMIT 100");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'get_item':
        $res = $conn->query("SELECT id, user_id, applicant_name, applicant_unit, item_name, item_quantity, DATE_FORMAT(loan_date,'%Y-%m-%d') as loan_date, loan_time, DATE_FORMAT(return_date,'%Y-%m-%d') as return_date, return_time, purpose, status, note, created_at FROM item_loan_requests ORDER BY created_at DESC LIMIT 100");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    // ============================================================
    // 1b. GET REQUESTS BY USER
    // ============================================================
    case 'get_vehicle_by_user':
        $stmt = $conn->prepare("SELECT id, user_id, vehicle_id, applicant_name, applicant_unit, destination, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, status, note, driver_name, created_at FROM vehicle_requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        $stmt->close();
        break;

    case 'get_room_by_user':
        $stmt = $conn->prepare("SELECT id, user_id, room_id, applicant_name, applicant_unit, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, participants, special_needs, status, note, created_at FROM room_requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        $stmt->close();
        break;

    case 'get_dormitory_by_user':
        $stmt = $conn->prepare("SELECT id, user_id, dormitory_id, applicant_name, applicant_unit, occupant_name, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, participants, special_needs, status, note, created_at FROM dormitory_requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        $stmt->close();
        break;

    case 'get_zoom_by_user':
        $stmt = $conn->prepare("SELECT id, user_id, zoom_account_id, applicant_name, applicant_unit, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, participants, request_type, special_needs, status, note, created_at FROM zoom_requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        $stmt->close();
        break;

    case 'get_repair_by_user':
        $stmt = $conn->prepare("SELECT id, user_id, applicant_name, applicant_unit, location_detail, DATE_FORMAT(incident_date,'%Y-%m-%d') as incident_date, incident_time, issue_description, priority, status, note, created_at FROM repair_requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        $stmt->close();
        break;

    case 'get_item_by_user':
        $stmt = $conn->prepare("SELECT id, user_id, applicant_name, applicant_unit, item_name, item_quantity, DATE_FORMAT(loan_date,'%Y-%m-%d') as loan_date, loan_time, DATE_FORMAT(return_date,'%Y-%m-%d') as return_date, return_time, purpose, status, note, created_at FROM item_loan_requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        $stmt->close();
        break;

    // ============================================================
    // 2. SUBMIT REQUESTS
    // ============================================================
    case 'submit_vehicle':
        $vehicle_id     = $_POST['vehicle_id']     ?? 'PENDING_ASSIGNMENT';
        $applicant_name = $_POST['applicant_name'] ?? '';
        $applicant_unit = $_POST['applicant_unit'] ?? '';
        $date_start     = !empty($_POST['date_start']) ? $_POST['date_start'] : null;
        $time_start     = !empty($_POST['time_start']) ? $_POST['time_start'] : null;
        $date_end       = !empty($_POST['date_end']) ? $_POST['date_end'] : null;
        $time_end       = !empty($_POST['time_end']) ? $_POST['time_end'] : null;
        $destination    = $_POST['destination']    ?? '';
        $purpose        = $_POST['purpose']        ?? '';

        $stmt = $conn->prepare("INSERT INTO vehicle_requests (user_id, vehicle_id, applicant_name, applicant_unit, destination, date_start, time_start, date_end, time_end, purpose, status) VALUES (?,?,?,?,?,?,?,?,?,?,'pending')");
        $stmt->bind_param("isssssssss", $userId, $vehicle_id, $applicant_name, $applicant_unit, $destination, $date_start, $time_start, $date_end, $time_end, $purpose);
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            notifyNewRequest('Vehicle', $newId, $applicant_name, $applicant_unit, $purpose);
            jsonResponse(true, 'Permohonan Kendaraan berhasil disimpan!', ['id' => $newId]);
        } else {
            jsonResponse(false, 'Gagal menyimpan data.');
        }
        $stmt->close();
        break;

    case 'submit_room':
        $room_id        = $_POST['room_id']        ?? '';
        $applicant_name = $_POST['applicant_name'] ?? '';
        $applicant_unit = $_POST['applicant_unit'] ?? '';
        $date_start     = !empty($_POST['date_start']) ? $_POST['date_start'] : null;
        $time_start     = !empty($_POST['time_start']) ? $_POST['time_start'] : null;
        $date_end       = !empty($_POST['date_end']) ? $_POST['date_end'] : null;
        $time_end       = !empty($_POST['time_end']) ? $_POST['time_end'] : null;
        $purpose        = $_POST['purpose']        ?? '';
        $participants   = (int)($_POST['participants'] ?? 0);
        $special_needs  = $_POST['special_needs']  ?? '';

        $stmt = $conn->prepare("INSERT INTO room_requests (user_id, room_id, applicant_name, applicant_unit, date_start, time_start, date_end, time_end, purpose, participants, special_needs) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("issssssssis", $userId, $room_id, $applicant_name, $applicant_unit, $date_start, $time_start, $date_end, $time_end, $purpose, $participants, $special_needs);
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            notifyNewRequest('Room', $newId, $applicant_name, $applicant_unit, $purpose);
            jsonResponse(true, 'Permintaan Ruangan berhasil disimpan!', ['id' => $newId]);
        } else {
            jsonResponse(false, 'Gagal menyimpan data.');
        }
        $stmt->close();
        break;

    case 'submit_dormitory':
        $dormitory_id   = $_POST['dormitory_id']   ?? '';
        $applicant_name = $_POST['applicant_name'] ?? '';
        $applicant_unit = $_POST['applicant_unit'] ?? '';
        $occupant_name  = $_POST['occupant_name']  ?? '';
        $date_start     = !empty($_POST['date_start']) ? $_POST['date_start'] : null;
        $time_start     = !empty($_POST['time_start']) ? $_POST['time_start'] : null;
        $date_end       = !empty($_POST['date_end']) ? $_POST['date_end'] : null;
        $time_end       = !empty($_POST['time_end']) ? $_POST['time_end'] : null;
        $purpose        = $_POST['purpose']        ?? '';
        $participants   = (int)($_POST['participants'] ?? 0);
        $special_needs  = $_POST['special_needs']  ?? '';

        $stmt = $conn->prepare("INSERT INTO dormitory_requests (user_id, dormitory_id, applicant_name, applicant_unit, occupant_name, date_start, time_start, date_end, time_end, purpose, participants, special_needs) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isssssssssis", $userId, $dormitory_id, $applicant_name, $applicant_unit, $occupant_name, $date_start, $time_start, $date_end, $time_end, $purpose, $participants, $special_needs);
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            notifyNewRequest('Dormitory', $newId, $applicant_name, $applicant_unit, $purpose);
            jsonResponse(true, 'Permintaan Dormitory berhasil disimpan!', ['id' => $newId]);
        } else {
            jsonResponse(false, 'Gagal menyimpan data.');
        }
        $stmt->close();
        break;

    case 'submit_zoom':
        $zoom_account_id = $_POST['zoom_account_id'] ?? '';
        $applicant_name  = $_POST['applicant_name']  ?? '';
        $applicant_unit  = $_POST['applicant_unit']  ?? '';
        $date_start     = !empty($_POST['date_start']) ? $_POST['date_start'] : null;
        $time_start     = !empty($_POST['time_start']) ? $_POST['time_start'] : null;
        $date_end       = !empty($_POST['date_end']) ? $_POST['date_end'] : null;
        $time_end       = !empty($_POST['time_end']) ? $_POST['time_end'] : null;
        $purpose        = $_POST['purpose']        ?? '';
        $participants    = (int)($_POST['participants'] ?? 0);
        $request_type    = $_POST['request_type']    ?? '';
        $special_needs   = $_POST['special_needs']   ?? '';

        $stmt = $conn->prepare("INSERT INTO zoom_requests (user_id, zoom_account_id, applicant_name, applicant_unit, date_start, time_start, date_end, time_end, purpose, participants, request_type, special_needs) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isssssssisss", $userId, $zoom_account_id, $applicant_name, $applicant_unit, $date_start, $time_start, $date_end, $time_end, $purpose, $participants, $request_type, $special_needs);
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            notifyNewRequest('Zoom', $newId, $applicant_name, $applicant_unit, $purpose);
            jsonResponse(true, 'Permintaan Zoom berhasil disimpan!', ['id' => $newId]);
        } else {
            jsonResponse(false, 'Gagal menyimpan data.');
        }
        $stmt->close();
        break;

    case 'submit_repair':
        $applicant_name  = $_POST['applicant_name']  ?? '';
        $applicant_unit  = $_POST['applicant_unit']  ?? '';
        $location_detail = $_POST['location_detail'] ?? '';
        $incident_date   = !empty($_POST['incident_date']) ? $_POST['incident_date'] : null;
        $incident_time   = !empty($_POST['incident_time']) ? $_POST['incident_time'] : null;
        $issue_description = $_POST['issue_description'] ?? '';
        $priority        = $_POST['priority']        ?? 'medium';

        $stmt = $conn->prepare("INSERT INTO repair_requests (user_id, applicant_name, applicant_unit, location_detail, incident_date, incident_time, issue_description, priority) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isssssss", $userId, $applicant_name, $applicant_unit, $location_detail, $incident_date, $incident_time, $issue_description, $priority);
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            notifyNewRequest('Repair', $newId, $applicant_name, $applicant_unit, $issue_description);
            jsonResponse(true, 'Laporan perbaikan berhasil dikirim!', ['id' => $newId]);
        } else {
            jsonResponse(false, 'Gagal menyimpan data.');
        }
        $stmt->close();
        break;

    case 'submit_item':
        $applicant_name = $_POST['applicant_name'] ?? '';
        $applicant_unit = $_POST['applicant_unit'] ?? '';
        $item_name      = $_POST['item_name']      ?? '';
        $item_quantity  = 1;
        $loan_date      = !empty($_POST['loan_date']) ? $_POST['loan_date'] : null;
        $loan_time      = !empty($_POST['loan_time']) ? $_POST['loan_time'] : null;
        $return_date    = !empty($_POST['return_date']) ? $_POST['return_date'] : null;
        $return_time    = !empty($_POST['return_time']) ? $_POST['return_time'] : null;
        $purpose        = $_POST['purpose']        ?? '';

        $stmt = $conn->prepare("INSERT INTO item_loan_requests (user_id, applicant_name, applicant_unit, item_name, item_quantity, loan_date, loan_time, return_date, return_time, purpose) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isssisssss", $userId, $applicant_name, $applicant_unit, $item_name, $item_quantity, $loan_date, $loan_time, $return_date, $return_time, $purpose);
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            notifyNewRequest('Item', $newId, $applicant_name, $applicant_unit, $purpose);
            jsonResponse(true, 'Permohonan peminjaman barang berhasil disimpan!', ['id' => $newId]);
        } else {
            jsonResponse(false, 'Gagal menyimpan data.');
        }
        $stmt->close();
        break;

    // ============================================================
    // 3. UPDATE STATUS & NOTE — setara updateRequestStatus()
    // ============================================================
    case 'update_status':
        $id        = (int)($_POST['id']     ?? 0);
        $type      = $_POST['type']         ?? '';
        $newStatus = $_POST['status']       ?? '';
        $noteInput = $_POST['note']         ?? '';
        $prevNote  = $_POST['prev_note']    ?? '';

        if (!$id || !$type || !$newStatus) {
            jsonResponse(false, 'Parameter tidak lengkap.');
        }

        $table = $tableMap[$type] ?? null;
        if (!$table) {
            jsonResponse(false, 'Tipe request tidak valid.');
        }

        // Jika PIC melanjutkan ke Manager FMD, paksa teks note jika kosong dengan detail item
        if ($newStatus === 'waiting_manager_fmd' && empty($noteInput)) {
            $resReq = $conn->query("SELECT * FROM `$table` WHERE id = $id");
            if ($resReq && $row = $resReq->fetch_assoc()) {
                $detailVal = '';
                if ($type === 'Vehicle') $detailVal = 'Kendaraan Dinas';
                else if ($type === 'Room') $detailVal = $row['room_id'] ?? 'Ruangan';
                else if ($type === 'Dormitory') $detailVal = $row['dormitory_id'] ?? 'Dormitory';
                else if ($type === 'Zoom') $detailVal = $row['zoom_account_id'] ?? 'Akun Zoom';
                else if ($type === 'Repair') $detailVal = $row['location_detail'] ?? 'Lokasi';
                else if ($type === 'Item') $detailVal = $row['item_name'] ?? 'Barang';
                
                $noteInput = "$detailVal tersedia, diteruskan kepada Manager FMD untuk approval permohonan";
            }
        }

        // Otomatisasi teks ketika Manager FMD Approve
        if ($newStatus === 'approved' && empty($noteInput)) {
            $resReq = $conn->query("SELECT status FROM `$table` WHERE id = $id");
            $row = $resReq->fetch_assoc();
            if ($row && $row['status'] === 'waiting_manager_fmd') {
                $typeLabels = [
                    'Vehicle' => 'Kendaraan (Unit & Driver)',
                    'Room'    => 'Ruangan & Fasilitas',
                    'Dormitory'=> 'Dormitory',
                    'Zoom'    => 'Akun Zoom/Link',
                    'Item'    => 'Peminjaman Barang',
                    'Repair'  => 'Perbaikan'
                ];
                $label = $typeLabels[$type] ?? $type;
                $noteInput = "Disetujui oleh Manager FMD. Silakan PIC $label menyiapkan permintaan dan memberikan laporan Check & Recheck.";
            }
        }

        // Otomatisasi teks ketika PIC melakukan Check & Recheck (approved → ready_for_user)
        if ($newStatus === 'ready_for_user' && empty($noteInput)) {
            $typeLabels = [
                'Vehicle' => 'Kendaraan', 'Room' => 'Ruangan',
                'Dormitory' => 'Dormitory',
                'Zoom' => 'Zoom/Virtual', 'Item' => 'Barang Pinjaman', 'Repair' => 'Perbaikan'
            ];
            $label = $typeLabels[$type] ?? $type;
            $noteInput = "PIC sedang melakukan Check & Recheck: mempersiapkan dan memastikan kebutuhan $label telah siap untuk diserahkan/dilaksanakan.";
        }

        // Otomatisasi teks ketika PIC menandai selesai (ready_for_user → completed/returned)
        if (in_array($newStatus, ['completed','returned']) && empty($noteInput)) {
            $typeLabels = [
                'Vehicle' => 'Kendaraan', 'Room' => 'Ruangan',
                'Dormitory' => 'Dormitory',
                'Zoom' => 'Zoom/Virtual', 'Item' => 'Barang Pinjaman', 'Repair' => 'Perbaikan'
            ];
            $label = $typeLabels[$type] ?? $type;
            $noteInput = $newStatus === 'returned'
                ? "PIC mengkonfirmasi: $label telah dikembalikan/diselesaikan. Permintaan tuntas."
                : "PIC mengkonfirmasi: seluruh kebutuhan $label telah terpenuhi. Permintaan selesai dilaksanakan.";
        }

        // Cek jika ada lampiran foto (khusus checklist ruangan dsb)
        $uploadedFiles = [];
        if (!empty($_FILES['foto_ruangan']['name'][0])) {
            $uploadDir = __DIR__ . '/../uploads/check_recheck/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            foreach ($_FILES['foto_ruangan']['name'] as $idx => $name) {
                if ($_FILES['foto_ruangan']['error'][$idx] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $newFileName = 'room_' . $id . '_' . time() . '_' . $idx . '.' . $ext;
                    $dest = $uploadDir . $newFileName;
                    if (move_uploaded_file($_FILES['foto_ruangan']['tmp_name'][$idx], $dest)) {
                        $uploadedFiles[] = 'uploads/check_recheck/' . $newFileName;
                    }
                }
            }
        }
        
        if (!empty($uploadedFiles)) {
            $baseAppUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . rtrim(dirname($_SERVER['PHP_SELF'], 2), '/') . '/';
            $links = array_map(function($path) use ($baseAppUrl) { return $baseAppUrl . $path; }, $uploadedFiles);
            $noteInput .= "\n\nLampiran Foto:\n- " . implode("\n- ", $links);
        }

        // Fetch current note directly from DB to prevent overwriting history due to stale frontend state
        $resCurrent = $conn->query("SELECT note FROM `$table` WHERE id = $id");
        $rowCurrent = $resCurrent ? $resCurrent->fetch_assoc() : null;
        $dbPrevNote = $rowCurrent ? ($rowCurrent['note'] ?? '') : '';

        // Buat log entry baru dan gabungkan dengan note di DB
        $newLog   = makeNoteLog($actorName, $newStatus, $noteInput);
        $finalNote = $dbPrevNote ? $dbPrevNote . "\n" . $newLog : $newLog;

        $stmt = $conn->prepare("UPDATE `$table` SET status = ?, note = ? WHERE id = ?");
        $stmt->bind_param("ssi", $newStatus, $finalNote, $id);
        if ($stmt->execute()) {
            // KIRIM NOTIFIKASI KE TELEGRAM USER
            notifyStatusUpdate($conn, $table, $id, $newStatus, $noteInput, $actorName);
            
            // JIKA VEHICLE DAN APPROVED, KIRIM NOTIF KE DRIVER
            if ($type === 'Vehicle' && $newStatus === 'approved') {
                $stmtDrv = $conn->prepare("SELECT v.vehicle_id, v.driver_name, v.applicant_name, DATE_FORMAT(v.date_start,'%d %b %Y') as ds, DATE_FORMAT(v.date_end,'%d %b %Y') as de, v.time_start, v.time_end, v.purpose, v.destination, u.whatsapp_number, u.telegram_chat_id FROM vehicle_requests v LEFT JOIN employees e ON v.driver_name = e.full_name LEFT JOIN users u ON e.id = u.employee_id WHERE v.id = ?");
                $stmtDrv->bind_param("i", $id);
                $stmtDrv->execute();
                $reqRow = $stmtDrv->get_result()->fetch_assoc();
                $stmtDrv->close();

                if ($reqRow && $reqRow['driver_name'] && trim($reqRow['driver_name']) !== '') {
                    $vName = $reqRow['vehicle_id'];
                    $stmtV = $conn->prepare("SELECT name FROM master_vehicles WHERE id = ?");
                    $stmtV->bind_param("s", $reqRow['vehicle_id']);
                    $stmtV->execute();
                    if ($resV = $stmtV->get_result()->fetch_assoc()) $vName = $resV['name'];
                    $stmtV->close();

                    $drvName = $reqRow['driver_name'];
                    $appName = $reqRow['applicant_name'];
                    $dest    = $reqRow['destination'] ?: '-';
                    $waktu   = $reqRow['ds'] . " jam " . substr($reqRow['time_start'], 0, 5) . " s/d " . $reqRow['de'] . " jam " . substr($reqRow['time_end'], 0, 5);
                    $purp    = $reqRow['purpose'] ?: '-';

                    if (!empty($reqRow['whatsapp_number']) && function_exists('sendWhatsAppFonnte')) {
                        $msgDriver = "🚗 *TUGAS BARU (DRIVER)*\n\nHalo *$drvName*,\nAnda telah ditugaskan sebagai pengemudi untuk pengajuan kendaraan *VEH-$id*.\n\n*Pemohon:* $appName\n*Kendaraan:* $vName\n*Lokasi Tujuan:* $dest\n*Waktu:* $waktu\n*Keperluan:* $purp\n\nMohon cek Dashboard Anda untuk detail lengkap.";
                        sendWhatsAppFonnte($msgDriver, $reqRow['whatsapp_number']);
                    }
                    if (!empty($reqRow['telegram_chat_id']) && function_exists('sendTelegramPHP')) {
                        $msgDriverTg = "🚗 <b>TUGAS BARU (DRIVER)</b>\n\nHalo <b>$drvName</b>,\nAnda telah ditugaskan sebagai pengemudi untuk pengajuan kendaraan <b>VEH-$id</b>.\n\n<b>Pemohon:</b> $appName\n<b>Kendaraan:</b> $vName\n<b>Lokasi Tujuan:</b> $dest\n<b>Waktu:</b> $waktu\n<b>Keperluan:</b> $purp\n\nMohon cek Dashboard Anda untuk detail lengkap.";
                        sendTelegramPHP($msgDriverTg, $reqRow['telegram_chat_id']);
                    }
                }
            }

            jsonResponse(true, 'Status berhasil diupdate.', ['finalNote' => $finalNote]);
        } else {
            jsonResponse(false, 'Gagal update status.');
        }
        $stmt->close();
        break;

    // ============================================================
    // 4. UPDATE VEHICLE ASSIGNMENT — setara updateVehicleAssignment()
    // ============================================================
    case 'update_vehicle_assignment':
        $id          = (int)($_POST['id']          ?? 0);
        $vehicleId   = $_POST['vehicle_id']        ?? '';
        $driverName  = $_POST['driver_name']       ?? '';

        // Ambil detail waktu request & pemohon
        $stmt = $conn->prepare("SELECT applicant_name, DATE_FORMAT(date_start,'%d %b %Y') as ds, DATE_FORMAT(date_end,'%d %b %Y') as de, time_start, time_end, purpose, destination FROM vehicle_requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $reqRow = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$reqRow) jsonResponse(false, 'Request tidak ditemukan.');

        $startDT = $reqRow['ds'] . ' ' . substr($reqRow['time_start'], 0, 5);
        $endDT   = $reqRow['de'] . ' ' . substr($reqRow['time_end'], 0, 5);

        // Cek konflik kendaraan
        if ($vehicleId && $vehicleId !== 'PENDING_ASSIGNMENT') {
            $stmt = $conn->prepare("SELECT id FROM vehicle_requests WHERE id != ? AND vehicle_id = ? AND status IN ('approved', 'ready_for_user', 'in-progress', 'verified', 'waiting_manager_fad', 'waiting_ppk', 'waiting_bod', 'approved_waiting_fund') AND CONCAT(DATE_FORMAT(date_start, '%Y-%m-%d'), ' ', SUBSTRING(time_start, 1, 5)) < ? AND CONCAT(DATE_FORMAT(date_end, '%Y-%m-%d'), ' ', SUBSTRING(time_end, 1, 5)) > ? LIMIT 1");
            $stmt->bind_param("isss", $id, $vehicleId, $endDT, $startDT);
            $stmt->execute();
            $resConf = $stmt->get_result();
            if ($resConf->num_rows > 0) {
                $conflictRow = $resConf->fetch_assoc();
                jsonResponse(false, 'Kendaraan sudah digunakan pada jam tersebut. (Bentrok dengan ID #' . $conflictRow['id'] . ')');
            }
            $stmt->close();
        }

        // Cek konflik driver
        if ($driverName && trim($driverName) !== '') {
            $stmt = $conn->prepare("SELECT id FROM vehicle_requests WHERE id != ? AND driver_name = ? AND status IN ('approved', 'ready_for_user', 'in-progress', 'verified', 'waiting_manager_fad', 'waiting_ppk', 'waiting_bod', 'approved_waiting_fund') AND CONCAT(DATE_FORMAT(date_start, '%Y-%m-%d'), ' ', SUBSTRING(time_start, 1, 5)) < ? AND CONCAT(DATE_FORMAT(date_end, '%Y-%m-%d'), ' ', SUBSTRING(time_end, 1, 5)) > ? LIMIT 1");
            $stmt->bind_param("isss", $id, $driverName, $endDT, $startDT);
            $stmt->execute();
            $resConf = $stmt->get_result();
            if ($resConf->num_rows > 0) {
                $conflictRow = $resConf->fetch_assoc();
                jsonResponse(false, 'Supir (Driver) sudah ditugaskan pada jam tersebut. (Bentrok dengan ID #' . $conflictRow['id'] . ')');
            }
            $stmt->close();
        }

        $stmt = $conn->prepare("UPDATE vehicle_requests SET vehicle_id = ?, driver_name = ? WHERE id = ?");
        $stmt->bind_param("ssi", $vehicleId, $driverName, $id);
        if ($stmt->execute()) {
            jsonResponse(true, 'Kendaraan dan Driver berhasil ditetapkan.');
        } else {
            jsonResponse(false, 'Gagal update kendaraan/driver.');
        }
        $stmt->close();
        break;

    // ============================================================
    // 4b. UPDATE ROOM ASSIGNMENT
    // ============================================================
    case 'update_room_assignment':
        $id      = (int)($_POST['id']      ?? 0);
        $roomId  = $_POST['room_id']       ?? '';

        if (!$id || !$roomId) jsonResponse(false, 'Parameter tidak lengkap.');

        // Ambil detail waktu request
        $stmt = $conn->prepare("SELECT DATE_FORMAT(date_start,'%Y-%m-%d') as ds, DATE_FORMAT(date_end,'%Y-%m-%d') as de, time_start, time_end FROM room_requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $reqRow = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$reqRow) jsonResponse(false, 'Request tidak ditemukan.');

        $startDT = $reqRow['ds'] . ' ' . substr($reqRow['time_start'], 0, 5);
        $endDT   = $reqRow['de'] . ' ' . substr($reqRow['time_end'], 0, 5);

        // Cek konflik ruangan
        $stmt = $conn->prepare("SELECT id FROM room_requests WHERE id != ? AND room_id = ? AND status IN ('approved', 'ready_for_user', 'in-progress', 'verified', 'waiting_manager_fad', 'waiting_ppk', 'waiting_bod', 'approved_waiting_fund') AND CONCAT(DATE_FORMAT(date_start, '%Y-%m-%d'), ' ', SUBSTRING(time_start, 1, 5)) < ? AND CONCAT(DATE_FORMAT(date_end, '%Y-%m-%d'), ' ', SUBSTRING(time_end, 1, 5)) > ? LIMIT 1");
        $stmt->bind_param("isss", $id, $roomId, $endDT, $startDT);
        $stmt->execute();
        $resConf = $stmt->get_result();
        if ($resConf->num_rows > 0) {
            $conflictRow = $resConf->fetch_assoc();
            jsonResponse(false, 'Ruangan sudah dipesan pada jam tersebut. (Bentrok dengan ID #' . $conflictRow['id'] . ')');
        }
        $stmt->close();

        $stmt = $conn->prepare("UPDATE room_requests SET room_id = ? WHERE id = ?");
        $stmt->bind_param("si", $roomId, $id);
        if ($stmt->execute()) {
            jsonResponse(true, 'Ruangan berhasil dipindahkan/ditetapkan.');
        } else {
            jsonResponse(false, 'Gagal update ruangan.');
        }
        $stmt->close();
        break;

    case 'update_dormitory_assignment':
        $id      = (int)($_POST['id']      ?? 0);
        $dormitoryId  = $_POST['dormitory_id']       ?? '';

        if (!$id || !$dormitoryId) jsonResponse(false, 'Parameter tidak lengkap.');

        // Ambil detail waktu request
        $stmt = $conn->prepare("SELECT DATE_FORMAT(date_start,'%Y-%m-%d') as ds, DATE_FORMAT(date_end,'%Y-%m-%d') as de, time_start, time_end FROM dormitory_requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $reqRow = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$reqRow) jsonResponse(false, 'Request tidak ditemukan.');

        $startDT = $reqRow['ds'] . ' ' . substr($reqRow['time_start'], 0, 5);
        $endDT   = $reqRow['de'] . ' ' . substr($reqRow['time_end'], 0, 5);

        // Cek konflik dormitory
        $stmt = $conn->prepare("SELECT id FROM dormitory_requests WHERE id != ? AND dormitory_id = ? AND status IN ('approved', 'ready_for_user', 'in-progress', 'verified', 'waiting_manager_fad', 'waiting_ppk', 'waiting_bod', 'approved_waiting_fund') AND CONCAT(DATE_FORMAT(date_start, '%Y-%m-%d'), ' ', SUBSTRING(time_start, 1, 5)) < ? AND CONCAT(DATE_FORMAT(date_end, '%Y-%m-%d'), ' ', SUBSTRING(time_end, 1, 5)) > ? LIMIT 1");
        $stmt->bind_param("isss", $id, $dormitoryId, $endDT, $startDT);
        $stmt->execute();
        $resConf = $stmt->get_result();
        if ($resConf->num_rows > 0) {
            $conflictRow = $resConf->fetch_assoc();
            jsonResponse(false, 'Dormitory sudah dipesan pada jam tersebut. (Bentrok dengan ID #' . $conflictRow['id'] . ')');
        }
        $stmt->close();

        $stmt = $conn->prepare("UPDATE dormitory_requests SET dormitory_id = ? WHERE id = ?");
        $stmt->bind_param("si", $dormitoryId, $id);
        if ($stmt->execute()) {
            jsonResponse(true, 'Dormitory berhasil dipindahkan/ditetapkan.');
        } else {
            jsonResponse(false, 'Gagal update dormitory.');
        }
        $stmt->close();
        break;

    // ============================================================
    // 5. UPDATE VEHICLE REQUEST TIME — setara updateVehicleRequestTime()
    // ============================================================
    case 'update_vehicle_time':
        $id        = (int)($_POST['id']         ?? 0);
        $dateStart = $_POST['date_start']       ?? '';
        $timeStart = $_POST['time_start']       ?? '';
        $dateEnd   = $_POST['date_end']         ?? '';
        $timeEnd   = $_POST['time_end']         ?? '';

        $stmt = $conn->prepare("UPDATE vehicle_requests SET date_start = ?, time_start = ?, date_end = ?, time_end = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $dateStart, $timeStart, $dateEnd, $timeEnd, $id);
        if ($stmt->execute()) {
            jsonResponse(true, 'Waktu peminjaman berhasil diubah.');
        } else {
            jsonResponse(false, 'Gagal mengubah waktu.');
        }
        $stmt->close();
        break;

    // ============================================================
    // 6. REPAIR BUDGET — setara submitRepairBudget() & getRepairBudget()
    // ============================================================
    case 'get_repair_budget':
        $requestId = (int)($_GET['request_id'] ?? 0);
        $stmt = $conn->prepare("SELECT id, item_name, quantity, unit_price, total_price FROM repair_budgets WHERE repair_request_id = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        $stmt->close();
        break;

    case 'submit_repair_budget':
        $requestId = (int)($_POST['request_id'] ?? 0);
        $itemsJson = $_POST['items']             ?? '[]';
        $items     = json_decode($itemsJson, true) ?? [];

        if (!$requestId || empty($items)) {
            jsonResponse(false, 'Data tidak lengkap.');
        }

        $conn->autocommit(false);
        try {
            // Hapus RAB lama
            $del = $conn->prepare("DELETE FROM repair_budgets WHERE repair_request_id = ?");
            $del->bind_param("i", $requestId);
            $del->execute();
            $del->close();

            // Insert RAB baru
            $ins = $conn->prepare("INSERT INTO repair_budgets (repair_request_id, item_name, quantity, unit_price, total_price) VALUES (?,?,?,?,?)");
            $totalRAB = 0;
            foreach ($items as $item) {
                $lineTotal  = (float)$item['quantity'] * (float)$item['unitPrice'];
                $totalRAB  += $lineTotal;
                $ins->bind_param("isidd", $requestId, $item['itemName'], $item['quantity'], $item['unitPrice'], $lineTotal);
                $ins->execute();
            }
            $ins->close();

            // Update status -> 'verified'
            $ts       = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('d M Y H:i');
            $noteLog  = "\n[$ts] [System] - STATUS UPDATE: RAB Diajukan (Rp " . number_format($totalRAB, 0, ',', '.') . ") - Verified (Menunggu Approval Manager FMD)";
            $upd = $conn->prepare("UPDATE repair_requests SET status = 'verified', note = CONCAT(IFNULL(note,''),?) WHERE id = ?");
            $upd->bind_param("si", $noteLog, $requestId);
            $upd->execute();
            $upd->close();

            // KIRIM NOTIFIKASI TELEGRAM
            notifyStatusUpdate($conn, 'repair_requests', $requestId, 'verified', "RAB Diajukan (Rp " . number_format($totalRAB, 0, ',', '.') . ")", 'System');

            $conn->commit();
            jsonResponse(true, 'RAB berhasil diajukan!');
        } catch (Exception $e) {
            $conn->rollback();
            jsonResponse(false, 'Gagal mengajukan RAB: ' . $e->getMessage());
        } finally {
            $conn->autocommit(true);
        }
        break;

    case 'approve_repair_budget':
        $requestId = (int)($_POST['request_id'] ?? 0);

        // Hitung total RAB
        $stmt = $conn->prepare("SELECT SUM(total_price) as total_rab FROM repair_budgets WHERE repair_request_id = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $row      = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $totalRAB = (float)($row['total_rab'] ?? 0);

        if ($totalRAB <= 20000000) {
            $newStatus  = 'waiting_manager_fad';
            $statusDesc = 'Menunggu Approval Manager FAD (< 20 Juta)';
        } elseif ($totalRAB <= 50000000) {
            $newStatus  = 'waiting_ppk';
            $statusDesc = 'Menunggu Approval PPK (20 - 50 Juta)';
        } else {
            $newStatus  = 'waiting_bod';
            $statusDesc = 'Menunggu Approval BOD (> 50 Juta)';
        }

        $ts      = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('d M Y H:i');
        $noteLog = "\n[$ts] [System]: RAB Disetujui Manager FMD. Lanjut ke alur: $statusDesc.";

        $stmt = $conn->prepare("UPDATE repair_requests SET status = ?, note = CONCAT(IFNULL(note,''),?) WHERE id = ?");
        $stmt->bind_param("ssi", $newStatus, $noteLog, $requestId);
        if ($stmt->execute()) {
            // KIRIM NOTIFIKASI TELEGRAM
            notifyStatusUpdate($conn, 'repair_requests', $requestId, $newStatus, "RAB Disetujui Manager FMD - Lanjut ke: $statusDesc", 'System');
            
            jsonResponse(true, "RAB Disetujui! Status: $statusDesc");
        } else {
            jsonResponse(false, 'Gagal menyetujui RAB.');
        }
        $stmt->close();
        break;

    default:
        jsonResponse(false, "Action '$action' tidak dikenali.");
}
