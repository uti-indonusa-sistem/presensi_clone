<?php
ob_start();

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../config/Security.php';

// Use safe input helpers
$username = getSafePost('username');
$password = getSafePost('password');

if (empty($username) || empty($password)) {
	header('Location: ' . $base_url . '/admin/login.php?error=missing');
	exit;
}

// Rate limiting could be added here (per-IP)

// Lookup user record from presensi_pengguna
$query = "SELECT id, username, password FROM presensi_pengguna WHERE username = ? LIMIT 1";
$result = executeSelect($connection, $query, 's', [$username]);

if ($result && $result->num_rows === 1) {
	$row = $result->fetch_assoc();
	$stored = $row['password'];

	$login_ok = false;

	// If stored password is a bcrypt hash
	// Try modern password verify first (BCRYPT)
	if (password_verify($password, $stored)) {
		$login_ok = true;

		// Rehash on login if needed (e.g. cost changed)
		if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
			$newHash = password_hash($password, PASSWORD_DEFAULT);
			$updateQuery = "UPDATE presensi_pengguna SET password = ? WHERE id = ?";
			$stmt = $connection->prepare($updateQuery);
			if ($stmt) {
				$stmt->bind_param('si', $newHash, $row['id']);
				$stmt->execute();
				$stmt->close();
			}
		}
	}
	// Fallback to MD5
	elseif (md5($password) === $stored) {
		$login_ok = true;

		// Upgrade to bcrypt
		$newHash = password_hash($password, PASSWORD_DEFAULT);
		$updateQuery = "UPDATE presensi_pengguna SET password = ? WHERE id = ?";
		$stmt = $connection->prepare($updateQuery);
		if ($stmt) {
			$stmt->bind_param('si', $newHash, $row['id']);
			$stmt->execute();
			$stmt->close();
		}
	}

	if ($login_ok) {
		// Successful login: set secure session + cookie
		setAdminSession((int) $row['id'], $row['username']);
		header('Location: ' . $base_url . '/admin.html');
		exit;
	}
}

// If DB lookup failed or no user found, allow environment-based admin (fallback)
$envAdmin = getenv('ADMIN_USER');
if ((!$result || ($result && $result->num_rows === 0)) && $envAdmin) {
	$envUser = $envAdmin;
	$envPassHash = getenv('ADMIN_PASS_HASH'); // bcrypt preferred
	$envPassPlain = getenv('ADMIN_PASS'); // fallback plain password

	if ($username === $envUser) {
		$envOk = false;
		if ($envPassHash) {
			if (password_verify($password, $envPassHash)) {
				$envOk = true;
			}
		} elseif ($envPassPlain) {
			if (hash_equals($envPassPlain, $password)) {
				$envOk = true;
			}
		}

		if ($envOk) {
			// Login via environment credentials — use id 0 to indicate synthetic user
			setAdminSession(0, $envUser);
			header('Location: ' . $base_url . '/admin.html');
			exit;
		}
	}
}

// Fallback: invalid credentials
header('Location: ' . $base_url . '/admin/login.php?error=invalid');
exit;
?>