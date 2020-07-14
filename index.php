<?php

	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();

	$token = $shop->getToken();

	$_SESSION['token'] = $token;

?>
<!DOCTYPE html>
<html>
	<head>
	<?php
	echo $shop->getmeta();				
	?>
	</head>
	<body>
	<div id="cart-contents"><a href="/shop/cart/">View Cart</a>
	<div id="result"></div>
	</div>
	
		<h1>Shop product list</h1>
			<div id="shop">
			
			<?php
				$products = $shop->getproducts('list',$category='index');				
				echo $products;
			?>
			</div>
			<div id="ts.paginate">
				<center>
				<?php 
					echo $shop->paginate(1);
				?>
				</center>
			</div>
			<!-- caller: method, opts, uri. -->
	</body>
</html>
