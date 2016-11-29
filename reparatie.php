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
	
	//Get all the information of the reparatie from the provided id
	//returns array $reparatieData on success, returns to klant.php?id=(returnid)
	//on failure
	if(isset($_GET['id']) && isset($_GET['returnid'])) {
		$query = $connection->prepare('select * from reparatie where id=:id');
		$query->bindParam(':id', $_GET['id']);
		$query->execute();
			
		if($query->rowCount() > 0) {
			$reparatieData = $query->fetchAll();
		} else {
			header('Location: /omega/klant.php?id='.$_GET['returnid']);
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
    <title>Euro Discount - Reparatie</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	
    <script>
        $(document).ready(function() {
            $('#updateReparatieForm').on('submit', function(event) {
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
							
							if(response.newstatus != null) {
								window.location = window.location;
								$('#statusupdates table').append('<tr id="'+response.id+'"><td>'+response.username+'</td><td>'+response.date+'</td><td>'+response.time+'</td><td>'+response.statusupdate+'</td><td><a class="removeStatus" id="'+response.id+'"><i class="fa fa-trash-o fa-lg"></i></a></td></tr>');
								$('#statusupdate').val('');
							}
						} else if(response.statusupdate != null) {
							$('#alert-success').fadeIn(500).delay(1000).fadeOut(500); 
							$('#statusupdates table').append('<tr id="'+response.id+'"><td>'+response.username+'</td><td>'+response.date+'</td><td>'+response.time+'</td><td>'+response.statusupdate+'</td><td><a class="removeStatus" id="'+response.id+'"><i class="fa fa-trash-o fa-lg"></i></a></td></tr>');
							$('#statusupdate').val('');
						} else {
							$('#alert-failed').fadeIn(500).delay(1000).fadeOut(500);
						}
                    },
                    error: function() {
                        alert('Er is een fout opgetreden!');
                    }
                });
            });
			
			$('#removeReparatie').click(function() {
				if(confirm('Weet u zeker dat u deze reparatie wil verwijderen?')) {
					event.preventDefault();    
					$.ajax({
						url: 'resources/remove.ajax.php',
						type: 'post',
						data: 'action=removeReparatie&id=<?php echo filterData($reparatieData[0]['id']); ?>',
						dataType: 'json',
						success: function(response) {
							if(response.success) {					
								window.location = 'klant.php?id=<?php echo $_GET['returnid'];?>';
							} else {
								alert('Kan de reparatie niet verwijderen, probeer het nog eens.');  
							}
						},
						error: function() {
							alert('Er is een fout opgetreden!');
						}
					});
				}
			});
		
			$('#statusupdates').on('click', '.removeStatus', function() {
				var statusId = $(this).attr('id');
				if(confirm('Weet u zeker dat u deze status wil verwijderen?')) {
					event.preventDefault();    
					$.ajax({
						url: 'resources/remove.ajax.php',
						type: 'post',
						data: 'action=removeStatus&id='+statusId,
						dataType: 'json',
						success: function(response) {
							if(response.success) {		
								$('tr#'+statusId).remove();
							} else {
								alert('Kan de status niet verwijderen, probeer het nog eens.');  
							}
						},
						error: function() {
							alert('Er is een fout opgetreden!');
						}
					});
				}
			});
			
			$('#newstatus').change(function() {
				if($(this).val() == 2) {
					$('#emailversturen').show();
				} else {
					$('#emailversturen').hide();
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
                    <li class="active"><a href="klanten.php"><i class="fa fa-tasks"></i> Klanten</a></li>
					<li><a href="instellingen.php"><i class="fa fa-gear"></i> Instellingen</a></li>
					<?php if($userData['role'] == getAuthorityLevel('accountsbeheren')) { ?>
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
                    <h1 id="naam">Reparatie - <?php echo filterData($reparatieData[0]['id']); ?></h1>
                </div>
				<?php if($userData['role'] <= getAuthorityLevel('reparatieverwijderen')) { ?>
				<div class="col-lg-4 text-right top-btn">
					<a class="btn btn-danger" id="removeReparatie">Verwijderen</a>
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
						<li class="active"><a data-toggle="tab" href="#reparatiegegevens">Reparatie gegevens</a></li>
						<li><a data-toggle="tab" href="#statusupdates">Status</a></li>
					</ul>
					<div class="tab-content">
						<div id="reparatiegegevens" class="tab-pane fade in active">
							<h3>Reparatie gegevens</h3>
							<div class="row">
								<form id="updateReparatieForm">
									<input type="hidden" name="action" value="updateReparatie">
									<input type="hidden" name="id" value="<?php echo filterData($reparatieData[0]['id']); ?>">
									<input type="hidden" name="customerid" value="<?php echo filterData($_GET['returnid']); ?>">
									<input type="hidden" name="status" value="<?php echo filterData($reparatieData[0]['status'], true); ?>">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Status:</label>
											<select class="form-control" name="newstatus" id="newstatus" tabindex="1" <?php if($reparatieData[0]['status'] == 2) {echo ' disabled';} ?>>
												<option value="0" <?php if($reparatieData[0]['status'] == 0) {echo ' selected';} ?>>Open</option>
												<option value="1" <?php if($reparatieData[0]['status'] == 1) {echo ' selected';} ?>>Wordt aan gewerkt</option>
												<option value="2" <?php if($reparatieData[0]['status'] == 2) {echo ' selected';} ?>>Afgerond</option>
											</select>
										</div>
										<div class="form-group">
											<label>Garantie</label>
											<select class="form-control" name="garantie" tabindex="2" <?php if($reparatieData[0]['status'] == 2) {echo ' disabled';} ?>>
												<option value="0">nee</option>
												<option value="1">ja</option>
											</select>
										</div>
										<div class="form-group">
											<label>Omschrijving</label>
											<textarea class="form-control" rows="5" name="omschrijving" placeholder="Omschrijving" tabindex="4" required <?php if($reparatieData[0]['status'] == 2) {echo ' disabled';} ?>><?php echo filterData($reparatieData[0]['omschrijving']); ?></textarea>
										</div>
										<div class="form-group no-display" <?php if($reparatieData[0]['emailverstuurd'] == 0) { ?> <?php } ?> id="emailversturen">
											<label>E-mail versturen naar klant:</label>
											<select class="form-control" name="emailversturen" tabindex="6" <?php if($reparatieData[0]['emailverstuurd'] != 0 || $reparatieData[0]['status'] == 2) {echo ' disabled';} ?>>
												<option value="0" <?php if($reparatieData[0]['emailverstuurd'] == 0) {echo ' selected';} ?>>nee</option>
												<option value="1" <?php if($reparatieData[0]['emailverstuurd'] == 1) {echo ' selected';} ?>>ja</option>
											</select>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Kosten</label>
											<input type="text" class="form-control" name="kosten" placeholder="Kosten" value="<?php echo filterData($reparatieData[0]['kosten']); ?>" tabindex="3" <?php if($reparatieData[0]['status'] == 2) {echo ' disabled';} ?>>
										</div>
										<div class="form-group">
											<label>Serienummer</label>
											<input type="text" class="form-control" name="serienummer" placeholder="Serienummer" value="<?php echo filterData($reparatieData[0]['serienummer']); ?>" tabindex="5" <?php if($reparatieData[0]['status'] == 2) {echo ' disabled';} ?>>
										</div>
										<div class="form-group">
											<label>Status update:</label>
											<textarea class="form-control" rows="5" name="statusupdate" id="statusupdate" placeholder="Status update" tabindex="7" <?php if($reparatieData[0]['status'] == 2) {echo ' disabled';} ?>></textarea>
										</div>
									</div>
									<div class="col-lg-12">
									<?php if($reparatieData[0]['status'] != 2) { ?>
										<button type="submit" class="btn btn-primary">Opslaan</button>
									<?php } ?>
									</div>
								</form>
							</div>
						</div>
						<div id="statusupdates" class="tab-pane fade in">
							<div class="row">
								<div class="col-lg-12">
									<h3>Status updates</h3>
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Ingevoerd door</th>
												<th>Datum</th>
												<th>Tijd</th>
												<th>Omschrijving</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php 
												$queryStatus = $connection->prepare('select * from updates where reparatieid=:reparatieid'); 
												$queryStatus->bindParam(':reparatieid', $reparatieData[0]['id']);
												$queryStatus->execute();
												
												if($queryStatus->rowCount()) {
													while($row = $queryStatus->fetch()) {
														echo '<tr id="'.filterData($row['id']).'">';
														echo '<td>'.filterData($row['medewerker']).'</td>';
														echo '<td>'.filterData($row['datum']).'</td>';													
														echo '<td>'.filterData($row['tijd']).'</td>';													
														echo '<td>'.filterData($row['omschrijving']).'</td>';													
														if(filterData($row['verwijderbaar']) == 1 && $row['medewerker'] == $userData['username'] && $reparatieData[0]['status'] != 2) {
															echo '<td><a class="removeStatus" id="'.filterData($row['id']).'"><i class="fa fa-trash-o fa-lg"></i></a></td>';							
														} else {
															echo '<td></td>';
														}
														echo '</tr>';
													}
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
</body>
</html>
