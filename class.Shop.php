<?php

// if possible, store this class below the www or html folder for more security.

class Shop {

	CONST DOMAIN				= 'https://www.example.com'; 
	CONST SHOPURI				= 'shop'; // path to the shop, without domain and trailing slash .i.e. : www.example.com/shop/ will be: shop
	CONST SHOP				= "./inventory/shop.json";
	CONST CSV				= "./inventory/csv/shop.csv"; 
	CONST BACKUPEXT				= ".bak"; 
	CONST PWD				= "Password to encrypt JSON"; // optional.
	CONST FILE_ENC				= "UTF-8";
	CONST FILE_OS				= "WINDOWS-1252"; 
	CONST MAIN_PAYMENT_METHOD		= 'PayPal'; // Tiny Store uses PayPal as default payment gateway.
	CONST PAYMENTGATEWAY			= ''; 	// Only required for 3rd party payment processing.
	CONST DEPTH				= 1024;
	CONST MAXWEIGHT				= 10000;
	CONST MAXTITLE				= 255; // Max length of title.
	CONST MAXDESCRIPTION			= 500; // Max length of description.
	CONST CURRENCY				= "&#163;";   // for a list, see currencies.json.

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
		if(is_array($string)) {
			return @array_map("htmlspecialchars", $string, array(ENT_QUOTES, 'UTF-8'));
			} else {
			return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
		}
	}
	/**
	* SEO-ing URL.
	* @param string
	* @return string
	*/
	public function seoUrl($string) 
	{
		$find 		= [' ','_','=','+','&','.'];
		$replace 	= ['-','-','-','-','-','-'];
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
	
	public function load_json($url) 
	{
		return json_decode(file_get_contents($url), true, self::DEPTH, JSON_BIGINT_AS_STRING);
	}

	public function addshop() 
	{
		$newshop = $this->products();
		$lijst = $this->decode();
		$i = count($lijst);
		$lijst = array($newshop);

		$this->storeshop($lijst);
	}

	public function message($value) 
	{
		if(isset($_SESSION['messages'])) { 
			array_push($_SESSION['messages'],$value);  
			} else { 
			$_SESSION['messages'] = array(); 
		} 	
	}

	public function debug($rawdata) 
	{
		$string = "<pre>";
		$string .= print_r($rawdata);
		$string .= "</pre>";
		return $string;
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
	* Meta generation
	* @return $string, html.
	*/	
	public function getmeta($json="inventory/site.json") {
		
		$html = '';
		
		$site = $this->load_json($json);

		foreach($site as $row)
		{
			if($row['site.status'] == 'offline') {
				header('Location: /offline/', true, 302);
				exit;
			}
			
			if($row['site.status'] == 'closed') {
				header('Location: /closed/', true, 301);
				exit;			
			}
			
			if(isset($row['site.cdn'])) {
				$cdn = $this->cleanInput($row['site.cdn']);
			}

			$html .= '<title>'.$this->cleanInput($row['site.title']).'</title>';
			$html .= '<meta charset="'.$this->cleanInput($row['site.charset']).'">';
			$html .= '<meta name="viewport" content="'.$this->cleanInput($row['site.viewport']).'">';
			$html .= '<meta name="description" content="'.$this->cleanInput($row['site.description']).'">';
			$html .= '<meta name="author" content="TinyShop">';
			
			if(!empty($row['site.updated'])) {
				$html .= '<meta http-equiv="last-modified" content="'.$this->cleanInput($row['site.updated']).'">';
			}			

			if(!empty($row['site.meta.name.1'])) {
				$html .= '<meta name="'.$this->cleanInput($row['site.meta.name.1']).'" content="'.$this->cleanInput($row['site.meta.value.1']).'">';
			}

			if(!empty($row['site.meta.name.2'])) {
				$html .= '<meta name="'.$this->cleanInput($row['site.meta.name.2']).'" content="'.$this->cleanInput($row['site.meta.value.2']).'">';
			}

			if(!empty($row['site.meta.name.3'])) {
				$html .= '<meta name="'.$this->cleanInput($row['site.meta.name.3']).'" content="'.$this->cleanInput($row['site.meta.value.3']).'">';
			}

			if(!empty($row['site.meta.name.4'])) {
				$html .= '<meta name="'.$this->cleanInput($row['site.meta.name.4']).'" content="'.$this->cleanInput($row['site.meta.value.4']).'">';
			}

			if(!empty($row['site.google.tags'])) {
				$html .= '<meta name="google-site-verification" content="'.$this->cleanInput($row['site.google.tags']).'">';
			}


			$html .= '<link rel="stylesheet" type="text/css" href="'.self::DOMAIN.'/'.self::SHOPURI.'/'.$this->cleanInput($row['site.stylesheet.reset']).'">';
			$html .= '<link rel="stylesheet" type="text/css" href="'.self::DOMAIN.'/'.self::SHOPURI.'/'.$this->cleanInput($row['site.stylesheet']).'">';
			
			if(!empty($row['site.ext.stylesheet'])) {
				$html .= '<link rel="stylesheet" type="text/css" href="'.$this->cleanInput($row['site.ext.stylesheet']).'">';
			}		
			
			$html .= '<link rel="icon" type="image/ico" href="'.self::DOMAIN.'/'.self::SHOPURI.'/'.$this->cleanInput($row['site.icon']).'">';
			$html .= '<script src="'.self::DOMAIN.'/'.self::SHOPURI.'/'.$this->cleanInput($row['site.javascript']).'" type="text/javascript"></script>';
			
			if(!empty($row['site.ext.javascript'])) {
				$html .= '<script src="'.$this->cleanInput($row['site.ext.javascript']).'" type="text/javascript"></script>';
			}					
			
			$html .= '<img src="'.self::DOMAIN.'/'.self::SHOPURI.'/'.$this->cleanInput($row['site.logo']).'" width="115" id="ts.shop.logo">';
		}
		
		return $html;
	}

	/**
	* Paginate function
	* @param int $page
	* @return $string, html, false for failure.
	*/	

	public function paginate($page) 
	{

		if(!is_numeric($page)) {
			$this->message('Pagination error: page value is not numeric, could not paginate.');
			return false;
		}
		
		// Cast to integer, for security.
		$p = (int)$page;

		$total = 100;
		$limit = 20;
		$ps = ceil($total / $limit);

		if($p <= 0) {
			$p = 1;
		}
		
		$offset = ($p - 1)  * $limit;
		$start  = $offset + 1;
		$end    = min(($offset + $limit), $total);
		
		$uri = self::DOMAIN.self::SHOPURI;
		
		$prevlink = ($p > 1) ? '<a href="'.$uri.'/1/" title="First page">&laquo;</a> <a href="'.$uri.'/' . ($p - 1) . '/" title="Previous page" class="ts.pagination.link">&lsaquo;</a>' : '<span class="ts.disabled.span">&laquo;</span> <span class="ts.disabled.span">&lsaquo;</span>';
		$nextlink = ($p < $ps) ? '<a href="'.$uri.'/' . ($p + 1) . '/" title="Next page" class="ts.pagination.link">&rsaquo;</a> <a href="'.$uri.'/' . $ps . '/" title="Last page" class="ts.pagination.link">&raquo;</a>' : '<span class="ts.disabled.span">&rsaquo;</span> <span class="ts.disabled.span">&raquo;</span>';

		return '<div id="ts.pagination">'. $prevlink. ' Page '.$p. ' of ' .$ps. ' pages, showing '.$start. '-'.$end. ' of '.$total.' results '. $nextlink. ' </div>';
	}


	/**
	* Returns a product list, by reading shop.json.
	* @param method: list|group.	
	* @param string: custom html can be added.
	* @param category: select shop category, if none is given it will list all products.
	* @return $string, html or array (if method is requested.)
	*/		
	public function getproducts($method,$category,$string=false) 
	{
		
		isset($string) ? $this->$string = $string : $string = false;
		isset($category) ? $this->$category = $category : $category = false;
		isset($page_id) ? $this->page_id = (int)$_GET['page_id'] : $this->page_id = 1;
	
		// Loading the shop configuration.
		$shopconf = $this->load_json("inventory/shop.conf.json");
		$configuration = [];
		
		if($shopconf !== null) {
			foreach($shopconf as $conf) {	
				array_push($configuration,$conf);
			}
		}
		
		// carousel selection.
		if($configuration[0]['products.carousel'] == 1 && $category == 'index') {
			$carousel = true;
		}
		
		/* 
			$configuration['products.orientation'] : "thumb"
			$configuration['products.alt.tags']    : "no"
			$configuration['products.scene.type']  : "box"
			$configuration['products.row.count']   : 10
			$configuration['products.per.page']    : 25		
			$configuration['products.per.cat']     : 25
		*/
		
		$productlist = $this->decode();

		$string .= "<div id=\"ts.product\">";

		if($productlist !== null) {

			$shoplist = $productlist;
			$ts 	  = array(); 
			$i 		  = count($ts)-1;
		
			foreach($productlist as $c) {	
			
				if($i >= $configuration[0]['products.per.page']) {
					$this->message('Too many products in category, try to generate pagination.');
					// todo: pagination
					$prc = $configuration[0]['products.row.count'];	
					$ppp = $configuration[0]['products.per.page'];		
					$ppc = $configuration[0]['products.per.cat'];
		
				} else {
					if($category != false) {
						if($c['product.category'] == $category) {
							array_push($ts,$c);
						}
					} else {
						array_push($ts,$c);
					}
				
					$this->cleanInput($c['product.title']);
					$i++;
				}
			}
			
			if($method == 'array') {
				return $ts;
				exit;
			}

			if($i >= 0) { 
			
				while($i >= 0) {
					
					if($ts[$i]['product.stock'] < 1) {
						$status = 'ts.product.status.red'; // low stock
						} else {
						$status = 'ts.product.status.green';
					}
					
					if($ts[$i]['product.image'] != "") {
						$productimage = '<div class="ts-product-image-div"><img src="'.$this->cleanInput($ts[$i]['product.image']).'" class="ts-product-image"/></div>';
						} else {
						$productimage = '<div class="ts-product-image-icon">&#128722;</div>';
					}				
					
					switch($method) {
						
						case 'list':		
						$string .= "<div class=\"ts-product-list\">";
						// $string .= $productimage;
						$string .= "<div class=\"ts-list-product-status\"><div class=\"".$status."\">".$this->cleanInput($ts[$i]['product.status'])."</div>";
						$string .= "<div class=\"ts-list-product-price\">".self::CURRENCY.' '.$this->cleanInput($ts[$i]['product.price'])."</div>";
						$string .= "<div class=\"ts-list-product-link\"><a href=\"item/".$this->seoUrl($this->cleanInput($ts[$i]['product.category'])).'/'.$this->seoUrl($this->cleanInput($ts[$i]['product.title'])).'/'.$this->cleanInput($ts[$i]['product.id'])."/".(int)$this->page_id."/\">".$this->cleanInput($ts[$i]['product.title'])."</a> </div>";
						$string .= "<div class=\"ts-list-product-desc\">".$this->cleanInput($ts[$i]['product.description'])."</div>";
						$string .= "<div class=\"ts-list-product-cat\">".$this->cleanInput($ts[$i]['product.category'])."</div>";
						
						if($configuration[0]['products.quick.cart'] == 'yes') {
							$string .= "<div><input type='button' onclick='tinyshop.addtocart(\"".$ts[$i]['product.id']."\");' class='ts-list-cart-button' name='add_cart' value='".$this->cleanInput($configuration[0]['products.cart.button'])."' /></div>";
							} else {
							$string .= "<div class='ts-list-view-link'><a href=\"product/".$this->cleanInput($ts[$i]['product.id'])."/\">view</a></div>";
						}
						
						$string .= "</div>";
						break;
						
						case 'group':		
						$string .= "<div class=\"ts-product-group\">";
						$string .= $productimage;
						$string .= "<div class=\"ts-group-product-status\"><div class=\"".$status."\">".$this->cleanInput($ts[$i]['product.status'])."</div>";
						$string .= "<div class=\"ts-group-product-price\">".self::CURRENCY.' '.$this->cleanInput($ts[$i]['product.price'])."</div>";
						$string .= "<div class=\"ts-group-product-link\"><a href=\"item/".$this->seoUrl($this->cleanInput($ts[$i]['product.category'])).'/'.$this->seoUrl($this->cleanInput($ts[$i]['product.title'])).'/'.$this->cleanInput($ts[$i]['product.id'])."/\">".$this->cleanInput($ts[$i]['product.title'])."</a> </div>";
						$string .= "<div class=\"ts-group-product-desc\">".$this->cleanInput($ts[$i]['product.description'])."</div>";
						$string .= "<div class=\"ts-group-product-cat\">".$this->cleanInput($ts[$i]['product.category'])."</div>";
						
						if($configuration[0]['products.quick.cart'] == 'yes') {
							$string .= "<div><input type='button' onclick='tinyshop.addtocart(\"".$ts[$i]['product.id']."\");' class='ts-group-cart-button' name='add_cart' value='".$this->cleanInput($configuration[0]['products.cart.button'])."' /></div>";
							} else {
							$string .= "<div class='ts-group-view-link'><a href=\"product/".$this->cleanInput($ts[$i]['product.id'])."/\">view</a></div>";
						}
						
						$string .= "</div>";
						break;
					}
				$i--;
				}
			}
					
		}

		$string .= "</div>";		
		
		return $string;
	}
	
	public function getpagelist($json,$method) {

		$html = '';
		
		switch($method) {
			
			case 'blog':
			if(!isset($json)) {
				$json = "inventory/blog.json";
			}
			$css = 'blog';
			break;
			
			case 'articles':
			if(!isset($json)) {
				$json = "inventory/articles.json";
			}
			$css = 'articles';
			break;

			case 'pages':
			if(!isset($json)) {
				$json = "inventory/pages.json";
			}
			$css = 'pages';
			break;			
		}
		
		$shopconf = $this->load_json($json);

		foreach($shopconf as $row)
		{	
			$html .= '<div class="ts-shop-'.$css.'-item">';
			foreach($row as $key => $value)
			{
				$html .='<b>'.$key.'</b>:'.$value.'<br>';
			}
			$html .= '</div>';
		}
		return $html;
	}

	/**
	* Showing session messages.
	* @return mixed object/array
	*/	
	public function getcart($json="inventory/cart.json") 
	{ 
		
		$array = [];
		
		$shopconf = $this->load_json($json);
	
		if(isset($_SESSION['cart'])) { 
			
			foreach($_SESSION['cart'] as $item) { 
			
				foreach($row as $key => $value)
				{
					array_push($array,$this->cleanInput($item));
				}
			} 
			
		}  else {
			$_SESSION['cart'] = array();
		}
		
		return $array;
	} 	
	
	
	public function gatewaylist($json,$keys) 
	{
		$html = "";
			if($json !== null) {
				foreach($json[0][$keys] as $key => $value)
				{
					$html.= "<option value=\"".$value."\">".$value."</option>";			
				}		
			}
		return $html;
	}

	public function generatecart($json,$split,$ignore) 
	{
			if($json !== null) {

				$i = 0;
				$html = "";
				
				foreach($json as $row)
				{
					foreach($row as $key => $value)
					{
						if(!in_array($key,$ignore)) {
							
							$key = str_replace(['.','customer'],['',''],$key);
							$keycss = str_replace('.','-',$key);
							
							if($key == 'newsletter') {
								$html .= "<label>".ucfirst($key)."</label>";
								$html .="<input type=\"checkbox\" id=\"".$keycss."\" name=\"".$key."\">";	
								} else {
								$html .= "<label>".ucfirst($key)."</label>";
								$html .= "<input type=\"text\" id=\"".$keycss."\" name=\"".$key."\">";
							}
							
							$i++;
						}
						
						if($i == $split) {
							$html .= "</div>";
							$html .= "<div class=\"ts-shop-form-field\">";
						}
					}
				}
			}
			return $html;
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
		
	public function arrays($array) 
	{

		switch($array) {
			
		case 'articles':
		$arraylist = array(
				"article_id" => "{$this->article_id}",
				"article_title" => "{$this->article_title }",
				"article_description" => "{$this->article_description}",
				"article_short_text" => "{$this->article_short_text }",
				"article_long_text" => "{$this->article_long_text }",
				"article_url" => "{$this->article_url }",
				"article_tags" => "{$this->article_tags }",
				"article_author" => "{$this->article_author }",
				"article_handle" => "{$this->article_handle }",
				"article_created" => "{$this->article_created }",
				"article_published" => "{$this->article_published }",
				"article_image_header" => "{$this->article_image_header }",
				"article_image_main" => "{$this->article_image_main }",
				"article_status" => "{$this->article_status }",
				"article_archived" => "{$this->article_archived }"
		);
		break;

		case 'blog':
		$arraylist = array(
				"blog_id" => "{$this->blog_id }",
				"blog_title" => "{$this->blog_title }",
				"blog_description" => "{$this->blog_description }",
				"blog_short_text" => "{$this->blog_short_text }",
				"blog_long_text" => "{$this->blog_long_text }",
				"blog_url" => "{$this->blog_url }",
				"blog_tags" => "{$this->blog_tags }",
				"blog_author" => "{$this->blog_author }",
				"blog_handle" => "{$this->blog_handle }",
				"blog_created" => "{$this->blog_created }",
				"blog_published" => "{$this->blog_published }",
				"blog_image_header" => "{$this->blog_image_header }",
				"blog_image_main" => "{$this->blog_image_main }",
				"blog_status" => "{$this->blog_status }",
				"blog_archived" => "{$this->blog_archived }"
		);
		break;

		case 'cart':
		$arraylist = array(
				"cart_id" => "{$this->cart_id }",
				"cart_customer_id" => "{$this->cart_customer_id }",
				"cart_creation_date" => "{$this->cart_creation_date }",
				"cart_data" => "{$this->cart_data }",
				"cart_sum" => "{$this->cart_sum }",
				"cart_tax" => "{$this->cart_tax }",		
				"cart_product_list" => "{$this->cart_product_list }",
				"cart_checkout_status" => "{$this->cart_checkout_status }",
				"cart_checkout_discount" => "{$this->cart_checkout_discount }",
				"cart_session_id" => "{$this->cart_session_id }",
				"cart_session_attempts" => "{$this->cart_session_attempts }",
				"cart_diff" => "{$this->cart_diff }"
		);
		break;

		case 'customer':
		$arraylist = array(
				"customer_id" => "{$this->customer_id }",
				"customer_attn" => "{$this->customer_attn }",
				"customer_first_name" => "{$this->customer_first_name }",
				"customer_last_name" => "{$this->customer_last_name }",
				"customer_address" => "{$this->customer_address }",
				"customer_address_number" => "{$this->customer_address_number }",
				"customer_postalcode" => "{$this->customer_postalcode }",
				"customer_region" => "{$this->customer_region }",
				"customer_city" => "{$this->customer_city }",
				"customer_country" => "{$this->customer_country }",
				"customer_password" => "{$this->customer_password }",
				"customer_hash" => "{$this->customer_hash }",
				"customer_email" => "{$this->customer_email }",
				"customer_newsletter" => "{$this->customer_newsletter }",
				"customer_signup_date" => "{$this->customer_signup_date }",
				"customer_signup_ip" => "{$this->customer_signup_ip }",
				"customer_signup_ua" => "{$this->customer_signup_ua }",
				"customer_diff" => "{$this->customer_diff }"
		);
		break;

		case 'orders':
		$arraylist = array(
				"orders_id" => "{$this->orders_id }",
				"orders_customer_id" => "{$this->orders_customer_id }",
				"orders_product_list" => "{$this->orders_product_list }",
				"orders_creation_date" => "{$this->orders_creation_date }",
				"orders_data" => "{$this->orders_data }",
				"orders_sum" => "{$this->orders_sum }",
				"orders_tax" => "{$this->orders_tax }",	
				"orders_customer_email" => "{$this->orders_customer_email }",		
				"orders_delivered" => "{$this->orders_delivered }",
				"orders_refunded" => "{$this->orders_refunded }",
				"orders_discount" => "{$this->orders_discount }",
				"orders_voucher" => "{$this->orders_voucher }",
				"orders_checkout_method" => "{$this->orders_checkout_method }",
				"orders_checkout_payment" => "{$this->orders_checkout_payment }",
				"orders_checkout_status" => "{$this->orders_checkout_status }",
				"orders_checkout_discount" => "{$this->orders_checkout_discount }",
				"orders_checkout_success" => "{$this->orders_checkout_success }",
				"orders_session_id" => "{$this->orders_session_id }",
				"orders_session_ip" => "{$this->orders_session_ip }",
				"orders_session_ua" => "{$this->orders_session_ua }",
				"orders_session_attempts" => "{$this->orders_session_attempts }",
				"orders_diff" => "{$this->orders_diff }"
		);
		break;
		
		case 'page':
		$arraylist = array(
				"page_id" => "{$this->page_id }",
				"page_title" => "{$this->page_title }",
				"page_description" => "{$this->page_description }",
				"page_short_text" => "{$this->page_short_text }",
				"page_long_text" => "{$this->page_long_text }",
				"page_url" => "{$this->page_url }",
				"page_tags" => "{$this->page_tags }",
				"page_image_header" => "{$this->page_image_header }",
				"page_image_main" => "{$this->page_image_main }",
				"page_image_left" => "{$this->page_image_left }",
				"page_image_right" => "{$this->page_image_right }",
				"page_status" => "{$this->page_status }",
				"page_archived" => "{$this->page_archived }",
				"page_created" => "{$this->page_created }",
				"page_published" => "{$this->page_published }",
				"page_updated" => "{$this->page_updated }",
				"page_meta_title" => "{$this->page_meta_title }",
				"page_meta_description" => "{$this->page_meta_description }",
				"page_meta_tags" => "{$this->page_meta_tags }"
		);
		break;
		
		case 'site':
		$arraylist = array(
				"site_url" => "{$this->site_url }",
				"site_domain" => "{$this->site_domain }",
				"site_canonical" => "{$this->site_canonical }",
				"site_cdn" => "{$this->site_cdn }",
				"site_charset" => "{$this->charset }",
				"site_title" => "{$this->site_title }",
				"site_description" => "{$this->site_description }",
				"site_logo" => "{$this->site_logo }",
				"site_icon" => "{$this->site_icon }",
				"site_status" => "{$this->site_status }",
				"site_updated" => "{$this->site_updated }",		
				"site_currency" => "{$this->site_currency }",		
				"site_meta_title" => "{$this->site_meta_title }",
				"site_meta_description" => "{$this->site_meta_description }",
				"site_meta_tags" => "{$this->site_meta_tags }",
				"site_meta_name_1" => "{$this->site_meta_name_1 }",
				"site_meta_name_2" => "{$this->site_meta_name_2 }",
				"site_meta_name_3" => "{$this->site_meta_name_3 }",
				"site_meta_name_4" => "{$this->site_meta_name_4 }",
				"site_meta_value_1" => "{$this->site_meta_value_1 }",
				"site_meta_value_2" => "{$this->site_meta_value_2 }",
				"site_meta_value_3" => "{$this->site_meta_value_3 }",
				"site_meta_value_4" => "{$this->site_meta_value_4 }",
				"site_tags" => "{$this->site_tags }",
				"site_socialmedia_option1" => "{$this->site_socialmedia_option1 }",
				"site_socialmedia_option2" => "{$this->site_socialmedia_option2 }",
				"site_socialmedia_option3" => "{$this->site_socialmedia_option3 }",
				"site_socialmedia_option4" => "{$this->site_socialmedia_option4 }",
				"site_socialmedia_option5" => "{$this->site_socialmedia_option5 }",
				"site_javascript" => "{$this->site_javascript }",
				"site_ext_javascript" => "{$this->site_ext_javascript }",
				"site_stylesheet" => "{$this->site_stylesheet }",
				"site_ext_stylesheet" => "{$this->site_ext_stylesheet }",
				"site_google_tags" => "{$this->site_google_tags }",
				"site_cookie_name_1" => "{$this->site_cookie_name_1 }",
				"site_cookie_name_2" => "{$this->site_cookie_name_2 }",
				"site_cookie_name_3" => "{$this->site_cookie_name_3 }",
				"site_cookie_value_1" => "{$this->site_cookie_value_1 }",
				"site_cookie_value_2" => "{$this->site_cookie_value_2 }",
				"site_cookie_value_3" => "{$this->site_cookie_value_3 }",		
				"site_analytics" => "{$this->site_analytics }"
			);
			break;

		}

		return $arraylist;
	}
	
	
	public function init_post_arrays($array) 
	{

		switch($array) {
			
		case 'articles':
		
				 isset($_POST['article_id']) ? $this->article_id = $this->cleanInput($_POST['article_id']) : $article_id = false;  
				 isset($_POST['article_title']) ? $this->article_title = $this->cleanInput($_POST['article_title']) : $article_title = false;  
				 isset($_POST['article_description']) ? $this->article_description = $this->cleanInput($_POST['article_description']) : $article_description = false;  
				 isset($_POST['article_short_text']) ? $this->article_short_text = $this->cleanInput($_POST['article_short_text']) : $article_short_text = false;  
				 isset($_POST['article_long_text']) ? $this->article_long_text = $this->cleanInput($_POST['article_long_text']) : $article_long_text = false;  
				 isset($_POST['article_url']) ? $this->article_url = $this->cleanInput($_POST['article_url']) : $article_url = false;  
				 isset($_POST['article_tags']) ? $this->article_tags = $this->cleanInput($_POST['article_tags']) : $article_tags = false;  
				 isset($_POST['article_author']) ? $this->article_author = $this->cleanInput($_POST['article_author']) : $article_author = false;  
				 isset($_POST['article_handle']) ? $this->article_handle = $this->cleanInput($_POST['article_handle']) : $article_handle = false;  
				 isset($_POST['article_created']) ? $this->article_created = $this->cleanInput($_POST['article_created']) : $article_created = false;  
				 isset($_POST['article_published']) ? $this->article_published = $this->cleanInput($_POST['article_published']) : $article_published = false;  
				 isset($_POST['article_image_header']) ? $this->article_image_header = $this->cleanInput($_POST['article_image_header']) : $article_image_header = false;  
				 isset($_POST['article_image_main']) ? $this->article_image_main = $this->cleanInput($_POST['article_image_main']) : $article_image_main = false;  
				 isset($_POST['article_status']) ? $this->article_status = $this->cleanInput($_POST['article_status']) : $article_status = false;  
				 isset($_POST['article_archived']) ? $this->article_archived = $this->cleanInput($_POST['article_archived']) : $article_archived = false;  
		break;

		case 'blog':
		
				 isset($_POST['blog_id']) ? $this->blog_id = $this->cleanInput($_POST['blog_id']) : $blog_id = false;  
				 isset($_POST['blog_title']) ? $this->blog_title = $this->cleanInput($_POST['blog_title']) : $blog_title = false;  
				 isset($_POST['blog_description']) ? $this->blog_description = $this->cleanInput($_POST['blog_description']) : $blog_description = false;  
				 isset($_POST['blog_short_text']) ? $this->blog_short_text = $this->cleanInput($_POST['blog_short_text']) : $blog_short_text = false;  
				 isset($_POST['blog_long_text']) ? $this->blog_long_text = $this->cleanInput($_POST['blog_long_text']) : $blog_long_text = false;  
				 isset($_POST['blog_url']) ? $this->blog_url = $this->cleanInput($_POST['blog_url']) : $blog_url = false;  
				 isset($_POST['blog_tags']) ? $this->blog_tags = $this->cleanInput($_POST['blog_tags']) : $blog_tags = false;  
				 isset($_POST['blog_author']) ? $this->blog_author = $this->cleanInput($_POST['blog_author']) : $blog_author = false;  
				 isset($_POST['blog_handle']) ? $this->blog_handle = $this->cleanInput($_POST['blog_handle']) : $blog_handle = false;  
				 isset($_POST['blog_created']) ? $this->blog_created = $this->cleanInput($_POST['blog_created']) : $blog_created = false;  
				 isset($_POST['blog_published']) ? $this->blog_published = $this->cleanInput($_POST['blog_published']) : $blog_published = false;  
				 isset($_POST['blog_image_header']) ? $this->blog_image_header = $this->cleanInput($_POST['blog_image_header']) : $blog_image_header = false;  
				 isset($_POST['blog_image_main']) ? $this->blog_image_main = $this->cleanInput($_POST['blog_image_main']) : $blog_image_main = false;  
				 isset($_POST['blog_status']) ? $this->blog_status = $this->cleanInput($_POST['blog_status']) : $blog_status = false;  
				 isset($_POST['blog_archived']) ? $this->blog_archived = $this->cleanInput($_POST['blog_archived']) : $blog_archived = false;  
		
		break;

		case 'cart':
		
				 isset($_POST['cart_id']) ? $this->cart_id = $this->cleanInput($_POST['cart_id']) : $cart_id = false;  
				 isset($_POST['cart_customer_id']) ? $this->cart_customer_id = $this->cleanInput($_POST['cart_customer_id']) : $cart_customer_id = false;  
				 isset($_POST['cart_creation_date']) ? $this->cart_creation_date = $this->cleanInput($_POST['cart_creation_date']) : $cart_creation_date = false;  
				 isset($_POST['cart_data']) ? $this->cart_data = $this->cleanInput($_POST['cart_data']) : $cart_data = false;  
				 isset($_POST['cart_sum']) ? $this->cart_sum = $this->cleanInput($_POST['cart_sum']) : $cart_sum = false;  
				 isset($_POST['cart_tax']) ? $this->cart_tax = $this->cleanInput($_POST['cart_tax']) : $cart_tax = false;  		
				 isset($_POST['cart_product_list']) ? $this->cart_product_list = $this->cleanInput($_POST['cart_product_list']) : $cart_product_list = false;  
				 isset($_POST['cart_checkout_status']) ? $this->cart_checkout_status = $this->cleanInput($_POST['cart_checkout_status']) : $cart_checkout_status = false;  
				 isset($_POST['cart_checkout_discount']) ? $this->cart_checkout_discount = $this->cleanInput($_POST['cart_checkout_discount']) : $cart_checkout_discount = false;  
				 isset($_POST['cart_session_id']) ? $this->cart_session_id = $this->cleanInput($_POST['cart_session_id']) : $cart_session_id = false;  
				 isset($_POST['cart_session_attempts']) ? $this->cart_session_attempts = $this->cleanInput($_POST['cart_session_attempts']) : $cart_session_attempts = false;  
				 isset($_POST['cart_diff']) ? $this->cart_diff = $this->cleanInput($_POST['cart_diff']) : $cart_diff = false;  
		break;

		case 'customer':
				 isset($_POST['customer_id']) ? $this->customer_id = $this->cleanInput($_POST['customer_id']) : $customer_id = false;  
				 isset($_POST['customer_attn']) ? $this->customer_attn = $this->cleanInput($_POST['customer_attn']) : $customer_attn = false;  
				 isset($_POST['customer_first_name']) ? $this->customer_first_name = $this->cleanInput($_POST['customer_first_name']) : $customer_first_name = false;  
				 isset($_POST['customer_last_name']) ? $this->customer_last_name = $this->cleanInput($_POST['customer_last_name']) : $customer_last_name = false;  
				 isset($_POST['customer_address']) ? $this->customer_address = $this->cleanInput($_POST['customer_address']) : $customer_address = false;  
				 isset($_POST['customer_address_number']) ? $this->customer_address_number = $this->cleanInput($_POST['customer_address_number']) : $customer_address_number = false;  
				 isset($_POST['customer_postalcode']) ? $this->customer_postalcode = $this->cleanInput($_POST['customer_postalcode']) : $customer_postalcode = false;  
				 isset($_POST['customer_region']) ? $this->customer_region = $this->cleanInput($_POST['customer_region']) : $customer_region = false;  
				 isset($_POST['customer_city']) ? $this->customer_city = $this->cleanInput($_POST['customer_city']) : $customer_city = false;  
				 isset($_POST['customer_country']) ? $this->customer_country = $this->cleanInput($_POST['customer_country']) : $customer_country = false;  
				 isset($_POST['customer_password']) ? $this->customer_password = $this->cleanInput($_POST['customer_password']) : $customer_password = false;  
				 isset($_POST['customer_hash']) ? $this->customer_hash = $this->cleanInput($_POST['customer_hash']) : $customer_hash = false;  
				 isset($_POST['customer_email']) ? $this->customer_email = $this->cleanInput($_POST['customer_email']) : $customer_email = false;  
				 isset($_POST['customer_newsletter']) ? $this->customer_newsletter = $this->cleanInput($_POST['customer_newsletter']) : $customer_newsletter = false;  
				 isset($_POST['customer_signup_date']) ? $this->customer_signup_date = $this->cleanInput($_POST['customer_signup_date']) : $customer_signup_date = false;  
				 isset($_POST['customer_signup_ip']) ? $this->customer_signup_ip = $this->cleanInput($_POST['customer_signup_ip']) : $customer_signup_ip = false;  
				 isset($_POST['customer_signup_ua']) ? $this->customer_signup_ua = $this->cleanInput($_POST['customer_signup_ua']) : $customer_signup_ua = false;  
				 isset($_POST['customer_diff']) ? $this->customer_diff = $this->cleanInput($_POST['customer_diff']) : $customer_diff = false;  
		break;

		case 'orders':
				 isset($_POST['orders_id']) ? $this->orders_id = $this->cleanInput($_POST['orders_id']) : $orders_id = false;  
				 isset($_POST['orders_customer_id']) ? $this->orders_customer_id = $this->cleanInput($_POST['orders_customer_id']) : $orders_customer_id = false;  
				 isset($_POST['orders_product_list']) ? $this->orders_product_list = $this->cleanInput($_POST['orders_product_list']) : $orders_product_list = false;  
				 isset($_POST['orders_creation_date']) ? $this->orders_creation_date = $this->cleanInput($_POST['orders_creation_date']) : $orders_creation_date = false;  
				 isset($_POST['orders_data']) ? $this->orders_data = $this->cleanInput($_POST['orders_data']) : $orders_data = false;  
				 isset($_POST['orders_sum']) ? $this->orders_sum = $this->cleanInput($_POST['orders_sum']) : $orders_sum = false;  
				 isset($_POST['orders_tax']) ? $this->orders_tax = $this->cleanInput($_POST['orders_tax']) : $orders_tax = false;  	
				 isset($_POST['orders_customer_email']) ? $this->orders_customer_email = $this->cleanInput($_POST['orders_customer_email']) : $orders_customer_email = false;  		
				 isset($_POST['orders_delivered']) ? $this->orders_delivered = $this->cleanInput($_POST['orders_delivered']) : $orders_delivered = false;  
				 isset($_POST['orders_refunded']) ? $this->orders_refunded = $this->cleanInput($_POST['orders_refunded']) : $orders_refunded = false;  
				 isset($_POST['orders_discount']) ? $this->orders_discount = $this->cleanInput($_POST['orders_discount']) : $orders_discount = false;  
				 isset($_POST['orders_voucher']) ? $this->orders_voucher = $this->cleanInput($_POST['orders_voucher']) : $orders_voucher = false;  
				 isset($_POST['orders_checkout_method']) ? $this->orders_checkout_method = $this->cleanInput($_POST['orders_checkout_method']) : $orders_checkout_method = false;  
				 isset($_POST['orders_checkout_payment']) ? $this->orders_checkout_payment = $this->cleanInput($_POST['orders_checkout_payment']) : $orders_checkout_payment = false;  
				 isset($_POST['orders_checkout_status']) ? $this->orders_checkout_status = $this->cleanInput($_POST['orders_checkout_status']) : $orders_checkout_status = false;  
				 isset($_POST['orders_checkout_discount']) ? $this->orders_checkout_discount = $this->cleanInput($_POST['orders_checkout_discount']) : $orders_checkout_discount = false;  
				 isset($_POST['orders_checkout_success']) ? $this->orders_checkout_success = $this->cleanInput($_POST['orders_checkout_success']) : $orders_checkout_success = false;  
				 isset($_POST['orders_session_id']) ? $this->orders_session_id = $this->cleanInput($_POST['orders_session_id']) : $orders_session_id = false;  
				 isset($_POST['orders_session_ip']) ? $this->orders_session_ip = $this->cleanInput($_POST['orders_session_ip']) : $orders_session_ip = false;  
				 isset($_POST['orders_session_ua']) ? $this->orders_session_ua = $this->cleanInput($_POST['orders_session_ua']) : $orders_session_ua = false;  
				 isset($_POST['orders_session_attempts']) ? $this->orders_session_attempts = $this->cleanInput($_POST['orders_session_attempts']) : $orders_session_attempts = false;  
				 isset($_POST['orders_diff']) ? $this->orders_diff = $this->cleanInput($_POST['orders_diff']) : $orders_diff = false;  
		break;
		
		case 'page':
				 isset($_POST['page_id']) ? $this->page_id = $this->cleanInput($_POST['page_id']) : $page_id = false;  
				 isset($_POST['page_title']) ? $this->page_title = $this->cleanInput($_POST['page_title']) : $page_title = false;  
				 isset($_POST['page_description']) ? $this->page_description = $this->cleanInput($_POST['page_description']) : $page_description = false;  
				 isset($_POST['page_short_text']) ? $this->page_short_text = $this->cleanInput($_POST['page_short_text']) : $page_short_text = false;  
				 isset($_POST['page_long_text']) ? $this->page_long_text = $this->cleanInput($_POST['page_long_text']) : $page_long_text = false;  
				 isset($_POST['page_url']) ? $this->page_url = $this->cleanInput($_POST['page_url']) : $page_url = false;  
				 isset($_POST['page_tags']) ? $this->page_tags = $this->cleanInput($_POST['page_tags']) : $page_tags = false;  
				 isset($_POST['page_image_header']) ? $this->page_image_header = $this->cleanInput($_POST['page_image_header']) : $page_image_header = false;  
				 isset($_POST['page_image_main']) ? $this->page_image_main = $this->cleanInput($_POST['page_image_main']) : $page_image_main = false;  
				 isset($_POST['page_image_left']) ? $this->page_image_left = $this->cleanInput($_POST['page_image_left']) : $page_image_left = false;  
				 isset($_POST['page_image_right']) ? $this->page_image_right = $this->cleanInput($_POST['page_image_right']) : $page_image_right = false;  
				 isset($_POST['page_status']) ? $this->page_status = $this->cleanInput($_POST['page_status']) : $page_status = false;  
				 isset($_POST['page_archived']) ? $this->page_archived = $this->cleanInput($_POST['page_archived']) : $page_archived = false;  
				 isset($_POST['page_created']) ? $this->page_created = $this->cleanInput($_POST['page_created']) : $page_created = false;  
				 isset($_POST['page_published']) ? $this->page_published = $this->cleanInput($_POST['page_published']) : $page_published = false;  
				 isset($_POST['page_updated']) ? $this->page_updated = $this->cleanInput($_POST['page_updated']) : $page_updated = false;  
				 isset($_POST['page_meta_title']) ? $this->page_meta_title = $this->cleanInput($_POST['page_meta_title']) : $page_meta_title = false;  
				 isset($_POST['page_meta_description']) ? $this->page_meta_description = $this->cleanInput($_POST['page_meta_description']) : $page_meta_description = false;  
				 isset($_POST['page_meta_tags']) ? $this->page_meta_tags = $this->cleanInput($_POST['page_meta_tags']) : $page_meta_tags = false;  
		break;
		
		case 'site':
				 isset($_POST['site_url']) ? $this->site_url = $this->cleanInput($_POST['site_url']) : $site_url = false;  
				 isset($_POST['site_domain']) ? $this->site_domain = $this->cleanInput($_POST['site_domain']) : $site_domain = false;  
				 isset($_POST['site_canonical']) ? $this->site_canonical = $this->cleanInput($_POST['site_canonical']) : $site_canonical = false;  
				 isset($_POST['site_cdn']) ? $this->site_cdn = $this->cleanInput($_POST['site_cdn']) : $site_cdn = false;  
				 isset($_POST['site_charset']) ? $this->site_charset = $this->cleanInput($_POST['site_charset']) : $site_charset = false;  
				 isset($_POST['site_title']) ? $this->site_title = $this->cleanInput($_POST['site_title']) : $site_title = false;  
				 isset($_POST['site_description']) ? $this->site_description = $this->cleanInput($_POST['site_description']) : $site_description = false;  
				 isset($_POST['site_logo']) ? $this->site_logo = $this->cleanInput($_POST['site_logo']) : $site_logo = false;  
				 isset($_POST['site_icon']) ? $this->site_icon = $this->cleanInput($_POST['site_icon']) : $site_icon = false;  
				 isset($_POST['site_status']) ? $this->site_status = $this->cleanInput($_POST['site_status']) : $site_status = false;  
				 isset($_POST['site_updated']) ? $this->site_updated = $this->cleanInput($_POST['site_updated']) : $site_updated = false;  		
				 isset($_POST['site_currency']) ? $this->site_currency = $this->cleanInput($_POST['site_currency']) : $site_currency = false;  		
				 isset($_POST['site_meta_title']) ? $this->site_meta_title = $this->cleanInput($_POST['site_meta_title']) : $site_meta_title = false;  
				 isset($_POST['site_meta_description']) ? $this->site_meta_description = $this->cleanInput($_POST['site_meta_description']) : $site_meta_description = false;  
				 isset($_POST['site_meta_tags']) ? $this->site_meta_tags = $this->cleanInput($_POST['site_meta_tags']) : $site_meta_tags = false;  
				 isset($_POST['site_meta_name_1']) ? $this->site_meta_name_1 = $this->cleanInput($_POST['site_meta_name_1']) : $site_meta_name_1 = false;  
				 isset($_POST['site_meta_name_2']) ? $this->site_meta_name_2 = $this->cleanInput($_POST['site_meta_name_2']) : $site_meta_name_2 = false;  
				 isset($_POST['site_meta_name_3']) ? $this->site_meta_name_3 = $this->cleanInput($_POST['site_meta_name_3']) : $site_meta_name_3 = false;  
				 isset($_POST['site_meta_name_4']) ? $this->site_meta_name_4 = $this->cleanInput($_POST['site_meta_name_4']) : $site_meta_name_4 = false;  
				 isset($_POST['site_meta_value_1']) ? $this->site_meta_value_1 = $this->cleanInput($_POST['site_meta_value_1']) : $site_meta_value_1 = false;  
				 isset($_POST['site_meta_value_2']) ? $this->site_meta_value_2 = $this->cleanInput($_POST['site_meta_value_2']) : $site_meta_value_2 = false;  
				 isset($_POST['site_meta_value_3']) ? $this->site_meta_value_3 = $this->cleanInput($_POST['site_meta_value_3']) : $site_meta_value_3 = false;  
				 isset($_POST['site_meta_value_4']) ? $this->site_meta_value_4 = $this->cleanInput($_POST['site_meta_value_4']) : $site_meta_value_4 = false;  
				 isset($_POST['site_tags']) ? $this->site_tags = $this->cleanInput($_POST['site_tags']) : $site_tags = false;  
				 isset($_POST['site_socialmedia_option1']) ? $this->site_socialmedia_option1 = $this->cleanInput($_POST['site_socialmedia_option1']) : $site_socialmedia_option1 = false;  
				 isset($_POST['site_socialmedia_option2']) ? $this->site_socialmedia_option2 = $this->cleanInput($_POST['site_socialmedia_option2']) : $site_socialmedia_option2 = false;  
				 isset($_POST['site_socialmedia_option3']) ? $this->site_socialmedia_option3 = $this->cleanInput($_POST['site_socialmedia_option3']) : $site_socialmedia_option3 = false;  
				 isset($_POST['site_socialmedia_option4']) ? $this->site_socialmedia_option4 = $this->cleanInput($_POST['site_socialmedia_option4']) : $site_socialmedia_option4 = false;  
				 isset($_POST['site_socialmedia_option5']) ? $this->site_socialmedia_option5 = $this->cleanInput($_POST['site_socialmedia_option5']) : $site_socialmedia_option5 = false;  
				 isset($_POST['site_javascript']) ? $this->site_javascript = $this->cleanInput($_POST['site_javascript']) : $site_javascript = false;  
				 isset($_POST['site_ext_javascript']) ? $this->site_ext_javascript = $this->cleanInput($_POST['site_ext_javascript']) : $site_ext_javascript = false;  
				 isset($_POST['site_stylesheet']) ? $this->site_stylesheet = $this->cleanInput($_POST['site_stylesheet']) : $site_stylesheet = false;  
				 isset($_POST['site_ext_stylesheet']) ? $this->site_ext_stylesheet = $this->cleanInput($_POST['site_ext_stylesheet']) : $site_ext_stylesheet = false;  
				 isset($_POST['site_google_tags']) ? $this->site_google_tags = $this->cleanInput($_POST['site_google_tags']) : $site_google_tags = false;  
				 isset($_POST['site_cookie_name_1']) ? $this->site_cookie_name_1 = $this->cleanInput($_POST['site_cookie_name_1']) : $site_cookie_name_1 = false;  
				 isset($_POST['site_cookie_name_2']) ? $this->site_cookie_name_2 = $this->cleanInput($_POST['site_cookie_name_2']) : $site_cookie_name_2 = false;  
				 isset($_POST['site_cookie_name_3']) ? $this->site_cookie_name_3 = $this->cleanInput($_POST['site_cookie_name_3']) : $site_cookie_name_3 = false;  
				 isset($_POST['site_cookie_value_1']) ? $this->site_cookie_value_1 = $this->cleanInput($_POST['site_cookie_value_1']) : $site_cookie_value_1 = false;  
				 isset($_POST['site_cookie_value_2']) ? $this->site_cookie_value_2 = $this->cleanInput($_POST['site_cookie_value_2']) : $site_cookie_value_2 = false;  
				 isset($_POST['site_cookie_value_3']) ? $this->site_cookie_value_3 = $this->cleanInput($_POST['site_cookie_value_3']) : $site_cookie_value_3 = false;  		
				 isset($_POST['site_analytics']) ? $this->site_analytics = $this->cleanInput($_POST['site_analytics']) : $site_analytics = false;  
			break;

		}

		return;
	}
	
}

?>
