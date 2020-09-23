<?php

	/**
		Upload CSV and convert it to JSON.
	**/
	
	error_reporting(E_ALL);
	session_start();
	
	include("class.Shop.php");
	$shop  = new Shop();
	$shoplist = $shop->decode();
	
	if(isset($_POST['upload'])) {
		if($_FILES['csv_file']['error'] == UPLOAD_ERR_OK  && is_uploaded_file($_FILES['csv_file']['tmp_name'])) { 
		
			$file = file_get_contents($_FILES['csv_file']['tmp_name']);  
			$showfile =  $shop->convert($file,'csv_to_json',$file);
			$shop->storeshop($showfile); 
			echo "<h3>Successfully upload CSV and converted to JSON.</h3>";
		}
	}
?>		

<hr>

<small>This part of the page should be placed behind a password protected area. For now, this is a demo.</small>

<h2>Upload CSV and convert to JSON</h2>

<form name="" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="upload" value="1">
	<input type="file" name="csv_file">
</form>