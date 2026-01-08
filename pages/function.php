<?php 

	/*if (($_COOKIE['simpreskul_nik']=='') OR ($_COOKIE['ruang']=='')){
		header("Location:$base_url");
	}*/


include"koneksi.php";

function tanggal_perkuliahan($id_kelas,$id_ptk,$tgl_awal,$tgl_akhir){
	global $connection;
	$sql=mysqli_query($connection,"SELECT*, DATE_FORMAT(tanggal, '%d-%m-%Y') AS tanggal FROM presensi_jurnal_perkuliahan 
	WHERE xid_kls='".str_replace("_yz_","-",$id_kelas)."' AND id_ptk='".str_replace("_yz_","-",$id_ptk)."' AND tanggal BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."'");
	
	while($data=mysqli_fetch_array($sql)){
		$tanggal.=$data['tanggal']."<br>";
	}
	
	return $tanggal;


}

function master_kelas($xid_sms,$kode_prodi,$tahun_angkatan,$kelas,$kolom){
	global $connection;
	
	$nipd=$kode_prodi.$tahun_angkatan;
	$sqlKelas=mysqli_query($connection,"SELECT wsia_mahasiswa.*,wsia_mahasiswa_pt.*,wsia_dosen.nm_ptk  FROM wsia_mahasiswa
				LEFT JOIN wsia_mahasiswa_pt ON wsia_mahasiswa.xid_pd=wsia_mahasiswa_pt.id_pd
				LEFT JOIN wsia_dosen ON wsia_mahasiswa_pt.pa=wsia_dosen.xid_ptk
				WHERE LEFT(wsia_mahasiswa_pt.nipd,3)='".$nipd."' AND wsia_mahasiswa_pt.kelas='".$kelas."'
			");
	
			
	$dataKelas=mysqli_fetch_array($sqlKelas);
	
	if($kolom=='nama_prodi'){
		$sqlProdi=mysqli_query($connection,"SELECT * FROM wsia_sms WHERE xid_sms='".$xid_sms."'");
		$dataProdi=mysqli_fetch_array($sqlProdi);
		return $dataProdi['nm_lemb'];
	}else if($kolom=='dosen_pa'){
		return $dataKelas['nm_ptk'];
	
	}




}


function ecp($data){
	$panjang=strlen($data);
	for($i=0;$i<=$panjang-1;$i++){
		$encrypt.=bin2hex(substr($data,$i,1));
	
	}
	return $encrypt;
}


function dcp($data){
	$panjang=strlen($data);
	for($i=0;$i<=$panjang-1;$i+=2){
		$decrypt.=hex2bin(substr($data,$i,2));
	
	}
	return $decrypt;
}


function semester($connection,$xid_mk){
	$sql=mysqli_query($connection,"SELECT smt FROM wsia_mata_kuliah_kurikulum WHERE id_mk='".$xid_mk."'");
	$data=mysqli_fetch_array($sql);
	return $data['smt'];
}


function hitung_ips($xid_reg_pd,$id_smt){
	global $connection;
	$sqlMataKuliah=mysqli_query($connection,"SELECT*FROM wsia_kuliah_mahasiswa WHERE xid_reg_pd='".$xid_reg_pd."' AND id_smt='".$id_smt."'");
	$dataMataKuliah=mysqli_fetch_array($sqlMataKuliah);
	//return $dataMataKuliah['ips'];
	if ($dataMataKuliah['ips']!=''){
	return "Detail";
	}
}

function view_kelas($id_kls,$id_ptk,$kolom){
	global $connection;
	$sql=mysqli_query($connection,"SELECT*FROM viewKelasKuliah WHERE xid_kls='".str_replace("_yz_","-",$id_kls)."' AND id_ptk='".str_replace("_yz_","-",$id_ptk)."'");
	$data=mysqli_fetch_array($sql);
	if ($kolom=='nm_matkul'){
		
		return $data['nm_mk'];
		
	}else if ($kolom=='prodi-kelas'){
		$sqlCekGabungan=mysqli_query($connection,"SELECT * FROM presensi_kelas_gabungan WHERE xid_kls='".str_replace("_yz_","-",$id_kls)."'");
		$jumlahCekGabungan=mysqli_num_rows($sqlCekGabungan);
		$dataCekGabungan=mysqli_fetch_array($sqlCekGabungan);
		if ($jumlahCekGabungan>0){
		
			$prodikelas="";
			$sqlCekGabungan2=mysqli_query($connection,"SELECT * FROM presensi_kelas_gabungan WHERE id_gabungan='".$dataCekGabungan['id_gabungan']."'");
			while($dataCekGabungan2=mysqli_fetch_array($sqlCekGabungan2)){
				$sqlKelasGabungan=mysqli_query($connection,"SELECT*FROM viewKelasKuliah WHERE xid_kls='".$dataCekGabungan2['xid_kls']."'");
				$dataKelasGabungan=mysqli_fetch_array($sqlKelasGabungan);
				 $prodikelas.= $dataKelasGabungan['nm_lemb']." / ".$dataKelasGabungan['nm_kls']."<br>";
				 
			}
			
			return $prodikelas;
		
		}else{
			return $data['nm_lemb']." / ".$data['nm_kls'];
		}
		
	}else if ($kolom=='prodi'){
		$dataProdi=explode("-",$data['progdi']);
		$prodi=$dataProdi[0];
		if ($prodi=='A'){$prodi="Mesin Otomotif";}
		else if ($prodi=='B'){$prodi="Manajemen Informatika";}
		else if ($prodi=='C'){$prodi="Komunikasi Massa";}
		else if ($prodi=='D'){$prodi="Perhotelan";}
		else if ($prodi=='E'){$prodi="Farmasi";}
		else if ($prodi=='F'){$prodi="Manajemen Informasi Kesehatan";}
		return $prodi;
	}else if ($kolom=='kd_prodi'){
		$dataProdi=explode("-",$data['progdi']);
		$prodi=$dataProdi[0];
		return $prodi;
	}else if ($kolom=='nama_dosen'){
		
		$sqlDosen=mysqli_query($connection,"SELECT nm_ptk FROM wsia_dosen WHERE wsia_dosen.xid_ptk='".$data['id_ptk']."'");
		$dataDosen=mysqli_fetch_array($sqlDosen);
		return $dataDosen['nm_ptk'];
		
	}else if ($kolom=='tahun_akademik'){
		
		$tahunAkademik=substr($data['id_smt'],0,4);
		$tahunAkademik.="-".($tahunAkademik+1);
		if (substr($data['id_smt'],4,1)=='1'){
			$tahunAkademik.=" Ganjil";
		}else if (substr($data['id_smt'],4,1)=='2'){
			$tahunAkademik.=" Genap";
		}
		
		return $tahunAkademik;
		 
		
	}else if ($kolom=='nik'){
		return $data['dosen'];
	}
	
	else{
		return $data[$kolom];
	}
}




function lihat_kehadiran($nim,$id_jurnal,$id_ptk){
global $connection;
$sql=mysqli_query($connection,"SELECT presensi_rekap.*,presensi_jurnal_perkuliahan.* FROM presensi_rekap 
	LEFT JOIN presensi_jurnal_perkuliahan ON presensi_rekap.id_jurnal=presensi_jurnal_perkuliahan.id_jurnal
	WHERE
	presensi_rekap.id_jurnal='".$id_jurnal."'
	AND presensi_rekap.nim='".$nim."'
	AND presensi_rekap.id_ptk='".str_replace("_yz_","-",$id_ptk)."'
 ");
 
 


 $jumlah=mysqli_num_rows($sql);
	if ($jumlah==0){
		return "<font color='red'><b>X</b></font>";
	 }else{
		return "<b>&#8730</b>";
	}
}

function lihat_kehadiran_checked($nim,$id_jurnal,$id_ptk){
global $connection;
$sql=mysqli_query($connection,"SELECT presensi_rekap.*,presensi_jurnal_perkuliahan.* FROM presensi_rekap 
	LEFT JOIN presensi_jurnal_perkuliahan ON presensi_rekap.id_jurnal=presensi_jurnal_perkuliahan.id_jurnal
	WHERE
	presensi_rekap.id_jurnal='".$id_jurnal."'
	AND presensi_rekap.nim='".$nim."'
	AND presensi_rekap.id_ptk='".str_replace("_yz_","-",$id_ptk)."'
 ");
 
 


 $jumlah=mysqli_num_rows($sql);
	if ($jumlah==0){
		return "";
	 }else{
		return "checked";
	}
}




function judul_head($module){
	if ($module=='presensi_perkuliahan'){
		return "Presensi Perkuliahan";
	}else if ($module=='presensi'){
		return "Silahkan TAP Kartu Tanda Mahasiswa Untuk Melakukan Absensi";
	}else if ($module=='jurnal_perkuliahan'){
		return "Jurnal Perkuliahan";
	}else if ($module=='rekap_presensi'){
		return "Rekap Presensi";
	}else if ($module=='beranda'){
		return "Beranda";
	}else if ($module=='halaman_utama'){
		return "Beranda";
	}else if ($module=='data_kehadiran'){
		return "Data Kehadiran";
	}

}



function view_tahun_akademik(){
	$sql=mysqli_query("SELECT*FROM presensi_tahun_akademik WHERE status='1'");
	$data=mysqli_fetch_array($sql);
	return $data['tahun_akademik'];

}


function view_jurnal($idJurnal,$kolom){
	$sql=mysqli_query("SELECT*FROM presensi_jurnal WHERE id_jurnal='".$idJurnal."'");
	
	$data=mysqli_fetch_array($sql);
	if ($kolom=="materi"){
		return $data['materi'];
	}else if($kolom=="pertemuan"){
		return $data['pertemuan_ke'];
	}else if($kolom=="nm_matkul"){
		$sqlMatkul=mysqli_query("SELECT*FROM siakad_matkul WHERE kd_matkul='".$data['matkul']."'");
		$dataMatkul=mysqli_fetch_array($sqlMatkul);
		return $dataMatkul['nm_matkul'];
	}else if($kolom=="prodi"){
		if ($prodi=='A'){$prodi="Mesin Otomotif";}
		else if ($data['prodi']=='B'){$prodi="Manajemen Informatika";}
		else if ($data['prodi']=='C'){$prodi="Komunikasi Massa";}
		else if ($data['prodi']=='D'){$prodi="Perhotelan";}
		else if ($data['prodi']=='E'){$prodi="Farmasi";}
		else if ($data['prodi']=='F'){$prodi="Manajemen Informasi Kesehatan";}
		
		
		return $prodi;
	}else if($kolom=="kelas"){
		return $data['kelas'];
	}else if($kolom=="kode_matkul"){
		return $data['matkul'];
	}else if($kolom=="ruang"){
		return $data['ruang'];
	}else if($kolom=="nama_dosen"){
		$sqlDosen=mysqli_query("SELECT*FROM simpeg_pegawai WHERE nik='".$_COOKIE['simpreskul_nik']."'");
		$sqlDosen=mysqli_fetch_array($sqlDosen);
		return $sqlDosen['nama'];
	}
}



function cek_gabungan($id_kelas){
	global $connection;
	$sqlCekKelasGabungan=mysqli_query($connection,"SELECT id_gabungan FROM presensi_kelas_gabungan WHERE xid_kls='".str_replace("_yz_","-",$id_kelas)."'");
	$dataKelasGabungan=mysqli_fetch_array($sqlCekKelasGabungan);
	$kondisiId_kls="";
	$y=1;
	if(!$dataKelasGabungan) return "";
	$sqlCekKelasGabungan2=mysqli_query($connection,"SELECT xid_kls FROM presensi_kelas_gabungan WHERE id_gabungan='".$dataKelasGabungan['id_gabungan']."'");
	while($dataCekKelasGabungan2=mysqli_fetch_array($sqlCekKelasGabungan2)){	
		if($y==1){
			$kondisiId_kls="presensi_jurnal_perkuliahan.xid_kls=".$dataCekKelasGabungan2['xid_kls'];
		$y++;
		}else{
			$kondisiId_kls.=" OR presensi_jurnal_perkuliahan.xid_kls=".$dataCekKelasGabungan2['xid_kls'];
		$y++;
		}
	}
	
	if($kondisiId_kls==''){
		$kondisiId_kls="presensi_jurnal_perkuliahan.xid_kls=".str_replace("_yz_","-",$id_kelas);
	}else{
		$kondisiId_kls="(".$kondisiId_kls.")";
	}
	
	return ($kondisiId_kls);
}


function presentase_presensi($nim,$id_kelas,$id_ptk){
	global $connection;
	$sqlAllPertemuan=mysqli_query($connection,"SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE ".cek_gabungan($id_kelas)."
	AND id_ptk='".str_replace("_yz_","-",$id_ptk)."'");
	
		
	$jumlahAllPertemuan=mysqli_num_rows($sqlAllPertemuan);
	
	
		
	$sql=mysqli_query($connection,"SELECT presensi_rekap.id_absensi 
		FROM presensi_rekap 
		LEFT JOIN presensi_jurnal_perkuliahan ON presensi_rekap.id_jurnal=presensi_jurnal_perkuliahan.id_jurnal
		WHERE 
		".cek_gabungan($id_kelas)."
		AND presensi_rekap.id_ptk='".str_replace("_yz_","-",$_GET['id_ptk'])."'
		AND presensi_rekap.nim='".$nim."'
		");
		
	
	$jumlah=mysqli_num_rows($sql);
	
	if ($jumlahAllPertemuan!=0){
		return number_format((($jumlah/$jumlahAllPertemuan)*100),2)." &nbsp";
	}else{
		return "";
	}
}


?>