<?php

session_start();
/* 
 * TinyShop PHP Query script that handles all XHR queries.
*/
include("resources/php/class.Session.php");

$session = new Session();

include("resources/php/class.Security.php");

$secure = new Security();

/* todo: 
	+ add anti-csrf
*/

$default = null;

if(isset($_GET['action'])) {
	
	$action = $_GET['action'];
	
	if(preg_match("/[a-zA-Z]/i",$action)) {
		
		$action = $secure->sanitize($action,'alpha');
		
		switch($action) {
			
			case 'addtocart':
			
			$id = (int)$secure->sanitize($_GET['id'],'num');
			$qty = (int)$secure->sanitize($_GET['qty'],'num');
			
			$arr = [
				'product.id' => $id,
				'product.qty' => $qty
			];

			$session->addtocart($arr);
			// echo $_SESSION['cart'][0]['product.id'];
			// echo $_SESSION['cart'][0]['product.qty'];
			echo "Product added to cart.";
			break;
			
			case 'deletefromcart':
			$id = (int)$secure->sanitize($_GET['id'],'num');
			break;
			
			case 'emptycart':
			$cartid = (int)$secure->sanitize($_GET['cartid'],'num');
			break;			
			
			case 'voucher':
			$code = $secure->sanitize($_GET['code'],'alpha');
			break;
			
			case 'wishlist':
			$product = $secure->sanitize($_GET['product'],'alpha'); 
			$tr 	 = $secure->sanitize($_GET['tr'],'alpha'); 
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
