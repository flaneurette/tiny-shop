<?php

error_reporting(0);

if(isset($shop)) {
	$host = $shop->gethost("../inventory/site.json");
	} else {
	require("class.Shop.php");
	$shop  = new Shop();
	$host = $shop->gethost("../inventory/site.json");
}

?>
<header>
<h1 id="logo"><span id="logo-left">Webshop Name</span></h1>
<br><br>
<nav>
	<a href="index.php" target="_self">home</a> 
	<a href="<?=$host;?>/nav2/" target="_self">nav 2</a>
	<a href="<?=$host;?>/nav3/" target="_self">nav 3</a>
	<a href="<?=$host;?>/nav4/" target="_self">nav 4</a> 
	<a href="<?=$host;?>/nav5/" target="_self">nav 5</a> 
	<a href="<?=$host;?>/nav6/" target="_self">nav 6</a> 
	<a href="<?=$host;?>/nav7/" target="_self">nav 7</a>
</nav>
</header>
