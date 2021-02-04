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
	ini_set('session.save_path',realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/session'));
	
	session_save_path(realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/session'));
	ini_set('session.gc_probability', 1);

	session_start();

	// error_reporting(E_ALL);

	header("X-Frame-Options: DENY"); 
	header("X-XSS-Protection: 1; mode=block"); 
	header("Strict-Transport-Security: max-age=30");
	header("Referrer-Policy: same-origin");
	
	function encode($string) {
		return htmlspecialchars($string,ENT_QUOTES,'UTF-8');
	}

	//$handle = fopen('./log.log', "r");
	if(filesize('./log.log') > 3000000) {
		//empty log
		@file_put_contents('./log.log', "");
		} else {
			if(isset($_SERVER['HTTP_REFERER'])) {
				$referer = $_SERVER['HTTP_REFERER'];
				} else {
				$referer = 'no-referer';
			}
		$log = date("F j, Y, g:i a") . ' - '. $_SERVER['REMOTE_ADDR'].' - '.$_SERVER['HTTP_USER_AGENT'].' - '. $referer.' - '.$_SERVER['SCRIPT_NAME']. ' - '.$_SERVER['QUERY_STRING']. PHP_EOL;
		@file_put_contents('./log.log', encode($log), FILE_APPEND);
	}
?>