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

// Get safe parameters
$id_kelas = str_replace("_yz_", "-", $_POST['id_kelas'] ?? $_GET['id_kelas'] ?? '');
$id_ptk = str_replace("_yz_", "-", $_POST['id_ptk'] ?? $_GET['id_ptk'] ?? '');

// Fallback to cookie if id_ptk not in POST or GET
if (empty($id_ptk)) {
    $id_ptk = getSafeCookie('simpreskul_id_ptk', '');
}

// Validate parameters exist
if (empty($id_kelas) || empty($id_ptk)) {
    die('Missing required parameters (Kelas/PTK)');
}

// PREPARED STATEMENT - Get kelas info safely (common for all meetings)
$query = "SELECT kode_mk, id_ptk, id_smt, id_sms, nm_kls, nm_mk FROM viewKelasKuliah WHERE xid_kls = ? AND id_ptk = ?";
$stmt = $connection->prepare($query);
if (!$stmt) {
    die('Database error: Prepare failed');
}
$stmt->bind_param("ss", $id_kelas, $id_ptk);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    die('Kelas tidak ditemukan');
}
$dataKelasInfo = $result->fetch_assoc();
$stmt->close();

$kode_mk = $dataKelasInfo['kode_mk'];
$id_smt = $dataKelasInfo['id_smt'];
$id_prodi = $dataKelasInfo['id_sms'];
$nm_kls = $dataKelasInfo['nm_kls'];
$nama_makul = $dataKelasInfo['nm_mk'] ?? '';

$saved_count = 0;
$updated_count = 0;
$skipped_count = 0;

// Get arrays from POST
$materis = $_POST['materi'] ?? [];
$tanggals = $_POST['tanggal'] ?? [];
$kegiatans = $_POST['kegiatan'] ?? [];
$ruangs = $_POST['ruang'] ?? [];

for ($i = 1; $i <= 16; $i++) {
    $materi = trim($materis[$i] ?? '');
    $tanggal = trim($tanggals[$i] ?? '');
    $kegiatan = trim($kegiatans[$i] ?? '');
    $ruang = trim($ruangs[$i] ?? '');

    // Skip if no data for this meeting
    if (empty($materi) && empty($tanggal)) {
        continue;
    }

    // Basic validation
    if (empty($materi) || empty($tanggal)) {
        // Skipping incomplete entries
        $skipped_count++;
        continue;
    }

    // Check dateline
    if ($tanggal <= $simpreskulV2_dateline) {
        // Skip if past deadline
        $skipped_count++;
        continue;
    }

    // Check if journal exists
    $cek_kls_sql = cek_gabungan($id_kelas);
    if (empty($cek_kls_sql)) {
        $queryCheck = "SELECT id_jurnal, tanggal FROM presensi_jurnal_perkuliahan WHERE xid_kls = ? AND pertemuan_ke = ? AND id_ptk = ?";
        $stmtCheck = $connection->prepare($queryCheck);
        $pertemuan_str = (string)$i;
        $stmtCheck->bind_param("sss", $id_kelas, $pertemuan_str, $id_ptk);
    } else {
        $queryCheck = "SELECT id_jurnal, tanggal FROM presensi_jurnal_perkuliahan WHERE " . $cek_kls_sql . " AND pertemuan_ke = ? AND id_ptk = ?";
        $stmtCheck = $connection->prepare($queryCheck);
        $pertemuan_str = (string)$i;
        $stmtCheck->bind_param("ss", $pertemuan_str, $id_ptk);
    }
    
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    
    if ($resultCheck && $row = $resultCheck->fetch_assoc()) {
        $id_jurnal = $row['id_jurnal'];
        $old_tanggal = $row['tanggal'];
        $stmtCheck->close();

        // Extra check for existing records: don't update if old record was past deadline
        if ($old_tanggal <= $simpreskulV2_dateline && $old_tanggal >= '2015-09-30') {
            $skipped_count++;
            continue;
        }

        // UPDATE
        $queryUpdate = "UPDATE presensi_jurnal_perkuliahan SET
            xid_kls = ?, tanggal = ?, kode_mk = ?, nama_makul = ?, materi = ?,
            id_ptk = ?, id_smt = ?, id_prodi = ?, nm_kls = ?, pertemuan_ke = ?,
            kegiatan = ?, ruang = ?, tanggal_input = NOW()
            WHERE id_jurnal = ?";
        $stmtUpdate = $connection->prepare($queryUpdate);
        $pertemuan_str = (string)$i;
        $stmtUpdate->bind_param("ssssssssssssi", 
            $id_kelas, $tanggal, $kode_mk, $nama_makul, $materi,
            $id_ptk, $id_smt, $id_prodi, $nm_kls, $pertemuan_str,
            $kegiatan, $ruang, $id_jurnal
        );
        if ($stmtUpdate->execute()) {
            $updated_count++;
        }
        $stmtUpdate->close();
    } else {
        $stmtCheck->close();
        // INSERT
        $queryInsert = "INSERT INTO presensi_jurnal_perkuliahan(
            xid_kls, tanggal, kode_mk, nama_makul, materi, id_ptk, id_smt, id_prodi,
            nm_kls, pertemuan_ke, kegiatan, ruang, tanggal_input
        ) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmtInsert = $connection->prepare($queryInsert);
        $pertemuan_str = (string)$i;
        $stmtInsert->bind_param("ssssssssssss", 
            $id_kelas, $tanggal, $kode_mk, $nama_makul, $materi,
            $id_ptk, $id_smt, $id_prodi, $nm_kls, $pertemuan_str,
            $kegiatan, $ruang
        );
        if ($stmtInsert->execute()) {
            $saved_count++;
        }
        $stmtInsert->close();
    }
}

// Success message
$safe_kelas = str_replace("-", "_yz_", $id_kelas);
$safe_ptk = str_replace("-", "_yz_", $id_ptk);

echo "<div class='alert alert-success'>";
echo "<h4>✅ Pemrosesan Selesai</h4>";
echo "<ul>";
if ($saved_count > 0) echo "<li>$saved_count data baru berhasil disimpan.</li>";
if ($updated_count > 0) echo "<li>$updated_count data berhasil diperbarui.</li>";
if ($skipped_count > 0) echo "<li>$skipped_count data dilewati (tidak lengkap atau melewati batas waktu).</li>";
if ($saved_count == 0 && $updated_count == 0 && $skipped_count == 0) echo "<li>Tidak ada data yang diproses.</li>";
echo "</ul>";
echo "</div>";

echo "<br>";
echo "<a href='dosen_jurnal_perkuliahan-" . htmlspecialchars($safe_kelas) . "-" . htmlspecialchars($safe_ptk) . ".html' class='btn btn-primary'>← Kembali ke Jurnal Perkuliahan</a> ";
echo "<a href='dosen_data_kehadiran-" . htmlspecialchars($safe_kelas) . "-" . htmlspecialchars($safe_ptk) . ".html' class='btn btn-default'>Lanjut ke Presensi Mahasiswa →</a>";

ob_end_flush();
exit;
