<?php ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_COOKIE['simpreskul_nik']==''){header("Location:login_dosen.html");}

if(isset($_POST['simpan_presensi'])){
	// Sanitize input
	$id_kelas_raw = str_replace("_yz_","-",$_GET['id_kelas']);
	$id_ptk_format = str_replace("_yz_","-",$_GET['id_ptk']);
	
	try {
		// Load semua mahasiswa
		$sqlMahasiswa = mysqli_query($connection,"SELECT viewNilai.*,wsia_mahasiswa_pt.*,wsia_mahasiswa.nm_pd FROM viewNilai 
												RIGHT JOIN wsia_mahasiswa_pt ON viewNilai.xid_reg_pd=wsia_mahasiswa_pt.xid_reg_pd
												LEFT JOIN wsia_mahasiswa ON wsia_mahasiswa_pt.id_pd=wsia_mahasiswa.xid_pd
												WHERE viewNilai.vid_kls='".$id_kelas_raw."' ORDER BY wsia_mahasiswa_pt.nipd ASC");
		
		if(!$sqlMahasiswa) {
			throw new Exception("Error loading mahasiswa: " . mysqli_error($connection));
		}
		
		// Load semua jurnal sekali saja (batch loading)
		$cek_kls = cek_gabungan($_GET['id_kelas']);
		if(empty($cek_kls)) $cek_kls = "presensi_jurnal_perkuliahan.xid_kls='".$id_kelas_raw."'";
		
		$sqlJurnalAll = mysqli_query($connection,"SELECT pertemuan_ke, id_jurnal FROM presensi_jurnal_perkuliahan 
									WHERE (".$cek_kls.") 
									AND id_ptk='".$id_ptk_format."' 
									AND pertemuan_ke BETWEEN 1 AND 16
									ORDER BY pertemuan_ke ASC");
		
		if(!$sqlJurnalAll) {
			throw new Exception("Error loading jurnal: " . mysqli_error($connection));
		}
		
		// Cache jurnal data
		$jurnalCache = array();
		while($row = mysqli_fetch_array($sqlJurnalAll)) {
			$jurnalCache[$row['pertemuan_ke']] = trim((string)$row['id_jurnal']); // Normalize to string
		}
		
		// Load semua existing presensi (batch loading)
		$sqlExistingPresensi = mysqli_query($connection,"SELECT nim, id_jurnal FROM presensi_rekap 
													WHERE id_ptk='".$id_ptk_format."' 
													AND id_jurnal IN (".implode(",", array_values($jurnalCache)).")");
		
		if(!$sqlExistingPresensi) {
			throw new Exception("Error loading existing presensi: " . mysqli_error($connection));
		}
		
		// Cache existing presensi: key = nim_id_jurnal
		$existingPresensi = array();
		while($row = mysqli_fetch_array($sqlExistingPresensi)) {
			$key = $row['nim'] . '_' . $row['id_jurnal'];
			$existingPresensi[$key] = true;
		}
		
		$successInsert = 0;
		$successDelete = 0;
		$skipped = 0;
		$errorCount = 0;
		$errors = array();
		$debugDeleteLog = array(); // Track delete operations
		
		// Process setiap mahasiswa
		while($dataMahasiswa = mysqli_fetch_array($sqlMahasiswa)) {
			$nim = $dataMahasiswa['nipd'];
			
			// Process setiap pertemuan
			for($pertemuanKe = 1; $pertemuanKe <= 16; $pertemuanKe++) {
				// Cek apakah jurnal ada untuk pertemuan ini
				if(!isset($jurnalCache[$pertemuanKe]) || empty($jurnalCache[$pertemuanKe])) {
					continue;
				}
				
				$id_jurnal = $jurnalCache[$pertemuanKe];
				$postKey = $pertemuanKe . '-' . $nim;
				$isPresent = isset($_POST[$postKey]) && $_POST[$postKey] == 'on';
				$presKey = $nim . '_' . $id_jurnal;
				$exists = isset($existingPresensi[$presKey]);
				
				// Logic: Only update if needed
				if($isPresent && !$exists) {
					// Need to INSERT
					$sqlInsert = "INSERT INTO presensi_rekap(nim, id_jurnal, id_ptk) 
								 VALUES('".$nim."', '".$id_jurnal."', '".$id_ptk_format."')";
					
					$resultInsert = mysqli_query($connection, $sqlInsert);
					if(!$resultInsert) {
						$errorCount++;
						$errors[] = "Error inserting untuk NIM $nim pertemuan $pertemuanKe: " . mysqli_error($connection);
					} else {
						$successInsert++;
					}
				} 
				else if(!$isPresent && $exists) {
					// Need to DELETE
					$sqlDelete = "DELETE FROM presensi_rekap 
								 WHERE nim='".$nim."' 
								 AND id_jurnal='".$id_jurnal."' 
								 AND id_ptk='".$id_ptk_format."'";
					
					$resultDelete = mysqli_query($connection, $sqlDelete);
					if(!$resultDelete) {
						$errorCount++;
						$errors[] = "Error deleting untuk NIM $nim pertemuan $pertemuanKe (id_jurnal=$id_jurnal): " . mysqli_error($connection);
						$debugDeleteLog[] = "FAILED: nim=$nim, pertemuan=$pertemuanKe, id_jurnal=$id_jurnal, id_ptk=$id_ptk_format";
					} else {
						$successDelete++;
						$debugDeleteLog[] = "SUCCESS: nim=$nim, pertemuan=$pertemuanKe, id_jurnal=$id_jurnal";
					}
				}
				else {
					// Data sama dengan yang ada di database, SKIP
					$skipped++;
				}
			}
		}
		
		// Show result
		echo "<div style='background:#d4edda; border:1px solid #c3e6cb; padding:15px; margin:15px 0; border-radius:5px;'>";
		echo "<h4 style='color:#155724; margin-top:0;'>✓ Data Berhasil Disimpan!</h4>";
		echo "<p><strong>Summary:</strong></p>";
		echo "<ul>";
		echo "<li>Presensi ditambahkan: <strong>$successInsert</strong></li>";
		echo "<li>Presensi dihapus: <strong>$successDelete</strong></li>";
		echo "<li>Data tidak berubah (dilewati): <strong>$skipped</strong></li>";
		echo "<li>Error: <strong>$errorCount</strong></li>";
		echo "</ul>";
		
		if(!empty($errors) && $errorCount > 0) {
			echo "<p style='color:#721c24;'><strong>Error Details:</strong></p>";
			echo "<pre style='background:#f8d7da; padding:10px; border-radius:3px; overflow-x:auto;'>";
			foreach($errors as $error) {
				echo htmlspecialchars($error) . "\n";
			}
			echo "</pre>";
		}
		
		echo "</div>";
		
		if(!empty($debugDeleteLog)) {
			echo "<div style='background:#e7f3ff; border:1px solid #b3d9ff; padding:10px; margin:10px 0; border-radius:5px; font-size:11px;'>";
			echo "<strong>Delete Log:</strong><br>";
			foreach($debugDeleteLog as $log) {
				echo htmlspecialchars($log) . "<br>";
			}
			echo "</div>";
		}
		
		// Auto-redirect ke halaman presensi dengan timestamp untuk force fresh load
		$timestamp = time();
		$redirectUrl = 'dosen_data_kehadiran-'.str_replace("-","_yz_",$_GET['id_kelas']).'-'.str_replace("-","_yz_",$_COOKIE['simpreskul_id_ptk']).'.html?t='.$timestamp;
		
		echo "<div style='margin-top:20px;'>";
		echo "<p><strong>Redirecting ke halaman presensi dalam 2 detik...</strong></p>";
		echo "<script>";
		echo "setTimeout(function() { window.location.href = '".$redirectUrl."'; }, 2000);";
		echo "</script>";
		echo "<a href='".$redirectUrl."' class='btn btn-primary'>Klik di sini jika tidak otomatis redirect</a> &nbsp; ";
		echo "<a href='dosen_jurnal_perkuliahan-".str_replace("-","_yz_",$_GET['id_kelas'])."-".str_replace("-","_yz_",$_GET['id_ptk']).".html' class='btn btn-secondary'>Lanjut ke Jurnal Perkuliahan</a>";
		echo "</div>";
		
	} catch(Exception $e) {
		echo "<div style='background:#f8d7da; border:1px solid #f5c6cb; padding:15px; margin:15px 0; border-radius:5px;'>";
		echo "<h4 style='color:#721c24; margin-top:0;'>✗ Error!</h4>";
		echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
		echo "</div>";
	}
}
?>