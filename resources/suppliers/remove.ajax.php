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
		
		//Remove supplier (action=removeSupplier)
		if($_POST['action'] == 'removeSupplier') {
			//First check if the user has authority
			checkAuthority('leveranciersbeheren');
			
			$query = $connection->prepare('update leverancier set inactief=1 where id=:id');
			$query->bindParam(':id', $_POST['id']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'removeProduct') {
			//First check if the user has authority
			checkAuthority('leveranciersbeheren');
			
			$query = $connection->prepare('delete from product where id=:id');
			$query->bindParam(':id', $_POST['id']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'removeCategory') {
			//First check if the user has authority
			checkAuthority('leveranciersbeheren');
			
			$query = $connection->prepare('delete from categorie where id=:id');
			$query->bindParam(':id', $_POST['id']);
				
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
