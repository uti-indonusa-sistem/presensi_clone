

<?php if((!isset($_POST['btn-edit'])) AND (!isset($_POST['btn-simpan']))){ ?>	
<?php 
					$sqlDokumen=mysql_query("SELECT bukti_pembelajaran,id_jadwal FROM presensi_jurnal WHERE id_jurnal='".$_GET['id_jurnal']."'");
					$dataDokumen=mysql_fetch_array($sqlDokumen);
		
		
		if (($dataDokumen['bukti_pembelajaran']=='') AND (isset($_POST['btn-cancel']))){ 
		
			Header('Location:dosen_jurnal_perkuliahan-'.$dataDokumen['id_jadwal'].'.html');
		
		}else if (($dataDokumen['bukti_pembelajaran']!='') OR (isset($_POST['btn-cancel']))){?>	

		<table class="table table-striped table-bordered table-hover" id="dataTables-example">
		<tr valign="top">
			<td width="20%">
			Bukti Dokumen Pembelajaran
			</td>
			<td>
				<a href="bukti_pembelajaran/<?php echo $dataDokumen['bukti_pembelajaran'] ?>" ><?php echo $dataDokumen['bukti_pembelajaran']?></a>;
			</td>
			<td>
			<form method="POST" action="dosen_form_bukti_pembelajaran-<?php echo $_GET['id_jurnal']?>.html">
			<input type="submit" class="btn btn-warning" value="Edit" name="btn-edit" style='width:75px'>
			<input type="submit" class="btn btn-danger" value="Hapus" name="btn-hapus" style='width:75px'>
			<input type="submit" class="btn btn-success" value="Kembali" name="btn-kembali" style='width:75px'>
			</form>
			</td>
		</tr>
	<?php }else{
		include"dosen/form_upload_bukti_pembelajaran.php";
	
	}?>
		
<?php } ?>	
<?php if(isset($_POST['btn-edit'])){ 

	include"dosen/form_upload_bukti_pembelajaran.php";
 } ?>


<?php
if(isset($_POST['btn-hapus']))
{
	$sqlDokumen=mysql_query("SELECT bukti_pembelajaran FROM presensi_jurnal WHERE id_jurnal='".$_GET['id_jurnal']."'");
	$dataDokumen=mysql_fetch_array($sqlDokumen);
	if(file_exists('bukti_pembelajaran/'.$dataDokumen['bukti_pembelajaran'])){
		unlink('bukti_pembelajaran/'.$dataDokumen['bukti_pembelajaran']);
	}
	mysql_query("UPDATE presensi_jurnal SET bukti_pembelajaran='' WHERE id_jurnal='".$_GET['id_jurnal']."'");	
	
	
	$sql=mysql_query("SELECT id_jadwal FROM presensi_jurnal WHERE id_jurnal='".$_GET['id_jurnal']."'");
	$data=mysql_fetch_array($sql);
	Header('Location:dosen_jurnal_perkuliahan-'.$data['id_jadwal'].'.html');
	
}

if(isset($_POST['btn-kembali']))
{
		
	$sql=mysql_query("SELECT id_jadwal FROM presensi_jurnal WHERE id_jurnal='".$_GET['id_jurnal']."'");
	$data=mysql_fetch_array($sql);
	Header('Location:dosen_jurnal_perkuliahan-'.$data['id_jadwal'].'.html');
	
}
if(isset($_POST['btn-simpan']))
{

	if($_FILES['filedokumen']['name']!=''){
			$sqlDokumen=mysql_query("SELECT bukti_pembelajaran,prodi,tanggal FROM presensi_jurnal WHERE id_jurnal='".$_GET['id_jurnal']."'");
			$dataDokumen=mysql_fetch_array($sqlDokumen);
			if(file_exists('bukti_pembelajaran/'.$dataDokumen['bukti_pembelajaran'])){
				unlink('bukti_pembelajaran/'.$dataDokumen['bukti_pembelajaran']);
			}
			mysql_query("UPDATE presensi_jurnal SET bukti_pembelajaran='' WHERE id_jurnal='".$_GET['id_jurnal']."'");	
	
			
			
			$nama_file = str_replace(" ","-",$_FILES['filedokumen']['name']);
			$nama_file= $dataDokumen['prodi']."-".$dataDokumen['tanggal']."-".date("Ymdhis")."-".$nama_file;
			$file_loc = $_FILES['filedokumen']['tmp_name'];
			$lokasi="bukti_pembelajaran/";
			move_uploaded_file($file_loc,$lokasi.$nama_file);
			mysql_query("UPDATE presensi_jurnal SET bukti_pembelajaran='".$nama_file."' WHERE id_jurnal='".$_POST['id_jurnal']."'");	
			$sql=mysql_query("SELECT id_jadwal FROM presensi_jurnal WHERE id_jurnal='".$_POST['id_jurnal']."'");
			$data=mysql_fetch_array($sql);
			Header('Location:dosen_jurnal_perkuliahan-'.$data['id_jadwal'].'.html');
	}else{
			echo"Data Belum Lengkap !"; ?>
			<form method="POST" action="dosen_form_bukti_pembelajaran-<?php echo $_GET['id_jurnal']?>.html">
			<input type="submit" class="btn btn-warning" value="Kembali" name="btn-edit">
			</form>
		<?php
		}
}
	
?>