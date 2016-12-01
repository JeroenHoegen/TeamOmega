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
		} else if($_POST['action'] == 'updateProduct') {
			//First check if the user has authority
			checkAuthority('leveranciersbeheren');
			
			$query = $connection->prepare("update product set categorieid=:categorieid, productnummer=:productnummer, naam=:naam, omschrijving=:omschrijving, prijs=:prijs where id=:id"); 
			$query->bindParam(':id', $_POST['id']);
			$query->bindParam(':categorieid', $_POST['categorie']);
			$query->bindParam(':productnummer', $_POST['productnummer']);
			$query->bindParam(':naam', $_POST['productnaam']);
			$query->bindParam(':omschrijving', $_POST['omschrijving']);
			$query->bindParam(':prijs', $_POST['prijs']);
				
			$query->execute();
				
			//If update statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
				$response['productnaam'] = filterData($_POST['productnaam']);
			} else {
				$response['success'] = false;
			}
		} else if($_POST['action'] == 'updateCategory') {
			//First check if the user has authority
			checkAuthority('leveranciersbeheren');
			
			$query = $connection->prepare("update categorie set naam=:naam, omschrijving=:omschrijving where id=:id"); 
			$query->bindParam(':id', $_POST['id']);
			$query->bindParam(':naam', $_POST['categorienaam']);
			$query->bindParam(':omschrijving', $_POST['omschrijving']);
				
			$query->execute();
				
			//If update statement succeed
			if($query->rowCount()) {
				$response['success'] = true;
				$response['categorienaam'] = filterData($_POST['categorienaam']);
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
