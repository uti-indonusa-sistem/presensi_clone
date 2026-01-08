<?php ob_start();

	unset($_COOKIE['ruang']);
	unset($_COOKIE['simpreskul_nik']);
	unset($_COOKIE['id_jurnal']);

	//setcookie("ruang","", time() + 3600 * 24);
	//setcookie("simpreskul_nik","", time() + 3600 * 24);
	//setcookie("id_jurnal","", time() + 3600 * 24);
	header("Location:$base_url");
?>

