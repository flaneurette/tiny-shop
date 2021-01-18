<?php

	include("resources/php/header.inc.php");
	include("resources/php/class.Session.php");
	include("class.Shop.php");
	
	$shop = new Shop();
	$session = new Session();
	
	$session->sessioncheck();
	
	if(!empty($_GET)) {	
		$shop->message('Checkout page cannot be accessed this way.');
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
		
	if(!isset($_POST['checkout-post'])) {	
		$shop->message('Checkout page could not be loaded from resource and cannot be accessed this way.');
		$shop->showmessage();
		exit;
	}
	
	/* Get the currency of site.json
	*  To change the default currency, edit site.json which has a numeric value that corresponds to the values inside currencies.json.
	*  DO NOT edit currencies.json, unless adding a new currency, as this file is used throughout TinyShop and might break functionality.
	*/
	
	$sitecurrency = $shop->getsitecurrency('inventory/site.json','inventory/currencies.json');
	
	// echo $shop->debug($_POST);
	
	if(isset($_POST['payment_gateway'])) {
		$payment_gateway = $shop->sanitize($_POST['payment_gateway'],'encode');
		} else {
		$payment_gateway = 'PayPal';
		$shop->message('Payment Gateway not selected, assuming and defaulting to PayPal');
		$shop->showmessage();
	}
	
	if(isset($_POST['shipping_country'])) {
		$shippingcountry = $shop->sanitize($_POST['shipping_country'],'encode');
		} else {
		$shop->message('Country not selected, cannot continue to checkout!');
		$shop->showmessage();
		exit;
	}
	
	$gateway = $shop->sanitize($payment_gateway,'alphanum');
	
	$siteconf = $shop->load_json("inventory/shipping.json");
	$countryprice = $shop->getcountryprice($siteconf,$shippingcountry);
	
	if($countryprice != false) {
		$country_price = (int)$countryprice;
		} else {
		$country_price = 10; // default shipping fee.
	}
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
include("header.php");
?>

<div id="wrapper">

		<div id="ts-shop-result-message" onclick="tinyshop.togglecartmsg('close');"></div>
		<div id="ts-shop-cart-form">
<h1>Checkout</h1>

	<?php 
		
		if(isset($_SESSION['cart']) && count($_SESSION['cart']) >= 1) {
		$c = count($_SESSION['cart']);
		
		if(($c > 0) && ($c < 9999) ) {
	?>
		<form name="ts_cart" method="post" action="/shop/payment/paypal/checkout.php" id="ts-shop-cart-form-data">
		<input type="hidden" name="token" value="<?=$token;?>">
		<input type="hidden" name="checkout-post-gateway" value="1">
		<hr />
		<div class="ts-shop-ul-set">
		<div class="ts-shop-ul">
				<li class="ts-shop-ul-li-item-icon" width="2%">&#128722;</li>
				<li class="ts-shop-ul-li-item-product" width="35%">Product Name</li>
				<li class="ts-shop-ul-li-item-description" width="35%">Description</li>
				<li class="ts-shop-ul-li-item-price" width="5%">Price</li>
				<li class="ts-shop-ul-li-item-qty" width="5%">Qty</li>
				<li class="ts-shop-ul-li-item-total" width="16%">Total</li>
		</div>
			
	<?php
			
		$products = $shop->getproductlist('inventory/shop.json');
		$productsum_total = 0;
		$productsum = 0;
		
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
						$productsum_total = ($productsum_total + $productsum);
						$qtyid = 'tscart-'.$j.$product;

				?>
				<div class="ts-shop-ul">
						<li class="ts-shop-ul-li-item-icon" width="2%">&#128722;</li>
						<li class="ts-shop-ul-li-item-product" width="35%"><?=$producttitle;?><!-- title --></li>
						<li class="ts-shop-ul-li-item-description" width="35%"><?=$productdesc;?><!-- desc --></li>
						<li class="ts-shop-ul-li-item-price" width="5%"><?=$sitecurrency;?> <?=$productprice;?><!-- price --></li>
						<li class="ts-shop-ul-li-item-qty" width="5%"><?=$productqty;?></li>
						<li class="ts-shop-ul-li-item-total" width="16%"><?=$sitecurrency;?> <?=$productsum;?><!-- sum --></li>
				</div>
			<?php
					}
					$j++;
					}
				}
			}
			
			?>
			</div>
			<br />
			<div class="ts-shop-ul-set">
			
			<div class="ts-shop-ul">
					<li class="ts-shop-ul-li-item" width="10%"></li>
					<li class="ts-shop-ul-li-item" width="10%">Country</li>
					<li class="ts-shop-ul-li-item" width="30%">Subtotal</li>
					<li class="ts-shop-ul-li-item" width="35%">Shipping &amp; handling</li>
					<li class="ts-shop-ul-li-item" width="15%">Total</li>
			</div>
			
			<li class="ts-shop-ul-li-item">
			</li>
			<li class="ts-shop-ul-li-item">
			<?=str_replace('shipping.','',$shippingcountry);?>
			</li>
			<li class="ts-shop-ul-li-item">	
			<!-- subtotal -->
			<?=$sitecurrency;?> <?=$productsum_total;?>
			</li>
			<li class="ts-shop-ul-li-item">
				<?=$sitecurrency;?> <?=$country_price;?>
			</li>		
			<li class="ts-shop-ul-li-item">
				<?=$sitecurrency;?> <?=($country_price + $productsum_total);?>	
			</li>
			</div>
			
		<div class="ts-shop-form-field">
		<input type="submit" name="submit" value="Pay with <?=$gateway;?>">
		</div>

		</form>
		
		<?php

			// Set session data for payment gateway page.
			$uniqueid 	= $shop->uniqueID();
			
			$_SESSION['cartid']   	= $shop->sanitize($uniqueid,'alphanum');
			$_SESSION['subtotal']   = (int) $productsum_total;
			$_SESSION['shipping']   = (int) $country_price;
			$_SESSION['totalprice'] = (int) ($country_price + $productsum_total);
			$_SESSION['shipping_country'] = $shop->sanitize(str_replace('shipping.','',$shippingcountry));
		}
		} else {
		echo "<div id='ts-shop-cart-error'>Cart is empty.</div>";
	} 
	
	?>
	</div>
</div>

<?php
include("footer.php");
?>
</body>
</html>