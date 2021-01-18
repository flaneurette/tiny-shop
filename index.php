<?php

	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();
	
	$token = $shop->getToken();
	$_SESSION['token'] = $token;

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

<div id="cart-contents"><a href="/shop/cart/">View Cart</a></div>
<div id="wrapper">
<h2>Store</h2>
	<div id="ts-shop-result-message" onclick="tinyshop.togglecartmsg('close');"></div>
		<!-- <h1>Shop product list</h1> -->
			<div id="shop">
			
			<div id="ts-shop-nav-left">
			<?php

					// categories
					$categories = "inventory/categories.json";		
					// subcategories
					$subcategories = "inventory/subcategories.json";	
					
					$selected = [];
					
					if(isset($_REQUEST['cat'])) {
						array_push($selected,$shop->sanitize($_REQUEST['cat'],'alphanum'));
					}
					
					if(isset($_REQUEST['subcat'])) {
						array_push($selected,$shop->sanitize($_REQUEST['subcat'],'alphanum'));
					}
					
					$cats = $shop->categories($categories,$subcategories,$selected,'left');
					
					echo $cats;
			?>
			</div>
			
			<div id="ts-shop-nav">
			<?php
				$products = $shop->getproducts('list',$category='index',false,$_SESSION['token']);				
				echo $products;
			?>
			</div>
			
			</div>
			<div id="ts-paginate">
				<?php 
					echo $shop->paginate(1);
				?>
			</div>
			<!-- caller: method, opts, uri. -->
</div>

<?php
include("footer.php");
?>
</body>
</html>