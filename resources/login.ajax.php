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
        
		//Checks if the logintimestried and loginusername session variable is set
		//loginusername and logintimestried belongs to each other, each username
		//can have a different amount of logintimestried.
		if(!isset($_SESSION['logintimestried']) && !isset($_SESSION['loginusername'])) {
			$_SESSION['logintimestried'] = 0;
			$_SESSION['loginusername'] = $username;
		}
		
		$query = $connection->prepare('select * from gebruiker where gebruikersnaam=:username and wachtwoord=:password');
		$userData = array();
			
		$query->bindParam(':username', $username);
        $query->bindParam(':password', $password);
			
		$query->execute();
			
		if($query->rowCount()) {
			while($row = $query->fetch()) {
				$userData[] = $row;
			}
			
			if($userData[0]['inactief'] == 1) {
				$response['blocked'] = $userData[0]['inactief'];
			} else {
				$response['inactief'] = true;
				$response['success'] = true;
					
				$_SESSION['username'] = $userData[0]['gebruikersnaam'];
				$_SESSION['role'] = $userData[0]['rol'];
				$_SESSION['firstname'] = $userData[0]['voornaam'];
				$_SESSION['lastname'] = $userData[0]['achternaam'];
				$_SESSION['last_activity'] = time();
				$_SESSION['token'] = hash('sha256', str_repeat(rand(0,33), 25));
				
				unset($_SESSION['logintimestried']);
				unset($_SESSION['loginusername']);
			}
		} else {
			//Check if the tried account is not the backupadmin
			if($_SESSION['logintimestried'] >= 3 && ($_SESSION['loginusername'] == $username) && $username != 'backupadmin') {
				if(blockUserByUsername($username)) {
					$response['blocked'] = true;
				} else {
					$response['success'] = false;
				}
				unset($_SESSION['logintimestried']);
				unset($_SESSION['loginusername']);
			} else if($_SESSION['loginusername'] != $username) {
				unset($_SESSION['logintimestried']);
				unset($_SESSION['loginusername']);
			} else {
				$_SESSION['logintimestried'] += 1;
				$response['success'] = false;
			}
		}
    } else {
        $response['success'] = false;
    }
    
    echo json_encode($response);
?>
