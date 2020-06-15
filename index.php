<?php

	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();
	
?>
<!DOCTYPE html>
<html>
	<head>
	<?php
	echo $shop->getmeta();				
	?>
	</head>
	<body>
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
	</body>
</html>
