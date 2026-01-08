<?php ob_start();


	if (($_COOKIE['simpreskul_nik']=='') OR ($_COOKIE['ruang']=='')){
		header("Location:$base_url");
	}
	
	?>
<html>
<head>
	<title>SIMPRESKUL - Politeknik Indonusa Surakarta</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/metisMenu.min.css" rel="stylesheet">
        <link href="css/bootstrap-social.css" rel="stylesheet">
        <link href="css/startmin.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" href="css/logo.png">
<?php
include"function.php";
?>
	
</head>
<body>


	<div id="wrapper">
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
                    <li><a href="http://www.poltekindonusa.ac.id"><i class="fa fa-home fa-fw"></i> Website</a></li>
                </ul>

                <ul class="nav navbar-right navbar-top-links">
                    
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i>
							<?php 
								$sql=mysql_query("SELECT*FROM simpeg_pegawai WHERE nik='".$_COOKIE['simpreskul_nik']."'");
								$data=mysql_fetch_array($sql);
								echo $data['nama'];
							
							?> 
							<b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                           
                          
                            <li><a href="proses_logout.html"><i class="fa fa-sign-out fa-fw"></i>Keluar</a>
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
                                
                            </li>
                            <li>
                                <a href="beranda.html"><i class="fa fa-dashboard fa-fw"></i> Beranda</a>
                            </li>
                            <li>
                                <a href="presensi_perkuliahan.html"><i class="fa fa-edit fa-fw"></i> Presensi Perkuliahan</a> 
                            </li>
							
                           
                           
                        </ul>
						<?php if($_GET['module']=='presensi'){?>
						<table class="table table-striped table-bordered table-hover" style="width:100%; cellpadding:0px;">
						<tr>
							<td style="width:12%;"><b><font style="font-size:10pt;">Nama Dosen</font></b></td>
							<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_jurnal($_COOKIE['id_jurnal'],"nama_dosen"); ?></td>
						</tr>
						<tr>
							<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>TA</b></td>
							<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_tahun_akademik(); ?></td>
						</tr>
						<tr>
							<td><font style="font-size:10pt;"><b>Mata Kuliah</b></td>
							<td><font style="font-size:10pt;"><?php echo  view_jurnal($_COOKIE['id_jurnal'],"nm_matkul"); ?></td>
						</tr>
						<tr>
							<td><font style="font-size:10pt;"><b>Ruang</b></td>
							<td><font style="font-size:10pt;"><?php echo $_COOKIE['ruang']; ?></td>
						</tr>
						<tr>
							<td><font style="font-size:10pt;"><b>Pertemuan Ke</b></td>
							<td><font style="font-size:10pt;"><?php echo view_jurnal($_COOKIE['id_jurnal'],"pertemuan"); ?></td>
						</tr>
						<tr>
							<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>Program Studi</b></td>
							<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_jurnal($_COOKIE['id_jurnal'],"prodi"); ?></td>
						</tr>
						<tr>
							<td><font style="font-size:10pt;"><b>Tanggal</b></td>
							<td><font style="font-size:10pt;"><font style="font-size:10pt;"><?php echo date("d-m-Y");  ?></td>
						</tr>
						<tr>
							<td><font style="font-size:10pt;"><b>Kelas</b></td>
							<td><font style="font-size:10pt;"><?php echo view_jurnal($_COOKIE['id_jurnal'],"kelas"); ?></td>
						</tr>
						<tr>
							<td colspan="2"><font style="font-size:10pt;"><?php echo view_jurnal($_COOKIE['id_jurnal'],"materi"); ?></td>
						</tr>
						<tr>
							
							
						</tr>
						</table>
						
						<?php } else if($_GET['module']=='rekap_presensi'){?>
						<table class="table table-striped table-bordered table-hover" style="width:100%; cellpadding:0px;">
						<tr>
							<td style="width:12%;"><b><font style="font-size:10pt;">Nama Dosen</font></b></td>
							<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_jurnal($_COOKIE['id_jurnal'],"nama_dosen"); ?></td>
						</tr>
						<tr>
							<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>TA</b></td>
							<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_tahun_akademik(); ?></td>
						</tr>
						<tr>
							<td><font style="font-size:10pt;"><b>Mata Kuliah</b></td>
							<td><font style="font-size:10pt;"><?php echo  view_jadwal($_GET['id_jadwal'],"nm_matkul"); ?></td>
						</tr>
						
						<tr>
							<td  style="width:12%;"><font style="font-size:10pt;"><font style="font-size:10pt;"><b>Program Studi</b></td>
							<td  style="width:25%;"><font style="font-size:10pt;"><?php echo view_jadwal($_GET['id_jadwal'],"prodi"); ?></td>
						</tr>
						
						<tr>
							<td><font style="font-size:10pt;"><b>Kelas</b></td>
							<td><font style="font-size:10pt;"><?php echo  view_jadwal($_GET['id_jadwal'],"kelas"); ?></td>
						</tr>
						<tr>
							<td colspan="2"><font style="font-size:10pt;"><?php echo view_jurnal($_COOKIE['id_jurnal'],"materi"); ?></td>
						</tr>
						<tr>
							
							
						</tr>
						</table>
						
						<?php } ?>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>

            <!-- Page Content -->
            <div id="page-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        &nbsp
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-12">

<form>
</form>
<span id="txtHint" style="font-family: Verdana, Geneva, sans-serif;"> </span>


<div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <?php echo judul_head($_GET['module']); ?>
                            </div>
                            <div class="panel-body">
                                <div class="dataTable_wrapper">
							
							<?php
							if ($_GET['module']!='halaman_utama'){
								include "pages/".$_GET['module'].".php";
							}else{
								include "pages/beranda.php";
							}
							?>

                                </div>
                               
                               
                            </div>
                            
                        </div>
                       
                    </div>
                  
                </div>
               
              


							

 </div>
                </div>
            </div>
        </div>
<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/metisMenu.min.js"></script>
		<script src="js/startmin.js"></script>
</body>
</html>
