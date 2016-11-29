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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - Accounts beheren</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	
	<script>
		$(document).ready(function() {
			$('#searchInput').keyup(function() {
				var value = this.value.toLowerCase().trim();

				$('table').find("tr").each(function(index) {
					if (index === 0) return;

					var tdCheck = false;
					$(this).find('td').each(function () {
						tdCheck = tdCheck || $(this).text().toLowerCase().trim().indexOf(value) !== -1;
					});

					$(this).toggle(tdCheck);

				});
			});
			
			$('#addUserForm').on('submit', function(event) {
                event.preventDefault();    
                $.ajax({
                    url: 'resources/add.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
						if(response.success) {
							window.location = 'accounts.php';
						} else {
							$('#alert-failed').fadeIn(500); 
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
					<li class="active"><a href="#"><i class="fa fa-id-card"></i> Accounts beheren</a></li>
					<li><a href="rollen-beheren.php"><i class="fa fa-briefcase"></i> Rollen beheren</a></li>
					<?php } ?>
                </ul>
                <ul class="nav navbar-nav navbar-right navbar-user">
                     <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo filterData($userData['username']); ?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
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
                    <h1>Overzicht gebruikers</h1>
                </div>
				<div class="col-lg-4 User-search">
					<input type="text" class="form-control" id="searchInput" placeholder="Zoek gebruiker" />
				</div>
            </div>
			<div class="row">
				<div class="col-lg-12">
					<a class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">Nieuwe gebruiker</a>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Gebruikersnaam</th>
								<th>Voornaam</th>
								<th>Achternaam</th>
								<th>Rol</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$query = $connection->prepare('select g.gebruikersnaam, g.voornaam, g.achternaam, r.naam from gebruiker g join rol r on g.rol=r.id where g.inactief=0 order by g.rol');
								$query->execute();
								if($query->rowCount()) {
									while($row = $query->fetch()) {
										echo '<tr>';
										echo '<td>'.filterData($row['gebruikersnaam']).'</td>';
										echo '<td>'.filterData($row['voornaam']).'</td>';
										echo '<td>'.filterData($row['achternaam']).'</td>';
										echo '<td>'.filterData($row['naam']).'</td>';
										echo '<td><a href="account-beheren.php?gebruikersnaam='.$row['gebruikersnaam'].'"><i class="fa fa-pencil-square-o fa-lg"></i></a></td>';
										echo '</tr>';
									}
								} else {
									echo '<tr><td>Er zijn geen gebruikeren gevonden.</td></tr>';
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
        </div>
    </div>
    <!-- /#wrapper -->
	<div id="addUserModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Nieuwe gebruiker</h4>
				</div>
				<div class="modal-body">
					<div id="alert-failed" class="alert alert-danger no-display">
						<strong>Oeps!</strong> Controleer of de gebruikersnaam uniek is
					</div>
					<div class="row">
						<form id="addUserForm">
							<input type="hidden" name="action" value="addUser">
							<div class="col-lg-6">
								<div class="form-group">
									<label>Gebruikersnaam</label>
									<input type="text" class="form-control" name="gebruikersnaam" placeholder="Gebruikersnaam" tabindex="1" required>
								</div>
								<div class="form-group">
									<label>Voornaam</label>
									<input type="text" class="form-control" name="voornaam" placeholder="Voornaam" tabindex="3" required>
								</div>
								<div class="form-group">
									<label>Rol</label>
										<select class="form-control" name="rol" id="rol" tabindex="5">
											<option value="3">Stagair</option>
											<option value="2">Medewerker</option>
											<option value="1">Beheerder</option>
										</select>
									</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label>Wachtwoord</label>
									<input type="password" class="form-control" name="wachtwoord" placeholder="Wachtwoord" tabindex="2" required>
								</div>
								<div class="form-group">
									<label>Achternaam</label>
									<input type="text" class="form-control" name="achternaam" placeholder="Achternaam" tabindex="4" required>
								</div>
							</div>
							<div class="col-lg-12">
								<button type="submit" class="btn btn-primary">Toevoegen</button>
							</div>
						</form>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
