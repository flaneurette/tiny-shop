<?php

	include("../resources/php/header.inc.php");
	include("../class.Shop.php");
	
	$shop  = new Shop();
?>

<!DOCTYPE html>
<html>
	<head>
	<?php
	echo $shop->getmeta("../inventory/site.json");				
	?>
	</head>
	<body>
		<h1>Articles</h1>
		
			<div id="ts-shop-articles">
			
			<?php
				$json = "../inventory/articles.json";
				echo $shop->getpagelist($json,'articles');
			?>
				
			</div>
	</body>
</html>