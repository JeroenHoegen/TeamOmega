<?php
	//Require all the general functions
	require $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';

	//Checks if the user is logged in
	checkLogin();
	
	//First check if the user has authority
	checkAuthority('leveranciersbeheren');
	
	//Set all the userdata to an array
	$userData = getUserData();
	
	//Store the number of unread messages otherwise
	//we have to make too much sql calls.
	$numberUnreadMessages = getNumberUnreadMessages($userData['username']);
	
	//Assign the connection to a local connection variable
	$connection = getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - Categorie overzicht</title>

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
			
			$('#addCategoryForm').on('submit', function(event) {
                event.preventDefault();    
                $.ajax({
                    url: 'resources/suppliers/add.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
						if(response.success) {
							window.location = 'categorie.php?id='+response.newcategoryid;
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
                    <li><a href="index.php"><i class="fa fa-arrow-left"></i> Terug</a></li>
                    <li><a href="leveranciers.php"><i class="fa fa-truck"></i> Leveranciers</a></li>
					<li class="active"><a href="categorie-beheer.php"><i class="fa fa-tags"></i> Categorieën</a></li>
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
                    <h1>Overzicht categoriën</h1>
                </div>
				<div class="col-lg-4 customer-search">
					<input type="text" class="form-control" id="searchInput" placeholder="Zoek categorie" />
				</div>
            </div>
			<div class="row">
				<div class="col-lg-8">
					<a class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">Nieuwe categorie</a>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Naam</th>
								<th>Omschrijving</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$query = $connection->prepare('select * from categorie');
								$query->execute();
								if($query->rowCount()) {
									while($row = $query->fetch()) {
										echo '<tr>';
										echo '<td>'.filterData($row['naam']).'</td>';
										echo '<td>'.filterData($row['omschrijving']).'</td>';
										echo '<td><a href="categorie.php?id='.filterData($row['id']).'"><i class="fa fa-pencil-square-o fa-lg"></i></a></td>';
										echo '</tr>';
									}
								} else {
									echo '<tr><td>Er zijn geen categoriën gevonden.</td></tr>';
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
        </div>
    </div>
    <!-- /#wrapper -->
	<div id="addCategoryModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Nieuwe categorie</h4>
				</div>
				<div class="modal-body">
					<div id="alert-failed" class="alert alert-danger no-display">
						<strong>Oeps!</strong> Controleer of alle velden zijn ingevuld.
					</div>
					<div class="row">
						<form id="addCategoryForm">
							<input type="hidden" name="action" value="addCategory">
							<div class="col-lg-12">
								<div class="form-group">
									<label>Naam</label>
									<input type="text" class="form-control" name="naam" placeholder="Naam" tabindex="1" required>
								</div>
								<div class="form-group">
									<label>Omschrijving</label>
									<textarea class="form-control" rows="5" name="omschrijving" placeholder="Omschrijving" tabindex="2" required></textarea>
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
