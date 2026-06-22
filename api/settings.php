<?php
session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Akses ditolak.');
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_all':
        // Hanya superadmin atau admin yang boleh lihat
        if (!in_array($_SESSION['role'], ['superadmin', 'super admin', 'admin'])) {
            jsonResponse(false, 'Akses ditolak.');
        }
        $settings = [];
        $res = $conn->query("SELECT setting_key, setting_value, setting_name, setting_type FROM system_settings");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $settings[$row['setting_key']] = $row;
            }
        }
        jsonResponse(true, 'Data pengaturan berhasil dimuat', $settings);
        break;

    case 'update_settings':
        if (!in_array($_SESSION['role'], ['superadmin', 'super admin'])) {
            jsonResponse(false, 'Akses ditolak. Hanya Superadmin yang dapat mengubah pengaturan.');
        }

        $updates = $_POST['settings'] ?? [];
        if (empty($updates) || !is_array($updates)) {
            jsonResponse(false, 'Tidak ada data pengaturan yang dikirim.');
        }

        $conn->autocommit(false);
        try {
            $stmt = $conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
            foreach ($updates as $key => $value) {
                $stmt->bind_param("ss", $value, $key);
                $stmt->execute();
            }
            $stmt->close();
            $conn->commit();
            jsonResponse(true, 'Pengaturan berhasil disimpan.');
        } catch (Exception $e) {
            $conn->rollback();
            jsonResponse(false, 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        } finally {
            $conn->autocommit(true);
        }
        break;

    default:
        jsonResponse(false, 'Aksi tidak dikenali.');
}
