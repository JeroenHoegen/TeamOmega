<?php
	if(isset($customerData)) {
		require_once $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/class.phpmailer.php';
		require_once $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/class.smtp.php';
		
		$mail = new PHPMailer;

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.jansengames.com';  				  // Specify main and backup SMTP servers
		$mail->Timeout = 5;					  				  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'testmail@jansengames.com';        // SMTP username
		$mail->Password = 'test123';                          // SMTP password
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to

		$mail->setFrom('testmail@jansengames.com', 'Eurodiscount Coevorden');
		$mail->addAddress('gerritjanjansen@live.nl');     	  // Add a recipient 

		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = 'Uw reparatie is afgerond';
		$mail->Body    = '<h2>Beste '.$customerData[0]['voornaam'].' '.$customerData[0]['achternaam'].'</h2>';
		$mail->Body    .= '<p>Uw reparatie is afgerond en kan worden opgehaald bij onze winkel.';
		$mail->Body    .= '<p>Met vriendelijke groet,</p>';
		$mail->Body    .= '<p>Eurodiscount Coevorden</p>';

		if(!$mail->send()) {
			$response['mailsuccess'] = false;
		} else {
			$response['mailsuccess'] = true;
		}
	} else {
		die();
	}
?>