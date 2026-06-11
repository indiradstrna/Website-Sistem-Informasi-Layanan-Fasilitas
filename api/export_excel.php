<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'super admin'])) {
    die("Akses ditolak.");
}

$monthStr = $_GET['month'] ?? '';
$year = $_GET['year'] ?? date('Y');

$months = [
    'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04', 
    'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08', 
    'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
];

$monthNum = $months[$monthStr] ?? null;

$whereClause = "";
if ($monthNum) {
    $whereClause = "WHERE DATE_FORMAT(created_at, '%Y-%m') = '{$year}-{$monthNum}'";
} else {
    $whereClause = "WHERE DATE_FORMAT(created_at, '%Y') = '{$year}'";
}

$filename = "Laporan_SILATAS_{$monthStr}_{$year}.csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
// Tambahkan BOM agar Excel membaca karakter UTF-8 dengan benar
fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, ['Kategori', 'ID Request', 'Pemohon', 'Unit', 'Tanggal Pengajuan', 'Status', 'Detail/Keperluan']);

$tables = [
    'Kendaraan' => "SELECT 'Kendaraan' as cat, id, applicant_name, applicant_unit, created_at, status, purpose FROM vehicle_requests $whereClause",
    'Ruangan' => "SELECT 'Ruangan' as cat, id, applicant_name, applicant_unit, created_at, status, purpose FROM room_requests $whereClause",
    'Dormitory' => "SELECT 'Dormitory' as cat, id, applicant_name, applicant_unit, created_at, status, purpose FROM dormitory_requests $whereClause",
    'Zoom' => "SELECT 'Zoom' as cat, id, applicant_name, applicant_unit, created_at, status, purpose FROM zoom_requests $whereClause",
    'Perbaikan' => "SELECT 'Perbaikan' as cat, id, applicant_name, applicant_unit, created_at, status, issue_description as purpose FROM repair_requests $whereClause",
    'Barang' => "SELECT 'Barang' as cat, id, applicant_name, applicant_unit, created_at, status, purpose FROM item_loan_requests $whereClause",
];

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
                $row['purpose']
            ]);
        }
    }
}
fclose($output);
exit();
?>
