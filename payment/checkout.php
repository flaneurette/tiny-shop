<?php

	include("../resources/php/header.inc.php");
	include("../resources/php/class.Session.php");
	include("../class.Shop.php");
	
	$shop = new Shop();
	$session = new Session();
	
	$session->sessioncheck();
	
	if(isset($_SESSION['token'])) { 
		$token = $_SESSION['token'];
		} else {
		$token = $shop->getToken();
		$_SESSION['token'] = $token;
	}

	$host_path = $shop->getbase(true);

	/* Get the currency of site.json
	*  To change the default currency, edit site.json which has a numeric value that corresponds to the values inside currencies.json.
	*  DO NOT edit currencies.json, unless adding a new currency, as this file is used throughout TinyShop and might break functionality.
	*/
	
	// $sitecurrency = $shop->getsitecurrency();
	
	echo $shop->debug($_POST);
	
?>
<!DOCTYPE html>
<html>
	<head>
	<?php
	//echo $shop->getmeta();				
	?>
	</head>
	<body>
		<h1>Shopping Cart</h1>
		
		<div id="result"></div>
		<div id="ts-shop-cart-form">

	<?php 
		
		if(isset($_SESSION['cart']) && count($_SESSION['cart']) >= 1) {
		$c = count($_SESSION['cart']);
		
		if(($c > 0) && ($c < 9999) ) {
			
	?>
		<form name="ts_cart" method="post" action="<?=$host;?>/cart/checkout/" id="ts-shop-cart-form-data">
		
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
			
		$products = $shop->getproductlist();
			
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

				?>
				<li class="ts-shop-li">
					<div class="ts-shop-ul-li-item" width="60">&#128722;</div>
					<div class="ts-shop-ul-li-item"><?=$producttitle;?><!-- title --></div>
					<div class="ts-shop-ul-li-item"><?=$productdesc;?><!-- desc --></div>
					<div class="ts-shop-ul-li-item"><?=$sitecurrency;?> <?=$productprice;?><!-- price --></div>
					<div class="ts-shop-ul-li-item"><input type="number" id="<?=$qtyid;?>" size="1" min="1" max="9999" value="<?=$productqty;?>"></div>
					<div class="ts-shop-ul-li-item"><a href="#" onclick="tinyshop.updatecart('<?=$product;?>','<?=$qtyid;?>','<?=$token;?>','<?=$host_path;?>');">&#x21bb;</a></div>
					<div class="ts-shop-ul-li-item">Total: <?=$sitecurrency;?> <?=$productsum;?><!-- sum --></div>
					<div class="ts-shop-ul-li-item" id="ts-shop-delete"><a href="#" onclick="tinyshop.deletefromcart('<?=$product;?>','<?=$token;?>','<?=$host_path;?>');">&#x2716;</a>
					</div>
				</li>
			<?php
					}
					$j++;
					}
				}
			}
			echo "</ul>";
		?>
		<hr />
		<h1>Checkout</h1>
		<hr />
			<select name="payment_gateway" id="ts-form-cart-payment-gateway-select">
			<option value="">Select payment method...</option>
			<?php
				// dynamically generate payment gateways from site.json
				// $siteconf = $shop->load_json("../inventory/site.json");
				// $keys = 'site.payment.gateways';
				// echo $shop->gatewaylist($siteconf,$keys);
			?>
			</select>
			
		<div class="ts-shop-form-field">
		<hr />
		<input type="submit" name="submit" value="Checkout">
		</div>

		
		</form>
		
		<?php
				}
		} else {
		echo "<div id='ts-shop-cart-error'>Cart is empty.</div>";
	} 
	
	?>
		</div>
	</body>
</html>