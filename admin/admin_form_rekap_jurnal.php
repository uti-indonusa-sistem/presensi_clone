<?php if($_COOKIE['simpreskul_admin']==''){header('Location: ' . $base_url . '/admin/login.php');} ?>
<div style="float:left; width:100%;" >
		<form action="cetak_rekap_jurnal.html" method=POST>
		<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
		
			<td><b>Bulan</b></td><td>
				<select name="bulan" class="form-control" style="width:150px;">
					<option value="2019-07">Juli 2019</option>
					<option value="2019-08">Agustus 2019</option>
					<option value="2019-09">September 2019</option>
					<option value="2019-10">Oktober 2019</option>
					<option value="2019-11">Nopember 2019</option>
					<option value="2019-12">Desember 2019</option>
					<option value="2020-01">Januari 2020</option>
					<option value="2020-02">Feruari 2020</option>
					<option value="2020-03">Maret 2020</option>
					<option value="2020-04">April 2020</option>
					<option value="2020-05">Mei 2020</option>
					<option value="2020-06">Juni 2020</option>
					<option value="2020-07">Juli 2020</option>
					<option value="2020-08">Agustus 2020</option>
					<option value="2020-09">September 2020</option>
					<option value="2020-10">Oktober 2020</option>
					<option value="2020-11">Nopember 2020</option>
					<option value="2020-12">Desember 2020</option>
				</select>
			</td>
			
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Cetak" class="btn btn-default"></td>
		</tr>
		
		<tr>
		
		
		
		</form>
	</div>