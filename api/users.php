<?php
// ============================================================
// api/users.php — Manajemen User (CRUD)
// Setara dengan: lib/action.ts => getUsers, addUser, updateUser, deleteUser
// ============================================================

session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Unauthorized.');
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Check admin role for admin-only actions
$adminOnly = ['get_all', 'get_employees', 'add', 'update', 'delete'];
if (in_array($action, $adminOnly) && $_SESSION['role'] !== 'admin') {
    jsonResponse(false, 'Forbidden: Admin access required.');
}

switch ($action) {

    case 'get_all':
        $res = $conn->query("
            SELECT u.id, u.employee_id, e.nip_nik, u.full_name, u.role, u.created_at 
            FROM users u 
            LEFT JOIN employees e ON u.employee_id = e.id 
            ORDER BY u.created_at DESC
        ");
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        break;

    case 'get_employees':
        $res = null;
        try {
            $res = $conn->query("SELECT id, full_name, position, department FROM employees WHERE status = 'active'");
        } catch (Exception $e) {}
        
        if (!$res) {
            try {
                $res = $conn->query("SELECT id, full_name, position, department FROM employees");
            } catch (Exception $e) {}
        }
        
        if (!$res) {
            try {
                $res = $conn->query("SELECT id, full_name, 'Pengemudi' as position, '' as department FROM employees");
            } catch (Exception $e) {}
        }
        
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'add':
        $full_name   = trim($_POST['full_name']   ?? '');
        $password    = trim($_POST['password']    ?? '');
        $role        = trim($_POST['role']        ?? '');
        $employee_id = !empty($_POST['employee_id']) ? (int)$_POST['employee_id'] : null;

        if (!$full_name || !$password || !$role || !$employee_id) {
            jsonResponse(false, 'Semua field (termasuk Nama dan Karyawan) harus diisi!');
        }

        $stmt = $conn->prepare("INSERT INTO users (employee_id, full_name, password, role) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $employee_id, $full_name, $password, $role);

        if ($stmt->execute()) {
            jsonResponse(true, 'User berhasil ditambahkan!');
        } else {
            $err = $stmt->error;
            if (strpos($err, 'Duplicate') !== false) {
                jsonResponse(false, 'Karyawan ini sudah memiliki akun!');
            }
            jsonResponse(false, 'Gagal menambahkan user.');
        }
        $stmt->close();
        break;

    case 'update':
        $id        = (int)($_POST['id']        ?? 0);
        $full_name = trim($_POST['full_name']  ?? '');
        $role      = trim($_POST['role']       ?? '');
        $password  = trim($_POST['password']   ?? '');

        if (!$id || !$full_name || !$role) {
            jsonResponse(false, 'Semua field wajib diisi!');
        }

        if ($password !== '') {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, role = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $full_name, $role, $password, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, role = ? WHERE id = ?");
            $stmt->bind_param("ssi", $full_name, $role, $id);
        }

        if ($stmt->execute()) {
            jsonResponse(true, 'User berhasil diupdate!');
        } else {
            jsonResponse(false, 'Gagal mengupdate user.');
        }
        $stmt->close();
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) jsonResponse(false, 'ID tidak valid.');

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            jsonResponse(true, 'User berhasil dihapus.');
        } else {
            jsonResponse(false, 'Gagal menghapus user.');
        }
        $stmt->close();
        break;

    case 'update_profile':
        $userId = (int)$_SESSION['user_id'];
        $chatId = trim($_POST['telegram_chat_id'] ?? '');
        $waNumber = trim($_POST['whatsapp_number'] ?? '');

        $stmt = $conn->prepare("UPDATE users SET telegram_chat_id = ?, whatsapp_number = ? WHERE id = ?");
        $stmt->bind_param("ssi", $chatId, $waNumber, $userId);
        if ($stmt->execute()) {
            $_SESSION['telegram_chat_id'] = $chatId; // Update session
            $_SESSION['whatsapp_number']  = $waNumber;
            jsonResponse(true, 'Profil berhasil diperbarui!');
        } else {
            jsonResponse(false, 'Gagal memperbarui profil.');
        }
        $stmt->close();
        break;

    case 'change_password':
        $userId = (int)$_SESSION['user_id'];
        $oldPass = trim($_POST['old_password'] ?? '');
        $newPass = trim($_POST['new_password'] ?? '');

        if (!$oldPass || !$newPass) {
            jsonResponse(false, 'Password lama dan baru wajib diisi!');
        }

        // Fetch current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        if (!$user) {
            jsonResponse(false, 'User tidak ditemukan.');
        }

        if ($user['password'] !== $oldPass) {
            jsonResponse(false, 'Password lama tidak cocok.');
        }

        // Update to new password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $newPass, $userId);
        if ($stmt->execute()) {
            jsonResponse(true, 'Password berhasil diperbarui!');
        } else {
            jsonResponse(false, 'Gagal memperbarui password.');
        }
        $stmt->close();
        break;

    default:
        jsonResponse(false, "Action '$action' tidak dikenali.");
}
