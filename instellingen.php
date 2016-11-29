<?php
	//Require all the general functions
	require $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';

	//Checks if the user is logged in
	checkLogin();
	
	//First check if the user has authority
	checkAuthority('wachtwoordwijzigen');
	
	//Set all the userdata to an array
	$userData = getUserData();
	
	//Store the number of unread messages otherwise
	//we have to make to much sql calls.
	$numberUnreadMessages = getNumberUnreadMessages($userData['username']);
	
	//Assign the connection to a local connection variable
	$connection = getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - Instellingen</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	
    <script>
        $(document).ready(function() {
            $('#updatePasswordForm').on('submit', function(event) {
                event.preventDefault();    
                $.ajax({
                    url: 'resources/update.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
						if(response.success) {					
							$('#alert-success').fadeIn(500).delay(1000).fadeOut(500);
							$('input').val('');
						} else if(response.matchfail) {
							$('#alert-failed').fadeIn(500).delay(1000).fadeOut(500); 
						} else {
							$('#alert-failed-2').fadeIn(500).delay(1000).fadeOut(500);  
						}
                    },
                    error: function() {
                        alert('Er is een fout opgetreden!');
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Euro Discount Beheer</a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li><a href="index.php"><i class="fa fa-bullseye"></i> Overzicht</a></li>
                    <li><a href="klanten.php"><i class="fa fa-tasks"></i> Klanten</a></li>
					<li class="active"><a href="#"><i class="fa fa-gear"></i> Instellingen</a></li>
					<?php if($userData['role'] == getAuthorityLevel('accountsbeheren')) { ?>
					<li><a href="accounts.php"><i class="fa fa-id-card"></i> Accounts beheren</a></li>
					<li><a href="rollen-beheren.php"><i class="fa fa-briefcase"></i> Rollen beheren</a></li>
					<?php } ?>
                </ul>
                <ul class="nav navbar-nav navbar-right navbar-user">
                     <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo filterData($userData['username']); ?> <?php echo ($numberUnreadMessages > 0) ? '<span class="badge">'.$numberUnreadMessages.'</span>' : '' ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="berichten.php"><i class="fa fa-envelope"></i> Berichten <?php echo ($numberUnreadMessages > 0) ? '<span class="badge">'.$numberUnreadMessages.'</span>' : '' ?></a></li>
							<li class="divider"></li>
                            <li><a href="instellingen.php"><i class="fa fa-gear"></i> Instellingen</a></li>
                            <li class="divider"></li>
                            <li><a href="logout.php"><i class="fa fa-power-off"></i> Uitloggen</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Instellingen</h1>
                </div>
            </div>
			<div id="alert-failed" class="alert alert-danger no-display">
				<strong>Oeps!</strong> Het huidige wachtwoord klopt niet.
			</div>
			<div id="alert-failed-2" class="alert alert-danger no-display">
				<strong>Oeps!</strong> Het nieuwe wachtwoord komt niet overeen.
			</div>
			<div id="alert-success" class="alert alert-success no-display">
				<strong><i class="fa fa-thumbs-up fa-lg"></i></strong> De gegevens zijn met succes gewijzigd.
			</div>
			<div class="row">
				<div class="col-lg-12">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#gegevens">Algemeen</a></li>
					</ul>
					<div class="tab-content">
						<div id="gegevens" class="tab-pane fade in active">
							<h3></h3>
							<div class="row">
								<form id="updatePasswordForm">
									<input type="hidden" name="action" value="updatePassword">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Huidige wachtwoord</label>
											<input type="password" class="form-control" name="huidigwachtwoord" placeholder="Huidige wachtwoord" tabindex="1" required>
										</div>
										<div class="form-group">
											<label>Nieuw wachtwoord</label>
											<input type="password" class="form-control" name="nieuwwachtwoord" placeholder="Nieuw wachtwoord" tabindex="2" required>
										</div>
										<div class="form-group">
											<label>Bevestig wachtwoord</label>
											<input type="password" class="form-control" name="bevestigwachtwoord" placeholder="Bevestig wachtwoord" tabindex="3" required>
										</div>
									</div>
									<div class="col-lg-12">
										<button type="submit" class="btn btn-primary">Opslaan</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
    <!-- /#wrapper -->
</body>
</html>
