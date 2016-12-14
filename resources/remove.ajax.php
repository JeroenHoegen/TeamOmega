<?php
	//Header needed because we're working with json
    header('Content-Type', 'application/json');
	
	//Require all the general functions
    require_once $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';
	
	//Array to store the status
    $response = array();
    
	if(isset($_POST['action']) && $_POST['token'] == $_SESSION['token']) {
		//Assign the connection to a local connection variable
		$connection = getConnection();
		
		//Remove customer (action=removeCustomer) or remove reparatie (action=removeReparatie)
		if($_POST['action'] == 'removeCustomer') {
			//First check if the user has authority
			checkAuthority('klantverwijderen');
			
			$query = $connection->prepare('update klant set inactief=1 where id=:id');
			$query->bindParam(':id', $_POST['id']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'removeReparatie') {
			//First check if the user has authority
			checkAuthority('reparatieverwijderen');
			
			$query = $connection->prepare('delete from reparatie where id=:id');
			$query->bindParam(':id', $_POST['id']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'removeStatus') {
			//First check if the user has authority
			checkAuthority('overzichtbekijken');
			
			$query = $connection->prepare('delete from updates where id=:id and medewerker=:gebruikersnaam');
			$query->bindParam(':id', $_POST['id']);
			$query->bindParam(':gebruikersnaam', getUserData()['username']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'removeUser') {
			//First check if the user has authority
			checkAuthority('accountsbeheren');
			
			$query = $connection->prepare('update gebruiker set inactief=1 where gebruikersnaam=:gebruikersnaam');
			$query->bindParam(':gebruikersnaam', $_POST['gebruikersnaam']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'removePassword') {
			//First check if the user has authority
			checkAuthority('accountsbeheren');
			
			$query = $connection->prepare('update gebruiker set wachtwoord="dc00c903852bb19eb250aeba05e534a6d211629d77d055033806b783bae09937" where gebruikersnaam=:gebruikersnaam');
			$query->bindParam(':gebruikersnaam', $_POST['gebruikersnaam']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		} else {
			$response['success'] = false;
		}
	} else {
		$response['success'] = false;
	}
    
    //Return the $response array as an json object
	echo json_encode($response);
?>
