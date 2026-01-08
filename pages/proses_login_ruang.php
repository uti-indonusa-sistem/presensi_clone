<?php ob_start();
include"koneksi.php";

$sql=mysql_query("SELECT ruang FROM presensi_rfid_ruang WHERE rfid='".$_POST['rfid_ruang']."'");
$jumlah=mysql_num_rows($sql);
$data=mysql_fetch_array($sql);

if ($jumlah>0){ 
	header("Location:set_cookie-$data[ruang]-ruang.html");
}else{
	header("Location:form_login_ruang.html");
}
?>


