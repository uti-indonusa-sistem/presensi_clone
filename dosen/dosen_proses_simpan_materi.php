<?php
ob_start();

session_start();

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../config/Security.php';

// Check authentication
if (empty(getSafeCookie('simpreskul_nik'))) {
	header("Location: login_dosen.html");
	exit;
}

// Get safe parameters and clean up separators
$id_kelas = str_replace("_yz_", "-", getSafeGet('id_kelas', ''));
$pertemuan_ke = getSafeGet('pertemuan_ke', '');
$id_jurnal = getSafeGet('id_jurnal', '');
$id_ptk = str_replace("_yz_", "-", getSafeGet('id_ptk', ''));

// Fallback to cookie if id_ptk not in GET
if (empty($id_ptk)) {
	$id_ptk = getSafeCookie('simpreskul_id_ptk', '');
}

// Validate parameters exist
if (empty($id_kelas) || empty($pertemuan_ke) || empty($id_ptk)) {
	error_log("Missing parameters: id_kelas=$id_kelas, pertemuan_ke=$pertemuan_ke, id_ptk=$id_ptk");
	die('Missing required parameters');
}

// Handle delete request
if (isset($_POST['hapus'])) {
	// Escape for safe use in URL
	$safe_kelas = str_replace("-", "_yz_", $id_kelas);
	$safe_ptk = str_replace("-", "_yz_", $id_ptk);
	if (empty($id_jurnal)) {
		die('ID jurnal tidak ditemukan untuk tindakan hapus');
	}
	header("Location: dosen_konfirm-" . $id_jurnal . "-" . $safe_kelas . "-" . $safe_ptk . ".html");
	exit;
}

// Handle save request
if (isset($_POST['lanjut'])) {
	// Validate inputs
	$tanggal = getSafePost('tanggal', '');
	$materi = getSafePost('materi', '');
	$kegiatan = getSafePost('kegiatan', '');
	$ruang = getSafePost('ruang', '');

	if (empty($tanggal) || empty($materi)) {
		die('Tanggal dan materi harus diisi');
	}

	// Check dateline
	if ($tanggal <= $simpreskulV2_dateline) {
		header("Location: warning-" . str_replace("-", "_yz_", $id_kelas) . ".html");
		exit;
	}

	// PREPARED STATEMENT - Get kelas info safely
	$query = "SELECT kode_mk, id_ptk, id_smt, id_sms, nm_kls, nm_mk FROM viewKelasKuliah WHERE xid_kls = ? AND id_ptk = ?";
	$stmt = $connection->prepare($query);

	if (!$stmt) {
		error_log('Prepare failed: ' . $connection->error);
		die('Database error');
	}

	$stmt->bind_param("ss", $id_kelas, $id_ptk);

	if (!$stmt->execute()) {
		error_log('Execute failed: ' . $stmt->error);
		die('Database error');
	}

	$result = $stmt->get_result();

	if (!$result || $result->num_rows === 0) {
		error_log('Kelas not found: ' . $id_kelas);
		die('Kelas tidak ditemukan');
	}

	$data = $result->fetch_assoc();
	$stmt->close();

	// Extract class data
	$kode_mk = $data['kode_mk'];
	$id_smt = $data['id_smt'];
	$id_prodi = $data['id_sms'];
	$nm_kls = $data['nm_kls'];
	$nama_makul = isset($data['nm_mk']) ? $data['nm_mk'] : '';

	// PREPARED STATEMENT - Check if jurnal exists
	$query2 = "SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE xid_kls = ? AND pertemuan_ke = ? AND id_ptk = ?";
	$stmt2 = $connection->prepare($query2);
	$stmt2->bind_param("sss", $id_kelas, $pertemuan_ke, $id_ptk);
	$stmt2->execute();
	$result2 = $stmt2->get_result();

	if ($result2 && $result2->num_rows > 0) {
		// UPDATE existing record
		$row = $result2->fetch_assoc();
		$id_jurnal = $row['id_jurnal'];
		$stmt2->close();

		$query3 = "UPDATE presensi_jurnal_perkuliahan SET
			xid_kls = ?,
			tanggal = ?,
			kode_mk = ?,
			nama_makul = ?,
			materi = ?,
			id_ptk = ?,
			id_smt = ?,
			id_prodi = ?,
			nm_kls = ?,
			pertemuan_ke = ?,
			kegiatan = ?,
			ruang = ?,
			tanggal_input = NOW()
			WHERE id_jurnal = ?";

		$stmt3 = $connection->prepare($query3);
		$types = str_repeat('s', 12) . 'i';
		$params = [
			$id_kelas,
			$tanggal,
			$kode_mk,
			$nama_makul,
			$materi,
			$id_ptk,
			$id_smt,
			$id_prodi,
			$nm_kls,
			$pertemuan_ke,
			$kegiatan,
			$ruang,
			$id_jurnal
		];
		$stmt3->bind_param($types, ...$params);

		if (!$stmt3->execute()) {
			error_log('Update failed: ' . $stmt3->error);
			die('Gagal menyimpan data: ' . htmlspecialchars($stmt3->error));
		}
		$stmt3->close();
		$action = "diupdate";

	} else {
		// INSERT new record
		$stmt2->close();

		$query3 = "INSERT INTO presensi_jurnal_perkuliahan(
			xid_kls, tanggal, kode_mk, nama_makul, materi, id_ptk, id_smt, id_prodi,
			nm_kls, pertemuan_ke, kegiatan, ruang, tanggal_input
		) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

		$stmt3 = $connection->prepare($query3);
		$types = str_repeat('s', 12);
		$params = [
			$id_kelas,
			$tanggal,
			$kode_mk,
			$nama_makul,
			$materi,
			$id_ptk,
			$id_smt,
			$id_prodi,
			$nm_kls,
			$pertemuan_ke,
			$kegiatan,
			$ruang
		];
		$stmt3->bind_param($types, ...$params);

		if (!$stmt3->execute()) {
			error_log('Insert failed: ' . $stmt3->error);
			die('Gagal menyimpan data: ' . htmlspecialchars($stmt3->error));
		}
		$stmt3->close();
		$action = "disimpan";
	}

	// Success message with safe links
	$safe_kelas = str_replace("-", "_yz_", $id_kelas);
	$safe_ptk = str_replace("-", "_yz_", $id_ptk);

	echo "✅ Data berhasil " . $action . ".<br><br>";
	echo "<a href='dosen_jurnal_perkuliahan-" . htmlspecialchars($safe_kelas) . "-" . htmlspecialchars($safe_ptk) . ".html'>← Kembali ke Jurnal Perkuliahan</a> | ";
	echo "<a href='dosen_data_kehadiran-" . htmlspecialchars($safe_kelas) . "-" . htmlspecialchars($safe_ptk) . ".html'>Lanjut ke Presensi Mahasiswa →</a>";

	ob_end_flush();
	exit;
}
?>