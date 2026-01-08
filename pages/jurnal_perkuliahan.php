<?php if($_COOKIE['simpreskul_nik']==''){header("Location:login_dosen.html");} ?>
<div style="float:left; width:100%;" >
		
		<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
		<tr>
			<td style="width:12%;"><b><font style="font-size:10pt;">Nama Dosen</font></b></td>
			<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_jadwal($_GET['id_jadwal'],"nama_dosen"); ?></td>
			<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>Tahun Akademik</b></td>
			<td  style="width:25%;"><?php echo view_tahun_akademik(); ?></td>
			
		</tr>
		<tr>
			<td><font style="font-size:10pt;"><b>Mata Kuliah</b></td>
			<td><font style="font-size:10pt;"><?php echo view_jadwal($_GET['id_jadwal'],"nm_matkul") ?></td>
			
			<td><font style="font-size:10pt;"><b>Kelas</b></td>
			<td><?php echo view_jadwal($_GET['id_jadwal'],"kelas"); ?></td>
		</tr>
		
		</table>
	</div>



	<table class="table table-striped table-bordered table-hover" id="dataTables-example">
		<tr valign="top">
			<td width="15%">
				Pertemuan Ke
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
		</tr>
		<?php for($i=1;$i<=14;$i++){ ?>
		<tr  valign="top">
			<td>
			<?php echo $i; ?>
			</td>
			
			<td>
				<?php 
					$sql=mysql_query("SELECT*,
									presensi_jurnal.tanggal FROM presensi_jurnal WHERE 
									thn_akademik='".view_tahun_akademik()."'
									AND matkul='".view_jadwal($_GET['id_jadwal'],"matkul")."'
									AND kelas='".view_jadwal($_GET['id_jadwal'],"kelas")."'
									AND prodi='".view_jadwal($_GET['id_jadwal'],"kd_prodi")."'
									AND pertemuan_ke='".$i."'
									");
					
					
				
					
					$data=mysql_fetch_array($sql);
					
				?>
				<form action="proses_simpan_materi-<?php echo $_GET['id_jadwal']; ?>-<?php echo $i; ?>.html" method="POST">
				<textarea class="form-control" name="materi"><?php echo $data["materi"]; ?></textarea>
				
			</td>
			<td>
				<?php echo $data['tanggal']; ?>
			</td>
			<td>
				<?php echo $data['ruang']; ?>
			</td>
			<td>
				<input type="submit" class="btn btn-warning" value="Lanjut">
				</form>
			</td>
		</tr>
		
		<?php } ?>
		
	</table>

	