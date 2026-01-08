<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../config/Security.php';
requireAdmin();
?>
                                  
				   <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th width="1%" rowspan="2" style="vertical-align:center">No</th>
                                                <th  width="2%" rowspan="2">NIM</th>
                                                <th  width="20%" rowspan="2">Nama</th>
											<?php for($i=1;$i<=14;$i++){
												$sqlTanggal=mysql_query("SELECT DATE_FORMAT(presensi_jurnal.tanggal,'%d-%m-%Y') AS tanggal FROM presensi_jurnal 
													WHERE presensi_jurnal.thn_akademik='".view_tahun_akademik()."'
													AND presensi_jurnal.matkul='".view_jadwal($_GET['id_jadwal'],"matkul")."'
													AND presensi_jurnal.kelas='".view_jadwal($_GET['id_jadwal'],"kelas")."'
													AND presensi_jurnal.pertemuan_ke='".$i."'
													");
																							
												$dataTanggal=mysql_fetch_array($sqlTanggal);
											
											?>
												
                                                <th  width="3%" style="transform: rotate(-45deg); vertical-align:center" >
												<font style="font-size:12px;"><center><?php echo $dataTanggal['tanggal']; ?></center></font>
												</th>
                                            <?php } ?>
                                                
                                                
                                            </tr>
											<tr>
                                               
                                               
                                                
											<?php for($i=1;$i<=14;$i++){?>
                                                <th  width="3%"><center><?php echo $i; ?></center></th>
                                            <?php } ?>
                                                
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
										$sql=mysql_query("SELECT siakad_krs.*,mahasiswa.nama FROM siakad_krs 
											LEFT JOIN mahasiswa ON siakad_krs.nim=mahasiswa.nim
											WHERE siakad_krs.tahun='".view_tahun_akademik()."'
											AND siakad_krs.matkul='".view_jadwal($_GET['id_jadwal'],"matkul")."'
											AND mahasiswa.kelas='".view_jadwal($_GET['id_jadwal'],"kelas")."'
											ORDER BY siakad_krs.nim ASC
											");
										
											$no=0;
											while($data=mysql_fetch_array($sql)){
										
										
											
											$no++;
										?>
                                            <tr style='background-color:white;'>
                                                <td><?php echo $no;?></td>
												<td><?php echo $data['nim'];?></td>
												<td><?php echo $data['nama'];?></td>
												<?php for($i=1;$i<=14;$i++){?>
                                                <td><center><?php echo lihat_kehadiran($data['nim'],$data['matkul'],view_tahun_akademik(),$i); ?></center></td>
                                                <?php } ?>
												
                                            </tr>
											
										<?php } ?>
                                        </tbody>
                                    </table>
				  
				  
				