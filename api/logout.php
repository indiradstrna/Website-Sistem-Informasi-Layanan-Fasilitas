<?php
// ============================================================
// api/logout.php — Handler Logout
// Setara dengan: lib/auth-action.ts => logoutUser()
// ============================================================

session_start();
session_unset();
session_destroy();
header('Location: ../index.php');
exit;
