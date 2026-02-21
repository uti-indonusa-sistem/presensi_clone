<?php
ob_start();
// Allow access for admin, dosen, or kaprodi
if (empty($_COOKIE['simpreskul_admin']) && empty($_COOKIE['simpreskul_id_ptk']) && empty($_COOKIE['simpreskul_id_prodi'])) {
	header('Location: ../admin/login.php');
	exit;
}
set_time_limit(9000000000);
include_once "../koneksi.php";
include_once "function.php";

require_once "../mpdf_v8.0.3-master/vendor/autoload.php";
$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);

if (empty($_POST['prodi']) || (empty($_POST['tahun_akademik']) && (empty($_POST['awal']) || empty($_POST['akhir'])))) {
	die("Error: Data Program Studi atau (Tahun Akademik/Periode) tidak dikirim.");
}

$sqlProdi = mysqli_query($connection, "SELECT xid_sms, nm_lemb FROM wsia_sms WHERE xid_sms='" . $_POST['prodi'] . "'");
$dataProdi = mysqli_fetch_array($sqlProdi);






if (!empty($_POST['tahun_akademik'])) {
	if (substr($_POST['tahun_akademik'], 4, 1) == '1') {
		$dataSemester = "GANJIL";
	} else if (substr($_POST['tahun_akademik'], 4, 1) == '2') {
		$dataSemester = "GENAP";
	}
	$textSemester = "SEMESTER $dataSemester TAHUN AKADEMIK " . substr($_POST['tahun_akademik'], 0, 4) . "/" . (substr($_POST['tahun_akademik'], 0, 4) + 1);
} else {
	$textSemester = "PERIODE " . date('d-m-Y', strtotime($_POST['awal'])) . " s/d " . date('d-m-Y', strtotime($_POST['akhir']));
}


echo "<br><table>
<tr><td colspan='13'><center>REKAPITULASI PRESENSI MAHASISWA</center></td></tr>
<tr><td colspan='13'><center>PROGRAM STUDI " . strtoupper($dataProdi['nm_lemb']) . "</center></td></tr>
<tr><td colspan='13'><center>$textSemester</center></td></tr>
</table><br>";

$filterTahun = "";
if (!empty($_POST['tahun_akademik'])) {
	$filterTahun = " AND viewKelasKuliah.id_smt='" . $_POST['tahun_akademik'] . "'";
} else {
	// Filter by classes that have journals in the date range
	$filterTahun = " AND EXISTS (SELECT 1 FROM presensi_jurnal_perkuliahan pj WHERE pj.xid_kls = viewKelasKuliah.xid_kls AND pj.tanggal BETWEEN '" . $_POST['awal'] . "' AND '" . $_POST['akhir'] . "')";
}

$sqlKelas = mysqli_query($connection, "SELECT viewKelasKuliah.*,wsia_mata_kuliah_kurikulum.* FROM viewKelasKuliah 
LEFT JOIN wsia_mata_kuliah_kurikulum ON viewKelasKuliah.xid_mk=wsia_mata_kuliah_kurikulum.id_mk
WHERE viewKelasKuliah.id_sms='" . $_POST['prodi'] . "' $filterTahun
GROUP BY viewKelasKuliah.nm_kls
");

while ($dataKelas = mysqli_fetch_array($sqlKelas)) {
	$sqlPA = mysqli_query($connection, "SELECT wsia_dosen.nm_ptk FROM wsia_mahasiswa_pt 
LEFT JOIN wsia_dosen ON wsia_mahasiswa_pt.pa=wsia_dosen.xid_ptk WHERE wsia_mahasiswa_pt.id_sms='" . $_POST['prodi'] . "' AND wsia_mahasiswa_pt.kelas='" . $dataKelas['nm_kls'] . "'
");
	$dataPA = mysqli_fetch_array($sqlPA);

	echo "<table>
<tr><td colspan='2'>Semester</td><td>: $dataKelas[smt]</td></tr>
<tr><td colspan='2'>Kelas</td><td>: $dataKelas[nm_kls]</td></tr>
<tr><td colspan='2'>Pembimbing Akademik</td><td>: $dataPA[nm_ptk]</td></tr>

</table>";

	$sqlMataKuliah = mysqli_query($connection, "SELECT viewKelasKuliah.*, wsia_dosen.nm_ptk FROM viewKelasKuliah 
LEFT JOIN wsia_dosen ON viewKelasKuliah.id_ptk=wsia_dosen.xid_ptk
WHERE viewKelasKuliah.id_sms='" . $_POST['prodi'] . "' $filterTahun AND nm_kls='" . $dataKelas['nm_kls'] . "'

");

	// Cache untuk matakuliah data
	$mataKuliahData = array();
	$dosen = array();
	$makul = array();
	$xid_kls = array();
	$id_ptk = array();
	$jumlahPertemuan = array();
	$kode = "A";
	$urutan = 1;

	while ($dataMataKuliah = mysqli_fetch_array($sqlMataKuliah)) {
		$dosen[$urutan] = $dataMataKuliah['nm_ptk'];
		$makul[$urutan] = $dataMataKuliah['nm_mk'];
		$xid_kls[$urutan] = $dataMataKuliah['xid_kls'];
		$id_ptk[$urutan] = $dataMataKuliah['id_ptk'];

		// Pre-calculate jumlah pertemuan based on date filter
		$filterTanggal = "";
		if (!empty($_POST['awal']) && !empty($_POST['akhir'])) {
			$filterTanggal = " AND tanggal BETWEEN '" . $_POST['awal'] . "' AND '" . $_POST['akhir'] . "'";
		}

		$sqlHitungPertemuan = mysqli_query($connection, "SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE " . cek_gabungan($dataMataKuliah['xid_kls']) . "
		AND id_ptk='" . str_replace("_yz_", "-", $dataMataKuliah['id_ptk']) . "' $filterTanggal");
		$jumlahPertemuan[$urutan] = mysqli_num_rows($sqlHitungPertemuan);
		$urutan++;
	}

	// Query semua presensi sekali (batch loading)
	$allPresensiData = array();
	$allPertemuan = array();
	for ($i = 1; $i < $urutan; $i++) {
		$sqlAllPertemuan = mysqli_query($connection, "SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE " . cek_gabungan($xid_kls[$i]) . "
	AND id_ptk='" . str_replace("_yz_", "-", $id_ptk[$i]) . "' $filterTanggal");
		$allPertemuan[$i] = array();
		while ($row = mysqli_fetch_array($sqlAllPertemuan)) {
			$allPertemuan[$i][] = $row['id_jurnal'];
		}
	}

	echo "<br><table border='1' style='border-collapse:collapse;'>
<tr><td rowspan='2'><center>No</center></td><td rowspan='2'><center>NIM</center></td><td rowspan='2'><center>Nama Mahasiswa</center></td>";
	for ($q = 1; $q < $urutan; $q++) {
		echo "<td style='width:60px' width='60px'><center>" . chr(64 + $q) . "</center></td>";
	}
	echo "</tr>";
	echo "<tr>";
	for ($q = 1; $q < $urutan; $q++) {
		echo "<td><center>" . $jumlahPertemuan[$q] . "X</center></td>";
	}
	echo "</tr>";


	// Use id_sms and nm_kls to get all students in the class, not just those in a specific subject ID
	// this fixes the issue where only one name appeared in the report
	$sqlMahasiswa = mysqli_query($connection, "SELECT wsia_mahasiswa_pt.*,wsia_mahasiswa.nm_pd FROM wsia_mahasiswa_pt
							LEFT JOIN wsia_mahasiswa ON wsia_mahasiswa_pt.id_pd=wsia_mahasiswa.xid_pd
							WHERE wsia_mahasiswa_pt.id_sms='" . $_POST['prodi'] . "' AND wsia_mahasiswa_pt.kelas='" . $dataKelas['nm_kls'] . "' 
							ORDER BY wsia_mahasiswa_pt.nipd ASC
							");

	// Load presensi data based on specific journals (meetings) for this class context
// This fixes the issue where a student's entire history with a lecturer was being counted
	$all_jurnal_ids = array();
	foreach ($allPertemuan as $jurnals) {
		foreach ($jurnals as $jid) {
			$all_jurnal_ids[] = "'" . $jid . "'";
		}
	}

	$start_attendance_map = []; // [id_jurnal][nim] = 1

	if (count($all_jurnal_ids) > 0) {
		// Chunked query to avoid max packet size or query length limits if too many journals
		$chunks = array_chunk($all_jurnal_ids, 500);

		foreach ($chunks as $chunk) {
			$chunk_str = implode(",", $chunk);
			$sqlAllPresensi = mysqli_query($connection, "SELECT nim, id_jurnal FROM presensi_rekap 
										WHERE id_jurnal IN ($chunk_str)");
			while ($row = mysqli_fetch_array($sqlAllPresensi)) {
				$start_attendance_map[$row['id_jurnal']][$row['nim']] = 1;
			}
		}
	}

	$no = 1;
	while ($dataMahasiswa = mysqli_fetch_array($sqlMahasiswa)) {
		echo "<tr><td>$no</td><td>$dataMahasiswa[nipd]</td><td>$dataMahasiswa[nm_pd]</td>";
		$no++;
		for ($q = 1; $q < $urutan; $q++) {
			// Calculate specific presence for this subject
			$hadir = 0;
			if (isset($allPertemuan[$q])) {
				foreach ($allPertemuan[$q] as $jid_check) {
					if (isset($start_attendance_map[$jid_check][$dataMahasiswa['nipd']])) {
						$hadir++;
					}
				}
			}

			if (count($allPertemuan[$q]) != 0) {
				$presentaseKehadiran = number_format((($hadir / count($allPertemuan[$q])) * 100), 2);
				$data = explode(".", $presentaseKehadiran);
				if (substr($data[1], 0, 2) == '00') {
					$desimal = "<font color='white'>_</font>";
				} else {
					$desimal = "." . $data[1] . "<font color='white'>_</font>";
				}
				echo "<td>" . $data[0] . $desimal . "</td>";
			} else {
				echo "<td></td>";
			}
		}

		echo "</tr>";


	}

	echo "</table>";


	echo "<br><table border='1' style='border-collapse:collapse;'>
<tr><td><center>Kode</td><td colspan='2'><center>Dosen</td><td colspan='5'><center>Matakuliah</td></tr>
";
	$t = "A";
	for ($q = 1; $q < $urutan; $q++) {
		echo "<tr><td><center>$t</center></td><td colspan='2'>$dosen[$q]</td><td colspan='5'>$makul[$q]</td></tr>";
		$t++;
	}
	echo "</table><br>";
}

// Get HTML from buffer
$html = ob_get_clean();

// CSS styling
$css = "
<style>
body { font-family: Arial, sans-serif; font-size: 9pt; }
table { border-collapse: collapse; margin-bottom: 10px; }
td { padding: 4px; }
</style>
";

// Write HTML to PDF
$mpdf->WriteHTML($css . $html);

// Output PDF
$filename = 'Rekapitulasi_Presensi_' . $dataProdi['nm_lemb'] . '_' . date('Y-m-d') . '.pdf';
$filename = preg_replace('/[^a-zA-Z0-9._\-]/', '_', $filename);
$mpdf->Output($filename, 'D');
exit;

?>








?>