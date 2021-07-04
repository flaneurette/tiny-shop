<?php

	include("../resources/php/header.inc.php");
	include("../class.Shop.php");
	
	$shop  = new Shop();
	
	$token = $shop->getToken();
	$_SESSION['token'] = $token;
	
	if(isset($_GET['cat'])) {
		$cat = (int) $_GET['cat'];
	}

	if(isset($_GET['blogid'])) {
		$blogid = (int) $_GET['blogid'];
	}
	
	if(isset($_GET['page'])) {
		$page = (int) $_GET['page'];
		} else {
		$page = 1;
	}
	
	$baseurl = $shop->getbase();
	
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=0.73">
	
<?php
	echo $shop->getmeta("../inventory/site.json");				
?>

<link rel="stylesheet" type="text/css" href="<?php echo $baseurl;?>resources/pages.css">

</head>

<body>

<?php
include("../header.php");
?>

<div id="wrapper">

	<h1>Blog</h1>
		
	<div id="ts-shop-page"></div>
<?php

	$json = "../inventory/blog.json";
	$pagelist = $shop->getpagelist($json,'blog');
				
	$num = count($pagelist);
	
	if($num >=1) {
		
	foreach($pagelist as $row)	{
		
		if(isset($blogid) >=1) {
			
			if($row['blog.id'] == $blogid && $row['blog.status'] == 1) {
			?>
				<div class="ts-shop-page-item">
					<div class="ts-shop-page-item-header-focus">
					<?php
					if(strlen($row['blog.image.header']) > 30) {
						echo '<img src="'.$shop->cleanInput($row['blog.image.header']).'" />';
					}
					?>
					</div>
					<div class="ts-shop-page-item-main">
						<div class="ts-shop-page-item-title">
							<h1><?php echo $shop->cleanInput($row['blog.title']);?></h1>
						</div>
						<div class="ts-shop-page-item-titles">
						
						<?php 
						
						if($row['blog.handle'] != '') {
							echo '<span class="ts-shop-page-item-author">By '.$shop->cleanInput($row['blog.handle']).'</span>';
							} elseif($row['blog.author'] != '') { 
							echo '<span class="ts-shop-page-item-author">By '.$shop->cleanInput($row['blog.author']).'</span>';
						} else {}
						
						?>
							<span class="ts-shop-page-item-date"><?php echo $shop->cleanInput($row['blog.published']);?></span>
						</div>
						<div class="ts-shop-page-item-textbox"><?php echo $shop->cleanPageOutput($row['blog.long.text']);?></div>
						</div>
						<div class="ts-shop-page-item-main-footer">
							<span class="ts-shop-page-item-tags"><?php echo $shop->cleanInput($row['blog.tags']);?></span>
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
				if(strlen($row['blog.image.header']) > 30) {
					echo '<img src="'.$shop->cleanInput($row['blog.image.header']).'" width="" height="" />';
				}
				?>
				</div>
				<div class="ts-shop-page-item-main">
					<div class="ts-shop-page-item-title">
						<h1><?php echo $shop->cleanInput($row['blog.title']);?></h1>
					</div>
					<div class="ts-shop-page-item-titles">
						
						<?php 
						
						if($row['blog.handle'] != '') {
							echo '<span class="ts-shop-page-item-author">By '.$shop->cleanInput($row['blog.handle']).'</span>';
							} elseif($row['blog.author'] != '') { 
							echo '<span class="ts-shop-page-item-author">By '.$shop->cleanInput($row['blog.author']).'</span>';
						} else {}
						
						?>
		
						<span class="ts-shop-page-item-date"><?php echo $shop->cleanInput($row['blog.published']);?></span>
					</div>
					<div class="ts-shop-page-item-textbox"><?php echo $shop->cleanInput($row['blog.short.text']);?></div>
					</div>
					<div class="ts-shop-page-item-main-footer">
						<span class="ts-shop-page-item-rm"><a href="<?php echo (int)$shop->cleanInput($row['blog.id']);?>/<?php echo $shop->seoUrl($shop->cleanInput($row['blog.title']));?>/">read more &raquo;</a></span> 
						<span class="ts-shop-page-item-tags"><?php echo $shop->cleanInput($row['blog.tags']);?></span>
					</div>
				</div>	
			</div>

		<?php
			}
		}
	} else {
		echo "No blogs have been written yet.";
	}
	
	?>
</div>

<?php
include("../footer.php");
?>
</body>
</html>