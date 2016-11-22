<?php
	//Header needed because we're working with json
    header('Content-Type', 'application/json');
	
	//Require all the general functions
    require_once $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';
	
	//Array to store all the customer data and status
    $response = array();
    
	if(isset($_POST['action'])) {
		//Assign the connection to a local connection variable
		$connection = getConnection();
		
		//Update customer data (action=updateCustomer) or update reparatie data (action=updateReparatie)
		if($_POST['action'] == 'updateCustomer') {
			//First check if the user has authority
			checkAuthority(3);
			
			$query = $connection->prepare("update klant set voornaam=:voornaam, achternaam=:achternaam, adres=:adres, woonplaats=:woonplaats, postcode=:postcode, email=:email, telefoonnummer=:telefoonnummer where id=:id"); 
			$query->bindParam(':id', $_POST['id']);
			$query->bindParam(':voornaam', $_POST['voornaam']);
			$query->bindParam(':achternaam', $_POST['achternaam']);
			$query->bindParam(':adres', $_POST['adres']);
			$query->bindParam(':woonplaats', $_POST['woonplaats']);
			$query->bindParam(':postcode', $_POST['postcode']);
			$query->bindParam(':email', $_POST['email']);
			$query->bindParam(':telefoonnummer', $_POST['telefoonnummer']);
				
			$query->execute();
				
			//If update statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
				$response['voornaam'] = filterData($_POST['voornaam']);
				$response['achternaam'] = filterData($_POST['achternaam']);
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'updateReparatie') {
			//First check if the user has authority
			checkAuthority(3);
			
			//Here we check if the status of the reparatie is 'afgerond'. In that case we
			//need to check if the customer needs to be informed by email. $sendMail returns
			//true or false.
			$sendMail = ($_POST['newstatus'] == 2 && isset($_POST['emailversturen']) && $_POST['emailversturen'] == 1) ? true : false;
			
			$query = $connection->prepare('update reparatie set garantie=:garantie, kosten=:kosten, omschrijving=:omschrijving, status=:status, emailverstuurd=:emailverstuurd where id=:id');
			$query->bindParam(':id', $_POST['id']);
			$query->bindParam(':garantie', $_POST['garantie']);
			$query->bindParam(':kosten', $_POST['kosten']);
			$query->bindParam(':omschrijving', $_POST['omschrijving']);
			$query->bindParam(':status', $_POST['newstatus']);
			$query->bindParam(':emailverstuurd', $_POST['emailversturen']);
			
			$query->execute();
			
			//If update statement succeed
			if($query->rowCount()) {
				//Successfully updated reparatie, so check if the customer needs to be informed
				//by email.
				if($sendMail) {
					//Get the customer data from the provided id
					$customerData = getCustomerDataById($_POST['customerid']);
					//Get the email from the customerData
					$email = $customerData[0]['email'];
					
					mail($email, 'Uw reparatie is afgerond', 'Hier een kort berichtje en een groet.');
				}
				
				//Here we check if the user added information to the 'statusupdate' field
				//if so we need to add a custom status to the reparatie status.
				if(trim($_POST['statusupdate']) != '') {
					$statusSuccess = addStatusToReparatie($_POST['id'], getUserData()['username'], date('d-m-Y'), date('h:m'), $_POST['statusupdate'], 1);
					if($statusSuccess != false) {
						//If addStatusToReparatie succeeds send back the data to the client
						$response['id'] = $statusSuccess;
						$response['username'] = getUserData()['username'];
						$response['date'] = date('d-m-Y');
						$response['time'] = date('h:m');
						$response['statusupdate'] = filterData($_POST['statusupdate']);
					}
				}
				
				//If the status changes we also need to add an update to the reparatie status
				if($_POST['status'] != $_POST['newstatus']) {
					if($_POST['newstatus'] == 0) {
						addStatusToReparatie($_POST['id'], getUserData()['username'], date('d-m-Y'), date('h:m'), 'Status: Open', 0);
					} else if($_POST['newstatus'] == 1) {
						addStatusToReparatie($_POST['id'], getUserData()['username'], date('d-m-Y'), date('h:m'), 'Status: Wordt aan gewerkt', 0);
					} else if($_POST['newstatus'] == 2) {
						addStatusToReparatie($_POST['id'], getUserData()['username'], date('d-m-Y'), date('h:m'), 'Status: Afgerond', 0);
					}
					$response['newstatus'] = $_POST['newstatus'];
				}
				
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'updatePassword') {
			//First check if the user has authority
			checkAuthority(3);
			
			//Check if the provided password match
			if($_POST['nieuwwachtwoord'] == $_POST['bevestigwachtwoord']) {
				//bindParam only accepts variables so we declare them below
				$wachtwoord = md5($_POST['huidigwachtwoord']);
				$nieuwwachtwoord = md5($_POST['nieuwwachtwoord']);
				
				$query = $connection->prepare('update gebruiker set wachtwoord=:nieuwwachtwoord where gebruikersnaam=:gebruikersnaam and wachtwoord=:wachtwoord'); 
				$query->bindParam(':gebruikersnaam', getUserData()['username']);
				$query->bindParam(':wachtwoord', $wachtwoord);
				$query->bindParam(':nieuwwachtwoord', $nieuwwachtwoord);
				
				$query->execute();
					
				//If update statement succeed
				if($query->rowCount()) {
					$response['success'] = true;
				} else {
					//throw error current password does not match
					$response['matchfail'] = true;
				}
			} else {
				//throw error
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'updateUser') {
			//First check if the user has authority
			checkAuthority(1);
			
			$query = $connection->prepare('update gebruiker set rol=:rol, voornaam=:voornaam, achternaam=:achternaam where gebruikersnaam=:gebruikersnaam');
			$query->bindParam(':rol', $_POST['rol']);
			$query->bindParam(':voornaam', $_POST['voornaam']);
			$query->bindParam(':achternaam', $_POST['achternaam']);
			$query->bindParam(':gebruikersnaam', $_POST['gebruikersnaam']);
				
			$query->execute();
				
			//If update statement succeed
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
