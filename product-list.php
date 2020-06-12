<html>
	<head>
	<link rel="stylesheet" type="text/css" href="resources/reset.css">
	<link rel="stylesheet" type="text/css" href="resources/style.css">
	</head>
		<body>

		<h1>Shop product list</h1>

		<div id="ts.product.list">

		<?php
		
			$shop  = new Shop();
			$product_list = $shop->decode();

			if($product_list !== null) {

				$shoplist 	= $product_list;
				$ts 		= array(); 
				$i 			= 0;
					
					foreach($product_list as $c) {	
						array_push($ts,$c);
						$shop->cleanInput($c['product.title']);
						$i++;
					}
					
					$i = count($ts)-1;
					
					if($i >= 0) { 
						while($i >= 0) {
							if($ts[$i]['stock'] < 1) {
								$status_color = 'ts.product.status.red'; // low stock
								} else {
								$status_color = 'ts.product.status.green';
							}
							echo "<div>";
							echo "<div class=".$status_color.">".$ts[$i]['product.status']."</div>";
							echo "<div>".$shop->CURRENCIES[3][0][0].' '.$ts[$i]['product.price']."</div>";
							echo "<div><a href=\"".$shop->seoUrl($ts[$i]['product.category']).'/'.$shop->seoUrl($ts[$i]['product.title']).'/'.$shop->cleanInput($ts[$i]['product.id'])."/\">".$shop->cleanInput($ts[$i]['product.title'])."</a> </div>";
							echo "<div>".$ts[$i]['product.description']."</div>";
							echo "<div>".$ts[$i]['product.category']."</div>";
							echo "<div><input type=\"button\" name=\"add_cart\" value=\"Add to cart\" /></div>";
							echo "</div>";
						$i--;
						}
					}
					
			}
					

			echo "<div id=\"ts.product\">";

			echo "</div>";
			
		?>

		</div>
		</body>
</html>