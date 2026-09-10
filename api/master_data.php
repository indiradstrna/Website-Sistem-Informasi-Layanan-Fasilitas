<?php
session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Akses ditolak. Silakan login kembali.');
}

// Hanya mengizinkan superadmin atau admin untuk kelola Master Data
if (!in_array(strtolower($_SESSION['role']), ['superadmin', 'super admin', 'admin'])) {
    jsonResponse(false, 'Akses ditolak. Anda tidak memiliki izin.');
}

$action = $_POST['action'] ?? '';
$type   = $_POST['type'] ?? '';

$tableMap = [
    'vehicle'   => 'master_vehicles',
    'room'      => 'master_rooms',
    'dormitory' => 'master_dormitories'
];

if (!isset($tableMap[$type])) {
    jsonResponse(false, 'Tipe master data tidak valid.');
}

$table = $tableMap[$type];

switch ($action) {
    case 'add':
        $id   = trim($_POST['id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        
        if (empty($id) || empty($name)) {
            jsonResponse(false, 'ID dan Nama harus diisi.');
        }

        // Cek apakah ID sudah ada
        $check = $conn->prepare("SELECT id FROM `$table` WHERE id = ?");
        $check->bind_param("s", $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            jsonResponse(false, 'ID tersebut sudah digunakan. Silakan gunakan ID lain.');
        }
        $check->close();

        $stmt = $conn->prepare("INSERT INTO `$table` (id, name) VALUES (?, ?)");
        $stmt->bind_param("ss", $id, $name);
        if ($stmt->execute()) {
            jsonResponse(true, 'Data master berhasil ditambahkan.');
        } else {
            jsonResponse(false, 'Gagal menambahkan data: ' . $stmt->error);
        }
        break;

    case 'edit':
        $id   = trim($_POST['id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        
        if (empty($id) || empty($name)) {
            jsonResponse(false, 'ID dan Nama harus diisi.');
        }

        $stmt = $conn->prepare("UPDATE `$table` SET name = ? WHERE id = ?");
        $stmt->bind_param("ss", $name, $id);
        if ($stmt->execute()) {
            jsonResponse(true, 'Data master berhasil diperbarui.');
        } else {
            jsonResponse(false, 'Gagal memperbarui data: ' . $stmt->error);
        }
        break;

    case 'delete':
        $id = trim($_POST['id'] ?? '');
        
        if (empty($id)) {
            jsonResponse(false, 'ID tidak valid.');
        }

        // Cek apakah data ini pernah dipakai di tabel pengajuan
        $reqTable = '';
        if ($type === 'vehicle') $reqTable = 'vehicle_requests';
        if ($type === 'room') $reqTable = 'room_requests';
        if ($type === 'dormitory') $reqTable = 'dormitory_requests';

        if ($reqTable) {
            $colName = ($type === 'vehicle') ? 'vehicle_id' : (($type === 'room') ? 'room_id' : 'dormitory_id');
            $checkUsage = $conn->prepare("SELECT id FROM `$reqTable` WHERE `$colName` = ? LIMIT 1");
            $checkUsage->bind_param("s", $id);
            $checkUsage->execute();
            if ($checkUsage->get_result()->num_rows > 0) {
                jsonResponse(false, 'Data ini tidak dapat dihapus karena sudah pernah digunakan pada riwayat pengajuan.');
            }
            $checkUsage->close();
        }

        $stmt = $conn->prepare("DELETE FROM `$table` WHERE id = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            jsonResponse(true, 'Data master berhasil dihapus.');
        } else {
            jsonResponse(false, 'Gagal menghapus data: ' . $stmt->error);
        }
        break;

    default:
        jsonResponse(false, 'Aksi tidak dikenali.');
}
