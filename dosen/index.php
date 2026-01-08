<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../config/Security.php';

// Check authentication FIRST before any output
if ((empty($_COOKIE['simpreskul_nik']) || $_COOKIE['simpreskul_nik'] == '') && (empty($_COOKIE['simpreskul_admin']) || $_COOKIE['simpreskul_admin'] == '')) {
    header('Location: ' . $base_url . '/login_dosen.html');
    exit;
}

// Ensure a module value to avoid undefined index warnings
$module = isset($_GET['module']) ? $_GET['module'] : 'dosen';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SIMPRESKUL V2 - Politeknik Indonusa Surakarta</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/metisMenu.min.css" rel="stylesheet">
        <link href="css/timeline.css" rel="stylesheet">
        <link href="css/startmin.css" rel="stylesheet">
        <link href="css/morris.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" href="css/logo.png">
	
        <!--------------------check All----------------------------->
		<script type="text/javascript">
		function checkAll(theForm, cName, allNo_stat) {
		var n=theForm.elements.length;
		for (var i=0;i<n;i++){
		if (theForm.elements[i].className.indexOf(cName) !=-1){
		if (allNo_stat.checked) {
		theForm.elements[i].checked = true;
		} else {
		theForm.elements[i].checked = false;
		}
		}
		}
		}
		</script>
        <!------------------------------------------------->

    </head>
    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.html">SIMPRESKUL V2</a>
                </div>

                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <ul class="nav navbar-nav navbar-left navbar-top-links">
                    <li><a href="http://www.poltekindonusa.ac.id"><i class="fa fa-home fa-fw"></i> Website</a></li>
                </ul>

                <ul class="nav navbar-right navbar-top-links">
                   
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> 
			    <?php
				
				
					$sql=mysqli_query($connection,"SELECT*FROM wsia_dosen WHERE nidn='".$_COOKIE['simpreskul_nik']."'");
								$data=mysqli_fetch_array($sql);
								echo $data['nm_ptk'];
				
				?>
				<b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            
                            <li><a href="login_dosen.html"><i class="fa fa-sign-out fa-fw"></i> Keluar</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li class="sidebar-search">
                                <div class="input-group custom-search-form">
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                </span>
                                </div>
                                <!-- /input-group -->
                            </li>
                          <!-- <li>
                                <a href="dosen.html" class="active"><i class="fa fa-dashboard fa-fw"></i> Beranda</a>
                            </li>-->
                           
                           
                            <li>
                                <a href="dosen_form_data_kehadiran.html"><i class="fa fa-edit fa-fw"></i> Data Kehadiran</a>
                            </li>
                           
                        <?php if($_GET['module']=='dosen_data_kehadiran'){?>  
			<div style="float:left; width:100%;" >
			<table class="table table-striped table-bordered table-hover" id="dataTables-example" style="width:100%;">
			<tr>
			<td style="width:12%;"><b><font style="font-size:10pt;">Nama Dosen</font></b></td>
			<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_kelas(str_replace("-","_yz_",$_GET['id_kelas']),str_replace("-","_yz_",$_GET['id_ptk']),"nama_dosen"); ?></td>
			</tr>
			
			<tr>
			<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>Tahun Akademik</b></td>
			<td  style="width:25%;"><?php echo view_kelas(str_replace("-","_yz_",$_GET['id_kelas']),str_replace("-","_yz_",$_GET['id_ptk']),"tahun_akademik") ?></td>
			</tr>
			
			<tr>
				<td><font style="font-size:10pt;"><b>Mata Kuliah</b></td>
				<td><font style="font-size:10pt;"><?php echo view_kelas(str_replace("-","_yz_",$_GET['id_kelas']),str_replace("-","_yz_",$_GET['id_ptk']),"nm_matkul") ?></td>
			<tr>
			
			<tr>
				<td><font style="font-size:10pt;"><b>Program Studi</b></td>
				<td><font style="font-size:10pt;"><?php echo view_kelas(str_replace("-","_yz_",$_GET['id_kelas']),str_replace("-","_yz_",$_GET['id_ptk']),"nm_lemb") ?></td>
			<tr>
			</tr>
				<td><font style="font-size:10pt;"><b>Kelas</b></td>
				<td><?php echo view_kelas(str_replace("-","_yz_",$_GET['id_kelas']),str_replace("-","_yz_",$_GET['id_ptk']),"nm_kls") ?></td>
			</tr>
			
			<?php
				$sqlCekKelasGabungan=mysqli_query($connection,"SELECT id_gabungan FROM presensi_kelas_gabungan WHERE xid_kls='".str_replace("_yz_","-",$_GET['id_kelas'])."'");
				$dataKelasGabungan=mysqli_fetch_array($sqlCekKelasGabungan);
				$sqlCekKelasGabungan2=mysqli_query($connection,"SELECT xid_kls FROM presensi_kelas_gabungan WHERE id_gabungan='".$dataKelasGabungan['id_gabungan']."'");
				while($dataCekKelasGabungan2=mysqli_fetch_array($sqlCekKelasGabungan2)){
				if ($dataCekKelasGabungan2['xid_kls']!=(str_replace("_yz_","-",$_GET['id_kelas']))){
					echo"<tr>
					<td colspan='2'>
					<a href='dosen_data_kehadiran-".str_replace("-","_yz_",$dataCekKelasGabungan2['xid_kls'])."-".str_replace("-","_yz_",$_COOKIE['simpreskul_id_ptk']).".html'>
					<center>
					Presensi Mahasiswa 
					".view_kelas(str_replace("-","_yz_",$dataCekKelasGabungan2['xid_kls']),str_replace("-","_yz_",$_GET['id_ptk']),"nm_lemb")."
					".view_kelas(str_replace("-","_yz_",$dataCekKelasGabungan2['xid_kls']),str_replace("-","_yz_",$_GET['id_ptk']),"nm_kls")."
					</center></a></td>
					</tr>";
				}
				
				}

			
			?>
			
			
			
			</tr>
				 
				<td colspan="2"><center><a href="dosen_jurnal_perkuliahan-<?php echo str_replace("-","_yz_",$_GET['id_kelas']);?>-<?php echo str_replace("-","_yz_",$_GET['id_ptk']);?>.html">Jurnal Perkuliahan</center></td>
			</tr>
				
			</table>
			</div>
			<?php } ?>
			  
			  
                        </ul>
                    </div>
                </div>
            </nav>

            <div id="page-wrapper">
	    
                <div class="row">
                    <div class="col-lg-12">
                        <h4 class="page-header">
			<?php
				$sql=mysqli_query($connection,"SELECT*FROM wsia_dosen WHERE nidn='".$_COOKIE['simpreskul_nik']."'");
				$data=mysqli_fetch_array($sql);
				if ($_GET['module']!='dosen_data_kehadiran'){
					echo "Selamat Datang ".$data['nm_ptk'];
				}
			
			?>
			</h4>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               <?php
				if($_GET['module']=='dosen'){
				
					echo"Beranda";
					
					Header("Location:dosen_form_data_kehadiran.html");
				}else if(($_GET['module']=='dosen_form_data_kehadiran') OR ($_GET['module']=='dosen_data_kehadiran')){
				
					echo"Data Kehadiran";
				}else if($_GET['module']=='dosen_jurnal_perkuliahan'){
				
					echo"Jurnal Perkuliahan";
				}else if($_GET['module']=='dosen_proses_simpan_materi'){
				
					echo"Data Kehadiran";
				}else if($_GET['module']=='dosen_form_bukti_pembelajaran'){
				
					echo"Bukti Pembelajaran";
				}
			    ?>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div class="dataTable_wrapper">
                
		<?php
			if ($module == 'dosen'){
				include "dosen/beranda.php";
			}else{
				include "dosen/".$module.".php";
			}
		?>
		
		</div>
                                <!-- /.table-responsive -->
                             
                            </div>
                            <!-- /.panel-body -->
                        </div>
                        <!-- /.panel -->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>

		
		
		
            </div>
            <!-- /#page-wrapper -->

        </div>
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/metisMenu.min.js"></script>
        <script src="js/raphael.min.js"></script>
        <script src="js/morris.min.js"></script>
        <script src="js/morris-data.js"></script>
        <script src="js/startmin.js"></script>
    </body>
</html>