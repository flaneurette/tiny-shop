<?php

namespace security\forms;

###########################################################################
##                                                                       ##
##  Copyright 2008-2019 Alexandra van den Heetkamp.                      ##
##                                                                       ##
##  Secure Mail Class. This class processes e-mails coming from a        ##
##  contact form.                                                        ##
##                                                                       ##
##  This class is free software: you can redistribute it and/or modify it##
##  under the terms of the GNU General Public License as published       ##
##  by the Free Software Foundation, either version 3 of the             ##
##  License, or any later version.                                       ##
##                                                                       ##
##  This class is distributed in the hope that it will be useful, but    ##
##  WITHOUT ANY WARRANTY; without even the implied warranty of           ##
##  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        ##
##  GNU General Public License for more details.                         ##
##  <http://www.gnu.org/licenses/>.                                      ##
##                                                                       ##
###########################################################################

class SecureMail
{
	### REQUIRED CONFIGURATION
	const DOMAIN			= 'yourserver.tld'; // Domain this script is hosted on.
	const SERVERADDR		= 'server <server@yourserver.tld>'; // Server e-mail address.
	const DEFAULTTO			= 'info@yourserver.tld'; // default "to" e-mail address when address has not been provided.
	
	### OPTIONAL CONFIGURATION (DEFAULT)
	const XMAILER			= 'Secure Mail'; // Name class mailer.
	const LANGUAGE			= 'en'; 	// en, fr, de, x-klingon. (rfc1766) 
	const MIMEVERSION		= '1.0';	// Mime-type version
	const TRANSFERENCODING 		= '8bit';	// Transfer encoding recommended: 8bit. (7bit, base64, quoted-printable)
	const CHARSET 			= 'UTF-8';	// Characterset of expected e-mail, recommended: utf8.
	const MAILFORMAT		= 'Flowed'; 	// Fixed, Flowed. (rfc3676)
	const DELSP			= 'Yes'; 	// Yes, No. (rfc3676)
	const OPTPARAM			= '-f'; 	// Optional 5th parameter. -f is required when SERVERADDR is set.
	const WORD_WRAP			= true;		// Wrap message?
	const WORD_WRAP_VALUE		= 70;		// Wrap at line length.
	const MAXBODYSIZE 		= 5000; 	// Number of chars of body text.
	const MAXFIELDSIZE 		= 150;   	// Number of allowed chars for single fields.
	const FORMTIME			= 10;  		// Minimum time in seconds for a user to fill out a form, detects bots.
	const TEMPLATE_START		= '{{';     	// Placeholder start for HTML template variables. 
	const TEMPLATE_END		= '}}';    	// Placeholder end for HTML template variables.
	
	### ADVANCED CONFIGURATION
	const PHPENCODING 		= 'UTF-8';	// Characterset of PHP functions: (htmlspecialchars, htmlentities) 
	const MINHASHBYTES		= 32; 		// Min. of bytes for secure hash.
	const MAXHASHBYTES		= 64; 		// Max. of bytes for secure hash, more increases cost. Max. recommended: 256 bytes.
	const MINMERSENNE		= 0xff; 	// Min. value of the Mersenne twister.
	const MAXMERSENNE		= 0xffffffff; 	// Max. value of the Mersenne twister.
	const SUPRESSMAILERROR  	= true; 	// Prevents PHP mail errors. (recommended)
	
	### EXPERIMENTAL CONFIGURATION
	const SENSITIVITY		= true;		// Enables sensitivity header.
	const SENSITIVITY_VALUE		= 'Normal'; 	// Normal, Personal, Private and Company-Confidential.
	const CUSTOMHEADER		= 'X-Klingon-Header-1'; // Optional, your own Header. The 'X-' part is required! (disabled by default)
	const CUSTOMHEADERVALUE		= 'JAJ VIGHAJ'; // Value of the custom Header. Klingon for: "Own the day." 

	### PRIVATE VARIABLES.
	private $sieve 			= 0;    // Empty sieve 
	private $slots 			= 10;	// Maximum number of mail slots per user, per browse session incuding refresh and errors. Increase for testing purposes.                      
		
	### ARRAYS
	// Detect proxy ports.
	const PROXYPORTS = [
		80, 
		443, 
		808, 
		3128, 
		8080, 
		8118, 
		1080
	]; 
	
	// Allowed request methods to access the form. GET is required. 
	const REQUESTMETHODS = [
		'POST',
		'GET'
	]; 
	
	// Attempts to find fragments of robots.
	const DISALLOWEDAGENTS = [
		'java',
		'curl',
		'wget',
		'winhttp',
		'HTTrack',
		'chromeframe',
		'clshttp',
		'archiver',
		'loader',
		'email',
		'harvest',
		'extract',
		'exploit',
		'grab',
		'miner',
		'metasploit',
		'libwww',
		'curl',
		'python',
		'nikto',
		'acunetix',
		'scan'
	]; 
	
	// Disallowed characters in the fields.
	const DISALLOWEDCHARS = [
		'%0A',
		'%0D',
		'\u000A',
		'\u000D',
		'0x000d',
		'0x000a',
		'&#13;',
		'&#10;',
		'\r',
		'\n',
		';',
		'<',
		'>',
		'`',
		'~',
		'$',
		'%',
		'/',
		'\\',
		'{',
		'}',
		'[',
		']',
		'\'',
		'"',
		'=',
		'-=',
		'=-',
		'<?',
		'?>',
		'<%',
		'%>',
		'!#',
		'<<<',
		'../',
		'./'
	];
	
	// These are allowed only once in a field.
	const FIELDVECTORS = [
		'@',
		'+',
		'-'
	];	
	
	// Disallowed vectors to detect spam/e-mail injection.
	const BODYVECTORS= [
		'Return-Path',
		'Content-Type',
		'text/plain',
		'MIME-Version',
		'Content-Transfer-Encoding',
		'Subject:',
		'bcc:'
	];
	
	// Detect proxy header.
	const PROXY = [
		'HTTP_VIA',
		'VIA',
		'PROXY',
		'Proxy-Connection',
		'HTTP_X_FORWARDED_FOR',  
		'HTTP_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_FORWARDED',
		'HTTP_CLIENT_IP',
		'HTTP_FORWARDED_FOR_IP',
		'X-PROXY-ID',
		'MT-PROXY-ID',
		'X-TINYPROXY',
		'X_FORWARDED_FOR',
		'FORWARDED_FOR',
		'X_FORWARDED',
		'FORWARDED',
		'CLIENT-IP',
		'CLIENT_IP',
		'PROXY-AGENT',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'FORWARDED_FOR_IP',
		'HTTP_PROXY_CONNECTION'
	];	
	
	### END OF CONFIGURATION 

	public function __construct($params = array()) 
	{ 
		$this->init($params);
		$this->allocateMailSlots();
	}
	
	public function __destruct()
	{
		$this->bodyvectors = array();
		$this->fieldvectors = array();
	}
	
	public function fullScan() 
	{
		$this->allocateMailSlots();
		$this->fieldScan();
		$this->bodyScan();

		if($this->sieve >= 1) { 
			$this->sessionmessage('Mail sieve found issues within the form fields. Mail has not been sent!'); 
			return FALSE; // e-mail cannot be send.
			} else {
			return TRUE;
		}
	}
	
	/**
	* @var array form parameters.
	*/	
	public $fields = array();
	
	/**
	* Initializes object.
	* @param array $params
	* @throws Exception
	*/	
    	public function init($params=[])
        {
		try {
			
			isset($params['to'])         ? $this->fields['to']  = $params['to'] : self::DEFAULTTO; 
			isset($params['name'])       ? $this->fields['name']   = $params['name'] : ''; 
			isset($params['email'])      ? $this->fields['email']    = $params['email']  : '';
			isset($params['url'])        ? $this->fields['url']    = $params['url']  : ''; 
			isset($params['phone'])      ? $this->fields['phone']    = $params['phone']  : '';
			isset($params['address'])    ? $this->fields['address']    = $params['address']  : '';	
			isset($params['city'])       ? $this->fields['city']    = $params['city']  : '';
			isset($params['country'])    ? $this->fields['country']    = $params['country']  : '';				
			isset($params['subject'])    ? $this->fields['subject']   = $params['subject'] : '';
			isset($params['terms'])      ? $this->fields['terms']   = $params['terms'] : '';
			isset($params['captcha'])    ? $this->fields['captcha']   = $params['captcha'] : '';
			isset($params['extrafield']) ? $this->fields['extrafield']   = $params['extrafield'] : '';
			isset($params['body'])       ? $this->body['body'] = $params['body'] : false;
		} catch(Exception $e) {
			$this->sessionmessage('Problem initializing:'.$e->getMessage());
		}
    	}

	/**
	* Performs a scan on the field contents.
	* @return boolean
	*/	
	public function fieldScan() 
	
	{	
	
		$fieldarray = array_values($this->fields);
	
		foreach($fieldarray as $key => $value)  {
		
				// check fieldsize.
				if(strlen($value) > self::MAXFIELDSIZE) { 
					$this->sessionmessage('Issue found: length of characters inside field exceed the maximum of ' . self::MAXFIELDSIZE); 
					$this->sieve++; 
					// returning false already, in case of overflow.
					return FALSE;
				} 
				
				// check for disallowed chars
				for($j=0; $j<count(self::DISALLOWEDCHARS); $j++) { 
					if(stristr($value,self::DISALLOWEDCHARS[$j])) {	
					$this->sessionmessage('Issue found: disallowed characters.'); 
					$this->sieve++;  
					}	
				}
				// scan for duplicate characters.
				for($k=0; $k<count(self::FIELDVECTORS); $k++) {
					if(substr_count($value, self::FIELDVECTORS[$k]) >1) { 
						$this->sessionmessage('Issue found: duplicate characters.'); 
						$this->sieve++; 
					} 
				}
		}
		
		if($this->sieve >= 1) { 
			$this->sessionmessage('Mail sieve found issues within the form fields. Mail has not been sent!'); 
			return FALSE; // e-mail cannot be send.
			} else {
			return TRUE;
		}
	}
	
	/**
	* Performs a scan on the mail contents, and compares vectors that should not be present in the body text.
	* @return boolean
	*/	
	public function bodyScan() 
	{	
		if($this->body['body'] != false) {
			for($i=0; $i<count(self::BODYVECTORS); $i++) {
				if(stristr($this->body['body'], self::BODYVECTORS[$i])) { 
					$this->sessionmessage('Issue found: body text contains disallowed characters.'); 
					$this->sieve++; 
				}
			}
		} else {
			$this->sessionmessage('Issue: body cannot be empty. Mail has not been sent!'); 
			$this->sieve++; 
		}
		
		if(strlen($this->body['body']) > self::MAXBODYSIZE) {
			$this->sessionmessage('Issue: Maximum body text exceeded:' . self::MAXBODYSIZE); 
			$this->sieve++; 		
		}
		
		if($this->sieve >= 1) { 
			$this->sessionmessage('Mail sieve found issues within the form fields. Mail has not been sent!'); 
			return FALSE; // e-mail cannot be send.
			} else {
			return TRUE;
		}
	}
	/**
	* The main mail function.
	* @return mixed boolean.
	*/	
	public function sendmail() 
	{	
	
		$mime_headers = [];
		$from    = self::SERVERADDR; 		
		$to      = $this->clean($this->fields['to'],'field');
		$name    = $this->clean($this->fields['name'],'field');
		$email    = $this->clean($this->fields['email'],'field');
		$subject = $this->clean($this->fields['subject'],'field');
		$message = $this->clean($this->body['body'],'body');
		$ip      = $this->clean($_SERVER['REMOTE_ADDR'],'field');
		
		$headers = [
			'From'                      	=> self::SERVERADDR,
			'Sender'                    	=> self::SERVERADDR,
			'Return-Path'               	=> self::SERVERADDR,
			'MIME-Version'              	=> self::MIMEVERSION,
			'Content-Type'              	=> 'text/plain; charset='.self::CHARSET.'; format='.self::MAILFORMAT.'; delsp='.self::DELSP,
			'Content-Language'		=> self::LANGUAGE,
			'Content-Transfer-Encoding' 	=> self::TRANSFERENCODING,
			'X-Mailer'                  	=> self::XMAILER,
			'Date'				=> date('r'),
			'Message-Id'			=> $this->generateBytes(),
		];
		
		if(self::SENSITIVITY == true) {
			$custom = array('Sensitivity' => self::SENSITIVITY_VALUE);
			$headers = array_merge($headers,$custom);
		}			
			
		if(self::CUSTOMHEADERVALUE != 'JAJ VIGHAJ') {
			$custom = array(self::CUSTOMHEADER => self::CUSTOMHEADERVALUE);
			$headers = array_merge($headers,$custom);
		}	
		
		foreach ($headers as $key => $value) {
			$mime_headers[] = "$key: $value";
		}
		
		$mail_headers = join("\n", $mime_headers);
		
		$message .= "\n\n";
		$message .= "From: " . $email;
		$message .= "\n";
		$message .= "IP: " . $ip;
		
		if(self::WORD_WRAP == true) {
			$message = wordwrap($message, self::WORD_WRAP_VALUE, "\r\n");
		}
		
		if(self::SUPRESSMAILERROR == true) {
			$send = @mail($to, $subject, $message, $mail_headers, self::OPTPARAM . $from);
			} else {
			$send = mail($to, $subject, $message, $mail_headers, self::OPTPARAM . $from);
		}
		return TRUE;
	}
	
	/**
	* Parses html templates.
	* @return string html code.
	*/
	public function parseTemplate($template,$parameters) {	
		$html = '';
		if(file_exists($template)) {
			$html = file_get_contents($template);
			if(is_array($parameters) && is_string($html)) {
				foreach ($parameters as $key => $value) {
					$html = str_ireplace(self::TEMPLATE_START.$key.self::TEMPLATE_END, $value, $html);
				}
			}
		} else {
			$this->sessionmessage('Template file does not exist.'); 
			return FALSE; // e-mail cannot be send.	
		}
		
		return $html;
	}
	
	/**
	* Detect robot through various tests. If found, we (intend to) show a captcha.
	* @return mixed boolean. TRUE if detected.
	*/
	public function detectrobot() {

		$requestMethod 	= $_SERVER['REQUEST_METHOD'];
		$userAgent 	= $_SERVER['HTTP_USER_AGENT'];
		$port		= $_SERVER['REMOTE_PORT'];
		
		$sizeRm = strlen($requestMethod);
		$sizeUa = strlen($userAgent);
		
		if($sizeRm > 12 || $sizeRm < 2) { 
			return TRUE;
			} else {
			// Find request method.
			if(!in_array($requestMethod,self::REQUESTMETHODS)) { 
			return TRUE;
			}
		}

		// Scan the port for proxy.
		if(in_array($port,self::PROXYPORTS)) {
			return TRUE;
		}

		if(isset($userAgent)) {
			// Check maximum and minimum size of user-agent.
			if($sizeUa > 512 || $sizeUa < 1) {
				return TRUE;
			}
		} 
		
		foreach(self::DISALLOWEDAGENTS as $key) {
			if(stristr($userAgent, $key)) {
				return TRUE;
				break;
			}

		} 

		foreach(self::PROXY as $value){
			if (isset($_SERVER[$value])) {
				return TRUE;
				break;
			}
		}
	}
	
  	/**
	* Allocates a timeslot. If the form is submited under 10 seconds, we can assume it's a bot.
	* @return mixed boolean, void.
	*/	
	public function setTime()
	{
		$_SESSION['form_time'] = microtime(true);	
		return TRUE;
	}
	
  	/**
	* Check timeslot. If the form is submited under 10 seconds, we can assume it's a bot.
	* @return mixed boolean, void.
	*/
	public function getTime()
	{
		if(isset($_SESSION['form_time'])) {
			
			$time_start = $_SESSION['form_time'];
			$time_end = microtime(true);
			$duration = round($time_end - $time_start);
			
			if($duration < self::FORMTIME) {
				$this->sessionmessage('Issue: form was submitted too quickly, looks like a bot.'); 
				return FALSE; 
				} else {
				return TRUE; 
			}
		} else {
			$this->sessionmessage('Issue: session time not initiated.'); 
			return FALSE; 			
		}
	}
	
 	/**
	* Allocates a pseudo random token to prevent CSRF.
	* @return mixed boolean, void.
	*/
	public function getToken()
	{
		
		$bytes = 0;
		
		if (function_exists('random_bytes')) {
			$len   = mt_rand(self::MINHASHBYTES,self::MAXHASHBYTES);
        		$bytes .= bin2hex(random_bytes($len));
    		}
		if (function_exists('openssl_random_pseudo_bytes')) {
			$len   = mt_rand(self::MINHASHBYTES,self::MAXHASHBYTES);
        		$bytes .= bin2hex(openssl_random_pseudo_bytes($len));
    		}
		
		if(strlen($bytes) < 128) {
			$bytes .= mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE)
				. mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) 
				. mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) 
				. mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE); 
		}
		
		$token = hash('sha512',$bytes);
		
		if(isset($_SESSION['token']) && $_SESSION['token'] != false) 
		{ 
			if(strlen($_SESSION['token']) < 128) {
				$this->sessionmessage('Issue found: session token is too short.'); 
				$this->sieve++; 
				} else {
				return $this->clean($_SESSION['token'],'alphanum'); 
			}
		} else { 
		return $token;
		} 
	} 
	
 	/**
	* Destroys the previously set token.
	* @return mixed string.
	*/
	public function destroyToken()
	{
		try {
			if(isset($_SESSION['token'])) {
				$_SESSION['token'] = '';
				// session_unset();
				// session_destroy();
			}
		} catch(Exception $e) {
			$this->sessionmessage('Issue: session could not be destroyed, '.$e->getMessage());
			return FALSE;
		}
		return TRUE;
	}
 	/**
	* Generates psuedo random bytes for the message-id.
	* @return mixed string.
	*/
	public function generateBytes()
	{
		$bytes = '';
		
		if (function_exists('random_bytes')) {
        		$bytes .= bin2hex(random_bytes(16));
    		}
		
		if (function_exists('openssl_random_pseudo_bytes')) {
        		$bytes .= bin2hex(openssl_random_pseudo_bytes(16));
    		}	
		
		if(strlen($bytes) < 16) {
			$bytes .= mt_rand(self::MINMERSENNE,self::MAXMERSENNE); 
			$bytes .= mt_rand(self::MINMERSENNE,self::MAXMERSENNE); 
			$bytes .= mt_rand(self::MINMERSENNE,self::MAXMERSENNE); 
			$bytes .= mt_rand(self::MINMERSENNE,self::MAXMERSENNE); 
		}
		
		$pseudobytes = substr($bytes,0,16);
		
		return sprintf("<%s.%s@%s>", base_convert(microtime(), 10, 36), base_convert(bin2hex($pseudobytes), 16, 36), $this->clean(self::DOMAIN,'domain'));
	}
	
	/**
	* Allocates the maximum mail slots.
	* @return mixed boolean, void.
	*/
	private function allocateMailSlots()
	{
		if(isset($_SESSION['current_mail_slot'])) 
		{ 
			if($_SESSION['current_mail_slot'] >= $this->slots) { 
				$this->sessionmessage('Mail slots exceeded. It is not allowed to send more than '.$this->slots.' per session.'); 
				return FALSE; 
				} else { 
				$_SESSION['current_mail_slot']++; 
			} 
		} else { 
			$_SESSION['current_mail_slot'] = 1; 
		} 
	}
	
	/**
	* Store session messages
        * @param string $value
	* @return void
	*/ 
	public function sessionmessage($value) 
	{ 
		if(isset($_SESSION['mail_message'])) { 
			array_push($_SESSION['mail_message'],$value);  
		} else { 
			$_SESSION['mail_message'] = array(); 
			array_push($_SESSION['mail_message'],$value); 
		} 
		if(count($_SESSION['mail_message']) > 50) {
			echo 'Fatal error: could not allocate any more session messages.';
			exit;
		}		
	} 
	
	/**
	* Dumps session messages
	* @return void
	*/	
	public function showmessage() 
	{ 
		if(!empty($_SESSION['mail_message'])) { 
			echo "<pre>"; 
			echo "<strong>Message:</strong>\r\n"; 
			foreach($_SESSION['mail_message'] as $message) { 
				echo $this->clean($message,'encode') . "\r\n" ; 
			} echo "</pre>"; 
		} 
	} 
	/**
	* Clears session messages
	* @return void
	*/	
	public function clearmessages() 
	{
		$_SESSION['mail_message'] = array(); 
	}
	
	public function destroysession() 
	{
		session_unset();
		session_destroy();	
	}
	
	/**
	* Cleans a string.
	* @return string
	*/		
	public function clean($string,$method) {
		
		$buffer=self::MAXFIELDSIZE;
		
		$data = '';
		switch($method) {
			// *only* call preg_replace when a string already has been checked, this prevents regex exploits. 
			case 'alpha':
				$this->data =  preg_replace('/[^a-zA-Z]/','', $string);
			break;
			case 'alphanum':
				$this->data =  preg_replace('/[^a-zA-Z-0-9]/','', $string);
			break;
			case 'field':
				$this->data =  preg_replace('/[^A-Za-z0-9-_.@]/','', $string);
			break;			
			case 'num':
				$this->data =  preg_replace('/[^0-9]/','', $string);
			break;
			case 'unicode':
				$this->data =  preg_replace("/[^[:alnum:][:space:]]/u", '', $string);
			break;
			case 'encode':
				$this->data =  htmlspecialchars($string,ENT_QUOTES,self::PHPENCODING);
			break;
			case 'entities':
				$this->data =  htmlentities($string, ENT_QUOTES | ENT_HTML5, self::PHPENCODING);
			break;
			case 'domain':
				$this->data =  str_ireplace(array('http://','www.'),array('',''),$string);
			break;				
			case 'body':
				$this->data =  strip_tags($string);
			break;
			}
		return $this->data;
	}
}

?>
