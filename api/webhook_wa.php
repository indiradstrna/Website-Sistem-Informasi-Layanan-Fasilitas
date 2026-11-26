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

$sender = trim((string)($data['sender'] ?? ''));
$message = trim((string)($data['message'] ?? ''));

// Abaikan pesan kosong, pesan dari Grup WA (@g.us / isgroup), atau status broadcast
$isGroup = !empty($data['isgroup']) && $data['isgroup'] !== 'false' && $data['isgroup'] !== '0';
if (!$sender || !$message || $isGroup || stripos($sender, '@g.us') !== false || stripos($sender, 'broadcast') !== false) {
    http_response_code(200);
    exit;
}

// Hanya proses jika pesan dimulai dengan SETUJU, TOLAK, SELESAI, COMPLETED, atau SIAP
if (!preg_match('/^(SETUJU|TOLAK|SELESAI|COMPLETED|SIAP)\s+([A-Z]+)-(\d+)(?:\s*([a-zA-Z0])(?:\s*(\d+))?)?$/i', $message, $matches)) {
    http_response_code(200); // Ignore non-command messages
    exit;
}

// Cegah eksekusi ganda jika webhook Fonnte me-retry pesan identik dalam 30 detik terakhir
$dedupeFile = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'silatas_wa_webhook_dedupe.json';
$dedupeKey  = md5(strtolower($sender . '|' . preg_replace('/\s+/', ' ', $message)));
$nowTs      = time();
$dedupeMap  = [];
if (is_file($dedupeFile)) {
    $rawDedupe = @file_get_contents($dedupeFile);
    $parsedDedupe = $rawDedupe ? json_decode($rawDedupe, true) : null;
    if (is_array($parsedDedupe)) {
        foreach ($parsedDedupe as $k => $tsVal) {
            if (($nowTs - (int)$tsVal) < 30) {
                $dedupeMap[$k] = (int)$tsVal;
            }
        }
    }
}
if (isset($dedupeMap[$dedupeKey])) {
    http_response_code(200);
    exit;
}
$dedupeMap[$dedupeKey] = $nowTs;
@file_put_contents($dedupeFile, json_encode($dedupeMap), LOCK_EX);

$actionTypeRaw = strtoupper($matches[1]);
$isSiapCmd     = ($actionTypeRaw === 'SIAP');
$actionType    = in_array($actionTypeRaw, ['SELESAI', 'COMPLETED', 'SIAP'], true) ? 'SETUJU' : $actionTypeRaw; // SETUJU / TOLAK / (SELESAI|SIAP -> SETUJU)
$typeCode   = strtoupper($matches[2]); // VEH
$reqId      = (int)$matches[3]; // 15
$optLetter  = isset($matches[4]) ? strtoupper($matches[4]) : ''; // A
$optNumber  = isset($matches[5]) && $matches[5] !== '' ? (int)$matches[5] : -1; // -1 means not provided

// Normalisasi nomor WA (Hapus 62 atau 0 di depan untuk pencarian yang fleksibel)
$cleanSender = preg_replace('/^(62|0)/', '', preg_replace('/\D+/', '', $sender));
if (strlen($cleanSender) < 8) {
    http_response_code(200);
    exit;
}

// 1. Cari user di DB berdasarkan nomor WA
$stmt = $conn->prepare("SELECT u.id, u.full_name, u.role, u.employee_id, COALESCE(e.full_name, u.full_name) as emp_full_name, e.nip_nik as username, e.department, e.role_title FROM users u LEFT JOIN employees e ON u.employee_id = e.id WHERE u.whatsapp_number LIKE ? ORDER BY u.employee_id IS NOT NULL DESC, u.id ASC LIMIT 1");
$likeSender = "%$cleanSender%";
$stmt->bind_param("s", $likeSender);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    // Jangan membalas otomatis ke nomor asing yang tidak terdaftar di database (mencegah risiko report/block)
    file_put_contents(__DIR__ . '/webhook_log.txt', date('Y-m-d H:i:s') . " - IGNORED UNREGISTERED SENDER: {$sender}\n", FILE_APPEND);
    http_response_code(200);
    exit;
}

// 2. Tentukan tabel & Cek Role Dasar untuk Resolusi Tabel
$role = $user['role'];
$username = $user['username'];
$isManagerFMD = ($role === 'managerFMD' || $username === '197707072025211067');

$tableMap = [
    'VEH' => 'vehicle_requests',
    'ROM' => 'room_requests',
    'DRM' => 'dormitory_requests',
    'ZOM' => 'zoom_requests',
    'REP' => 'repair_requests',
    'ITM' => 'item_loan_requests',
    'BRG' => 'item_requests'
];

$table = $tableMap[$typeCode] ?? null;
if ($typeCode === 'ITM') {
    $resLoan = $conn->query("SELECT id, user_id, status FROM item_loan_requests WHERE id = $reqId");
    $rowLoan = ($resLoan && $resLoan->num_rows > 0) ? $resLoan->fetch_assoc() : null;

    $resReq  = $conn->query("SELECT id, user_id, applicant_unit, status FROM item_requests WHERE id = $reqId");
    $rowReq  = ($resReq && $resReq->num_rows > 0) ? $resReq->fetch_assoc() : null;

    if ($rowLoan && !$rowReq) {
        $table = 'item_loan_requests';
    } elseif (!$rowLoan && $rowReq) {
        $table = 'item_requests';
    } elseif ($rowLoan && $rowReq) {
        // Jika kedua tabel sama-sama memiliki ID tersebut (bentrok ID antara Peminjaman Barang & Permintaan Barang),
        // pilih tabel yang statusnya sesuai dengan hak akses pengirim WA saat ini:
        $loanStatus = $rowLoan['status'];
        $reqStatus  = $rowReq['status'];

        if ($isManagerFMD) {
            if ($loanStatus === 'waiting_manager_fmd' && $reqStatus !== 'waiting_manager_fmd') {
                $table = 'item_loan_requests';
            } elseif ($reqStatus === 'waiting_manager_fmd' && $loanStatus !== 'waiting_manager_fmd') {
                $table = 'item_requests';
            } else {
                // Jika Manager FMD adalah Manajer Dept FMD untuk item_requests
                $isFmdUnit = stripos($rowReq['applicant_unit'] ?? '', 'fmd') !== false;
                $table = ($isFmdUnit && $reqStatus === 'waiting_manager_unit') ? 'item_requests' : 'item_loan_requests';
            }
        } else {
            // Cek apakah pengirim adalah PIC ITM
            $isPicItm = in_array($username, ['198902222025211044', '198605082025211053']);
            if ($isPicItm) {
                if (in_array($loanStatus, ['pending', 'approved']) && !in_array($reqStatus, ['verified', 'approved', 'ready_for_user'])) {
                    $table = 'item_loan_requests';
                } elseif (in_array($reqStatus, ['verified', 'approved', 'ready_for_user'])) {
                    $table = 'item_requests';
                } else {
                    $table = 'item_loan_requests';
                }
            } else {
                // Selain Manager FMD & PIC (misal Manajer Departemen lain atau Pemohon)
                $table = 'item_requests';
                if ($rowLoan['user_id'] == $user['id'] && in_array($loanStatus, ['approved', 'ready_for_user'])) {
                    $table = 'item_loan_requests';
                }
            }
        }
    }
}

if (!$table) {
    sendWhatsAppFonnte("Maaf, format tipe pengajuan ($typeCode) tidak valid.", $sender, ['mode' => 'reply']);
    http_response_code(200);
    exit;
}

// 3. Ambil data pengajuan
$resReq = $conn->query("SELECT * FROM `$table` WHERE id = $reqId");
$requestData = $resReq ? $resReq->fetch_assoc() : null;

if (!$requestData) {
    sendWhatsAppFonnte("Maaf, pengajuan dengan ID #$reqId tidak ditemukan.", $sender, ['mode' => 'reply']);
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
        if ($optLetter !== '') {
            if ($optLetter === '0') {
                $selectedVehicleId = 'TANPA_KENDARAAN';
                $vehicleNameStr = 'Tanpa Kendaraan (Hanya Jasa Driver)';
            } else {
                $idx = ord($optLetter) - 65;
                $resV = $conn->query("SELECT id, name FROM master_vehicles ORDER BY id ASC LIMIT $idx, 1");
                if ($resV && $v = $resV->fetch_assoc()) {
                    $selectedVehicleId = $v['id'];
                    $vehicleNameStr = $v['name'];
                }
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
            $resR = $conn->query("SELECT id, name FROM master_rooms ORDER BY id ASC LIMIT $idx, 1");
            if ($resR && $r = $resR->fetch_assoc()) {
                $selectedRoomId = $r['id'];
                $roomNameStr = $r['name'];
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
    'ITM' => ['198902222025211044', '198605082025211053'], 
    'BRG' => ['198902222025211044', '198605082025211053'], 
    'ZOM' => ['198902222025211044'], 
    'ROM' => ['199008092025212052', '16268300055'], 
    'DRM' => ['199008092025212052', '16268300055'], 
    'REP' => ['198605082025211053', '197212162014091003', '198902222025211044', '16268000027']
];
$isPIC = in_array($username, $picMap[$typeCode] ?? []);

// Logika Transisi Status
if ($table === 'item_requests') {
    if ($currentStatus === 'waiting_manager_unit' || $currentStatus === 'pending') {
        // Cek apakah user adalah Manajer Departemen dari pengaju
        $appUnit = '';
        $resUnit = $conn->query("SELECT applicant_unit FROM item_requests WHERE id = $reqId");
        if ($resUnit && $rowU = $resUnit->fetch_assoc()) $appUnit = trim($rowU['applicant_unit']);

        $cleanUnit = strtolower(trim(str_ireplace(['divisi', 'bagian', 'unit', 'departemen', 'department'], '', $appUnit)));
        if ($cleanUnit === 'hrd') $cleanUnit = 'hrad';
        $isFmdReq = ($cleanUnit === 'fmd' || stripos($appUnit, 'fmd') !== false);

        $isDeptMgr = false;
        $eDept  = strtolower(trim($user['department'] ?? ''));
        $eTitle = strtolower(trim($user['role_title'] ?? ''));

        if ((empty($eDept) || empty($eTitle)) && !empty($user['employee_id'])) {
            $stmtEmp = $conn->prepare("SELECT department, role_title FROM employees WHERE id = ?");
            $stmtEmp->bind_param("i", $user['employee_id']);
            $stmtEmp->execute();
            $empRow = $stmtEmp->get_result()->fetch_assoc();
            $stmtEmp->close();
            if ($empRow) {
                $eDept  = strtolower(trim($empRow['department'] ?? ''));
                $eTitle = strtolower(trim($empRow['role_title'] ?? ''));
            }
        }

        if ($eDept && $eTitle) {
            $cleanMyDept = strtolower(trim(str_ireplace(['divisi', 'bagian', 'unit', 'departemen', 'department'], '', $eDept)));
            if ($cleanMyDept === 'hrd') $cleanMyDept = 'hrad';

            $isMgrTitle = (strpos($eTitle, 'manajer') !== false || strpos($eTitle, 'manager') !== false || strpos($eTitle, 'direktur') !== false || strpos($eTitle, 'pejabat') !== false);
            if ($isMgrTitle && ($cleanUnit === $cleanMyDept || strpos($cleanUnit, $cleanMyDept) !== false || strpos($cleanMyDept, $cleanUnit) !== false || strpos(strtolower($appUnit), $eDept) !== false)) {
                $isDeptMgr = true;
            }
        }

        // Khusus pengajuan dari unit FMD yang masih berstatus waiting_manager_unit:
        // Bisa diproses langsung oleh PIC Gudang (ke waiting_manager_fmd) atau Manager FMD (ke verified / PIC Gudang)
        if ($isFmdReq && $isPIC) {
            $canProcess = true;
            $nextStatusApprove = 'waiting_manager_fmd';
        } else if ($isFmdReq && $isManagerFMD) {
            $canProcess = true;
            $nextStatusApprove = 'verified';
        } else if ($isDeptMgr || $role === 'superadmin') {
            $canProcess = true;
            $nextStatusApprove = 'verified'; // Masuk ke PIC Permintaan Barang
        } else {
            sendWhatsAppFonnte("Maaf, pengajuan ini memerlukan persetujuan dari Manajer Departemen ($appUnit).", $sender, ['mode' => 'reply']);
            http_response_code(200);
            exit;
        }
    } else if ($currentStatus === 'verified') {
        // PIC Gudang meneruskan ke Manager FMD
        if ($isPIC || $role === 'superadmin') {
            $canProcess = true;
            $nextStatusApprove = 'waiting_manager_fmd';
        }
    } else if ($currentStatus === 'waiting_manager_fmd') {
        // Manager FMD memberi persetujuan
        if ($isManagerFMD || $role === 'superadmin') {
            $canProcess = true;
            $nextStatusApprove = 'approved';
        }
    } else if ($currentStatus === 'approved') {
        // PIC Gudang menyiapkan barang & ubah status ke ready_for_user
        if ($isPIC || $role === 'superadmin') {
            $canProcess = true;
            $nextStatusApprove = 'ready_for_user';
        }
    } else if ($currentStatus === 'ready_for_user') {
        // Konfirmasi selesai
        $isApplicant = false;
        $stmtA = $conn->prepare("SELECT user_id FROM item_requests WHERE id = ?");
        $stmtA->bind_param("i", $reqId);
        $stmtA->execute();
        $resA = $stmtA->get_result()->fetch_assoc();
        $stmtA->close();
        if ($resA && $resA['user_id'] == $user['id']) $isApplicant = true;

        if ($isApplicant || $isPIC || $role === 'superadmin') {
            $canProcess = true;
            $nextStatusApprove = 'completed';
        }
    }
} else if ($currentStatus === 'pending') {
    if ($isPIC) {
        if ($typeCode === 'REP') {
            sendWhatsAppFonnte("Maaf, pengajuan Perbaikan (Repair) tidak dapat diproses via WhatsApp karena memerlukan pemilihan Metode Penanganan dan pengeluaran Sparepart/Barang. Silakan login ke Dashboard Web SILATAS untuk memprosesnya.", $sender, ['mode' => 'reply']);
            http_response_code(200);
            exit;
        }
        if ($actionType === 'TOLAK') {
            sendWhatsAppFonnte("Maaf, PIC tidak dapat menolak pengajuan. Semua pengajuan harus diteruskan ke Manager FMD. Balas SETUJU untuk meneruskan.", $sender, ['mode' => 'reply']);
            http_response_code(200);
            exit;
        }
        $canProcess = true;
        $nextStatusApprove = 'waiting_manager_fmd'; // Default PIC forward
    }
} else if ($currentStatus === 'waiting_manager_fmd') {
    if ($isManagerFMD || $role === 'superadmin') {
        $canProcess = true;
        if ($typeCode === 'REP') {
            $resBgt = $conn->query("SELECT COALESCE(SUM(total_price), 0) AS total FROM repair_budgets WHERE repair_request_id = $reqId");
            $rowBgt = $resBgt ? $resBgt->fetch_assoc() : null;
            $budgetTotal = (float)($rowBgt['total'] ?? 0);
            if ($budgetTotal > 50000000) {
                $nextStatusApprove = 'waiting_bod';
            } else if ($budgetTotal > 20000000) {
                $nextStatusApprove = 'waiting_ppk';
            } else if ($budgetTotal > 0) {
                $nextStatusApprove = 'waiting_manager_fad';
            } else {
                $nextStatusApprove = 'approved';
            }
        } else {
            $nextStatusApprove = 'approved';
        }
    }
} else if ($currentStatus === 'waiting_manager_fad') {
    if ($role === 'managerFAD' || $role === 'superadmin') {
        $canProcess = true;
        $nextStatusApprove = ($typeCode === 'REP') ? 'approved_waiting_fund' : 'approved';
    }
} else if ($currentStatus === 'waiting_ppk') {
    if ($role === 'ppk' || $role === 'superadmin') {
        $canProcess = true;
        $nextStatusApprove = ($typeCode === 'REP') ? 'waiting_manager_fad' : 'approved_waiting_fund';
    }
} else if ($currentStatus === 'waiting_bod') {
    if ($role === 'bod' || $role === 'superadmin') {
        $canProcess = true;
        $nextStatusApprove = ($typeCode === 'REP') ? 'waiting_ppk' : 'approved';
    }
} else if ($currentStatus === 'approved_waiting_fund') {
    if ($role === 'bendahara' || $role === 'superadmin') {
        $canProcess = true;
        $nextStatusApprove = ($typeCode === 'REP') ? 'in-progress' : 'approved';
    }
} else if ($currentStatus === 'approved' || $currentStatus === 'ready_for_user' || $currentStatus === 'in-progress') {
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

    $callerEmpName    = trim((string)($user['emp_full_name'] ?? $user['full_name'] ?? ''));
    $isAssignedDriver = ($typeCode === 'VEH') && isVehicleDriverMatch($requestData['driver_name'] ?? '', $callerEmpName);

    if ($isSiapCmd) {
        if ($typeCode !== 'VEH' || $currentStatus !== 'approved') {
            sendWhatsAppFonnte("Perintah SIAP hanya dapat digunakan untuk kendaraan (VEH) yang berstatus Approved.", $sender, ['mode' => 'reply']);
            http_response_code(200);
            exit;
        }
        if (!$isPIC && $role !== 'superadmin') {
            sendWhatsAppFonnte("Perintah SIAP VEH-$reqId hanya dapat diproses oleh PIC Kendaraan.", $sender, ['mode' => 'reply']);
            http_response_code(200);
            exit;
        }
    }

    if ($typeCode === 'REP') {
        if ($currentStatus === 'approved') {
            if ($isPIC || $role === 'superadmin') {
                $canProcess = true;
                $nextStatusApprove = 'in-progress';
            } else if ($isApplicant && in_array($actionTypeRaw, ['SELESAI', 'COMPLETED'], true)) {
                $canProcess = true;
                $nextStatusApprove = 'completed';
            }
        } else if (in_array($currentStatus, ['in-progress', 'ready_for_user'], true)) {
            if ($isPIC || $role === 'superadmin' || $isApplicant) {
                $canProcess = true;
                $nextStatusApprove = 'completed';
            }
        }
    } else if ($typeCode === 'VEH') {
        if ($currentStatus === 'approved') {
            if ($isPIC || $role === 'superadmin') {
                $canProcess = true;
                $nextStatusApprove = 'ready_for_user';
            } else if ($isApplicant && in_array($actionTypeRaw, ['SELESAI', 'COMPLETED'], true)) {
                $canProcess = true;
                $nextStatusApprove = 'completed';
            }
        } else {
            // ready_for_user atau in-progress pada VEH
            if ($isApplicant || $isAssignedDriver || $isPIC || $role === 'superadmin') {
                if ($actionType === 'TOLAK' && $isAssignedDriver && $role !== 'superadmin') {
                    sendWhatsAppFonnte("Maaf, Driver hanya dapat mengkonfirmasi penyelesaian tugas (balas: *SELESAI VEH-$reqId*).", $sender, ['mode' => 'reply']);
                    http_response_code(200);
                    exit;
                }
                $canProcess = true;
                $nextStatusApprove = 'completed';
            }
        }
    } else if ($currentStatus !== 'in-progress') {
        if ($isApplicant) {
            $canProcess = true;
            $nextStatusApprove = 'completed';
        } else if ($currentStatus === 'approved' && ($isPIC || $role === 'superadmin')) {
            $canProcess = true;
            $nextStatusApprove = 'ready_for_user'; // PIC marks as ready
        }
    }
}

if (!$canProcess) {
    sendWhatsAppFonnte("Anda tidak memiliki hak akses atau pengajuan *$typeCode-$reqId* saat ini berada di tahap ('$currentStatus') yang bukan tanggung jawab Anda.", $sender, ['mode' => 'reply']);
    http_response_code(200);
    exit;
}

// 5. Eksekusi Perubahan
$finalStatus = ($actionType === 'SETUJU') ? $nextStatusApprove : $nextStatusReject;

// Generate dynamic note similar to web
$actionNote = "Pengajuan diproses melalui WhatsApp";
if ($actionType === 'SETUJU') {
    if ($table === 'item_requests') {
        if ($nextStatusApprove === 'verified') {
            $actionNote = "Disetujui oleh Manajer Departemen. Diteruskan ke PIC Gudang.";
        } else if ($nextStatusApprove === 'waiting_manager_fmd') {
            $actionNote = "Daftar barang telah diverifikasi oleh PIC Gudang. Diteruskan kepada Manager FMD untuk persetujuan.";
        } else if ($nextStatusApprove === 'approved') {
            $actionNote = "Disetujui oleh Manager FMD. Pengajuan dikembalikan ke PIC Gudang untuk penyiapan barang.";
        } else if ($nextStatusApprove === 'ready_for_user') {
            $actionNote = "Barang telah siap di gudang. Silakan diambil dalam estimasi waktu 10-15 menit.";
        } else if ($nextStatusApprove === 'completed') {
            $actionNote = "Permintaan barang telah selesai diserahkan kepada pemohon.";
        }
    } else if ($currentStatus === 'pending' && $nextStatusApprove === 'waiting_manager_fmd') {
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
            $actionNote = "Disetujui oleh Manager FMD. Pengajuan perbaikan siap diproses oleh PIC/Teknisi (In-Progress).";
        }
    } else if ($typeCode === 'REP' && $nextStatusApprove === 'in-progress') {
        $actionNote = "PIC/Teknisi memulai proses pengerjaan perbaikan.";
    } else if ($typeCode === 'REP' && $nextStatusApprove === 'completed') {
        $actionNote = "PIC/Teknisi mengkonfirmasi: perbaikan telah selesai dilaksanakan.";
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
    } else if (in_array($currentStatus, ['ready_for_user', 'in-progress'], true) && $nextStatusApprove === 'completed') {
        $actionNote = "PIC mengkonfirmasi: seluruh kebutuhan telah terpenuhi. Permintaan selesai dilaksanakan.";
        if (isset($requestData['user_id']) && $requestData['user_id'] == $user['id']) {
            $actionNote = "Pengajuan diselesaikan oleh Pemohon.";
        } else if ($typeCode === 'VEH' && !empty($isAssignedDriver)) {
            $actionNote = "Driver (" . ($requestData['driver_name'] ?? $user['full_name']) . ") mengkonfirmasi: tugas pengantaran kendaraan telah selesai dilaksanakan.";
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
         sendWhatsAppFonnte("❌ Gagal!\nMohon sertakan kode kendaraan & supir yang valid.\nContoh balas: *SETUJU VEH-$reqId A1*", $sender, ['mode' => 'reply']);
         exit;
    }
    $stmtStr = "UPDATE `$table` SET status = ?, note = ?, vehicle_id = ?, driver_name = ? WHERE id = ?";
    $params = ["ssssi", $finalStatus, $finalNote, $selectedVehicleId, $selectedDriverName, $reqId];
} else if ($currentStatus === 'pending' && $typeCode === 'ROM' && $actionType === 'SETUJU') {
    if (!$selectedRoomId) {
         sendWhatsAppFonnte("❌ Gagal!\nMohon sertakan kode ruangan yang valid.\nContoh balas: *SETUJU ROM-$reqId A*", $sender, ['mode' => 'reply']);
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
    // Jika Permintaan Barang (item_requests) selesai (completed), otomatis potong stok gudang
    if ($table === 'item_requests' && $finalStatus === 'completed') {
        deductItem2InventoryStock($conn, $reqId, (int)$user['id']);
    }

    $reply = "✅ Berhasil!\nPengajuan *$typeCode-$reqId* telah diubah statusnya menjadi *$finalStatus*.";
    sendWhatsAppFonnte($reply, $sender, ['mode' => 'reply']);
    
    // Notifikasi ke Supir sebagai info tugas baru saat status masuk waiting_manager_fmd
    if ($typeCode === 'VEH' && $finalStatus === 'waiting_manager_fmd' && hasAssignedVehicleDriver($selectedDriverName)) {
        notifyAssignedDriverNewTaskPendingApproval($conn, $reqId, true);
    }
    
    // Panggil fungsi notifikasi agar dikirim ke Manager FMD / User / Approver berikutnya
    notifyStatusUpdate($conn, $table, $reqId, $finalStatus, $actionNote, $user['full_name']);
} else {
    sendWhatsAppFonnte("❌ Gagal mengupdate database server.", $sender, ['mode' => 'reply']);
}

$stmt->close();
http_response_code(200);
