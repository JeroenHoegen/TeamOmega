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
		
		//Remove customer (action=removeCustomer) or remove reparatie (action=removeReparatie)
		if($_POST['action'] == 'removeCustomer') {
			//First check if the user has authority
			checkAuthority(2);
			
			$query = $connection->prepare('delete from klant where id=:id');
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
			checkAuthority(2);
			
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
			checkAuthority(2);
			
			$query = $connection->prepare('delete from updates where id=:id');
			$query->bindParam(':id', $_POST['id']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'removeUser') {
			//First check if the user has authority
			checkAuthority(1);
			
			$query = $connection->prepare('delete from gebruiker where gebruikersnaam=:gebruikersnaam');
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
