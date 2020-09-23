<?php
error_reporting(0);

function encode($string) {
	return htmlspecialchars($string,ENT_QUOTES,'UTF-8');
}

function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

//$handle = fopen('./log.log', "r");
if(filesize('./log.log') > 3000000) {
	//empty log
	@file_put_contents('./log.log', "");
	} else {
	$log = date("F j, Y, g:i a") . ' - '. $_SERVER['REMOTE_ADDR'].' - '.$_SERVER['HTTP_USER_AGENT'].' - '. $_SERVER['HTTP_REFERER'].' - '.$_SERVER['SCRIPT_NAME']. ' - '.$_SERVER['QUERY_STRING']. PHP_EOL;
	@file_put_contents('./log.log', encode($log), FILE_APPEND);
}
?>
<header>
<h1 id="logo"><span id="logo-left">Mystryl Art</span></h1>
<br><br>
<nav>
	<a href="index.php" target="_self">home</a> 
	<a href="nav2/" target="_self">nav 2</a>
	<a href="nav3/" target="_self">nav 3</a>
	<a href="nav4/" target="_self">nav 4</a> 
	<a href="nav5/" target="_self">nav 5</a> 
	<a href="nav6/" target="_self">nav 6</a> 
	<a href="nav7/" target="_self">nav 7</a>
</nav>
</header>