<?php ob_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SIMPRESKUL - Politeknik Indonusa Surakarta</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/metisMenu.min.css" rel="stylesheet">
        <link href="css/startmin.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" href="css/logo.png">
    </head>
    <body>
    <?php
	
	setcookie("simpreskul_admin","", time() + 3600 * 24);
	setcookie("nama_pengguna","", time() + 3600 * 24);
	setcookie("tahun_akaemik","", time() + 3600 * 24);
	setcookie("ruang","", time() + 3600 * 24);
    
    ?>
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
		
                    <div class="login-panel panel panel-default">
		    
                        <div class="panel-heading">
				

                            <h3 class="panel-title">Silahkan Login</h3>
                        </div>
                        <div class="panel-body">
                            <form role="form" action="admin_proses_login.html" method="POST">
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Username" name="username" type="text" autofocus>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                    </div>
                                   
                                    <input type="submit" class="btn btn-lg btn-success btn-block" value="Login">
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery -->
        <script src="js/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="js/bootstrap.min.js"></script>

        <!-- Metis Menu Plugin JavaScript -->
        <script src="js/metisMenu.min.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="js/startmin.js"></script>

    </body>
</html>
