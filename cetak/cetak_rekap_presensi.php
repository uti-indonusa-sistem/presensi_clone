<?php if($_COOKIE['simpreskul_admin']==''){header('Location: ../admin/login.php');}
set_time_limit(9000000000);
include"../koneksi.php";
include"function.php";

require_once "../mpdf_v8.0.3-master/vendor/autoload.php";
$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);

$sqlProdi=mysqli_query($connection,"SELECT xid_sms, nm_lemb FROM wsia_sms WHERE xid_sms='".$_POST['prodi']."'"); 
$dataProdi=mysqli_fetch_array($sqlProdi);

// Mulai capture output
ob_start();


$sqlProdi=mysqli_query($connection,"SELECT xid_sms, nm_lemb FROM wsia_sms WHERE xid_sms='".$_POST['prodi']."'"); 
$dataProdi=mysqli_fetch_array($sqlProdi);


if (substr($_POST['tahun_akademik'],4,1)=='1'){
	$dataSemester="GANJIL";
}else if (substr($_POST['tahun_akademik'],4,1)=='2'){
	$dataSemester="GENAP";
}


echo"<br><table>
<tr><td colspan='13'><center>REKAPITULASI PRESENSI MAHASISWA</center></td></tr>
<tr><td colspan='13'><center>PROGRAM STUDI ".strtoupper($dataProdi['nm_lemb'])."</center></td></tr>
<tr><td colspan='13'><center>SEMESTER $dataSemester TAHUN AKADEMIK ".substr($_POST['tahun_akademik'],0,4)."/".(substr($_POST['tahun_akademik'],0,4)+1)."</center></td></tr>
</table><br>";

$sqlKelas=mysqli_query($connection,"SELECT viewKelasKuliah.*,wsia_mata_kuliah_kurikulum.* FROM viewKelasKuliah 
LEFT JOIN wsia_mata_kuliah_kurikulum ON viewKelasKuliah.xid_mk=wsia_mata_kuliah_kurikulum.id_mk
WHERE viewKelasKuliah.id_sms='".$_POST['prodi']."' AND viewKelasKuliah.id_smt='".$_POST['tahun_akademik']."'
GROUP BY viewKelasKuliah.nm_kls
");

while($dataKelas=mysqli_fetch_array($sqlKelas)){
$sqlPA=mysqli_query($connection,"SELECT wsia_dosen.nm_ptk FROM wsia_mahasiswa_pt 
LEFT JOIN wsia_dosen ON wsia_mahasiswa_pt.pa=wsia_dosen.xid_ptk WHERE wsia_mahasiswa_pt.id_sms='".$_POST['prodi']."' AND wsia_mahasiswa_pt.kelas='".$dataKelas['nm_kls']."'
");
$dataPA=mysqli_fetch_array($sqlPA);

echo"<table>
<tr><td colspan='2'>Semester</td><td>: $dataKelas[smt]</td></tr>
<tr><td colspan='2'>Kelas</td><td>: $dataKelas[nm_kls]</td></tr>
<tr><td colspan='2'>Pembimbing Akademik</td><td>: $dataPA[nm_ptk]</td></tr>

</table>";

$sqlMataKuliah=mysqli_query($connection,"SELECT viewKelasKuliah.*, wsia_dosen.nm_ptk FROM viewKelasKuliah 
LEFT JOIN wsia_dosen ON viewKelasKuliah.id_ptk=wsia_dosen.xid_ptk
WHERE viewKelasKuliah.id_sms='".$_POST['prodi']."' AND viewKelasKuliah.id_smt='".$_POST['tahun_akademik']."' AND nm_kls='".$dataKelas['nm_kls']."'

");

// Cache untuk matakuliah data
$mataKuliahData = array();
$dosen = array();
$makul = array();
$xid_kls = array();
$id_ptk = array();
$jumlahPertemuan = array();
$kode = "A";
$urutan = 1;

while($dataMataKuliah=mysqli_fetch_array($sqlMataKuliah)){
	$dosen[$urutan]=$dataMataKuliah['nm_ptk'];
	$makul[$urutan]=$dataMataKuliah['nm_mk'];
	$xid_kls[$urutan]=$dataMataKuliah['xid_kls'];
	$id_ptk[$urutan]=$dataMataKuliah['id_ptk'];
	
	// Pre-calculate jumlah pertemuan
	$jumlahPertemuan[$urutan]=hitung_jumlah_pertemuan($dataMataKuliah['xid_kls'],$dataMataKuliah['id_ptk']);
	$urutan++;
}

// Query semua presensi sekali (batch loading)
$allPresensiData = array();
$allPertemuan = array();
for($i=1;$i<$urutan;$i++){
	$sqlAllPertemuan=mysqli_query($connection,"SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE ".cek_gabungan($xid_kls[$i])."
	AND id_ptk='".str_replace("_yz_","-",$id_ptk[$i])."'");
	$allPertemuan[$i] = array();
	while($row = mysqli_fetch_array($sqlAllPertemuan)){
		$allPertemuan[$i][] = $row['id_jurnal'];
	}
}

echo"<br><table border='1' style='border-collapse:collapse;'>
<tr><td rowspan='2'><center>No</center></td><td rowspan='2'><center>NIM</center></td><td rowspan='2'><center>Nama Mahasiswa</center></td>";
for($q=1;$q<$urutan;$q++){
echo"<td style='width:60px' width='60px'><center>".chr(64+$q)."</center></td>";
}
echo"</tr>";
echo"<tr>";
for($q=1;$q<$urutan;$q++){
echo"<td><center>".$jumlahPertemuan[$q]."X</center></td>";
}
echo"</tr>";

$sqlMahasiswa=mysqli_query($connection,"SELECT viewNilai.*,wsia_mahasiswa_pt.*,wsia_mahasiswa.nm_pd FROM viewNilai 
											RIGHT JOIN wsia_mahasiswa_pt ON viewNilai.xid_reg_pd=wsia_mahasiswa_pt.xid_reg_pd
											LEFT JOIN wsia_mahasiswa ON wsia_mahasiswa_pt.id_pd=wsia_mahasiswa.xid_pd
											WHERE viewNilai.vid_kls='".str_replace("_yz_","-",$dataKelas['xid_kls'])."' ORDER BY wsia_mahasiswa_pt.nipd ASC
											");

// Load semua presensi mahasiswa untuk kelas ini sekaligus
$ptk_list = array();
for($i=1;$i<$urutan;$i++){
	$ptk_list[] = "'".str_replace("_yz_","-",$id_ptk[$i])."'";
}

if(count($ptk_list) > 0){
	$sqlAllPresensi = mysqli_query($connection,"SELECT presensi_rekap.nim, presensi_rekap.id_ptk, COUNT(*) as total_hadir 
									FROM presensi_rekap 
									WHERE presensi_rekap.id_ptk IN (".implode(",",$ptk_list).")
									GROUP BY presensi_rekap.nim, presensi_rekap.id_ptk
									");
	$presensiByNimAndPtk = array();
	while($row = mysqli_fetch_array($sqlAllPresensi)){
		$key = $row['nim']."_".$row['id_ptk'];
		$presensiByNimAndPtk[$key] = $row['total_hadir'];
	}
} else {
	$presensiByNimAndPtk = array();
}

$no=1;		
while($dataMahasiswa=mysqli_fetch_array($sqlMahasiswa)){
echo"<tr><td>$no</td><td>$dataMahasiswa[nipd]</td><td>$dataMahasiswa[nm_pd]</td>";
$no++;
for($q=1;$q<$urutan;$q++){
	$ptk_key = str_replace("_yz_","-",$id_ptk[$q]);
	$presKey = $dataMahasiswa['nipd']."_".$ptk_key;
	$hadir = isset($presensiByNimAndPtk[$presKey]) ? $presensiByNimAndPtk[$presKey] : 0;
	
	if(count($allPertemuan[$q])!=0){
		$presentaseKehadiran=number_format((($hadir/count($allPertemuan[$q]))*100),2);
		$data=explode(".",$presentaseKehadiran);
		if (substr($data[1],0,2)=='00'){
			$desimal="<font color='white'>_</font>";
		}else{
			$desimal=".".$data[1]."<font color='white'>_</font>";
		}
		echo"<td>".$data[0].$desimal."</td>";
	}else{
		echo"<td></td>";
	}
}

echo"</tr>";


}

echo"</table>";


echo"<br><table border='1' style='border-collapse:collapse;'>
<tr><td><center>Kode</td><td colspan='2'><center>Dosen</td><td colspan='5'><center>Matakuliah</td></tr>
";
$t="A";
for($q=1;$q<$urutan;$q++){
echo"<tr><td><center>$t</center></td><td colspan='2'>$dosen[$q]</td><td colspan='5'>$makul[$q]</td></tr>";
$t++;
}
echo"</table><br>";
}

// Get HTML from buffer
$html = ob_get_clean();

// CSS styling
$css = "
<style>
body { font-family: Arial, sans-serif; font-size: 9pt; }
table { border-collapse: collapse; margin-bottom: 10px; }
td { padding: 4px; }
</style>
";

// Write HTML to PDF
$mpdf->WriteHTML($css . $html);

// Output PDF
$filename = 'Rekapitulasi_Presensi_'.$dataProdi['nm_lemb'].'_'.date('Y-m-d').'.pdf';
$filename = preg_replace('/[^a-zA-Z0-9._\-]/', '_', $filename);
$mpdf->Output($filename, 'D');
exit;

?>








?>