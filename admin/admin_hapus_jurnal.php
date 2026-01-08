<?php

	mysqli_query($connection,"DELETE FROM presensi_jurnal_perkuliahan WHERE id_jurnal='".$_GET['id_jurnal']."'");
	mysqli_query($connection,"DELETE FROM presensi_rekap WHERE id_jurnal='".$_GET['id_jurnal']."' AND id_ptk='".str_replace("_yz_","-",$_GET['id_ptk'])."'");
	header("Location:admin_jurnal_perkuliahan-".$_GET['id_kelas']."-".str_replace("-","_yz_",$_GET['id_ptk']).".html");
?>