<?php

	include("../resources/php/header.inc.php");
	include("../class.Shop.php");
	
	$shop  = new Shop();
	
	$token = $shop->getToken();
	$_SESSION['token'] = $token;

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
					<h1><?=$shop->cleanInput($row['article.title']);?></h1>
					</div>
					<div class="ts-shop-page-item-titles">
						<!-- <span class="ts-shop-page-item-author"></span> -->
						<span class="ts-shop-page-item-date"><?=$shop->cleanInput($row['article.published']);?></span>
					</div>
					<div class="ts-shop-page-item-textbox"><?=$shop->cleanInput($row['article.short.text']);?></div>
					
					</div>
					<div class="ts-shop-page-item-main-footer">
						<span class="ts-shop-page-item-rm"><a href="article/<?= (int)$shop->cleanInput($row['article.id']);?>/">read more &raquo;</a></span> 
						<span class="ts-shop-page-item-tags"><?=$shop->cleanInput($row['article.tags']);?></span>
					</div>
				</div>	
			</div>

		<?
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