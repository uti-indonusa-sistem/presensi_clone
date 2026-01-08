<?php
ob_start();
session_start();

include "../koneksi.php";

// Ensure DB connection exists
if (!isset($connection) || !$connection) {
	error_log('Database connection missing in dosen_proses_login.php');
	die('Database connection error');
}

// Get POST data safely
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
	header("Location: login.php?error=empty");
	exit;
}

// PREPARED STATEMENT - Safe from SQL injection
$query = "SELECT xid_ptk, nidn FROM wsia_dosen WHERE nidn = ?";
$stmt = $connection->prepare($query);

if (!$stmt) {
	error_log('Prepare failed: ' . $connection->error);
	header("Location: login.php?error=system");
	exit;
}

$stmt->bind_param("s", $username);

if (!$stmt->execute()) {
	error_log('Execute failed: ' . $stmt->error);
	header("Location: login.php?error=system");
	exit;
}

$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
	$data = $result->fetch_assoc();
	
	// For now, login succeeds with username match
	// TODO: Implement password verification when password field is added to table
	
	// Set secure cookies
	setcookie(
		"simpreskul_nik", 
		$username, 
		time() + (86400 * 30),
		"/",
		$_SERVER['HTTP_HOST'] ?? 'localhost',
		(strpos($_SERVER['HTTP'] ?? '', 'https') === 0),
		true
	);
	
	setcookie(
		"simpreskul_id_ptk", 
		$data['xid_ptk'], 
		time() + (86400 * 30),
		"/",
		$_SERVER['HTTP_HOST'] ?? 'localhost',
		(strpos($_SERVER['HTTP'] ?? '', 'https') === 0),
		true
	);
	
	$_SESSION['user_id'] = $data['xid_ptk'];
	$_SESSION['nidn'] = $username;
	$_SESSION['_user_agent'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
	
	$stmt->close();
	header("Location: index.php");
	exit;
} else {
	error_log('Login failed for username: ' . $username);
	$stmt->close();
	header("Location: login.php?error=invalid");
	exit;
}
?>