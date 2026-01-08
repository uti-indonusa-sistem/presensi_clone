<?php

if($_COOKIE['simpreskul_nik']==''){header("Location:login_dosen.html");}

$sql=mysql_query("SELECT presensi_rekap.id_absensi FROM presensi_rekap 
	LEFT JOIN presensi_jurnal ON presensi_rekap.id_jurnal=presensi_jurnal.id_jurnal
	WHERE
	presensi_jurnal.matkul='".$_GET['matkul']."'
	AND presensi_jurnal.thn_akademik='".view_tahun_akademik()."'
	AND presensi_jurnal.pertemuan_ke='".$_GET['pertemuan_ke']."'
	AND presensi_rekap.nim='".$_GET['nim']."'
 ");
 
$data=mysql_fetch_array($sql);

if ($data['id_absensi']==''){
mysql_query("INSERT INTO presensi_rekap(id_jurnal,nim) VALUES('".$_GET['id_jurnal']."','".$_GET['nim']."')");

}else{
mysql_query("DELETE FROM presensi_rekap WHERE id_absensi='".$data['id_absensi']."'");
} 



header("Location:data_kehadiran-$_GET[id_jadwal]-$_GET[prodi]-$_GET[kelas]-$_GET[matkul].html");
 
?>