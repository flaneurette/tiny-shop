<?php

	include("../resources/php/header.inc.php");
	include("../class.Shop.php");
	
	$shop  = new Shop();
	
	$token = $shop->getToken();
	$_SESSION['token'] = $token;
	
	if(isset($_GET['cat'])) {
		$cat = (int) $_GET['cat'];
	}

	if(isset($_GET['articleid'])) {
		$articleid = (int) $_GET['articleid'];
	}
	
	if(isset($_GET['page'])) {
		$page = (int) $_GET['page'];
		} else {
		$page = 1;
	}
	
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=0.73">
	
<?php
	echo $shop->getmeta("../inventory/site.json");				
?>

<link rel="stylesheet" type="text/css" href="../resources/pages.css">

</head>

<body>

<?php
include("../header.php");
?>

<div id="wrapper">

<h1>Articles</h1>
		
	<div id="ts-shop-page"></div>
<?php
	$json = "../inventory/articles.json";
	$pagelist = $shop->getpagelist($json,'articles');
				
	$num = count($pagelist);
	
	if($num >=1) {
		
	foreach($pagelist as $row)	{
	
			if(isset($articleid) >=1) {
			
				if($row['article.id'] == $articleid) {
				?>

					<div class="ts-shop-page-item">
						<div class="ts-shop-page-item-header">
						<?php
						if(strlen($row['article.image.header']) > 30) {
							echo '<img src="' . $shop->cleanInput($row['article.image.header']) . '" width="" height="" />';
						}
						?>
						</div>
						<div class="ts-shop-page-item-main">
							<div class="ts-shop-page-item-title">
							<h1><?php echo $shop->cleanInput($row['article.title']);?></h1>
							</div>
							<div class="ts-shop-page-item-titles">
								<!-- <span class="ts-shop-page-item-author"></span> -->
								<span class="ts-shop-page-item-date"><?php echo $shop->cleanInput($row['article.published']);?></span>
							</div>
							<div class="ts-shop-page-item-textbox"><?php echo $shop->cleanInput($row['article.long.text']);?></div>
							
							</div>
							<div class="ts-shop-page-item-main-footer">
								<span class="ts-shop-page-item-tags"><?php echo $shop->cleanInput($row['article.tags']);?></span>
							</div>
						</div>	
					</div>
			
					
				<?php
				}
			
			} else {
	?>
				
			<div class="ts-shop-page-item">
				<div class="ts-shop-page-item-header">
				<?php
				if(strlen($row['article.image.header']) > 30) {
					echo '<img src="' . $shop->cleanInput($row['article.image.header']) . '" width="" height="" />';
				}
				?>
				</div>
				<div class="ts-shop-page-item-main">
					<div class="ts-shop-page-item-title">
					<h1><?php echo $shop->cleanInput($row['article.title']);?></h1>
					</div>
					<div class="ts-shop-page-item-titles">
						<!-- <span class="ts-shop-page-item-author"></span> -->
						<span class="ts-shop-page-item-date"><?php echo $shop->cleanInput($row['article.published']);?></span>
					</div>
					<div class="ts-shop-page-item-textbox"><?php echo $shop->cleanInput($row['article.short.text']);?></div>
					
					</div>
					<div class="ts-shop-page-item-main-footer">
						<span class="ts-shop-page-item-rm"><a href="article/<?php echo (int)$shop->cleanInput($row['article.id']);?>/<?php echo $shop->cleanInput($row['article.title']);?>/<?php echo $page;?>/">read more &raquo;</a></span> 
						<span class="ts-shop-page-item-tags"><?php echo $shop->cleanInput($row['article.tags']);?></span>
					</div>
				</div>	
			</div>

		<?php
			}
		}
	} else {
		echo "No articles have been written yet.";
	}
	
	?>
</div>

<?php
include("../footer.php");
?>
</body>
</html>