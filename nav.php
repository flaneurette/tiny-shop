<?php
		include("resources/php/header.inc.php");
		include("class.Shop.php");
		$shop  = new Shop();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=0.73">
	<?php
	echo $shop->getmeta();				
	?>
</head>

<body>

<ul id="ts-shop-left-navigation">

<?php
		// categories
		$categories = "inventory/categories.json";
		
		// subcategories
		$subcategories = "inventory/subcategories.json";	

		$cats = $shop->categories($categories,$subcategories,'left');
		echo $cats;
?>