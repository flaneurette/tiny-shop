<?php

class Shop {

	CONST SHOP  			= "./inventory/shop.json";
	CONST CSV  			= "./inventory/csv/shop.csv"; 
	CONST BACKUPEXT  		= ".bak"; 
	CONST PWD 			= "Password to encrypt JSON"; // optional.
	CONST FILE_ENC  		= "UTF-8";
	CONST FILE_OS  			= "WINDOWS-1252"; 
	CONST MAIN_PAYMENT_METHOD 	= 'PayPal'; // Tiny Store uses PayPal as default payment gateway.
	CONST PAYMENTGATEWAY 		= ''; 	// Only required for 3rd party payment processing.
	CONST DEPTH			= 1024;
	CONST MAXWEIGHT 		= 10000;
	CONST MAXTITLE 			= 255; // Max length of title.
	CONST MAXDESCRIPTION 		= 500; // Max length of description.
	CONST CURRENCY 			= 0;   // this should, ideally, be set in the JSON file: site.json.
	
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
	
	public function products() 
	{ 
		$products = array(
			"product_id" => "{$this->product_id}",
			"product_status" => "{$this->product_status}",
			"product_title" => "{$this->product_title}",
			"product_description" => "{$this->product_description}",
			"product_category" => "{$this->product_category}",
			"product_catno" => "{$this->product_catno}",
			"product_stock" => "{$this->product_stock}",
			"product_quantity" => "{$this->product_quantity}",
			"product_format" => "{$this->product_format}",
			"product_type" => "{$this->product_type}",
			"product_weight" => "{$this->product_weight}",
			"product_condition" => "{$this->product_condition}",
			"product_ean" => "{$this->product_ean}",
			"product_sku" => "{$this->product_sku}",
			"product_vendor" => "{$this->product_vendor}",
			"product_price" => "{$this->product_price}",
			"product_margin" => "{$this->product_margin}",
			"product_price_min" => "{$this->product_price_min}",
			"product_price_max" => "{$this->product_price_max}",
			"product_price_varies" => "{$this->product_price_varies}",
			"product_date" => "{$this->product_date}",
			"product_url" => "{$this->product_url}",
			"product_image" => "{$this->product_image}",
			"product_tags" => "{$this->product_tags}",
			"product_images" => "{$this->product_images}",
			"product_featured" => "{$this->product_featured}",
			"product_featured_location" => "{$this->product_featured_location}",
			"product_featured_carousel" => "{$this->product_featured_carousel}",
			"product_featured_image" => "{$this->product_featured_image}",
			"product_content" => "{$this->product_content}",
			"product_variants" => "{$this->product_variants}",
			"product_available" => "{$this->product_available}",
			"product_selected_variant" => "{$this->product_selected_variant}",
			"product_collections" => "{$this->product_collections}",
			"product_options" => "{$this->product_options}",
			"socialmedia_option1" => "{$this->socialmedia_option1}",
			"socialmedia_option2" => "{$this->socialmedia_option2}",
			"socialmedia_option3" => "{$this->socialmedia_option3}",
			"variant_title1" => "{$this->variant_title1}",
			"variant_title2" => "{$this->variant_title2}",
			"variant_title3" => "{$this->variant_title3}",
			"variant_image1" => "{$this->variant_image1}",
			"variant_image2" => "{$this->variant_image2}",
			"variant_image3" => "{$this->variant_image3}",
			"variant_option1" => "{$this->variant_option1}",
			"variant_option2" => "{$this->variant_option2}",
			"variant_option3" => "{$this->variant_option3}",
			"variant_price1" => "{$this->variant_price1}",
			"variant_price2" => "{$this->variant_price2}",
			"variant_price3" => "{$this->variant_price3}",
			"shipping" => "{$this->shipping}",
			"shipping_fixed_price" => "{$this->shipping_fixed_price}",
			"shipping_flatfee" => "{$this->shipping_flatfee}",
			"shipping_locations" => "{$this->shipping_locations}",
			"payment_paypal_button_id" => "{$this->payment_paypal_button_id}",
			"payment_payment_button1" => "{$this->payment_payment_button1}",
			"payment_payment_button2" => "{$this->payment_payment_button2}",
			"payment_payment_button3" => "{$this->payment_payment_button3}",
			"payment_payment_array" => "{$this->shipping_fixed_price}"
		);
		return $products;
	}
		
	public function addshop() 
	{
		$newshop = $this->products();
		$lijst = $this->decode();
		$i = count($lijst);
		$lijst = array($newshop);

		$this->storeshop($lijst);
	}

	public function editshop($id) 
	{
		$product =$this->products();
		$list = $this->decode();
		
		foreach ($list as $key => $value) {
			
			if ($value['id'] == $id) {
				
			$list[$key]['product_id'] = "{$this->product_id}";
			$list[$key]['product_status'] = "{$this->product_status}";
			$list[$key]['product_title'] = "{$this->product_title}";
			$list[$key]['product_description'] = "{$this->product_description}";
			$list[$key]['product_category'] = "{$this->product_category}";
			$list[$key]['product_catno'] = "{$this->product_catno}";
			$list[$key]['product_stock'] = "{$this->product_stock}";
			$list[$key]['product_quantity'] = "{$this->product_quantity}";
			$list[$key]['product_format'] = "{$this->product_format}";
			$list[$key]['product_type'] = "{$this->product_type}";
			$list[$key]['product_weight'] = "{$this->product_weight}";
			$list[$key]['product_condition'] = "{$this->product_condition}";
			$list[$key]['product_ean'] = "{$this->product_ean}";
			$list[$key]['product_sku'] = "{$this->product_sku}";
			$list[$key]['product_vendor'] = "{$this->product_vendor}";
			$list[$key]['product_price'] = "{$this->product_price}";
			$list[$key]['product_margin'] = "{$this->product_margin}";
			$list[$key]['product_price_min'] = "{$this->product_price_min}";
			$list[$key]['product_price_max'] = "{$this->product_price_max}";
			$list[$key]['product_price_varies'] = "{$this->product_price_varies}";
			$list[$key]['product_date'] = "{$this->product_date}";
			$list[$key]['product_url'] = "{$this->product_url}";
			$list[$key]['product_image'] = "{$this->product_image}";
			$list[$key]['product_tags'] = "{$this->product_tags}";
			$list[$key]['product_images'] = "{$this->product_images}";
			$list[$key]['product_featured'] = "{$this->product_featured}";
			$list[$key]['product_featured_location'] = "{$this->product_featured_location}";
			$list[$key]['product_featured_carousel'] = "{$this->product_featured_carousel}";
			$list[$key]['product_featured_image'] = "{$this->product_featured_image}";
			$list[$key]['product_content'] = "{$this->product_content}";
			$list[$key]['product_variants'] = "{$this->product_variants}";
			$list[$key]['product_available'] = "{$this->product_available}";
			$list[$key]['product_selected_variant'] = "{$this->product_selected_variant}";
			$list[$key]['product_collections'] = "{$this->product_collections}";
			$list[$key]['product_options'] = "{$this->product_options}";
			$list[$key]['socialmedia_option1'] = "{$this->socialmedia_option1}";
			$list[$key]['socialmedia_option2'] =  "{$this->socialmedia_option2}";
			$list[$key]['socialmedia_option3'] =  "{$this->socialmedia_option3}";
			$list[$key]['variant_title1'] =  "{$this->variant_title1}";
			$list[$key]['variant_title2'] =  "{$this->variant_title2}";
			$list[$key]['variant_title3'] =  "{$this->variant_title3}";
			$list[$key]['variant_image1'] =  "{$this->variant_image1}";
			$list[$key]['variant_image2'] =  "{$this->variant_image2}";
			$list[$key]['variant_image3'] =  "{$this->variant_image3}";
			$list[$key]['variant_option1'] =  "{$this->variant_option1}";
			$list[$key]['variant_option2'] =  "{$this->variant_option2}";
			$list[$key]['variant_option3'] =  "{$this->variant_option3}";
			$list[$key]['variant_price1'] =  "{$this->variant_price1}";
			$list[$key]['variant_price2'] =  "{$this->variant_price2}";
			$list[$key]['variant_price3'] =  "{$this->variant_price3}";
			$list[$key]['shipping'] =  "{$this->shipping}";
			$list[$key]['shipping_fixed_price'] =  "{$this->shipping_fixed_price}";
			$list[$key]['shipping_flatfee'] =  "{$this->shipping_flatfee}";
			$list[$key]['shipping_locations'] =  "{$this->shipping_locations}";
			$list[$key]['payment_paypal_button_id'] =  "{$this->payment_paypal_button_id}";
			$list[$key]['payment_payment_button1'] =  "{$this->payment_payment_button1}";
			$list[$key]['payment_payment_button2'] =  "{$this->payment_payment_button2}";
			$list[$key]['payment_payment_button3'] =  "{$this->payment_payment_button3}";
			$list[$key]['payment_payment_array'] =  "{$this->shipping_fixed_price}";
			
			}
		}
		
		$list[$i] = $product;
		# var_dump($list); // debugging, to show list.
		$this->storeshop($list);
	}

	public function checkForm() 
	{
		isset($_POST['product_id'])  ? $this->product_id =  $this->cleanInput($_POST['product_id']) : $product_id = false; 
		isset($_POST['product_status'])  ? $this->product_status =  $this->cleanInput($_POST['product_status']) : $product_status = false; 
		isset($_POST['product_title'])  ? $this->product_title =  $this->cleanInput($_POST['product_title']) : $product_title = false; 
		isset($_POST['product_description'])  ? $this->product_description =  $this->cleanInput($_POST['product_description']) : $product_description = false; 
		isset($_POST['product_category'])  ? $this->product_category =  $this->cleanInput($_POST['product_category']) : $product_category = false; 
		isset($_POST['product_catno'])  ? $this->product_catno =  $this->cleanInput($_POST['product_catno']) : $product_catno = false; 
		isset($_POST['product_stock'])  ? $this->product_stock =  $this->cleanInput($_POST['product_stock']) : $product_stock = false; 
		isset($_POST['product_quantity'])  ? $this->product_quantity =  $this->cleanInput($_POST['product_quantity']) : $product_quantity = false; 
		isset($_POST['product_format'])  ? $this->product_format =  $this->cleanInput($_POST['product_format']) : $product_format = false; 
		isset($_POST['product_type'])  ? $this->product_type =  $this->cleanInput($_POST['product_type']) : $product_type = false; 
		isset($_POST['product_weight'])  ? $this->product_weight =  $this->cleanInput($_POST['product_weight']) : $product_weight = false; 
		isset($_POST['product_condition'])  ? $this->product_condition =  $this->cleanInput($_POST['product_condition']) : $product_condition = false; 
		isset($_POST['product_ean'])  ? $this->product_ean =  $this->cleanInput($_POST['product_ean']) : $product_ean = false; 
		isset($_POST['product_sku'])  ? $this->product_sku =  $this->cleanInput($_POST['product_sku']) : $product_sku = false; 
		isset($_POST['product_vendor'])  ? $this->product_vendor =  $this->cleanInput($_POST['product_vendor']) : $product_vendor = false; 
		isset($_POST['product_price'])  ? $this->product_price =  $this->cleanInput($_POST['product_price']) : $product_price = false; 
		isset($_POST['product_margin'])  ? $this->product_margin =  $this->cleanInput($_POST['product_margin']) : $product_margin = false; 
		isset($_POST['product_price_min'])  ? $this->product_price_min =  $this->cleanInput($_POST['product_price_min']) : $product_price_min = false; 
		isset($_POST['product_price_max'])  ? $this->product_price_max =  $this->cleanInput($_POST['product_price_max']) : $product_price_max = false; 
		isset($_POST['product_price_varies'])  ? $this->product_price_varies =  $this->cleanInput($_POST['product_price_varies']) : $product_price_varies = false; 
		isset($_POST['product_date'])  ? $this->product_date =  $this->cleanInput($_POST['product_date']) : $product_date = false; 
		isset($_POST['product_url'])  ? $this->product_url =  $this->cleanInput($_POST['product_url']) : $product_url = false; 
		isset($_POST['product_image'])  ? $this->product_image =  $this->cleanInput($_POST['product_image']) : $product_image = false; 
		isset($_POST['product_tags'])  ? $this->product_tags =  $this->cleanInput($_POST['product_tags']) : $product_tags = false; 
		isset($_POST['product_images'])  ? $this->product_images =  $this->cleanInput($_POST['product_images']) : $product_images = false; 
		isset($_POST['product_featured'])  ? $this->product_featured =  $this->cleanInput($_POST['product_featured']) : $product_featured = false; 
		isset($_POST['product_featured_location'])  ? $this->product_featured_location =  $this->cleanInput($_POST['product_featured_location']) : $product_featured_location = false; 
		isset($_POST['product_featured_carousel'])  ? $this->product_featured_carousel =  $this->cleanInput($_POST['product_featured_carousel']) : $product_featured_carousel = false; 
		isset($_POST['product_featured_image'])  ? $this->product_featured_image =  $this->cleanInput($_POST['product_featured_image']) : $product_featured_image = false; 
		isset($_POST['product_content'])  ? $this->product_content =  $this->cleanInput($_POST['product_content']) : $product_content = false; 
		isset($_POST['product_variants'])  ? $this->product_variants =  $this->cleanInput($_POST['product_variants']) : $product_variants = false; 
		isset($_POST['product_available'])  ? $this->product_available =  $this->cleanInput($_POST['product_available']) : $product_available = false; 
		isset($_POST['product_selected_variant'])  ? $this->product_selected_variant =  $this->cleanInput($_POST['product_selected_variant']) : $product_selected_variant = false; 
		isset($_POST['product_collections'])  ? $this->product_collections =  $this->cleanInput($_POST['product_collections']) : $product_collections = false; 
		isset($_POST['product_options'])  ? $this->product_options =  $this->cleanInput($_POST['product_options']) : $product_options = false; 
		isset($_POST['socialmedia_option1'])  ? $this->socialmedia_option1 =  $this->cleanInput($_POST['socialmedia_option1']) : $socialmedia_option1 = false; 
		isset($_POST['socialmedia_option2'])  ? $this->socialmedia_option2 =  $this->cleanInput($_POST['socialmedia_option2']) : $socialmedia_option2 = false; 
		isset($_POST['socialmedia_option3'])  ? $this->socialmedia_option3 =  $this->cleanInput($_POST['socialmedia_option3']) : $socialmedia_option3 = false; 
		isset($_POST['variant_title1'])  ? $this->variant_title1 =  $this->cleanInput($_POST['variant_title1']) : $variant_title1 = false; 
		isset($_POST['variant_title2'])  ? $this->variant_title2 =  $this->cleanInput($_POST['variant_title2']) : $variant_title2 = false; 
		isset($_POST['variant_title3'])  ? $this->variant_title3 =  $this->cleanInput($_POST['variant_title3']) : $variant_title3 = false; 
		isset($_POST['variant_image1'])  ? $this->variant_image1 =  $this->cleanInput($_POST['variant_image1']) : $variant_image1 = false; 
		isset($_POST['variant_image2'])  ? $this->variant_image2 =  $this->cleanInput($_POST['variant_image2']) : $variant_image2 = false; 
		isset($_POST['variant_image3'])  ? $this->variant_image3 =  $this->cleanInput($_POST['variant_image3']) : $variant_image3 = false; 
		isset($_POST['variant_option1'])  ? $this->variant_option1 =  $this->cleanInput($_POST['variant_option1']) : $variant_option1 = false; 
		isset($_POST['variant_option2'])  ? $this->variant_option2 =  $this->cleanInput($_POST['variant_option2']) : $variant_option2 = false; 
		isset($_POST['variant_option3'])  ? $this->variant_option3 =  $this->cleanInput($_POST['variant_option3']) : $variant_option3 = false; 
		isset($_POST['variant_price1'])  ? $this->variant_price1 =  $this->cleanInput($_POST['variant_price1']) : $variant_price1 = false; 
		isset($_POST['variant_price2'])  ? $this->variant_price2 =  $this->cleanInput($_POST['variant_price2']) : $variant_price2 = false; 
		isset($_POST['variant_price3'])  ? $this->variant_price3 =  $this->cleanInput($_POST['variant_price3']) : $variant_price3 = false; 
		isset($_POST['shipping'])  ? $this->shipping =  $this->cleanInput($_POST['shipping']) : $shipping = false; 
		isset($_POST['shipping_fixed_price'])  ? $this->shipping_fixed_price =  $this->cleanInput($_POST['shipping_fixed_price']) : $shipping_fixed_price = false; 
		isset($_POST['shipping_flatfee'])  ? $this->shipping_flatfee =  $this->cleanInput($_POST['shipping_flatfee']) : $shipping_flatfee = false; 
		isset($_POST['shipping_locations'])  ? $this->shipping_locations =  $this->cleanInput($_POST['shipping_locations']) : $shipping_locations = false; 
		isset($_POST['payment_paypal_button_id'])  ? $this->payment_paypal_button_id =  $this->cleanInput($_POST['payment_paypal_button_id']) : $payment_paypal_button_id = false; 
		isset($_POST['payment_payment_button1'])  ? $this->payment_payment_button1 =  $this->cleanInput($_POST['payment_payment_button1']) : $payment_payment_button1 = false; 
		isset($_POST['payment_payment_button2'])  ? $this->payment_payment_button2 =  $this->cleanInput($_POST['payment_payment_button2']) : $payment_payment_button2 = false; 
		isset($_POST['payment_payment_button3'])  ? $this->payment_payment_button3 =  $this->cleanInput($_POST['payment_payment_button3']) : $payment_payment_button3 = false; 
		isset($_POST['payment_payment_array']) ? $this->payment_payment_array =  $this->cleanInput($_POST['payment_payment_array']) : $payment_payment_array = false; 

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
		$copy 	= self::SHOP.self::BACKUPEXT;
		@copy($file, $copy);
		// convert encoding
		$json = mb_convert_encoding($this->encode($shop), self::FILE_ENC, self::FILE_OS);
		// write file.
		file_put_contents(self::SHOP,$json, LOCK_EX);
	}

	/**
	* Store shop into SHOP
	* @param array $shop
	* @return boolean, true for success, false for failure.
	*/
	public function storefile($shop) 
	{
		// make a backup before doing anything.
		$file 	= 'test.json';
		$copy 	= 'test.json.bak';
		@copy($file, $copy);
		// convert encoding
		$json = mb_convert_encoding($this->encode($shop), self::FILE_ENC, self::FILE_OS);
		// write file.
		file_put_contents($file,$json, LOCK_EX);
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
			$libraylist = usort($lijst, $this->sortNatural('id'));
			$shops = array();
			foreach($lijst as $c) {	
				echo $shop."<br>";
				if($c['id'] != $shop) {
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
