<?php
	//Require all the general functions
	require $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';

	//Checks if the user is logged in
	checkLogin();
	
	//First check if the user has authority
	checkAuthority('accountsbeheren');
	
	//Set all the userdata to an array
	$userData = getUserData();
	
	//Assign the connection to a local connection variable
	$connection = getConnection();
	
	//Get all the information of the functieroles and store it in the 
	//array $roleInformation
	$query = $connection->prepare('select * from functierol');
	$query->execute();
	
	if($query->rowCount() > 0) {
		$roleInformation = $query->fetchAll(PDO::FETCH_KEY_PAIR);
	} else {
		header('Location: /omega/index.php');
		die();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - Rollen beheren</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	
    <script>
        $(document).ready(function() {
            $('#updateRoleForm').on('submit', function(event) {
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
					<li><a href="instellingen.php"><i class="fa fa-gear"></i> Instellingen</a></li>
					<?php if($userData['role'] == getAuthorityLevel('accountsbeheren')) { ?>
					<li><a href="accounts.php"><i class="fa fa-id-card"></i> Accounts beheren</a></li>
					<li class="active"><a href="#"><i class="fa fa-briefcase"></i> Rollen beheren</a></li>
					<?php } ?>
                </ul>
                <ul class="nav navbar-nav navbar-right navbar-user">
                     <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo filterData($userData['username']); ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><i class="fa fa-gear"></i> Instellingen</a></li>
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
                    <h1>Rollen beheren</h1>
                </div>
            </div>
			<div id="alert-failed" class="alert alert-danger" style="display: none;">
				<strong>Oeps!</strong> Er is iets mis gegaan, probeer het nog eens.
			</div>
			<div id="alert-success" class="alert alert-success" style="display: none;">
				<strong><i class="fa fa-thumbs-up fa-lg"></i></strong> De gegevens zijn met succes gewijzigd.
			</div>
			<div class="row">
				<div class="col-lg-12">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#rolgegevens">Rollen</a></li>
					</ul>
					<div class="tab-content">
						<div id="rolgegevens" class="tab-pane fade in active">
							<h3>Selecteer de minimale rol</h3>
							<div class="row">
								<form id="updateRoleForm">
									<input type="hidden" name="action" value="updateRole">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Klant toevoegen</label>
											<select class="form-control" name="klanttoevoegen" tabindex="1">
												<option value="3" <?php if($roleInformation['klanttoevoegen'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($roleInformation['klanttoevoegen'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($roleInformation['klanttoevoegen'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
										</div>
										<div class="form-group">
											<label>Klant verwijderen</label>
											<select class="form-control" name="klantverwijderen" tabindex="3">
												<option value="3" <?php if($roleInformation['klantverwijderen'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($roleInformation['klantverwijderen'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($roleInformation['klantverwijderen'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
										</div>
										<div class="form-group">
											<label>Reparatie toevoegen</label>
											<select class="form-control" name="reparatietoevoegen" tabindex="5">
												<option value="3" <?php if($roleInformation['reparatietoevoegen'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($roleInformation['reparatietoevoegen'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($roleInformation['reparatietoevoegen'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
										</div>
										<div class="form-group">
											<label>Reparatie verwijderen</label>
											<select class="form-control" name="reparatieverwijderen" tabindex="7">
												<option value="3" <?php if($roleInformation['reparatieverwijderen'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($roleInformation['reparatieverwijderen'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($roleInformation['reparatieverwijderen'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
										</div>
										<div class="form-group">
											<label>Eigen wachtwoord wijzigen</label>
											<select class="form-control" name="wachtwoordwijzigen" tabindex="9">
												<option value="3" <?php if($roleInformation['wachtwoordwijzigen'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($roleInformation['wachtwoordwijzigen'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($roleInformation['wachtwoordwijzigen'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Klant bewerken</label>
											<select class="form-control" name="klantbewerken" tabindex="2">
												<option value="3" <?php if($roleInformation['klantbewerken'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($roleInformation['klantbewerken'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($roleInformation['klantbewerken'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
										</div>
										<div class="form-group">
											<label>Klanten exporteren</label>
											<select class="form-control" name="klantenexporteren" tabindex="4">
												<option value="3" <?php if($roleInformation['klantenexporteren'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($roleInformation['klantenexporteren'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($roleInformation['klantenexporteren'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
										</div>
										<div class="form-group">
											<label>Reparatie bewerken</label>
											<select class="form-control" name="reparatiebewerken" tabindex="6">
												<option value="3" <?php if($roleInformation['reparatiebewerken'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($roleInformation['reparatiebewerken'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($roleInformation['reparatiebewerken'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
										</div>
										<div class="form-group">
											<label>Accounts beheren</label>
											<select class="form-control" name="accountsbeheren" tabindex="8">
												<option value="3" <?php if($roleInformation['accountsbeheren'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($roleInformation['accountsbeheren'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($roleInformation['accountsbeheren'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
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
