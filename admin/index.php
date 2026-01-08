<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../config/Security.php';

// Check authentication FIRST before any output
if (empty($_COOKIE['simpreskul_admin'])) {
    header('Location: ' . $base_url . '/login_admin.html');
    exit;
}

// Ensure a module value to avoid undefined index warnings
$module = isset($_GET['module']) ? $_GET['module'] : 'admin';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SIMPRESKUL - Politeknik Indonusa Surakarta</title>
        <link href="<?php echo $base_url;?>/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $base_url;?>/css/metisMenu.min.css" rel="stylesheet">
        <link href="<?php echo $base_url;?>/css/timeline.css" rel="stylesheet">
        <link href="<?php echo $base_url;?>/css/startmin.css" rel="stylesheet">
        <link href="<?php echo $base_url;?>/css/morris.css" rel="stylesheet">
        <link href="<?php echo $base_url;?>/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    	<link rel="shortcut icon" href="<?php echo $base_url;?>/css/logo.png">
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
        </body>
    </html>
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
                    <a class="navbar-brand" href="index.html">SIMPRESKUL</a>
                </div>

                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <ul class="nav navbar-nav navbar-left navbar-top-links">
                    <li><a href="http://www.poltekindounusa.ac.id"><i class="fa fa-home fa-fw"></i> Website</a></li>
                </ul>

                <ul class="nav navbar-right navbar-top-links">
                   
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> Administrator <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            
                            <li><a href="<?php echo $base_url;?>/admin/logout.php"><i class="fa fa-sign-out fa-fw"></i> Keluar</a>
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
                            <li>
                                <a href="admin.html" class="active"><i class="fa fa-dashboard fa-fw"></i> Beranda</a>
                            </li>
                           
                           
                            <li>
                                <a href="admin_form_rekap_presentase_kehadiran.html"><i class="fa fa-edit fa-fw"></i> Presentase Kehadiran</a>
                            </li>
			    
							<li>
                               <a href="admin_form_data_kehadiran.html"><i class="fa fa-edit fa-fw"></i> Data Kehadiran</a>
                            </li>
							
							<li>
                               <a href="admin_form_data_monitoring.html"><i class="fa fa-edit fa-fw"></i> Monitoring Kehadiran</a>
                            </li>

				 <!--
			   <li>
                                <a href="admin_form_rekap_jurnal.html"><i class="fa fa-edit fa-fw"></i> Rekap Jurnal</a>
                            </li>
							<li>
                                <a href="admin_form_rekap_kehadiran.html"><i class="fa fa-edit fa-fw"></i> Pemantauan Perkuliahan Online (Selama KLB Kota Surakarta)</a>
                            </li>
				-->
                              <?php if($module=='admin_data_kehadiran'){?>  
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
					<a href='admin_data_kehadiran-".str_replace("-","_yz_",$dataCekKelasGabungan2['xid_kls'])."-".str_replace("-","_yz_",$_GET['id_ptk']).".html'>
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
				 
				<td colspan="2"><center><a href="admin_jurnal_perkuliahan-<?php echo str_replace("-","_yz_",$_GET['id_kelas']);?>-<?php echo str_replace("-","_yz_",$_GET['id_ptk']);?>.html">Jurnal Perkuliahan</center></td>
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
                        <h4 class="page-header"></h4>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               <?php
                if($module=='admin'){
                    echo "Beranda";
                } else if($module=='admin_form_rekap_presentase_kehadiran'){
                    echo "Presentase Kehadiran";
                } else if($module=='admin_jurnal_perkuliahan'){
                    echo "Jurnal Perkuliahan";
                } else if(($module=='admin_form_data_kehadiran') || ($module=='admin_data_kehadiran')){
                    echo "Data Kehadiran";
                }
            ?>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div class="dataTable_wrapper">
                
		<?php
			
            if ($module === 'admin') {
                include __DIR__ . '/beranda.php';
            } else {
                $safe = preg_replace('/[^a-zA-Z0-9_\-]/', '', $module);
                $file = __DIR__ . '/' . $safe . '.php';
                if (is_file($file)) {
                    include $file;
                } else {
                    echo '<div class="alert alert-warning">Module not found.</div>';
                    }
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
