<?php
if (($_COOKIE['simpreskul_nik']=='') OR ($_COOKIE['ruang']=='')){
	header("Location:$base_url");
}

$sql=mysql_query("SELECT*FROM simpeg_pegawai WHERE nik='".$_COOKIE['simpreskul_nik']."'");
$data=mysql_fetch_array($sql);

echo "Selamat Datang ".$data['nama'];

?>
								
