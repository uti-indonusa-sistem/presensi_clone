<?php
	if (($_COOKIE['simpreskul_nik']=='') OR ($_COOKIE['ruang']=='')){
		header("Location:$base_url");
	}
?>
<div style="float:left; width:100%;" >
		
		<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
		<tr>
			<td style="width:12%;"><b><font style="font-size:10pt;">Nama Dosen</font></b></td>
			<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_jurnal($_COOKIE['id_jurnal'],"nama_dosen"); ?></td>
			<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>Tahun Akademik</b></td>
			<td  style="width:25%;"><?php echo view_tahun_akademik(); ?></td>
			
		</tr>
		<tr>
			<td><font style="font-size:10pt;"><b>Ruang</b></td>
			<td><font style="font-size:10pt;"><?php echo $_COOKIE['ruang']; ?></td>
			<td><font style="font-size:10pt;"><b>Tanggal</b></td>
			<td><?php echo date("d-m-Y");  ?></td>
		</tr>
		</table>
	</div>
	
                                  
  <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Matakuliah</th>
                                                <th>Semester</th>
                                            
                                               
                                                <th></th>
                                                <th></th>
                                                
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
						
					
						
						
						
							$semester1="2";
							$semester2="4";
							$semester3="6";
						
											
											$sql=mysql_query("SELECT 
											siakad_jadwal.id_jadwal,
											siakad_jadwal.progdi,
											siakad_jadwal.smt,	
											siakad_jadwal.hari,	
											siakad_jadwal.pukul,	
											siakad_jadwal.matkul,	
											siakad_jadwal.dosen,	
											siakad_jadwal.ruang,	
											siakad_jadwal.th_ak,	
											siakad_jadwal.th_kur,
											siakad_matkul.nm_matkul,
											CONCAT(
											siakad_jadwal.smt,	
											siakad_jadwal.hari,	
											siakad_jadwal.pukul,	
											siakad_jadwal.dosen,	
											siakad_jadwal.ruang,	
											siakad_jadwal.th_ak,	
											siakad_jadwal.th_kur
											) AS gabungan
										FROM siakad_jadwal LEFT JOIN siakad_matkul ON siakad_matkul.kd_matkul = siakad_jadwal.matkul
										WHERE 
										siakad_jadwal.dosen='".$_COOKIE['simpreskul_nik']."'
										AND (siakad_jadwal.smt='".$semester1."' OR siakad_jadwal.smt='".$semester2."' OR siakad_jadwal.smt='".$semester3."')
										AND siakad_jadwal.th_ak='".view_tahun_akademik()."'
										GROUP BY gabungan
										ORDER BY siakad_jadwal.id_jadwal ASC
										");
										
										
										
											$no=0;
											while($data=mysql_fetch_array($sql)){
											
											
											
											$no++;
										?>
										
											<tr>
											<td width="1%"><?php echo $no; ?></td>
											<td width="20%"><?php echo $data['nm_matkul']; ?></td>
											<td width="2%"><?php echo $data['smt']; ?></td>
											<td>
											<table>
													<thead><tr><td width="40%"><b>Program Studi</b></td><td width="5%"><b>Kelas</b></td><td></td></tr></thead>
													<tbody>
													<?php 
										$sqlKehadiran=mysql_query("SELECT 	
											siakad_jadwal.progdi,
											siakad_jadwal.matkul
											FROM siakad_jadwal LEFT JOIN siakad_matkul ON siakad_matkul.kd_matkul = siakad_jadwal.matkul
											WHERE 
											siakad_jadwal.smt='".$data['smt']."'	
											AND siakad_jadwal.hari='".$data['hari']."'	
											AND siakad_jadwal.pukul='".$data['pukul']."'
											AND siakad_jadwal.dosen='".$data['dosen']."'	
											AND siakad_jadwal.ruang='".$data['ruang']."'	
											AND siakad_jadwal.th_ak='".$data['th_ak']."'	
											AND siakad_jadwal.th_kur='".$data['th_kur']."'
											GROUP BY siakad_jadwal.progdi
											ORDER BY siakad_jadwal.id_jadwal ASC
											
											");
									
											
											
										while($dataKehadiran=mysql_fetch_array($sqlKehadiran)){	

							$dataProdi=explode("-",$dataKehadiran['progdi']);
							$prodi=$dataProdi[0];
							$kd_prodi=$dataProdi[0];
							$kelas=$dataProdi[1];
							if ($prodi=='A'){$prodi="Mesin Otomotif";}
							else if ($prodi=='B'){$prodi="Manajemen Informatika";}
							else if ($prodi=='C'){$prodi="Komunikasi Massa";}
							else if ($prodi=='D'){$prodi="Perhotelan";}
							else if ($prodi=='E'){$prodi="Farmasi";}
							else if ($prodi=='F'){$prodi="Manajemen Informasi Kesehatan";}

										?>		
										 <tr style='background-color:white;'>
                                               
						<td><?php echo $prodi;?></td>
						
						
                                            
                                                <td><?php echo $kelas;?></td>
												<td>	
													<a href="data_kehadiran-<?php echo $data['id_jadwal']; ?>-<?php echo $kd_prodi;?>-<?php echo $kelas;?>-<?php echo $dataKehadiran['matkul']; ?>.html">Data Kehadiran</a>
													
													
												</td>
                                               
                                            </tr>
										
										
										
										<?php }	?>
													
													
													</tbody>
												</table>
											
											
											
											
											</td>
											<td>
												<a href="jurnal_perkuliahan-<?php echo $data['id_jadwal']; ?>.html">Jurnal Perkuliahan</a><br>
											</tr>
											
										
										<?php
									
										} ?>
                                        </tbody>
                                    </table>