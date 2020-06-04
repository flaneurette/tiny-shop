<?php

class Shop {

	CONST SHOP  		= "./Shop.json";
	CONST CSV 		= "./Shop.csv";
	CONST DEPTH		= 1024;
	CONST MAXWEIGHT 	= 10000;
	CONST MAXTITLE 		= 255; // Max length of title.
	CONST MAXDESCRIPTION 	= 500; // Max length of description.
	CONST CURRENCY 		= 1;   // Choose from currency list below.
	CONST PWD 		= "Password to encrypt"; // optional.
	CONST FILE_ENC  	= "UTF-8";
	CONST FILE_OS  	 	= "WINDOWS-1252";
	
	CONST CURRENCIES = [
		"0" => array(['&#8352;','EURO-CURRENCY SIGN']),
		"1" => array(['&#8383;','BITCOIN SIGN']),
		"2" => array(['&#163;','POUND SIGN']),
		"3" => array(['&#36;','DOLLAR SIGN']),
		"4" => array(['&#165;','YEN SIGN']),
		"5" => array(['&#162;','CENT SIGN']),
		"6" => array(['&#8355;','FRENCH FRANC SIGN']),
		"7" => array(['&#8359;','PESETA SIGN']),
		"8" => array(['&#8360;','RUPEE SIGN']),
		"9" => array(['&#8361;','WON SIGN']),
		"10" => array(['&#8362;','NEW SHEQEL SIGN']),
		"11" => array(['&#8363;','DONG SIGN']),
		"12" => array(['&#8364;','EURO SIGN']),
		"13" => array(['&#8365;','KIP SIGN']),
		"14" => array(['&#8366;','TUGRIK SIGN']),
		"15" => array(['&#8367;','DRACHMA SIGN']),
		"16" => array(['&#8368;','GERMAN PENNY SYMBOL']),
		"17" => array(['&#8369;','PESO SIGN']),
		"18" => array(['&#8370;','GUARANI SIGN']),
		"19" => array(['&#8371;','AUSTRAL SIGN']),
		"20" => array(['&#8372;','HRYVNIA SIGN']),
		"21" => array(['&#8373;','CEDI SIGN']),
		"22" => array(['&#8374;','LIVRE TOURNOIS SIGN']),
		"23" => array(['&#8375;','SPESMILO SIGN']),
		"24" => array(['&#8376;','TENGE SIGN']),
		"25" => array(['&#8377;','INDIAN RUPEE SIGN']),
		"26" => array(['&#8378;','TURKISH LIRA SIGN']),
		"27" => array(['&#8379;','NORDIC MARK SIGN']),
		"28" => array(['&#8380;','MANAT SIGN']),
		"29" => array(['&#8381;','RUBLE SIGN']),
		"30" => array(['&#8382;','LARI SIGN']),
		"31" => array(['&#8353;','COLON SIGN']),
		"32" => array(['&#8354;','CRUZEIRO SIGN']),
		"33" => array(['&#8358;','NAIRA SIGN']),
		"34" => array(['&#8356;','LIRA SIGN']),
		"35" => array(['&#8357;','MILL SIGN'])
	];
	
	// CURRENCY = CURRENCIES[2][0][0];

	public function __construct() {
		$incomplete = false;
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
	* SEO-ing URL.
	* @param string
	* @return string
	*/
	public function seoUrl($string) 
	{
		$find 		= [' ','_','=','+','&','&nbsp;','.'];
		$replace 	= ['-','-','-','-','-','-','-'];
		$string 	= str_replace($find,$replace,strtolower($string));
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}	
	
	/**
	* Encodes JSON object
	* @param shop
	* @return void
	*/
	public function encode($shop) 
	{
		return json_encode($shop, JSON_PRETTY_PRINT);
	}
	/**
	* Loads and decodes JSON object
	* @return mixed object/array
	*/
	public function decode() 
	{
		return json_decode(file_get_contents(self::SHOP), true, self::DEPTH, JSON_BIGINT_AS_STRING);
	}
	
	public function addshop() 
	{
		$newshop = 
			array(
			  "id" => "{$this->listing_id}",
			  "product" => "{$this->product}",
			  "title" => "{$this->title}",
			  "description" => "{$this->description}",
			  "catno" => "{$this->catno}",
			  "category" => "{$this->category}",
			  "image" => "{$this->image}",
			  "format" => "{$this->format}",
			  "quantity" => "{$this->quantity}",
			  "status" => "{$this->status}",
			  "price" => "{$this->price}",
			  "listed" => "{$this->listed}",
			  "stock" => "{$this->stock}",
			  "EAN" => "{$this->EAN}",
			  "weight" => "{$this->weight}",
			  "format" => "{$this->format}",
			  "datetime" => "{$this->datetime}",
			  "condition" => "{$this->condition}",
			  "weight" => "{$this->weight}",
			  "shipping" => "{$this->shipping}",
			  "status" => "{$this->status}"
		);
		
		$lijst = $this->decode();
		$i = count($lijst);
		$lijst = array($newshop);

		$this->storeshop($lijst);
	}


	public function editshop($id) 
	{
		$product = 
			array(
			  "id" => "{$this->id}",
			  "product" => "{$this->product}",
			  "title" => "{$this->title}",
			  "description" => "{$this->description}",
			  "catno" => "{$this->catno}",
			  "category" => "{$this->category}",
			  "image" => "{$this->image}",
			  "format" => "{$this->format}",
			  "quantity" => "{$this->quantity}",
			  "status" => "{$this->status}",
			  "price" => "{$this->price}",
			  "listed" => "{$this->listed}",
			  "stock" => "{$this->stock}",
			  "EAN" => "{$this->EAN}",
			  "weight" => "{$this->weight}",
			  "format" => "{$this->format}",
			  "datetime" => "{$this->datetime}",
			  "condition" => "{$this->condition}",
			  "weight" => "{$this->weight}",
			  "shipping" => "{$this->shipping}",
			  "status" => "{$this->status}"
		);
		
		$list = $this->decode();
		
		foreach ($list as $key => $value) {
			if ($value['id'] == $id) {
				  $list[$key]['id'] = "{$this->id}";
				  $list[$key]['product'] = "{$this->product}";
				  $list[$key]['title'] = "{$this->title}";
				  $list[$key]['description'] = "{$this->description}";
				  $list[$key]['catno'] = "{$this->catno}";
				  $list[$key]['category'] = "{$this->category}";
				  $list[$key]['image'] = "{$this->image}";
				  $list[$key]['format'] = "{$this->format}";
				  $list[$key]['quantity'] = "{$this->quantity}";
				  $list[$key]['status'] = "{$this->status}";
				  $list[$key]['price'] = "{$this->price}";
				  $list[$key]['listed'] = "{$this->listed}";
				  $list[$key]['stock'] = "{$this->stock}";
				  $list[$key]['EAN'] = "{$this->EAN}";
				  $list[$key]['weight'] = "{$this->weight}";
				  $list[$key]['format'] = "{$this->format}";
				  $list[$key]['datetime'] = "{$this->datetime}";
				  $list[$key]['condition'] = "{$this->condition}";
				  $list[$key]['weight'] = "{$this->weight}";
				  $list[$key]['shipping'] = "{$this->shipping}";
				  $list[$key]['status'] = "{$this->status}";
			}
		}
		
		$list[$i] = $product;
		# var_dump($list); // debugging, to show list.
		$this->storeshop($list);
	}

	public function checkForm() 
	{
	
      isset($_POST['id']) 	? $this->id = $this->cleanInput($_POST['id']) : $id = false;  
      isset($_POST['product']) 	? $this->product = $this->cleanInput($_POST['product']) : $product = false;  
      isset($_POST['title']) 	? $this->title = $this->cleanInput($_POST['title']) : $title = false;  
      isset($_POST['description']) ? $this->description = $this->cleanInput($_POST['description']) : $description = false;  
      isset($_POST['catno']) 	? $this->catno = $this->cleanInput($_POST['catno']) : $catno = false;  
      isset($_POST['category']) ? $this->category = $this->cleanInput($_POST['category']) : $category = false;  
      isset($_POST['image']) 	? $this->image = $this->cleanInput($_POST['image']) : $image = false;  
      isset($_POST['format']) 	? $this->format = $this->cleanInput($_POST['format']) : $format = false;  
      isset($_POST['quantity']) ? $this->quantity = $this->cleanInput($_POST['quantity']) : $quantity = false;  
      isset($_POST['status']) 	? $this->status = $this->cleanInput($_POST['status']) : $status = false;  
      isset($_POST['price']) 	? $this->price = $this->cleanInput($_POST['price']) : $price = false;  
      isset($_POST['listed']) 	? $this->listed = $this->cleanInput($_POST['listed']) : $listed = false;  
      isset($_POST['stock']) 	? $this->stock = $this->cleanInput($_POST['stock']) : $stock = false;  
      isset($_POST['EAN']) 	? $this->EAN = $this->cleanInput($_POST['EAN']) : $EAN = false;  
      isset($_POST['weight']) 	? $this->weight = $this->cleanInput($_POST['weight']) : $weight = false;  
      isset($_POST['format']) 	? $this->format = $this->cleanInput($_POST['format']) : $format = false;  
      isset($_POST['datetime']) ? $this->datetime = $this->cleanInput($_POST['datetime']) : $datetime = false;  
      isset($_POST['condition'])? $this->condition = $this->cleanInput($_POST['condition']) : $condition = false;  
      isset($_POST['weight']) 	? $this->weight = $this->cleanInput($_POST['weight']) : $weight = false;  
      isset($_POST['shipping']) ? $this->shipping = $this->cleanInput($_POST['shipping']) : $shipping = false;  
      isset($_POST['status']) 	? $this->status = $this->cleanInput($_POST['status']) : $status = false; 

		$_SESSION['messages'] = array();

		if($this->title != false) {
			if(strlen($this->title) > self::MAXTITLE ) {
				$this->message('Title may not be longer than '.self::MAXTITLE.' characters.');
				return false;
			}
		}  else {
				$this->message('Title may not be empty.');
				return false;
		}

		if($this->weight != false) {
			if(!is_int((int)$this->weight) || preg_match("/[a-zA-Z]/i",$this->weight)) { 
				$this->message('Weight may not contain characters.');
				return false;
			}
		}  else {
				$this->message('Weight must not be empty.');
				return false;
		}

		if($this->description != false) {
			if(strlen($this->description) > self::MAXDESCRIPTION ) {
				$this->message('Description may not be longer than '.self::MAXDESCRIPTION.' characters.');
				return false;
			}
		}  else {
				$this->message('Description may not be empty.');
				return false;
		}
	}

	public function message($value) 
	{
		if(isset($_SESSION['messages'])) { 
			array_push($_SESSION['messages'],$value);  
			} else { 
			$_SESSION['messages'] = array(); 
		} 	
	}

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
	* Store shop into SHOP
	* @param array $shop
	* @return boolean, true for success, false for failure.
	*/
	public function storeshop($shop) 
	{
		// make a backup before doing anything.
		$file 	= self::SHOP;
		$copy 	= self::SHOP.'.bak';
		@copy($file, $copy);
		// convert encoding
		$json = mb_convert_encoding($this->encode($shop), self::FILE_ENC, self::FILE_OS);
		// write file.
		file_put_contents(self::SHOP,$json, LOCK_EX);
	}
	
	/**
	* Delete product in shop
	* @param array $shop
	* @return boolean, true for success, false for failure.
	*/	
	public function deleteshop($shop) 
	{
		$lijst = $this->decode();
		if($lijst !== null) {
			$libraylist = usort($lijst, $this->sortISBN('isbn'));
			$shops = array();
			foreach($lijst as $c) {	
				echo $shop."<br>";
				if($c['isbn'] != $shop) {
					array_push($shops,$c);
				}
			}
		}
		$this->storeshop($shops);
	}
	
	/**
	* Converter for data, types and strings.
	* @param string $string
	* @return array
	*/	
	public function convert($string,$method,$file){
		
		$data = [];
		
		switch($method) {
			
			case 'csv_to_json':
			
				if(!isset($file)) {
					$this->message('Please choose a CSV file to convert.');
					break;
				}
		
				$data = array_map("str_getcsv", explode("\n", $file));
				$columns = $data[0];
				foreach ($data as $row_index => $row_data) {
					if($row_index === 0) continue;
					$data[$row_index] = [];
					foreach ($row_data as $column_index => $column_value) {
						$label = $columns[$column_index];
						$data[$row_index][$label] = $column_value;       
					}
				}	
				
			break;
			
			case 'json_to_csv':

				if(!defined(self::SHOP)) {
					$this->message('Conversion failed: JSON file not found.');
					break;
				}
				
				if(!defined(self::CSV)) {
					$this->message('Conversion failed: CSV file not found.');
					break;
				}
				
				$json_data = $this->convert(self::SHOP,'json_decode');
				$csv_file = fopen(self::CSV, 'w');
				$header = false;
				
				foreach ($json_data as $line){

					if (empty($header)) {
						$header = array_keys($line);
						fputcsv($f, $header);
						$header = array_flip($header);
					}
					
					$data = array($line['type']);
					foreach ($line as $value) {
						array_push($data,$value);
					}
					
					array_push($data,$line['stream_type']);
					fputcsv($csv_file, $data);
				}
			break;
			
			case 'json_decode':
			$data = json_decode(file_get_contents($file), true, self::DEPTH, JSON_BIGINT_AS_STRING);
			break;

			case 'json_encode':
			$data = json_encode($shop, JSON_PRETTY_PRINT);
			break;
		}
		
		return $data;
	}
	
	/**
	* Sorting a string as a human would.
	* @param string $string
	* @return $string
	*/	
	public function sortNatural($key) {
		return function ($a, $b) use ($key) {
			return strnatcmp($a[$key], $b[$key]);
		};
	}

	/**
	* Encryption function (requires OpenSSL)
	* @param string $plaintext
	* @return $ciphertext
	*/	
	
	// We don't use this, but you could call it to encrypt the JSON data.
	public function encrypt($plaintext) {

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
	
	// We don't use this, but you could call it to decrypt the JSON data.
	public function decrypt($ciphertext) {
		
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
