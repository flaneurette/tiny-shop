<?php

	/**
		Upload CSV and convert it to JSON.
	**/
	
	ini_set('max_file_uploads', 30);
	error_reporting(E_ALL);
	session_start();

	include("../class.Shop.php");
	$shop  = new Shop();
?>
<hr />

<small>This part of the page should be placed behind a password protected area. </small>

<h2>Upload CSV files, and convert to JSON</h2>

<form name="" action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="upload" value="1">
	<input type="file" name="csv_file[]" multiple>
	<input type="submit" name="submit" value="Upload & Convert">
</form>

<hr />
<small>N.B. your current PHP configuration allows only: <?=ini_get('max_file_uploads');?> simultaneous files to uploaded. To change it, edit PHP.ini max_file_uploads = number.</small>
<hr />
<?php

	if(isset($_POST['upload'])) {
		
		$count = count($_FILES['csv_file']['name']);
		$j=1;
		for ($i = 0; $i < $count; $i++) {
				
			if($_FILES['csv_file']['error'][$i] == UPLOAD_ERR_OK  && is_uploaded_file($_FILES['csv_file']['tmp_name'][$i])) { 
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
		
	}
			
?>		

