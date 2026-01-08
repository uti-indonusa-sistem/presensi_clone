</table><table class="table table-striped table-bordered table-hover" id="dataTables-example">
		<tr valign="top">
			<td width="20%">
			Bukti Dokumen Pembelajaran
			</td>
			<td>
				<form method="POST" action="dosen_form_bukti_pembelajaran-<?php echo $_GET['id_jurnal']?>.html" enctype="multipart/form-data">
				<table>
				<tr>
				<td>
				<input type='file' name='filedokumen'>
				<input type='text' name='id_jurnal' value="<?php echo $_GET['id_jurnal']?>" hidden>
				</td>
				</tr>
				<tr >
				<td style='padding-top:10px'>
				<input type="submit" class="btn btn-warning" value="Simpan" name='btn-simpan' style='width:72px'>
				<input type="submit" class="btn btn-danger" value="Batal" name="btn-cancel" style='width:72px'>
				</td>
				</tr>
				</table>
				</form>
			</td>
		</tr>
</table>