<?php ob_start();


function cek_gabungan($id_kelas){
	global $connection;
	$sqlCekKelasGabungan=mysqli_query($connection,"SELECT id_gabungan FROM presensi_kelas_gabungan WHERE xid_kls='".str_replace("_yz_","-",$id_kelas)."'");
	$dataKelasGabungan=mysqli_fetch_array($sqlCekKelasGabungan);
	$kondisiId_kls="";
	$y=1;
	$sqlCekKelasGabungan2=mysqli_query($connection,"SELECT xid_kls FROM presensi_kelas_gabungan WHERE id_gabungan='".$dataKelasGabungan['id_gabungan']."'");
	while($dataCekKelasGabungan2=mysqli_fetch_array($sqlCekKelasGabungan2)){	
		if($y==1){
			$kondisiId_kls="id_kelas=".$dataCekKelasGabungan2['xid_kls'];
		$y++;
		}else{
			$kondisiId_kls.=" OR id_kelas=".$dataCekKelasGabungan2['xid_kls'];
		$y++;
		}
	}
	
	if($kondisiId_kls==''){
		$kondisiId_kls="id_kelas=".str_replace("_yz_","-",$id_kelas);
	}else{
		$kondisiId_kls="(".$kondisiId_kls.")";
	}
	
	return ($kondisiId_kls);
}

$hostname_conf = "116.206.197.228";
$database_conf = "siakaddb";
$username_conf = "uti-check";
$password_conf = "JcMnZu2D4mWUPcLr";


header("Content-type: application/vnd-ms-excel"); header("Content-Disposition: attachment; filename=jurnal-Januari_2021.xls");

/*
$hostname_conf = "localhost";
$database_conf = "siakaddb";
$username_conf = "root";
$password_conf = "";*/
$connection = mysqli_connect($hostname_conf,$username_conf,$password_conf,$database_conf);

/*
$hostname_conf2 = "localhost";
$database_conf2 = "indonusa3";
$username_conf2 = "root";
$password_conf2 = "";
*/
$hostname_conf2 = "117.20.58.122";
$database_conf2 = "indonusa";
$username_conf2 = "pembayaran";
$password_conf2 = "zFzPmqEUnNaxVBed";
$connection2 = mysqli_connect($hostname_conf2,$username_conf2,$password_conf2,$database_conf2);


$sqlDosen=mysqli_query($connection,"SELECT*FROM presensi_temporary_kelas GROUP BY nidn ORDER BY nama_dosen ASC");
echo"<table border='1' style='border: 1px solid black; border-collapse:collapse;'>";
echo"<tr style='text-align:center;vertical-align:center; font-size:10pt; font-family:arial;'>
<td rowspan=2 style='text-align:center;vertical-align:center; font-size:10pt;'>No
</td><td rowspan=2 style='font-size:9pt;'>NAMA</td><td rowspan=2 style='font-size:9pt;'>MATA KULIAH</td><td rowspan=2 style='font-size:9pt;'> PRODI</td>
<td rowspan=2 style='font-size:9pt;'>SMT</td><td rowspan=2 style='font-size:9pt;'>SKS</td>
<td colspan=9 style='font-size:9pt;'>Tgl Mengajar Pagi</td>
<td rowspan=2 style='font-size:9pt;'>A</td>
<td colspan=9 style='font-size:9pt;'>Tgl Mengajar Petang</td>
<td rowspan=2 style='font-size:9pt;'>B</td>
<td rowspan=2 style='font-size:9pt;'>HR. PG</td>
<td rowspan=2 style='font-size:9pt;'>HR. PTG</td>
<td rowspan=2 style='font-size:9pt;'>PG + PTG</td>
<td rowspan=2 style='font-size:9pt;'>TOTAL</td>
<td rowspan=2 style='font-size:9pt;'>TGL</td>
<td rowspan=2 style='font-size:9pt;'>PARAF</td></tr>";
echo"<tr style='font-size:9pt;'><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td>
<td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td></tr>";



$no=1;
while($dataDosen=mysqli_fetch_array($sqlDosen)){
	
	$sqlMataKuliah=mysqli_query($connection,"SELECT*FROM presensi_temporary_kelas WHERE nidn='".$dataDosen['nidn']."' GROUP BY id_gabungan");
	
	$makulke=1;
	$jumlahMakul=mysqli_num_rows($sqlMataKuliah);
	
	while($dataMataKuliah=mysqli_fetch_array($sqlMataKuliah)){
	
		$prodi_kelas="";
		$prodi="";
			$sqlDetailMakul=mysqli_query($connection,"SELECT*FROM presensi_temporary_kelas WHERE id_gabungan='".$dataMataKuliah['id_gabungan']."'");
			while($dataDetailMakul=mysqli_fetch_array($sqlDetailMakul)){
				if($dataDetailMakul['prodi']=='A'){$prodi='TO';}
				if($dataDetailMakul['prodi']=='B'){$prodi='SI';}
				if($dataDetailMakul['prodi']=='C'){$prodi='KM';}
				if($dataDetailMakul['prodi']=='D'){$prodi='HT';}
				if($dataDetailMakul['prodi']=='E'){$prodi='Far';}
				if($dataDetailMakul['prodi']=='F'){$prodi='MIK';}
				$prodi_kelas.=" ".$prodi."(".str_replace("20","",$dataDetailMakul['kelas']).")";
				
				
				$sqlMakul=mysqli_query($connection,"SELECT*FROM wsia_mata_kuliah WHERE kode_mk='".$dataDetailMakul['matkul']."'");
				$dataMakul=mysqli_fetch_array($sqlMakul);
				
				$sqlDetailMakul2=mysqli_query($connection,"SELECT*FROM wsia_mata_kuliah_kurikulum WHERE id_mk='".$dataMakul['xid_mk']."'");
				$dataDetailMakul2=mysqli_fetch_array($sqlDetailMakul2);
				$makul=$dataMakul['nm_mk'];
				$semester=$dataDetailMakul2['smt'];
				$sks=$dataMakul['sks_mk'];
				
				if ($makul==''){
					$sqlMakul=mysqli_query($connection2,"SELECT*FROM siakad_matkul WHERE kd_matkul='".$dataDetailMakul['matkul']."'");
					$dataMakul=mysqli_fetch_array($sqlMakul);
					$makul=$dataMakul['nm_matkul'];
					$semester=$dataMakul['smt'];
					$sks=$dataMakul['sks'];
				}
			
			}
			
		
		$sqlCekStatusGabungan1=mysqli_query($connection,"SELECT*FROM presensi_temporary WHERE ".cek_gabungan($dataMataKuliah['id_kelas'])." AND reguler='1' AND nidn='".$dataDosen['nidn']."'");
		$dataCekStatusGabungan1=mysqli_fetch_array($sqlCekStatusGabungan1);
		if ($dataCekStatusGabungan1['id_join']==''){
			$sqlPertemuanPagi=mysqli_query($connection,"SELECT*FROM presensi_temporary WHERE ".cek_gabungan($dataMataKuliah['id_kelas'])." AND reguler='1' AND nidn='".$dataDosen['nidn']."' ORDER BY pertemuan_ke ASC ");
		}else{
			$sqlPertemuanPagi=mysqli_query($connection,"SELECT*FROM presensi_temporary WHERE ".cek_gabungan($dataMataKuliah['id_kelas'])." AND reguler='1' AND nidn='".$dataDosen['nidn']."' GROUP BY id_join ORDER BY pertemuan_ke ASC ");
		}
	
				
		
		$minggukePagi=1;
		for($pertemuanPagi=1;$pertemuanPagi<=9;$pertemuanPagi++){
			$presensiPagi[$pertemuanPagi]="";
		}
		while($dataPertemuanPagi=mysqli_fetch_array($sqlPertemuanPagi)){
			$presensiPagi[$minggukePagi]=$dataPertemuanPagi['tanggal'];
			$minggukePagi++;
		}
				
		$totalPagi="";
		
		
		$sqlCekStatusGabungan2=mysqli_query($connection,"SELECT*FROM presensi_temporary WHERE ".cek_gabungan($dataMataKuliah['id_kelas'])." AND reguler='2' AND nidn='".$dataDosen['nidn']."'");
		$dataCekStatusGabungan2=mysqli_fetch_array($sqlCekStatusGabungan2);
		if ($dataCekStatusGabungan2['id_join']==''){
			$sqlPertemuanPetang=mysqli_query($connection,"SELECT*FROM presensi_temporary WHERE ".cek_gabungan($dataMataKuliah['id_kelas'])." AND reguler='2' AND nidn='".$dataDosen['nidn']."' ORDER BY pertemuan_ke ASC");
		}else{
			$sqlPertemuanPetang=mysqli_query($connection,"SELECT*FROM presensi_temporary WHERE ".cek_gabungan($dataMataKuliah['id_kelas'])." AND reguler='2' AND nidn='".$dataDosen['nidn']."' GROUP BY id_join ORDER BY pertemuan_ke ASC");
		}
		
		$minggukePetang=1;
		for($pertemuanPetang=1;$pertemuanPetang<=9;$pertemuanPetang++){
			$presensiPetang[$pertemuanPetang]="";
		}
		while($dataPertemuanPetang=mysqli_fetch_array($sqlPertemuanPetang)){
			$presensiPetang[$minggukePetang]=$dataPertemuanPetang['tanggal'];
			$minggukePetang++;
		}
				
		$totalPetang="";
			
			
		if($makulke=='1'){
		echo "<tr><td rowspan='$jumlahMakul'>$no</td><td rowspan='$jumlahMakul'>$dataDosen[nama_dosen]</td>
		<td>$makul</td><td>$prodi_kelas</td><td>$semester</td><td>$sks</td>";
		
		//==================================pagi=================================================
			
		for($pertemuanPagi=1;$pertemuanPagi<=9;$pertemuanPagi++){
			
			if($presensiPagi[$pertemuanPagi]!=''){
				$hasilPertemuanPagi=substr($presensiPagi[$pertemuanPagi],8,2);
				$totalPagi++;
			}else{
				$hasilPertemuanPagi="";
			}
			echo"<td>$hasilPertemuanPagi</td>";
		}
		echo"<td><b>$totalPagi</b></td>";
		//======================Petang========================================================
		for($pertemuanPetang=1;$pertemuanPetang<=9;$pertemuanPetang++){
			
			if($presensiPetang[$pertemuanPetang]!=''){
				$hasilPertemuanPetang=substr($presensiPetang[$pertemuanPetang],8,2);
				$totalPetang++;
			}else{
				$hasilPertemuanPetang="";
			}
			echo"<td>$hasilPertemuanPetang</td>";
		}
		echo"<td><b>$totalPetang</b></td>";
		echo"<td></td><td></td><td></td><td></td><td></td><td></td>";
		echo"</tr>";
		}else{
			echo "<tr><td>$makul</td><td>$prodi_kelas</td><td>$semester</td><td>$sks</td>";
			//================================PAGI=============================================
			for($pertemuanPagi=1;$pertemuanPagi<=9;$pertemuanPagi++){
				if($presensiPagi[$pertemuanPagi]!=''){
					$hasilPertemuanPagi=substr($presensiPagi[$pertemuanPagi],8,2);
					$totalPagi++;
				}else{
					$hasilPertemuanPagi="";
				}
				echo"<td>$hasilPertemuanPagi</td>";
			}
			echo"<td><b>$totalPagi</b></td>";
			
			//===============================================PETANG=======================================
			for($pertemuanPetang=1;$pertemuanPetang<=9;$pertemuanPetang++){
				if($presensiPetang[$pertemuanPetang]!=''){
					$hasilPertemuanPetang=substr($presensiPetang[$pertemuanPetang],8,2);
					$totalPetang++;
				}else{
					$hasilPertemuanPetang="";
				}
				echo"<td>$hasilPertemuanPetang</td>";
			}
			echo"<td><b>$totalPetang</b></td>";
			echo"<td></td><td></td><td></td><td></td><td></td><td></td>";
			echo"</tr>";
		}
		
		$makulke++;
	}
$no++;
}

echo"</table>";

?>
