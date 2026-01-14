<?php 
require_once __DIR__ . '/../koneksi.php';
if(($_COOKIE['simpreskul_nik']=='') AND ($_COOKIE['simpreskul_admin']=='')){header("Location:login_dosen.html");} ?>





<form action="admin_input_kehadiran-<?php echo $_GET['id_kelas']?>-<?php echo $_GET['id_ptk']?>.html" method="POST" id="selectForm">

<input type="submit" class="btn btn-primary" value="Simpan Presensi" name="simpan_presensi" style="margin-bottom:10px">
<br>

                                    <table border="1" style="border-color:#DDDDDD; border:1px solid #DDDDDD">
                                        <thead>
                                            <tr>
                                                <th width="1%" rowspan="3" style="vertical-align:center"><center>No</center></th>
                                                <th  width="2%" rowspan="3"><center>NIM</center></th>
                                                <th  width="20%" rowspan="3"><center>Nama</center></th>
											<?php for($i=1;$i<=16;$i++){
												$sqlTanggal=mysqli_query($connection,"SELECT DATE_FORMAT(presensi_jurnal_perkuliahan.tanggal,'%d-%m-%Y') 
												AS tanggal FROM presensi_jurnal_perkuliahan 
												WHERE 
												".cek_gabungan($_GET['id_kelas'])."
												AND id_ptk='".str_replace("_yz_","-",$_GET['id_ptk'])."'
												AND presensi_jurnal_perkuliahan.pertemuan_ke='".$i."'
													");
												$dataTanggal=mysqli_fetch_array($sqlTanggal);
											
											?>
											
												
                                                <th  width="3%" style="transform: rotate(-45deg); vertical-align:center" >
												<font style="font-size:12px;"><center><?php echo $dataTanggal['tanggal']; ?></center></font>
												</th>
                                            <?php } ?>
                                                <th rowspan="3" width="1%" style="transform: rotate(-45deg); vertical-align:center"><center><font style="font-size:7pt">Presentase</font><br>%</center></th>
                                                
                                            </tr>
					    
											<tr>
                                               
                                               
                                                
											<?php for($i=1;$i<=16;$i++){?>
                                                <th  width="3%"><center><?php echo "<font style='font-size:12px'><center>$i</center></font>"; ?></center></th>
                                            <?php } ?>
                                                
                                                
                                            </tr>
											
					    
					    <tr>
					    <?php 
						$class="A";
						for($t=1;$t<=16;$t++){
						$class++;
						$sqlJurnal1=mysqli_query($connection,"SELECT id_jurnal FROM presensi_jurnal_perkuliahan
						WHERE	".cek_gabungan($_GET['id_kelas'])."
								AND id_ptk='".str_replace("_yz_","-",$_GET['id_ptk'])."'
								AND presensi_jurnal_perkuliahan.pertemuan_ke='".$t."'
						");
								
						
						
								$dataJurnal1=mysqli_fetch_array($sqlJurnal1);
					
					if($dataJurnal1['id_jurnal']!=''){	
							?>
							<td><center><input type="checkbox" onclick="checkAll(document.getElementById('selectForm'), '<?php echo $class; ?>',this);" /></center>
							
							</td>
								
							<?php
					   }else{
						echo"<td></td>";
					   } 
					   }
					    ?>
						
					    </tr>
						
                                        </thead>
                                        <tbody>
                                        <?php
										// Query robust: Langsung ambil dari tabel mahasiswa_pt tanpa viewNilai yang bermasalah/bikin filter implisit
										$id_kls_clean = str_replace("_yz_","-",$_GET['id_kelas']);
										$queryMhs = "SELECT wsia_mahasiswa_pt.*, wsia_mahasiswa.nm_pd 
													FROM wsia_mahasiswa_pt
													LEFT JOIN wsia_mahasiswa ON wsia_mahasiswa_pt.id_pd=wsia_mahasiswa.xid_pd
													WHERE wsia_mahasiswa_pt.xid_kls='$id_kls_clean' 
													ORDER BY wsia_mahasiswa_pt.nipd ASC";
													
										$sql=mysqli_query($connection, $queryMhs);

										// --- DEBUG BLOCK START ---
										echo "<tr><td colspan='100%' style='background-color: #ffeebc; padding: 10px; text-align: left;'>";
										echo "<strong>[DEBUG INFO]</strong><br>";
										echo "<strong>GET ID Kelas:</strong> " . htmlspecialchars($_GET['id_kelas']) . "<br>";
										echo "<strong>Processed ID Kelas:</strong> " . htmlspecialchars($id_kls_clean) . "<br>";
										echo "<strong>Connection Status:</strong> " . ($connection ? "Connected" : "FAILED") . "<br>";
										echo "<strong>Query SQL:</strong> " . htmlspecialchars($queryMhs) . "<br>";
										
										if(!$sql) {
											echo "<strong>Query Error:</strong> " . mysqli_error($connection) . "<br>";
										} else {
											$rowCount = mysqli_num_rows($sql);
											echo "<strong>Rows Found:</strong> " . $rowCount . "<br>";
											if ($rowCount == 0) echo "<strong>WARNING:</strong> Tidak ada mahasiswa ditemukan dengan ID Kelas tersebut di tabel wsia_mahasiswa_pt.<br>";
										}
										echo "</td></tr>";
										// --- DEBUG BLOCK END ---
										
										if(!$sql) {
											// Error handling handled in debug block above
										}
											
																				
											$no=0;
											// Reset pointer if we used num_rows (though num_rows doesn't advance pointer, better safe)
											if($sql) mysqli_data_seek($sql, 0); 
											
											while($data=mysqli_fetch_array($sql)){
										
										
											
											$no++;
										?>
                                            <tr style='background-color:white;'>
                                                <td><?php echo"<font style='font-size:12px'><center>$no</center></font>"?></td>
						<td><?php echo "<font style='font-size:12px'><center>$data[nipd]</center></font>"?></td>
						<td><?php echo "<font style='font-size:12px; padding-left:2px'>$data[nm_pd]</font>"?></td>
												
								<?php 
								$class="A";
								for($i=1;$i<=16;$i++){
								$class++;
								
										$sqlJurnal=mysqli_query($connection,"SELECT id_jurnal FROM presensi_jurnal_perkuliahan WHERE
										".cek_gabungan($_GET['id_kelas'])."
										AND id_ptk='".str_replace("_yz_","-",$_GET['id_ptk'])."'
										AND presensi_jurnal_perkuliahan.pertemuan_ke='".$i."'
										");
										
										$dataJurnal=mysqli_fetch_array($sqlJurnal);
											
												
								?>
												
                                <td><center>
						<?php 
						if($dataJurnal['id_jurnal']!=''){
						?>
						<input type="checkbox" name="<?php echo $i."-".$data['nipd']; ?>" class="<?php echo $class; ?>" <?php echo lihat_kehadiran_checked($data['nipd'],$dataJurnal['id_jurnal'],str_replace("_yz_","-",$_GET['id_ptk'])); ?> >
						
						<?php } ?>
						</center></td>
                                                <?php 
												
												} ?>
												<td style="text-align:right;"><font style="font-size:12px;"><?php echo  presentase_presensi($data['nipd'],$_GET['id_kelas'],str_replace("_yz_","-",$_GET['id_ptk'])) ?></font></td>
                                
								</tr>
											
										<?php } ?>
                                        </tbody>
                                    </table>						
									<br>
									
</form>