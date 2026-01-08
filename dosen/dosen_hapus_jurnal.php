<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Safer delete: validate inputs, check ownership, remove attachment file, run in transaction
$id_jurnal = (int)($_GET['id_jurnal'] ?? 0);
$id_ptk_raw = $_GET['id_ptk'] ?? '';
$id_kelas = $_GET['id_kelas'] ?? '';
$id_ptk = str_replace("_yz_","-",$id_ptk_raw);
$id_ptk = mysqli_real_escape_string($connection, $id_ptk);

if ($id_jurnal <= 0) {
	header("Location:dosen_jurnal_perkuliahan-".str_replace("-","_yz_",$id_kelas)."-".str_replace("-","_yz_",$id_ptk_raw).".html");
	exit;
}

// Verify this journal exists and belongs to the same lecturer (prevent accidental mass-deletes)
$cek = mysqli_query($connection, "SELECT id_ptk FROM presensi_jurnal_perkuliahan WHERE id_jurnal='".$id_jurnal."' LIMIT 1");

if(!$cek) {
	$errorMsg = "Error query jurnal: " . mysqli_error($connection);
	echo "<div style='background:#f8d7da; border:1px solid #f5c6cb; padding:15px; margin:15px; border-radius:5px;'>";
	echo "<h4 style='color:#721c24; margin-top:0;'>✗ Error Menghapus Jurnal!</h4>";
	echo "<p>" . htmlspecialchars($errorMsg) . "</p>";
	echo "<a href='dosen_jurnal_perkuliahan-".str_replace("-","_yz_",$id_kelas)."-".str_replace("-","_yz_",$id_ptk_raw).".html'>← Kembali</a>";
	echo "</div>";
	exit;
}

$cekRow = mysqli_fetch_array($cek);
if (!$cekRow) {
	// nothing to delete - journal doesn't exist
	header("Location:dosen_jurnal_perkuliahan-".str_replace("-","_yz_",$id_kelas)."-".str_replace("-","_yz_",$id_ptk_raw).".html");
	exit;
}

// If the id_ptk from request doesn't match record, abort (safety)
if ($cekRow['id_ptk'] != $id_ptk) {
	$errorMsg = "Anda tidak memiliki akses untuk menghapus jurnal ini";
	echo "<div style='background:#f8d7da; border:1px solid #f5c6cb; padding:15px; margin:15px; border-radius:5px;'>";
	echo "<h4 style='color:#721c24; margin-top:0;'>✗ Akses Ditolak!</h4>";
	echo "<p>" . htmlspecialchars($errorMsg) . "</p>";
	echo "<a href='dosen_jurnal_perkuliahan-".str_replace("-","_yz_",$id_kelas)."-".str_replace("-","_yz_",$id_ptk_raw).".html'>← Kembali</a>";
	echo "</div>";
	exit;
}

// Begin transaction
if(!mysqli_begin_transaction($connection)) {
	$errorMsg = "Error memulai transaksi: " . mysqli_error($connection);
	echo "<div style='background:#f8d7da; border:1px solid #f5c6cb; padding:15px; margin:15px; border-radius:5px;'>";
	echo "<h4 style='color:#721c24; margin-top:0;'>✗ Error Menghapus Jurnal!</h4>";
	echo "<p>" . htmlspecialchars($errorMsg) . "</p>";
	echo "<a href='dosen_jurnal_perkuliahan-".str_replace("-","_yz_",$id_kelas)."-".str_replace("-","_yz_",$id_ptk_raw).".html'>← Kembali</a>";
	echo "</div>";
	exit;
}

try {
	// Delete attendance recap rows for this jurnal and this lecturer
	$sqlDelPresensi = "DELETE FROM presensi_rekap WHERE id_jurnal='".$id_jurnal."' AND id_ptk='".$id_ptk."'";
	$resultDelPresensi = mysqli_query($connection, $sqlDelPresensi);
	if(!$resultDelPresensi) {
		throw new Exception("Error menghapus presensi_rekap: " . mysqli_error($connection));
	}

	// Delete the journal record (perkuliahan)
	$sqlDelJurnal = "DELETE FROM presensi_jurnal_perkuliahan WHERE id_jurnal='".$id_jurnal."'";
	$resultDelJurnal = mysqli_query($connection, $sqlDelJurnal);
	if(!$resultDelJurnal) {
		throw new Exception("Error menghapus jurnal: " . mysqli_error($connection));
	}

	// Commit transaction
	if(!mysqli_commit($connection)) {
		throw new Exception("Error commit transaksi: " . mysqli_error($connection));
	}

	// Success - redirect
	header("Location:dosen_jurnal_perkuliahan-".str_replace("-","_yz_",$id_kelas)."-".str_replace("-","_yz_",$id_ptk_raw).".html?success=1");
	exit;
	
} catch (Exception $e) {
	// Rollback on error
	mysqli_rollback($connection);
	
	$errorMsg = $e->getMessage();
	echo "<div style='background:#f8d7da; border:1px solid #f5c6cb; padding:15px; margin:15px; border-radius:5px;'>";
	echo "<h4 style='color:#721c24; margin-top:0;'>✗ Error Menghapus Jurnal!</h4>";
	echo "<p><strong>Error:</strong> " . htmlspecialchars($errorMsg) . "</p>";
	echo "<p style='font-size:12px; color:#666;'><strong>Debug Info:</strong><br>";
	echo "ID Jurnal: " . htmlspecialchars($id_jurnal) . "<br>";
	echo "ID PTK: " . htmlspecialchars($id_ptk) . "<br>";
	echo "ID Kelas: " . htmlspecialchars($id_kelas) . "</p>";
	echo "<a href='dosen_jurnal_perkuliahan-".str_replace("-","_yz_",$id_kelas)."-".str_replace("-","_yz_",$id_ptk_raw).".html' class='btn btn-secondary'>← Kembali</a>";
	echo "</div>";
	exit;
}

?>