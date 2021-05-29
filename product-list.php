<?php

	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();
	$products = $shop->getproducts('list');
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
				echo $products[1];
				?>
			</div>
	</body>
</html>