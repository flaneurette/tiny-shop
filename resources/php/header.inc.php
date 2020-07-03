<?php

	ini_set('display_errors', 1); 
	ini_set('session.cookie_httponly', 1);
	ini_set('session.use_only_cookies', 1);
	ini_set('session.cookie_secure', 1);
	
	session_start();
	
	// error_reporting(E_ALL);

	// Optional headers to consider.
	header("X-Frame-Options: DENY"); 
	header("X-XSS-Protection: 1; mode=block"); 
	header("Strict-Transport-Security: max-age=30");
	header("Referrer-Policy: same-origin");
	
	/* ************************
	 * Optional headers to set. 
	 * ************************
	 Custom header:
	 ----------------
	 header("HeaderName: HeaderValue");
	 
	 Prevent caching:
	 ----------------
	 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	 header("Cache-Control: no-cache");
	 header("Pragma: no-cache");
	 
	 Content security policy: 
	 These are quite strict, and may break functionaility. Use at your own discretion.
	 ----------------
	 * header("Content-Security-Policy: max-age=30");
	 * header("Content-Security-Policy: default-src 'self'; script-src example.com 'nonce-".uniqid()."'; frame-src 'self'; style-src 'self'; img-src 'self';");
	 * header("Content-Security-Policy: script-src 'unsafe-inline' ; style-src 'unsafe-inline' ");
	 * header("HTTP Strict Transport Security: max-age=31536000 ; includeSubDomains");
	 * header("Public-Key-Pins: pin-sha256="d6qzRu9zOECb90Uez27xWltNsj0e1Md7GkYYkVoZWmM="; pin-sha256="E9CZ9INDbd+2eRQozYqqbQ2yXLVKB9+xcprMF+44U1g="; report-uri="http://example.com/pkp-report"; max-age=10000; includeSubDomains");
	 * header("X-Content-Type-Options: nosniff"); 
	 * header("Referrer-Policy: no-referrer");
	 * header("Expect-CT: max-age=86400, enforce, report-uri="https://foo.example/report"");
	 * header("Feature-Policy: vibrate 'none'; geolocation 'none'");
	 
	*/
?>
