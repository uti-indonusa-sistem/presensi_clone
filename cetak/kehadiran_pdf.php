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

// Get parameters - keep raw GET values for cek_gabungan (which does its own _yz_ replacement)
$id_kelas_raw = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : '';
$id_kelas = str_replace("_yz_", "-", $id_kelas_raw);
$id_ptk_raw = isset($_GET['id_ptk']) ? $_GET['id_ptk'] : '';
$id_ptk = str_replace("_yz_", "-", $id_ptk_raw);

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

// Get all pertemuan (meetings) - use raw id_kelas for cek_gabungan (it does its own _yz_ replacement)
$cek_kls = cek_gabungan($id_kelas_raw);
if (empty($cek_kls)) {
    $cek_kls = "presensi_jurnal_perkuliahan.xid_kls='" . $id_kelas . "'";
}
$sqlPertemuan = mysqli_query($connection, "SELECT * FROM presensi_jurnal_perkuliahan WHERE (" . $cek_kls . ")
    AND id_ptk='" . $id_ptk . "' AND pertemuan_ke BETWEEN 1 AND 16 ORDER BY pertemuan_ke ASC");
$jumlahPertemuan = mysqli_num_rows($sqlPertemuan);

$pertemuanData = array();
$pertemuanJournals = array();
while ($p = mysqli_fetch_array($sqlPertemuan)) {
    $k = (int)$p['pertemuan_ke'];
    $pertemuanData[$k] = $p;
    $pertemuanJournals[$k] = trim((string)$p['id_jurnal']);
}

// Load all attendance data - same approach as dosen_data_kehadiran.php
$allJurnalIds = array_values($pertemuanJournals);
if (empty($allJurnalIds)) {
    $allJurnalIds = array(0);
}
$idsList = implode(',', array_map('intval', $allJurnalIds));

$attendanceMap = array(); // [id_jurnal][nim] = true
$presentCount  = array(); // [nim] = total meetings attended
if (!empty($allJurnalIds) && $allJurnalIds[0] != 0) {
    $sqlPresensi = mysqli_query($connection, "SELECT nim, id_jurnal FROM presensi_rekap WHERE id_jurnal IN (" . $idsList . ") AND id_ptk='" . $id_ptk . "'");
    if ($sqlPresensi) while ($row = mysqli_fetch_array($sqlPresensi)) {
        $nimRaw = trim((string)$row['nim']);
        $jid    = trim((string)$row['id_jurnal']);
        
        // Store by raw JID
        $attendanceMap[$jid][$nimRaw] = true;
        
        // Safely normalize numeric JID (handle '01' vs '1' mismatch) without breaking non-numeric/long IDs
        // Only normalize if purely numeric and length is safe (standard int/bigint range)
        if (preg_match('/^[0-9]+$/', $jid) && strlen($jid) < 19) {
            $jidInt = (string) intval($jid);
            if ($jidInt !== $jid) {
                $attendanceMap[$jidInt][$nimRaw] = true;
            }
        }

        // Also support int-like matching for NIM
        $nimInt = (string) intval($nimRaw);
        if ($nimInt !== $nimRaw) {
            $attendanceMap[$jid][$nimInt] = true;
            // Also store under normalized JID if applicable
            if (isset($jidInt) && $jidInt !== $jid) {
                $attendanceMap[$jidInt][$nimInt] = true;
            }
        }
        
        // Accumulate per-student present count (same as web view)
        if (!isset($presentCount[$nimRaw])) $presentCount[$nimRaw] = 0;
        $presentCount[$nimRaw]++;
    }
}
$jumlahAllPertemuan = count(array_filter($pertemuanJournals));

// Get Students - Strict Dosen Logic (viewNilai + RIGHT JOIN)
// This ensures the PDF matches the screen exactly
$sqlMahasiswa = mysqli_query($connection, "SELECT viewNilai.*,wsia_mahasiswa_pt.*,wsia_mahasiswa.nm_pd FROM viewNilai 
    RIGHT JOIN wsia_mahasiswa_pt ON viewNilai.xid_reg_pd=wsia_mahasiswa_pt.xid_reg_pd
    LEFT JOIN wsia_mahasiswa ON wsia_mahasiswa_pt.id_pd=wsia_mahasiswa.xid_pd
    WHERE viewNilai.vid_kls='" . $id_kelas . "' ORDER BY wsia_mahasiswa_pt.nipd ASC");

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
$headerRow .= "<td style='text-align:center; font-weight:bold;' width='5%'>Presentase<br>%</td>";
$headerRow .= "</tr>";

// Build student rows
$studentRows = "";
$no = 1;
if ($sqlMahasiswa)
    while ($mhs = mysqli_fetch_array($sqlMahasiswa)) {
        $nipd = $mhs['nipd'];
        $studentRows .= "<tr>
        <td style='text-align:center;'>$no</td>
        <td>" . $nipd . "</td>
        <td>" . $mhs['nm_pd'] . "</td>";

        $nipdKey = trim((string)$nipd);
        $hadir = 0;
        for ($i = 1; $i <= 16; $i++) {
            $idj = isset($pertemuanJournals[$i]) ? $pertemuanJournals[$i] : '';
            if ($idj != '') {
                // Flexible checking (string or int) for BOTH Journal ID and Student ID/NIM
                $isPresent = false;
                
                // Check raw JID
                if (isset($attendanceMap[$idj][$nipdKey])) $isPresent = true;
                else {
                     $nipdInt = (string) intval($nipdKey);
                     if (isset($attendanceMap[$idj][$nipdInt])) $isPresent = true;
                }
                
                // Check int-normalized JID (if raw failed), but ONLY if safe/numeric
                if (!$isPresent && preg_match('/^[0-9]+$/', $idj) && strlen($idj) < 19) {
                    $idjInt = (string) intval($idj);
                    if ($idjInt !== $idj) {
                        if (isset($attendanceMap[$idjInt][$nipdKey])) $isPresent = true;
                        else {
                             $nipdInt = (string) intval($nipdKey);
                             if (isset($attendanceMap[$idjInt][$nipdInt])) $isPresent = true;
                        }
                    }
                }

                if ($isPresent) {
                    $studentRows .= "<td style='text-align:center;'>&radic;</td>";
                    $hadir++;
                } else {
                    $studentRows .= "<td style='text-align:center; color:red;'>X</td>";
                }
            } else {
                $studentRows .= "<td style='text-align:center;'>-</td>";
            }
        }

        // Calculate percentage using presentCount (same as web view)
        $activePertemuan = count($pertemuanData);
        $cnt = 0;
        if (isset($presentCount[$nipdKey])) {
            $cnt = $presentCount[$nipdKey];
        } else {
            $nipdInt = (string) intval($nipdKey);
            if (isset($presentCount[$nipdInt])) $cnt = $presentCount[$nipdInt];
        }
        $persen = $activePertemuan > 0 ? number_format(($cnt / $activePertemuan) * 100, 2) : 0;

        $studentRows .= "<td style='text-align:center;'>$cnt/$activePertemuan</td>";
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