<?php
ob_start();
error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

// Allow access for admin, dosen, or kaprodi
if (empty($_COOKIE['simpreskul_admin']) && empty($_COOKIE['simpreskul_id_ptk']) && empty($_COOKIE['simpreskul_id_prodi'])) {
	header("Location:../dosen/login.php");
	exit;
}

require_once "../mpdf_v8.0.3-master/vendor/autoload.php";
$mpdf = new \Mpdf\Mpdf();
$mpdf->AddPage("P", "", "", "", "", "15", "15", "15", "15", "", "", "", "", "", "", "", "", "", "", "", "A4");

include_once "function.php";

// Get id_ptk from URL or cookie
$id_ptk_param = isset($_GET['id_ptk']) ? str_replace("_yz_", "-", $_GET['id_ptk']) : '';
$id_kelas_param = isset($_GET['id_kelas']) ? str_replace("_yz_", "-", $_GET['id_kelas']) : '';

// Use id_ptk from URL if provided, otherwise from cookie
$id_ptk_use = !empty($id_ptk_param) ? $id_ptk_param : (isset($_COOKIE['simpreskul_id_ptk']) ? $_COOKIE['simpreskul_id_ptk'] : '');

if (empty($id_kelas_param)) {
	die("Error: Parameter id_kelas tidak ditemukan.");
}

// Query data kelas directly instead of using view_kelas function
$sqlKelas = mysqli_query($connection, "SELECT * FROM viewKelasKuliah WHERE xid_kls='" . mysqli_real_escape_string($connection, $id_kelas_param) . "' AND id_ptk='" . mysqli_real_escape_string($connection, $id_ptk_use) . "'");
$dataKelas = mysqli_fetch_array($sqlKelas);

if (!$dataKelas) {
	// Try without id_ptk filter
	$sqlKelas = mysqli_query($connection, "SELECT * FROM viewKelasKuliah WHERE xid_kls='" . mysqli_real_escape_string($connection, $id_kelas_param) . "' LIMIT 1");
	$dataKelas = mysqli_fetch_array($sqlKelas);
}

if (!$dataKelas) {
	die("Error: Data kelas tidak ditemukan untuk id_kelas: " . htmlspecialchars($id_kelas_param));
}

// Get dosen name
$sqlDosen = mysqli_query($connection, "SELECT nm_ptk FROM wsia_dosen WHERE xid_ptk='" . mysqli_real_escape_string($connection, $id_ptk_use) . "'");
$dataDosen = mysqli_fetch_array($sqlDosen);

// Get semester info
$sqlSmt = mysqli_query($connection, "SELECT smt FROM wsia_mata_kuliah_kurikulum WHERE id_mk='" . $dataKelas['xid_mk'] . "'");
$dataSmt = mysqli_fetch_array($sqlSmt);

// Get tahun akademik
$tahunAkademik = substr($dataKelas['id_smt'], 0, 4);
$tahunAkademikStr = $tahunAkademik . "-" . ($tahunAkademik + 1);
if (substr($dataKelas['id_smt'], 4, 1) == '1') {
	$semester = "GANJIL";
} else {
	$semester = "GENAP";
}

// Get prodi-kelas info (handle gabungan)
$prodi_kelas = $dataKelas['nm_lemb'] . " / " . $dataKelas['nm_kls'];
$sqlCekGabungan = mysqli_query($connection, "SELECT * FROM presensi_kelas_gabungan WHERE xid_kls='" . $id_kelas_param . "'");
$dataCekGabungan = mysqli_fetch_array($sqlCekGabungan);
if ($dataCekGabungan) {
	$prodi_kelas = "";
	$sqlGabungan = mysqli_query($connection, "SELECT xid_kls FROM presensi_kelas_gabungan WHERE id_gabungan='" . $dataCekGabungan['id_gabungan'] . "'");
	while ($g = mysqli_fetch_array($sqlGabungan)) {
		$sqlKelasGab = mysqli_query($connection, "SELECT nm_lemb, nm_kls FROM viewKelasKuliah WHERE xid_kls='" . $g['xid_kls'] . "' LIMIT 1");
		$dataKelasGab = mysqli_fetch_array($sqlKelasGab);
		if ($dataKelasGab) {
			$prodi_kelas .= $dataKelasGab['nm_lemb'] . " / " . $dataKelasGab['nm_kls'] . "<br>";
		}
	}
}

$header = "
<img src='../medicio/kop.jpg' width='100%'>
";

$title = "
<table width='100%'><tr><td style='text-align:center;'><b>PRESENSI DOSEN SEMESTER " . strtoupper($semester) . " TAHUN AKADEMIK $tahunAkademikStr</b></td></tr></table><br>
<table width='80%' border='0'>
    <tr><td style='height:10px' height='10px'>Mata Kuliah</td><td>:</td><td>" . $dataKelas['nm_mk'] . "</td></tr>
    <tr><td style='height:10px' height='10px'>SKS</td><td>:</td><td>" . $dataKelas['sks_mk'] . "</td></tr>
    <tr><td style='height:10px' height='10px'>Pengampu</td><td>:</td><td>" . $dataDosen['nm_ptk'] . "</td></tr>
    <tr valign='top'><td style='height:10px' height='10px'>Program Studi / Kelas</td><td>:</td><td>$prodi_kelas</td></tr>
    <tr><td style='height:10px' height='10px'>Semester</td><td>:</td><td>" . $dataSmt['smt'] . "</td></tr>
</table><br>
";

// Load all jurnal data once
$sqlAllJurnal = mysqli_query($connection, "SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE " . cek_gabungan($id_kelas_param) . "
    AND id_ptk='" . mysqli_real_escape_string($connection, $id_ptk_use) . "'");
$jurnalIds = array();
while ($j = mysqli_fetch_array($sqlAllJurnal)) {
	$jurnalIds[] = "'" . $j['id_jurnal'] . "'";
}

$jumlahCache = array();
if (count($jurnalIds) > 0) {
	$sqlJumlah = mysqli_query($connection, "SELECT id_jurnal, COUNT(*) as total FROM presensi_rekap WHERE id_jurnal IN (" . implode(",", $jurnalIds) . ") GROUP BY id_jurnal");
	while ($row = mysqli_fetch_array($sqlJumlah)) {
		$jumlahCache[$row['id_jurnal']] = $row['total'];
	}
}

$baris = "";
for ($i = 1; $i <= 16; $i++) {
	$sqlJurnal = mysqli_query($connection, "SELECT *, date_format(tanggal,'%d/%m/%Y') AS tanggal_fmt, date_format(tanggal,'%a') AS hari 
        FROM presensi_jurnal_perkuliahan 
        WHERE " . cek_gabungan($id_kelas_param) . "
        AND id_ptk='" . mysqli_real_escape_string($connection, $id_ptk_use) . "'
        AND pertemuan_ke='" . $i . "'");

	$dataJurnal = mysqli_fetch_array($sqlJurnal);
	$hari = konversi_hari($dataJurnal['hari']);

	$jumlah_mahasiswa = "";
	if (!empty($dataJurnal['id_jurnal'])) {
		$jumlah_mahasiswa = isset($jumlahCache[$dataJurnal['id_jurnal']]) ? $jumlahCache[$dataJurnal['id_jurnal']] : 0;
	}

	$baris .= "<tr><td style='height:50px' height='50px'>$i</td>
        <td>" . $dataJurnal['materi'] . "</td>
        <td>$hari<br>" . $dataJurnal['tanggal_fmt'] . "</td>
        <td>" . $dataJurnal['ruang'] . "</td>
        <td>$jumlah_mahasiswa</td></tr>";
}

$content = "<table width='100%' border='1' style='border:1px solid black; border-collapse:collapse;'>
    <tr>
        <td width='5%' valign='middle' style='text-align:center'>
            <b>Pertemuan Ke</b>
        </td>
        <td valign='middle' style='text-align:center' width='55%'>
            <b>Materi Perkuliahan</b>
        </td>
        <td valign='middle' style='text-align:center' width='15%'><b>Tanggal</b></td>
        <td valign='middle' style='text-align:center' width='15%'>
            <b>Ruang</b>
        </td>
        <td valign='middle' style='text-align:center' width='10%'>
            <b>Jumlah Mahasiswa</b>
        </td>
    </tr>
    $baris
</table>";

$namaFile = 'Jurnal_Perkuliahan_' . preg_replace('/[^a-zA-Z0-9]/', '_', $dataKelas['nm_mk']) . '_' . date('Y-m-d') . '.pdf';
$mpdf->WriteHTML($header . $title . $content);
$mpdf->Output($namaFile, 'D');
exit;
?>