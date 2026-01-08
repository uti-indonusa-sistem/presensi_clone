<?php
if($_COOKIE['simpreskul_nik']==''){header("Location:login_dosen.html");}


$sqlDataMahasiswa=mysql_query("SELECT siakad_krs.*,mahasiswa.nama FROM siakad_krs 
	LEFT JOIN mahasiswa ON siakad_krs.nim=mahasiswa.nim
	WHERE siakad_krs.tahun='".view_tahun_akademik()."'
	AND siakad_krs.matkul='".$_GET['matkul']."'
	AND mahasiswa.kelas='".$_GET['kelas']."'
	ORDER BY siakad_krs.nim ASC
	");
	
	
	
	while($dataMahasiswa=mysql_fetch_array($sqlDataMahasiswa)){

	$sql=mysql_query("SELECT presensi_rekap.id_absensi FROM presensi_rekap 
			LEFT JOIN presensi_jurnal ON presensi_rekap.id_jurnal=presensi_jurnal.id_jurnal
			WHERE
			presensi_jurnal.matkul='".$_GET['matkul']."'
			AND presensi_jurnal.thn_akademik='".view_tahun_akademik()."'
			AND presensi_jurnal.pertemuan_ke='".$_GET['pertemuan_ke']."'
			AND presensi_rekap.nim='".$dataMahasiswa['nim']."'
		 ");
		 
	
	$data=mysql_fetch_array($sql);
		
	if ($_GET['status']=='v'){
		if ($data['id_absensi']==''){
			mysql_query("INSERT INTO presensi_rekap(id_jurnal,nim) VALUES('".$_GET['id_jurnal']."','".$dataMahasiswa['nim']."')");
		}else{
			mysql_query("DELETE FROM presensi_rekap WHERE id_absensi='".$data['id_absensi']."'");
		} 
	}else if ($_GET['status']=='x'){
		mysql_query("DELETE FROM presensi_rekap WHERE id_jurnal='".$_GET['id_jurnal']."' AND nim='".$dataMahasiswa['nim']."'");
	}
}
//exit;
header("Location:data_kehadiran-$_GET[id_jadwal]-$_GET[prodi]-$_GET[kelas]-$_GET[matkul].html");
 
?>