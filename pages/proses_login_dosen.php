<?php
ob_start();
session_start();

include "koneksi.php";
require_once "../config/Security.php";

// Ensure DB connection exists
if (!isset($connection) || !$connection) {
	error_log('Database connection missing');
	header("Location: form_login_dosen.html?error=system");
	exit;
}

// Get input safely
$rfid_dosen = getSafePost('rfid_dosen', '');

// Validate input
if (empty($rfid_dosen)) {
	header("Location: form_login_dosen.html?error=empty");
	exit;
}

// PREPARED STATEMENT - Safe from SQL injection
$query = "SELECT nidn, xid_ptk FROM wsia_dosen WHERE nidn = ?";
$result = executeSelect($connection, $query, "s", [$rfid_dosen]);

if ($result && $result->num_rows > 0) {
	$data = $result->fetch_assoc();
	
	$_SESSION['nidn'] = $rfid_dosen;
	$_SESSION['xid_ptk'] = $data['xid_ptk'];
	$_SESSION['_user_agent'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
	
	header("Location: set_cookie-" . escape($data['nidn']) . "-dosen.html");
	exit;
} else {
	error_log('Dosen not found: ' . $rfid_dosen);
	header("Location: form_login_dosen.html?error=notfound");
	exit;
}
?>

	