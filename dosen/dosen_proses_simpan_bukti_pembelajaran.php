<?php

if(isset($_POST['btn-simpan']))
{
	
	
		$nama_file = str_replace(" ","-",$_FILES['filedokumen']['name']);
		$nama_file= date("Ymdhis")."-".$nama_file;
		$jenis_file=explode(".",$_FILES['filedokumen']['name']);
		$jenis_file=$jenis_file[1];
		$file_loc = $_FILES['filedokumen']['tmp_name'];
		$lokasi="dokumen_pembelajaran/";
		move_uploaded_file($file_loc,$lokasi.$nama_file);
		mysql_query("UPDATE presensi_jurnal SET bukti_pembelajaran='".$nama_file."' WHERE id_jurnal='".$_POST['id_jurnal']."'");	

}
	$sql=mysql_query("SELECT id_jadwal FROM presensi_jurnal WHERE id_jurnal='".$_POST['id_jurnal']."'");
	$data=mysql_fetch_array($sql);

	Header('Location:dosen_jurnal_perkuliahan-'.$data['id_jadwal'].'.html');
?>