<?php
/**
 * File: api/notifications.php
 * Berisi fungsi-fungsi notifikasi untuk pengajuan (WhatsApp & Telegram)
 */

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
        'Item'    => 'ITM',
        'Item2'   => 'ITM'
    ];
    $code = $typeCodes[$type] ?? 'REQ';
    $approvableStatuses = ['pending', 'waiting_manager_fmd', 'waiting_manager_fad', 'waiting_ppk', 'waiting_bod', 'approved_waiting_fund', 'approved', 'ready_for_user'];
    
    if (in_array($newStatus, $approvableStatuses)) {
        if ($newStatus === 'pending') {
            $promptWa = "\n*Untuk meneruskan ke Manager FMD, balas:*\nSETUJU {$code}-{$id}";
            $promptTg = "\n<b>Untuk meneruskan ke Manager FMD, balas:</b>\nSETUJU {$code}-{$id}";
        } else if ($newStatus === 'waiting_manager_fmd') {
            $promptWa = "\n*Untuk menyetujui pengajuan ini (Manager FMD), balas:*\nSETUJU {$code}-{$id}";
            $promptTg = "\n<b>Untuk menyetujui pengajuan ini (Manager FMD), balas:</b>\nSETUJU {$code}-{$id}";
        } else if ($newStatus === 'waiting_manager_fad') {
            $promptWa = "\n*Untuk menyetujui pengajuan ini (Manager FAD), balas:*\nSETUJU {$code}-{$id}";
            $promptTg = "\n<b>Untuk menyetujui pengajuan ini (Manager FAD), balas:</b>\nSETUJU {$code}-{$id}";
        } else if ($newStatus === 'waiting_ppk') {
            $promptWa = "\n*Untuk menyetujui pengajuan ini (PPK), balas:*\nSETUJU {$code}-{$id}";
            $promptTg = "\n<b>Untuk menyetujui pengajuan ini (PPK), balas:</b>\nSETUJU {$code}-{$id}";
        } else if ($newStatus === 'waiting_bod') {
            $promptWa = "\n*Untuk menyetujui pengajuan ini (BOD), balas:*\nSETUJU {$code}-{$id}";
            $promptTg = "\n<b>Untuk menyetujui pengajuan ini (BOD), balas:</b>\nSETUJU {$code}-{$id}";
        } else if ($newStatus === 'approved') {
            $promptWa = "\nuntuk memproses pengecekan kesiapan dan mengubah status menjadi \"READY FOR USER\", silakan proses melalui Web: https://silatas.biotrop.org/";
            $promptTg = "\nuntuk memproses pengecekan kesiapan dan mengubah status menjadi \"READY FOR USER\", silakan proses melalui Web: https://silatas.biotrop.org/";
        } else {
            $promptWa = "\n*Untuk memproses pengajuan ini, balas:*\nSETUJU {$code}-{$id}";
            $promptTg = "\n<b>Untuk memproses pengajuan ini, balas:</b>\nSETUJU {$code}-{$id}";
        }

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
            
            $waMsg .= "\n*PILIHAN SUPIR:*\n0. Tanpa Supir\n";
            $tgMsg .= "\n<b>PILIHAN SUPIR:</b>\n0. Tanpa Supir\n";
            $resD = $conn->query("SELECT id, full_name FROM employees WHERE position LIKE '%driver%' OR position LIKE '%pengemudi%' ORDER BY full_name ASC");
            if ($resD) {
                $dCount = 1;
                while($row = $resD->fetch_assoc()) {
                    $waMsg .= $dCount . ". " . $row['full_name'] . "\n";
                    $tgMsg .= $dCount . ". " . $row['full_name'] . "\n";
                    $dCount++;
                }
            }
            $waMsg .= $promptWa . " A1\n_(Ganti A & 1 sesuai pilihan)_";
            $tgMsg .= $promptTg . " A1\n<i>(Ganti A & 1 sesuai pilihan)</i>";
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
            $waMsg .= $promptWa . " A\n_(Ganti A sesuai pilihan)_";
            $tgMsg .= $promptTg . " A\n<i>(Ganti A sesuai pilihan)</i>";
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
            $waMsg .= $promptWa . " A\n_(Ganti A sesuai pilihan)_";
            $tgMsg .= $promptTg . " A\n<i>(Ganti A sesuai pilihan)</i>";
        } else {
            $waMsg .= "\n\n---" . $promptWa;
            $tgMsg .= "\n\n---" . $promptTg;
        }
    }
    
    $targetNumbers = [];
    $targetTelegramIds = [];
    $picMap = [
        'Vehicle' => ['198605082025211053'], // Alfi
        'Item'    => ['198902222025211044'], // Indra
        'Item2'   => ['198902222025211044'], // Indra
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
        'Item'    => '📦',
        'Item2'   => '📦'
    ][$type] ?? '🔔';
    
    $typeLabel = [
        'Vehicle' => 'KENDARAAN DINAS',
        'Room'    => 'RUANGAN',
        'Zoom'    => 'ZOOM MEETING',
        'Repair'  => 'PERBAIKAN',
        'Item'    => 'PEMINJAMAN BARANG',
        'Item2'   => 'PERMINTAAN BARANG'
    ][$type] ?? strtoupper($type);

    // Fetch details for better formatting
    $detailTxt = "";
    $table = [
        'Vehicle' => 'vehicle_requests',
        'Room'    => 'room_requests',
        'Dormitory'=> 'dormitory_requests',
        'Zoom'    => 'zoom_requests',
        'Repair'  => 'repair_requests',
        'Item'    => 'item_loan_requests',
        'Item2'   => 'item_requests'
    ][$type] ?? '';

    if ($table) {
        $res = $conn->query("SELECT * FROM `$table` WHERE id = $id");
        if ($res && $row = $res->fetch_assoc()) {
            $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            $formatWaktu = function($dStart, $tStart, $dEnd, $tEnd) use ($days) {
                if (empty($dStart)) return '-';
                $tsStart = strtotime($dStart);
                $tsEnd = empty($dEnd) ? false : strtotime($dEnd);
                $hariStart = $tsStart ? $days[date('w', $tsStart)] . ', ' . date('d/m/Y', $tsStart) : '-';
                $hariEnd = $tsEnd ? $days[date('w', $tsEnd)] . ', ' . date('d/m/Y', $tsEnd) : '-';
                $jamStart = empty($tStart) ? '' : substr($tStart, 0, 5);
                $jamEnd = empty($tEnd) ? '' : substr($tEnd, 0, 5);
                
                if (empty($dEnd) || $dStart === $dEnd) {
                    if ($jamStart === '') return $hariStart;
                    if ($jamEnd === '') return "$hariStart ($jamStart)";
                    return "$hariStart ($jamStart - $jamEnd)";
                } else {
                    if ($jamStart === '') return "$hariStart s/d $hariEnd";
                    return "$hariStart $jamStart s/d $hariEnd $jamEnd";
                }
            };

            if ($type === 'Vehicle') {
                $vName = $row['vehicle_id'] ?? '-';
                if (!empty($row['vehicle_id'])) {
                    if ($row['vehicle_id'] === 'TANPA_KENDARAAN') {
                        $vName = 'Tanpa Kendaraan (Hanya Jasa Driver)';
                    } else {
                        $stmtV = $conn->prepare("SELECT name FROM master_vehicles WHERE id = ?");
                        $stmtV->bind_param("s", $row['vehicle_id']);
                        $stmtV->execute();
                        if ($resV = $stmtV->get_result()->fetch_assoc()) $vName = $resV['name'];
                        $stmtV->close();
                    }
                }
                $dName = $row['driver_name'] ?: '-';

                $detailTxt .= "<b>Nama Penumpang:</b> " . htmlspecialchars($row['passenger_name'] ?? '-') . "\n";
                $detailTxt .= "<b>Keberangkatan:</b> " . htmlspecialchars($row['departure'] ?? '-') . "\n";
                $detailTxt .= "<b>Tujuan:</b> " . htmlspecialchars($row['destination'] ?? '-') . "\n";
                $detailTxt .= "<b>Waktu:</b> " . $formatWaktu($row['date_start'] ?? '', $row['time_start'] ?? '', $row['date_end'] ?? '', $row['time_end'] ?? '') . "\n";
                $detailTxt .= "<b>Biaya Ditanggung:</b> " . htmlspecialchars($row['cost_bearer'] ?? '-') . "\n";
                if (!empty($row['vehicle_id'])) $detailTxt .= "<b>Kendaraan:</b> " . htmlspecialchars($vName) . "\n";
                if (!empty($row['driver_name'])) $detailTxt .= "<b>Supir:</b> " . htmlspecialchars($dName) . "\n";
            } elseif ($type === 'Room') {
                $rName = $row['room_id'] ?? '-';
                if (!empty($row['room_id'])) {
                    $stmtR = $conn->prepare("SELECT name FROM master_rooms WHERE id = ?");
                    $stmtR->bind_param("s", $row['room_id']);
                    $stmtR->execute();
                    if ($resR = $stmtR->get_result()->fetch_assoc()) $rName = $resR['name'];
                    $stmtR->close();
                }
                
                $detailTxt .= "<b>Ruangan:</b> " . htmlspecialchars($rName) . "\n";
                $detailTxt .= "<b>Nama Kegiatan:</b> " . htmlspecialchars($row['purpose'] ?? '-') . "\n";
                $detailTxt .= "<b>Waktu:</b> " . $formatWaktu($row['date_start'] ?? '', $row['time_start'] ?? '', $row['date_end'] ?? '', $row['time_end'] ?? '') . "\n";
                if (!empty($row['participants'])) $detailTxt .= "<b>Peserta:</b> " . htmlspecialchars($row['participants']) . " org\n";
                if (!empty($row['special_needs'])) $detailTxt .= "<b>Kebut. Khusus:</b> " . htmlspecialchars($row['special_needs']) . "\n";
            } elseif ($type === 'Dormitory') {
                $rName = $row['dormitory_id'] ?? '-';
                if (!empty($row['dormitory_id'])) {
                    $stmtR = $conn->prepare("SELECT name FROM master_dormitories WHERE id = ?");
                    $stmtR->bind_param("s", $row['dormitory_id']);
                    $stmtR->execute();
                    if ($resR = $stmtR->get_result()->fetch_assoc()) $rName = $resR['name'];
                    $stmtR->close();
                }
                $detailTxt .= "<b>Dormitory:</b> " . htmlspecialchars($rName) . "\n";
                $detailTxt .= "<b>Penghuni:</b> " . htmlspecialchars($row['occupant_name'] ?? '-') . "\n";
                $detailTxt .= "<b>Keperluan:</b> " . htmlspecialchars($row['purpose'] ?? '-') . "\n";
                $detailTxt .= "<b>Waktu:</b> " . $formatWaktu($row['date_start'] ?? '', $row['time_start'] ?? '', $row['date_end'] ?? '', $row['time_end'] ?? '') . "\n";
                if (!empty($row['participants'])) $detailTxt .= "<b>Peserta:</b> " . htmlspecialchars($row['participants']) . " org\n";
                if (!empty($row['special_needs'])) $detailTxt .= "<b>Kebut. Khusus:</b> " . htmlspecialchars($row['special_needs']) . "\n";
            } elseif ($type === 'Zoom') {
                $zName = $row['zoom_account_id'] ?? '-';
                if (!empty($row['zoom_account_id'])) {
                    $zoomMap = [
                        'zoom_01' => 'Zoom 1 (Kap. 300)',
                        'zoom_02' => 'Zoom 2 (Kap. 300)'
                    ];
                    $zName = $zoomMap[$row['zoom_account_id']] ?? $row['zoom_account_id'];
                }
                if (!empty($row['zoom_account_id'])) $detailTxt .= "<b>Akun Zoom:</b> " . htmlspecialchars($zName) . "\n";
                $detailTxt .= "<b>Nama Kegiatan:</b> " . htmlspecialchars($row['purpose'] ?? '-') . "\n";
                $detailTxt .= "<b>Waktu:</b> " . $formatWaktu($row['date_start'] ?? '', $row['time_start'] ?? '', $row['date_end'] ?? '', $row['time_end'] ?? '') . "\n";
                if (!empty($row['participants'])) $detailTxt .= "<b>Peserta:</b> " . htmlspecialchars($row['participants']) . " org\n";
                if (!empty($row['request_type'])) $detailTxt .= "<b>Permintaan Tambahan:</b> " . htmlspecialchars($row['request_type']) . "\n";
                if (!empty($row['special_needs'])) $detailTxt .= "<b>Kebut. Khusus:</b> " . htmlspecialchars($row['special_needs']) . "\n";
            } elseif ($type === 'Repair') {
                $detailTxt .= "<b>Lokasi:</b> " . htmlspecialchars($row['location_detail'] ?? '-') . "\n";
                $detailTxt .= "<b>Waktu Kejadian:</b> " . $formatWaktu($row['incident_date'] ?? '', $row['incident_time'] ?? '', '', '') . "\n";
                $detailTxt .= "<b>Masalah:</b> " . htmlspecialchars($row['issue_description'] ?? '-') . "\n";
                $detailTxt .= "<b>Prioritas:</b> " . strtoupper($row['priority'] ?? 'MEDIUM') . "\n";
            } elseif ($type === 'Item') {
                $detailTxt .= "<b>Barang:</b> " . htmlspecialchars($row['item_name'] ?? '-') . "\n";
                if (!empty($row['item_quantity'])) $detailTxt .= "<b>Jumlah:</b> " . htmlspecialchars($row['item_quantity']) . "\n";
                $detailTxt .= "<b>Keperluan:</b> " . htmlspecialchars($row['purpose'] ?? '-') . "\n";
                $detailTxt .= "<b>Waktu Pinjam:</b> " . $formatWaktu($row['loan_date'] ?? '', $row['loan_time'] ?? '', $row['return_date'] ?? '', $row['return_time'] ?? '') . "\n";
            } elseif ($type === 'Item2') {
                $items = json_decode($row['items_json'] ?? '[]', true);
                if (is_array($items) && count($items) > 0) {
                    $detailTxt .= "<b>Daftar Barang:</b>\n";
                    foreach ($items as $idx => $itm) {
                        $name = trim($itm['name'] ?? 'Unknown');
                        $qty = $itm['quantity'] ?? 1;
                        $detailTxt .= ($idx + 1) . ". " . htmlspecialchars($name) . " (" . $qty . "x)\n";
                    }
                }
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
    $stmt = $conn->prepare("SELECT * FROM `$table` WHERE id = ?");
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

        $typeToLabel = [
            'vehicle_requests' => 'KENDARAAN',
            'room_requests'    => 'RUANGAN',
            'dormitory_requests' => 'DORMITORY',
            'zoom_requests'    => 'ZOOM',
            'repair_requests'  => 'PERBAIKAN',
            'item_loan_requests' => 'PEMINJAMAN BARANG',
            'item_requests' => 'PERMINTAAN BARANG'
        ];
        $typeLabelUser = $typeToLabel[$table] ?? 'UMUM';

        $msg = "<b>📢 UPDATE PENGAJUAN (" . $typeLabelUser . ")</b>\n\n";
        $msg .= "Halo " . htmlspecialchars($request['applicant_name']) . ",\n";
        $msg .= "Status pengajuan Anda <b>#$id</b> telah diperbarui.\n\n";
        $msg .= "<b>Status Baru:</b> $statusLabel\n";
        if ($noteInput) {
            $msg .= "<b>Catatan Admin:</b> " . htmlspecialchars($noteInput) . "\n";
        }
        $msg .= "<b>Diproses Oleh:</b> " . htmlspecialchars($actorName) . "\n\n";
        
        if ($newStatus === 'ready_for_user') {
            $typeCodes = [
                'vehicle_requests' => 'VEH',
                'room_requests'    => 'ROM',
                'dormitory_requests'=> 'DRM',
                'zoom_requests'    => 'ZOM',
                'repair_requests'  => 'REP',
                'item_loan_requests' => 'ITM',
                'item_requests' => 'ITM'
            ];
            $code = $typeCodes[$table] ?? 'REQ';
            
            $msg .= "<b>Permintaan Anda sudah siap digunakan.</b>\n\n";
            $msg .= "<i>Untuk konfirmasi pengajuan selesai, balas pesan ini dengan format:</i>\n";
            $msg .= "<b>SELESAI $code-$id</b>\n\n";
        }
        
        $msg .= "<i>Silakan cek Dashboard SILATAS untuk detail:</i>\nhttps://silatas.biotrop.org/";

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
            'item_loan_requests' => 'Item',
            'item_requests' => 'Item2'
        ];
        $type = $tableToType[$table] ?? 'Unknown';

        // Build detail text for Approver (Manager FMD dll)
        $detailTxt = "";
        $row = $request;
        if ($type === 'Vehicle') {
            $vName = $row['vehicle_id'] ?? '-';
            if (!empty($row['vehicle_id'])) {
                if ($row['vehicle_id'] === 'TANPA_KENDARAAN') {
                    $vName = 'Tanpa Kendaraan (Hanya Jasa Driver)';
                } else {
                    $stmtV = $conn->prepare("SELECT name FROM master_vehicles WHERE id = ?");
                    $stmtV->bind_param("s", $row['vehicle_id']);
                    $stmtV->execute();
                    if ($resV = $stmtV->get_result()->fetch_assoc()) $vName = $resV['name'];
                    $stmtV->close();
                }
            }
            $dName = $row['driver_name'] ?: '-';

            $detailTxt .= "<b>Nama Penumpang:</b> " . htmlspecialchars($row['passenger_name'] ?? '-') . "\n";
            $detailTxt .= "<b>Keberangkatan:</b> " . htmlspecialchars($row['departure'] ?? '-') . "\n";
            $detailTxt .= "<b>Tujuan:</b> " . htmlspecialchars($row['destination'] ?? '-') . "\n";
            $detailTxt .= "<b>Waktu:</b> " . ($row['date_start'] ?? '-') . " s/d " . ($row['date_end'] ?? '-') . " (Jam: " . substr($row['time_start'] ?? '', 0, 5) . ")\n";
            $detailTxt .= "<b>Biaya Ditanggung:</b> " . htmlspecialchars($row['cost_bearer'] ?? '-') . "\n";
            if (!empty($row['vehicle_id'])) $detailTxt .= "<b>Kendaraan:</b> " . htmlspecialchars($vName) . "\n";
            if (!empty($row['driver_name'])) $detailTxt .= "<b>Supir:</b> " . htmlspecialchars($dName) . "\n";
        } elseif ($type === 'Room') {
            $rName = $row['room_id'] ?? '-';
            if (!empty($row['room_id'])) {
                $stmtR = $conn->prepare("SELECT name FROM master_rooms WHERE id = ?");
                $stmtR->bind_param("s", $row['room_id']);
                $stmtR->execute();
                if ($resR = $stmtR->get_result()->fetch_assoc()) $rName = $resR['name'];
                $stmtR->close();
            }
            
            $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            $tsStart = strtotime($row['date_start'] ?? '');
            $tsEnd = strtotime($row['date_end'] ?? '');
            $hariStart = $tsStart ? $days[date('w', $tsStart)] . ', ' . date('d/m/Y', $tsStart) : '-';
            $hariEnd = $tsEnd ? $days[date('w', $tsEnd)] . ', ' . date('d/m/Y', $tsEnd) : '-';
            $jamStart = substr($row['time_start'] ?? '', 0, 5);
            $jamEnd = substr($row['time_end'] ?? '', 0, 5);
            $waktu = ($hariStart === $hariEnd) ? "$hariStart ($jamStart - $jamEnd)" : "$hariStart $jamStart s/d $hariEnd $jamEnd";

            $detailTxt .= "<b>Ruangan:</b> " . htmlspecialchars($rName) . "\n";
            $detailTxt .= "<b>Nama Kegiatan:</b> " . htmlspecialchars($row['purpose'] ?? '-') . "\n";
            $detailTxt .= "<b>Waktu:</b> " . $waktu . "\n";
            if (!empty($row['participants'])) $detailTxt .= "<b>Peserta:</b> " . htmlspecialchars($row['participants']) . " org\n";
            if (!empty($row['special_needs'])) $detailTxt .= "<b>Kebut. Khusus:</b> " . htmlspecialchars($row['special_needs']) . "\n";
        } elseif ($type === 'Dormitory') {
            $rName = $row['dormitory_id'] ?? '-';
            if (!empty($row['dormitory_id'])) {
                $stmtR = $conn->prepare("SELECT name FROM master_dormitories WHERE id = ?");
                $stmtR->bind_param("s", $row['dormitory_id']);
                $stmtR->execute();
                if ($resR = $stmtR->get_result()->fetch_assoc()) $rName = $resR['name'];
                $stmtR->close();
            }
            $detailTxt .= "<b>Dormitory:</b> " . htmlspecialchars($rName) . "\n";
            $detailTxt .= "<b>Penghuni:</b> " . htmlspecialchars($row['occupant_name'] ?? '-') . "\n";
            $detailTxt .= "<b>Waktu:</b> " . ($row['date_start'] ?? '') . " " . substr($row['time_start'] ?? '', 0, 5) . " s/d " . ($row['date_end'] ?? '') . " " . substr($row['time_end'] ?? '', 0, 5) . "\n";
        } elseif ($type === 'Zoom') {
            $zName = $row['zoom_account_id'] ?? '-';
            if (!empty($row['zoom_account_id'])) {
                $zoomMap = [
                    'zoom_01' => 'Zoom 1 (Kap. 300)',
                    'zoom_02' => 'Zoom 2 (Kap. 300)'
                ];
                $zName = $zoomMap[$row['zoom_account_id']] ?? $row['zoom_account_id'];
            }
            if (!empty($row['zoom_account_id'])) $detailTxt .= "<b>Akun Zoom:</b> " . htmlspecialchars($zName) . "\n";
            $detailTxt .= "<b>Permintaan Tambahan:</b> " . htmlspecialchars($row['request_type'] ?? '-') . "\n";
            $detailTxt .= "<b>Kebutuhan Khusus:</b> " . htmlspecialchars($row['special_needs'] ?? '-') . "\n";
            $detailTxt .= "<b>Waktu:</b> " . ($row['date_start'] ?? '') . " " . substr($row['time_start'] ?? '', 0, 5) . " s/d " . ($row['date_end'] ?? '') . " " . substr($row['time_end'] ?? '', 0, 5) . "\n";
        } elseif ($type === 'Repair') {
            $detailTxt .= "<b>Lokasi:</b> " . htmlspecialchars($row['location_detail'] ?? '') . "\n";
            $detailTxt .= "<b>Prioritas:</b> " . strtoupper($row['priority'] ?? 'MEDIUM') . "\n";
        } elseif ($type === 'Item') {
            $detailTxt .= "<b>Barang:</b> " . htmlspecialchars($row['item_name'] ?? '') . "\n";
            $detailTxt .= "<b>Waktu:</b> " . ($row['loan_date'] ?? '') . " " . substr($row['loan_time'] ?? '', 0, 5) . " s/d " . ($row['return_date'] ?? '') . " " . substr($row['return_time'] ?? '', 0, 5) . "\n";
        } elseif ($type === 'Item2') {
            $items = json_decode($row['items_json'] ?? '[]', true);
            if (is_array($items) && count($items) > 0) {
                $detailTxt .= "<b>Daftar Barang:</b>\n";
                foreach ($items as $idx => $itm) {
                    $name = trim($itm['name'] ?? 'Unknown');
                    $qty = $itm['quantity'] ?? 1;
                    $detailTxt .= ($idx + 1) . ". " . htmlspecialchars($name) . " (" . $qty . "x)\n";
                }
            }
        }
        
        $msgApprover = "<b>📢 UPDATE PENGAJUAN (" . strtoupper($type) . ")</b>\n\n";
        $msgApprover .= "<b>ID:</b> #$id\n";
        $msgApprover .= "<b>Pemohon:</b> " . htmlspecialchars($row['applicant_name'] ?? '-') . "\n";
        $msgApprover .= "<b>Unit:</b> " . htmlspecialchars($row['applicant_unit'] ?? '-') . "\n";
        
        $purpose = $row['purpose'] ?? $row['issue_description'] ?? '-';
        if ($type === 'Repair') {
            $msgApprover .= "<b>Masalah:</b> " . htmlspecialchars($purpose) . "\n";
        } else {
            $msgApprover .= "<b>Keperluan:</b> " . htmlspecialchars($purpose) . "\n";
        }
        
        if ($detailTxt) {
            $msgApprover .= $detailTxt;
        }

        $msgApprover .= "\n<b>Status Baru:</b> $statusLabel oleh " . htmlspecialchars($actorName) . ".\n";
        
        if ($newStatus === 'approved') {
            $msgApprover .= "<b>Mohon cek Dashboard Admin:</b>";
        } else {
            $msgApprover .= "<i>Mohon cek Dashboard Admin untuk review/tindakan.</i>";
        }
        
        notifyApprovers($conn, $newStatus, $type, $id, $msgApprover);
    }
}
