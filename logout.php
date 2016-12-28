<?php
	//Destroys the user session to logout the user and return to the login page
	if(session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	if($_GET['token'] == $_SESSION['token']) {
		session_destroy();
		header('Location: /omega/login.php');
	} else {
		header('Location: /omega/index.php');
	}
?>