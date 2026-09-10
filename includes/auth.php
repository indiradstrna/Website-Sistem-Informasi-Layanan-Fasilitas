<?php
// ============================================================
// includes/auth.php — Guard Halaman (Proteksi Session)
// Setara dengan: lib/auth-action.ts => getSession()
// ============================================================
// Cara pakai: require_once di awal setiap halaman protected
// ============================================================

require_once __DIR__ . '/../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/index.php?error=not_logged_in');
    exit;
}

function getSession(): array {
    return [
        'id'       => $_SESSION['user_id']       ?? 0,
        'username' => $_SESSION['username']       ?? '',
        'fullName' => $_SESSION['full_name']      ?? '',
        'role'     => $_SESSION['role']           ?? 'user',
        'department'=> $_SESSION['department']    ?? '',
        'telegram_chat_id' => $_SESSION['telegram_chat_id'] ?? null,
        'whatsapp_number' => $_SESSION['whatsapp_number'] ?? null,
        'callmebot_apikey' => $_SESSION['callmebot_apikey'] ?? null,
    ];
}

// Fungsi helper: cek apakah user punya minimal satu dari role yang disebutkan
function hasRole(string ...$roles): bool {
    $sess = getSession();
    $userRoles = array_map('trim', explode(',', $sess['role']));
    return count(array_intersect($userRoles, $roles)) > 0;
}

// Fungsi helper: proteksi halaman berdasarkan role
function requireRole(string ...$roles): void {
    if (!hasRole(...$roles)) {
        header('Location: ' . BASE_URL . '/index.php?error=unauthorized');
        exit;
    }
}
