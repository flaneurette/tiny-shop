<?php
	include("resources/php/header.inc.php");
	include("resources/php/class.Session.php");
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
	
	<ul class="ts-shop-ul">
	
		<li class="ts-shop-li">
			<div class="ts-shop-ul-li-item" width="60">&#128722;</div>
			<div class="ts-shop-ul-li-item">Product Name</div>
			<div class="ts-shop-ul-li-item">Description</div>
			<div class="ts-shop-ul-li-item">Price</div>
			<div class="ts-shop-ul-li-item">Quantity</div>
			<div class="ts-shop-ul-li-item">Delete</div>
		</li>
			
	<?php
	
	if(isset($_SESSION['cart'])) {
		
	$c = count($_SESSION['cart']);
		
		if(($c > 0) && ($c < 9999)) {
			
		$products = $shop->getproductlist();
			
		for($i=0; $i < $c; $i++) {
			
			$product = $_SESSION['cart'][$i]['product.id'];
			$qty = $_SESSION['cart'][$i]['product.qty'];
			$j=0;
			
			foreach($products as $key => $value) {
				
				if($products[$j][0][1] == $product) {
					
				//echo $products[$j][2][1]; // title
				//echo $products[$j][3][1]; // desc
				//echo $products[$j][18][1]; // price
		?>
			<li class="ts-shop-li">
				<div class="ts-shop-ul-li-item" width="60">&#128722;</div>
				<div class="ts-shop-ul-li-item"><?=$products[$j][2][1];?></div>
				<div class="ts-shop-ul-li-item"><?=$products[$j][3][1];?></div>
				<div class="ts-shop-ul-li-item"><?=$products[$j][18][1];?></div>
				<div class="ts-shop-ul-li-item"><input type="number" size="1" max="9999" value="<?=$qty;?>"></div>
				<div class="ts-shop-ul-li-item"><input type="button" value="X"></div>
			</li>
		<?php
				 }
				 $j++;
				}
			}
			echo "</ul>";
		}
	} else {
		echo "<h3>Cart is empty.</h3>";
	} 
		?>
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
