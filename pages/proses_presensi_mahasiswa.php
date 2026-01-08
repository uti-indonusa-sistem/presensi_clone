<?php 
	
	if ($_POST['rfid_absensi']!=''){
		$sqlNIM=mysql_query("SELECT nim FROM presensi_rfid_mahasiswa WHERE rfid='".$_POST['rfid_absensi']."'");
		$dataNIM=mysql_fetch_array($sqlNIM);
	
	$tanggal=date("Y-m-d");
	$sql=mysql_query("SELECT presensi_rekap.nim 
	FROM presensi_rekap
	LEFT JOIN presensi_jurnal ON presensi_rekap.id_jurnal=presensi_jurnal.id_jurnal
	WHERE 
	presensi_rekap.nim='".$dataNIM['nim']."' 
	AND presensi_jurnal.thn_akademik='".view_tahun_akademik()."'
	AND presensi_jurnal.pertemuan_ke='".view_jurnal($_COOKIE['id_jurnal'],"pertemuan")."'
	AND presensi_jurnal.matkul='".view_jurnal($_COOKIE['id_jurnal'],"kode_matkul")."'
	");
	
	$jumlah=mysql_num_rows($sql);
	
	if ($jumlah==0){
		if ($dataNIM['nim']!=''){
			date_default_timezone_set('Asia/Jakarta');
			mysql_query("INSERT INTO presensi_rekap(nim,waktu,id_jurnal) 
			VALUES
			('".$dataNIM['nim']."','".date("H:i:s")."','".$_COOKIE['id_jurnal']."')");
		}
	}
	}
header("Location:presensi-$_GET[id_jadwal].html");
	?>