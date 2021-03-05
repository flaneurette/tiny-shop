<?php

include("resources/php/header.inc.php");

// debugging:
// echo $_SESSION['token'];
// var_dump($_SESSION);

include("resources/php/class.Session.php");
include("resources/php/class.Security.php");

$session = new Session();
$secure = new Security();

if(isset($shop)) {
	$host = $shop->gethost("inventory/site.json");
	} else {
	require("class.Shop.php");
	$shop  = new Shop();
	$host 		= $shop->gethost("inventory/site.json");
	$host_path 	= $shop->gethost("inventory/site.json",true);
}

if(isset($_POST['token']))  {
	// A token was provided through $_POST data. Check if it is the same as our session token.
	if($_POST['token'] == $_SESSION['token']) {
		// token is correct.
		} else {
			echo 'token is incorrect';
			// echo $_SESSION['token'];
		exit;
	}
}

if(isset($_POST['qty'])) {
	if(is_int($_POST['qty'])) {
		if($_POST['qty'] > 9999) {
			$qty = 1;
		}
	} else {
		$qty = (int)$_POST['qty'];
	}
}
			
$default = null;

if(isset($_GET['action'])) {
	
	$action = $_GET['action'];
	
	if(preg_match("/[a-zA-Z]/i",$action)) {
			
		$action = $secure->sanitize($action,'alpha');
		
		switch($action) {
			
				case 'cancel':
				echo "Checkout has been cancelled.";
				// redirect to cart
				header('Location: '.$host, true, 302);
				exit;
				break;
				
				case 'payed':
				case 'paid':
				
				// update stock here.
				header('Location: '.$host.'/payment/paid/index.php?token='.$secure->sanitize($_SESSION['token'],'alphanum'), true, 302);
				exit;
				
				break;	
				
				case 'ipn':
				echo "Checkout process has been notified.";
				break;
		}
	}	
}

if(isset($_POST['action'])) {
	
	$action = $_POST['action'];
	
	if(preg_match("/[a-zA-Z]/i",$action)) {
		
		$action = $secure->sanitize($action,'alpha');
		
		switch($action) {
			
			case 'addtocart':
			
				$id  = (int)$secure->sanitize($_POST['id'],'num');
				$qty = (int)$secure->sanitize($_POST['qty'],'num');
				
				$arr = [
						'product.id' => $id,
						'product.qty' => $qty
				];

				$session->addtocart($arr);

				$_SESSION['cart'] = $session->unique_array($_SESSION['cart'], 'product.id');

				echo "Product added to cart. ";
				echo PHP_EOL;
				echo "X";
			
			break;			
			
			case 'deletefromcart':
			
				$id = (int)$secure->sanitize($_POST['id'],'num');
				
				if($id) {
					$_SESSION['cart'] = $session->deletefromcart($id);
				}
				
				$_SESSION['cart'] = array_values($_SESSION['cart']);
				
				echo "Product deleted from cart.";
			
			break;
			
			case 'updatecart':
			
				$id = (int)$secure->sanitize($_POST['id'],'num');
				$qty = (int)$secure->sanitize($_POST['qty'],'num');
				
				if($id) {
					$_SESSION['cart'] = $session->updatecart($id,$qty);
				}

				$_SESSION['cart'] = array_values($_SESSION['cart']);
				echo "Cart has been updated.";
			
			break;			
			
			case 'emptycart':
				
				$cartid = (int)$secure->sanitize($_POST['cartid'],'num');
				$_SESSION['cart'] = [];
				echo "Cart was emptied.";
				
			break;			
			
			case 'voucher':
				$code = $secure->sanitize($_POST['code'],'alpha');
			break;
			
			case 'wishlist':
				$product = $secure->sanitize($_POST['product'],'alpha'); 
				$tr 	 = $secure->sanitize($_POST['tr'],'alpha'); 
			break;	
			
			case 'query':
			
			break;
		}
	

	} else {
	// contains other characters.
	echo $default;
	}
} else {
	echo $default;
}
?>