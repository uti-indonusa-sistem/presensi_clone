<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../config/Security.php';
requireAdmin();
?>
<div style="float:left; width:100%;">
	<form action="<?php echo $base_url; ?>/cetak_rekap_presensi.html" method="POST" target="_blank">
		<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
			<tr>
				<td style="width:12%;"><b>
						<font style="font-size:10pt;">Program Studi</font>
					</b></td>
				<td style="width:25%;">
					<font style="font-size:10pt;">
						<select name="prodi" class="form-control">
							<?php
							$sqlProdi = mysqli_query($connection, "SELECT xid_sms, nm_lemb FROM wsia_sms ORDER BY xid_sms DESC");
							while ($dataProdi = mysqli_fetch_array($sqlProdi)) {
								echo "<option value=$dataProdi[xid_sms]>$dataProdi[nm_lemb]</option>";

							}
							?>


						</select>


				</td>
				<td style="width:12%;">
					<font style="font-size:10pt;">
						<font style="font-size:10pt;"><b>Tahun Akademik</b>
				</td>
				<td style="width:25%;">
					<select name="tahun_akademik" class="form-control" style="width:180px;">
						<?php $sql = mysqli_query($connection, "SELECT id_smt,nm_smt FROM wsia_semester WHERE id_smt >= 20201 ORDER BY id_smt DESC");
						echo "<option value=''></option>";
						while ($data = mysqli_fetch_array($sql)) {
							?>
							<option value="<?php echo $data['id_smt']; ?>"><?php echo $data['nm_smt']; ?></option>
						<?php } ?>
					</select>

				</td>

			</tr>
			<tr>
				<td colspan="2"></td>


				<td colspan="2">
					<font style="font-size:10pt;"><input type="submit" value="Cari" class="btn btn-default">
				</td>

			</tr>
		</table>
	</form>
</div>