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
// Mencegah Double Submit (Debounce 3 detik) untuk semua submit
// ============================================================
if (strpos($action, 'submit_') === 0) {
    $lastSubmit = $_SESSION['last_submit_time'] ?? 0;
    $currentTime = time();
    if ($currentTime - $lastSubmit < 3) {
        jsonResponse(false, 'Harap tunggu beberapa saat sebelum mengirim pengajuan kembali (Mencegah Double Submit).');
    }
    $_SESSION['last_submit_time'] = $currentTime;
}

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
    'Item2'   => 'item_requests',
];

require_once __DIR__ . '/notifications.php';

switch ($action) {

    // ============================================================
    // 0. SEARCH INVENTORY ITEMS (Untuk form repair/gudang)
    // ============================================================
    case 'search_inventory_items':
        $q = $_GET['q'] ?? '';
        $like = "%$q%";
        $stmt = $conn->prepare("
            SELECT i.id, i.item_code, i.name, i.stock, i.unit,
                   COALESCE((SELECT unit_price FROM inv_transactions WHERE item_id = i.id AND type='in' ORDER BY id DESC LIMIT 1), 0) as last_price
            FROM inv_items i 
            WHERE i.item_code LIKE ? OR i.name LIKE ?
            LIMIT 10
        ");
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    // ============================================================
    // 1. GET ALL REQUESTS (Admin melihat semua)
    // ============================================================
    case 'get_vehicle':
        $res = $conn->query("SELECT id, user_id, vehicle_id, applicant_name, applicant_unit, destination, passenger_name, departure, cost_bearer, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, status, note, driver_name, created_at FROM vehicle_requests ORDER BY created_at DESC LIMIT 100");
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

    case 'get_item2':
        $res = $conn->query("SELECT id, user_id, applicant_name, applicant_unit, purpose, items_json, status, note, created_at FROM item_requests ORDER BY created_at DESC LIMIT 100");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    // ============================================================
    // 1b. GET REQUESTS BY USER
    // ============================================================
    case 'get_vehicle_by_user':
        $stmt = $conn->prepare("SELECT id, user_id, vehicle_id, applicant_name, applicant_unit, destination, passenger_name, departure, cost_bearer, DATE_FORMAT(date_start,'%Y-%m-%d') as date_start, time_start, DATE_FORMAT(date_end,'%Y-%m-%d') as date_end, time_end, purpose, status, note, driver_name, created_at FROM vehicle_requests WHERE user_id = ? ORDER BY created_at DESC");
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

    case 'get_item2_by_user':
        $stmt = $conn->prepare("SELECT id, user_id, applicant_name, applicant_unit, purpose, items_json, status, note, created_at FROM item_requests WHERE user_id = ? ORDER BY created_at DESC");
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
        $vehicle_id     = $_POST['vehicle_id']      ?? 'PENDING_ASSIGNMENT';
        $applicant_name = $_POST['applicant_name']  ?? '';
        $applicant_unit = $_POST['applicant_unit']  ?? '';
        $date_start     = !empty($_POST['date_start']) ? $_POST['date_start'] : null;
        $time_start     = !empty($_POST['time_start']) ? $_POST['time_start'] : null;
        $date_end       = !empty($_POST['date_end']) ? $_POST['date_end'] : null;
        $time_end       = !empty($_POST['time_end']) ? $_POST['time_end'] : null;
        $destination    = $_POST['destination']     ?? '';
        $passenger_name = $_POST['passenger_name']  ?? '';
        $departure      = $_POST['departure']       ?? '';
        $cost_bearer    = $_POST['cost_bearer']     ?? '';
        $purpose        = $_POST['purpose']         ?? '';

        $stmt = $conn->prepare("INSERT INTO vehicle_requests (user_id, vehicle_id, applicant_name, applicant_unit, destination, passenger_name, departure, cost_bearer, date_start, time_start, date_end, time_end, purpose, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,'pending')");
        $stmt->bind_param("issssssssssss", $userId, $vehicle_id, $applicant_name, $applicant_unit, $destination, $passenger_name, $departure, $cost_bearer, $date_start, $time_start, $date_end, $time_end, $purpose);
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

        if ($room_id && $date_start && $time_start && $date_end && $time_end) {
            $startDT = $date_start . ' ' . substr($time_start, 0, 5);
            $endDT   = $date_end . ' ' . substr($time_end, 0, 5);
            
            $stmt = $conn->prepare("SELECT id FROM room_requests WHERE room_id = ? AND status IN ('approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','pending') AND CONCAT(DATE_FORMAT(date_start, '%Y-%m-%d'), ' ', SUBSTRING(time_start, 1, 5)) < ? AND CONCAT(DATE_FORMAT(date_end, '%Y-%m-%d'), ' ', SUBSTRING(time_end, 1, 5)) > ? LIMIT 1");
            $stmt->bind_param("sss", $room_id, $endDT, $startDT);
            $stmt->execute();
            $resConf = $stmt->get_result();
            if ($resConf->num_rows > 0) {
                jsonResponse(false, 'Ruangan sudah dipesan pada jam tersebut.');
            }
            $stmt->close();
        }

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

        if ($zoom_account_id && $date_start && $time_start && $date_end && $time_end) {
            $startDT = $date_start . ' ' . substr($time_start, 0, 5);
            $endDT   = $date_end . ' ' . substr($time_end, 0, 5);
            
            $stmt = $conn->prepare("SELECT id FROM zoom_requests WHERE zoom_account_id = ? AND status IN ('approved','ready_for_user','in-progress','verified','waiting_manager_fad','waiting_ppk','waiting_bod','approved_waiting_fund','pending') AND CONCAT(DATE_FORMAT(date_start, '%Y-%m-%d'), ' ', SUBSTRING(time_start, 1, 5)) < ? AND CONCAT(DATE_FORMAT(date_end, '%Y-%m-%d'), ' ', SUBSTRING(time_end, 1, 5)) > ? LIMIT 1");
            $stmt->bind_param("sss", $zoom_account_id, $endDT, $startDT);
            $stmt->execute();
            $resConf = $stmt->get_result();
            if ($resConf->num_rows > 0) {
                jsonResponse(false, 'Akun Zoom sudah dipesan pada jam tersebut.');
            }
            $stmt->close();
        }

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

    case 'submit_item2':
        $applicant_name = $_POST['applicant_name'] ?? '';
        $applicant_unit = $_POST['applicant_unit'] ?? '';
        $purpose        = $_POST['purpose']        ?? '';
        $items_json     = $_POST['items_json']     ?? '[]';

        $stmt = $conn->prepare("INSERT INTO item_requests (user_id, applicant_name, applicant_unit, purpose, items_json, status) VALUES (?,?,?,?,?,'pending')");
        $stmt->bind_param("issss", $userId, $applicant_name, $applicant_unit, $purpose, $items_json);
        if ($stmt->execute()) {
            $newId = $conn->insert_id;
            
            // Loop through JSON and insert into item_request_details
            $items = json_decode($items_json, true);
            if (is_array($items)) {
                $stmtDet = $conn->prepare("INSERT INTO item_request_details (request_id, item_id, item_name, quantity) VALUES (?,?,?,?)");
                foreach ($items as $itm) {
                    $iId = $itm['id'] ?? 0;
                    $iName = $itm['name'] ?? '';
                    $iQty = $itm['quantity'] ?? 1;
                    $stmtDet->bind_param("iisi", $newId, $iId, $iName, $iQty);
                    $stmtDet->execute();
                }
                $stmtDet->close();
            }

            notifyNewRequest('Item2', $newId, $applicant_name, $applicant_unit, $purpose);
            jsonResponse(true, 'Permintaan barang berhasil dikirim!', ['id' => $newId]);
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
                    'Item2'   => 'Permintaan Barang',
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

        // Otomatisasi teks ketika PIC membatalkan pengajuan (canceled)
        if ($newStatus === 'canceled' && empty($noteInput)) {
            $noteInput = "Pengajuan dibatalkan/ditolak (Canceled/Declined) oleh pihak pengelola (PIC/Admin).";
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
            // ============================================================
            // AUTOMATIC INVENTORY DEDUCTION FOR REPAIR
            // ============================================================
            if ($type === 'Repair' && isset($_POST['gudang_items'])) {
                $gudangItemsStr = $_POST['gudang_items'];
                $gItems = json_decode($gudangItemsStr, true);
                if (is_array($gItems) && count($gItems) > 0) {
                    $docNum = "REP-" . $id . "-" . time();
                    $updStmt = $conn->prepare("UPDATE inv_items SET stock = stock - ? WHERE id = ?");
                    $transStmt = $conn->prepare("INSERT INTO inv_transactions (item_id, type, transaction_subtype, doc_number, doc_date, book_date, reference_doc, notes, quantity, unit_price, total_price, user_id) VALUES (?, 'out', 'Perbaikan Internal', ?, CURDATE(), CURDATE(), ?, ?, ?, 0, 0, ?)");
                    
                    foreach ($gItems as $gi) {
                        if (!empty($gi['itemId'])) {
                            $itemId = (int)$gi['itemId'];
                            $qty = (int)$gi['quantity'];
                            if ($itemId > 0 && $qty > 0) {
                                // Deduct stock
                                $updStmt->bind_param("ii", $qty, $itemId);
                                $updStmt->execute();
                                
                                // Insert transaction
                                $refDoc = "REP-" . $id;
                                $tNote = "Otomatis: Penggunaan barang untuk laporan kerusakan (Request ID: $id)";
                                $transStmt->bind_param("isssii", $itemId, $docNum, $refDoc, $tNote, $qty, $userId);
                                $transStmt->execute();
                            }
                        }
                    }
                    if ($updStmt) $updStmt->close();
                    if ($transStmt) $transStmt->close();
                }
            }
            
            // KIRIM NOTIFIKASI KE TELEGRAM USER
            notifyStatusUpdate($conn, $table, $id, $newStatus, $noteInput, $actorName);
            
            // JIKA VEHICLE DAN APPROVED, KIRIM NOTIF KE DRIVER
            if ($type === 'Vehicle' && $newStatus === 'approved') {
                $stmtDrv = $conn->prepare("SELECT v.vehicle_id, v.driver_name, v.applicant_name, v.passenger_name, DATE_FORMAT(v.date_start,'%d %b %Y') as ds, DATE_FORMAT(v.date_end,'%d %b %Y') as de, v.time_start, v.time_end, v.purpose, v.destination, u.whatsapp_number, u.telegram_chat_id FROM vehicle_requests v LEFT JOIN employees e ON v.driver_name = e.full_name LEFT JOIN users u ON e.id = u.employee_id WHERE v.id = ?");
                $stmtDrv->bind_param("i", $id);
                $stmtDrv->execute();
                $reqRow = $stmtDrv->get_result()->fetch_assoc();
                $stmtDrv->close();

                if ($reqRow && $reqRow['driver_name'] && trim($reqRow['driver_name']) !== '') {
                    $vName = $reqRow['vehicle_id'];
                    if ($vName === 'TANPA_KENDARAAN') {
                        $vName = 'Tanpa Kendaraan (Hanya Jasa Driver)';
                    } else {
                        $stmtV = $conn->prepare("SELECT name FROM master_vehicles WHERE id = ?");
                        $stmtV->bind_param("s", $reqRow['vehicle_id']);
                        $stmtV->execute();
                        if ($resV = $stmtV->get_result()->fetch_assoc()) $vName = $resV['name'];
                        $stmtV->close();
                    }

                    $drvName  = $reqRow['driver_name'];
                    $appName  = $reqRow['applicant_name'];
                    $passName = $reqRow['passenger_name'] ?: '-';
                    $dest     = $reqRow['destination'] ?: '-';
                    
                    $waktuTanggal = $reqRow['ds'];
                    if (!empty($reqRow['de']) && $reqRow['de'] !== $reqRow['ds']) {
                        $waktuTanggal .= " - " . $reqRow['de'];
                    }
                    $waktuJam = substr($reqRow['time_start'] ?? '00:00:00', 0, 5);
                    $purp     = $reqRow['purpose'] ?: '-';

                    if (!empty($reqRow['whatsapp_number']) && function_exists('sendWhatsAppFonnte')) {
                        $msgDriver = "🚗 *TUGAS BARU (DRIVER)*\n\nHalo *$drvName*,\nAnda telah ditugaskan sebagai pengemudi untuk pengajuan kendaraan *VEH-$id*.\n\n*Pemohon:* $appName\n*Penumpang:* $passName\n*Kendaraan:* $vName\n*Lokasi Tujuan:* $dest\n*Tanggal:* $waktuTanggal\n*Jam Berangkat:* $waktuJam\n*Keperluan:* $purp\n\nMohon cek Dashboard Anda untuk detail lengkap.";
                        sendWhatsAppFonnte($msgDriver, $reqRow['whatsapp_number']);
                    }
                    if (!empty($reqRow['telegram_chat_id']) && function_exists('sendTelegramPHP')) {
                        $msgDriverTg = "🚗 <b>TUGAS BARU (DRIVER)</b>\n\nHalo <b>$drvName</b>,\nAnda telah ditugaskan sebagai pengemudi untuk pengajuan kendaraan <b>VEH-$id</b>.\n\n<b>Pemohon:</b> $appName\n<b>Penumpang:</b> $passName\n<b>Kendaraan:</b> $vName\n<b>Lokasi Tujuan:</b> $dest\n<b>Tanggal:</b> $waktuTanggal\n<b>Jam Berangkat:</b> $waktuJam\n<b>Keperluan:</b> $purp\n\nMohon cek Dashboard Anda untuk detail lengkap.";
                        sendTelegramPHP($msgDriverTg, $reqRow['telegram_chat_id']);
                    }
                }
            }

            // JIKA ITEM2 (Permintaan Barang) DAN COMPLETED, OTOMATIS POTONG STOK GUDANG
            if ($type === 'Item2' && $newStatus === 'completed') {
                $resItem = $conn->query("SELECT items_json, applicant_name, applicant_unit FROM item_requests WHERE id = $id");
                if ($resItem && $rowItem = $resItem->fetch_assoc()) {
                    $items = json_decode($rowItem['items_json'], true);
                    if (is_array($items)) {
                        $docDate = date('Y-m-d');
                        $bookDate = date('Y-m-d H:i:s');
                        $notes = "Otomatis dari Permintaan Barang #" . $id . " (" . $rowItem['applicant_name'] . " - " . $rowItem['applicant_unit'] . ")";
                        $refDoc = "REQ-ITM-" . str_pad($id, 4, '0', STR_PAD_LEFT);
                        
                        $stmtInsert = $conn->prepare("INSERT INTO inv_transactions (item_id, type, transaction_subtype, doc_number, doc_date, book_date, reference_doc, notes, quantity, unit_price, total_price, user_id) VALUES (?, 'out', 'Pemakaian', ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                        $stmtUpdate = $conn->prepare("UPDATE inv_items SET stock = stock - ? WHERE id = ?");
                        
                        // Group items by location_id (UAKPB)
                        $groupedItems = [];
                        foreach ($items as $itm) {
                            $itemId = (int)($itm['id'] ?? 0);
                            $qty = (int)($itm['quantity'] ?? 0);
                            if ($itemId > 0 && $qty > 0) {
                                $locId = 0;
                                $resLoc = $conn->query("SELECT location_id FROM inv_items WHERE id = $itemId");
                                if ($resLoc && $rowLoc = $resLoc->fetch_assoc()) {
                                    $locId = (int)$rowLoc['location_id'];
                                }
                                if (!isset($groupedItems[$locId])) $groupedItems[$locId] = [];
                                $groupedItems[$locId][] = ['id' => $itemId, 'qty' => $qty];
                            }
                        }

                        $year = date('Y');
                        $suffix = 'K';

                        foreach ($groupedItems as $locId => $grpItems) {
                            // Generate doc_number for this UAKPB
                            $prefix = 'INV-';
                            if ($locId > 0) {
                                $resLocCode = $conn->query("SELECT code FROM inv_locations WHERE id = $locId");
                                if ($resLocCode && $rowLocCode = $resLocCode->fetch_assoc()) {
                                    $prefix = $rowLocCode['code'];
                                }
                            }
                            
                            $likePattern = $prefix . $year . '%' . $suffix;
                            $stmtDoc = $conn->prepare("SELECT doc_number FROM inv_transactions WHERE doc_number LIKE ? ORDER BY id DESC LIMIT 1");
                            $stmtDoc->bind_param("s", $likePattern);
                            $stmtDoc->execute();
                            $resDocNum = $stmtDoc->get_result()->fetch_assoc();
                            $stmtDoc->close();
                            
                            $nextNum = 1;
                            if ($resDocNum && preg_match('/' . $year . '(\d{5})' . $suffix . '$/', $resDocNum['doc_number'], $matches)) {
                                $nextNum = (int)$matches[1] + 1;
                            }
                            $docNum = $prefix . $year . str_pad($nextNum, 5, '0', STR_PAD_LEFT) . $suffix;

                            foreach ($grpItems as $gItm) {
                                $itemId = $gItm['id'];
                                $qty = $gItm['qty'];

                                // Get last unit_price
                                $unitPrice = 0.0;
                                $resPrice = $conn->query("SELECT unit_price FROM inv_transactions WHERE item_id = $itemId AND type='in' ORDER BY id DESC LIMIT 1");
                                if ($resPrice && $rowPrice = $resPrice->fetch_assoc()) {
                                    $unitPrice = (float)$rowPrice['unit_price'];
                                }
                                $totalPrice = $unitPrice * $qty;

                                // insert transaction
                                $stmtInsert->bind_param("isssssidd", $itemId, $docNum, $docDate, $bookDate, $refDoc, $notes, $qty, $unitPrice, $totalPrice);
                                $stmtInsert->execute();
                                // update stock
                                $stmtUpdate->bind_param("ii", $qty, $itemId);
                                $stmtUpdate->execute();
                            }
                        }
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

        $startDT = $reqRow['ds'] . ' ' . substr($reqRow['time_start'] ?? '00:00:00', 0, 5);
        $endDT   = ($reqRow['de'] ?? $reqRow['ds']) . ' ' . substr($reqRow['time_end'] ?? '23:59:59', 0, 5);

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
        if ($driverName && trim($driverName) !== '' && $driverName !== 'TANPA_SUPIR') {
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
            
            // JIKA STATUS SUDAH APPROVED/IN-PROGRESS, KIRIM NOTIF KE DRIVER KARENA BARU DITETAPKAN
            $stmtStatus = $conn->prepare("SELECT status, applicant_name, passenger_name, DATE_FORMAT(date_start,'%d %b %Y') as ds, DATE_FORMAT(date_end,'%d %b %Y') as de, time_start, time_end, purpose, destination FROM vehicle_requests WHERE id = ?");
            $stmtStatus->bind_param("i", $id);
            $stmtStatus->execute();
            $reqRow = $stmtStatus->get_result()->fetch_assoc();
            $stmtStatus->close();

            if ($reqRow && in_array($reqRow['status'], ['approved', 'in-progress', 'ready_for_user']) && $driverName && $driverName !== 'TANPA_SUPIR') {
                $stmtDrv = $conn->prepare("SELECT u.whatsapp_number, u.telegram_chat_id FROM users u INNER JOIN employees e ON u.employee_id = e.id WHERE e.full_name = ?");
                $stmtDrv->bind_param("s", $driverName);
                $stmtDrv->execute();
                $drvRow = $stmtDrv->get_result()->fetch_assoc();
                $stmtDrv->close();

                if ($drvRow) {
                    $vName = $vehicleId;
                    if ($vName === 'TANPA_KENDARAAN') {
                        $vName = 'Tanpa Kendaraan (Hanya Jasa Driver)';
                    } else {
                        $stmtV = $conn->prepare("SELECT name FROM master_vehicles WHERE id = ?");
                        $stmtV->bind_param("s", $vehicleId);
                        $stmtV->execute();
                        if ($resV = $stmtV->get_result()->fetch_assoc()) $vName = $resV['name'];
                        $stmtV->close();
                    }

                    $appName  = $reqRow['applicant_name'];
                    $passName = $reqRow['passenger_name'] ?: '-';
                    $dest     = $reqRow['destination'] ?: '-';
                    $waktuTanggal = $reqRow['ds'];
                    if (!empty($reqRow['de']) && $reqRow['de'] !== $reqRow['ds']) {
                        $waktuTanggal .= " - " . $reqRow['de'];
                    }
                    $waktuJam = substr($reqRow['time_start'] ?? '00:00:00', 0, 5);
                    $purp     = $reqRow['purpose'] ?: '-';

                    if (!empty($drvRow['whatsapp_number']) && function_exists('sendWhatsAppFonnte')) {
                        $msgDriver = "🚗 *TUGAS BARU (DRIVER)*\n\nHalo *$driverName*,\nAnda telah ditugaskan sebagai pengemudi untuk pengajuan kendaraan *VEH-$id*.\n\n*Pemohon:* $appName\n*Penumpang:* $passName\n*Kendaraan:* $vName\n*Lokasi Tujuan:* $dest\n*Tanggal:* $waktuTanggal\n*Jam Berangkat:* $waktuJam\n*Keperluan:* $purp\n\nMohon cek Dashboard Anda untuk detail lengkap.";
                        sendWhatsAppFonnte($msgDriver, $drvRow['whatsapp_number']);
                    }
                    if (!empty($drvRow['telegram_chat_id']) && function_exists('sendTelegramPHP')) {
                        $msgDriverTg = "🚗 <b>TUGAS BARU (DRIVER)</b>\n\nHalo <b>$driverName</b>,\nAnda telah ditugaskan sebagai pengemudi untuk pengajuan kendaraan <b>VEH-$id</b>.\n\n<b>Pemohon:</b> $appName\n<b>Penumpang:</b> $passName\n<b>Kendaraan:</b> $vName\n<b>Lokasi Tujuan:</b> $dest\n<b>Tanggal:</b> $waktuTanggal\n<b>Jam Berangkat:</b> $waktuJam\n<b>Keperluan:</b> $purp\n\nMohon cek Dashboard Anda untuk detail lengkap.";
                        sendTelegramPHP($msgDriverTg, $drvRow['telegram_chat_id']);
                    }
                }
            }

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
        $jenis     = $_POST['jenis']             ?? '';
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

            // Update status -> 'waiting_manager_fmd'
            $ts       = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('d M Y H:i');
            $jenisStr = $jenis ? "[$jenis] " : "";
            $noteLog  = "\n[$ts] [System] - STATUS UPDATE: {$jenisStr}RAB Diajukan (Rp " . number_format($totalRAB, 0, ',', '.') . ") - Menunggu Approval Manager FMD";
            $upd = $conn->prepare("UPDATE repair_requests SET status = 'waiting_manager_fmd', note = CONCAT(IFNULL(note,''),?) WHERE id = ?");
            $upd->bind_param("si", $noteLog, $requestId);
            $upd->execute();
            $upd->close();

            // KIRIM NOTIFIKASI TELEGRAM
            notifyStatusUpdate($conn, 'repair_requests', $requestId, 'waiting_manager_fmd', "RAB Diajukan (Rp " . number_format($totalRAB, 0, ',', '.') . ")", 'System');

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

    case 'superadmin_update_request':
        if ($_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'super admin') {
            jsonResponse(false, 'Akses ditolak.');
        }
        $id = (int)($_POST['id'] ?? 0);
        $type = $_POST['type'] ?? '';
        $applicant_name = $_POST['applicant_name'] ?? '';
        $applicant_unit = $_POST['applicant_unit'] ?? '';
        $purpose = $_POST['purpose'] ?? '';
        $status = $_POST['status'] ?? '';
        $rawNote = $_POST['note'] ?? '';
        $date_start = $_POST['date_start'] ?? null;
        $date_end = $_POST['date_end'] ?? null;
        $time_start = $_POST['time_start'] ?? null;
        $time_end = $_POST['time_end'] ?? null;

        $table = match($type) {
            'Vehicle' => 'vehicle_requests',
            'Room' => 'room_requests',
            'Dormitory' => 'dormitory_requests',
            'Zoom' => 'zoom_requests',
            'Repair' => 'repair_requests',
            'Item' => 'item_loan_requests',
            default => ''
        };
        if (!$id || !$table) jsonResponse(false, 'Parameter tidak lengkap.');

        $ts = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('d M Y H:i');
        $note = $rawNote . "\n[$ts] [Superadmin]: Data pengajuan telah diubah manual oleh Superadmin.";

        if ($type === 'Vehicle') {
            $dest = $_POST['destination'] ?? '';
            $vid = empty($_POST['vehicle_id']) ? null : (int)$_POST['vehicle_id'];
            $driver = $_POST['driver_name'] ?? '';
            $stmt = $conn->prepare("UPDATE `$table` SET applicant_name=?, applicant_unit=?, purpose=?, status=?, note=?, date_start=?, date_end=?, time_start=?, time_end=?, destination=?, vehicle_id=?, driver_name=? WHERE id=?");
            $stmt->bind_param("ssssssssssisi", $applicant_name, $applicant_unit, $purpose, $status, $note, $date_start, $date_end, $time_start, $time_end, $dest, $vid, $driver, $id);
        } else if ($type === 'Room') {
            $rid = empty($_POST['room_id']) ? null : (int)$_POST['room_id'];
            $part = (int)($_POST['participants'] ?? 0);
            $sn = $_POST['special_needs'] ?? '';
            $stmt = $conn->prepare("UPDATE `$table` SET applicant_name=?, applicant_unit=?, purpose=?, status=?, note=?, date_start=?, date_end=?, time_start=?, time_end=?, room_id=?, participants=?, special_needs=? WHERE id=?");
            $stmt->bind_param("sssssssssiisi", $applicant_name, $applicant_unit, $purpose, $status, $note, $date_start, $date_end, $time_start, $time_end, $rid, $part, $sn, $id);
        } else if ($type === 'Dormitory') {
            $did = empty($_POST['dormitory_id']) ? null : (int)$_POST['dormitory_id'];
            $occ = $_POST['occupant_name'] ?? '';
            $part = (int)($_POST['participants'] ?? 0);
            $stmt = $conn->prepare("UPDATE `$table` SET applicant_name=?, applicant_unit=?, purpose=?, status=?, note=?, date_start=?, date_end=?, time_start=?, time_end=?, dormitory_id=?, occupant_name=?, participants=? WHERE id=?");
            $stmt->bind_param("sssssssssisii", $applicant_name, $applicant_unit, $purpose, $status, $note, $date_start, $date_end, $time_start, $time_end, $did, $occ, $part, $id);
        } else if ($type === 'Zoom') {
            $zid = empty($_POST['zoom_account_id']) ? null : (int)$_POST['zoom_account_id'];
            $part = (int)($_POST['participants'] ?? 0);
            $stmt = $conn->prepare("UPDATE `$table` SET applicant_name=?, applicant_unit=?, purpose=?, status=?, note=?, date_start=?, date_end=?, time_start=?, time_end=?, zoom_account_id=?, participants=? WHERE id=?");
            $stmt->bind_param("sssssssssiii", $applicant_name, $applicant_unit, $purpose, $status, $note, $date_start, $date_end, $time_start, $time_end, $zid, $part, $id);
        } else if ($type === 'Repair') {
            $loc = $_POST['location_detail'] ?? '';
            $iss = $_POST['issue_description'] ?? '';
            $stmt = $conn->prepare("UPDATE `$table` SET applicant_name=?, applicant_unit=?, status=?, note=?, incident_date=?, incident_time=?, location_detail=?, issue_description=? WHERE id=?");
            $stmt->bind_param("ssssssssi", $applicant_name, $applicant_unit, $status, $note, $date_start, $time_start, $loc, $iss, $id);
        } else if ($type === 'Item') {
            $item = $_POST['item_name'] ?? '';
            $qty = (int)($_POST['item_quantity'] ?? 0);
            $stmt = $conn->prepare("UPDATE `$table` SET applicant_name=?, applicant_unit=?, purpose=?, status=?, note=?, loan_date=?, return_date=?, loan_time=?, return_time=?, item_name=?, item_quantity=? WHERE id=?");
            $stmt->bind_param("ssssssssssii", $applicant_name, $applicant_unit, $purpose, $status, $note, $date_start, $date_end, $time_start, $time_end, $item, $qty, $id);
        }

        if ($stmt->execute()) {
            jsonResponse(true, 'Data berhasil diubah.');
        } else {
            jsonResponse(false, 'Gagal mengubah data: ' . $stmt->error);
        }
        $stmt->close();
        break;

    case 'superadmin_delete_request':
        if ($_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'super admin') {
            jsonResponse(false, 'Akses ditolak.');
        }
        $id = (int)($_POST['id'] ?? 0);
        $type = $_POST['type'] ?? '';
        $table = match($type) {
            'Vehicle' => 'vehicle_requests',
            'Room' => 'room_requests',
            'Dormitory' => 'dormitory_requests',
            'Zoom' => 'zoom_requests',
            'Repair' => 'repair_requests',
            'Item' => 'item_loan_requests',
            default => ''
        };
        if (!$id || !$table) jsonResponse(false, 'Parameter tidak lengkap.');

        $conn->autocommit(false);
        try {
            if ($type === 'Repair') {
                $conn->query("DELETE FROM repair_budgets WHERE repair_request_id = $id");
            }
            if ($type === 'Room') {
                $conn->query("DELETE FROM room_checklists WHERE request_id = $id");
            }
            $stmt = $conn->prepare("DELETE FROM `$table` WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $conn->commit();
            jsonResponse(true, 'Data berhasil dihapus.');
        } catch (Exception $e) {
            $conn->rollback();
            jsonResponse(false, 'Gagal menghapus: ' . $e->getMessage());
        } finally {
            $conn->autocommit(true);
        }
        break;

    default:
        jsonResponse(false, 'Aksi tidak dikenali.');
}
