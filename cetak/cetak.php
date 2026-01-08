<?php ob_start();
/*
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=cetak-jurnal.xls");*/
if($_COOKIE['simpreskul_admin']==''){header('Location: ../admin/login.php');}



set_time_limit(9000000000);



include"../koneksi.php";
include"function.php";

echo"<table border='1' style='border: 1px solid black; border-collapse:collapse;'>";
echo"<tr style='text-align:center;vertical-align:center;'><td rowspan=2 style='text-align:center;vertical-align:center;'>No</td><td rowspan=2>NAMA</td><td rowspan=2>MATA KULIAH</td><td rowspan=2> PRODI</td><td rowspan=2>SMT</td><td rowspan=2>SKS</td><td colspan=5>Tgl Mengajar Pagi</td><td rowspan=2>A</td>
<td colspan=5>Tgl Mengajar Petang</td><td rowspan=2>B</td><td rowspan=2>HR. PG</td><td rowspan=2>HR. PTG</td><td rowspan=2>PG + PTG</td><td rowspan=2>TOTAL</td><td rowspan=2>TGL</td><td rowspan=2>PARAF</td></tr>";
echo"<tr><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>";

$no=0;
$sqlDosen=mysqli_query($connection,"SELECT*FROM presensi_jurnal WHERE tanggal LIKE '%".$_POST['bulan']."%' GROUP BY nik");

$allDosen = array();
while($row = mysqli_fetch_array($sqlDosen)){
	$allDosen[] = $row;
}

// Batch load semua data jadwal dan mata kuliah
$sqlAllData = mysqli_query($connection,"SELECT DISTINCT id_jadwal, nik FROM presensi_jurnal WHERE tanggal LIKE '%".$_POST['bulan']."%'");
$jadwalData = array();
while($row = mysqli_fetch_array($sqlAllData)){
	$jadwalData[$row['id_jadwal']] = $row['nik'];
}

// Batch load semua tanggal dan jurnal
$sqlAllTanggal = mysqli_query($connection,"SELECT * FROM presensi_jurnal WHERE tanggal LIKE '%".$_POST['bulan']."%'");
$tanggalByJadwal = array();
$jurnalByJadwal = array();
while($row = mysqli_fetch_array($sqlAllTanggal)){
	$id_jadwal = $row['id_jadwal'];
	if(!isset($tanggalByJadwal[$id_jadwal])){
		$tanggalByJadwal[$id_jadwal] = array();
		$jurnalByJadwal[$id_jadwal] = array();
	}
	$tanggalByJadwal[$id_jadwal][$row['pertemuan_ke']] = $row['tanggal'];
	$key = $row['prodi'].$row['kelas'];
	if(!isset($jurnalByJadwal[$id_jadwal][$key])){
		$jurnalByJadwal[$id_jadwal][$key] = array('prodi' => $row['prodi'], 'kelas' => $row['kelas']);
	}
}

foreach($allDosen as $dataDosen){
	$no++;
	
	$sql=mysqli_query($connection,"SELECT DISTINCT id_jadwal FROM presensi_jurnal WHERE nik='".$dataDosen['nik']."' AND tanggal LIKE '%".$_POST['bulan']."%'");
	
	$jadwalIds = array();
	while($jadwalRow = mysqli_fetch_array($sql)){
		$jadwalIds[] = $jadwalRow['id_jadwal'];
	}
	
	$a=1;
	$jumlah_row=count($jadwalIds);
	
	foreach($jadwalIds as $id_jadwal){
		$data = array('id_jadwal' => $id_jadwal);
		$data['matkul'] = 'unknown'; // fallback
		$dataJadwal = mysqli_query($connection,"SELECT matkul FROM presensi_jurnal WHERE id_jadwal='".$id_jadwal."' LIMIT 1");
		$jadwalDetail = mysqli_fetch_array($dataJadwal);
		if($jadwalDetail) $data['matkul'] = $jadwalDetail['matkul'];
		
		$pertemuan_ke = array();
		if(isset($tanggalByJadwal[$id_jadwal])){
			foreach($tanggalByJadwal[$id_jadwal] as $p => $tgl){
				$pecah_tanggal=explode("-",$tgl);
				$pertemuan_ke[$p]=$pecah_tanggal[2];
			}
		}
		
		$jurusan = "";
		if(isset($jurnalByJadwal[$id_jadwal])){
			$b = 1;
			foreach($jurnalByJadwal[$id_jadwal] as $key => $j){
				$prodi_singkat = "";
				if ($j['prodi']=='A'){$prodi_singkat="MO";}
				else if ($j['prodi']=='B'){$prodi_singkat="MI";}
				else if ($j['prodi']=='C'){$prodi_singkat="KM";}
				else if ($j['prodi']=='D'){$prodi_singkat="HT";}
				else if ($j['prodi']=='E'){$prodi_singkat="Far";}
				else if ($j['prodi']=='F'){$prodi_singkat="MIK";}
				
				if($b==1){
					$jurusan.= $prodi_singkat." (".$j['kelas'].")";
				}else{
					$jurusan.=", ".$prodi_singkat." (".$j['kelas'].")";
				}
				$b++;
			}
		}
		
		echo"<tr>";
		if ($a==1){ echo"<td rowspan=$jumlah_row>$no</td><td rowspan=$jumlah_row>".nama_dosen($dataDosen['nik'])."</td>"; }

		echo"<td>".mata_kuliah($data['matkul'],"nm_matkul")."</td><td>".$jurusan."</td><td>".mata_kuliah($data['matkul'],"smt")."</td>
		<td>".mata_kuliah($data['matkul'],"sks")."</td>
		<td>".(isset($pertemuan_ke[1]) ? $pertemuan_ke[1] : "")."</td><td>".(isset($pertemuan_ke[2]) ? $pertemuan_ke[2] : "")."</td><td></td><td></td>
		<td></td><td></td><td></td><td></td><td></td>
		<td></td><td></td><td></td><td></td><td></td>
		<td></td><td></td><td></td><td></td></tr>";
		$a++;
	}
}
echo"</table>";

?>



	
 