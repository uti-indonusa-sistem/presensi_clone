<?php ob_start();
error_reporting(0); error_reporting(E_ALL & ~E_NOTICE);
	
	include "koneksi.php";
	include"pages/function.php";
	include"kaprodi/index.php";
	
	//header("Location:$base_url");

?>