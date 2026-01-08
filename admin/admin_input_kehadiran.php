<?php ob_start();
error_reporting(0); error_reporting(E_ALL & ~E_NOTICE);

if($_COOKIE['simpreskul_admin']==''){header("Location:login_dosen.html");}


if(isset($_POST['simpan_presensi'])){


$sqlMahasiswa=mysqli_query($connection,"SELECT viewNilai.*,wsia_mahasiswa_pt.*,wsia_mahasiswa.nm_pd FROM viewNilai 
											RIGHT JOIN wsia_mahasiswa_pt ON viewNilai.xid_reg_pd=wsia_mahasiswa_pt.xid_reg_pd
											LEFT JOIN wsia_mahasiswa ON wsia_mahasiswa_pt.id_pd=wsia_mahasiswa.xid_pd
											WHERE viewNilai.vid_kls='".str_replace("_yz_","-",$_GET['id_kelas'])."' ORDER BY wsia_mahasiswa_pt.nipd ASC
											");
	
	$no=0;
	$id_ptk_format = str_replace("_yz_","-",$_GET['id_ptk']);
	if($sqlMahasiswa) while($dataMahasiswa=mysqli_fetch_array($sqlMahasiswa)){
	for($pertemuanKe=1;$pertemuanKe<=16;$pertemuanKe++){
		$sqlJurnal=mysqli_query($connection,"SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE 
		".cek_gabungan(str_replace("_yz_","-",$_GET['id_kelas']))."
		AND
		id_ptk='".$id_ptk_format."' AND pertemuan_ke='".$pertemuanKe."'
		");
		
		if($sqlJurnal) {
			$dataJurnal=mysqli_fetch_array($sqlJurnal);
			if ($dataJurnal && $dataJurnal['id_jurnal']!=''){
				mysqli_query($connection,"DELETE FROM presensi_rekap WHERE nim='".$dataMahasiswa['nipd']."' AND 
				id_jurnal='".$dataJurnal['id_jurnal']."' AND
				id_ptk='".$id_ptk_format."'
				");
			
				if(isset($_POST[$pertemuanKe.'-'.$dataMahasiswa['nipd']]) && $_POST[$pertemuanKe.'-'.$dataMahasiswa['nipd']]=='on'){
					mysqli_query($connection,"INSERT INTO presensi_rekap(nim,id_jurnal,id_ptk)
					VALUES('".$dataMahasiswa['nipd']."','".$dataJurnal['id_jurnal']."','".$id_ptk_format."')
					");
				}
			}
		}
			");
		
		}
		
		
		}
		
			
				



			}
	
	
	
	}
	
	echo"Data berhasil disimpan. <br><br><a href='admin_jurnal_perkuliahan-".str_replace("-","_yz_",$_GET['id_kelas'])."-".str_replace("-","_yz_",$_GET['id_ptk']).".html'>Lanjut ke Jurnal Perkuliahan</a> atau 
	<a href='admin_data_kehadiran-".str_replace("-","_yz_",$_GET['id_kelas'])."-".str_replace("-","_yz_",$_GET['id_ptk']).".html'>Kembali Ke Halaman Presensi Mahasiswa</a>";


}
?>