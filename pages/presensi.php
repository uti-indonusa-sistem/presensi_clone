<?php
	if (($_COOKIE['simpreskul_nik']=='') OR ($_COOKIE['ruang']=='')){
		header("Location:$base_url");
	}
?>


	<div style="float:left; width:100%;" >
		

	</div>
	
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>waktu</th>
                                                
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
																					
					$sql=mysql_query("SELECT 
						mahasiswa.nim,mahasiswa.nama,presensi_rekap.waktu 
						FROM presensi_rekap 
						LEFT JOIN mahasiswa ON presensi_rekap.nim=mahasiswa.nim
						LEFT JOIN presensi_jurnal ON presensi_rekap.id_jurnal=presensi_jurnal.id_jurnal
						WHERE presensi_jurnal.id_jadwal='".$_GET['id_jadwal']."'
						AND presensi_jurnal.pertemuan_ke='".view_jurnal($_COOKIE['id_jurnal'],"pertemuan")."'
						ORDER BY presensi_rekap.id_absensi DESC
					");
					
				
					
					$no=0;
						while($data=mysql_fetch_array($sql)){
							$no++;
					?>
                                            <tr style='background-color:white;'>
                                                <td><?php echo $no;?></td>
						<td><?php echo $data['nim'];?></td>
                                                <td><?php echo $data['nama'];?></td>
                                                <td><?php echo $data['waktu'];?></td>
                                               
                                            </tr>
											
										<?php } ?>
                                        </tbody>
                                    </table>
				 <!--------------------------->
		<form method="POST" action="proses_presensi_mahasiswa-<?php echo $_GET['id_jadwal'];?>.html">
		<input value="" style="width:0px; height:0px;" type="text" autocomplete="off" name="rfid_absensi" autofocus/>
		</form> 
<!--------------------------->
