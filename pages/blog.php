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
		<h1>Blog</h1>
		
			<div id="ts-shop-blog">
			
			<?php
				$json = "../inventory/blog.json";
				echo $shop->getpagelist($json,'blog');
			?>
				
			</div>
	</body>
</html>