<?php
	//Header needed because we're working with json
    header('Content-Type', 'application/json');

	//Require all the general functions
    require_once $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';
    
	//Array to store all the customer data and status
    $response = array();
    
    if(isset($_POST['username']) && isset($_POST['password'])) {
		//Assign the connection to a local connection variable
		$connection = getConnection();
		
        $username = $_POST['username'];
        $password = hash('sha256', $_POST['password']);
        
		$query = $connection->prepare('select * from gebruiker where gebruikersnaam=:username and wachtwoord=:password and inactief=0');
		$userData = array();
			
		$query->bindParam(':username', $username);
        $query->bindParam(':password', $password);
			
		$query->execute();
			
		if($query->rowCount()) {
			while($row = $query->fetch()) {
				$userData[] = $row;
			}
				
			$response['success'] = true;
				
			$_SESSION['username'] = $userData[0]['gebruikersnaam'];
			$_SESSION['role'] = $userData[0]['rol'];
			$_SESSION['firstname'] = $userData[0]['voornaam'];
			$_SESSION['lastname'] = $userData[0]['achternaam'];
			$_SESSION['last_activity'] = time();
		} else {
			$response['success'] = false;
		}
    } else {
        $response['success'] = false;
    }
    
    echo json_encode($response);
?>
