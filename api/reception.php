<?php
// ============================================================
// api/reception.php — Public API untuk Layar Informasi (Reception)
// ============================================================

require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

/* 
  Endpoint ini terbuka untuk umum (public display).
  Hanya mengembalikan data yang berstatus 'approved' atau 'in-progress'.
*/

$today = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

// 1. Ambil dari vehicle_requests
$vRes = $conn->query("
    SELECT id, 'Vehicle' as type, applicant_name, applicant_unit, 
           DATE_FORMAT(date_start, '%Y-%m-%d') as date_start, time_start, 
           DATE_FORMAT(date_end, '%Y-%m-%d') as date_end, time_end, 
           purpose, status, vehicle_id as sub_title, driver_name as info_extra
    FROM vehicle_requests 
    WHERE (status IN ('approved', 'in-progress')) 
      AND (date_start >= '$today' OR date_end >= '$today')
    ORDER BY date_start ASC, time_start ASC
");
$vehicles = $vRes->fetch_all(MYSQLI_ASSOC);

// 2. Ambil dari room_requests
$rRes = $conn->query("
    SELECT id, 'Room' as type, applicant_name, applicant_unit, 
           DATE_FORMAT(date_start, '%Y-%m-%d') as date_start, time_start, 
           DATE_FORMAT(date_end, '%Y-%m-%d') as date_end, time_end, 
           purpose, status, room_id as sub_title, participants as info_extra
    FROM room_requests 
    WHERE (status IN ('approved', 'in-progress')) 
      AND (date_start >= '$today' OR date_end >= '$today')
    ORDER BY date_start ASC, time_start ASC
");
$rooms = $rRes->fetch_all(MYSQLI_ASSOC);

// 3. Ambil dari zoom_requests
$zRes = $conn->query("
    SELECT id, 'Zoom' as type, applicant_name, applicant_unit, 
           DATE_FORMAT(date_start, '%Y-%m-%d') as date_start, time_start, 
           DATE_FORMAT(date_end, '%Y-%m-%d') as date_end, time_end, 
           purpose, status, zoom_account_id as sub_title, participants as info_extra
    FROM zoom_requests 
    WHERE (status IN ('approved')) 
      AND (date_start >= '$today' OR date_end >= '$today')
    ORDER BY date_start ASC, time_start ASC
");
$zooms = $zRes->fetch_all(MYSQLI_ASSOC);

// 4. Ambil dari item_loan_requests
$iRes = $conn->query("
    SELECT id, 'Item' as type, applicant_name, applicant_unit, 
           DATE_FORMAT(loan_date, '%Y-%m-%d') as date_start, loan_time as time_start, 
           DATE_FORMAT(return_date, '%Y-%m-%d') as date_end, return_time as time_end, 
           purpose, status, item_name as sub_title, item_quantity as info_extra
    FROM item_loan_requests 
    WHERE (status IN ('approved', 'in-progress')) 
      AND (loan_date >= '$today' OR return_date >= '$today')
    ORDER BY loan_date ASC, loan_time ASC
");
$items = $iRes->fetch_all(MYSQLI_ASSOC);

// 5. Ambil dari dormitory_requests
$dRes = $conn->query("
    SELECT id, 'Dormitory' as type, occupant_name as applicant_name, applicant_unit, 
           DATE_FORMAT(date_start, '%Y-%m-%d') as date_start, time_start, 
           DATE_FORMAT(date_end, '%Y-%m-%d') as date_end, time_end, 
           purpose, status, dormitory_id as sub_title, participants as info_extra
    FROM dormitory_requests 
    WHERE (status IN ('approved', 'in-progress')) 
      AND (date_start >= '$today' OR date_end >= '$today')
    ORDER BY date_start ASC, time_start ASC
");
$dormitories = $dRes->fetch_all(MYSQLI_ASSOC);

// Gabungkan semua
$all = array_merge($vehicles, $rooms, $zooms, $items, $dormitories);

// Sortir berdasarkan tanggal dan waktu
usort($all, function($a, $b) {
    if ($a['date_start'] === $b['date_start']) {
        return strcmp($a['time_start'], $b['time_start']);
    }
    return strcmp($a['date_start'], $b['date_start']);
});

echo json_encode($all);
