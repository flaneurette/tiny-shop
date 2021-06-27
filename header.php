<?php

//error_reporting(0);

if(isset($shop)) {
	$host = $shop->host();
	} else {
	require("class.Shop.php");
	$shop  = new Shop();
	$host = $shop->host();
}

$site = $shop->load_json("inventory/site.json");
$title = $shop->cleanInput($site[0]['site.title']);
	
if(!$title) {
	$title = 'Webshop Name';
} 

?>
<header>
<h1 id="logo"><span id="logo-left"><?php echo $title;?></span></h1>
<br><br>
<?php
echo $shop->navigation($host);
?>
</header>