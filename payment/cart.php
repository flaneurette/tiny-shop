<?php

	include("../resources/php/header.inc.php");
	include("../class.Shop.php");
	
	$shop     = new Shop();
	$shopconf = $shop->load_json("../inventory/customer.json");

?>

<html>
	<head>
	<link rel="stylesheet" type="text/css" href="../resources/reset.css">
	<link rel="stylesheet" type="text/css" href="../resources/style.css">
	</head>
	<body>
		<h1>Shopping Cart</h1>
		
		<div id="ts-shop-cart-form">
		
		<form name="" method="" id="ts-shop-cart-form-data">
		<hr />
			<select name="payment_gateway" id="ts-form-cart-payment-gateway-select">
				<option value="">Select payment method...</option>
				<option value="ACH">ACH</option>
				<option value="Alipay">Alipay</option>
				<option value="Apple Pay">Apple Pay</option>
				<option value="Bancontact">Bancontact</option>
				<option value="BenefitPay">BenefitPay</option>
				<option value="Boleto Bancário">Boleto Bancário</option>
				<option value="CitrusPay">Citrus Pay</option>
				<option value="EPS">EPS</option>
				<option value="Fawry">Fawry</option>
				<option value="Giropay">Giropay</option>
				<option value="Google Pay">Google Pay</option>
				<option value="PayPal">PayPal</option>
				<option value="KNET">KNET</option>
				<option value="Klarna">Klarna</option>
				<option value="Mada">Mada</option>
				<option value="Multibanco">Multibanco</option>
				<option value="OXXO">OXXO</option>
				<option value="Pago Fácil">Pago Fácil</option>
				<option value="Poli">Poli</option>
				<option value="Przelewy24">Przelewy24</option>
				<option value="QPAY">QPAY</option>
				<option value="Rapipago">Rapipago</option>
				<option value="SEPA Direct Debit">SEPA Direct Debit</option>
				<option value="Sofort">Sofort</option>
				<option value="Stripe">Stripe</option>
				<option value="Via Baloto">Via Baloto</option>
				<option value="iDEAL">iDEAL</option>
			</select>
			
		<div class="ts-shop-form-field">
		<?php
		
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
