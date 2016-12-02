<?php
	//Header needed because we're working with json
    header('Content-Type', 'application/json');
	
	//Require all the general functions
    require_once $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';
	
	//Array to the new customerid and status
    $response = array();
    
	if(isset($_POST['action'])) {
		//Assign the connection to a local connection variable
		$connection = getConnection();
		
		//Add customer (action=addCustomer) or add reparatie (action=addReparatie)
		//or add user (action=addUser)
		if($_POST['action'] == 'addCustomer') {
			//First check if the user has authority
			checkAuthority('klanttoevoegen');
			
			$query = $connection->prepare("insert into klant values(null, :voornaam, :achternaam, :adres, :woonplaats, :postcode, :email, :telefoonnummer, 0)"); 
			$query->bindParam(':voornaam', $_POST['voornaam']);
			$query->bindParam(':achternaam', $_POST['achternaam']);
			$query->bindParam(':adres', $_POST['adres']);
			$query->bindParam(':woonplaats', $_POST['woonplaats']);
			$query->bindParam(':postcode', $_POST['postcode']);
			$query->bindParam(':email', $_POST['email']);
			$query->bindParam(':telefoonnummer', $_POST['telefoonnummer']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
				$response['newcustomerid'] = $connection->lastInsertId();
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'addReparatie') {
			//First check if the user has authority
			checkAuthority('reparatietoevoegen');
			
			$query = $connection->prepare("insert into reparatie values(null, :klantid, :medewerker, :serienummer, :startdatum, :omschrijving, null, null, 0, 0)"); 
			$query->bindParam(':klantid', $_POST['id']);
			$query->bindParam(':medewerker', $_POST['medewerker']);
			$query->bindParam(':serienummer', $_POST['serienummer']);
			$query->bindParam(':startdatum', $_POST['startdatum']);
			$query->bindParam(':omschrijving', $_POST['omschrijving']);
				
			$query->execute();
			
			//Here we store the id of the newly inserted reparatie
			$reparatieid = $connection->lastInsertId();
				
			//Store the time (hour:minute) to use it in the status updates
			$time = date('h:i');
			
			//Add status to reparatie
			addStatusToReparatie($reparatieid, $_POST['startdatum'], $time, 'Reparatie toegevoegd', '0');	
			
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
				
				$response['newreparatieid'] = $reparatieid;
				$response['returnid'] = $_POST['id'];
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'addUser') {
			//First check if the user has authority
			checkAuthority('accountsbeheren');
		
			//Convert the password to a md5 hash, since bindParam
			//only accepts one variable
			$password = hash('sha256', $_POST['wachtwoord']);
			
			$query = $connection->prepare("insert into gebruiker values (:gebruikersnaam, :wachtwoord, :rol, :voornaam, :achternaam, 0)");
			$query->bindParam(':gebruikersnaam', $_POST['gebruikersnaam']);
			$query->bindParam(':wachtwoord', $password);
			$query->bindParam(':rol', $_POST['rol']);
			$query->bindParam(':voornaam', $_POST['voornaam']);
			$query->bindParam(':achternaam', $_POST['achternaam']);
			
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		}
	} else {
			$response['success'] = false;
	}
    
    //Return the $response array as an json object
	echo json_encode($response);
?>
