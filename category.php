<?php

error_reporting(0);

	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();
	
	$token = $shop->getToken();
	$_SESSION['token'] = $token;
	
	if(isset($_GET['cat'])) {
		$cat   = $shop->sanitize($_GET['cat'],'cat');
		$catid = $shop->getcatId($cat,$subcat=false);
	}
	
	if(isset($_GET['subcat'])) {
		$cat    = $shop->sanitize($_GET['cat'],'cat');
		$subcat = $_REQUEST['subcat'];
		$catid  = $shop->getcatId($cat,false); // TODO: highlight subcats
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
			
			if(isset($_GET['page'])) {
				if($_GET['page'] != '' || $_GET['page'] != null) {
					$paginate = (int) $_GET['page'];
					} else {
					$paginate = false;
				}
			} else {
				$paginate = false;
			}
				
			if(isset($subcat)) {
				$products = $shop->getproducts('list',$subcat,false,false,$paginate,$_SESSION['token']);				
				echo $products[1];
			} elseif(isset($cat)) {
				$products = $shop->getproducts('list',$cat,false,false,$paginate,$_SESSION['token']);				
				echo $products[1];
			} else { }	

			$catitems = $products[0];
			?>
			</div>
			
			</div>
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