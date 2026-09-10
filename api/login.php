<?php
// ============================================================
// api/login.php — Handler Login
// Setara dengan: lib/auth-action.ts => loginUser()
// ============================================================

session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Method tidak diizinkan.');
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (!$username || !$password) {
    jsonResponse(false, 'Username dan Password harus diisi.');
}

// 1. Cari user berdasarkan nik (identitas karyawan)
try {
    $stmt = $conn->prepare("
        SELECT u.id, e.nip_nik as username, u.full_name, u.password, u.role, u.employee_id, u.telegram_chat_id, u.whatsapp_number, u.callmebot_apikey 
        FROM users u 
        INNER JOIN employees e ON u.employee_id = e.id 
        WHERE e.nip_nik = ?
    ");
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();
} catch (Exception $e) {
    jsonResponse(false, 'DB Error: ' . $e->getMessage());
}

if (!$user) {
    jsonResponse(false, 'NIP/NIK tidak ditemukan atau user tidak terdaftar.');
}

// 2. Cek password (plaintext — sesuai sistem existing, tidak pakai bcrypt)
if ($user['password'] !== $password) {
    jsonResponse(false, 'Password salah.');
}

// 3. Ambil department dari tabel employees jika ada employee_id
$department = '';
if ($user['employee_id']) {
    $stmt2 = $conn->prepare("SELECT department FROM employees WHERE id = ?");
    $stmt2->bind_param("i", $user['employee_id']);
    $stmt2->execute();
    $empResult = $stmt2->get_result();
    $empRow    = $empResult->fetch_assoc();
    $stmt2->close();
    if ($empRow) {
        $department = $empRow['department'];
    }
}

// 4. Set session
$_SESSION['user_id']    = $user['id'];
$_SESSION['username']   = $user['username'];
$_SESSION['full_name']  = $user['full_name'];
$_SESSION['role']       = $user['role'];
$_SESSION['department'] = $department;
$_SESSION['employee_id']= $user['employee_id'];
$_SESSION['telegram_chat_id'] = $user['telegram_chat_id'];
$_SESSION['whatsapp_number'] = $user['whatsapp_number'];
$_SESSION['callmebot_apikey'] = $user['callmebot_apikey'];

// 5. Tentukan redirect URL berdasarkan role
$normalizedRole = strtolower(trim($user['role']));
$redirectMap = [
    'admin'       => 'admin/index.php',
    'supervisor'  => 'supervisor/index.php',
    'user'        => 'user/index.php',
    'superadmin'  => 'superadmin/index.php',
    'super admin' => 'superadmin/index.php',
];

$redirectUrl = $redirectMap[$normalizedRole] ?? 'user/index.php';

jsonResponse(true, 'Login berhasil.', ['redirectUrl' => $redirectUrl]);
