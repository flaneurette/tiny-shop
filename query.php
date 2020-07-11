<?php

session_start();

/* 
 * TinyShop PHP Query script that handles all XHR queries.
*/

include("resources/php/class.Session.php");
include("resources/php/class.Security.php");

$session = new Session();
$secure = new Security();

if(isset($_POST['token']))  {
	
	// A token was provided through $_POST data. Check if it is the same as our session token.
	if($_POST['token'] === $_SESSION['token']) {
		// token is correct.
		} else {
		exit;
	}
}
			
$default = null;

if(isset($_GET['action'])) {
	$action = $_GET['action'];
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
			echo "Product added to cart.";
			
			break;
			
			case 'deletefromcart':
			
			$id = (int)$secure->sanitize($_POST['id'],'num');
			
			if($id) {
				$_SESSION['cart'] = $session->deletefromcart($id);
			}
			
			break;
			
			case 'emptycart':
			$cartid = (int)$secure->sanitize($_POST['cartid'],'num');
			$_SESSION['cart'] = [];
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
1
