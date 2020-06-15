<?php
	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop     = new Shop();
	$shopconf = $shop->load_json("inventory/customer.json");
	$siteconf = $shop->load_json("inventory/site.json");
?>

<html>
	<head>
	<link rel="stylesheet" type="text/css" href="resources/reset.css">
	<link rel="stylesheet" type="text/css" href="resources/style.css">
	</head>
	<body>
		<h1>Shopping Cart</h1>
		
		<div id="ts-shop-cart-form">
		
		<form name="" method="" id="ts-shop-cart-form-data">
		<hr />
			<select name="payment_gateway" id="ts-form-cart-payment-gateway-select">
			<option value="">Select payment method...</option>
			<?php
			// dynamically generate payment gateways from site.json
			if($siteconf !== null) {
				foreach($siteconf[0]['site.payment.gateways'] as $key => $value)
				{
					echo "<option value=\"".$value."\">".$value."</option>";			
				}		
			}
			?>
			</select>
			
		<div class="ts-shop-form-field">
		<?php
		
			// dynamically generate form fields from customer.json.
			$ignore = ['customer.id','customer.diff','customer.ua','customer.signup.ua','customer.hash','customer.signup.date','customer.signup.ip'];
			
			if($shopconf !== null) {

				$i = 0;
				$split = 6;
				
				foreach($shopconf as $row)
				{
					foreach($row as $key => $value)
					{
						if(!in_array($key,$ignore)) {
							
									
							$key = str_replace(['.','customer'],['',''],$key);
							$keycss = str_replace('.','-',$key);
							if($key == 'newsletter') {
								echo "<label>".ucfirst($key)."</label>";
								echo "<input type=\"checkbox\" id=\"".$keycss."\" name=\"".$key."\">";	
								} else {
								echo "<label>".ucfirst($key)."</label>";
								echo "<input type=\"text\" id=\"".$keycss."\" name=\"".$key."\">";
							}
							$i++;
						}
						
						if($i == $split) {
							echo "</div>";
							echo "<div class=\"ts-shop-form-field\">";
						}
					}
				}
			}
		
		?>
		</div>
			<hr />
			<input type="submit" name="submit" value="Submit">
		</form>
		</div>
	</body>
</html>
