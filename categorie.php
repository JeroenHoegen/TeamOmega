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
	//returns array $categoryData on success, returns to leverancieren.php
	//on failure
	if(isset($_GET['id'])) {
		$categoryData = getCategoryDataById($_GET['id']);
		
		if(!is_array($categoryData)) {
			header('Location: /omega/categorie-beheer.php');
			die();
		}
	} else {
		header('Location: /omega/categorie-beheer.php');
		die();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - categorie</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	
    <script>
        $(document).ready(function() {
            $('#updateCategoryForm').on('submit', function(event) {
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
							$('h1#naam').text('Product - '+response.categorienaam);
						} else {
							$('#alert-failed').fadeIn(500).delay(1000).fadeOut(500);  
						}
                    },
                    error: function() {
                        alert('Er is een fout opgetreden!');
                    }
                });
            });
			
			$('#removeCategory').click(function() {
				if(confirm('Weet u zeker dat u deze categorie en bijbehorende producten wilt verwijderen?')) {
					event.preventDefault();    
					$.ajax({
						url: 'resources/suppliers/remove.ajax.php',
						type: 'post',
						data: 'action=removeCategory&token=<?php echo $_SESSION['token']; ?>&id=<?php echo filterData($categoryData[0]['id']); ?>',
						dataType: 'json',
						success: function(response) {
							if(response.success) {					
								window.location = 'categorie-beheer.php';
							} else {
								alert('Kan de categorie niet verwijderen, probeer het nog eens.');  
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
                            <li><a href="logout.php?token=<?php echo $_SESSION['token']; ?>"><i class="fa fa-power-off"></i> Uitloggen</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-8">
                    <h1 id="naam">Categorie - <?php echo filterData($categoryData[0]['naam']); ?></h1>
                </div>
				<?php if($userData['role'] <= getAuthorityLevel('leveranciersbeheren')) { ?>
				<div class="col-lg-4 text-right top-btn">
					<a class="btn btn-danger" id="removeCategory">Verwijderen</a>
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
						<li class="active"><a data-toggle="tab" href="#categoriegegevens">Gegevens</a></li>
					</ul>
					<div class="tab-content">
						<div id="categoriegegevens" class="tab-pane fade in active">
							<h3>Categorie gegevens</h3>
							<div class="row">
								<form id="updateCategoryForm">
									<input type="hidden" name="action" value="updateCategory">
									<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
									<input type="hidden" name="id" value="<?php echo filterData($categoryData[0]['id']); ?>">
									<div class="col-lg-12">
										<div class="form-group">
											<label>Categorie naam</label>
											<input type="text" class="form-control" name="categorienaam" placeholder="Categorie naam" value="<?php echo filterData($categoryData[0]['naam']); ?>" tabindex="1" required>
										</div>
										<div class="form-group">
											<label>Omschrijving</label>
											<textarea class="form-control" rows="5" name="omschrijving" placeholder="Omschrijving" tabindex="5" required><?php echo filterData($categoryData[0]['omschrijving']); ?></textarea>
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
