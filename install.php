<pre>
<?php

	session_start();
	
	$versioning = PHP_VERSION_ID;
	$error = [];
	
	if($_GET['delete']) {
		if($_SESSION['nonce'] == $_GET['delete']) {
			header("Location: index.php",302);
			@unlink("install.php");
			exit;
		} else {
			echo 'Nonce was incorrect, could not delete file. Please delete it manually.';
			echo '</pre>';
			exit;
		}
	} 

	function nonce($max=0xffffffff) {
		$tmp_nonce = uniqid().mt_rand(0,$max).mt_rand(0,$max).mt_rand(0,$max).mt_rand(0,$max);
		return $tmp_nonce;
	}
	
	$nonce = nonce();
	
	$_SESSION['nonce'] = $nonce;

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
			
					echo "<blockquote>";
					
						$i=1;
						
						foreach($error as $e) {
							echo $i . ": " . $e . PHP_EOL;
							$i++;
						}
						
					echo "</blockquote>";
				
				echo 'Please install and configure the above missing extensions or libraries.'. PHP_EOL . PHP_EOL;
				
				echo '<hr />' . PHP_EOL . PHP_EOL;
				echo '<h1>Additional information:</h1>' . PHP_EOL . PHP_EOL;
				
				phpinfo();
			echo '</pre>';
			
			exit;

	} else {

		echo 'All requirements were met. TinyShop should function correctly! If not, please read the manual on Github: https://github.com/flaneurette/tiny-shop'. PHP_EOL;
		echo 'Please delete the install.php file, or <a href="install.php?delete='.$nonce.'">click here.</a>'. PHP_EOL;
	}
?>
</pre>