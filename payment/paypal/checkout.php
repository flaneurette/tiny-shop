<?php

	include("../../resources/php/header.inc.php");
	include("../../resources/php/class.Session.php");
	include("../../class.Shop.php");
	
	$session = new Session();
	$session->sessioncheck();
	
	$shop  		= new Shop();
	$shopconf 	= $shop->load_json("paypal.json");
	
	if(!empty($_GET)) {	
		$shop->message('Gateway cannot be accessed this way. Please open the cart on the shop website.');
		$shop->showmessage();
		exit;
	}
	
	if(!isset($_SESSION['token'])) {
		$shop->message('Token is not set.');
		$shop->showmessage();
		exit;	
	}
	
	if(!isset($_SESSION['cartid'])) {
		$shop->message('Cart ID is not set.');
		$shop->showmessage();
		exit;
	}	
	
	if(isset($_SESSION['token'])) {
		
		$token = $_SESSION['token'];
		
			if($token != $_POST['token']) {
				$shop->message('Token is incorrect.');
				$shop->showmessage();
				exit;
			}
	
		} else {
			
		$shop->message('Token is incorrect or not set.');
		$shop->showmessage();
		exit;
	}
	
	if(!isset($_POST['checkout-post-gateway'])) {	
		$shop->message('Gateway page could not be loaded from resource and cannot be accessed this way.');
		$shop->showmessage();
		exit;
	}
	
	$cartid 			= $shop->sanitize($_SESSION['cartid'],'alphanum');
	$productsum_total 	= (int)$_SESSION['subtotal'];
	$country_price 		= (int)$_SESSION['shipping'];
	$total_price 		= (int)$_SESSION['totalprice'];
			
	/* No need to edit this below. 
	*  Start of PayPal code 
	*/
		
	// Price of the product.
	$item_price = $productsum_total;
	// Handling price.
	$handling_price = 0;
	// Shipping price.
	$shipping_price = $country_price;
	
	// PayPal variables: only edit this in paypal.json!
	$paypal_domain 				= $shop->cleanInput($shopconf[0]['paypal.domain']);
	$paypal_cancel_page 		= $shop->cleanInput($shopconf[0]['paypal.cancel.page']);
	$paypal_return_page 		= $shop->cleanInput($shopconf[0]['paypal.return.page']);
	$paypal_email 				= $shop->cleanInput($shopconf[0]['paypal.email']);
	$paypal_notify_url 			= $shop->cleanInput($shopconf[0]['paypal.notify.url']);
	$paypal_currency_code 		= $shop->cleanInput($shopconf[0]['paypal.currency.code']);
	$paypal_invoice_number 		= $shop->cleanInput($shopconf[0]['paypal.custom.field']);
	
	if(empty($paypal_invoice_number)) {
		// should not be empty.
		$paypal_invoice_number 	= 1;
	}
	
	$paypal_image_url 			= $shop->cleanInput($shopconf[0]['paypal.image.url']);
	
	if(empty($paypal_image_url)) {
		$paypal_image_url 		= 'http://www.paypal.com/en_US/i/btn/x-click-but01.gif';
	}
	
	$paypal_no_note 			= $shop->cleanInput($shopconf[0]['paypal.no.note']);
	$paypal_no_shipping 		= $shop->cleanInput($shopconf[0]['paypal.no.shipping']);
	$paypal_on0 				= $shop->cleanInput($shopconf[0]['paypal.on0']);
	$paypal_on1 				= $shop->cleanInput($shopconf[0]['paypal.on1']);
	$paypal_os0 				= $shop->cleanInput($shopconf[0]['paypal.os0']);
	$paypal_os1 				= $shop->cleanInput($shopconf[0]['paypal.os1']);
	$paypal_show_user_details 	= $shop->cleanInput($shopconf[0]['paypal.show.user.details']);
	$paypal_store_user_details 	= $shop->cleanInput($shopconf[0]['paypal.store.user.details']);
	
	/*
	* doc: https://developer.paypal.com/docs/paypal-payments-standard/integration-guide/Appx-websitestandard-htmlvariables/#individual-items-variables
	*/
	
?>
<!DOCTYPE html>
<html>
	<head>
	<?php
	echo $shop->getmeta("../../inventory/site.json");				
	?>
	</head>
	<body>
	<h1>Payment with PayPal.</h1>
	<form action="https://www.paypal.com/us/cgi-bin/webscr" method="post">	

						<input type="hidden" name="item_name" maxlength="127" size="20" value="A product" title="cart item, 127 chars">
						<input type="hidden" name="item_number" maxlength="127" size="20" value="123456" title="track payments, 127 chars">
						<input type="hidden" name="item_price" maxlength="127" size="20" id="item_price" value="<?=$item_price;?>" title="" disabled>
						
						<!-- required -->
						<input type="hidden" name="amount" maxlength="127" size="20" id="item_price" value="<?=$productsum_total;?>" title="">
						<input type="hidden" name="quantity" value="1">

						<input type="hidden" name="no_note" maxlength="1" min="0" max="1" value="1" title="0 or 1. 1 = no prompt">
						<input type="hidden" name="no_shipping" maxlength="1" min="0" max="1" value="1" title="0 or 1. 0 = to add shipping address">
						<input type="hidden" name="shipping" id="shipping" size="5" title="The item's shipping cost" value="<?=$shipping_price;?>">
						<input type="hidden" name="handling" id="handling" size="5" title="handling cost" value="<?=$handling_price;?>">
						<input type="hidden" name="amount" size="5" id="total_amount" title="total amount" value="<?=$total_price;?>" disabled>	

			<div id="ts-shop-form">
				<div class="ts-shop-form-section">	
					<input type="hidden" name="image_url" value="<?=$paypal_image_url;?>">
					<input type="hidden" name="currency_code" value="<?=$paypal_currency_code;?>">		
					<input type="hidden" name="business" value="<?=$paypal_email;?>">
					<input type="hidden" name="cancel_return" value="<?=$paypal_domain.''.$paypal_cancel_page;?>">
					<input type="hidden" name="custom" value="<?=$paypal_currency_code;?>">
					<input type="hidden" name="invoice" value="<?=$paypal_invoice_number;?>">
					<input type="hidden" name="notify_url" value="<?=$paypal_domain.''.$paypal_notify_url;?>">
					<?php
					if($paypal_on0) {
					?>
						<input type="hidden" name="on0" maxlength="64" value="<?=$paypal_on0;?>">
						<input type="hidden" name="on1" maxlength="64" value="<?=$paypal_on1;?>">
					<?php
					}
					if($paypal_os0) {
					?>
						<input type="hidden" name="os0" maxlength="64" value="<?=$paypal_os0;?>">
						<input type="hidden" name="os1" maxlength="64" value="<?=$paypal_os1;?>">
					<?php
					}
					?>
					<input type="hidden" name="return" value="<?=$paypal_domain.''.$paypal_return_page;?>">
					<!-- optional -->			
					<input type="hidden" name="cmd" value="_ext-enter">
					<input type="hidden" name="redirect_cmd" value="_xclick">
					
					<label for="address1">Address</label>
					<input type="text" name="address1" id="address1" maxlength="100" value="" title="The first line of the customer's address (100-alphanumeric character limit).">
					<label for="city">City</label>
					<input type="text" name="city" id="city" maxlength="100" value="" title="The city noted in the customer's address (100-alphanumeric character limit).">
					<label for="day_phone_a">Area code</label>
					<input type="text" name="day_phone_a" id="day_phone_a" size="5" value="">
					
					<label for="state">State</label>
					<input type="text" name="state" id="state" size="2" maxlength="2" value="" title="The state noted in the customer's address (the official two-letter abbreviation).">
					<label for="zip">Zip</label>
					<input type="text" name="zip" id="zip" size="5"  maxlength="32" value="" title="The postal code noted in the customer's address.">
				</div>
				<div class="ts-shop-form-section">	
					<label for="email">E-mail</label>
					<input type="text" name="email" id="email" size="15" value="" title="The customer's email address.">
					<label for="day_phone_b">Phone</label>
					<input type="text" name="day_phone_b" id="day_phone_b" size="7" value="" value="">
					<label for="first_name">First name</label>
					<input type="text" name="first_name" id="first_name" size="15" maxlength="32" value="" title="The customer's first name (32-alphanumeric character limit).">
					<label for="last_name">Last name</label>
					<input type="text" name="last_name" id="last_name" size="15" maxlength="64" value="" title="The customer's last name (64-alphanumeric character limit).">
					<!-- 
						input type="text" name="night_phone_a" id="night_phone_a" value="The area code of the customer's evening telephone number.">
						<input type="text" name="night_phone_b" id="night_phone_b" value="The first three digits of the customer's evening telephone number.">
					-->
					<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" name="submit" title="Make payments with PayPal - it's fast, free and secure!">
				</div>
			</div>
			</form>
			
			<hr />
			
	</body>
</html>
