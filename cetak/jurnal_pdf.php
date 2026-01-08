<?php ob_start();
error_reporting(0); error_reporting(E_ALL & ~E_NOTICE);
if(($_COOKIE['simpreskul_nik']=='')AND($_COOKIE['simpreskul_admin']==''))
{
header("Location:login_dosen.html");

} 

	require_once "../mpdf_v8.0.3-master/vendor/autoload.php";
	$mpdf = new \Mpdf\Mpdf();
	$mpdf->AddPage("P","","","","","15","15","15","15","","","","","","","","","","","","A4");
	$stylesheet = file_get_contents('../mpdf/mpdfstyletables.css');
	$mpdf->WriteHTML($stylesheet,1);

include"function.php";
	
$header="
<img src='../medicio/kop.jpg' width='100%'>
";

$dataTahunAkademik=explode(" ",view_kelas($_GET['id_kelas'],"tahun_akademik"));
$dataProdi=explode("/",view_kelas($_GET['id_kelas'],"prodi-kelas"));
$dataNamaMatkul=view_kelas($_GET['id_kelas'],"nm_matkul");
$dataSKS=view_kelas($_GET['id_kelas'],"sks_mk");
$dataNamaDosen=view_kelas($_GET['id_kelas'],"nama_dosen");
$prodi_kelas=view_kelas($_GET['id_kelas'],"prodi-kelas");
$dataSemester=view_kelas(view_kelas($_GET['id_kelas'],"xid_mk"),"smt");

$title="
<table width='100%'><tr><td style='text-align:center;'><b>PRESENSI DOSEN SEMESTER ".strtoupper($dataTahunAkademik[1])." TAHUN AKADEMIK $dataTahunAkademik[0]</b></td></tr></table><br>
<table width='80%' border='0'>
	<tr><td    style='height:10px' height='10px'>Mata Kuliah</td><td>:</td><td>$dataNamaMatkul</td></tr>
	<tr><td   style='height:10px' height='10px'>SKS</td><td>:</td><td>$dataSKS</td></tr>
	<tr><td   style='height:10px' height='10px'>Pengampu</td><td>:</td><td>$dataNamaDosen</td></tr>
	<tr valign='top'><td   style='height:10px' height='10px'>Program Studi / Kelas</td><td>:</td><td>$prodi_kelas</td></tr>
	<tr><td   style='height:10px' height='10px'>Semester</td><td>:</td><td>$dataSemester</td></tr>
</table><br>
";


for($i=1;$i<=16;$i++){ 
		
				$sqlKelas=mysqli_query($connection,"SELECT*FROM viewKelasKuliah WHERE xid_kls='".$_GET['id_kelas']."' 
					AND id_ptk='".$_COOKIE['simpreskul_id_ptk']."'");

					$dataKelas=mysqli_fetch_array($sqlKelas);
					
					$sqlJurnal=mysqli_query($connection,"SELECT*,date_format(tanggal,'%d /%m/%Y') AS tanggal,date_format(tanggal,'%a') AS hari FROM presensi_jurnal_perkuliahan WHERE 
									".cek_gabungan($_GET['id_kelas'])."
									AND id_ptk='".$dataKelas['id_ptk']."'
									AND pertemuan_ke='".$i."'
									");
					
					// Load semua jumlah mahasiswa sekaligus
					$sqlAllJurnal=mysqli_query($connection,"SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE 
									".cek_gabungan($_GET['id_kelas'])."
									AND id_ptk='".$dataKelas['id_ptk']."'");
					$jurnalIds = array();
					$jurnalData = array();
					while($j = mysqli_fetch_array($sqlAllJurnal)){
						$jurnalIds[] = "'".$j['id_jurnal']."'";
						$jurnalData[$j['id_jurnal']] = 0;
					}
					
					$jumlahCache = array();
					if(count($jurnalIds) > 0){
						$sqlJumlah = mysqli_query($connection,"SELECT id_jurnal, COUNT(*) as total FROM presensi_rekap WHERE id_jurnal IN (".implode(",",$jurnalIds).") GROUP BY id_jurnal");
						while($row = mysqli_fetch_array($sqlJumlah)){
							$jumlahCache[$row['id_jurnal']] = $row['total'];
						}
					}
					
					$dataJurnal=mysqli_fetch_array($sqlJurnal);
					$hari=konversi_hari($dataJurnal['hari']);
					$idJurnal=$dataJurnal['id_jurnal'];
					if ($idJurnal==''){
						$idJurnal='x';
					}
					
		$jumlah_mahasiswa="";
		
	if ($dataJurnal['id_jurnal']!=''){
	$jumlah_mahasiswa = isset($jumlahCache[$dataJurnal['id_jurnal']]) ? $jumlahCache[$dataJurnal['id_jurnal']] : 0;
	}
		$baris.="<tr><td  style='height:50px' height='50px'>$i</td>
			<td>$dataJurnal[materi]</td>
			<td>$hari<br>$dataJurnal[tanggal]</td>
			<td>$dataJurnal[ruang]</td>
			<td>$jumlah_mahasiswa</td></tr>";
}

$content="<table width='100%' border='1' style='border:1px solid black; border-collapse:collapse;'>
		<tr >
			<td width='1%' valign='middle' style='text-align:center'>
				<b>Perte- muan Ke
			</td>
			
			<td valign='middle' style='text-align:center' width='60%'>
				<b>Materi Perkuliahan
			</td>
			<td valign='middle' style='text-align:center' width='12%'><b>Tanggal</td>
			<td valign='middle' style='text-align:center' width='12%'>
			<b>Ruang
			</td>
			<td valign='middle' style='text-align:center' width='1%'>
			<b>Jumlah Mahasiswa</b>
			</td>
			
		</tr>
		$baris
		
		</table>";


//echo $title.$content;

$namaFile='jurnal-perkuliahan"'.$dataProdi.'""'.$dataNamaMatkul.'".pdf';
$namaFile=str_replace(",","_",$namaFile);
$mpdf->WriteHTML($header.$title.$content);		
$mpdf->Output($namaFile,'D');

?>