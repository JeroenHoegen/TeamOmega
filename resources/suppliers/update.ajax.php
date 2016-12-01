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
		if($_POST['action'] == 'updateSupplier') {
			//First check if the user has authority
			checkAuthority('leveranciersbeheren');
			
			$query = $connection->prepare("update leverancier set naam=:naam, adres=:adres, postcode=:postcode, vestigingsplaats=:vestigingsplaats, telefoonnummer=:telefoonnummer where id=:id and inactief=0"); 
			$query->bindParam(':id', $_POST['id']);
			$query->bindParam(':naam', $_POST['naam']);
			$query->bindParam(':adres', $_POST['adres']);
			$query->bindParam(':postcode', $_POST['postcode']);
			$query->bindParam(':vestigingsplaats', $_POST['vestigingsplaats']);
			$query->bindParam(':telefoonnummer', $_POST['telefoonnummer']);
				
			$query->execute();
				
			//If update statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
				$response['naam'] = filterData($_POST['naam']);
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
