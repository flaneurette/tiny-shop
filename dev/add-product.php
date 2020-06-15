<?php

	include("class.Shop.php");
	$shop  = new Shop();
	$shoplist = $shop->decode();
	
	if(isset($_POST)) { 
		  isset($_POST['id']) 			? $id = $cleanInput($_POST['id']) : $id = false;  
		  isset($_POST['product']) 		? $product = $cleanInput($_POST['product']) : $product = false;  
		  isset($_POST['title']) 		? $title = $cleanInput($_POST['title']) : $title = false;  
		  isset($_POST['description']) 	? $description = $cleanInput($_POST['description']) : $description = false;  
		  isset($_POST['catno']) 		? $catno = $cleanInput($_POST['catno']) : $catno = false;  
		  isset($_POST['category']) 	? $category = $cleanInput($_POST['category']) : $category = false;  
		  isset($_POST['image']) 		? $image = $cleanInput($_POST['image']) : $image = false;  
		  isset($_POST['format']) 		? $format = $cleanInput($_POST['format']) : $format = false;  
		  isset($_POST['quantity']) 	? $quantity = $cleanInput($_POST['quantity']) : $quantity = false;  
		  isset($_POST['status']) 		? $status = $cleanInput($_POST['status']) : $status = false;  
		  isset($_POST['price']) 		? $price = $cleanInput($_POST['price']) : $price = false;  
		  isset($_POST['listed']) 		? $listed = $cleanInput($_POST['listed']) : $listed = false;  
		  isset($_POST['stock']) 		? $stock = $cleanInput($_POST['stock']) : $stock = false;  
		  isset($_POST['EAN']) 			? $EAN = $cleanInput($_POST['EAN']) : $EAN = false;  
		  isset($_POST['weight']) 		? $weight = $cleanInput($_POST['weight']) : $weight = false;  
		  isset($_POST['format']) 		? $format = $cleanInput($_POST['format']) : $format = false;  
		  isset($_POST['datetime']) 	? $datetime = $cleanInput($_POST['datetime']) : $datetime = false;  
		  isset($_POST['condition'])	? $condition = $cleanInput($_POST['condition']) : $condition = false;  
		  isset($_POST['weight']) 		? $weight = $cleanInput($_POST['weight']) : $weight = false;  
		  isset($_POST['shipping']) 	? $shipping = $cleanInput($_POST['shipping']) : $shipping = false;  
		  isset($_POST['status']) 		? $status = $cleanInput($_POST['status']) : $status = false;
	}	  

?>

<html>

	<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<h1>Add Product to Shop.</h1>

			<h1>Add Product.</h1>
			<form action="" method="post">
				<fieldset>
				<label>Product title:</label><br />
				<input type = "text" name="title" size="80" value="<?= ($formfill) ? $shop->cleanInput($_POST['title']) : '';  ?>" />
				<label>ISBN nummer:</label><br />
				<input type = "text" name="isbn" size="80" value="<?= ($formfill) ? $shop->cleanInput($_POST['isbn']) : '';  ?>"/>
				<label>Weight (gram):</label><br />
				<input type = "text" name="weight" size="80" value="<?= ($formfill) ? $shop->cleanInput($_POST['weight']) : '';  ?>" />
				<label>Description:</label><br />
				<textarea rows="10" cols="40" name="description"><?= ($formfill) ? $shop->cleanInput($_POST['description']) : ''; ?></textarea><br />
				
				
				<input type= "hidden" value="1" name="addProduct" /><br />
				<input type= "submit" value="Add." />
				</fieldset>
			</form>
		<?
			}
		?>
		<div id="output"></div>
	</body>

</html>


