<?php
	/*
	* TinyShop headers. 
	* For more header options, see readme.md
	*/

	ini_set('display_errors', 1); 
	ini_set('session.cookie_httponly', 1);
	ini_set('session.use_only_cookies', 1);
	ini_set('session.cookie_secure', 1);
	
	session_start();

	// error_reporting(E_ALL);

	header("X-Frame-Options: DENY"); 
	header("X-XSS-Protection: 1; mode=block"); 
	header("Strict-Transport-Security: max-age=30");
	header("Referrer-Policy: same-origin");
?>
