<?php

	/**
		display products in the shop.
	**/

	ini_set('display_errors', 1); 
	ini_set('session.cookie_httponly', 1);
	ini_set('session.use_only_cookies', 1);
	ini_set('session.cookie_secure', 1);
	
	error_reporting(E_ALL);

	// Optional headers to consider.
	header("X-Frame-Options: DENY"); 
	header("X-XSS-Protection: 1; mode=block"); 
	header("Strict-Transport-Security: max-age=30");
	header("Referrer-Policy: same-origin");


	error_reporting(E_ALL);
	session_start();
	
	include("class.Shop.php");
	$shop  = new Shop();
	$shoplist = $shop->decode();
?>
<html>
	<head>
	<link rel="stylesheet" type="text/css" href="resources/reset.css">
	<link rel="stylesheet" type="text/css" href="resources/style.css">
	</head>
	<body>
		<h1>Shop product list</h1>
		<div id="shop">
		
			<?php 
			
				$shop  = new Shop();
				$product_list = $shop->decode();

				if($product_list !== null) {

					$shoplist = $product_list;
					$iv = array();
					$i = 0;
					
						foreach($product_list as $c) {	
							array_push($iv,$c);
							$shop->cleanInput($c['product.title']);
							$i++;
						}
					
					echo '<table border="0" cellpadding="3" cellspacing="5" width="100%">';
					echo '<tr><th>Status</th><th>Price</th><th>Product</th><th>Description</th><th>Category</th><th>Buy</th></tr>';
					
					$i = count($iv)-1;
					
					if($i >= 0) { 
						while($i >= 0) {
							if($iv[$i]['stock'] < 1) {
								$status_color = 'status-red'; // low stock
								} else {
								$status_color = 'status-green';
							}
							echo "<tr><td width=\"90\">";
							echo "<div class=".$status_color.">".$iv[$i]['product.status']."</div></td>";
							echo "<td>".$shop->CURRENCIES[3][0][0].' '.$iv[$i]['product.price']."</td>";
							echo "<td><a href=\"".$shop->seoUrl($iv[$i]['product.category']).'/'.$shop->seoUrl($iv[$i]['product.title']).'/'.$shop->cleanInput($iv[$i]['product.id'])."/\">".$shop->cleanInput($iv[$i]['product.title'])."</a> </td>";
							echo "<td>".$iv[$i]['product.description']."</td>";
							echo "<td>".$iv[$i]['product.category']."</td>";
							echo "<td><input type='button' name='add_cart' value='Add to cart' /></td>";
							echo "</tr>";
						$i--;
						}
					}
					
					echo '</table>';

				} else {
					echo "<p class='result'><em>Shop is empty...</em></p>";
				}
			?>
			</div>
		
		<div id="output"></div>
		
	</body>
</html>
