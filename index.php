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
	<div id="cart-contents"><a href="cart.php">View cart</a></div>
	<div id="result"></div>
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
			<button onclick="tinyshop.caller('GET','shipping',['verzendmethode',100,'Afghanistan','result']);">test</button>
	</body>
</html>
