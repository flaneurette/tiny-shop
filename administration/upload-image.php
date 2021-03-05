<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

	set_time_limit(0); 
	session_start(); 

	include("../resources/php/class.ImageSanitize.php");

	if(isset($_POST['upload'])) {
		
		 
		 
		 
		if($_POST['upload'] == 1) {
			
			$parameters = array('image' => 'files','path' => "test/",'thumb' => false,'width' => false,'height' => false);
			// $checkImage = new \security\images\ImageSecurityCheck($parameters);
			
			$checkImage = new ImageSecurityCheck($parameters);
			$checkImage->clearmessages();
			$checkImage->fullScan();
			
		}		
	} 

?>

<h2>Secure image class</h2>

<p>Select an image to process...</p>

<form name="" action="" method="post" enctype="multipart/form-data"> 
	<input type="file" name="files" /> 
	<input type="hidden" name="upload" value="1" /> 
	<input type="submit" name="submit" value="Upload Image" /> 
</form>

