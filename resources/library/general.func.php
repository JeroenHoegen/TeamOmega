<?php
	//We need to require config.php because of the database settings
	require $_SERVER['DOCUMENT_ROOT'].'/omega/resources/config.php';

	//Returns the PDO connection
	function getConnection() {
		try {
			$connection = new PDO('mysql:host='.DB_HOST.';dbname='.DB_DATABASE, DB_USER, DB_PASSWORD);
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $connection;
		} catch(PDOException $ex) {
			die('Kan geen verbinding maken met de database.');
		}
	}

	//Checks if the user is logged in, $loginpage is false on default
	function checkLogin($loginpage=false) {
		if(session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		if(!isset($_SESSION['username']) && !isset($_SESSION['role'])) {
			if(!$loginpage) {
				header('Location: /omega/login.php');
				die();
			}
		} else {
			if($loginpage) {
				header('Location: /omega/index.php');
			}
		}
	}
	
	//Checks if the user has the authority to access a certain page
	//the level parameter is the minumum level needed for access (1, 2 ,3)
	//returns to index.php on false
	function checkAuthority($action) {
		if(session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		if($_SESSION['role'] > getAuthorityLevel($action)) {
			header('Location: /omega/index.php');
			die();
		}
	}
	
	//This function returns the needed minimal authority level for
	//an action from the database. Returns the level on success
	//and 1 (the highest possible security level) on failure.
	function getAuthorityLevel($action) {
		$connection = getConnection();
		$query = $connection->prepare('select minimalerol from functierol where naam=:naam');
		$query->bindParam(':naam', $action);
		$query->execute();
		
		if($query->rowCount() > 0) {
			$authorityData = $query->fetchAll();
			return $authorityData[0]['minimalerol'];
		} else {
			return 1;
		}
	}
	
	//Returns all the user data as an array
	function getUserData() {
		$userData = array('username' => $_SESSION['username'], 
						  'role' => $_SESSION['role'], 
						  'firstname' => $_SESSION['firstname'], 
						  'lastname' => $_SESSION['lastname']);
		return $userData;
	}
	
	//This functions removes special characters in order to prevent XSS
	function filterData($data, $status=false) {
		return (empty($data) && !$status) ? '-' : htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
	}
	
	//This functions returns the number of unread messages
	//We use this for the little badge in the right top if
	//there are unread messages. Returns the number on success
	//and 0 on failure.
	function getNumberUnreadMessages($username) {
		$connection = getConnection();
		$query = $connection->prepare('select id from bericht where naar=:gebruikersnaam and gelezen=0');
		$query->bindParam(':gebruikersnaam', $username);
		$query->execute();
		
		if($query->rowCount() > 0) {
			return $query->rowCount();
		} else {
			return 0;
		}
	}
	
	//Get the customer data by id returns array on success
	//and false on failure.
	function getCustomerDataById($id) {
		$connection = getConnection();
		$query = $connection->prepare('select * from klant where id=:id and inactief=0');
		$query->bindParam(':id', $id);
		$query->execute();
		
		if($query->rowCount() > 0) {
			return $query->fetchAll();
		} else {
			return false;
		}
	}
	
	//Get the supplier data by id returns array on success
	//and false on failure.
	function getSupplierDataById($id) {
		$connection = getConnection();
		$query = $connection->prepare('select * from leverancier where id=:id and inactief=0');
		$query->bindParam(':id', $id);
		$query->execute();
		
		if($query->rowCount() > 0) {
			return $query->fetchAll();
		} else {
			return false;
		}
	}
	
	//Get the product data by id returns array on success
	//and false on failure.
	function getProductDataById($id) {
		$connection = getConnection();
		$query = $connection->prepare('select * from product where id=:id');
		$query->bindParam(':id', $id);
		$query->execute();
		
		if($query->rowCount() > 0) {
			return $query->fetchAll();
		} else {
			return false;
		}
	}
	
	//Get the category data by id returns array on success
	//and false on failure.
	function getCategoryDataById($id) {
		$connection = getConnection();
		$query = $connection->prepare('select * from categorie where id=:id');
		$query->bindParam(':id', $id);
		$query->execute();
		
		if($query->rowCount() > 0) {
			return $query->fetchAll();
		} else {
			return false;
		}
	}
	
	//This function adds a new status to a reparatie
	//Returns the status id on success, false on failure
	function addStatusToReparatie($reparatieid, $datum, $tijd, $omschrijving, $verwijderen) {
		$connection = getConnection();
		
		$queryUpdates = $connection->prepare("insert into updates values (null, :reparatieid, :medewerker, :datum, :tijd, :omschrijving, :verwijderen)");
		$queryUpdates->bindParam(':reparatieid', $reparatieid);
		$queryUpdates->bindParam(':medewerker', getUserData()['username']);
		$queryUpdates->bindParam(':datum', $datum);
		$queryUpdates->bindParam(':tijd', $tijd);
		$queryUpdates->bindParam(':omschrijving', $omschrijving);
		$queryUpdates->bindParam(':verwijderen', $verwijderen);
			
		$queryUpdates->execute();
		
		if($queryUpdates->rowCount()) {
			return $connection->lastInsertId();
		} else {
			return false;
		}
	}
?>