<?php

	/**
	display products in the shop.
	**/
	
	error_reporting(E_ALL);
	session_start();
	
	include("class.Shop.php");
	$shop  = new Shop();
	$shoplist = $shop->decode();
	
	if(isset($_POST['upload'])) {
		
		if($_FILES['csv_file']['error'] == UPLOAD_ERR_OK  && is_uploaded_file($_FILES['csv_file']['tmp_name'])) { 
		
			$file = file_get_contents($_FILES['csv_file']['tmp_name']);  
			$showfile =  $shop->convert($file,'csv_to_json',$file);
			$shop->storeshop($showfile); 
			echo "<h3>Successfully upload CSV and converted to JSON.</h3>";
		}
	}
?>
<html>
	<head>
	<!-- <link rel="stylesheet" type="text/css" href="style.css"> -->
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
							$shop->cleanInput($c['title']);
							$i++;
						}
					
					echo '<table border="0" cellpadding="3" cellspacing="5" width="100%">';
					echo '<tr><td width="90">Status</td><td>Product</td><td>Description</td><td>Category</td><td>Price</td></tr>';
					
					$i = count($iv)-1;
					
					if($i >= 0) { 
						while($i >= 0) {
							if($iv[$i]['stock'] < 1) {
								$status_color = 'status-red'; // low stock
								} else {
								$status_color = 'status-green';
							}
							echo "<tr><td width=\"90\">";
							echo "<div class=".$status_color.">".$iv[$i]['status']."</div></td>";
							echo "<td><a href=\"".$shop->seoUrl($iv[$i]['category']).'/'.$shop->seoUrl($iv[$i]['title']).'/'.$shop->cleanInput($iv[$i]['id'])."/\">".$shop->cleanInput($iv[$i]['title']).' </a> </td><td> '.$iv[$i]['description']."</td><td>".$iv[$i]['category']."</td><td>".$shop->CURRENCIES[3][0][0].' '.$iv[$i]['price']."</td></tr>";
						$i--;
						}
					}
					
					echo '</table>';

				} else {
					echo "<p class='book'><em>Shop is empty...</em></p>";
				}
			?>
			</div>
		
		<div id="output"></div>
		
		<hr>
		<small>This part of the page should be placed behind a password protected area. For now, this is a demo.</small>
		<h2>Upload CSV and convert to JSON</h2>
		<form name="" action"" method="post" enctype="multipart/form-data">
		<input type="hidden" name="upload" value="1">
			<input type="file" name="csv_file">
			<input type="submit" value="Upload CSV">
		</form>
		
	</body>
</html>
