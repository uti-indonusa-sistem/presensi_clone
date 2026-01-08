<?php ob_start();

header("Content-type: application/vnd-ms-excel"); header("Content-Disposition: attachment; filename=jurnal-$_POST[bulan].xls");


if($_COOKIE['simpreskul_admin']==''){header('Location: ../admin/login.php');}



set_time_limit(9000000000);


include"../koneksi.php";
include"function.php";



echo"<table border='1' style='border: 1px solid black; border-collapse:collapse;'>";
echo"<tr style='text-align:center;vertical-align:center; font-size:10pt; font-family:arial;'>
<td rowspan=2 style='text-align:center;vertical-align:center; font-size:10pt;'>No
</td><td rowspan=2 style='font-size:9pt;'>NAMA</td><td rowspan=2 style='font-size:9pt;'>MATA KULIAH</td><td rowspan=2 style='font-size:9pt;'> PRODI</td>
<td rowspan=2 style='font-size:9pt;'>SMT</td><td rowspan=2 style='font-size:9pt;'>SKS</td><td colspan=8 style='font-size:9pt;'>Tgl Mengajar Pagi</td>
<td rowspan=2 style='font-size:9pt;'>A</td>
<td colspan=8 style='font-size:9pt;'>Tgl Mengajar Petang</td>
<td rowspan=2 style='font-size:9pt;'>B</td>
<td rowspan=2 style='font-size:9pt;'>HR. PG</td>
<td rowspan=2 style='font-size:9pt;'>HR. PTG</td>
<td rowspan=2 style='font-size:9pt;'>PG + PTG</td>
<td rowspan=2 style='font-size:9pt;'>TOTAL</td>
<td rowspan=2 style='font-size:9pt;'>TGL</td>
<td rowspan=2 style='font-size:9pt;'>PARAF</td></tr>";
echo"<tr style='font-size:9pt;'><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td>
<td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td></tr>";



$pilihanKelas1="(
(siakad_jadwal.progdi='A-A-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='A-B-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='B-A-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='B-B-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='C-A-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='D-A-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='D-B-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='D-C-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='E-A-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='E-B-2017' AND siakad_jadwal.smt='6') OR
(siakad_jadwal.progdi='E-C-2017' AND siakad_jadwal.smt='6') OR

(siakad_jadwal.progdi='B-C-2017X' AND siakad_jadwal.smt='6x') OR
(siakad_jadwal.progdi='C-B-2017X' AND siakad_jadwal.smt='6x') OR
(siakad_jadwal.progdi='D-D-2017X' AND siakad_jadwal.smt='6x') OR



(siakad_jadwal.progdi='A-A-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='A-B-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='B-A-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='B-B-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='C-A-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='C-B-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='D-A-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='D-B-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='E-A-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='E-B-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='E-C-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='E-D-2018' AND siakad_jadwal.smt='4') OR
(siakad_jadwal.progdi='F-A-2018' AND siakad_jadwal.smt='4') OR


(siakad_jadwal.progdi='B-C-2018X' AND siakad_jadwal.smt='4x') OR
(siakad_jadwal.progdi='B-D-2018X' AND siakad_jadwal.smt='4x') OR
(siakad_jadwal.progdi='C-C-2018X' AND siakad_jadwal.smt='4x') OR
(siakad_jadwal.progdi='D-C-2018X' AND siakad_jadwal.smt='4x') OR



(siakad_jadwal.progdi='A-A-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='A-B-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='B-A-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='B-B-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='C-A-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='D-A-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='D-B-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='E-A-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='E-B-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='E-C-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='E-D-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='F-A-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='F-B-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='F-C-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='F-D-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='F-E-2019' AND siakad_jadwal.smt='2') OR

(siakad_jadwal.progdi='B-C-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='B-D-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='C-B-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='D-C-2019' AND siakad_jadwal.smt='2') OR
(siakad_jadwal.progdi='D-D-2019' AND siakad_jadwal.smt='2') 

	
)";
	
	
	
	
$no=0;
$sqlDosen=mysql_query("SELECT siakad_jadwal.dosen AS nik 
FROM siakad_jadwal 
LEFT JOIN simpeg_pegawai ON siakad_jadwal.dosen=simpeg_pegawai.nik
WHERE simpeg_pegawai.nik='FAR021' AND ".$pilihanKelas1." GROUP BY siakad_jadwal.dosen ORDER BY simpeg_pegawai.nama ASC");


$no=0;
$barisExcel=2;

while($dataDosen=mysql_fetch_array($sqlDosen)){
$matkulKe=1;

$no++;
	$sqlMataKuliah=mysql_query("SELECT *,CONCAT(hari,pukul,dosen,ruang,th_ak) as gabungan, CONCAT(smt,progdi,matkul,th_ak) AS urutan
			FROM siakad_jadwal WHERE ".$pilihanKelas1."  AND dosen='".$dataDosen['nik']."' AND th_ak='2019-2020'
			 GROUP BY gabungan ORDER BY urutan ASC
			");
	
	$sqlHonorTransport=mysql_query("SELECT transport FROM presensi_honor_mengajar WHERE nik='".$dataDosen['nik']."'");
	$dataHonorTransport=mysql_fetch_array($sqlHonorTransport);
	$honorTransport=$dataHonorTransport['transport'];
	
	if ($honorTransport==''){
		$honorTransport=0;
	}
	
	$sqlJumlahHariHadir=mysql_query("SELECT*FROM presensi_jurnal WHERE nik='".$dataDosen['nik']."' 
		AND tanggal LIKE '%".$_POST['bulan']."%' AND (kegiatan='1' OR kegiatan='') GROUP BY tanggal");
		
			
		$dataJumlahHariHadir=mysql_num_rows($sqlJumlahHariHadir);
	
	$jumlahMataKuliah=mysql_num_rows($sqlMataKuliah);
$barisPertama=$barisExcel+1;
$barisAkhir=($barisPertama+$jumlahMataKuliah)-1;
	echo"<tr>
	<td rowspan='$jumlahMataKuliah' style='font-size:9pt;'>$no</td>
	<td rowspan='$jumlahMataKuliah' style='font-size:9pt;'>".nama_dosen($dataDosen['nik'])."</td>";
	
	
		
	
	
	while($dataMataKuliah=mysql_fetch_array($sqlMataKuliah)){
	$barisExcel++;
	
	$sqlKelasMataKuliah=mysql_query("SELECT*FROM siakad_jadwal WHERE 
	hari='".$dataMataKuliah['hari']."' AND	
	pukul='".$dataMataKuliah['pukul']."' AND
	dosen='".$dataMataKuliah['dosen']."' AND	
	ruang='".$dataMataKuliah['ruang']."' AND	
	smt='".$dataMataKuliah['smt']."' AND	
	th_ak='".$dataMataKuliah['th_ak']."'");
	
	
		
	$b=1;
	$kelasKe=1;
	for($yy=1;$yy<=20;$yy++){
	$jurusan[$yy]="";
	}
	$id_jadwal="";
	while($dataKelasMataKuliah=mysql_fetch_array($sqlKelasMataKuliah)){
	
	if (substr($dataKelasMataKuliah['progdi'],0,1)=='A'){$prodi="MO";}
	else if (substr($dataKelasMataKuliah['progdi'],0,1)=='B'){$prodi="MI";}
	else if (substr($dataKelasMataKuliah['progdi'],0,1)=='C'){$prodi="KM";}
	else if (substr($dataKelasMataKuliah['progdi'],0,1)=='D'){$prodi="HT";}
	else if (substr($dataKelasMataKuliah['progdi'],0,1)=='E'){$prodi="Far";}
	else if (substr($dataKelasMataKuliah['progdi'],0,1)=='F'){$prodi="MIK";}
	
	if($b==1){
	$jurusan[$kelasKe].= $prodi." (".substr($dataKelasMataKuliah['progdi'],2,1).")";
	$id_jadwal= "presensi_jurnal.id_jadwal='$dataKelasMataKuliah[id_jadwal]'";
	}else{
	$jurusan[$kelasKe].=", ".$prodi." (".substr($dataKelasMataKuliah['progdi'],2,1).")";
	$id_jadwal.= " OR presensi_jurnal.id_jadwal='$dataKelasMataKuliah[id_jadwal]'";
	}
	$b++;
	
	
	}
	
	
	
	
	//Hitung Pertemuan
	$p=1;
	$sqlTanggal=mysql_query("SELECT*FROM presensi_jurnal WHERE (".$id_jadwal.") AND tanggal LIKE '%".$_POST['bulan']."%' AND (kegiatan='1' OR kegiatan='')
		GROUP BY pertemuan_ke ORDER BY tanggal ASC");
		
		
			
	$pertemuan_ke[1]="";$pertemuan_ke[2]="";$pertemuan_ke[3]="";$pertemuan_ke[4]="";$pertemuan_ke[5]="";$pertemuan_ke[6]="";$pertemuan_ke[7]="";$pertemuan_ke[8]="";
	$pertemuan_pagi="";
	$pertemuan_petang="";
	while($dataTanggal=mysql_fetch_array($sqlTanggal)){
	
	
		$pecah_tanggal=explode("-",$dataTanggal['tanggal']);
		$pertemuan_ke[$p]=$pecah_tanggal[2];
			
		$p++;
	
		
		$sqlPilihanKelas=mysql_query("SELECT siakad_jadwal.progdi,presensi_pilihan_kelas.kelas FROM siakad_jadwal LEFT JOIN presensi_jurnal 
				ON siakad_jadwal.id_jadwal=presensi_jurnal.id_jadwal 
				LEFT JOIN presensi_pilihan_kelas ON siakad_jadwal.progdi=presensi_pilihan_kelas.prodi
				WHERE ".$id_jadwal." AND tanggal LIKE '%".$_POST['bulan']."%'
				");
				
					
				
	$dataPilihanKelas=mysql_fetch_array($sqlPilihanKelas);
	
		if ($dataPilihanKelas['kelas']=='1'){
			$pertemuan_pagi++;
		}else if ($dataPilihanKelas['kelas']=='2'){
			$pertemuan_petang++;
		}
	}
	
	
	
	echo"<td style='font-size:9pt;'>".singkatan_matakuliah($dataMataKuliah['matkul'])."</td>
	<td style='font-size:9pt;'>".$jurusan[$kelasKe]."</td><td style='font-size:9pt;'>".mata_kuliah($dataMataKuliah['matkul'],"smt")."</td>
	<td style='font-size:9pt;'>".mata_kuliah($dataMataKuliah['matkul'],"sks")."</td>";
	
	if ($dataPilihanKelas['kelas']=='1'){
		echo"<td style='font-size:9pt;'>".$pertemuan_ke[1]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[2]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[3]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[4]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[5]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[6]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[7]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[8]."</td>";
		}else{
		echo"<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
		}

		echo"<td style='font-size:9pt;'>".$pertemuan_pagi."</td>";
		if ($dataPilihanKelas['kelas']=='2'){
		echo"<td style='font-size:9pt;'>".$pertemuan_ke[1]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[2]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[3]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[4]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[5]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[6]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[7]."</td>
		<td style='font-size:9pt;'>".$pertemuan_ke[8]."</td>";
		}else{
		echo"<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
		}

	$jumlah_kelas=$b-1;
	//$hr_pg_ptg=''
		echo"<td style='font-size:9pt;'>".$pertemuan_petang."</td>
		<td>
		
		".hitung_honor($dataDosen['nik'],$dataPilihanKelas['kelas'],$jumlah_kelas,$barisExcel,"1",$dataMataKuliah['matkul'])."
		
		</td>
		<td>
		
		".hitung_honor($dataDosen['nik'],$dataPilihanKelas['kelas'],$jumlah_kelas,$barisExcel,"2",$dataMataKuliah['matkul'])."
		
		</td>
		<td>=Y".$barisExcel."+Z".$barisExcel."</td>";
		
		
		
		
		if ($matkulKe==1){ 
		echo"<td rowspan='$jumlahMataKuliah'>=SUM(AA".$barisPertama.":AA".$barisAkhir.")+($dataJumlahHariHadir*$honorTransport)</td>";
		echo"<td rowspan='$jumlahMataKuliah'></td><td rowspan='$jumlahMataKuliah'></td><td rowspan='$jumlahMataKuliah'></td>";
		}
		
	
	
	if ($kelasKe==1){
	echo"</tr>";
	}
	
	$kelasKe++;
	$matkulKe++;
	}
	
	
	

}

echo"</table>";
?>



	
 