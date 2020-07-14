<?php

class Session {

	CONST PWD 		= "Password to encrypt session data";
	CONST FILE_ENC  = "UTF-8";
	CONST MAXQTY    = 9999; // Max quantity per product, a fixed constant to prevent buffer overflows.

	public function __construct() {
		$incomplete = false;
		session_regenerate_id();
	}
	
	/**
	* Sanitizes user-input
	* @param string
	* @return string
	*/
	public function cleanInput($string) 
	{
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}
	/**
	* Sanitizes user-input
	* @param string
	* @return string
	*/
	public function cleanArray($string) 
	{
		if(is_array($string)) {
			return @array_map("htmlspecialchars", $string, array(ENT_QUOTES, 'UTF-8'));
			} else {
			return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
		}
	}
	
	/**
	* Session array with messages
	* @return mixed object/array
	*/	
	public function message($value) 
	{
		if(isset($_SESSION['messages'])) { 
			array_push($_SESSION['messages'],$value);  
			} else { 
			$_SESSION['messages'] = array(); 
		} 	
	}
	
	/**
	* Showing session messages.
	* @return mixed object/array
	*/	
	public function showmessage() 
	{ 
		if(isset($_SESSION['messages'])) { 
			echo "<pre>"; 
			echo "<strong>Message:</strong>\r\n"; 
			foreach($_SESSION['messages'] as $message) { 
				echo $message . "\r\n" ; 
			} echo "</pre>"; 
		} 
		$_SESSION['messages'] = array();
	} 
	
	/**
	* Showing session messages.
	* @return mixed object/array
	*/	
	
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
					    array_push($arraynew,$array[$i]);
					}		        
				}
			 $i++;
			}
			
		return $arraynew;
		} else {
		return false;
		}
	}

	public function sessioncheck() 
	{ 
		if(isset($_SESSION['cart'])) {
			if(isset($_SESSION['cart'][0])) {
				if($_SESSION['cart'][0] === NULL || $_SESSION['cart'] === NULL ) {
				$_SESSION['cart'] = [];
				}
			}
		}
		return true;
	}
	
	public function addtocart($obj) 
	{ 
		$c = count($_SESSION['cart']);
		
		if($obj['product.qty'] > self::MAXQTY) {
			$obj['product.qty'] = 1;
		}
		
		if($c > 0) { 

			for($i = 0; $i <= $c; $i++) {
				
					if($_SESSION['cart'][$i]['product.id'] === $obj['product.id']) {
						
						if($obj['product.qty'] < 1) {
							$obj['product.qty'] = 1;
							} elseif($obj['product.qty'] > self::MAXQTY) {
							$obj['product.qty'] = 1;
						} else {}
						
						if(($_SESSION['cart'][$i]['product.qty'] + $obj['product.qty']) > self::MAXQTY) {
						} else {
						$_SESSION['cart'][$i]['product.qty'] = ($_SESSION['cart'][$i]['product.qty'] + $obj['product.qty']);
						}
						} else {
						array_push($_SESSION['cart'],$obj);
					}
			}
			
		} else {
			$_SESSION['cart'] = [];
			array_push($_SESSION['cart'],$obj);
		}

		return true;
	} 	
	
	function deletefromcart($needle=false) {
		
		$array = $_SESSION['cart'];
		
		if($needle != false) {
			if(is_array($array)) {
				$c = count($array);
				$i=0;
				foreach($array as $key => $value) {
					if($needle) {  
						if(in_array($needle,$array[$i])) {
							if($array[$i]['product.id'] == $needle) {
								unset($array[$i]);
							}
						}
					}
				 $i++;
				}
			}
		}
		
		$array = array_values($array);

		return $array;
	}
	
	
	function updatecart($id,$qty) {
		
		$array = $_SESSION['cart'];
		
		$i=0;
		
		foreach($array as $key => $value) {
			
			if($array[$i]['product.id'] == $id) {
				$array[$i]['product.qty'] = (int) $qty;
			}
			
			$i++;
		}
		
		return $array;
	}
	
	
	
	/**
	* Showing session messages.
	* @return mixed object/array
	*/	
	public function getcart() 
	{ 
		if(isset($_SESSION['cart'])) { 

			$array = [];
			
			foreach($_SESSION['cart'] as $item) { 
				array_push($array,cleanArray($item));
			} 
			
		}  else {
			$_SESSION['cart'] = array();
		}
		return $array;
	} 	
	
	/**
	* Encryption function (requires OpenSSL)
	* @param string $plaintext
	* @return $ciphertext
	*/	
	public function encrypt($plaintext) 
	{

		if (!function_exists('openssl_encrypt')) {
			$this->message('Encryption failed: OpenSSL is not supported or enabled on this PHP instance.');
			return false;
    	}
		
		$key = self::PWD; // Password is set above at the Constants
		$ivlen = openssl_cipher_iv_length($cipher="AES-256-CTR");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		$ciphertext = base64_encode($iv.$hmac.$ciphertext_raw );
		return bin2hex($ciphertext);
	}
	
	/**
	* Decryption function (requires OpenSSL)
	* @param string $ciphertext
	* @return $plaintext or false if there is no support for OpenSSL.
	*/		
	public function decrypt($ciphertext) 
	{
		
		if (!function_exists('openssl_decrypt')) {
			$this->message('Decryption failed: OpenSSL is not supported or enabled on this PHP instance.');
			return false;
    	}
		
		$key = self::PWD; // Password is set above at the Constants
		$ciphertext = hex2bin($ciphertext);
		$c = base64_decode($ciphertext);
		$ivlen = openssl_cipher_iv_length($cipher="AES-256-CTR");
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len=32);
		$ciphertext_raw = substr($c, $ivlen+$sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
		
		if (hash_equals($hmac, $calcmac)) { //PHP 5.6+ timing attack safe comparison
			return $original_plaintext;
		}
	}
}
?>
