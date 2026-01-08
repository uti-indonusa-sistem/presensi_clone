<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../config/Security.php';
requireAdmin();
?>
<div style="float:left; width:100%;" >
		<form action="admin_form_data_kehadiran.html" method=POST>
		<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
		<tr>
			<td style="width:12%;"><b><font style="font-size:10pt;">Program Studi</font></b></td>
			<td  style="width:25%;"><font style="font-size:10pt;">
				<select name="prodi" class="form-control" style="width:200px;" required>
					<?php $sql=mysqli_query($connection,"SELECT xid_sms,nm_lemb FROM wsia_sms 
					"); 
					echo"<option value=''></option>";
					
					while($data=mysqli_fetch_array($sql)){
					?>
					<option value="<?php echo $data['xid_sms']; ?>"><?php echo $data['nm_lemb']; ?></option>
					<?php } ?>
				</select>

			
			</td>
			<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>Tahun Akademik</b></td>
			<td  style="width:25%;">
				<select name="tahun_akademik" class="form-control" style="width:160px;" required>
					<?php $sql=mysqli_query($connection,"SELECT id_smt,nm_smt FROM wsia_semester WHERE 
					id_smt >= 20201
					"); 
					echo"<option value=''></option>";
					
					while($data=mysqli_fetch_array($sql)){
					?>
					<option value="<?php echo $data['id_smt']; ?>"><?php echo $data['nm_smt']; ?></option>
					<?php } ?>
				</select>
			
			</td>
			
		</tr>
		<tr>
			<td></td>
			<td>
				
			</td>
			
			<td colspan="2"><font style="font-size:10pt;"><input type="submit" value="Cari" class="btn btn-default"></td>
			
			
		</tr>
		</table>
		</form>
	</div>
	
 	 <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th style="width:1%;">No</th>
                                                <th style="width:20%;">Matakuliah</th>
                                                <th style="width:20%;">Dosen</th>
                                                <th style="width:50%;">Program Studi / Kelas / Semester</th>
                                                <th style="width:15%;"></th>
                                                
                                                
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
						
					

$sqlGabungan=mysqli_query($connection,"SELECT presensi_kelas_gabungan.*,viewKelasKuliah.* FROM presensi_kelas_gabungan 
LEFT JOIN viewKelasKuliah ON viewKelasKuliah.xid_kls=presensi_kelas_gabungan.xid_kls WHERE viewKelasKuliah.xid_sms='".$_POST['prodi']."' AND
viewKelasKuliah.id_smt='".$_POST['tahun_akademik']."' GROUP BY 
presensi_kelas_gabungan.id_gabungan");

$no=1;
	while($dataGabungan=mysqli_fetch_array($sqlGabungan)){
		echo"<tr><td>$no</td><td>$dataGabungan[nm_mk]</td>
		<td>".view_kelas($dataGabungan['xid_kls'],$dataGabungan['id_ptk'],"nama_dosen")."</td>
		<td>";
		$sqlMataKuliah=mysqli_query($connection,"SELECT presensi_kelas_gabungan.*,viewKelasKuliah.* FROM presensi_kelas_gabungan 
		LEFT JOIN viewKelasKuliah ON viewKelasKuliah.xid_kls=presensi_kelas_gabungan.xid_kls WHERE 
		presensi_kelas_gabungan.id_gabungan='".$dataGabungan['id_gabungan']."'
		ORDER BY presensi_kelas_gabungan.xid_kls DESC
		");
		echo"<table border='0' width='100%'><tr><td width='35%'><b>Program Studi</td><td width='10%'><b>Kelas</td><td width='10%'><b>Semester</b></td><td></td></tr>";
		while($dataMataKuliah=mysqli_fetch_array($sqlMataKuliah)){
			echo"<tr><td>$dataMataKuliah[nm_lemb]</td><td>$dataMataKuliah[nm_kls]</td>
			<td style='text-align:center'>".semester($connection,$dataMataKuliah['xid_mk'])."</td>
			<td style='text-align:center'>
			<a href='admin_data_kehadiran-".str_replace("-","_yz_",$dataMataKuliah['xid_kls'])."-".str_replace("-","_yz_",$dataGabungan['id_ptk']).".html'>Presensi Mahasiswa</a>
			<a href='http://www.cetaksimpreskul.poltekindonusa.ac.id/kehadiran_pdf.php?id_kelas=".str_replace("-","_yz_",$dataMataKuliah['xid_kls'])."&id_ptk=".str_replace("-","_yz_",$dataMataKuliah['id_ptk'])."'><img src='medicio/PDF-icon.png' width='20px'></a>
		
			</td>
			</tr>";
		}
		echo"</table>";
		echo"";
		echo"</td>
		<td>
		<a href='admin_jurnal_perkuliahan-".str_replace("-","_yz_",$dataGabungan['xid_kls'])."-".str_replace("-","_yz_",$dataGabungan['id_ptk']).".html'>Jurnal Perkuliahan</a>
		<a href='http://www.cetaksimpreskul.poltekindonusa.ac.id/jurnal_pdf.php?id_kelas=".str_replace("-","_yz_",$dataGabungan['xid_kls'])."&id_ptk=".str_replace("-","_yz_",$dataGabungan['id_ptk'])."'><img src='medicio/PDF-icon.png' width='20px'></a>										
		</td>
		</tr>";
	$no++;
		
	}
	
//Bukan kelas gabungan
	$sqlBukanKelasGabungan=mysqli_query($connection,"SELECT viewKelasKuliah.* FROM viewKelasKuliah LEFT JOIN
	presensi_kelas_gabungan ON viewKelasKuliah.xid_kls=presensi_kelas_gabungan.xid_kls
	WHERE presensi_kelas_gabungan.xid_kls IS NULL AND viewKelasKuliah.xid_sms='".$_POST['prodi']."' AND viewKelasKuliah.id_smt='".$_POST['tahun_akademik']."'");
	while($dataBukanKelasGabungan=mysqli_fetch_array($sqlBukanKelasGabungan)){
		echo"<tr><td>$no</td><td>$dataBukanKelasGabungan[nm_mk]</td>
		<td>".view_kelas($dataBukanKelasGabungan['xid_kls'],$dataBukanKelasGabungan['id_ptk'],"nama_dosen")."</td>
		
		<td>";
		echo"<table border='0' width='100%'><tr><td width='35%'><b>Program Studi</td><td width='10%'><b>Kelas</td><td width='10%'><b>Semester</b></td><td></td></tr>";
		
		echo"<tr><td>$dataBukanKelasGabungan[nm_lemb]</td><td>$dataBukanKelasGabungan[nm_kls]</td><td style='text-align:center'>".semester($connection,$dataBukanKelasGabungan['xid_mk'])."</td>
		<td style='text-align:center'>
		<a href='admin_data_kehadiran-".str_replace("-","_yz_",$dataBukanKelasGabungan['xid_kls'])."-".str_replace("-","_yz_",$dataBukanKelasGabungan['id_ptk']).".html'>Presensi Mahasiswa</a>
		<a href='http://www.cetaksimpreskul.poltekindonusa.ac.id/kehadiran_pdf.php?id_kelas=".str_replace("-","_yz_",$dataBukanKelasGabungan['xid_kls'])."&id_ptk=".str_replace("-","_yz_",$dataBukanKelasGabungan['id_ptk'])."'><img src='medicio/PDF-icon.png' width='20px'></a>
		</td>
		
		</tr>";
		
		echo"</table>
		</td>
		<td>
			<a href='admin_jurnal_perkuliahan-".str_replace('-','_yz_',$dataBukanKelasGabungan['xid_kls'])."-".str_replace('-','_yz_',$dataBukanKelasGabungan['id_ptk']).".html'>Jurnal Perkuliahan</a><br>
			<a href='http://www.cetaksimpreskul.poltekindonusa.ac.id/jurnal_pdf.php?id_kelas=".str_replace("-","_yz_",$dataBukanKelasGabungan['xid_kls'])."&id_ptk=".str_replace("-","_yz_",$dataBukanKelasGabungan['id_ptk'])."'><img src='medicio/PDF-icon.png' width='20px'></a>
			</td>
		</tr>";
	$no++;
	}
echo"</table>";