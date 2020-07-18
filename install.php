<?php

	session_start();

	include("class.Shop.php");
	$shop = new Shop();
	
	$versioning = PHP_VERSION_ID;
	$error = [];
	
	if($_GET['delete']) {
		if($_SESSION['nonce'] == $_GET['delete']) {
			header("Location: index.php",302);
			@unlink("install.php");
			exit;
		} else {
			echo '<pre>';
			echo 'Nonce was incorrect, could not delete file. Please delete it manually.';
			echo '</pre>';
			exit;
		}
	} 
	
	if(isset($_SESSION['nonce'])) {
		$nonce = $shop->sanitize($_SESSION['nonce'],'alphanum');
		} else {
		$nonce = $shop->pseudoNonce();
		$_SESSION['nonce'] = $shop->sanitize($nonce,'alphanum');
	}

	if(!defined('PHP_VERSION_ID') || $versioning  < 50400) {
		array_push($error,'PHP version 5.4 or above is required, cannot install TinyShop.');
	}

	if(!function_exists('file_get_contents')) {
		
		array_push($error,'file_get_contents function does not work, please "allow_url_fopen" for stream support. Tinyshop does NOT work without it.');
		
		if(function_exists('stream_get_wrappers')) {
			$wrappers = stream_get_wrappers();
			if(count($wrappers) > 0) {
				$sr = implode(",",stream_get_wrappers());
				array_push($error,'The current supported streamwrappers are: '.$sr);
				} else {
				array_push($error,'There appears to be no support for streamwrappers, please "allow_url_fopen" for stream support.');
			}
		}
	}

	if(function_exists('ini_get')) {
		if(!ini_get('allow_url_fopen') ) {
			array_push($error,'Please set "allow_url_fopen" to "On" in PHP.ini, for adequate stream support. Tinyshop does NOT work without it.');
		}
	}
	
	if(!function_exists('mb_convert_encoding')) {
		array_push($error,'mb_convert_encoding function does not work, please install the "mbstring" library for multibyte support. Tinyshop might not work without it.');
	}

	if(!function_exists('mail')) {
		array_push($error,'MAIL extension is not working properly.');
	}

	if(!function_exists('json_decode')) {
		array_push($error,'JSON extension is not working properly.');
	}

	if(!function_exists('random_bytes')) {
		array_push($error,'random_bytes function does not exist.');
	}

	if(!function_exists('openssl_random_pseudo_bytes')) {
		array_push($error,'Openssl_random_pseudo_bytes function does not exist.');
	}
	
	if(!function_exists('openssl_encrypt')) {
		array_push($error,'OpenSSL is not supported or enabled on this PHP instance.');
	}
	
	if(!function_exists('openssl_decrypt')) {
		array_push($error,'Openssl_decrypt function does not exist.');
    }
	
	if(count($error) > 0) {
		
		echo '<h1>Installation failed.</h1>' . PHP_EOL . PHP_EOL;
		
			echo 'Could not install TinyShop. The following requirements were not met:'. PHP_EOL . PHP_EOL;
			echo '<pre>';
					echo "<blockquote>";
					
						$i=1;
						
						foreach($error as $e) {
							echo $i . ": " . $shop->sanitize($e,'field') . PHP_EOL;
							$i++;
						}
						
					echo "</blockquote>";
				
				echo 'Please install and configure the above missing extensions or libraries.'. PHP_EOL . PHP_EOL;
				
				echo '<hr />' . PHP_EOL . PHP_EOL;
				echo '<h1>Additional information:</h1>' . PHP_EOL . PHP_EOL;
				
				phpinfo();
			echo '</pre>';
			
			exit;

	} elseif($_POST['setup'] == 1) {
		
				if(isset($_SESSION['nonce'])) {
					
					$nonce = $shop->sanitize($_SESSION['nonce'],'alphanum');
					
					if($_SESSION['nonce'] != $shop->sanitize($_POST['nonce'])) {
						echo 'Security Nonce is expired or missing.';
						exit;	
					}
						
				} else {
					echo 'Security Nonce is expired or missing.';
					exit;					
				}

				if(!isset($_POST['admin_username'])) {
					echo 'Username cannot be empty, setup could not continue.';
					exit;
				}
				
				if(!isset($_POST['admin_password'])) {
					echo 'Password cannot be empty, setup could not continue.';
					exit;
				}
				
				if(!isset($_POST['admin_website'])) {
					echo 'Website cannot be empty, setup could not continue.';
					exit;					
				}
				
				if(!isset($_POST['admin_email'])) {
					echo 'Admin e-mail cannot be empty, setup could not continue.';
					exit;					
				}	
				
				if(!isset($_POST['admin_paypal_email'])) {
					echo 'Admin e-mail cannot be empty, setup could not continue.';
					exit;					
				}				
				
				if(!isset($_POST['admin_ip'])) {
					echo 'IP cannot be empty, setup could not continue.';
					exit;				
				}
				
				if(!isset($_POST['admin_currency'])) {
					echo 'Currency cannot be empty, setup could not continue.';
					exit;				
				}
				
				function create_htpasswd($username,$password) {

					$encrypted_password = crypt($password, base64_encode($password));
					$data = $username.":".$encrypted_password;
					
					$ht = fopen("administration/.htpasswd", "w") or die("Unable to open .htpasswd");
					fwrite($ht, $data);
					fclose($ht);
				}
			
				function create_htaccess($ip,$root) {
					
					$htaccess = 'AuthType Basic
					AuthName "Tinyshop Administration"
					AuthUserFile '.$root.'/shop/administration/.htpasswd
					Require valid-user
					Order Deny,Allow
					Deny from all
					Allow from '.$ip.'
					';
					
					$hta = fopen("administration/.htaccess", "w") or die("Unable to open .htaccess");
					fwrite($hta, $htaccess);
					fclose($hta);
				}

				$username = $shop->sanitize($_POST['admin_username'],'table');
				$password = $shop->sanitize($_POST['admin_password'],'table');
				$ip 	  = $shop->sanitize($_POST['admin_ip'],'table');
				$root 	  = $shop->sanitize($_SERVER['DOCUMENT_ROOT'],'table');
				
				create_htpasswd($username,$password);
				create_htaccess($ip,$root);
				
				$keys = 'inventory/site.json';
				$shop->backup($keys);
				
				$json = $shop->load_json($keys); 
		
				$json[0]["site.url"] = $shop->sanitize($_POST['admin_website'],'url');
				$json[0]["site.domain"] = $shop->sanitize($_POST['admin_website'],'url');
				$json[0]["site.currency"] = $shop->sanitize($_POST['admin_currency'],'num');
				$json[0]["site.email"] = $shop->sanitize($_POST['admin_email'],'url');

				if($_POST['admin_encryption'] == '1') {
					$json[0]["site.email"] = $shop->encrypt($shop->sanitize($_POST['admin_email'],'url'));
					} else {
					$json[0]["site.email"] = $shop->sanitize($_POST['admin_email'],'url');
				}

				$shop->storedata($keys,$json);	 
		
		echo '<pre>';
		echo 'TinyShop was installed and should function correctly! If not, please read the manual on Github: https://github.com/flaneurette/tiny-shop'. PHP_EOL;
		echo 'Please delete the install.php file, or <a href="install.php?delete='.$shop->sanitize($nonce,'alphanum').'">click here.</a> to let Tinyshop do it for you'. PHP_EOL;
		echo '</pre>';	
		
	} elseif($_POST['setup-complete'] == 1) {
		
		echo '<pre>';
		echo 'TinyShop was installed and should function correctly! If not, please read the manual on Github: https://github.com/flaneurette/tiny-shop'. PHP_EOL;
		echo 'Please delete the install.php file, or <a href="install.php?delete='.$shop->sanitize($nonce,'alphanum').'">click here.</a> to let Tinyshop do it for you'. PHP_EOL;
		echo '</pre>';			
	
	} else {
		
		// Installer
		
		?>
		<!DOCTYPE html>
		<html>
			<head>
				<link rel="stylesheet" type="text/css" href="resources/reset.css">
				<link rel="stylesheet" type="text/css" href="resources/style.css">
			</head>
			<body>
			<h1>Setup TinyShop</h1>
			<div>
			<?php echo 'All requirements were met. Continue to configure TinyShop';?>
			</div>
			
			<hr />
				<div id="ts-shop-cart-form" style="margin-left:20px;">
					<form name="" action="" method="post">
						<input name="setup" value="1" type="hidden">
						<input name="nonce" value="<?=$shop->sanitize($nonce,'alphanum');?>" type="hidden">
						Website: <input name="admin_website" value="https://www.example.com" type="text">
						Website e-mail: <input name="admin_website_email" value="info@website.com" type="text">
						<hr />
						Currency:
						<select name="admin_currency">
						<?php
						echo $shop->currencylist();
						?>
						</select>
						<hr />
						Admin Username: <input name="admin_username" value="" type="text">
						Admin Password: <input name="admin_password" value="" type="text">
						Admin E-mail: <input name="admin_email" value="" type="text">
						<hr />
						Admin IP: <input name="admin_ip" value="<?= $shop->sanitize($_SERVER['REMOTE_ADDR'],'table');?>" type="text">
						<hr />
						PayPal e-mail (to accept payments on): <input name="admin_paypal_email" value="info@website.com" type="text">
						<hr />
						
						Security. Encrypt e-mail address? 
						
						<select name="admin_encryption">
							<option value="2">No</option>
							<option value="1">Yes</option>
						</select> <sup>(if NO, it will be visible to everyone)</sup>
						<hr />
						<input type="submit" value="Setup TinyShop >>">
					</form>
				</div>
			</body>
		</html>
<?php
	
	}
?>
