<?php if($_COOKIE['simpreskul_admin']==''){header("Location:login_dosen.html");} ?>
<div style="float:left; width:100%;" >
		<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
		<tr>
			<td style="width:12%;"><b><font style="font-size:10pt;">Nama Dosen</font></b></td>
			<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_kelas($_GET['id_kelas'],$_GET['id_ptk'],"nama_dosen"); ?></td>
			<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>Tahun Akademik</b></td>
			<td  style="width:25%;"><?php echo view_kelas($_GET['id_kelas'],$_GET['id_ptk'],"tahun_akademik"); ?></td>
			
		</tr>
		<tr>
			<td><font style="font-size:10pt;"><b>Mata Kuliah</b></td>
			<td><font style="font-size:10pt;"><?php echo view_kelas($_GET['id_kelas'],$_GET['id_ptk'],"nm_matkul") ?></td>
			
			<td><font style="font-size:10pt;"><b>Program Studi / Kelas</b></td>
			<td><?php echo view_kelas($_GET['id_kelas'],$_GET['id_ptk'],"prodi-kelas"); ?></td>
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
		<?php for($i=1;$i<=16;$i++){ ?>
		
		<tr  valign="top">
			<td>
			<?php echo $i; ?>
			</td>
			
			<td>
				<?php 
					$sqlKelas=mysqli_query($connection,"SELECT*FROM viewKelasKuliah WHERE xid_kls='".$_GET['id_kelas']."' 
					AND id_ptk='".str_replace("_yz_","-",$_GET['id_ptk'])."'");

					$dataKelas=mysqli_fetch_array($sqlKelas);
					
					$sqlJurnal=mysqli_query($connection,"SELECT*FROM presensi_jurnal_perkuliahan WHERE 
									".cek_gabungan($_GET['id_kelas'])."
									AND id_ptk='".$dataKelas['id_ptk']."'
									AND pertemuan_ke='".$i."'
									");

				$dataJurnal=null;
				if($sqlJurnal && is_object($sqlJurnal)){
					$dataJurnal=mysqli_fetch_array($sqlJurnal);
				}
					$idJurnal=$dataJurnal['id_jurnal'];
					if ($idJurnal==''){
						$idJurnal='x';
					}
					
					
				?>
				
				
				<?php if (($dataJurnal['tanggal']<='2020-01-01') AND ($dataJurnal['tanggal']>='2015-09-30')){ 
					$statusdisabled="disabled";
				}else{ 
					$statusdisabled="enabled";
				}?>
				
				<form action="admin_proses_simpan_materi-<?php echo str_replace("-","_yz_",$_GET['id_kelas']); ?>-<?php echo str_replace("-","_yz_",$_GET['id_ptk']); ?>-<?php echo $i; ?>-<?php echo $idJurnal; ?>.html" method="POST">
				
				<input type="radio" required  name="kegiatan" <?php echo $statusdisabled; ?> value="1" <?php  if ($dataJurnal['kegiatan']=='1'){echo"checked";} ?> > Perkuliahan &nbsp </input>
				<input type="radio" required name="kegiatan" <?php echo $statusdisabled; ?> value="2" <?php if ($dataJurnal['kegiatan']=='2'){echo"checked";} ?>> UTS &nbsp </input>
				<input type="radio" required name="kegiatan" <?php echo $statusdisabled; ?> value="3" <?php if ($dataJurnal['kegiatan']=='3'){echo"checked";} ?>> UAS &nbsp </input>
				
				<textarea class="form-control" name="materi" class="form-control" required style="width:400px; height:40px" <?php echo $statusdisabled; ?>><?php echo $dataJurnal["materi"]; ?></textarea>
				
				<?php /*if ($dataJurnal['id_jurnal']!=''){ 
					$sqlDokumen=mysql_query("SELECT bukti_pembelajaran,id_kelas,prodi FROM presensi_jurnal WHERE id_jurnal='".$dataJurnal['id_jurnal']."'");
					$dataDokumen=mysql_fetch_array($sqlDokumen);
					
					if ($dataDokumen['bukti_pembelajaran']!=''){
						$statusDokumen="Sudah ada";
					}else{
						$statusDokumen="<font style='color:red'>Belum ada</font>";
					
					}
					
				?>
				<a href="admin_form_bukti_pembelajaran-<?php echo $dataJurnal['id_jurnal'];?>.html">Bukti Pembelajaran : </a> <?php echo $statusDokumen; ?>
				<?php 
				
				}*/ ?>
			</td>
			
			<td>
				<input type="date" name="tanggal" required value="<?php echo $dataJurnal['tanggal']; ?>" class="form-control" style="width:160px;" <?php echo $statusdisabled; ?>></input>
			</td>
			<td>
			
			
					<select name="ruang" required class="form-control" style="width:160px;" <?php echo $statusdisabled; ?>>
				
				
				<option value="<?php echo $dataJurnal['ruang']; ?>"><?php echo $dataJurnal['ruang']; ?></option>
					<?php 
					$sqlRuang=mysqli_query($connection,"SELECT ruang FROM presensi_ruang ORDER BY id_ruang DESC"); 
					while($dataRuang=mysqli_fetch_array($sqlRuang)){
					?>
					<option value="<?php echo $dataRuang['ruang']; ?>"><?php echo $dataRuang['ruang']; ?></option>
					<?php
					}
					?>
				</select>
			
				
			</td>
			
			<td>
			
				<?php if ($dataJurnal['id_jurnal']!=''){ ?>
					
				<input type="submit" class="btn btn-warning" value="Simpan" name="lanjut" disabled <?php echo $statusdisabled; ?> >
				<?php }else{?>
				<input type="submit" class="btn btn-warning" value="Simpan" name="lanjut" >
				<?php } ?>
			</td>
			<td>
			
				<?php if ($dataJurnal['id_jurnal']!=''){ ?>
				<input type="submit" class="btn btn-danger" value="Hapus" name="hapus" disabled <?php echo $statusdisabled; ?>>
				<?php }?>
				
				
				</form>
			</td>
		</tr>
		
		<?php } ?>
		
	</table>

	