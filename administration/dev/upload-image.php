<?php

set_time_limit(0); 
session_start(); 

require("../resources/php/class.imageSanitize.php");

if(isset($_REQUEST['upload'])) {

	$parameters = array('image' => 'files','path' => "test/",'thumb' => false,'width' => false,'height' => false);
	$checkImage = new \security\images\ImageSecurityCheck($parameters);
	$checkImage->fullScan(); 
} 

?>

<h2>Secure image class</h2>

<p>Select an image to process...</p>

<form name="" action="" method="post" enctype="multipart/form-data"> 
	<input type="file" name="files" /> 
	<input type="hidden" name="upload" value="1" /> 
	<input type="submit" name="submit" value="Upload Image" /> 
</form>

<small>NB. Don't forget to create a /test/ folder to write files to.</small>
