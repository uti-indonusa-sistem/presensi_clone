<?php if($_COOKIE['simpreskul_admin']==''){header('Location: ' . $base_url . '/admin/login.php');} ?>
<div style="float:left; width:100%;" >
		<form action="cetak_rekap_kehadiran.html" method=POST>
		<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
		<tr>
			<td style="width:12%;"><b><font style="font-size:10pt;">Program Studi</font></b></td>
			<td  style="width:25%;"><font style="font-size:10pt;">
				<select name="prodi" class="form-control">
					<option value="all">Semua Program Studi</option>
					<option value="A">Mesin Otomotif</option>
					<option value="B">Manajemen Informatika</option>
					<option value="C">Komunikasi Massa</option>
					<option value="D">Perhotelan</option>
					<option value="E">Farmasi</option>
					<option value="F">Manajemen Informasi Kesehatan</option>
				</select>

			
			</td>
			<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>Tanggal</b></td>
			<td  style="width:25%;">
				<input name="tanggalAwal" type="date" class="form-control" style="width:150px;" required> sampai dengan
				<input name="tanggalAkhir" type="date" class="form-control" style="width:150px;" required>
					
			</td>
			
		</tr>
		<tr>
			<td></td>
			<td>
				
			</td>
			
			<td colspan="2"><font style="font-size:10pt;"><input type="submit" value="Cari" class="btn btn-default"></td>
			
		</tr>
		</table>
		</form>
	</div>