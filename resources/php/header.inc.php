<?php
	/*
	* TinyShop headers. 
	* For more header options, see readme.md
	*/

	ini_set('display_errors', 1); 
	ini_set('session.cookie_httponly', 1);
	ini_set('session.use_only_cookies', 1);
	ini_set('session.cookie_secure', 1);
	// if sessions still expire, check if PHP is allowed to modify .ini settings.
	ini_set('session.gc_maxlifetime',12*60*60);
	ini_set('session.cookie_lifetime',12*60*60);
	
	session_start();

	// error_reporting(E_ALL);

	header("X-Frame-Options: DENY"); 
	header("X-XSS-Protection: 1; mode=block"); 
	header("Strict-Transport-Security: max-age=30");
	header("Referrer-Policy: same-origin");
?>
