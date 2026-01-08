<?php ob_start();

if($_COOKIE['simpreskul_admin']==''){header("Location:login_dosen.html");} 



if(isset($_POST['hapus'])){
	
header("Location:admin_konfirm-".$_GET['id_jurnal']."-".$_GET['id_kelas']."-".$_GET['id_ptk'].".html");


}else if(isset($_POST['lanjut'])){

if ($_POST['tanggal']<='2020-01-01'){
header("Location:warning-".$_GET['id_kelas'].".html"); 

}else{

$sql=mysqli_query($connection,"SELECT*FROM viewKelasKuliah WHERE xid_kls='".$_GET['id_kelas']."' AND id_ptk='".str_replace("_yz_","-",$_GET['id_ptk'])."'");
$data=mysqli_fetch_array($sql);


$sqlJurnalPerkuliahan=mysqli_query($connection,"SELECT*FROM presensi_jurnal_perkuliahan WHERE 
xid_kls='".$_GET['id_kelas']."' AND pertemuan_ke='".$_GET['pertemuan_ke']."' AND id_ptk='".$data['id_ptk']."'
");

$dataJurnalPerkuliahan=mysqli_fetch_array($sqlJurnalPerkuliahan);
$jumlahJurnalPerkuliahan=mysqli_num_rows($sqlJurnalPerkuliahan);


if ($jumlahJurnalPerkuliahan>0){
	mysqli_query($connection,"UPDATE presensi_jurnal_perkuliahan SET
	xid_kls='".$_GET['id_kelas']."',
	tanggal='".$_POST['tanggal']."',
	kode_mk='".$data['kode_mk']."',
	materi='".$_POST['materi']."',
	id_ptk='".$data['id_ptk']."',
	id_smt='".$data['id_smt']."',
	id_prodi='".$data['id_sms']."',
	nm_kls='".$data['nm_kls']."',
	pertemuan_ke='".$_GET['pertemuan_ke']."',
	kegiatan='".$_POST['kegiatan']."',
	ruang='".$_POST['ruang']."',
	tanggal_input='".date('d-m-Y  h:i:s a')."'
	WHERE id_jurnal='".$dataJurnalPerkuliahan['id_jurnal']."'
	");

	
}else{
	mysqli_query($connection,"INSERT INTO presensi_jurnal_perkuliahan(xid_kls,tanggal,kode_mk,materi,id_ptk,id_smt,id_prodi,nm_kls,pertemuan_ke,kegiatan,ruang,tanggal_input)
	VALUES('".$_GET['id_kelas']."','".$_POST['tanggal']."','".$data['kode_mk']."','".$_POST['materi']."','".$data['id_ptk']."','".$data['id_smt']."',
	'".$data['id_sms']."','".$data['nm_kls']."','".$_GET['pertemuan_ke']."','".$_POST['kegiatan']."','".$_POST['ruang']."','".date('d-m-Y  h:i:s a')."'
	)");

	
	
}
echo"Data berhasil disimpan. <br><br>
<a href='admin_jurnal_perkuliahan-".str_replace("-","_yz_",$_GET['id_kelas'])."-".str_replace("-","_yz_",$_GET['id_ptk']).".html'>Kembali ke Jurnal Perkuliahan</a> atau 
<a href='admin_data_kehadiran-".str_replace("-","_yz_",$_GET['id_kelas'])."-".str_replace("-","_yz_",$_GET['id_ptk']).".html'>Lanjut Ke Halaman Presensi Mahasiswa</a>";
	

}
}
?>