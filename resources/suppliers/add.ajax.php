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
		
		//Add supplier (action=addSupplier)
		if($_POST['action'] == 'addSupplier') {
			//First check if the user has authority
			checkAuthority('leveranciersbeheren');
			
			$query = $connection->prepare("insert into leverancier values(null, :naam, :adres, :postcode, :vestigingsplaats, :telefoonnummer, 0)"); 
			$query->bindParam(':naam', $_POST['naam']);
			$query->bindParam(':adres', $_POST['adres']);
			$query->bindParam(':postcode', $_POST['postcode']);
			$query->bindParam(':vestigingsplaats', $_POST['vestigingsplaats']);
			$query->bindParam(':telefoonnummer', $_POST['telefoonnummer']);
				
			$query->execute();
				
			//If insert statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
				$response['newsupplierid'] = $connection->lastInsertId();
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
