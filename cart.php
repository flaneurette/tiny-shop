<?php
	include("resources/php/header.inc.php");
	include("class.Shop.php");
	$shop = new Shop();
?>
<!DOCTYPE html>
<html>
	<head>
	<?php
	echo $shop->getmeta();				
	?>
	</head>
	<body>
		<h1>Shopping Cart</h1>
		
		<div id="ts-shop-cart-form">
		
		<form name="ts_cart" method="post" id="ts-shop-cart-form-data">
		<hr />
			<select name="payment_gateway" id="ts-form-cart-payment-gateway-select">
			<option value="">Select payment method...</option>
			<?php
			// dynamically generate payment gateways from site.json
			$siteconf = $shop->load_json("inventory/site.json");
			$keys = 'site.payment.gateways';
			echo $shop->gatewaylist($siteconf,$keys);
			?>
			</select>
			
		<div class="ts-shop-form-field">
		<?php
			// dynamically generate form fields from customer.json.
			$ignore = ['customer.id','customer.diff','customer.ua','customer.signup.ua','customer.hash','customer.signup.date','customer.signup.ip'];
			$split = 6; 
			$shopconf = $shop->load_json("inventory/customer.json");
			echo $shop->generatecart($shopconf,$split,$ignore);
		?>
		</div>

		<input type="submit" name="submit" value="Submit">
		</form>
		</div>
	</body>
</html>