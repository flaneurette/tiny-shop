<?php

	session_start();

	/***
	/* There is no need in editing this file.
	*/
	
	include("class.Shop.php");
	include("resources/php/class.Security.php");
	
	$shop = new Shop();
	$security = new Security();
	
	$versioning = PHP_VERSION_ID;
	$error = [];
	
	$host = $_SERVER['HTTP_HOST'];

	/***
	/* Installer deletion.
	*/
	
	if(isset($_GET['delete'])) {
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

	/***
	/* Create security nonce, against CSRF.
	*/
	
	if(isset($_SESSION['nonce'])) {
		$nonce = $shop->sanitize($_SESSION['nonce'],'alphanum');
		} else {
		$nonce = $shop->pseudoNonce();
		$_SESSION['nonce'] = $shop->sanitize($nonce,'alphanum');
	}

	/***
	/* Check if required PHP version is present.
	*/
	
	if(!defined('PHP_VERSION_ID') || $versioning  < 50400) {
		array_push($error,'PHP version 5.4 or above is required, cannot install TinyShop.');
	}

	/***
	/* Check if fopen is enabled in PHP, required for installation.
	*/
	
	if(function_exists('ini_get')) {
		if(!ini_get('allow_url_fopen') ) {
			die('Please set "allow_url_fopen" to "On" in PHP.ini, for adequate stream support. Tinyshop does NOT work without it.');
		} 
	}

	if(!is_writable("inventory/site.json")) {

		if(chmod("inventory/site.json",0775) == false) {
			array_push($error, "Could not chmod the inventory. Please chmod the /inventory/ folder manually to 0775, to write files to it.");
		}
	}

	if(!is_writable("payment/paypal/paypal.json")) {
		if(chmod("payment/paypal/paypal.json",0775) == false) {
			array_push($error, "Could not chmod the paypal directory. Please chmod the /payment/paypal/ folder manually to 0775, to write files to it.");
		}
	}

	/***
	/* Check if required files are missing.
	*/
	
	if(!file_exists('inventory/site.json')) {
		array_push($error, "TinyShop software package is incomplete or has missing files: inventory/site.json. Please clone or download again.");
	}
	
	if(!file_exists('payment/paypal/paypal.json')) {
		array_push($error, "TinyShop software package is incomplete or has missing files: payment/paypal/paypal.json. Please clone or download again.");
	}
	
	if(!is_writable("resources/images/")) {
		if(chmod("resources/images/",0775) == false) {
			array_push($error, "Could not chmod resources/images/. Please chmod the directory manually to 0775, to write files to it.");			
		}
	}

	if(!is_writable("resources/images/products/")) {
		if(chmod("resources/images/products/",0775) == false) {
			array_push($error, "Could not chmod resources/images/products/. Please chmod the directory manually to 0775, to write files to it.");			
		}
	}
	
	if(!is_writable("resources/images/category/")) {
		if(chmod("resources/images/category/",0775) == false) {
			array_push($error, "Could not chmod resources/images/category/. Please chmod the directory manually to 0775, to write files to it.");			
		}
	}	


	if(!is_writable("administration/.htpasswd")) {

		if(chmod("administration/",0775) == false) {
			array_push($error, "Could not chmod the administration directory. Please chmod the /administration/ folder manually to 0775, to write files to it.");
		}
		
		if(chmod("administration/.htpasswd",0775) == false) {
			array_push($error, "Could not chmod the .htpasswd file. Please chmod the file manually to 0775, to write files to it.");
		}
	}
	
						
	$httest = fopen("administration/.htpasswd", "w");
	
	if($httest == FALSE || $httest == false) {
		array_push($error, "Could not open .htpassword for writing. Please make sure that the server is allowed to write to the administration folder. For example: In Apache, the folder should be chowned to www-data:www-data.");
	}

	/***
	/* Check if required functions and extensions are missing.
	*/
	
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

	/***
	/* Error reporting.
	*/

	if(count($error) >= 1) {
		
		echo '<h1>Installation failed.</h1>' . PHP_EOL . PHP_EOL;
		
			echo 'Could not install TinyShop. The following requirements were not met:'. PHP_EOL . PHP_EOL;
			echo '<pre>';
					echo "<blockquote>";
					
						$i=1;
						
						foreach($error as $e) {
							echo $i . ": " . $shop->sanitize($e,'cat') . PHP_EOL;
							$i++;
						}
						
					echo "</blockquote>";
				
				echo 'Please install and configure the above missing extensions or libraries.'. PHP_EOL . PHP_EOL;
				
				echo '<hr />' . PHP_EOL . PHP_EOL;
				echo '<h1>Additional information:</h1>' . PHP_EOL . PHP_EOL;
				
				phpinfo();
			echo '</pre>';
			
			exit;

	} elseif(isset($_POST['setup']) == 1) {
		
			/***
			/* Check if the installer already has been run before, or is running by another user. If so, exit the installer and warn user.
			*/

			if(!is_writable("administration/session.ses")) {
				chmod("administration/session.ses",0775);
			}

			$session = fopen("administration/session.ses", "rw+") or die("Unable to open administration/session.ses. Cannot continue installation.");
			$tmp_nonce = $security->getToken();
			
			$ip = $security->sanitize($_SERVER['REMOTE_ADDR'],'field');
			$install_nonce = sha1($ip . PHP_VERSION_ID . $tmp_nonce);
			
			$sdata 	 = fread($session,1);
			
			if(strlen($sdata) >= 1) {
				fclose($session);
				die("Unable to continue installation. Reason: installer has been run before, or is in use by another user. For security reasons, we cannot have more than one installer running at once or an installation that has already been completed. If this is in error, delete <a href=\"administration/session.ses\">session.ses</a> manually and run the installer again.");
				} else {
				$tmp_nonce = $security->getToken();
				$install_nonce = 'TINYSHOP-INSTALL-ID:' . sha1($_SERVER['REMOTE_ADDR'] . PHP_VERSION_ID . $tmp_nonce) . '-IP:' . $ip;
				fwrite($session, $install_nonce);
				fclose($session);
			}
			
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

				if(!isset($_POST['shop_folder'])) {
					echo 'Shop folder cannot be empty, setup could not continue.';
					exit;				
					} else {
					$ts_shop_folder = $shop->sanitize($_POST['shop_folder'],'alpha');
				}
				
/***
/* Create a .htpasswd programatically.
/* For better security, it would be good to create it manually. Consider it.
*/
	
function create_htpasswd($username,$password) {

	$encrypted_password = crypt($password, base64_encode($password));
	$data = $username.":".$encrypted_password;
					
	$ht = fopen("administration/.htpasswd", "w") or die("Could not open .htpassword for writing. Please make sure that the server is allowed to write to the administration folder. In Apache, the folder should be chowned to www-data. ");
	fwrite($ht, $data);
	fclose($ht);
}

function create_htaccess_root($ts_shop_folder) {
	
$htaccess_mod = '
Options All -Indexes
Options +FollowSymLinks

RewriteEngine On

# Rewrite URI\'s
RewriteCond %{HTTPS} !on
RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI}

# build this dynamically

# product single item
RewriteRule ^category/(.*)/(.*)/(item)/(.*)/(.*)/(.*)/(.*)/$ /'.$ts_shop_folder.'/product.php?cat=$1&subcat=$2&product=$5&productid=$6&page=$7 [NC,L]
RewriteRule ^category/(.*)/(.*)/(item)/(.*)/(.*)/(.*)/$ /'.$ts_shop_folder.'/product.php?cat=$1&subcat=$2&product=$5&productid=$6 [NC,L]

RewriteRule ^category/(.*)/(item)/(.*)/(.*)/(.*)/(.*)/(.*)/$ /'.$ts_shop_folder.'/product.php?cat=$1&product=$4&productid=$5&page=$6 [NC,L]
RewriteRule ^category/(.*)/(item)/(.*)/(.*)/(.*)/(.*)/$ /'.$ts_shop_folder.'/product.php?cat=$1&product=$4&productid=$5&page=$6 [NC,L]
RewriteRule ^category/(.*)/(item)/(.*)/(.*)/(.*)/$ /'.$ts_shop_folder.'/product.php?cat=$1&product=$4&productid=$5 [NC,L]

# ^ watch out for double json entries without unique productId, cannot filter through .htaccess.

# products index
RewriteRule ^category/(.*)/(item)/(.*)/(.*)/(.*)/(.*)$ /'.$ts_shop_folder.'/product.php?cat=$1&product=$4&productid=$5&productid=$6 [NC,L]
RewriteRule ^category/(.*)/(item)/(.*)/(.*)/(.*)/$ /'.$ts_shop_folder.'/product.php?cat=$1&product=$4&productid=$5 [NC,L]

# single cat pag.
RewriteRule ^category/(.*)/(.*)/$ /'.$ts_shop_folder.'/category.php?cat=$1&page=$2 [NC,L]
# single cat
RewriteRule ^category/(.*)/$ /'.$ts_shop_folder.'/category.php?cat=$1 [NC,L]

# subcat pag.
RewriteRule ^subcategory/(.*)/(.*)/(.*)/$ /'.$ts_shop_folder.'/category.php?cat=$1&subcat=$2&page=$3 [NC,L]
# subcat
RewriteRule ^subcategory/(.*)/(.*)/$ /'.$ts_shop_folder.'/category.php?cat=$1&subcat=$2 [NC,L]

RewriteRule ^blog/$ /'.$ts_shop_folder.'/pages/blog.php  [NC,L]
RewriteRule ^articles/$ /'.$ts_shop_folder.'/pages/articles.php  [NC,L]
RewriteRule ^pages/$ /'.$ts_shop_folder.'/pages/page.php  [NC,L]

RewriteRule ^blog/(.*)/(.*)/$ /'.$ts_shop_folder.'/pages/blog.php?blogid=$1&blogtitle=$2  [NC,L]
RewriteRule ^pages/(.*)/(.*)/$ /'.$ts_shop_folder.'/pages/page.php?pageid=$1&pagetitle=$2  [NC,L]
RewriteRule ^articles/(.*)/(.*)/$ /'.$ts_shop_folder.'/pages/articles.php?articleid=$1&articletitle=$2  [NC,L]

RewriteRule ^'.$ts_shop_folder.'/blog/$ /'.$ts_shop_folder.'/pages/blog.php  [NC,L]
RewriteRule ^'.$ts_shop_folder.'/articles/$ /'.$ts_shop_folder.'/pages/articles.php  [NC,L]
RewriteRule ^'.$ts_shop_folder.'/blog/(.*)/(.*)/$ /'.$ts_shop_folder.'/pages/blog.php?blogid=$1&blogtitle=$2  [NC,L]
RewriteRule ^'.$ts_shop_folder.'/pages/(.*)/(.*)/$ /'.$ts_shop_folder.'/pages/page.php?pageid=$1&pagetitle=$2  [NC,L]
RewriteRule ^'.$ts_shop_folder.'/articles/(.*)/(.*)/$ /'.$ts_shop_folder.'/pages/articles.php?articleid=$1&articletitle=$2  [NC,L]

RewriteRule ^vacation/(.*)$ /'.$ts_shop_folder.'/pages/shop-error.php?reason=1 [NC,L]
RewriteRule ^offline/(.*)$ /'.$ts_shop_folder.'/pages/shop-error.php?reason=2 [NC,L]
RewriteRule ^closed/(.*)$ /'.$ts_shop_folder.'/pages/shop-error.php?reason=3 [NC,L]

# /query/rnd/action/code/
RewriteRule ^query/(.*)/(.*)/(.*)/$ query.php?action=$2&code=$3  [NC,L]

# /wishlist/rnd/action/product/tr/
RewriteRule ^wishlist/(.*)/(.*)/(.*)/(.*)/$ query.php?action=$2&product=$3&tr=$4  [NC,L]

# /cart/action/rnd/product/
# /cart/addtocart/rnd/id/

RewriteRule ^cart/$ cart.php [NC,L]

RewriteRule ^cart/checkout/$ checkout.php [NC,L]
RewriteRule ^'.$ts_shop_folder.'/cart/checkout/$ checkout.php [NC,L]

RewriteRule ^cart/cancel/$ query.php?action=cancel [NC,L]
RewriteRule ^cart/paid/$ query.php?action=payed [NC,L]
RewriteRule ^'.$ts_shop_folder.'/cart/paid/$ query.php?action=payed [NC,L]
RewriteRule ^cart/ipn/$ query.php?action=ipn [NC,L]
RewriteRule ^'.$ts_shop_folder.'/cart/delete/(.*)/$ query.php [NC,L]
RewriteRule ^'.$ts_shop_folder.'/cart/update/(.*)/$ query.php?action=$1 [NC,L]
RewriteRule ^cart/(.*)/(.*)/$ query.php?action=$1 [NC,L]
RewriteRule ^'.$ts_shop_folder.'/cart/(.*)/(.*)/$ query.php?action=$1 [NC,L]

# Webapplication firewall.

RewriteCond %{REQUEST_METHOD}  ^(HEAD|TRACE|DELETE|TRACK) [NC,OR]
RewriteCond %{HTTP_REFERER}    ^(.*)(<|>|\'|%0A|%0D|%27|%3C|%3E|%00).* [NC,OR]
RewriteCond %{REQUEST_URI}     ^/(,|;|<|>|/{2,999}).* [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^$ [OR]
RewriteCond %{HTTP_USER_AGENT} ^(java|curl|wget).* [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^.*(winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner).* [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^.*(libwww|curl|wget|python|nikto|scan).* [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^.*(<|>|\'|%0A|%0D|%27|%3C|%3E|%00).* [NC,OR]
RewriteCond %{HTTP_COOKIE}     ^.*(<|>|\'|%0A|%0D|%27|%3C|%3E|%00).* [NC,OR]
RewriteCond %{QUERY_STRING}    ^.*(;|\'|").*(union|select|insert|declare|drop|update|md5|benchmark).* [NC,OR]
RewriteCond %{QUERY_STRING}    ^.*(localhost|loopback|127\.0\.0\.1).* [NC,OR]
RewriteCond %{QUERY_STRING}    ^.*\.[A-Za-z0-9].* [NC,OR] # prevents shell injection
RewriteCond %{QUERY_STRING}    ^.*(<|>|\'|%0A|%0D|%27|%3C|%3E|%00).* [NC]
RewriteRule ^(.*)$ index.php

# Prevent framing
# Header set X-Frame-Options SAMEORIGIN env=!allow_framing

<IfModule mod_headers.c>
    Header unset ETag
</IfModule>

<FilesMatch "(\.(bak|config|dist|fla|inc|ini|log|psd|sh|sql|swp)|~)$">
    # Apache 2.2
    Order allow,deny
    Deny from all
    Satisfy All
    # Apache 2.4
    # Require all denied
</FilesMatch>

<IfModule mod_deflate.c>

    # Compress all output labeled with one of the following MIME-types
    <IfModule mod_filter.c>
        AddOutputFilterByType DEFLATE application/atom+xml \
                                      application/javascript \
                                      application/json \
                                      application/rss+xml \
                                      application/x-web-app-manifest+json \
                                      application/xhtml+xml \
                                      application/xml \
                                      font/opentype \
                                      image/svg+xml \
                                      image/x-icon \
                                      text/css \
                                      text/html \
                                      text/plain \
                                      text/x-component \
                                      text/xml
    </IfModule>

</IfModule>
';

if(!is_writable(".htaccess")) {

chmod(".htaccess",0775);

}

$hta_root = fopen(".htaccess", "w+") or die("Unable to open .htaccess");
fwrite($hta_root, $htaccess_mod);
fclose($hta_root);

}

/***
/* Create a .htaccess programatically.
*/	
				
function create_htaccess($ip,$root,$ts_shop_folder) {
					
$htaccess = 'AuthType Basic
AuthName "Tinyshop Administration"
AuthUserFile '.$root.'/'.$ts_shop_folder.'/administration/.htpasswd
Require valid-user
Order Deny,Allow
Deny from all
Allow from '.$ip.'
';

if(!is_writable("administration/.htaccess")) {

chmod("administration/.htaccess",0775);

}
	
$hta = fopen("administration/.htaccess", "w+") or die("Unable to open administration .htaccess");
fwrite($hta, $htaccess);
fclose($hta);

}

				$username = $shop->sanitize($_POST['admin_username'],'table');
				$password = $shop->sanitize($_POST['admin_password'],'table');
				$ip 	  = $shop->sanitize($_POST['admin_ip'],'table');
				$root 	  = $shop->sanitize($_SERVER['DOCUMENT_ROOT'],'table');
				
				create_htpasswd($username,$password);
				create_htaccess($ip,$root,$ts_shop_folder);
				create_htaccess_root($ts_shop_folder);

				/***
				/* Store Site JSON configuration.
				*/	
				
				$keys = 'inventory/site.json';
				$shop->backup($keys);
				$json = $shop->load_json($keys); 
				
				$json[0]["site.canonical"] 	= $ts_shop_folder;
				$json[0]["site.url"] 		= $shop->sanitize($_POST['admin_website'],'url');
				$json[0]["site.domain"] 	= $shop->sanitize($_POST['admin_website'],'url');
				$json[0]["site.currency"] 	= $shop->sanitize($_POST['admin_currency'],'num');
				$json[0]["site.email"] 		= $shop->sanitize($_POST['admin_email'],'url');

				if($_POST['admin_encryption'] == '1') {
					$json[0]["site.email"] = $shop->encrypt($shop->sanitize($_POST['admin_email'],'url'));
					} else {
					$json[0]["site.email"] = $shop->sanitize($_POST['admin_email'],'url');
				}

				$shop->storedata($keys,$json);

				/***
				/* Store PayPal JSON configuration.
				*/
				
				$keys_paypal = 'payment/paypal/paypal.json';
				$shop->backup($keys_paypal);
				$json_paypal = $shop->load_json($keys_paypal); 		
				$json_paypal[0]["paypal.domain"] = $shop->sanitize($_POST['admin_website'],'url');		
				$json_paypal[0]["paypal.email"] = $shop->sanitize($_POST['admin_paypal_email'],'url');
				
				$shop->storedata($keys_paypal,$json_paypal);

		/***
		/* If successful, show the following message.
		*/
				
		echo '<pre>';
		echo 'TinyShop was installed and should function correctly! If not, please read the manual on Github: https://github.com/flaneurette/tiny-shop'. PHP_EOL;
		echo 'Please delete the install.php file, or <a href="install.php?delete='.$shop->sanitize($nonce,'alphanum').'">click here.</a> to let Tinyshop do it for you'. PHP_EOL;
		echo '</pre>';	
		
	} elseif(isset($_POST['setup-complete']) == 1) {
		
		echo '<pre>';
		echo 'TinyShop was installed and should function correctly! If not, please read the manual on Github: https://github.com/flaneurette/tiny-shop'. PHP_EOL;
		echo 'Please delete the install.php file, or <a href="install.php?delete='.$shop->sanitize($nonce,'alphanum').'">click here.</a> to let Tinyshop do it for you'. PHP_EOL;
		echo '</pre>';		

		// make files non-writeable again.

		chmod("administration/.htpasswd",0755);
		chmod("administration/session.ses",0755);
		chmod("administration/.htaccess",0755);
		chmod(".htaccess",0755);
		chmod("payment/paypal/paypal.json",0755);
		chmod("inventory/site.json",0755);
	
	} else {
		
// End of Installer
?>
		<!DOCTYPE html>
		<html>
		
			<head>
				<link rel="stylesheet" type="text/css" href="resources/admin.css">
			</head>
			
			<body>
			<h1>Setup TinyShop</h1>
			<div>
			<?php echo 'All requirements were met. Continue to configure TinyShop';?>
			</div>
			<hr />
				<div id="ts-shop-cart-form">
					<form name="" action="" method="post">
						<input name="setup" value="1" type="hidden">
						<input name="nonce" value="<?php echo $shop->sanitize($nonce,'alphanum');?>" type="hidden">
						Website: <input name="admin_website" value="https://<?php echo $host;?>" type="text"> Shop folder /shop/ <input name="shop_folder" value="shop" type="text" alt="Without slashes" title="Without slashes">
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
						<strong>Security.</strong> Encrypt e-mail address? 
						<select name="admin_encryption">
							<option value="2">No</option>
							<option value="1">Yes</option>
						</select> <sup>(if NO, it will be visible to everyone)</sup>
						<hr />
						Admin IP: <input name="admin_ip" value="<?php echo  $shop->sanitize($_SERVER['REMOTE_ADDR'],'table');?>" type="text">
						<hr />
						PayPal e-mail (to accept payments on): <input name="admin_paypal_email" value="info@website.com" type="text">
						<hr />
						<input type="submit" value="Setup TinyShop &raquo;">
						<br />
						<hr />
					</form>
				</div>
			</body>
		</html>
<?php
	
	}
?>