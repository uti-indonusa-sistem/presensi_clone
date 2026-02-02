<?php if ($_COOKIE['simpreskul_nik'] == '') {
	header("Location:login_dosen.html");
} ?>
<div style="float:left; width:100%;">
	<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
		<tr>
			<td style="width:12%;"><b>
					<font style="font-size:10pt;">Nama Dosen</font>
				</b></td>
			<td style="width:25%;">
				<font style="font-size:10pt;"><?php echo view_kelas($_GET['id_kelas'], $_GET['id_ptk'], "nama_dosen"); ?>
			</td>
			<td style="width:12%;">
				<font style="font-size:10pt;">
					<font style="font-size:10pt;"><b>Tahun Akademik</b>
			</td>
			<td style="width:25%;"><?php echo view_kelas($_GET['id_kelas'], $_GET['id_ptk'], "tahun_akademik"); ?></td>

		</tr>
		<tr>
			<td>
				<font style="font-size:10pt;"><b>Mata Kuliah</b>
			</td>
			<td>
				<font style="font-size:10pt;"><?php echo view_kelas($_GET['id_kelas'], $_GET['id_ptk'], "nm_matkul") ?>
			</td>

			<td>
				<font style="font-size:10pt;"><b>Program Studi / Kelas</b>
			</td>
			<td><?php echo view_kelas($_GET['id_kelas'], $_GET['id_ptk'], "prodi-kelas"); ?></td>
		</tr>

	</table>
</div>



<table class="table table-striped table-bordered table-hover" id="dataTables-example">
	<tr valign="top">
		<td width="5%">
			Perte- muan Ke
		</td>

		<td>
			Materi Perkuliahan
		</td>
		<td>Tanggal</td>
		<td>
			Ruang
		</td>
		<td>

		</td>
		<td>

		</td>
	</tr>
	<?php for ($i = 1; $i <= 16; $i++) { ?>

		<tr valign="top">
			<td>
				<?php echo $i; ?>
			</td>

			<td>
				<?php
				$id_kelas = isset($_GET['id_kelas']) ? str_replace("_yz_", "-", $_GET['id_kelas']) : '';
				$id_ptk = isset($_GET['id_ptk']) ? str_replace("_yz_", "-", $_GET['id_ptk']) : '';

				if (empty($id_kelas) || empty($id_ptk)) {
					echo "Kelas atau PTK tidak ditemukan";
				} else {
					$sqlKelas = mysqli_query($connection, "SELECT*FROM viewKelasKuliah WHERE xid_kls='" . $id_kelas . "' AND id_ptk='" . $id_ptk . "'");
					$dataKelas = mysqli_fetch_array($sqlKelas);

					if (!$dataKelas) {
						echo "Data kelas tidak ditemukan";
					} else {
						$sqlJurnal = mysqli_query($connection, "SELECT*FROM presensi_jurnal_perkuliahan WHERE xid_kls='" . $id_kelas . "' AND id_ptk='" . $dataKelas['id_ptk'] . "' AND pertemuan_ke='" . $i . "'");
						$dataJurnal = null;
						if ($sqlJurnal && is_object($sqlJurnal)) {
							$dataJurnal = mysqli_fetch_array($sqlJurnal);
						}
						$idJurnal = $dataJurnal['id_jurnal'] ?? '';
						if ($idJurnal == '') {
							$idJurnal = 'x';
						}

						if ($dataJurnal && (($dataJurnal['tanggal'] <= $simpreskulV2_dateline) AND ($dataJurnal['tanggal'] >= '2015-09-30'))) {
							$statusdisabled = "disabled";
						} else {
							$statusdisabled = "enabled";
						}
						?>

						<form id="form_materi_<?php echo $i; ?>"
							action="dosen_proses_simpan_materi-<?php echo str_replace("-", "_yz_", $id_kelas); ?>-<?php echo str_replace("-", "_yz_", $id_ptk); ?>-<?php echo $i; ?>-<?php echo $idJurnal; ?>.html"
							method="POST"></form>

						<input form="form_materi_<?php echo $i; ?>" type="radio" required name="kegiatan" <?php echo $statusdisabled; ?> value="1" <?php if ($dataJurnal && $dataJurnal['kegiatan'] == '1') {
								   echo "checked";
							   } ?>>
						Perkuliahan &nbsp </input>
						<input form="form_materi_<?php echo $i; ?>" type="radio" required name="kegiatan" <?php echo $statusdisabled; ?> value="2" <?php if ($dataJurnal && $dataJurnal['kegiatan'] == '2') {
								   echo "checked";
							   } ?>>
						UTS &nbsp </input>
						<input form="form_materi_<?php echo $i; ?>" type="radio" required name="kegiatan" <?php echo $statusdisabled; ?> value="3" <?php if ($dataJurnal && $dataJurnal['kegiatan'] == '3') {
								   echo "checked";
							   } ?>>
						UAS &nbsp </input>

						<textarea form="form_materi_<?php echo $i; ?>" class="form-control" name="materi" required
							style="width:400px; height:40px" <?php echo $statusdisabled; ?>><?php echo ($dataJurnal ? $dataJurnal["materi"] : ""); ?></textarea>
					</td>

					<td>
						<input form="form_materi_<?php echo $i; ?>" type="date" name="tanggal" required
							value="<?php echo ($dataJurnal ? $dataJurnal['tanggal'] : ''); ?>" class="form-control"
							style="width:160px;" <?php echo $statusdisabled; ?>></input>
					</td>
					<td>
						<select form="form_materi_<?php echo $i; ?>" name="ruang" required class="form-control" style="width:160px;"
							<?php echo $statusdisabled; ?>>
							<option value="<?php echo ($dataJurnal ? $dataJurnal['ruang'] : ''); ?>">
								<?php echo ($dataJurnal ? $dataJurnal['ruang'] : ''); ?></option>
							<?php
							$sqlRuang = mysqli_query($connection, "SELECT ruang FROM presensi_ruang ORDER BY id_ruang DESC");
							while ($dataRuang = mysqli_fetch_array($sqlRuang)) {
								?>
								<option value="<?php echo $dataRuang['ruang']; ?>"><?php echo $dataRuang['ruang']; ?></option>
							<?php } ?>
						</select>
					</td>

					<td>
						<?php if ($dataJurnal && $dataJurnal['id_jurnal'] != '') { ?>
							<input form="form_materi_<?php echo $i; ?>" type="submit" class="btn btn-warning" value="Simpan"
								name="lanjut" <?php echo $statusdisabled; ?>>
						<?php } else { ?>
							<input form="form_materi_<?php echo $i; ?>" type="submit" class="btn btn-warning" value="Simpan"
								name="lanjut">
						<?php } ?>
					</td>
					<td>
						<?php if ($dataJurnal && $dataJurnal['id_jurnal'] != '') { ?>
							<input form="form_materi_<?php echo $i; ?>" type="submit" class="btn btn-danger" value="Hapus" name="hapus"
								<?php echo $statusdisabled; ?>>
						<?php } ?>
					</td>
					<?php
					}
				}
				?>
		</tr>

	<?php } ?>

</table>