<?php 
			
		error_reporting(E_ALL);
		session_start();
		
		include("class.Shop.php");
		$shop  = new Shop();
		$shoplist = $shop->decode();
		
		
		$shop  = new Shop();
		$product_list = $shop->decode();

		if($product_list !== null) {

			$shoplist = $product_list;
			
			$iv = array();
			
			$i = 0;
			
				foreach($product_list as $c) {	
					array_push($iv,$c);
					$shop->cleanInput($c['title']);
					$i++;
				}
			
			echo "<pre>";
			var_dump($iv);
			echo "</pre>";
			
			echo '<table border="0" cellpadding="3" cellspacing="5" width="100%">';
			$i = count($iv)-1;
			
			if($i >= 0) { 
			$j=0;
				while($i >= 0) {


					$iv[$i];
					echo "<tr>";
					echo "<td><a href=\"".$iv[$i][0]."</td>";
					echo $i."-".$j;
					echo "</tr>";
				$j++;
				
				
				$i--;
				}
			}
			
			echo '</table>';

		} else {
			echo "<p class='book'><em>Shop database is empty... edit the JSON database and add a product.</em></p>";
		}
?>