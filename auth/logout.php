<?php
session_start();
require_once '../config/config.php';

// Hapus semua data session
$_SESSION = [];

// Hancurkan session
session_destroy();

// Hapus cookie session (biar bersih total)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirect ke halaman utama
header("Location: ../index.php");
exit;
