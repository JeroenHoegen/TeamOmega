<?php
	if(isset($customerData)) {
		require_once $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/class.phpmailer.php';
		require_once $_SERVER['DOCUMENT_ROOT'].'/omega/resources/library/class.smtp.php';
		
		$mail = new PHPMailer;

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.jansengames.com';  				  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'testmail@jansengames.com';        // SMTP username
		$mail->Password = 'test123';                          // SMTP password
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to

		$mail->setFrom('testmail@jansengames.com', 'Jansengames Test Email');
		$mail->addAddress('gerritjanjansen@live.nl');     	  // Add a recipient 

		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = 'Here is the subject';
		$mail->Body    = 'This is the HTML message body <b>in bold!</b>';

		if(!$mail->send()) {
			$response['mailsuccess'] = false;
		} else {
			$response['mailsuccess'] = true;
		}
	} else {
		die();
	}
?>