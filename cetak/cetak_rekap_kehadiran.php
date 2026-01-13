<?php
// Allow access for admin, dosen, or kaprodi
if (empty($_COOKIE['simpreskul_admin']) && empty($_COOKIE['simpreskul_id_ptk']) && empty($_COOKIE['simpreskul_id_prodi'])) {
	header('Location: ../admin/login.php');
	exit;
}

require_once "../mpdf_v8.0.3-master/vendor/autoload.php";
$mpdf = new \Mpdf\Mpdf();

set_time_limit(9000000000);
include_once "../koneksi.php";
include_once "function.php";

// Mulai capture output
ob_start();

if ($_POST['prodi'] != 'all') {
	$kode_prodi = "AND presensi_jurnal.prodi='" . $_POST['prodi'] . "'";
}

echo "<br><table>
<tr><td colspan='13'><b>PEMANTAUAN PERKULIAHAN ONLINE</b></td></tr>
<tr><td colspan='13'>SELAMA WORK FROM HOME</td></tr>
<tr><td colspan='13'><br></td></tr>";

echo "<br><table border='1'>

<tr  style='text-align:center;'><td><b>No</td><td><b>Nama Dosen</td><td><b>Mata Kuliah</td><td><b>Semester</td>
<td><b>Prodi/Kelas</td><td><b>Hari/Tanggal</td>
<td><b>Ruang/Media</td>
<td><b>Jumlah Mahasiswa Hadir</td>

<td><b>Bukti Pembelajaran</td></tr>
<tr  style='text-align:center;'><td><b>1</td><td><b>2</td><td><b>3</td><td><b>4</td><td><b>5</td><td><b>6</td><td><b>7</td><td><b>8</b></td></tr>";
$sqlKehadiran = mysqli_query($connection, "
SELECT presensi_jurnal.*,presensi_jurnal.tanggal AS tgl,
CONCAT(
presensi_jurnal.id_jadwal,
presensi_jurnal.tanggal,
presensi_jurnal.waktu,
presensi_jurnal.ruang,
presensi_jurnal.matkul,
presensi_jurnal.thn_akademik,
presensi_jurnal.nik,
presensi_jurnal.pertemuan_ke,
presensi_jurnal.materi,
presensi_jurnal.kegiatan) AS gabungan,
DATE_FORMAT(presensi_jurnal.tanggal,'%d-%m-%Y') AS tanggal,
DATE_FORMAT(presensi_jurnal.tanggal,'%a') AS hari, presensi_jurnal.bukti_pembelajaran,presensi_jurnal.ruang,
simpeg_pegawai.nama,siakad_matkul.nm_matkul,presensi_jurnal.prodi,siakad_jadwal.smt,presensi_jurnal.kelas,
presensi_jurnal.id_jurnal, IF(presensi_jurnal.kegiatan='','Perkuliahan',
IF(presensi_jurnal.kegiatan='1','Perkuliahan',IF(presensi_jurnal.kegiatan='2','UTS',IF(presensi_jurnal.kegiatan='3','UAS','Perkuliahan')))) AS kegiatan
FROM presensi_jurnal 
LEFT JOIN simpeg_pegawai ON presensi_jurnal.nik=simpeg_pegawai.nik
LEFT JOIN siakad_matkul ON presensi_jurnal.matkul=siakad_matkul.kd_matkul
LEFT JOIN siakad_jadwal ON presensi_jurnal.id_jadwal=siakad_jadwal.id_jadwal
WHERE presensi_jurnal.kegiatan!='2' AND presensi_jurnal.kegiatan!='3' AND (presensi_jurnal.tanggal 
BETWEEN '" . $_POST['tanggalAwal'] . "' AND '" . $_POST['tanggalAkhir'] . "') AND siakad_matkul.tahun='2017' " . $kode_prodi . "
GROUP BY gabungan");

// Batch load semua jumlah mahasiswa
$jumlahMahasiswaCache = array();
$allJurnal = array();
while ($row = mysqli_fetch_array($sqlKehadiran)) {
	$allJurnal[] = $row;
	$jumlahMahasiswaCache[$row['id_jurnal']] = null;
}

// Load semua jumlah mahasiswa dalam satu query
if (count($allJurnal) > 0) {
	$jurnalIds = array();
	foreach ($allJurnal as $j) {
		$jurnalIds[] = "'" . $j['id_jurnal'] . "'";
	}
	$sqlJumlah = mysqli_query($connection, "SELECT id_jurnal, COUNT(*) as total FROM presensi_rekap WHERE id_jurnal IN (" . implode(",", $jurnalIds) . ") GROUP BY id_jurnal");
	while ($row = mysqli_fetch_array($sqlJumlah)) {
		$jumlahMahasiswaCache[$row['id_jurnal']] = $row['total'];
	}
}

$no = 1;
foreach ($allJurnal as $dataKehadiran) {
	$jmlMahasiswa = isset($jumlahMahasiswaCache[$dataKehadiran['id_jurnal']]) ? $jumlahMahasiswaCache[$dataKehadiran['id_jurnal']] : 0;

	$sqlKelas = mysqli_query($connection, "SELECT*FROM presensi_jurnal WHERE 
		tanggal='" . $dataKehadiran['tgl'] . "' AND	
		waktu='" . $dataKehadiran['waktu'] . "' AND
		nik='" . $dataKehadiran['nik'] . "' AND	
		ruang='" . $dataKehadiran['ruang'] . "' AND	
		thn_akademik='" . $dataKehadiran['thn_akademik'] . "' AND
		pertemuan_ke='" . $dataKehadiran['pertemuan_ke'] . "' AND
		id_jadwal='" . $dataKehadiran['id_jadwal'] . "'
	");

	$kelas = "";
	$buktiPembelajaran = "";

	while ($dataKelas = mysqli_fetch_array($sqlKelas)) {
		if ($kelas == "") {
			$kelas = konversi_prodi_singkatan($dataKelas['prodi']) . " (" . $dataKelas['kelas'] . ")";

		} else {
			$kelas .= ", " . konversi_prodi_singkatan($dataKelas['prodi']) . " (" . $dataKelas['kelas'] . ")";
		}

		if ($buktiPembelajaran == "") {
			$buktiPembelajaran = $dataKelas['bukti_pembelajaran'];
		}
	}

	echo "<tr style='vertical-align:top'>
	<td style='text-align:center;'>$no</td>
	<td>" . $dataKehadiran['nama'] . "</td>
	<td>" . $dataKehadiran['nm_matkul'] . "</td>
	<td style='text-align:center;'>" . $dataKehadiran['smt'] . "</td>
	<td style='text-align:center;'>$kelas</td>
	<td>" . konversi_hari($dataKehadiran['hari']) . "/" . $dataKehadiran['tanggal'] . "</td>
	<td>" . $dataKehadiran['ruang'] . "</td>
	<td>" . $jmlMahasiswa . "</td>
	<td>$buktiPembelajaran</td>
	</tr>";
	$no++;

}
echo "</table>";

// Get HTML from buffer
$html = ob_get_clean();

// CSS styling
$css = "
<style>
body { font-family: Arial, sans-serif; font-size: 9pt; }
table { border-collapse: collapse; margin-bottom: 10px; }
td { padding: 4px; }
b { font-weight: bold; }
</style>
";

// Write HTML to PDF
$mpdf->WriteHTML($css . $html);

// Output PDF
$filename = 'Pemantauan_Perkuliahan_Online_' . date('Y-m-d') . '.pdf';
$mpdf->Output($filename, 'D');
exit;

?>