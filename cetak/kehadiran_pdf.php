<?php
ob_start();
error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

// Allow access for admin, dosen, or kaprodi
if (empty($_COOKIE['simpreskul_admin']) && empty($_COOKIE['simpreskul_id_ptk']) && empty($_COOKIE['simpreskul_id_prodi'])) {
    header('Location: ../admin/login.php');
    exit;
}

require_once "../mpdf_v8.0.3-master/vendor/autoload.php";
$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
$mpdf->AddPage("L", "", "", "", "", "15", "15", "15", "15", "", "", "", "", "", "", "", "", "", "", "", "A4");

include_once "function.php";

// Get parameters
$id_kelas = isset($_GET['id_kelas']) ? str_replace("_yz_", "-", $_GET['id_kelas']) : '';
$id_ptk = isset($_GET['id_ptk']) ? str_replace("_yz_", "-", $_GET['id_ptk']) : '';

if (empty($id_kelas)) {
    die("Error: Parameter id_kelas tidak ditemukan.");
}

// Get class/course info
$sqlKelas = mysqli_query($connection, "SELECT * FROM viewKelasKuliah WHERE xid_kls='" . $id_kelas . "' LIMIT 1");
$dataKelas = mysqli_fetch_array($sqlKelas);

if (!$dataKelas) {
    die("Error: Data kelas tidak ditemukan.");
}

// Get tahun akademik
$tahunAkademik = substr($dataKelas['id_smt'], 0, 4);
$tahunAkademikStr = $tahunAkademik . "-" . ($tahunAkademik + 1);
if (substr($dataKelas['id_smt'], 4, 1) == '1') {
    $semester = "GANJIL";
} else {
    $semester = "GENAP";
}

// Get dosen
$sqlDosen = mysqli_query($connection, "SELECT nm_ptk FROM wsia_dosen WHERE xid_ptk='" . $id_ptk . "'");
$dataDosen = mysqli_fetch_array($sqlDosen);

// Get semester matakuliah
$sqlSmt = mysqli_query($connection, "SELECT smt FROM wsia_mata_kuliah_kurikulum WHERE id_mk='" . $dataKelas['xid_mk'] . "'");
$dataSmt = mysqli_fetch_array($sqlSmt);

$header = "
<img src='../medicio/kop.jpg' width='100%'>
";

$title = "
<table width='100%'><tr><td style='text-align:center;'><b>PRESENSI MAHASISWA SEMESTER " . strtoupper($semester) . " TAHUN AKADEMIK $tahunAkademikStr</b></td></tr></table><br>
<table width='80%' border='0'>
    <tr><td style='height:10px' width='20%'>Mata Kuliah</td><td width='2%'>:</td><td>" . $dataKelas['nm_mk'] . "</td></tr>
    <tr><td style='height:10px'>SKS</td><td>:</td><td>" . $dataKelas['sks_mk'] . "</td></tr>
    <tr><td style='height:10px'>Dosen Pengampu</td><td>:</td><td>" . $dataDosen['nm_ptk'] . "</td></tr>
    <tr><td style='height:10px'>Program Studi</td><td>:</td><td>" . $dataKelas['nm_lemb'] . "</td></tr>
    <tr><td style='height:10px'>Kelas</td><td>:</td><td>" . $dataKelas['nm_kls'] . "</td></tr>
    <tr><td style='height:10px'>Semester</td><td>:</td><td>" . $dataSmt['smt'] . "</td></tr>
</table><br>
";

// Get all pertemuan (meetings)
$sqlPertemuan = mysqli_query($connection, "SELECT * FROM presensi_jurnal_perkuliahan WHERE " . cek_gabungan($id_kelas) . "
    AND id_ptk='" . $id_ptk . "' ORDER BY pertemuan_ke ASC");
$jumlahPertemuan = mysqli_num_rows($sqlPertemuan);

$pertemuanData = array();
$jurnalIds = array();
while ($p = mysqli_fetch_array($sqlPertemuan)) {
    $pertemuanData[$p['pertemuan_ke']] = $p;
    $jurnalIds[] = "'" . $p['id_jurnal'] . "'";
}

// Load all attendance data in one query
$attendanceMap = array(); // [id_jurnal][nim] = 1
if (count($jurnalIds) > 0) {
    $sqlPresensi = mysqli_query($connection, "SELECT nim, id_jurnal FROM presensi_rekap WHERE id_jurnal IN (" . implode(",", $jurnalIds) . ")");
    while ($row = mysqli_fetch_array($sqlPresensi)) {
        $attendanceMap[$row['id_jurnal']][$row['nim']] = 1;
    }
}

// Get students from the class
// Get students from the class
$sqlMahasiswa = mysqli_query($connection, "SELECT wsia_mahasiswa_pt.*, wsia_mahasiswa.nm_pd 
    FROM wsia_mahasiswa_pt
    LEFT JOIN wsia_mahasiswa ON wsia_mahasiswa_pt.id_pd=wsia_mahasiswa.xid_pd
    WHERE wsia_mahasiswa_pt.xid_kls='" . $id_kelas . "' 
    ORDER BY wsia_mahasiswa_pt.nipd ASC");

// Build table header
$headerRow = "<tr>
    <td style='text-align:center; font-weight:bold;' width='3%'>No</td>
    <td style='text-align:center; font-weight:bold;' width='10%'>NIM</td>
    <td style='text-align:center; font-weight:bold;' width='25%'>Nama Mahasiswa</td>";

for ($i = 1; $i <= 16; $i++) {
    $tgl = "";
    if (isset($pertemuanData[$i])) {
        $tgl = date("d/m", strtotime($pertemuanData[$i]['tanggal']));
    }
    $headerRow .= "<td style='text-align:center; font-weight:bold;' width='3%'>$i<br><small>$tgl</small></td>";
}
$headerRow .= "<td style='text-align:center; font-weight:bold;' width='5%'>Hadir</td>";
$headerRow .= "<td style='text-align:center; font-weight:bold;' width='5%'>%</td>";
$headerRow .= "</tr>";

// Build student rows
$studentRows = "";
$no = 1;
while ($mhs = mysqli_fetch_array($sqlMahasiswa)) {
    $studentRows .= "<tr>
        <td style='text-align:center;'>$no</td>
        <td>" . $mhs['nipd'] . "</td>
        <td>" . $mhs['nm_pd'] . "</td>";

    $hadir = 0;
    for ($i = 1; $i <= 16; $i++) {
        if (isset($pertemuanData[$i])) {
            $jurnalId = $pertemuanData[$i]['id_jurnal'];
            if (isset($attendanceMap[$jurnalId][$mhs['nipd']])) {
                $studentRows .= "<td style='text-align:center;'>&radic;</td>";
                $hadir++;
            } else {
                $studentRows .= "<td style='text-align:center; color:red;'>X</td>";
            }
        } else {
            $studentRows .= "<td style='text-align:center;'>-</td>";
        }
    }

    // Calculate percentage
    $activePertemuan = count($pertemuanData);
    $persen = $activePertemuan > 0 ? number_format(($hadir / $activePertemuan) * 100, 1) : 0;

    $studentRows .= "<td style='text-align:center;'>$hadir/$activePertemuan</td>";
    $studentRows .= "<td style='text-align:center;'>$persen%</td>";
    $studentRows .= "</tr>";
    $no++;
}

$content = "<table width='100%' border='1' style='border:1px solid black; border-collapse:collapse; font-size:9pt;'>
    $headerRow
    $studentRows
</table>";

// Generate PDF
$namaFile = 'Presensi_Mahasiswa_' . $dataKelas['nm_lemb'] . '_' . $dataKelas['nm_kls'] . '_' . date('Y-m-d') . '.pdf';
$namaFile = preg_replace('/[^a-zA-Z0-9._\\-]/', '_', $namaFile);

$mpdf->WriteHTML($header . $title . $content);
$mpdf->Output($namaFile, 'D');
exit;
?>