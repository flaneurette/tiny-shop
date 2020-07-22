<?php

// Error reporting (for testing only).
ini_set('display_errors', 1); 
error_reporting(E_ALL);

// Optional headers to consider.
header("X-Frame-Options: DENY"); 
header("X-XSS-Protection: 1; mode=block"); 
header("Strict-Transport-Security: max-age=30");
header("Referrer-Policy: same-origin");

include("../resources/php/header.inc.php");
include("../resources/php/class.Session.php");
include("../resources/php/class.SecureMail.php");
include("../class.Shop.php");
	
$shop = new Shop();
	
$setup = new \security\forms\SecureMail();
$token = $setup->getToken();

// Place the token inside a server-side session.
$_SESSION['token'] = $token;

// Try to detect a Robot on this form. If found, do you want to show a Captcha?
$robot = $setup->detectrobot();

$siteconf = $shop->load_json("../inventory/site.json");
$result = $shop->getasetting($siteconf,'site.email');

if($result["site.email"] != '') {
	$email = $shop->decrypt($result["site.email"]);
}

?>
<!DOCTYPE html>
<html>
	<head>
	<?php
	echo $shop->getmeta('../inventory/site.json');				
	?>
	</head>
<body>

<h2>Contact</h2>
<b>E-mail</b>
For any inquiry, use the contact form below.

<h2>Contact form</h2>Contact us and we will try to get back to your request as soon as time allows us to. Please leave your name and e-mail as well as your request. Thank you for your patience and inquiry. 
<?php
			if(isset($_POST['token']))  {
				// A token was provided through $_POST data. Check if it is the same as our session token.
				if($_POST['token'] === $_SESSION['token']) {
					// The submitted token appears to be similar as the session token we set. Obtain $_POST data.  
					
					$parameters = array( 
						'to' => $email,
						'name' => $_POST['name'],
						'email' => $_POST['email'],				
						'subject' => $_POST['subject'],
						'body' => $_POST['body']
					);
					// Proceed to check the $_POST data.
					$checkForm = new \security\forms\SecureMail($parameters);
					// Check the script timer to see how much time was spent.
					$spent_time = $checkForm->getTime();
					if($spent_time == TRUE) {
						// Enough time has been spent, proceed scanning the $_POST data.
						$send = TRUE;
						
						if(isset($_SESSION['captcha_question'])) {
							if($_SESSION['captcha_question'] != $_POST['captcha']) {
								$checkForm->sessionmessage('Captcha was not correct!'); 
								$send = FALSE;
								} else {
								$send = TRUE;
							}
						} 
						
						if($send == TRUE) {
							$scan = $checkForm->fullScan();
							// Did the scan found something?
							if($scan != FALSE) {
								// The class decided the $_POST data was correct. 
								// Start sending the mail.
								$checkForm->sendmail();
								// Show a message.
								$checkForm->sessionmessage('Mail sent!'); 
								} else {
								// The class found something, we cannot send the mail.
								$checkForm->sessionmessage('Mail not sent.');
								}
						}
					}
				} 

			// Show all session messages.
			$checkStatus = new \security\forms\SecureMail();
			$checkStatus->showmessage();
			$checkStatus->destroysession();
		} 

	// Setup new secure mail form.
	$setup = new \security\forms\SecureMail();
	// Clear any previous sessions messages.
	$setup->clearmessages();
	// Create a secure token.
	$token = $setup->getToken();
	// Place the token inside a server-side session.
	$_SESSION['token'] = $token;
	// Create some time to track how long a user takes to complete the form.
	$time  = $setup->setTime();
?><form action="" method="post" class="form">
<input type="hidden" name="token" value="<?php echo $token;?>">
<label for="name">Name:</label><input type="text" name="name" value="">
<label for="email">E-mail:</label>
<input type="text" name="email" value="">
<label for="subject">Subject:</label>			
<input type="text" name="subject" value="">
<label for="body">Message:</label>
<textarea name="body" rows="10" cols="30"></textarea>
				
<?php
				
if($robot == TRUE) {
	echo "Prove to us you are not a robot.<br>";
	echo '<img src="captcha/" width="190"><br>';
	echo '<input type="text" name="captcha" value="answer..."><br><br>';
}
	
?>
<input type="submit" name="submit" value="Send message">
<hr />
<small><b>Formatting</b>
Please make sure you enter all your details correctly, and that the message text is no longer than 5000 characters. HTML formatting is not allowed and will result in a form error when submitted. Thank you.</small>

</form> 
</body>
</html>
