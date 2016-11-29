<?php
	//Require all the general functions
	require $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';

	//Checks if the user is logged in
	checkLogin();
	
	//First check if the user has authority
	checkAuthority('accountsbeheren');
	
	//Set all the userdata to an array
	$userData = getUserData();
	
	//Store the number of unread messages otherwise
	//we have to make too much sql calls.
	$numberUnreadMessages = getNumberUnreadMessages($userData['username']);
	
	//Assign the connection to a local connection variable
	$connection = getConnection();
	
	//Get all the information of the user from the provided username
	//returns array $userDataByUsername on success, returns to accounts.php
	//on failure
	if(isset($_GET['gebruikersnaam'])) {
		$query = $connection->prepare('select * from gebruiker where gebruikersnaam=:gebruikersnaam and inactief=0');
		$query->bindParam(':gebruikersnaam', $_GET['gebruikersnaam']);
		$query->execute();
		
		if($query->rowCount() > 0) {
			$userDataByUsername = $query->fetchAll();
		} else {
			header('Location: /omega/accounts.php');
			die();
		}
	} else {
		header('Location: /omega/accounts.php');
		die();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - Account beheren</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	
    <script>
        $(document).ready(function() {
			$('#updateUserForm').on('submit', function(event) {
                event.preventDefault();    
                $.ajax({
                    url: 'resources/update.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
						if(response.success) {
							$('#alert-success').fadeIn(500).delay(1000).fadeOut(500);
						} else {
							$('#alert-failed').fadeIn(500).delay(1000).fadeOut(500);  
						}
                    },
                    error: function() {
                        alert('Er is een fout opgetreden!');
                    }
                });
            });

			$('#resetPassword').click(function() {
				if(confirm('Weet u zeker dat u het wachtwoord van deze gebruiker wil resetten naar "wachtwoord"?')) {
					event.preventDefault();    
					$.ajax({
						url: 'resources/remove.ajax.php',
						type: 'post',
						data: 'action=removePassword&gebruikersnaam=<?php echo filterData($userDataByUsername[0]['gebruikersnaam']); ?>',
						dataType: 'json',
						success: function(response) {
							if(response.success) {					
							$('#alert-success-password').fadeIn(500).delay(1000).fadeOut(500);
							} else {
								alert('Kan het wachtwoord niet resetten, mogelijk is het wachtwoord al "wachtwoord".');  
							}
						},
						error: function() {
							alert('Er is een fout opgetreden!');
						}
					});
				}
			});
			
			$('#removeUser').click(function() {
				if(confirm('Weet u zeker dat u deze gebruiker wil verwijderen?')) {
					event.preventDefault();    
					$.ajax({
						url: 'resources/remove.ajax.php',
						type: 'post',
						data: 'action=removeUser&gebruikersnaam=<?php echo filterData($userDataByUsername[0]['gebruikersnaam']); ?>',
						dataType: 'json',
						success: function(response) {
							if(response.success) {					
								window.location = 'accounts.php';
							} else {
								alert('Kan de gebruiker niet verwijderen, probeer het nog eens.');  
							}
						},
						error: function() {
							alert('Er is een fout opgetreden!');
						}
					});
				}
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
					<li class="active"><a href="accounts.php"><i class="fa fa-id-card"></i> Accounts beheren</a></li>
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
                <div class="col-lg-8">
                    <h1 id="naam">Gebruiker - <?php echo $userDataByUsername[0]['gebruikersnaam'] ?></h1>
                </div>
				<div class="col-lg-4 text-right top-btn">
					<a class="btn btn-danger" id="resetPassword">Reset wachtwoord</a>
					<a class="btn btn-danger" id="removeUser">Verwijderen</a>
				</div>
            </div>
			<div id="alert-failed" class="alert alert-danger no-display">
				<strong>Oeps!</strong> Controleer of alle velden zijn ingevuld.
			</div>
			<div id="alert-success" class="alert alert-success no-display">
				<strong><i class="fa fa-thumbs-up fa-lg"></i></strong> De gegevens zijn met succes gewijzigd.
			</div>
			<div id="alert-success-password" class="alert alert-success no-display">
				<strong><i class="fa fa-thumbs-up fa-lg"></i></strong> Het wachtwoord is gereset naar "wachtwoord".
			</div>
			<div class="row">
				<div class="col-lg-12">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#gebruikergegevens">Instellingen</a></li>
					</ul>
					<div class="tab-content">
						<div id="gebruikergegevens" class="tab-pane fade in active">
							<h3>Gebruiker gegevens</h3>
							<div class="row">
								<form id="updateUserForm">
									<input type="hidden" name="action" value="updateUser">
									<input type="hidden" name="gebruikersnaam" value="<?php echo $userDataByUsername[0]['gebruikersnaam']; ?>">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Voornaam</label>
											<input type="text" class="form-control" name="voornaam" value="<?php echo $userDataByUsername[0]['voornaam']; ?>" placeholder="Voornaam" tabindex="1" required>
										</div>
										<div class="form-group">
											<label>Rol</label>
											<select class="form-control" name="rol" id="rol" tabindex="3">
												<option value="3" <?php if($userDataByUsername[0]['rol'] == 3) {echo ' selected';} ?>>Stagair</option>
												<option value="2" <?php if($userDataByUsername[0]['rol'] == 2) {echo ' selected';} ?>>Medewerker</option>
												<option value="1" <?php if($userDataByUsername[0]['rol'] == 1) {echo ' selected';} ?>>Beheerder</option>
											</select>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Achternaam</label>
											<input type="text" class="form-control" name="achternaam" value="<?php echo $userDataByUsername[0]['achternaam']; ?>" placeholder="Achternaam" tabindex="2" required>
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
