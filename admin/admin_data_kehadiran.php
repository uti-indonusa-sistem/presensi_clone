<?php
require_once __DIR__ . '/../koneksi.php';
// Authentication check 
if (($_COOKIE['simpreskul_nik'] == '') AND ($_COOKIE['simpreskul_admin'] == '')) {
	header("Location:login_dosen.html");
}

// Debug helper
$debug_enabled = 0;

// Variables
$id_kls_raw = $_GET['id_kelas'];
$id_ptk_raw = $_GET['id_ptk'];
$id_kls = str_replace("_yz_", "-", $id_kls_raw);
$id_ptk = str_replace("_yz_", "-", $id_ptk_raw);
?>

<style>
	.btn-check-all {
		padding: 5px 10px;
		margin: 2px;
		font-size: 11px;
		background-color: #5cb85c;
		color: white;
		border: none;
		border-radius: 3px;
		cursor: pointer;
	}

	.btn-check-all:hover {
		background-color: #4cae4c;
	}

	.btn-uncheck-all {
		padding: 5px 10px;
		margin: 2px;
		font-size: 11px;
		background-color: #d9534f;
		color: white;
		border: none;
		border-radius: 3px;
		cursor: pointer;
	}

	.btn-uncheck-all:hover {
		background-color: #c9302c;
	}
</style>

<script>
	function checkAll(columnClass, checkIt) {
		var inputs = document.querySelectorAll('.' + columnClass);
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].type == 'checkbox') {
				inputs[i].checked = checkIt;
			}
		}
	}

	function checkAllStudents(checkIt) {
		var inputs = document.querySelectorAll('input[type="checkbox"]');
		for (var i = 0; i < inputs.length; i++) {
			inputs[i].checked = checkIt;
		}
	}

	// Clear form data on page load to prevent browser form restoration
	window.addEventListener('load', function () {
		var allInputs = document.querySelectorAll('input[type="checkbox"]');
		for (var i = 0; i < allInputs.length; i++) {
			// Only uncheck if not checked by default (HTML attribute)
			if (!allInputs[i].hasAttribute('checked')) {
				allInputs[i].checked = false;
			}
		}
	});
</script>

<form action="admin_input_kehadiran-<?php echo $_GET['id_kelas'] ?>-<?php echo $_GET['id_ptk'] ?>.html" method="POST"
	id="selectForm">

	<div style="margin-bottom: 15px;">
		<button type="button" class="btn-check-all" onclick="checkAllStudents(true)">✓ Pilih Semua</button>
		<button type="button" class="btn-uncheck-all" onclick="checkAllStudents(false)">✗ Hapus Semua</button>
		<input type="submit" class="btn btn-primary" value="Simpan Presensi" name="simpan_presensi"
			style="margin-left:10px;">
	</div>

	<table border="1" style="border-color:#DDDDDD; border:1px solid #DDDDDD">
		<thead>
			<tr>
				<th width="1%" rowspan="3" style="vertical-align:center">
					<center>No</center>
				</th>
				<th width="2%" rowspan="3">
					<center>NIM</center>
				</th>
				<th width="20%" rowspan="3">
					<center>Nama</center>
				</th>
				<?php
				// Preload pertemuan journals and dates (Optimized from Dosen)
				$pertemuanJournals = array();
				$pertemuanDates = array();

				$cek_kls = cek_gabungan($_GET['id_kelas']);
				if (empty($cek_kls)) {
					$cek_kls = "presensi_jurnal_perkuliahan.xid_kls='" . $id_kls . "'";
				}

				$sqlPertemuanStr = "SELECT presensi_jurnal_perkuliahan.pertemuan_ke, presensi_jurnal_perkuliahan.id_jurnal, DATE_FORMAT(presensi_jurnal_perkuliahan.tanggal,'%d-%m-%Y') AS tanggal FROM presensi_jurnal_perkuliahan WHERE (" . $cek_kls . ") AND id_ptk='" . $id_ptk . "' AND presensi_jurnal_perkuliahan.pertemuan_ke BETWEEN 1 AND 16 ORDER BY pertemuan_ke ASC";

				$sqlPertemuan = mysqli_query($connection, $sqlPertemuanStr);
				if ($sqlPertemuan) {
					while ($rP = mysqli_fetch_array($sqlPertemuan)) {
						$k = (int) $rP['pertemuan_ke'];
						$pertemuanJournals[$k] = trim((string) $rP['id_jurnal']);
						$pertemuanDates[$k] = $rP['tanggal'];
					}
				}

				for ($i = 1; $i <= 16; $i++) {
					$dt = isset($pertemuanDates[$i]) ? $pertemuanDates[$i] : '';
					?>
					<th width="3%" style="transform: rotate(-45deg); vertical-align:center">
						<font style="font-size:12px;">
							<center><?php echo $dt; ?></center>
						</font>
					</th>
				<?php } ?>
				<th rowspan="3" width="1%" style="transform: rotate(-45deg); vertical-align:center">
					<center>
						<font style="font-size:7pt">Presentase</font><br>%
					</center>
				</th>
			</tr>
			<tr>
				<?php for ($i = 1; $i <= 16; $i++) { ?>
					<th width="3%">
						<center><?php echo "<font style='font-size:12px'><center>$i</center></font>"; ?></center>
					</th>
				<?php } ?>
			</tr>
			<tr>
				<?php
				// Preload attendance (Optimized from Dosen)
				$allJurnalIds = array_values($pertemuanJournals);
				if (empty($allJurnalIds)) {
					$allJurnalIds = array(0);
				}
				$idsList = implode(',', array_map('intval', $allJurnalIds));
				$attendance = array();
				$presentCount = array();

				if (!empty($allJurnalIds) && $allJurnalIds[0] != 0) {
					$sqlAttend = mysqli_query($connection, "SELECT nim, id_jurnal FROM presensi_rekap WHERE id_jurnal IN (" . $idsList . ") AND id_ptk='" . $id_ptk . "'");
					if ($sqlAttend)
						while ($a = mysqli_fetch_array($sqlAttend)) {
							$jid = isset($a['id_jurnal']) ? trim((string) $a['id_jurnal']) : '';
							$nimRaw = isset($a['nim']) ? trim((string) $a['nim']) : '';
							if ($jid === '' || $nimRaw === '')
								continue;

							$attendance[$jid][$nimRaw] = true;
							// Also support int-like matching if needed (from Dosen logic)
							$nimInt = (string) intval($nimRaw);
							if ($nimInt !== $nimRaw)
								$attendance[$jid][$nimInt] = true;

							if (!isset($presentCount[$nimRaw]))
								$presentCount[$nimRaw] = 0;
							$presentCount[$nimRaw]++;
						}
				}

				$jumlahAllPertemuan = count(array_filter($pertemuanJournals));

				$class = "A";
				for ($t = 1; $t <= 16; $t++) {
					$class++;
					$idj = isset($pertemuanJournals[$t]) ? $pertemuanJournals[$t] : '';
					if ($idj != '') {
						echo "<td style='padding:5px;'><center>";
						echo "<button type='button' class='btn-check-all' onclick=\"checkAll('{$class}', true); return false;\" title='Pilih semua'>✓</button>";
						echo "<button type='button' class='btn-uncheck-all' onclick=\"checkAll('{$class}', false); return false;\" title='Hapus semua'>✗</button>";
						echo "</center></td>";
					} else {
						echo "<td></td>";
					}
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			// Query Students - Exact Dosen Implementation without filters
			$queryMhs = "SELECT viewNilai.*,wsia_mahasiswa_pt.*,wsia_mahasiswa.nm_pd FROM viewNilai 
        RIGHT JOIN wsia_mahasiswa_pt ON viewNilai.xid_reg_pd=wsia_mahasiswa_pt.xid_reg_pd
        LEFT JOIN wsia_mahasiswa ON wsia_mahasiswa_pt.id_pd=wsia_mahasiswa.xid_pd
        WHERE viewNilai.vid_kls='$id_kls' ORDER BY wsia_mahasiswa_pt.nipd ASC";

			$sql = mysqli_query($connection, $queryMhs);

			$no = 0;
			if ($sql)
				while ($data = mysqli_fetch_array($sql)) {
					$no++;
					?>
					<tr style='background-color:white;'>
						<td><?php echo "<font style='font-size:12px'><center>$no</center></font>" ?></td>
						<td><?php echo "<font style='font-size:12px'><center>$data[nipd]</center></font>" ?></td>
						<td><?php echo "<font style='font-size:12px; padding-left:2px'>$data[nm_pd]</font>" ?></td>
						<?php
						$class = "A";
						for ($i = 1; $i <= 16; $i++) {
							$class++;
							$idj = isset($pertemuanJournals[$i]) ? $pertemuanJournals[$i] : '';
							?>
							<td>
								<center>
									<?php
									if ($idj != '') {
										$studentKey = trim((string) $data['nipd']);
										$checked = '';

										if (isset($attendance[$idj]) && !empty($attendance[$idj])) {
											if (isset($attendance[$idj][$studentKey]) && $attendance[$idj][$studentKey]) {
												$checked = 'checked';
											}
										}
										?>
										<input type="checkbox" class="<?php echo $class; ?>" name="<?php echo $i . "-" . $studentKey; ?>"
											value="on" <?php echo $checked; ?> />
									<?php } ?>
								</center>
							</td>
						<?php
						}

						// Calculate Percentage
						$studentKey = trim((string) $data['nipd']);
						$cnt = 0;
						if ($studentKey !== '' && isset($presentCount[$studentKey]))
							$cnt = $presentCount[$studentKey];
						else {
							$studentKeyInt = (string) intval($studentKey);
							if (isset($presentCount[$studentKeyInt]))
								$cnt = $presentCount[$studentKeyInt];
						}
						$pct = ($jumlahAllPertemuan) ? number_format((($cnt / $jumlahAllPertemuan) * 100), 2) . " &nbsp" : "";
						?>
						<td style="text-align:right;">
							<font style="font-size:12px;"><?php echo $pct; ?></font>
						</td>
					</tr>
				<?php } ?>
		</tbody>
	</table>
	<br>
	<input type="submit" class="btn btn-primary" value="Simpan Presensi" name="simpan_presensi"
		style="margin-top:10px;">
</form>