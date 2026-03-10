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



<form id="bulk_jurnal_form" action="dosen_proses_simpan_materi_bulk-<?php echo str_replace("-", "_yz_", $id_kelas); ?>-<?php echo str_replace("-", "_yz_", $id_ptk); ?>.html" method="POST">
	<input type="hidden" name="id_kelas" value="<?php echo str_replace("-", "_yz_", $id_kelas); ?>">
	<input type="hidden" name="id_ptk" value="<?php echo str_replace("-", "_yz_", $id_ptk); ?>">
</form>

<div style="margin-bottom: 15px; text-align: right;">
	<button form="bulk_jurnal_form" type="submit" class="btn btn-primary" style="font-weight: bold;">
		<i class="fa fa-save"></i> SIMPAN SEMUA JURNAL
	</button>
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
						$cek_kls = cek_gabungan($id_kelas);
						if (empty($cek_kls)) {
							$cek_kls = "presensi_jurnal_perkuliahan.xid_kls='" . $id_kelas . "'";
						}

						$sqlJurnal = mysqli_query($connection, "SELECT*FROM presensi_jurnal_perkuliahan WHERE " . $cek_kls . " AND id_ptk='" . $dataKelas['id_ptk'] . "' AND pertemuan_ke='" . $i . "'");
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

						<input form="bulk_jurnal_form" type="radio" name="kegiatan[<?php echo $i; ?>]" <?php echo $statusdisabled; ?> value="1" <?php if ($dataJurnal && $dataJurnal['kegiatan'] == '1') {
								   echo "checked";
							   } ?>>
						Perkuliahan &nbsp </input>
						<input form="bulk_jurnal_form" type="radio" name="kegiatan[<?php echo $i; ?>]" <?php echo $statusdisabled; ?> value="2" <?php if ($dataJurnal && $dataJurnal['kegiatan'] == '2') {
								   echo "checked";
							   } ?>>
						UTS &nbsp </input>
						<input form="bulk_jurnal_form" type="radio" name="kegiatan[<?php echo $i; ?>]" <?php echo $statusdisabled; ?> value="3" <?php if ($dataJurnal && $dataJurnal['kegiatan'] == '3') {
								   echo "checked";
							   } ?>>
						UAS &nbsp </input>

						<textarea form="bulk_jurnal_form" class="form-control" name="materi[<?php echo $i; ?>]" 
							style="width:400px; height:40px" <?php echo $statusdisabled; ?>><?php echo ($dataJurnal ? $dataJurnal["materi"] : ""); ?></textarea>
					</td>

					<td>
						<input form="bulk_jurnal_form" type="date" name="tanggal[<?php echo $i; ?>]" 
							value="<?php echo ($dataJurnal ? $dataJurnal['tanggal'] : ''); ?>" class="form-control"
							style="width:160px;" <?php echo $statusdisabled; ?>></input>
					</td>
					<td>
						<select form="bulk_jurnal_form" name="ruang[<?php echo $i; ?>]" class="form-control" style="width:160px;"
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
						<?php if ($statusdisabled != "disabled") { ?>
							<span class="text-success"><i class="fa fa-edit"></i> Siap simpan</span>
						<?php } else { ?>
							<span class="text-muted"><i class="fa fa-lock"></i> Terkunci</span>
						<?php } ?>
					</td>
					<td>
						<?php if ($dataJurnal && $dataJurnal['id_jurnal'] != '') { ?>
							<a href="dosen_hapus_jurnal-<?php echo $dataJurnal['id_jurnal']; ?>-<?php echo str_replace("-", "_yz_", $id_kelas); ?>-<?php echo str_replace("-", "_yz_", $id_ptk); ?>.html" 
							   class="btn btn-danger btn-xs" <?php echo ($statusdisabled == 'disabled' ? 'style="display:none;"' : ''); ?> onclick="return confirm('Hapus jurnal ini?')">Hapus</a>
						<?php } ?>
					</td>
					<?php
					}
				}
				?>
		</tr>

	<?php } ?>

</table>

<div style="margin-top: 20px; margin-bottom: 40px; text-align: center;">
	<button form="bulk_jurnal_form" type="submit" class="btn btn-primary btn-lg" style="padding: 10px 40px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
		<i class="fa fa-save"></i> SIMPAN SEMUA JURNAL PERKULIAHAN
	</button>
</div>