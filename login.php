<?php
	//Require all the general functions
	require $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/general.func.php';
	
	//Checks if the user is logged in
	checkLogin(true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Discount - Inloggen</title>
        
    <link href="css/login.css" rel="stylesheet" type="text/css" />
       
    <script src="js/jquery-3.1.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginform').on('submit', function(event) {
                event.preventDefault();    
                $.ajax({
                    url: 'resources/login.ajax.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            window.location = 'index.php';
                        } else if(response.blocked) {
							alert('Je account is inactief. Neem contact op met de admin.');
						} else {
                            $('input').attr('class', 'error-class');
						}
						$('input[name=password]').val('');
                    },
                    error: function() {
                        alert('Er is een fout opgetreden!');
                    }
                });
            });
        });
    </script>
</head>
<body>
	<div class="logo">
		<img src="images/EuroDiscountLogo.png" />
	</div>
	<div class="login">
		<div class="login-screen">
			<div class="app-title">
				<h1>Welkom</h1>
			</div>

			<div class="login-form">
				<form id="loginform">
					<div class="control-group">
						<input type="text" class="login-field" name="username" placeholder="Gebruikersnaam">
					</div>
					<div class="control-group">
						<input type="password" class="login-field" name="password" placeholder="Wachtwoord">
					</div>
					<button class="btn" type="submit" name="submit">Inloggen</button>
				</form>
			</div>
		</div>
	</div>
</body>
</html>