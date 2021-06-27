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
	.alertmessage {
		color:#ff0000;
		font-size:12px;
	}
	</style>
	</head>
	<body>
	<h1>Administration</h1>
<hr />

<span style="alertmessage">This part of the page should be placed behind a password protected area. </span>

<h2>Upload CSV files, and convert to JSON</h2>

<form name="" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="upload_csv" value="1">
	<input type="file" name="csv_file[]" multiple>
	<input type="submit" name="submit" value="Upload & Convert">
</form>

<h2>Upload JSON files, and convert to CSV</h2>

<form name="" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="upload_json" value="1">
	<input type="file" name="json_file[]" multiple>
	<input type="submit" name="submit" value="Upload & Convert">
</form>

<hr />
<small>N.B. your current PHP configuration allows only: <?php echo ini_get('max_file_uploads');?> simultaneous files to uploaded. To change it, edit PHP.ini max_file_uploads = number.</small>

<?php


	if(isset($_POST['upload_json'])) {
		
		echo "<hr /><div class=\"message\">";
		
		$count = count($_FILES['json_file']['name']);
		
		$j=1;
		
		for ($i = 0; $i < $count; $i++) {
				
			if($_FILES['json_file']['error'][$i] == UPLOAD_ERR_OK  && is_uploaded_file($_FILES['json_file']['tmp_name'][$i])) { 
			
				if($_FILES['json_file']['type'][$i] != 'application/json') {
					echo "File is not a JSON file.";
					exit;
				}
			
					$f = str_replace('.json','.csv',$_FILES['json_file']['name'][$i]);
					$file = file_get_contents($_FILES['json_file']['tmp_name'][$i]);  
					$showfile = $shop->convert($file,'json_to_csv',$f);

					$shop->storedata('../inventory/csv/'.$shop->sanitize($f,'dir'),$showfile,'csv'); 
					
					echo $j .":<em>Successfully upload ".$shop->sanitize($_FILES['json_file']['name'][$i],'table')." JSON and converted to CSV.</em><br />";
				
				} else {
					
				echo $shop->sanitize($_FILES['json_file']['error'][$i],'table');
			}
			$j++;
		}
		
		echo '</div>';
	
	}


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
				
				if($_POST['destination'] != '') {

						$destination  = '../resources/images/';
						$catfolder = $shop->sanitize($_POST['destination'],'dir');
						
						if(strstr($catfolder,'../') || strstr($catfolder,'./'))  {
							echo "<div class=\"alertmessage\">Directory travesal is not allowed.</div>".PHP_EOL;
							exit;
						} else {
							
							$destination .= $shop->sanitize($_POST['destination'],'dir');
							
							echo $destination;
							
							if (!is_dir($destination)) {
								mkdir($destination, 0777, true);
								echo "<div class=\"message\">Directory did not exist, TinyShop created the new directory.</div>".PHP_EOL;
							}
						}
				}
			}
			
			$count = count($_FILES['upload']['name']);
				
			$j = 1;
				
				for ($i = 0; $i < $count; $i++) {

					$parameters = array('image' => 'files','path' => $destination,'thumb' => false,'width' => false,'height' => false);
					// $checkImage = new \security\images\ImageSecurityCheck($parameters);
					
					$checkImage = new ImageSecurityCheck($parameters);
					$checkImage->clearmessages();
					$upload = $checkImage->fullScan();
				}
				
			echo "<div class=\"message\">Image successfully uploaded.</div>";
		}		
	} 

?>

<h2>Upload images.</h2>

<p>Select an image to process...</p>

	<form name="" action="" method="post" enctype="multipart/form-data">
	
		Place in category: 
		<select name="destination">
			<option value="">Select category...</option>
			<?php
			$category	 = $shop->load_json("../inventory/categories.json");
			$subcategory = $shop->load_json("../inventory/subcategories.json");
			echo $shop->categorylist('all',$category, $subcategory);
			?>
		</select>

		<input type="file" name="files[]" /> 
		<input type="hidden" name="upload" value="1" /> 
		<input type="submit" name="submit" value="Upload Image" /> 
		<hr />
		<small>N.B. If there are no categories, please create categories through uploading the categories.csv and subcategories.csv first, as this list is dynamically created from these files.</small>
		<hr />
	</form>
	
	</body>
</html>