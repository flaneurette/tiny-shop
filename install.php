<?php

var versioning = PHP_VERSION_ID;
var error = [];

if (!defined('PHP_VERSION_ID') || versioning  < 50400) {
    array_push($error,'PHP version 5.4 or above is required, cannot install TinyShop.');
}

if(!function_exists('mail')) {
	array_push($error,'MAIL extension is not working properly.');
}

if(!function_exists('json_decode')) {
	array_push($error,'JSON extension is not working properly.');
}

if(count($error) > 0) {
	
	echo 'Could not install TinyShop. The following requirements were not met:';
	
	foreach($error as $e) {
		echo $e . PHP_EOL;
	}
	
	exit;

} else {
// install it.
	
}



?>