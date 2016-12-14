<?php
	//Require all the general functions
	require $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';

	//Checks if the user is logged in
	checkLogin();
	
	//First check if the user has authority
	checkAuthority('overzichtbekijken');
	
	//Set all the userdata to an array
	$userData = getUserData();
	
	//Store the number of unread messages otherwise
	//we have to make too much sql calls.
	$numberUnreadMessages = getNumberUnreadMessages($userData['username']);
	
	//Assign the connection to a local connection variable
	$connection = getConnection();
	
	//If there are unread messages set 'gelezen=1' on these messages
	//so the badge disappears.
	if($numberUnreadMessages > 0) {
		$queryMessages = $connection->prepare('update bericht set gelezen=1 where naar=:gebruikersnaam and gelezen=0');
		$queryMessages->bindParam(':gebruikersnaam', $userData['username']);
		$queryMessages->execute();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - Berichten</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	
	<script>
		$(document).ready(function() {
			$('#sendMessageForm').on('submit', function(event) {
                event.preventDefault();    
                $.ajax({
                    url: 'resources/add.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
						if(response.success) {
							$('#alert-success').fadeIn(500);
						} else {
							$('#alert-failed').fadeIn(500).delay(500).fadeOut(500); 
						}
                    },
                    error: function() {
                        alert('Er is een fout opgetreden!');
                    }
                });
			});
			
			$('#messagetable').on('click', '.removeMessage', function() {
				var messageId = $(this).attr('id');
				if(confirm('Weet u zeker dat u dit bericht wil verwijderen?')) {
					event.preventDefault();    
					$.ajax({
						url: 'resources/remove.ajax.php',
						type: 'post',
						data: 'action=removeMessage&token=<?php echo $_SESSION['token']; ?>&id='+messageId,
						dataType: 'json',
						success: function(response) {
							if(response.success) {		
								$('tr#'+messageId).remove();
							} else {
								alert('Kan het bericht niet verwijderen, probeer het nog eens.');  
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
					<?php if($userData['role'] <= getAuthorityLevel('leveranciersbeheren')) { ?>
					<li><a href="leveranciers.php"><i class="fa fa-truck"></i> Leveranciers</a></li>
					<?php } ?>
					<?php if($userData['role'] <= getAuthorityLevel('accountsbeheren')) { ?>
					<li class="active"><a href="#"><i class="fa fa-id-card"></i> Accounts beheren</a></li>
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
                            <li><a href="logout.php?token=<?php echo $_SESSION['token']; ?>"><i class="fa fa-power-off"></i> Uitloggen</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Overzicht berichten</h1>
                </div>
            </div>
			<div class="row">
				<div class="col-lg-12">
					<?php if($userData['role'] <= getAuthorityLevel('berichtversturen')) { ?>
					<a class="btn btn-primary" data-toggle="modal" data-target="#addMessageModal">Nieuw bericht</a>
					<?php } ?>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<table id="messagetable" class="table table-striped">
						<thead>
							<tr>
								<th>Van</th>
								<th>Datum</th>
								<th>Tijd</th>
								<th>Bericht</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$query = $connection->prepare('select g.voornaam, g.achternaam, b.id, b.datum, b.tijd, b.bericht from gebruiker g join bericht b on b.van=g.gebruikersnaam where b.naar=:gebruikersnaam order by b.datum, b.tijd desc');
								$query->bindParam(':gebruikersnaam', $userData['username']);
								$query->execute();
								if($query->rowCount()) {
									while($row = $query->fetch()) {
										echo '<tr id="'.filterData($row['id']).'">';
										echo '<td>'.filterData($row['voornaam']).' '.filterData($row['achternaam']).'</td>';
										echo '<td>'.filterData($row['datum']).'</td>';
										echo '<td>'.filterData($row['tijd']).'</td>';
										echo '<td>'.filterData($row['bericht']).'</td>';
										echo '<td><a class="removeMessage" id="'.filterData($row['id']).'"><i class="fa fa-trash-o fa-lg"></i></a></td>';
										echo '</tr>';
									}
								} else {
									echo '<tr><td>Er zijn geen berichten gevonden.</td></tr>';
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
        </div>
    </div>
    <!-- /#wrapper -->
	<div id="addMessageModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Nieuw bericht</h4>
				</div>
				<div class="modal-body">
					<div id="alert-failed" class="alert alert-danger no-display">
						<strong>Oeps!</strong> Controleer of alles is ingevuld
					</div>
					<div id="alert-success" class="alert alert-success no-display">
						<strong><i class="fa fa-thumbs-up fa-lg"></i></strong> Bericht verstuurd! U kunt dit venster sluiten.
					</div>
					<div class="row">
						<form id="sendMessageForm">
							<input type="hidden" name="action" value="sendMessage">
							<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Naar</label>
										<select class="form-control" name="naar" tabindex="1">
											<?php
												//Get all the active users from the database loggedin account
												//excluded.
												$userQuery = $connection->prepare('select gebruikersnaam, voornaam, achternaam from gebruiker where inactief=0 and gebruikersnaam!=:gebruikersnaam');
												$userQuery->bindParam(':gebruikersnaam', $userData['username']);
												$userQuery->execute();
												
												while($row = $userQuery->fetch()) {
													echo '<option value="'.$row['gebruikersnaam'].'">'.$row['voornaam'].' '.$row['achternaam'].'</option>';
												}
											?>
										</select>
									</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label>Bericht</label>
									<textarea class="form-control" rows="5" name="bericht" placeholder="Bericht" tabindex="2" required></textarea>
								</div>
							</div>
							<div class="col-lg-12">
								<button type="submit" class="btn btn-primary">Versturen</button>
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