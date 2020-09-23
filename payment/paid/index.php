<?php

	include("../../resources/php/header.inc.php");
	include("../../resources/php/class.Session.php");
	include("../../resources/php/class.SecureMail.php");
	include("../../class.Shop.php");
	
	$shop = new Shop();
	$session = new Session();
	
	$session->sessioncheck();
	
	if(isset($_SESSION['token'])) {
		
		$token = $_SESSION['token'];
		
			if($token != $_GET['token']) {
				$shop->message('Transaction completed, however token is incorrect. Please contact the shop owner if issues arrive through either e-mail or the contact form. N.B. The shopowner has not been notified of this error.');
				$shop->showmessage();
				//exit;
			}
	
		} else {
			
		$shop->message('Transaction completed, however token is incomplete. Please contact the shop owner if issues arrive through either e-mail or the contact form. N.B. The shopowner has not been notified of this error.');
		$shop->showmessage();
		//exit;
	}


	/*
		$item_name = $_POST['item_name'];
		$item_number = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$payment_amount = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id = $_POST['txn_id'];
		$receiver_email = $_POST['receiver_email'];
		$payer_email = $_POST['payer_email'];
	*/

	$paypalinvoice = (int)$_REQUEST['invoice'];
	
	$dir = 	'../../inventory/orders.conf.json';
	
	$invoiceid = $shop->invoiceid($dir,'get');
	
	if($paypalinvoice != $invoiceid) {

		// different invoice ID, check for race condition.
				// probable race condition.
				$invoicediff = ($invoiceid - $_SESSION['invoiceid']);

				if($invoicediff == 1) {
					$shop->invoiceid($dir,'set',$invoiceid+1);
					} elseif($invoicediff > 1) {
					// certainly race condition.
					// mail shop owner here
					} else {
					$shop->invoiceid($dir,'set',$invoiceid+1);
				}
	
	} else {

		$shop->invoiceid('set',$invoiceid+1);
	}

	
	$sitecurrency = $shop->getsitecurrency('../../inventory/site.json','../../inventory/currencies.json');
	$shippingcountry = $shop->sanitize($_SESSION['shipping_country'],'encode');
	$siteconf = $shop->load_json("../../inventory/shipping.json");
	$countryprice = $shop->getcountryprice($siteconf,$shippingcountry);
	
	if($countryprice != false) {
		$country_price = (int)$countryprice;
		} else {
		$country_price = 10; // default shipping fee.
	}
	
	// mail to shopowner.

	$setup = new \security\forms\SecureMail();

	$siteconf = $shop->load_json("../../inventory/site.json");
	$result = $shop->getasetting($siteconf,'site.email');

	if($result["site.email"] != '') {
		if(strlen($result["site.email"]) > 64) {
			$email = $shop->decrypt($result["site.email"]);
			} else {
			$email = $shop->sanitize($result["site.email"],'email');
		}
	}
	
	$siteconf = $shop->load_json("../../inventory/site.json");
	$result = $shop->getasetting($siteconf,'site.title');
	
	if($result["site.title"] != '') {
		if(strlen($result["site.title"]) > 10) {
			$shopname = $shop->sanitize($result["site.title"],'unicode');
			} else {
			$shopname = 'Webshop owner';
		}
	}

	$body  = "Today, a new order was placed in the webshop and paid. Below are the details of the order.".PHP_EOL . PHP_EOL;
	$body .= "### ORDER ###".PHP_EOL;
	
	if(isset($_SESSION['cart']) && count($_SESSION['cart']) >= 1) {
		
		$products = $shop->getproductlist("../../inventory/shop.json");
		$productsum_total = 0;
		$productsum = 0;
		
		$c = count($_SESSION['cart']);
		
		for($i=0; $i < $c; $i++) {
			
			if($_SESSION['cart'][$i]) {
				$product = (int) $_SESSION['cart'][$i]['product.id'];
				if($_SESSION['cart'][$i]['product.qty'] == 0) {
					$_SESSION['cart'][$i]['product.qty'] = 1;
				}
				$productqty = $_SESSION['cart'][$i]['product.qty'];
			}
			
			$j = 0;

			if(isset($product)) {
			
				foreach($products as $key => $value) {
					
					if($products[$j][0][1] == $product) {
						
						$producttitle = $products[$j][2][1];
						$productdesc  = $products[$j][3][1];
						$productprice = $products[$j][18][1];
						
						if($productprice == null || $productprice == 0 ) {
							$productprice = 1;
						}
						
						if($productqty == null || $productqty == 0 ) {
							$productqty = 1;
						}					
								
						$productsum = round(($productprice * (int)$productqty),2);
						
						$qtyid = 'tscart-'.$j.$product;

			
					$body .='<div class="ts-shop-ul">';
					$body .='<li class="ts-shop-ul-li-item-product">'.$producttitle.'</li>';
					$body .='<li class="ts-shop-ul-li-item-description">'.$productdesc.'</li>';
					$body .='<li class="ts-shop-ul-li-item-price">'.$sitecurrency .' '.$productprice.'</li>';
					$body .='<li class="ts-shop-ul-li-item-qty">'.$productqty.'</li>';
					$body .='<li class="ts-shop-ul-li-item-total">'.$sitecurrency .' '. $productsum.'</li>';
					$body .='</div>';
					$body .='<div class="ts-shop-ul-set">';
					$body .='<div class="ts-shop-ul">';
					$body .='<li class="ts-shop-ul-li-item" width="10%"></li>';
					$body .='<li class="ts-shop-ul-li-item" width="10%">Country</li>';
					$body .='<li class="ts-shop-ul-li-item" width="30%">Subtotal</li>';
					$body .='<li class="ts-shop-ul-li-item" width="35%">Shipping &amp; handling</li>';
					$body .='<li class="ts-shop-ul-li-item" width="15%">Total</li>';
					$body .='</div>';
			
					$body .='<li class="ts-shop-ul-li-item">';
					$body .='</li>';
					$body .='<li class="ts-shop-ul-li-item">';
					$body .= str_replace('shipping.','',$shippingcountry);
					$body .='</li>';
					$body .='<li class="ts-shop-ul-li-item">';
					$body .= $sitecurrency .' '. (int) $_SESSION['subtotal'];
					$body .='</li>';
					$body .='<li class="ts-shop-ul-li-item">';
					$body .= $sitecurrency .' '. (int) $_SESSION['shipping'];
					$body .='</li>		';
					$body .='<li class="ts-shop-ul-li-item">';
					$body .= $sitecurrency .' '. (int) $_SESSION['totalprice'];
					$body .='</li>';
					$body .='</div>';
			
					}
				$j++;
			}
		}
	}
	
	}
	
	
	$parameters = array( 
		'to' => $email,
		'name' => $shopname,
		'email' => $email,				
		'subject' => "A new order was placed in the shop today.",
		'body' => $body
	);

	$ordermail = new \security\forms\SecureMail($parameters);
	$ordermail->sendmail();

	// destroy cart session.
	/*
	$_SESSION['cart']  = array();
	$_SESSION['token'] = null;
	$_SESSION['messages'] = array();
	session_destroy();
	*/
?>
