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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - Welkom</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />

    <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
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
                    <li class="active"><a href="index.php"><i class="fa fa-bullseye"></i> Overzicht</a></li>
                    <li><a href="klanten.php"><i class="fa fa-tasks"></i> Klanten</a></li>
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
                <div class="col-lg-12">
                    <h1>Welkom <?php echo filterData($userData['firstname']).' '.filterData($userData['lastname']) ?></h1>
                </div>
			</div>
			<div class="row">
				<?php 
					$query = $connection->prepare('select r.id, r.klantid, r.omschrijving, r.status, k.voornaam, k.achternaam from reparatie r join klant k on r.klantid=k.id where r.status != 2'); 
					$query->execute();
					if($query->rowCount()) {
						echo '<div class="col-lg-12"><h3>De volgende reparaties moeten nog worden uitgevoerd:</h3></div>';
						while($row = $query->fetch()) {
							//Checks which class needs to be added to the reparatie
							//green on uitgevoerd = true, red on uitgevoerd = false
							$classReparatie = ($row['status'] == '1') ? 'list-group-item-success' : 'list-group-item-danger';
							
							echo '<div class="col-lg-3 index-distance">';
							echo '<a href="reparatie.php?id='.$row['id'].'&returnid='.$row['klantid'].'" class="list-group-item '.$classReparatie.'">';
							echo '<h4 class="list-group-item-heading">'.filterData($row['voornaam']).' '.filterData($row['achternaam']).'</h4>';
							echo '<p class="list-group-item-text">'.substr($row['omschrijving'], 0, 300).'</p>';
							echo '</a>';
							echo '</div>';
						}
					} else {
						echo '<div class="col-lg-12"><h3>Er zijn geen reparaties die moeten worden uitgevoerd</h3></div>';
					}
				?>
            </div>
        </div>
    </div>
    <!-- /#wrapper -->
</body>
</html>
