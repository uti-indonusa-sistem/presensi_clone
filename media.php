<?php

include "koneksi.php";

	
		if (($_GET['module']=='form_login_dosen') OR ($_GET['module']=='form_login_ruang')OR ($_GET['module']=='set_cookie')){
			include"pages/".$_GET['module'].".php";
		}else if (($_GET['module']=='proses_login_dosen') OR ($_GET['module']=='proses_login_ruang')){
			include"pages/".$_GET['module'].".php";
		}else{
			include"pages/halaman_utama.php";
		}


?>