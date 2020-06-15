<?php
	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();
	$products = $shop->getproducts('list',$category='index');
?>
<!DOCTYPE html>
<html>
	<head>
	<link rel="stylesheet" type="text/css" href="resources/reset.css">
	<link rel="stylesheet" type="text/css" href="resources/style.css">
	</head>
	<body>
		<h1>Shop product list</h1>
			<div id="shop">
				<?php 
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
