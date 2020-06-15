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
		<h1>Pages</h1>
		
			<div id="ts-shop-page">
			
			<?php
				$json = "../inventory/pages.json";
				echo $shop->getpagelist($json,'pages');
			?>
				
			</div>
	</body>
</html>