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

	<h1>Blog</h1>
		
	<div id="ts-shop-page"></div>
<?php

	$json = "../inventory/blog.json";
	$pagelist = $shop->getpagelist($json,'blog');
				
	$num = count($pagelist);
	
	if($num >=1) {
		
	foreach($pagelist as $row)	{
		
		if(isset($blogid) >=1) {
			
			if($row['blog.id'] == $blogid) {
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
							<!-- <span class="ts-shop-page-item-author"></span> -->
							<span class="ts-shop-page-item-date"><?php echo $shop->cleanInput($row['blog.published']);?></span>
						</div>
						<div class="ts-shop-page-item-textbox"><?php echo $shop->cleanInput($row['blog.long.text']);?></div>
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
						<!-- <span class="ts-shop-page-item-author"></span> -->
						<span class="ts-shop-page-item-date"><?php echo $shop->cleanInput($row['blog.published']);?></span>
					</div>
					<div class="ts-shop-page-item-textbox"><?php echo $shop->cleanInput($row['blog.short.text']);?></div>
					</div>
					<div class="ts-shop-page-item-main-footer">
						<span class="ts-shop-page-item-rm"><a href="<?php echo (int)$shop->cleanInput($row['blog.id']);?>/<?php echo $shop->cleanInput($row['blog.title']);?>/<?php echo $page;?>/">read more &raquo;</a></span> 
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