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
	
	//Get all the information of the supplier from the provided id
	//returns array $supplierData on success, returns to leverancieren.php
	//on failure
	if(isset($_GET['id'])) {
		$supplierData = getSupplierDataById($_GET['id']);
		
		if(!is_array($supplierData)) {
			header('Location: /omega/leveranciers.php');
			die();
		}
	} else {
		header('Location: /omega/leveranciers.php');
		die();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - leverancier</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	
    <script>
        $(document).ready(function() {
            $('#updateSupplierForm').on('submit', function(event) {
                event.preventDefault();    
                $.ajax({
                    url: 'resources/suppliers/update.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
						if(response.success) {
							$('html, body').animate({ scrollTop: 0 }, 'fast');
							$('#alert-success').fadeIn(500).delay(1000).fadeOut(500);
							$('h1#naam').text('leverancier - '+response.naam);
						} else {
							$('#alert-failed').fadeIn(500).delay(1000).fadeOut(500);  
						}
                    },
                    error: function() {
                        alert('Er is een fout opgetreden!');
                    }
                });
            });
			
			$('#removeSupplier').click(function() {
				if(confirm('Weet u zeker dat u deze leverancier wil verwijderen?')) {
					event.preventDefault();    
					$.ajax({
						url: 'resources/suppliers/remove.ajax.php',
						type: 'post',
						data: 'action=removeSupplier&id=<?php echo filterData($supplierData[0]['id']); ?>',
						dataType: 'json',
						success: function(response) {
							if(response.success) {					
								window.location = 'leveranciers.php';
							} else {
								alert('Kan de leverancier niet verwijderen, probeer het nog eens.');  
							}
						},
						error: function() {
							alert('Er is een fout opgetreden!');
						}
					});
				}
			});
			
			$('#addProductForm').on('submit', function(event) {
				event.preventDefault();
                $.ajax({
                    url: 'resources/suppliers/add.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
						if(response.success) {
							window.location = 'product.php?id='+response.newreparatieid+'&returnid='+response.returnid;
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
                    <li class="active"><a href="leveranciers.php"><i class="fa fa-truck"></i> Leveranciers</a></li>
					<li><a href="categorie.php"><i class="fa fa-tags"></i> Categorieën</a></li>
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
                    <h1 id="naam">leverancier - <?php echo filterData($supplierData[0]['naam']); ?></h1>
                </div>
				<?php if($userData['role'] <= getAuthorityLevel('leverancierverwijderen')) { ?>
				<div class="col-lg-4 text-right top-btn">
					<a class="btn btn-danger" id="removeSupplier">Verwijderen</a>
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
						<li class="active"><a data-toggle="tab" href="#leveranciergegevens">Gegevens</a></li>
						<li><a data-toggle="tab" href="#producten">Producten</a></li>
					</ul>
					<div class="tab-content">
						<div id="leveranciergegevens" class="tab-pane fade in active">
							<h3>Leverancier gegevens</h3>
							<div class="row">
								<form id="updateSupplierForm">
									<input type="hidden" name="action" value="updateSupplier">
									<input type="hidden" name="id" value="<?php echo filterData($supplierData[0]['id']); ?>">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Naam</label>
											<input type="text" class="form-control" name="naam" placeholder="Naam" value="<?php echo filterData($supplierData[0]['naam']); ?>" tabindex="1" required>
										</div>
										<div class="form-group">
											<label>Postcode</label>
											<input type="text" class="form-control" name="postcode" placeholder="Postcode" value="<?php echo filterData($supplierData[0]['postcode']); ?>" tabindex="3" required>
										</div>
										<div class="form-group">
											<label>Telefoonnummer</label>
											<input type="text" class="form-control" name="telefoonnummer" placeholder="Telefoonnummer" value="<?php echo filterData($supplierData[0]['telefoonnummer']); ?>" tabindex="5" required>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Adres</label>
											<input type="text" class="form-control" name="adres" placeholder="Adres" value="<?php echo filterData($supplierData[0]['adres']); ?>" tabindex="2" required>
										</div>
										<div class="form-group">
											<label>Vestigingsplaats</label>
											<input type="text" class="form-control" name="vestigingsplaats" placeholder="Vestigingsplaats" value="<?php echo filterData($supplierData[0]['vestigingsplaats']); ?>" tabindex="4" required>
										</div>
									</div>
									<div class="col-lg-12">
										<button type="submit" class="btn btn-primary">Opslaan</button>
									</div>
								</form>
							</div>
						</div>
						<div id="producten" class="tab-pane fade">
							<div class="row">
								<div class="col-lg-8">
									<h3>Producten</h3>
								</div>
								<div class="col-lg-4 text-right top-table-btn">
									<a class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">Nieuw product</a>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Naam</th>
												<th>Productnummer</th>
												<th>Omschrijving</th>
												<th>Categorie</th>
												<th>Prijs</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php 
												$queryProduct = $connection->prepare('select p.naam, p.productnummer, p.omschrijving,
																					  c.naam as categorie, p.prijs from product p
																					  join categorie c on p.categorieid=c.id
																					  where leverancierid=:leverancierid'); 
												$queryProduct->bindParam(':leverancierid', $supplierData[0]['id']);
												$queryProduct->execute();
												
												if($queryProduct->rowCount()) {
													while($row = $queryProduct->fetch()) {
														echo '<tr>';
														echo '<td>'.filterData($row['naam']).'</td>';
														echo '<td>'.filterData($row['productnummer']).'</td>';
														echo '<td>'.substr(filterData($row['omschrijving']), 0, 30).'...'.'</td>';
														echo '<td>'.filterData($row['categorie']).'</td>';
														echo '<td>'.filterData($row['prijs']).'</td>';
														//echo '<td><a href="reparatie.php?id='.filterData($row['id']).'&returnid='.$supplierData[0]['id'].'"><i class="fa fa-pencil-square-o fa-lg"></i></a></td>';
														echo '</tr>';
													}
												} else {
													echo '<tr><td>Er zijn geen producten gevonden.</td></tr>';
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
	<div id="addProductModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Nieuw product</h4>
				</div>
				<div class="modal-body">
					<div id="alert-failed" class="alert alert-danger no-display">
						<strong>Oeps!</strong> Controleer of alle velden zijn ingevuld.
					</div>
					<div class="row">
						<form id="addProductForm">
							<input type="hidden" name="action" value="addProduct">
							<input type="hidden" name="id" value="<?php echo filterData($supplierData[0]['id']); ?>">
							<div class="col-lg-6">
								<div class="form-group">
									<label>Product naam</label>
									<input type="text" class="form-control" name="productnaam" placeholder="Productnaam" tabindex="1" required>
								</div>
								<div class="form-group">
									<label>Prijs</label>
									<input type="text" class="form-control" name="prijs" placeholder="Prijs" tabindex="3" required>
								</div>
								<div class="form-group">
									<label>Omschrijving</label>
									<textarea class="form-control" rows="5" name="omschrijving" placeholder="Omschrijving" tabindex="5" required></textarea>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label>Categorie</label>
										<select class="form-control" name="categorie" tabindex="2">
											<?php
												//Get all the categories from the database
												$userQuery = $connection->prepare('select id, naam from categorie');
												$userQuery->execute();
												
												while($row = $userQuery->fetch()) {
													echo '<option value="'.$row['id'].'">'.$row['naam'].'</option>';
												}
											?>
										</select>
									</div>
								<div class="form-group">
									<label>Productnummer</label>
									<input type="text" class="form-control" name="productnummer" placeholder="Productnummer" tabindex="4" required>
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
