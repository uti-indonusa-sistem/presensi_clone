<?php ob_start();

include"../koneksi.php";


if ($_GET['jenis_login']=='ruang'){
	setcookie("ruang",$_GET['id_jenis'], time() + 3600 * 24);
	header("Location:form_login_dosen.html");
}else{
	setcookie("simpreskul_nik",$_GET['id_jenis'], time() + 3600 * 24);
	//header("Location:halaman_utama.html");
	header("Location:presensi_perkuliahan.html");
}
?>

