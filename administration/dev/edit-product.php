<?php

	error_reporting(E_ALL);

	session_start();

	include("class.Shop.php");

	$shop  = new Shop();
	$shoplist = $shop->decode();

	if(isset($_POST['addProduct']) == '1') {
		$check = $shop->checkForm();
		if($check !== false) {
			$shop->addProduct(); 
			} else {
			$shop->showmessage();
		}
	} 

	if(isset($_POST['editProduct'])) {
		$check = $shop->checkForm();
		if($check !== false) {
			$shop->editProduct($_POST['editProduct']); 
			} else {
			$shop->showmessage();
		}
	} 

	if(isset($_GET['remove'])) {
		$shop->deleteProduct($_GET['remove']); 
	} 
?>
<html>

	<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<h1>Edit Product in Shop.</h1>

		<div id="ProductLibrary">
			<?php 
			
				$shop  = new Shop();
				$lijst = $shop->decode();

				if($lijst !== null) {

					$shoplist = usort($lijst, $shop->sortISBN('isbn'));
					$Products = array();
					$heavyProducts = array();
					$weightplank = 0;
					
					$i = 0;
						foreach($lijst as $c) {	
							if($shop->tooHeavy($c['weight']) == true) {
								array_push($heavyProducts,$c);
								} else {
								array_push($Products,$c);
							}
							$shop->cleanInput($c['title']);
							$i++;
						}
					
					echo '<table border="1" class="ProductLibrary" cellpadding="0" cellspacing="5" width="300" height="700">';
					echo '<tr><td height=\"150\">';
					$i = count($Products)-1;
					if($i >= 0) { 
						while($i >= 0) {
							if(($weightplank + $Products[$i]['weight']) > $shop::MAXWEIGHT) {
								echo "</td></tr><tr><td height=\"150\">";
								$weightplank	= 0;
								} else {
								echo "<div class=\"rood\"><a href=\"?view=".$i."\" alt=\"view Product\">".$shop->cleanInput($Products[$i]['title']).' - '.$Products[$i]['weight']."</a></div>";
								$weightplank = ($weightplank + $Products[$i]['weight']);
							}
						$i--;
						}
					}
					echo '</table>';

				} else {
					echo "<p class='Product'><em>ProductLibrary is empty...</em></p>";
				}
			?>
		

		<?php

		if(isset($_REQUEST['view'])) {
				$id = (int) $_REQUEST['view'];	
				echo "<div class=\"cover\">"; 
				echo "<a href=\"?edit=".$id."&view=".$id."\" alt=\"edit Product\">edit</a> | <a href=\"?remove=".$shop->cleanInput(ucfirst($Products[$id]['isbn']))."\" alt=\"remove Product\">remove</a>";
				echo "<p>".$shop->cleanInput(ucfirst($Products[$id]['title']))."</p>";
				echo "</div>";
			}

			$id = (int)$_REQUEST['edit'];
			
			$shop  = new ProductLibrary();
			$Products = $shop->decode();
			$item = $Products[$id];
			$title  = $item['title'];
			$isbn = $item['isbn'];
			$weight = $item['weight'];
			$description = $item['description'];
		
		?>
		<h1>Edit Product.</h1>
			<form action="" method="post">
				<fieldset>
				<label>Product title:</label><br />
				<input type = "text" name="title" size="80" value="<?= $shop->cleanInput($title); ?>" />
				<label>ISBN nummer:</label><br />
				<input type = "text" name="isbn" size="80" value="<?= $shop->cleanInput($isbn); ?>"/>
				<label>Weight (gram):</label><br />
				<input type = "text" name="weight" size="80" value="<?= $shop->cleanInput($weight); ?>" />
				<label>Description:</label><br />
				<textarea rows="10" cols="40" name="description"><?= $shop->cleanInput($description); ?></textarea><br />
				<input type= "hidden" value="<?=$shop->cleanInput($isbn);?>" name="editProduct" /><br />
				<input type= "submit" value="Edit." />
				</fieldset>
			</form>


		<div id="output">
		</div>
	</body>

</html>