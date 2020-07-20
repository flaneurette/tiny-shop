<?php

	error_reporting(E_ALL);
	ini_set('max_file_uploads', 30);
	set_time_limit(0); 
	session_start(); 

	include("../resources/php/class.ImageSanitize.php");

	include("../class.Shop.php");
	$shop  = new Shop();
?>
<!DOCTYPE html>
<html>
	<head>
	
	<style>
	.message {
		background-color:lightgreen;
		border-color:1px solid green;
	}
	
	</style>
	</head>
	<body>
	<h1>Administration</h1>
<hr />

<span style="color:#ff0000;font-size:12px;">This part of the page should be placed behind a password protected area. </span>

<h2>Upload CSV files, and convert to JSON</h2>

<form name="" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="upload_csv" value="1">
	<input type="file" name="csv_file[]" multiple>
	<input type="submit" name="submit" value="Upload & Convert">
</form>

<hr />
<small>N.B. your current PHP configuration allows only: <?=ini_get('max_file_uploads');?> simultaneous files to uploaded. To change it, edit PHP.ini max_file_uploads = number.</small>

<?php

	if(isset($_POST['upload_csv'])) {
		
		
		echo "<hr /><div class=\"message\">";
		$count = count($_FILES['csv_file']['name']);
		$j=1;
		for ($i = 0; $i < $count; $i++) {
				
			if($_FILES['csv_file']['error'][$i] == UPLOAD_ERR_OK  && is_uploaded_file($_FILES['csv_file']['tmp_name'][$i])) { 
			
				if($_FILES['csv_file']['type'][$i] != 'application/octet-stream') {
					echo "File is not a CSV.";
					exit;
				}
			
			
				$file = file_get_contents($_FILES['csv_file']['tmp_name'][$i]);  
				$showfile =  $shop->convert($file,'csv_to_json',$file);
				
				$f = str_replace('.csv','',$_FILES['csv_file']['name'][$i]);
				$shop->storedata('../inventory/'.$shop->sanitize($f,'table').'.json',$showfile,'json'); 
				$shop->storedata('../inventory/csv/'.$shop->sanitize($_FILES['csv_file']['name'][$i],'table'),$file,'csv'); 
				
				echo $j.":<em>Successfully upload ".$shop->sanitize($_FILES['csv_file']['name'][$i],'table')." CSV and converted to JSON.</em><br />";
				
				} else {
					
				echo $shop->sanitize($_FILES['csv_file']['error'][$i],'table');
			}
			$j++;
		}
		
		echo '</div>';
	}
			
?>	

<hr />

<?php

	if(isset($_POST['upload'])) {
		
		if($_POST['upload'] == 1) {
			
			if(isset($_POST['destination'])) {
				
				if($_POST['destination'] == 'category') {
					$destination = '../resources/images/category/';
					} elseif($_POST['destination'] == 'products') {
					$destination = '../resources/images/products/';
					} else {
					$destination = '../resources/images/';
				}
			}
			
			$parameters = array('image' => 'files','path' => $destination,'thumb' => false,'width' => false,'height' => false);
			// $checkImage = new \security\images\ImageSecurityCheck($parameters);
			$checkImage = new ImageSecurityCheck($parameters);
			$checkImage->clearmessages();
			$upload = $checkImage->fullScan();
			
			echo "<div class=\"message\">Image successfully uploaded.</div>";
		}		
	} 

?>

<h2>Upload images.</h2>

<p>Select an image to process...</p>

	<form name="" action="" method="post" enctype="multipart/form-data">
		Type of image: 
		<select name="destination">
			<option value="category">category</option>
			<option value="products">products</option>
		</select>
		<input type="file" name="files" /> 
		<input type="hidden" name="upload" value="1" /> 
		<input type="submit" name="submit" value="Upload Image" /> 
	</form>
	
	</body>
</html>