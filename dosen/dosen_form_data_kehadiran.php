<?php if($_COOKIE['simpreskul_id_ptk']==''){header("Location:login_dosen.html");} ?>


<div style="float:left; width:100%;" >
		<form action="dosen_form_data_kehadiran.html" method=POST>
		<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
		<tr>
		
			<td><font style="font-size:10pt;"><b>Tahun Akademik</b></td>
			<td>
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
			<td  style="width:25%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b></b></td>
			<td  style="width:25%;">
				
			
			</td>
			
		</tr>
		<tr>
			<td colspan="4">
			<font style="font-size:10pt;"><input type="submit" value="Cari" class="btn btn-default"></td>
			
		</tr>
		</table>
		</form>
	</div>
	
	
	 <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th style="width:1%;">No</th>
                                                <th style="width:20%;">Matakuliah</th>
                                                <th style="width:45%;">Program Studi / Kelas / Semester</th>
                                                <th style="width:15%;"></th>
                                                
                                                
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
						
					

// Load grouped gabungan and kelas entries in a single query to avoid N+1 queries
$no = 1;

$id_ptk_cookie = mysqli_real_escape_string($connection, $_COOKIE['simpreskul_id_ptk']);
$id_smt_post = isset($_POST['tahun_akademik']) ? mysqli_real_escape_string($connection, $_POST['tahun_akademik']) : "";

// Preload all mataKuliah entries grouped by id_gabungan
$groups = [];
$sqlAll = mysqli_query($connection, "SELECT p.id_gabungan, v.* FROM presensi_kelas_gabungan p
	LEFT JOIN viewKelasKuliah v ON v.xid_kls = p.xid_kls
	WHERE v.id_ptk='".$id_ptk_cookie."' AND v.id_smt='".$id_smt_post."' ORDER BY p.id_gabungan, p.xid_kls DESC");
while ($r = mysqli_fetch_array($sqlAll)) {
	$groups[$r['id_gabungan']][] = $r;
}

// Loop through each group exactly once
foreach($groups as $idg => $dataGroup){
	// Take the first element of the group for shared details like Subject Name
	$dataGabungan = $dataGroup[0];
	
	echo"<tr><td>$no</td><td>$dataGabungan[nm_mk]</td>
	<td>";
	echo"<table border='0' width='100%'><tr><td width='35%'><b>Program Studi</td><td width='10%'><b>Kelas</td><td width='10%'><b>Semester</b></td><td></td></tr>";

	foreach ($dataGroup as $dataMataKuliah) {
		echo"<tr><td>$dataMataKuliah[nm_lemb]</td><td>$dataMataKuliah[nm_kls]</td>
		<td style='text-align:center'>".semester($connection,$dataMataKuliah['xid_mk'])."</td>
		<td style='text-align:center'>
		<a href='dosen_data_kehadiran-".str_replace("-","_yz_",$dataMataKuliah['xid_kls'])."-".str_replace("-","_yz_",$dataMataKuliah['id_ptk']).".html'>Presensi Mahasiswa</a>
		<a href='http://www.cetaksimpreskul.poltekindonusa.ac.id/kehadiran_pdf.php?id_kelas=".str_replace("-","_yz_",$dataMataKuliah['xid_kls'])."&id_ptk=".str_replace("-","_yz_",$id_ptk_cookie)."'><img src='medicio/PDF-icon.png' width='20px'></a>
		</td>
		</tr>";
	}

	echo"</table>";
	echo"</td>
	<td>
	<a href='dosen_jurnal_perkuliahan-".str_replace("-","_yz_",$dataGabungan['xid_kls'])."-".str_replace("-","_yz_",$dataGabungan['id_ptk']).".html'>Jurnal Perkuliahan</a>
	<a href='http://www.cetaksimpreskul.poltekindonusa.ac.id/jurnal_pdf.php?id_kelas=".str_replace("-","_yz_",$dataGabungan['xid_kls'])."&id_ptk=".str_replace("-","_yz_",$id_ptk_cookie)."'><img src='medicio/PDF-icon.png' width='20px'></a>                                        
	</td>
	</tr>";
	$no++;

}
	
//Bukan kelas gabungan
	$sqlBukanKelasGabungan=mysqli_query($connection,"SELECT viewKelasKuliah.* FROM viewKelasKuliah LEFT JOIN
	presensi_kelas_gabungan ON viewKelasKuliah.xid_kls=presensi_kelas_gabungan.xid_kls
	WHERE presensi_kelas_gabungan.xid_kls IS NULL AND
	viewKelasKuliah.id_ptk='".$_COOKIE['simpreskul_id_ptk']."' AND viewKelasKuliah.id_smt='".$id_smt_post."'");
	while($dataBukanKelasGabungan=mysqli_fetch_array($sqlBukanKelasGabungan)){
		echo"<tr><td>$no</td><td>$dataBukanKelasGabungan[nm_mk]</td><td>";
		echo"<table border='0' width='100%'><tr><td width='35%'><b>Program Studi</td><td width='10%'><b>Kelas</td><td width='10%'><b>Semester</b></td><td></td></tr>";
		echo"<tr><td>$dataBukanKelasGabungan[nm_lemb]</td><td>$dataBukanKelasGabungan[nm_kls]</td><td style='text-align:center'>".semester($connection,$dataBukanKelasGabungan['xid_mk'])."</td>
		<td style='text-align:center'>
		<a href='dosen_data_kehadiran-".str_replace("-","_yz_",$dataBukanKelasGabungan['xid_kls'])."-".str_replace("-","_yz_",$dataBukanKelasGabungan['id_ptk']).".html'>Presensi Mahasiswa</a>
		<a href='http://www.cetaksimpreskul.poltekindonusa.ac.id/kehadiran_pdf.php?id_kelas=".str_replace("-","_yz_",$dataBukanKelasGabungan['xid_kls'])."&id_ptk=".str_replace("-","_yz_",$_COOKIE['simpreskul_id_ptk'])."'><img src='medicio/PDF-icon.png' width='20px'></a>
		</td>
		
		</tr>";
		
		echo"</table>
		</td>
		<td>
			<a href='dosen_jurnal_perkuliahan-".str_replace('-','_yz_',$dataBukanKelasGabungan['xid_kls'])."-".str_replace("-","_yz_",$dataBukanKelasGabungan['id_ptk']).".html'>Jurnal Perkuliahan</a><br>
			<a href='http://www.cetaksimpreskul.poltekindonusa.ac.id/jurnal_pdf.php?id_kelas=".str_replace("-","_yz_",$dataBukanKelasGabungan['xid_kls'])."&id_ptk=".str_replace("-","_yz_",$_COOKIE['simpreskul_id_ptk'])."'><img src='medicio/PDF-icon.png' width='20px'></a>
			</td>
		</tr>";
	$no++;
	}
echo"</table>";