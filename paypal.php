<?php

	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();
	
	$paypal_domain = "https://www.shop.com";

	$paypal_cancel_page = "/pages/cancel";
	$paypal_return_page = "/pages/payed";
	$paypal_email = "info@shop.com";
	$paypal_notify_url = "/pages/ipn/";
	$paypal_tracking_number = "123456";
	$paypal_currency_code = "USD";
	$paypal_invoice_number = "123456";
?>
<!DOCTYPE html>
<html>
	<head>
	<?php
	echo $shop->getmeta();				
	?>
	</head>
	<body>
		<h1>Payment with PayPal.</h1>
			<div id="ts-shop-form">

			<form action="https://www.paypal.com/us/cgi-bin/webscr" method="post">

				<div class="ts-shop-form-section">
					<input type="hidden" name="currency_code" value="<?=$paypal_currency_code;?>">
					<input type="hidden" name="amount" value="total amount">
					<input type="hidden" name="business" value="<?=$paypal_domain;?>">
					<input type="hidden" name="cancel_return" value="<?=$paypal_cancel_page;?>">
					<input type="hidden" name="custom" value="<?=$paypal_currency_code;?>">
					<input type="hidden" name="handling" value="handling cost">
					<input type="hidden" name="image_url" value="150x50px">
					<input type="hidden" name="invoice" value="<?=$paypal_invoice_number;?>">
					<input type="hidden" name="item_name" maxlength="127" value="cart item, 127 chars">
					<input type="hidden" name="item_number" maxlength="127" value="track payments, 127 chars">
					<input type="hidden" name="no_note" maxlength="1" min="0" max="1" value="0 or 1. 1 = no prompt">
					<input type="hidden" name="no_shipping" maxlength="1" min="0" max="1" value="0 or 1. 0 = to add shpping address">
					<input type="hidden" name="notify_url" value="<?=$paypal_notify_url;?>">
					
					<!--
						<input type="hidden" name="on0" maxlength="64" value="The first option field name (64-character limit)">
						<input type="hidden" name="on1" maxlength="64" value="The second option field name (64-character limit).">
						<input type="hidden" name="os0" maxlength="200" value="The first set of option values (200-character limit).">
						<input type="hidden" name="os1"  maxlength="200" value="The second set of option values (200-character limit).">
					-->
					
					<label for="quantity">Quantity</label>
					<input type="number" name="quantity" maxlength="2" min="1" max="9999" value="1">
					<input type="hidden" name="return" value="<?=$paypal_return_page;?>">
					<input type="hidden" name="shipping" value="The item's shipping cost">
					
					<!-- optional -->
					
					<input type="hidden" name="cmd" value="_ext-enter">
					<input type="hidden" name="redirect_cmd" value="_xclick">
					
					<label for="address1">Address</label>
					<input type="text" name="address1" id="address1" maxlength="100" title="The first line of the customer's address (100-alphanumeric character limit).">
					<label for="city">City</label>
					<input type="text" name="city" id="city" maxlength="100" title="The city noted in the customer's address (100-alphanumeric character limit).">
					<label for="day_phone_a">Area code</label>
					<input type="text" name="day_phone_a" id="day_phone_a" size="5" value="">
					<label for="day_phone_b">Phone</label>
					<input type="text" name="day_phone_b" id="day_phone_b" size="7" value="">
				</div>
				
				<div class="ts-shop-form-section">	
				
					<label for="email">E-mail</label>
					<input type="text" name="email" id="email" size="15" title="The customer's email address.">
					<label for="first_name">First name</label>
					<input type="text" name="first_name" id="first_name" size="15" maxlength="32" title="The customer's first name (32-alphanumeric character limit).">
					<label for="last_name">Last name</label>
					<input type="text" name="last_name" id="last_name" size="15" maxlength="64" title="The customer's last name (64-alphanumeric character limit).">
					<!-- 
						input type="text" name="night_phone_a" id="night_phone_a" value="The area code of the customer's evening telephone number.">
						<input type="text" name="night_phone_b" id="night_phone_b" value="The first three digits of the customer's evening telephone number.">
					-->
					<label for="state">State</label>
					<input type="text" name="state" id="state" size="2" maxlength="2" title="The state noted in the customer's address (the official two-letter abbreviation).">
					<label for="zip">Zip</label>
					<input type="text" name="zip" id="zip" size="5"  maxlength="32" title="The postal code noted in the customer's address.">
					<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" name="submit" title="Make payments with PayPal - it's fast, free and secure!">
					
				</div>

			</form>

			</div>
	</body>
</html>
