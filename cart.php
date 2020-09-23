<?php

	include("resources/php/header.inc.php");
	include("resources/php/class.Session.php");
	include("class.Shop.php");
	
	$shop = new Shop();
	$session = new Session();
	
	$session->sessioncheck();
	
	if(isset($_SESSION['token'])) { 
		$token = $_SESSION['token'];
		} else {
		$token = $shop->getToken();
		$_SESSION['token'] = $token;
	}
		
	/* Get the currency of site.json
	 * To change the default currency, edit site.json which has a numeric value that corresponds to the values inside currencies.json.
	 * DO NOT edit currencies.json, unless adding a new currency, as this file is used throughout TinyShop and might break functionality.
	*/
	
	$sitecurrency = $shop->getsitecurrency('inventory/site.json','inventory/currencies.json');
?>
<!DOCTYPE html>
<html>
	<head>
	<?php
	echo $shop->getmeta();				
	?>
	</head>
<body>

<?php
include("../header.php");
?>
<div id="result"></div>
<div id="bio-wrapper">

<div id="ts-shop-cart-form">
<h1>Shopping Cart</h1>
	<?php 
		
		if(isset($_SESSION['cart']) && count($_SESSION['cart']) >= 1) {
		$c = count($_SESSION['cart']);
		
		
		if(($c > 0) && ($c < 9999) ) {
			
	?>
		<form name="ts_cart" method="post" action="/shop/cart/checkout/" id="ts-shop-cart-form-data">
		<input type="hidden" name="token" value="<?=$token;?>">
		<input type="hidden" name="checkout-post" value="1">
		<hr />
		
		<div class="ts-shop-ul-set">
		<div class="ts-shop-ul">
			<li class="ts-shop-ul-li-item-product">Product Name</li>
			<li class="ts-shop-ul-li-item-description">Description</li>
			<li class="ts-shop-ul-li-item">Price</li>
			<li class="ts-shop-ul-li-item-qty">Qty</li>
			<li class="ts-shop-ul-li-item-update"></li>
			<li class="ts-shop-ul-li-item-total">Total</li>			
			<li class="ts-shop-ul-li-item-delete"></li>
		</div>

	<?php
			
		$products = $shop->getproductlist('inventory/shop.json');
			
		//var_dump($products);
		
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
			
			<div class="ts-shop-ul">
					<li class="ts-shop-ul-li-item-product"><?=$producttitle;?><!-- title --></li>
					<li class="ts-shop-ul-li-item-description"><?=$productdesc;?><!-- desc --></li>
					<li class="ts-shop-ul-li-item-price"><?=$sitecurrency;?> <?=$productprice;?><!-- price --></li>
					<li class="ts-shop-ul-li-item-qty"><input type="number" name="qty" id="<?=$qtyid;?>" size="1" min="1" max="9999" value="<?=$productqty;?>"></li>
					<li class="ts-shop-ul-li-item-update"><a href="#" onclick="tinyshop.updatecart('<?=$product;?>','<?=$qtyid;?>','<?=$token;?>');">&#x21bb;</a></li>
					<li class="ts-shop-ul-li-item-total"><?=$sitecurrency;?> <?=$productsum;?><!-- sum --></li>
					<li class="ts-shop-ul-li-item-delete" id="ts-shop-delete"><a href="#" onclick="tinyshop.deletefromcart('<?=$product;?>','<?=$token;?>');">&#x2716;</a>
					</li>
			</div>
			
			<?php
					}
					$j++;
					}
				}
			}
		?>
		
		</div>
		
		
		<hr />
		<h1>Checkout</h1>
		<hr />
		
			<select name="shipping_country" id="ts-form-cart-payment-gateway-select">
			
			<option value="">Select shipping country...</option>
			<?php
				// dynamically generate payment gateways from site.json
				$siteconf = $shop->load_json("inventory/shipping.json");
				$keys = false;
				echo $shop->shippinglist($siteconf,$keys);
			?>
			</select>
			<br />
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
</div>

<?php
include("../footer.php");
?>
</body>
</html>