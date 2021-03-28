<?php

error_reporting(0);

	include("resources/php/header.inc.php");
	include("class.Shop.php");
	
	$shop  = new Shop();
	
	$token = $shop->getToken();
	$_SESSION['token'] = $token;
	
	if(isset($_REQUEST['cat'])) {
		$cat 		= $shop->sanitize($_REQUEST['cat'],'cat');
		$product 	= $shop->sanitize($_REQUEST['product'],'cat');
		$productid	= $shop->sanitize($_REQUEST['productid'],'num');
		$page 		= $shop->sanitize($_REQUEST['page'],'num');	
		
		if(isset($_REQUEST['subcat'])) {
			echo $_REQUEST['subcat'];
			$scat 		= $shop->sanitize($_REQUEST['subcat'],'cat');
			$subcat  	= $shop->getcatId($cat,$scat);
			echo $subcat;
		}
		
	}
	
	// get host
	if(isset($shop)) {
		$hostaddr = $shop->getbase();
		} else {
		echo "Could not load Shop.class.php";
		exit;
	}
			
	// var_dump($_REQUEST);
	
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
			
			if(isset($_REQUEST['productid'])) {
				$id = (int) $_REQUEST['productid'];
			}
			
			$product_list = $shop->decode();
			
			$base_url = $shop->getbase();

			if($product_list !== null) {

				$shoplist = $product_list;
				
				$iv = array();
				
				$i = 0;
				
					foreach($product_list as $c) {	
					
						array_push($iv,$c);
						
						if($iv[$i]['product.id'] == $id) {

							if($iv[$i]['product.status'] == '1') {

								  $product_id 			= $shop->cleanInput($iv[$i]["product.id"]);
								  $product_title 		= $shop->cleanInput($iv[$i]["product.title"]);
								  $product_description 	= $shop->cleanInput($iv[$i]["product.description"]);
								  $product_category 	= $shop->cleanInput($iv[$i]["product.category"]);
								  $product_stock 		= $shop->cleanInput($iv[$i]["product.stock"]);
								  $product_price 		= $shop->cleanInput($iv[$i]["product.price"]);
								  $product_image 		= $shop->cleanInput($iv[$i]["product.image"]);

							$string_button = "<div><input type='number' name='qty' size='1' value='1' min='1' max='9999' id='ts-group-cart-qty-".$i.'-'.$product_id."'><input type='button' onclick='tinyshop.addtocart(\"".$product_id."\",\"ts-group-cart-qty-".$i.'-'.$product_id."\",\"".$token."\",\"".$hostaddr."\");' class='ts-list-cart-button' name='add_cart' value='Add to Cart' /></div>";
							
							echo '<div class="product-box">
									<div class="product-title"><h2>'.$product_title.'</h2></div>
										<div class="product-subbox">
											<div class="product-image">
												<img src="'.$base_url.$product_image.'" />
											</div>
											<div class="product-details">
												<div class="product-description">'.$shop->formatter($product_description,'product-description').'</div>
												<div class="product-price">'.$shop->getsitecurrency('inventory/site.json','inventory/currencies.json').' '.$product_price.'</div>
												<a href="#"><div class="product-buynow">'.$string_button.'</div></a>
											</div>
										</div>
									<div class="product-images"></div>
							</div>';
							
							} else {
								echo "Product cannot be shown.";
							}
	 
							break;
						}
						
						$i++;
					}

			} else {
				echo "<p class='book'><em>Shop database is empty... edit the JSON database and add a product.</em></p>";
			}
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