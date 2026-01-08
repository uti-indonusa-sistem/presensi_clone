<?php if($_COOKIE['simpreskul_nik']==''){header("Location:login_dosen.html");} ?>
<div style="float:left; width:100%;" >
	<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
	<tr>
	<td style="width:12%;"><b><font style="font-size:10pt;">Nama Dosen</font></b></td>
	<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_jadwal($_GET['id_jadwal'],"nama_dosen"); ?></td>
	<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>Tahun Akademik</b></td>
	<td  style="width:25%;"><?php echo view_tahun_akademik(); ?></td>
	</tr>
	<tr>
		<td><font style="font-size:10pt;"><b>Mata Kuliah</b></td>
		<td><font style="font-size:10pt;"><?php echo view_jadwal($_GET['id_jadwal'],"nm_matkul") ?></td>
		<td><font style="font-size:10pt;"><b>Kelas</b></td>
		<td><?php echo $_GET['kelas']; ?></td>
	</tr>
		
	</table>
</div>


                                   <table border="1" style="border-color:#DDDDDD; border:1px solid #DDDDDD">
                                        <thead>
                                            <tr>
                                                <th width="1%" rowspan="3" style="vertical-align:center"><center>No</center></th>
                                                <th  width="2%" rowspan="3"><center>NIM</center></th>
                                                <th  width="20%" rowspan="3"><center>Nama</center></th>
											<?php for($i=1;$i<=16;$i++){
												$sqlTanggal=mysql_query("SELECT DATE_FORMAT(presensi_jurnal.tanggal,'%d-%m-%Y') AS tanggal FROM presensi_jurnal 
													WHERE presensi_jurnal.thn_akademik='".view_tahun_akademik()."'
													AND presensi_jurnal.matkul='".$_GET['matkul']."'
													AND presensi_jurnal.kelas='".$_GET['kelas']."'
													AND presensi_jurnal.pertemuan_ke='".$i."'
													");
																							
												$dataTanggal=mysql_fetch_array($sqlTanggal);
											
											?>
												
                                                <th  width="3%" style="transform: rotate(-45deg); vertical-align:center" >
												<font style="font-size:9px;"><center><?php echo $dataTanggal['tanggal']; ?></center></font>
												</th>
                                            <?php } ?>
                                                
                                                
                                            </tr>
											<tr>
                                               
                                               
                                                
											<?php for($i=1;$i<=16;$i++){?>
                                                <th  width="3%"><center><?php echo "<font style='font-size:10px'><center>$i</center></font>"; ?></center></th>
                                           
                                            <?php } ?>
                                                
                                                
                                            </tr>
					    
					     <tr>
					    <?php 
						for($t=1;$t<=16;$t++){
						
						$sqlJurnal1=mysql_query("SELECT id_jurnal FROM presensi_jurnal WHERE
										matkul='".$_GET['matkul']."'
										AND kelas='".$_GET['kelas']."'
										AND thn_akademik='".view_tahun_akademik()."'
										AND prodi='".$_GET['prodi']."'
										AND pertemuan_ke='".$t."'");
								
						$dataJurnal1=mysql_fetch_array($sqlJurnal1);
					if($dataJurnal1['id_jurnal']!=''){	
						
					echo"<td><center><a href='input_kehadiran_all-
						$_GET[matkul]-$i-
						$dataJurnal1[id_jurnal]-
						$_GET[id_jadwal]-
						$_GET[prodi]-$_GET[kelas]-v.html'>&#8730</a>";
					echo" | ";
					echo"<a href='input_kehadiran_all-
						$_GET[matkul]-$i-
						$dataJurnal1[id_jurnal]-
						$_GET[id_jadwal]-
						$_GET[prodi]-$_GET[kelas]-x.html'>x</a></center></td>";
					   }else{
						echo"<td></td>";
					   } 
					   }
					    ?>
					    </tr>
                                        </thead>
                                        <tbody>
                                        <?php
										$sql=mysql_query("SELECT siakad_krs.*,mahasiswa.nama FROM siakad_krs 
											LEFT JOIN mahasiswa ON siakad_krs.nim=mahasiswa.nim
											WHERE siakad_krs.tahun='".view_tahun_akademik()."'
											AND siakad_krs.matkul='".$_GET['matkul']."'
											AND mahasiswa.kelas='".$_GET['kelas']."'
											ORDER BY siakad_krs.nim ASC
											");
											
																				
											$no=0;
											while($data=mysql_fetch_array($sql)){
										
										
											
											$no++;
										?>
                                            <tr style='background-color:white;'>
                                                 <td><?php echo"<font style='font-size:10px'><center>$no</center></font>"?></td>
												<td><?php echo "<font style='font-size:10px'><center>$data[nim]</center></font>"?></td>
												<td><?php echo "<font style='font-size:10px; padding-left:2px'>$data[nama]</font>"?></td>
												
						
												<?php for($i=1;$i<=16;$i++){
								$sqlJurnal=mysql_query("SELECT id_jurnal FROM presensi_jurnal WHERE
										matkul='".$_GET['matkul']."'
										AND kelas='".$_GET['kelas']."'
										AND thn_akademik='".view_tahun_akademik()."'
										AND prodi='".$_GET['prodi']."'
										AND pertemuan_ke='".$i."'");
								
								
								$dataJurnal=mysql_fetch_array($sqlJurnal);
											
												
												?>
												
                                                <td><center>
						<?php 
						if($dataJurnal['id_jurnal']!=''){
						?>
						<a href="input_kehadiran-<?php echo $data['nim']; ?>-<?php echo $data['matkul']; ?>-<?php echo $i ?>-<?php echo $dataJurnal['id_jurnal'];?>-<?php echo $_GET['id_jadwal'];?>-<?php echo $_GET['prodi'];?>-<?php echo $_GET['kelas'];?>.html">
							<?php echo lihat_kehadiran($data['nim'],$data['matkul'],view_tahun_akademik(),$i); ?>
						</a>
						<?php } ?>
						</center></td>
                                                <?php } ?>
												
                                            </tr>
											
										<?php } ?>
                                        </tbody>
                                    </table>