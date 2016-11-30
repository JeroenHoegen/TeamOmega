<?php
	//Require all the general functions
	require $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';

	//Checks if the user is logged in
	checkLogin();
	
	//First check if the user has authority
	checkAuthority('overzichtbekijken');
	
	//Set all the userdata to an array
	$userData = getUserData();
	
	//Assign the connection to a local connection variable
	$connection = getConnection();
	
	//Get all the information of the customer from the provided id
	//returns array $customerData on success, returns to klanten.php
	//on failure
	if(isset($_GET['id'])) {
		$customerData = getCustomerDataById($_GET['id']);
		
		if(!is_array($customerData)) {
			header('Location: /omega/klanten.php');
			die();
		}
	} else {
		header('Location: /omega/klanten.php');
		die();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - Klant</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	
    <script>
        $(document).ready(function() {
            $('#updateCustomerForm').on('submit', function(event) {
                event.preventDefault();    
                $.ajax({
                    url: 'resources/update.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
						if(response.success) {
							$('html, body').animate({ scrollTop: 0 }, 'fast');
							$('#alert-success').fadeIn(500).delay(1000).fadeOut(500);
							$('h1#naam').text('Klant - '+response.voornaam+' '+response.achternaam);
						} else {
							$('#alert-failed').fadeIn(500).delay(1000).fadeOut(500);  
						}
                    },
                    error: function() {
                        alert('Er is een fout opgetreden!');
                    }
                });
            });
			
			$('#removeCustomer').click(function() {
				if(confirm('Weet u zeker dat u deze klant wil verwijderen?')) {
					event.preventDefault();    
					$.ajax({
						url: 'resources/remove.ajax.php',
						type: 'post',
						data: 'action=removeCustomer&id=<?php echo filterData($customerData[0]['id']); ?>',
						dataType: 'json',
						success: function(response) {
							if(response.success) {					
								window.location = 'klanten.php';
							} else {
								alert('Kan de klant niet verwijderen, probeer het nog eens.');  
							}
						},
						error: function() {
							alert('Er is een fout opgetreden!');
						}
					});
				}
			});
			
			$('#addReparatieForm').on('submit', function(event) {
				event.preventDefault();
                $.ajax({
                    url: 'resources/add.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
						if(response.success) {
							window.location = 'reparatie.php?id='+response.newreparatieid+'&returnid='+response.returnid;
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
                    <li class="active"><a href="klanten.php"><i class="fa fa-tasks"></i> Klanten</a></li>
					<li><a href="instellingen.php"><i class="fa fa-gear"></i> Instellingen</a></li>
					<?php if($userData['role'] <= getAuthorityLevel('accountsbeheren')) { ?>
					<li><a href="accounts.php"><i class="fa fa-id-card"></i> Accounts beheren</a></li>
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
                    <h1 id="naam">Klant - <?php echo filterData($customerData[0]['voornaam']).' '.filterData($customerData[0]['achternaam']); ?></h1>
                </div>
				<?php if($userData['role'] <= getAuthorityLevel('klantverwijderen')) { ?>
				<div class="col-lg-4 text-right top-btn">
					<a class="btn btn-danger" id="removeCustomer">Verwijderen</a>
				</div>
				<?php } ?>
            </div>
			<div id="alert-failed" class="alert alert-danger no-display">
				<strong>Oeps!</strong> Controleer of alle velden zijn ingevuld.
			</div>
			<div id="alert-success" class="alert alert-success no-display">
				<strong><i class="fa fa-thumbs-up fa-lg"></i></strong> De gegevens zijn met succes gewijzigd.
			</div>
			<div class="row">
				<div class="col-lg-12">
					<ul class="nav nav-tabs">
						<li class="active"><a data-toggle="tab" href="#klantgegevens">Klant gegevens</a></li>
						<li><a data-toggle="tab" href="#reparaties">Reparaties</a></li>
					</ul>
					<div class="tab-content">
						<div id="klantgegevens" class="tab-pane fade in active">
							<h3>Klant gegevens</h3>
							<div class="row">
								<form id="updateCustomerForm">
									<input type="hidden" name="action" value="updateCustomer">
									<input type="hidden" name="id" value="<?php echo filterData($customerData[0]['id']); ?>">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Voornaam</label>
											<input type="text" class="form-control" name="voornaam" placeholder="Voornaam" value="<?php echo filterData($customerData[0]['voornaam']); ?>" tabindex="1" required>
										</div>
										<div class="form-group">
											<label>Adres</label>
											<input type="text" class="form-control" name="adres" placeholder="Adres" value="<?php echo filterData($customerData[0]['adres']); ?>" tabindex="3" required>
										</div>
										<div class="form-group">
											<label>Postcode</label>
											<input type="text" class="form-control" name="postcode" placeholder="Postcode" value="<?php echo filterData($customerData[0]['postcode']); ?>" tabindex="5" required>
										</div>
										<div class="form-group">
											<label>E-mailadres</label>
											<input type="text" class="form-control" name="email" placeholder="E-mailadres" value="<?php echo filterData($customerData[0]['email']); ?>" tabindex="7" required>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Achternaam</label>
											<input type="text" class="form-control" name="achternaam" placeholder="Achternaam" value="<?php echo filterData($customerData[0]['achternaam']); ?>" tabindex="2" required>
										</div>
										<div class="form-group">
											<label>Woonplaats</label>
											<input type="text" class="form-control" name="woonplaats" placeholder="Woonplaats" value="<?php echo filterData($customerData[0]['woonplaats']); ?>" tabindex="4" required>
										</div>
										<div class="form-group">
											<label>Telefoonnummer</label>
											<input type="text" class="form-control" name="telefoonnummer" placeholder="Telefoonnummer" value="<?php echo filterData($customerData[0]['telefoonnummer']); ?>" tabindex="6" required>
										</div>
									</div>
									<div class="col-lg-12">
										<button type="submit" class="btn btn-primary">Opslaan</button>
									</div>
								</form>
							</div>
						</div>
						<div id="reparaties" class="tab-pane fade">
							<div class="row">
								<div class="col-lg-8">
									<h3>Reparaties</h3>
								</div>
								<div class="col-lg-4 text-right top-table-btn">
									<a class="btn btn-primary" data-toggle="modal" data-target="#addReparatieModal">Nieuwe reparatie</a>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Ingevoerd door</th>
												<th>Datum invoering</th>
												<th>Serienummer</th>
												<th>Omschrijving</th>
												<th>Garantie</th>
												<th>Kosten</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												$queryReparatie = $connection->prepare('select * from reparatie where klantid=:klantid'); 
												$queryReparatie->bindParam(':klantid', $customerData[0]['id']);
												$queryReparatie->execute();
												
												if($queryReparatie->rowCount()) {
													while($row = $queryReparatie->fetch()) {
														echo '<tr>';
														echo '<td>'.filterData($row['medewerker']).'</td>';
														echo '<td>'.filterData($row['startdatum']).'</td>';
														echo '<td>'.filterData($row['serienummer']).'</td>';
														echo '<td>'.substr(filterData($row['omschrijving']), 0, 30).'...'.'</td>';
														echo '<td>'.filterData($row['garantie']).'</td>';
														echo '<td>'.filterData($row['kosten']).'</td>';
														echo '<td><a href="reparatie.php?id='.filterData($row['id']).'&returnid='.$customerData[0]['id'].'"><i class="fa fa-pencil-square-o fa-lg"></i></a></td>';
														echo '</tr>';
													}
												} else {
													echo '<tr><td>Er zijn geen reparaties gevonden.</td></tr>';
												}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
    <!-- /#wrapper -->
	<div id="addReparatieModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Nieuwe reparatie</h4>
				</div>
				<div class="modal-body">
					<div id="alert-failed" class="alert alert-danger no-display">
						<strong>Oeps!</strong> Controleer of alle velden zijn ingevuld.
					</div>
					<div class="row">
						<form id="addReparatieForm">
							<input type="hidden" name="action" value="addReparatie">
							<input type="hidden" name="id" value="<?php echo filterData($customerData[0]['id']); ?>">
							<div class="col-lg-6">
								<div class="form-group">
									<label>Ingevoerd door</label>
									<input type="text" class="form-control" name="medewerker" value="<?php echo getUserData()['username'] ?>" tabindex="1" readonly>
								</div>
								<div class="form-group">
									<label>Omschrijving</label>
									<textarea class="form-control" rows="5" name="omschrijving" placeholder="Omschrijving" tabindex="3" required></textarea>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label>Datum invoering</label>
									<input type="text" class="form-control" name="startdatum" value="<?php echo date('d-m-Y'); ?>" placeholder="Datum invoering" tabindex="2" readonly>
								</div>
								<div class="form-group">
									<label>Serienummer</label>
									<input type="text" class="form-control" name="serienummer" placeholder="Serienummer" tabindex="4" required>
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
