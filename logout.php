<?php
	//Destroys the user session to logout the user and return to the login page
	if($_POST['token'] == $_SESSION['token']) {
		if(session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		session_destroy();
		header('Location: /omega/login.php');
	}
?>