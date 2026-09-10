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

// Fungsi helper: cek role
function requireRole(string ...$roles): void {
    $sess = getSession();
    if (!in_array($sess['role'], $roles, true)) {
        header('Location: ' . BASE_URL . '/index.php?error=unauthorized');
        exit;
    }
}
