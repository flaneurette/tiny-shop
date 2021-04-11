<?php

error_reporting(0);

	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();
	
	$token = $shop->getToken();
	$_SESSION['token'] = $token;
	
	if(isset($_REQUEST['cat'])) {
		$cat   = $shop->sanitize($_REQUEST['cat'],'cat');
		$catid = $shop->getcatId($cat,$subcat=false);
	}
	
	if(isset($_REQUEST['subcat'])) {
		$cat    = $shop->sanitize($_REQUEST['cat'],'cat');
		$subcat = $shop->sanitize($_REQUEST['subcat'],'cat');
		$catid  = $shop->getcatId($cat,$subcat=false); // TODO: highlight subcats
	}
	
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

<div id="cart-contents"><a href="<?=$host;?>cart/">View Cart</a></div>
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
			
			if(isset($cat)) {
				$products = $shop->getproducts('list',$cat,false,$_SESSION['token']);				
				echo $products;
			} elseif(isset($subcat)) {
				$products = $shop->getproducts('list',$subcat,false,$_SESSION['token']);				
				echo $products;
			} else { }	

			?>
			</div>
			
			</div>
			<div id="ts-paginate">
				<?php 
					// echo $shop->paginate(1);
				?>
			</div>
			<!-- caller: method, opts, uri. -->
</div>

<?php
include("footer.php");
?>
<script>


function categoryEvents() {
	tinyshop.toggle(<?=$catid;?>,'8');
}

tinyshop.tinyEvents('categories');

</script>
</body>
</html>