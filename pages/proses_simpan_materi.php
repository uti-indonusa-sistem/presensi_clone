<?php ob_start();

if($_COOKIE['simpreskul_nik']==''){header("Location:login_dosen.html");} 

$sql=mysql_query("SELECT*FROM siakad_jadwal WHERE id_jadwal='".$_GET['id_jadwal']."'");
$data=mysql_fetch_array($sql);

$sqlJadwal=mysql_query("SELECT*FROM siakad_jadwal WHERE 
siakad_jadwal.smt='".$data['smt']."'	
AND siakad_jadwal.hari='".$data['hari']."'	
AND siakad_jadwal.pukul='".$data['pukul']."'
AND siakad_jadwal.dosen='".$data['dosen']."'	
AND siakad_jadwal.ruang='".$data['ruang']."'	
AND siakad_jadwal.th_ak='".$data['th_ak']."'	
AND siakad_jadwal.th_kur='".$data['th_kur']."'

");

$i=0;
$semester=$data['smt'];
while ($dataJadwal=mysql_fetch_array($sqlJadwal)){

	$dataProgdi=explode("-",$dataJadwal['progdi']);


	$sqlCekJurnal=mysql_query("SELECT*FROM presensi_jurnal WHERE
		nik='".$_COOKIE['simpreskul_nik']."'
		AND matkul='".$dataJadwal['matkul']."'
		AND prodi='".$dataProgdi[0]."'
		AND kelas='".$dataProgdi[1]."'
		AND thn_akademik='".view_tahun_akademik()."'
		AND pertemuan_ke='".$_GET['pertemuan_ke']."'
	");



	$jumlahCekJurnal=mysql_num_rows($sqlCekJurnal);
	$dataCekJurnal=mysql_fetch_array($sqlCekJurnal);



	if($jumlahCekJurnal==0){
	
	mysql_query("INSERT INTO presensi_jurnal (id_jadwal,tanggal,waktu,ruang,nik,matkul,prodi,kelas,thn_akademik,pertemuan_ke,materi) 
		VALUES(
		'".$_GET['id_jadwal']."',
		'".date("Y-m-d")."',
		'".date("H:i:s")."',
		'".$_COOKIE['ruang']."',
		'".$_COOKIE['simpreskul_nik']."',
		'".$dataJadwal['matkul']."',
		'".$dataProgdi[0]."',
		'".$dataProgdi[1]."',
		'".view_tahun_akademik()."',
		'".$_GET['pertemuan_ke']."',
		'".$_POST['materi']."')");
		


		$sqlJurnal=mysql_query("SELECT id_jurnal FROM presensi_jurnal ORDER BY id_jurnal DESC");
		$dataJurnal=mysql_fetch_array($sqlJurnal);

		setcookie("id_jurnal",$dataJurnal['id_jurnal'], time() + 3600 * 24);
		
	}else{
		mysql_query("UPDATE presensi_jurnal SET materi='".$_POST['materi']."',tanggal='".date("Y-m-d")."',
		ruang='".$_COOKIE['ruang']."' WHERE id_jurnal='".$dataCekJurnal['id_jurnal']."'");
		
		setcookie("id_jurnal",$dataCekJurnal['id_jurnal'], time() + 3600 * 24);

	}
	
		
	
	

}


header("Location:presensi-$_GET[id_jadwal].html");
?>