<?php

	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();
	
	$token = $shop->getToken();
	$_SESSION['token'] = $token;
	
	$cat   = $shop->sanitize('index','cat');
	$catid = $shop->getcatId($cat,$subcat=false);
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

<?php
include("header.php");
?>

<div id="cart-contents"><a href="<?php echo $host;?>cart/">View Cart</a></div>
<div id="wrapper">
<h2>Store</h2>
	<div id="ts-shop-result-message" onclick="tinyshop.togglecartmsg('close');" onmouseover="tinyshop.togglecartmsg('close');"></div>
		<!-- <h1>Shop product list</h1> -->
			<div id="shop">
			
			<div id="ts-shop-nav-left">
			<?php

					// categories
					$categories = "inventory/categories.json";
					
					// subcategories
					$subcategories = "inventory/subcategories.json";
					
					$selected = [];
					
					if(isset($cat) != false) {
						array_push($selected,$cat);
					} 
					
					if(isset($subcat) != false) {
						array_push($selected,$subcat);
					} 
					
					$cats = $shop->categories($categories,$subcategories,$selected,'left');
					
					echo $cats;
			?>
			</div>
			
			<div id="ts-shop-nav">
			<?php

			$products = $shop->getproducts('list','index',false,false,false,$_SESSION['token']);
		
			echo $products[1];	

			?>
			</div>
			
			</div>
</div>

<?php
include("footer.php");
?>
<script>


function categoryEvents() {
	tinyshop.toggle(<?php echo $catid;?>,'8');
}

tinyshop.tinyEvents('categories');

</script>
</body>
</html>