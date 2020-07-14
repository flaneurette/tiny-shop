<pre><?php

session_start();

$_SESSION['cart'] =[];

	function addtocart($obj) 
	{ 
		//$_SESSION['cart'] =[];
		// $c = count($_SESSION['cart']);
		
		array_push($_SESSION['cart'],$obj);
		/*
		if($c > 0) { 

			for($i = 0; $i <= $c; $i++) {
				
					if($_SESSION['cart'][$i]['product.id'] === $obj['product.id']) {
						
						if($obj['product.qty'] < 1) {
							$obj['product.qty'] = 1;
							} elseif($obj['product.qty'] > 9999) {
							$obj['product.qty'] = 1;
						} else {}
						
						$_SESSION['cart'][$i]['product.qty'] = ($_SESSION['cart'][$i]['product.qty'] + $obj['product.qty']);
						} else {
						array_push($_SESSION['cart'],$obj);
					}
			}
			
		} else {
			$_SESSION['cart'] = [];
			array_push($_SESSION['cart'],$obj);
		}
		*/
		return true;
	} 	
	
	
function unique_array($array, $needle=false) {
    
    if(is_array($array)) {
		
		$arraynew = [];
		$c = count($array);
		$i=0;
		foreach($array as $key => $value) {
		    if($needle) {
    		    if(!in_array($array[$key][$needle],array_column($arraynew,$needle))) {
    		        array_push($arraynew,$array[$i]); 
    		    }
		    } else {
    		    if(!in_array($array[$i],$arraynew)) {
    		      //  array_push($arraynew,$array[$i]);
    		    }		        
		    }
		 $i++;
		}
		
	return $arraynew;
    } else {
    return false;
    }
}


$array1 = ['product.id' => 12,'product.qty' => 11];
$array2 = ['product.id' => 13,'product.qty' => 11];
$array3 = ['product.id' => 12,'product.qty' => 11];
$array4 = ['product.id' => 14,'product.qty' => 11];

addtocart($array1);
addtocart($array2);
addtocart($array3);
addtocart($array4);
  
$_SESSION['cart'] = unique_array($_SESSION['cart'], 'product.id');

var_dump($_SESSION['cart']);
?>
</pre>