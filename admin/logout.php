<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../config/Security.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION = [];
session_destroy();
setcookie('simpreskul_admin', '', time() - 3600, '/');
setcookie('simpreskul_nama_pengguna', '', time() - 3600, '/');
header('Location: ' . $base_url . '/admin/login.php');
exit;
?>