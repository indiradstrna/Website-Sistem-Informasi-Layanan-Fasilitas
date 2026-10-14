<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'super admin'])) {
    die("Akses ditolak.");
}

$monthStr      = $_GET['month'] ?? '';
$year          = $_GET['year'] ?? date('Y');
$statusFilter  = $_GET['status'] ?? 'all';
$typeFilter    = $_GET['type'] ?? 'all';
$driverFilter  = $_GET['driver'] ?? 'all';
$techFilter    = $_GET['technician'] ?? 'all';

$months = [
    'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04', 
    'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08', 
    'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
];

$monthNum = $months[$monthStr] ?? null;

// Base date conditions
$baseConditions = [];
if ($year !== 'all' && !empty($year)) {
    $yEsc = (int)$year;
    if ($monthNum) {
        $baseConditions[] = "DATE_FORMAT(created_at, '%Y-%m') = '{$yEsc}-{$monthNum}'";
    } else {
        $baseConditions[] = "DATE_FORMAT(created_at, '%Y') = '{$yEsc}'";
    }
} elseif ($monthNum) {
    $baseConditions[] = "DATE_FORMAT(created_at, '%m') = '{$monthNum}'";
}

if ($statusFilter !== 'all' && !empty($statusFilter)) {
    $sEsc = $conn->real_escape_string($statusFilter);
    $baseConditions[] = "status = '$sEsc'";
}

$buildWhere = function($extraConditions = []) use ($baseConditions) {
    $all = array_merge($baseConditions, $extraConditions);
    return count($all) > 0 ? "WHERE " . implode(" AND ", $all) : "";
};

// Prepare dynamic filenames
$fileLabel = "Laporan_SILATAS";
if ($driverFilter !== 'all' && !empty($driverFilter)) {
    $fileLabel = "Laporan_Driver_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $driverFilter);
} elseif ($techFilter !== 'all' && !empty($techFilter)) {
    $fileLabel = "Laporan_Teknisi_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $techFilter);
} elseif ($typeFilter !== 'all') {
    $fileLabel = "Laporan_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $typeFilter);
}

$mLabel = ($monthStr && $monthStr !== 'all') ? $monthStr : 'SemuaBulan';
$yLabel = ($year && $year !== 'all') ? $year : 'SemuaTahun';
$filename = "{$fileLabel}_{$mLabel}_{$yLabel}.csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
// BOM UTF-8 for Excel compatibility
fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, ['Kategori', 'ID Request', 'Pemohon', 'Unit', 'Tanggal Pengajuan', 'Status', 'Petugas / Driver / Teknisi', 'Detail / Keperluan']);

// Build queries per facility
$vehExtra = [];
if ($driverFilter !== 'all' && !empty($driverFilter)) {
    $dEsc = $conn->real_escape_string($driverFilter);
    $vehExtra[] = "(driver_name = '$dEsc' OR driver_name LIKE '%$dEsc%')";
}
$vehWhere = $buildWhere($vehExtra);

$repExtra = [];
if ($techFilter !== 'all' && !empty($techFilter)) {
    $tEsc = $conn->real_escape_string($techFilter);
    $repExtra[] = "(technician_name = '$tEsc' OR technician_name LIKE '%$tEsc%')";
}
if (strpos($typeFilter, 'Repair_') === 0) {
    $sub = substr($typeFilter, 7);
    $subEsc = $conn->real_escape_string($sub);
    $repExtra[] = "repair_type = '$subEsc'";
}
$repWhere = $buildWhere($repExtra);

$stdWhere = $buildWhere();

$tables = [
    'Kendaraan' => "SELECT 'Kendaraan' as cat, id, applicant_name, applicant_unit, created_at, status, driver_name as assignee, CONCAT('[Tujuan: ', IFNULL(destination, '-'), '] Keperluan: ', IFNULL(purpose, '-'), IF(passenger_name IS NOT NULL AND passenger_name != '', CONCAT(' | Penumpang: ', passenger_name), '')) as purpose FROM vehicle_requests $vehWhere",
    'Ruangan'   => "SELECT 'Ruangan' as cat, id, applicant_name, applicant_unit, created_at, status, '-' as assignee, purpose FROM room_requests $stdWhere",
    'Dormitory' => "SELECT 'Dormitory' as cat, id, applicant_name, applicant_unit, created_at, status, '-' as assignee, CONCAT('Penghuni: ', IFNULL(occupant_name, '-'), ' | Keperluan: ', IFNULL(purpose, '-')) as purpose FROM dormitory_requests $stdWhere",
    'Zoom'      => "SELECT 'Zoom' as cat, id, applicant_name, applicant_unit, created_at, status, '-' as assignee, purpose FROM zoom_requests $stdWhere",
    'Perbaikan' => "SELECT CONCAT('Perbaikan (', IFNULL(repair_type, 'TIK'), ')') as cat, id, applicant_name, applicant_unit, created_at, status, technician_name as assignee, CONCAT('[', IFNULL(location_detail, '-'), '] ', issue_description) as purpose, repair_type FROM repair_requests $repWhere ORDER BY repair_type ASC, created_at DESC",
    'Barang'    => "SELECT 'Peminjaman Barang' as cat, id, applicant_name, applicant_unit, created_at, status, '-' as assignee, CONCAT('Barang: ', IFNULL(item_name, '-'), ' (', IFNULL(item_quantity, '1'), ') | Keperluan: ', IFNULL(purpose, '-')) as purpose FROM item_loan_requests $stdWhere",
];

// Handle filters priority
if ($driverFilter !== 'all' && !empty($driverFilter)) {
    // Specifically exporting Driver data -> only Kendaraan
    $tables = ['Kendaraan' => $tables['Kendaraan']];
} elseif ($techFilter !== 'all' && !empty($techFilter)) {
    // Specifically exporting Technician data -> only Perbaikan
    $tables = ['Perbaikan' => $tables['Perbaikan']];
} elseif ($typeFilter !== 'all') {
    if (strpos($typeFilter, 'Repair') === 0) {
        $tables = ['Perbaikan' => $tables['Perbaikan']];
    } elseif (isset($tables[$typeFilter])) {
        $tables = [$typeFilter => $tables[$typeFilter]];
    }
}

foreach ($tables as $cat => $query) {
    $res = $conn->query($query);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            fputcsv($output, [
                $row['cat'],
                "REQ-" . $row['id'],
                $row['applicant_name'],
                $row['applicant_unit'],
                $row['created_at'],
                strtoupper($row['status']),
                $row['assignee'] ?: '-',
                $row['purpose']
            ]);
        }
    }
}
fclose($output);
exit();
?>
