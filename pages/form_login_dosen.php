<?php
	/*if (($_COOKIE['ruang']=='')){
		header("Location:$base_url");
	}*/
?>

<html lang="en">
<head>
	<title>Form Login Dosen</title>
	<link href="medicio/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="medicio/css/style.css" rel="stylesheet">
	<link id="t-colors" href="medicio/color/green.css" rel="stylesheet">
	<link rel="shortcut icon" href="css/logo.png">

</head>
<body id="page-top" data-spy="scroll" data-target=".navbar-custom">

				
<div id="wrapper">
	
    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
		<div class="top-area">
			<div class="container">
				<div class="row">
					<div class="col-sm-6 col-md-6">
					<p class="bold text-left">POLITEKNIK INDONUSA SURAKARTA </p>
					</div>
					<div class="col-sm-6 col-md-6">
					<p class="bold text-right">Selamat Datang di <?php echo $_COOKIE['ruang']; ?></p>
					</div>
				</div>
			</div>
		</div>
       </nav>
	
<!-- Section: intro -->
	<br>
	<br>
	<br>
	<br>
  
			<div class="container">
			
				<div class="row">
				<div class="col-lg-6">
						<div>
						<div >
							<div class="panel panel-skin" >
							<div class="panel-heading">
									<h3 class="panel-title"><span class="fa fa-pencil-square-o"></span> <center>Silahkan Login Dosen</center></small></h3>
									</div>
									<div class="panel-body" style="height:240px;">
									
						<form method="POST" action="proses_login_dosen.html"><input value="" style="width:0px; height:0px;" type="text" name="rfid_dosen" autocomplete="off" autofocus/></form>						

						<span id="txtHint" style="font-family: Verdana, Geneva, sans-serif;"> </span>	
						<center>TAP Kartu RFID Dosen Pada RFID Reader</center><br>
								</div>
							</div>				
						
						</div>
						</div>
				</div>
				
				<div class="col-lg-6" >
						<div class="form-wrapper" >
						<div>
							<div class="panel panel-skin">
							
						<div class="panel-body" style="height:296px;">
									
							<font style="font-size:30px;"><b>Sistem Informasi Presensi Perkuliahan</b></font>
					<br>Provide <span class="color">best quality system</span> for you
					
					
							<br><br><span class="fa fa-check fa-2x icon-success"></span> <span class="list"><strong>RFID Technology</strong><br />Metode identifikasi menggunakan sarana yang disebut RFID untuk menyimpan dan mengambil data jarak jauh.</span>
							
						</div>
							</div>				
						
						</div>
						</div>
				</div>
				
					
										
				</div>	


<div class="col-lg-12" style="border:1px solid #DDDDDD;background-color:#F0F0F0; border-radius: 4px;">
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			</div>				
			</div>
				

	
	
	
	</div>
</body>

</html>
