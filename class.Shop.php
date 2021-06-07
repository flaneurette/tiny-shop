<?php

// if possible, store this class below the www or html folder for more security.

###########################################################################
##                                                                       ##
##  Copyright 2020 Alexandra van den Heetkamp.                           ##
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

class Shop {

	CONST SHOP				= "./inventory/shop.json";
	CONST SHOPVERSION 			= "?cache-control=1"; // increment if major changes are made to the shop database.
	CONST CSV				= "./inventory/csv/shop.csv"; 
	CONST BACKUPEXT				= ".bak"; 
	CONST FILE_ENC				= "UTF-8";
	CONST FILE_OS				= "WINDOWS-1252"; // only for JSON and CSV, not the server architecture.
	CONST MAXINT  				= 999999999;
	CONST DEPTH					= 10024;
	CONST MAXWEIGHT				= 10000;
	CONST MAXTITLE				= 255; // Max length of title.
	CONST MAXDESCRIPTION			= 500; // Max length of description.

	CONST PHPENCODING 		= 'UTF-8';		// Characterset of PHP functions: (htmlspecialchars, htmlentities) 
	CONST MINHASHBYTES		= 32; 			// Min. of bytes for secure hash.
	CONST MAXHASHBYTES		= 64; 			// Max. of bytes for secure hash, more increases cost. Max. recommended: 256 bytes.
	CONST MINMERSENNE		= 0xff; 		// Min. value of the Mersenne twister.
	CONST MAXMERSENNE		= 0xffffffff; 	// Max. value of the Mersenne twister.
	
	CONST GATEWAYS 			= ["ACH","Alipay","Apple Pay","Bancontact","BenefitPay","Boleto Bancário","Citrus Pay","EPS","Fawry","Giropay","Google Pay","PayPal","KNET","Klarna","Mada","Multibanco","OXXO","Pago Fácil","Poli","Przelewy24","QPAY","Rapipago","SEPA Direct Debit","Sofort","Stripe","Via Baloto","iDEAL"];
	
	public function __construct() {
		$incomplete = false;
		$host = $this->getbase();
	}
	
	// Password to encrypt JSON
	private static function PWD() {
		return "thepasswordisnow";
	}

	public function getbase($path=false,$nav=false) 
	{	
	
		$host_path 	= $this->gethost("inventory/site.json",true);
		
		$siteconf 	 = $this->load_json("inventory/site.json");
		$result_url  = $this->getasetting($siteconf,'site.url');
		$result_can  = $this->getasetting($siteconf,'site.canonical');

		if($nav == true) {
			return $result_url['site.url'];
		}
		
		if($path == true) {
			return $result_can['site.canonical'];
		}
		
		$find 	 = ['http://','https://','http//','https//','www.','www','/']; // todo: make regex out of it.
		$replace = ['','','','','','',''];

		// build paths dynamically
		$home  = 'https://';
		$home .= str_replace($find,$replace,$result_url['site.url']);
		$home .= '/';
		$home .= $result_can['site.canonical'];
		$home .= '/';
		
		return $home;
	}
	
	public function host() {
		return $this->getbase();
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
	* Max string of user-input
	* @param string, length and dots.
	* @return string
	*/
	
	public function maxstring($string,$len,$dots) 
	{
		$wordarray = explode(' ',$string);
		
		$returnstring = '';
		
		$c = count($wordarray);
		
		for($i = 0; $i < $c; $i++) {
			
			if(strlen($returnstring) >= $len) {
				break;
			} else {
				$returnstring .= $wordarray[$i] . ' ';
			}
		}
		
		if($dots == true) {
			$returnstring .= '...';
		}
		
		return $returnstring;
	}	
	

	/**
	* Sanitizes user-input
	* @param string
	* @return string
	*/
	
	public function sanitize($string,$method='') 
	{
		
		$data = '';
		
		switch($method) {
			
			case 'alpha':
				$this->data =  preg_replace('/[^a-zA-Z]/','', $string);
			break;
			
			case 'trim':
				
				if(isset($string)) {
					
					if(trim($string) != "") {
						$this->data = $string;
						} elseif(strlen($string) > 2) {
						$this->data = $string;
						} else {
						$this->data = false;
					}
					
				} else {
					$this->data = false;
				}
				
			break;		
			
			case 'num':
			
			if($string > self::MAXINT) {
				return false;
				} else {
				$this->data =  preg_replace('/[^0-9]/','', $string);
			}
				
			break;
			
			case 'dir':
				$this->data =  preg_replace('/[^a-zA-Z-0-9\.\/]/','', $string);
			break;			

			case 'email':
			case 'cat':
				$this->data = $string;
				# $this->data =  preg_replace('/[^a-zA-Z-0-9\-_\/]/','', $string);
			break;
			
			case 'alphanum':
				$this->data =  preg_replace('/[^a-zA-Z-0-9]/','', $string);
			break;
			
			case 'field':
				$this->data =  preg_replace('/[^A-Za-z0-9-_.@/]/','', $string);
			break;
			
			case 'query':
				$search  = ['`','"','\'',';'];
				$replace = ['','','',''];
				$this->data = str_replace($search,$replace,$string);
			break;
			
			case 'cols':
				// comma is allowed for selecting multiple columns.
				$search  = ['`','"','\'',';'];
				$replace = ['','','',''];
				$this->data = str_replace($search,$replace,$string);
			break;
			
			case 'table':
				$search  = ['`','"',',','\'',';','$','%','>','<'];
				$replace = ['','','','','','','','',''];
				$this->data = str_replace($search,$replace,$string);
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
			
			case 'url':
				$search  = ['`','"',',','\'',';','$','%','>','<','\/'];
				$replace = ['','','','','','','','','','/'];
				$this->data = stripslashes(str_replace($search,$replace,$string));
			break;
			
			case 'domain':
				$this->data =  str_ireplace(array('http://','www.'),array('',''),$string);
			break;
			
			default:
			return $this->data;
			
			}
		return $this->data;
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
	
	public function encode($json) 
	{
		return json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}
	
	/**
	* Loads and decodes JSON object
	* @return mixed object/array
	*/
	
	public function decode() 
	{
		
		$file = file_get_contents(self::SHOP);
		$json = json_decode($file, true, self::DEPTH, JSON_BIGINT_AS_STRING);
		
		if($json !== NULL || $json != false) {
			return $json;
			} else {
				$this->message("Error: JSON file could not be loaded.");
			exit;
		} 
		
	}
	
	public function load_json($url) 
	{
		
		if(!$url) {
			$url = 'inventory/site.json';
		}
		
		$url = str_replace('.json','',$url);
		$url .= '.json';

		if(file_exists($url)) {
			$file = file_get_contents($url);
			} elseif(file_exists('../'.$url)) {
			$file = file_get_contents('../'.$url);
			} elseif(file_exists('../../'.$url)) {
			$file = file_get_contents('../../'.$url);
			} else {
			return false;
		}
		
		$json = json_decode($file, true, self::DEPTH, JSON_BIGINT_AS_STRING);
		
		if($json !== NULL || $json != false) {
			return $json;
			} else {
				$this->message("Error: JSON file could not be loaded.");
			exit;
		} 
	}

	public function addshop() 
	{
		$newshop = $this->products();
		$lijst = $this->decode();
		$i = count($lijst);
		$lijst = array($newshop);

		$this->storedata($lijst);
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
		$string  = "<pre>";
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
	public function storedata($url,$data,$method='json') 
	{
		if($method == 'json') {
			$json = mb_convert_encoding($this->encode($data), self::FILE_ENC, self::FILE_OS);
			file_put_contents($url,$json, LOCK_EX);			
			} else {
			file_put_contents($url,$data, LOCK_EX);			
		}
	}
	
	public function backup($url) 
	{	
		$copy 	= $url.self::BACKUPEXT;
		@copy($url, $copy);
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
		$this->storedata($shops);
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
			// $html .= '<meta name="viewport" content="'.$this->cleanInput($row['site.viewport']).'">';
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

			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->cleanInput($row['site.domain']).'/'.$this->cleanInput($row['site.canonical']).'/'.$this->cleanInput($row['site.stylesheet.reset']).'">';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->cleanInput($row['site.domain']).'/'.$this->cleanInput($row['site.canonical']).'/'.$this->cleanInput($row['site.stylesheet1']).'">';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->cleanInput($row['site.domain']).'/'.$this->cleanInput($row['site.canonical']).'/'.$this->cleanInput($row['site.stylesheet2']).'">';


			if(!empty($row['site.ext.stylesheet'])) {
				$html .= '<link rel="stylesheet" type="text/css" href="'.$this->cleanInput($row['site.ext.stylesheet']).'">';
			}		
			
			$html .= '<link rel="icon" type="image/ico" href="'.$this->cleanInput($row['site.domain']).'/'.$this->cleanInput($row['site.canonical']).'/'.$this->cleanInput($row['site.icon']).'">';
			$html .= '<script src="'.$this->cleanInput($row['site.domain']).'/'.$this->cleanInput($row['site.canonical']).'/'.$this->cleanInput($row['site.javascript']).'" type="text/javascript"></script>';
			
			if(!empty($row['site.ext.javascript'])) {
				$html .= '<script src="'.$this->cleanInput($row['site.ext.javascript']).'" type="text/javascript"></script>';
			}					
			
			// $html .= '<img src="'.$this->cleanInput($row['site.domain']).'/'.$this->cleanInput($row['site.canonical']).'/'.$this->cleanInput($row['site.logo']).'" width="115" id="ts.shop.logo">';
		}
		
		return $html;
	}

	/**
	* Paginate function
	* @param int $page
	* @return $string, html, false for failure.
	*/	

	public function invoiceid($dir,$method,$value=false) 
	{

		if(!isset($method)) {
			return false;
		}

		$shopconf = $this->load_json($dir);
		
		if($shopconf == null || $shopconf == '') {
			return false;
		}
		
		$configuration = [];
		
		if($shopconf !== null) {
			foreach($shopconf as $conf) {	
				array_push($configuration,$conf);
			}
		}
		
		if($method == 'get') {
			$invoiceid = $configuration[0]['orders.conf.invoice.id'];
			return $invoiceid;
		} 
		
		if($method == 'set') {
			
			if(isset($value)) {
				$shopconf[0]['orders.conf.invoice.id'] = (int)$value;
				$this->backup($dir);
				$this->storedata($dir,$shopconf);
				return true;
				} else {
				return false;
			}
		} 
	}
	
	public function navigation($host) { 
	
		$navigate = $this->load_json('inventory/navigation.json');
	
		$hostaddr = $this->getbase(false,true);
	
		if(isset($_SERVER["SCRIPT_URL"])) {
			$script_url 	= $this->sanitize($_SERVER["SCRIPT_URL"],'alpha');
		}
		if(isset($_SERVER["REQUEST_URI"])) {
			$request_uri 	= $this->sanitize($_SERVER["REQUEST_URI"],'alpha');
		}

		if(strstr($request_uri,'category')) {
			$hostaddr = $this->getbase(false,true) .'/'; 
		} elseif(strstr($request_uri,'subcategory')) {
			$hostaddr = $this->getbase(false,true) .'/';
		} elseif(strstr($request_uri,'cart')) {
			$hostaddr = $this->getbase(false,true) .'/';
		} elseif(strstr($request_uri,$this->getbase(true,false))) {
			$hostaddr = $this->getbase(false,true) .'/';
		} else {
			$hostaddr = $this->getbase();
		}
	
		$nav = '<nav>';
		
		$total = count($navigate);
		
		foreach($navigate as $n) {	
				
			if($n['nav.status'] =='1') {
				$nav .= '<a href="'.$hostaddr.$this->cleanInput($n['nav.url']).'" target="_self">'.$this->cleanInput($n['nav.title']).'</a>' .PHP_EOL;
			}
		}

		$nav .= '</nav>';
		
		return $nav;
	}	
	
	public function formatter($string,$method) {
		
		$returnstring = '';
		
		switch($method) {
			
			case 'product-description':

			$returnstring = $this->sanitize($string,'encode');
			$returnstring = substr($returnstring,0,512);
			
			break;
			
		}
		
		return $returnstring;
	}
	
	public function getcatId($cat,$subcat) {
		
		// categories JSON
		$categories = "inventory/categories.json";
		
		// subcategories JSON
		$subcategories = "inventory/subcategories.json";

		if(isset($cat) && ($subcat !=false)) {
			
			$category = $this->load_json($categories);
			foreach($category as $c) {	
				if($c['category.title'] == $cat) {
					$catno = (int)($c['category.id'] -1);
					break;
				}
			}
			
			// subcategories
			$subcategory = $this->load_json($subcategories);
			
			foreach($subcategory as $sc) {	
			
				if(($sc['sub.category.title'] == str_replace('-',' ',$subcat)) && ($sc['sub.category.cat.id'] == $catno)) {
					return (int)($sc['sub.category.cat.id'] -1);
					break;
				}
			}
			
		} elseif(isset($cat)) {
			
			// categories
			$category = $this->load_json($categories);
				
			foreach($category as $c) {	
				if($c['category.title'] == $cat) {
					return (int)($c['category.id'] -1);
					break;
				}
			}
			
		} else {
		return false;
		}
	}

	public function categories($categories,$subcategories,$selected=false,$direction) { 

			if(!isset($categories)) {
				return false;
			}
			
			if(!isset($subcategories)) {
				return false;
			}			
			
			if($selected != false) {
			
				if(is_array($selected)) {
					
					$c = count($selected);
					
					if($selected[0]) {
						$catselected = $this->sanitize($selected[0],'cat');
					}
					if($selected[1]) {
						$subcatselected = $this->sanitize($selected[1],'cat');
					}			
				}
			}
		
			// get host
			$hostaddr = $this->getbase();
		
			// categories
			$categories = $this->load_json($categories);
			
			// subcategories
			$subcategories = $this->load_json($subcategories);	
			
			if($direction == 'left') {
				$cssdirection = 'left';
			}

			if($direction == 'right') {
				$cssdirection = 'right';
			}
			
			if($direction == 'top') {
				$cssdirection = 'top';
			}
			
			$html = '<ul id="ts-shop-'.$cssdirection.'-navigation">';
			
			if($categories !== null) {
				
				$i = 0;
				$totalcats = count($categories);
				$totalsubcats = count($subcategories);
				foreach($categories as $c) {	
				
				if($c['category.title'] !='') {
					
					if(isset($catselected) == isset($c['category.title'])) {
						$html .= '<li class="ts-shop-'.$cssdirection.'-navigation-cat-selected" onclick="tinyshop.toggle(\''.$i.'\',\''.$totalcats.'\');" id="cat'.$i.'"><a href="'.$hostaddr.'category/'.$this->seoUrl($c['category.title']).'/">'.$c['category.title'].'</a></li>'.PHP_EOL;
						} else {
						$html .= '<li class="ts-shop-'.$cssdirection.'-navigation-cat" onclick="tinyshop.toggle(\''.$i.'\',\''.$totalcats.'\');" id="cat'.$i.'"><a href="'.$hostaddr.'category/'.$this->seoUrl($c['category.title']).'/">'.$c['category.title'].'</a></li>'.PHP_EOL;
					}
					
					$catid = (int)$c['category.id'];
					
					$j = 0;
					
					if($totalsubcats >=1) {
						foreach($subcategories as $sc) {	
							if($catid == $sc['sub.category.cat.id']) {
								if($j == 0) {
									$html .= '<ul class="ts-shop-'.$cssdirection.'-navigation-subcat" id="toggle'.$i.'">'.PHP_EOL;
								}
								
								if(isset($subcatselected) == isset($c['sub.category.title'])) {
								$html .= '<li class="ts-shop-'.$cssdirection.'-navigation-subcat-item-selected"><a href="'.$hostaddr.'subcategory/'.$this->seoUrl($c['category.title']).'/'.$this->seoUrl($sc['sub.category.title']).'/">'.$sc['sub.category.title'].'</a></li>'.PHP_EOL;
									} else {
								$html .= '<li class="ts-shop-'.$cssdirection.'-navigation-subcat-item"><a href="'.$hostaddr.'subcategory/'.$this->seoUrl($c['category.title']).'/'.$this->seoUrl($sc['sub.category.title']).'/">'.$sc['sub.category.title'].'</a></li>'.PHP_EOL;	
								}
								$j++;
							}
						}
					}
					
					if($j > 0) {
						$html .= '</ul>'.PHP_EOL;
					}
				}
				$i++;
				}
			}
			$html .= '</ul>';
			
			return $html;
	}	
		
	/**
	* Returns a product list, by reading shop.json.
	* @param method: list|group.	
	* @param string: custom html can be added.
	* @param category: select shop category, if none is given it will list all products.
	* @return $string, html or array (if method is requested.)
	*/		
	public function getproducts($method,$category,$string=false,$limit=false,$page=false,$token) 
	{
	
		isset($string) ? $this->$string = $string : $string = false;
		isset($category) ? $this->$category = $category : $category = false;
		isset($page_id) ? $this->page_id = (int)$_GET['page_id'] : $this->page_id = 1;	
		
		isset($_GET['cat']) ? $this->product_cat = $_GET['cat'] : $this->product_cat = false;	
		isset($_GET['subcat']) ? $this->product_subcat = $_GET['subcat'] : $this->product_subcat = false;	
				
		$hostaddr = $this->getbase();
		
		// Loading the shop configuration.
		$shopconf = $this->load_json("inventory/shop.conf.json");
		$configuration = [];
		
		if($shopconf !== null) {
			foreach($shopconf as $conf) {	
				array_push($configuration,$conf);
			}
		}
		
		// Logic for pagination on products.
		
		if($limit == false) {
			$siteconf 	= $this->load_json("inventory/site.json");
			$result 	= $this->getasetting($siteconf,'site.maxproducts.visible.in.cat');
			$limit 		= (int) $result["site.maxproducts.visible.in.cat"];
			$limit_products = $limit;
			} else {
			$limit_products = $limit;
		}
		
		if($page != false) {
			$page_products = $page;
			} else {
			$page_products = 1;
		}
		
		$productlist = $this->decode();	
		
		if($productlist !== null) {
			$amount_products = count($productlist);
			} else {
			$amount_products = 0;
		}
		
		// build pagination for product page.
		
		if($amount_products >= 1) {

			$pagination = true;
			
			if(isset($_GET['page'])) {
				$page_products   = (int)$_GET['page'];
				} else {
				$page_products   = 1;
			}
		 
			if($amount_products <= 1) {
				echo 'There are not enough products to view.';
				exit;
			}

			if($limit_products >= 500) {
				echo 'There are too many products to view. Please edit the appropiate max product value setting in site.json.';
				exit;
			}

			if($limit_products <= 1) {
				$limit_products = 10;
			}

			if($page_products < 1) {
				$page_products = 1;
			}
			
			// todo: fix bug on limit ~ amount
			if($limit_products > $amount_products) {
				$limit_products = $amount_products;
			}
			
			$pages = round($amount_products / $limit_products);
			
			if($page_products == 1) {
				$min = 0;
				$max = $limit_products;
			}
			
			if($page_products > 1) {
				$min = (($page_products -1) * $limit_products);
				$max = ($page_products * $limit_products);		
			}
			
			if($max > $amount_products) {
				$min = ($amount_products - $limit_products); 
				$max = $amount_products; 
			}
			
		} else {
			$pagination = false;
		}
		
		// top paginate links
		$string_pag = '<div id="ts-paginate">';
		$string_pag .= '<div id="ts-paginate-left">';
		$string_pag .= 'Showing product ';
		
		if($min == 0) {
			$string_pag .= $min+1;
			} else {
			$string_pag .= $min;
		}
	
		$string_pag .= ' to ';
		$string_pag .= $max;
		$string_pag .= '</div>';
		$string_pag .= '<div id="ts-paginate-right">';
		$string_pag .= 'Page '.$page_products.' of '. $pages; 
		
		if($page != $pages) {
		   $string_pag .= '&nbsp;<span id="ts-paginate-arrow"><a href="'.($page_products+1).'/">&rarr;</a></span>';
		} 
		
		$string_pag .= '</div>';
		$string_pag .= '</div>';
		
		// carousel selection.
		if($configuration[0]['products.carousel'] == 1 && $category == 'index') {
			$carousel = true;
		}
		
		/* 
			TODO: 
			carousel selection, plus:
			$configuration['products.orientation'] : "thumb"
			$configuration['products.alt.tags']    : "no"
			$configuration['products.scene.type']  : "box"
			$configuration['products.row.count']   : 10
			$configuration['products.per.page']    : 25		
			$configuration['products.per.cat']     : 25
		*/

		$string .= "<div id=\"ts-product\">";
		
		if($productlist !== null) {

			$ts 	  = array();
			$shoplist = $productlist;
			
			if($pagination == false) {
				$min  = 0;
				$max  = count($productlist);
			} 
		
			for($k = $min; $k < count($productlist); $k++) {	
			
				$c = $productlist[$k];
			
					if($this->product_subcat != false) {
						// category and subcategory
						if($c['product.category.sub'] == $this->product_subcat && $c['product.category'] == $this->product_cat) {
								array_push($ts,$c);
						}	
					} elseif($this->product_cat != false) {
						// only category
						if($c['product.category'] == $this->product_cat) {
								array_push($ts,$c);
						}	
					} else {
						// no cat nor subcat.
					}
				
					$this->cleanInput($c['product.title']);
			}
			
			// pagination count correction.
			
			$ts_pag = count($ts);

			if($ts_pag > $limit) {
				$string .= $string_pag;
				} else {
				
			}
			
			if($k <= 0) {
				return '<div id="ts-products-noproducts">There are no products in this category.</div>';
			}
			
			if($method == 'array') {
				return $ts;
				exit;
			}

			if($pagination == false) {
				$i = count($ts);
				} else {
				$i = $max;
			}

			if($i >= 0) { 
			
				while($i >= 0) {
					
					if(isset($ts[$i]['product.stock'])) {
						$stock = (int) $ts[$i]['product.stock'];
					} else {
						$stock = 0;
					}
					
					if($stock <= 5) {
						$status = 'ts-product-status-red'; // low stock
						} else {
						$status = 'ts-product-status-green';
					}
					
					if(isset($ts[$i]['product.image']) != "") {
						$productimage = '<div class="ts-product-image-div"><img src="'.$hostaddr.$this->cleanInput($ts[$i]['product.image']).'" class="ts-product-image"/></div>';
						} else {
						$productimage = '<div class="ts-product-image-icon">&#128722;</div>';
					}				
					
					switch($method) {
						
						case 'list':

						if(isset($ts[$i]['product.description'])) {

							$string .= "<div class=\"ts-product-list\">";
							$string .= $productimage;
							$string .= "<div class=\"ts-list-product-link\"><a href=\"item/".$this->seoUrl($this->cleanInput($ts[$i]['product.category'])).'/'.$this->seoUrl($this->cleanInput($ts[$i]['product.title'])).'/'.$this->cleanInput($ts[$i]['product.id'])."/".(int)$this->page_id."/\">".$this->maxstring($this->cleanInput($ts[$i]['product.title']),10,false)."</a> </div>";
							$string .= "<div class=\"ts-list-product-desc\">".$this->maxstring($this->cleanInput($ts[$i]['product.description']),30,true)."</div>";
							// $string .= "<div class=\"ts-list-product-cat\">".$this->cleanInput($ts[$i]['product.category'])."</div>";
							$string .= "<div class=\"ts-list-product-price\">".$this->getsitecurrency('inventory/site.json','inventory/currencies.json').' '.$this->cleanInput($ts[$i]['product.price'])."</div>";
							$string .= "<div class=\"ts-list-product-status\">left in stock.<div class=\"".$status."\">".$this->cleanInput($ts[$i]['product.stock'])."</div></div>";
							
							if(isset($configuration[0]['products.quick.cart']) == 'yes') {
								$string .= "<div><input type='number' name='qty' size='1' value='1' min='1' max='9999' id='ts-group-cart-qty-".($i+1).'-'.$ts[$i]['product.id']."'><input type='button' onclick='tinyshop.addtocart(\"".$ts[$i]['product.id']."\",\"ts-group-cart-qty-".($i+1).'-'.$ts[$i]['product.id']."\",\"".$token."\",\"".$hostaddr."\");' class='ts-list-cart-button' name='add_cart' value='".$this->cleanInput($configuration[0]['products.cart.button'])."' /></div>";
								} else {
								$string .= "<div class='ts-list-view-link'><a href=\"product/".$this->cleanInput($ts[$i]['product.id'])."/\">view</a></div>";
							}
							
							$string .= "</div>";
						} 
		
						break;
						
						case 'group':		
						$string .= "<div class=\"ts-product-group\">";
						$string .= $productimage;
						$string .= "<div class=\"ts-group-product-link\"><a href=\"item/".$this->seoUrl($this->cleanInput($ts[$i]['product.category'])).'/'.$this->seoUrl($this->cleanInput($ts[$i]['product.title'])).'/'.$this->cleanInput($ts[$i]['product.id'])."/\">".$this->cleanInput($ts[$i]['product.title'])."</a> </div>";
						$string .= "<div class=\"ts-group-product-desc\">".$this->cleanInput($ts[$i]['product.description'])."</div>";
						$string .= "<div class=\"ts-group-product-price\">".$this->getsitecurrency('inventory/site.json','inventory/currencies.json').' '.$this->cleanInput($ts[$i]['product.price'])."</div>";
						// $string .= "<div class=\"ts-group-product-cat\">".$this->cleanInput($ts[$i]['product.category'])."</div>";
						$string .= "<div class=\"ts-group-product-status\">left in stock.<div class=\"".$status."\">".$this->cleanInput($ts[$i]['product.stock'])."</div></div>";
						
						if(isset($configuration[0]['products.quick.cart']) == 'yes') {
							
							$string .= "<div><input type='number' name='qty' size='1' min='1' max='9999' value='1' id='ts-group-cart-qty-".($i+1).'-'.$ts[$i]['product.id']."'><input type='button' onclick='tinyshop.addtocart(\"".$ts[$i]['product.id']."\",\"ts-group-cart-qty-".($i+1).'-'.$ts[$i]['product.id']."\",\"".$token."\",\"".$host."\");' class='ts-group-cart-button' name='add_cart' value='".$this->cleanInput($configuration[0]['products.cart.button'])."' /></div>";
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
		
		return array($k,$string);
	}
	
	public function getproductlist($json) {
		
		if(!isset($json)) {
			$json = "inventory/shop.json";
		} 
		
		$cart = $this->load_json($json);
		
		$products = [];
		
		$i=0;
			foreach($cart as $item)
			{	
				$products[$i] = [];
				
			foreach($item as $product => $value)
			{
				array_push($products[$i],[$product,$value]);
			}
			$i++;
		}
			
		return $products;
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

		return $shopconf;
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
	
	public function categorylist($method,$category=null,$subcategory=null) 
	{
		
		$html = "";
		$igoreset = [];
		
		switch($method) {
			
			case 'all':
		
				if($category !== null) {

					$i = 0;
					$html = "";
					
					foreach($category as $row)
					{
						foreach($row as $key => $value)
						{
							if($key == 'category.id') {
								$subcatid = $value;
							}
							
							if($key == 'category.title') {
								
								if($value !='' || $value != null) {
								$html .= "<option value=\"".$this->sanitize($value,'dir')."\">".$this->sanitize($value,'unicode')."</option>";
									foreach($subcategory as $subrow)
									{
										foreach($subrow as $subkey => $subvalue)
										{
											if($subkey == 'sub.category.title') {
												$subkeytitle = $subvalue;
											}
											
											if($subkey == 'sub.category.cat.id') {
												if($subcatid == $subvalue) {
													if($subvalue !='' || $subvalue != null) {
														$html .= "<option value=\"".$this->sanitize($value,'dir').'/'.$this->sanitize($subkeytitle,'dir')."\"> - ".$this->sanitize($subkeytitle,'unicode')."</option>";
													}
												}
											}
										}
									}
								}
							}
						}
					}		
				}		
			
			break;
			
			case 'category':
			
				if($category !== null) {

					$i = 0;
					$html = "";
					
					foreach($category as $row)
					{
						foreach($row as $key => $value)
						{
							if($key == 'category.title') {
								$html .= "<option value=\"".$value."\">".$value."</option>";
							}
						}
					}		
				}	
			
			break;
			
			case 'subcategory':
			
				if($category !== null) {

					$i = 0;
					$html = "";
					
					foreach($category as $row)
					{
						foreach($row as $key => $value)
						{
							if($key == 'sub.category.title') {
								$html .= "<option value=\"".$value."\">".$value."</option>";
							}
						}
					}		
				}		
			
			break;	

		}			
			
		return $html;
	}

	public function gethost($json,$shoppath=false)   
	{
		$siteconf 		= $this->load_json($json);
		$result 		= $this->getasetting($siteconf,'site.url');
		$result_path 	= $this->getasetting($siteconf,'site.canonical');   
		
		$find 		= ['http://','https://','www.','/'];
		$replace 	= ['','','',''];
		
		$home  		= 'https://';
		$home 	   .= str_replace($find,$replace,$result['site.url']);
		
		if($shoppath==true) {
			$home  .= '/' . $result_path['site.canonical'] . '/';
		}
		
		return $home;
	}

	public function getasetting($json,$akey) 
	{
			if($json !== null) {
			
				foreach($json as $key => $value)
				{
					 
					if($key == $akey) {
						return $value;	
					}		
				}		
			}
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

	public function shippinglist($json) 
	{
		$html = "";
		$igoreset = ['shipping.Flat.Fee','shipping.Flat.Fee.International'];
		
			if($json !== null) {
				foreach($json[0] as $key => $value)
				{
					if(!in_array($key,$igoreset)) {
						if($value == 0) {
						$html.= "<option value=\"".$key."\" disabled>".str_replace('shipping.','',$this->cleanInput($key))."</option>";
						} else {
						$html.= "<option value=\"".$key."\">".str_replace('shipping.','',$this->cleanInput($key))."</option>";
						$html.= "<option disabled>⠀⠀> shipping price: ".(float)$value."</option>";
						}
					}

				}		
			}
		return $html;
	}
	
	public function currencylist() 
	{
		$html = "";
		
		$currencies = $this->load_json("inventory/currencies.json");
		
			if($currencies !== null) {
				$i=0;
				foreach($currencies[0] as $key => $value)
				{
					$html .= "<option value=\"".$key."\">".$this->cleanInput($currencies[0][$i]['sign'])."</option>";
					$i++;
				}		
			}
			
		return $html;
	}
	
	public function getcountries() 
	{
		$html = "";
		
		$shipping = $this->load_json("inventory/shipping.json");
		
			if($shipping !== null) {
				$i=0;
				foreach($shipping[0] as $key => $value)
				{
					$html .= '<div class=\"ts-country-list-option\">' . $this->cleanInput($key) .": <input type=\"text\" name=\"".$key."\" value=\"".$value."\" size=\"20\" /></div>";
					$i++;
				}		
			}
			
		return $html;
	}	
	
	public function getcountryprice($json,$country) {
		
		$countryprice = 0;
			if($json !== null) {
				foreach($json[0] as $key => $value)
				{
					if($key == $country) {
						$countryprice = $value;
					}
				}		
			}
			
		if($countryprice > 0) {
			return $countryprice;
			} else {
			return false;
		}
	}
	
	/* Get the currency of site.json
	*  To change the default currency, edit site.json which has a numeric value that corresponds to the values inside currencies.json.
	*  DO NOT edit currencies.json, unless adding a new currency, as this file is used throughout TinyShop and might break functionality.
	*/
	public function getsitecurrency($conf=false,$currency=false) 
	{
		
		if(!isset($conf)) {
			$siteconf = $this->load_json("inventory/site.json");
			} else {
			$siteconf = $this->load_json($conf);
		}
		
		if(!isset($currency)) {
			$currencies = $this->load_json("inventory/currencies.json");
			} else {
			$currencies = $this->load_json($currency);
		}
		
		if($siteconf !== null || $siteconf !== false) {
				
			if($siteconf[0]['site.currency'] >=0) {
				return $currencies[0][$siteconf[0]['site.currency']]['symbol'];
			}
		} else {
			return 'Price:';
		}
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
								$html .= "<input type=\"checkbox\" id=\"".$keycss."\" name=\"".$key."\">";	
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
					
					unset($data[0]);
				}
				
				$c = count($data);
				if($c > 1) {
					unset($data[$c]);
				}
				
				$data = array_values($data);
				
			break;

			case 'json_to_csv_admin':
				
				$json_data = $this->convert($string,'json_decode');

				$shop->storedata('../inventory/csv/'.$shop->sanitize($file,'dir'),$showfile,'csv'); 
				
				$f = str_replace('.json','.csv',$_FILES['json_file']['name'][$i]);
				
				$csv_file = '../inventory/csv/'.$shop->sanitize($f,'dir');
				
				
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
	
	public function uniqueID() {
		
		$len_id 	= 0;
		$bytes_id 	= 0;
		
		if (function_exists('random_bytes')) {
			$len   = mt_rand(self::MINHASHBYTES,self::MAXHASHBYTES);
        		$bytes_id .= bin2hex(random_bytes($len));
    		}
		if (function_exists('openssl_random_pseudo_bytes')) {
			$len   = mt_rand(self::MINHASHBYTES,self::MAXHASHBYTES);
        		$bytes_id .= bin2hex(openssl_random_pseudo_bytes($len));
    		}
		
		if(strlen($bytes_id) < 128) {
			$bytes_id .= mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE)
				. mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) 
				. mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) 
				. mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE) . mt_rand(self::MINMERSENNE,self::MAXMERSENNE); 
		}
		
		$token_id 	= hash('sha512',$bytes_id);
		$uniqueid  	= substr($token_id,0,12);
		
		return $uniqueid;
	}

	public function pseudoNonce($max=0xffffffff) {
		$tmp_nonce = uniqid().mt_rand(0,$max).mt_rand(0,$max).mt_rand(0,$max).mt_rand(0,$max);
		return $tmp_nonce;
	}
	
	public function getToken()
	{
		
		$bytes = 0;
		
		$_SESSION['token'] = "";
		
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
				// $this->sessionmessage('Issue found: session token is too short.'); 
				} else {
				return $this->sanitize($_SESSION['token'],'alphanum'); 
			}
		} else { 
		return $token;
		} 
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
		
		$key = self::PWD(); // Password is set above at the Constants
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
		
		$key = self::PWD(); // Password is set above at the Constants
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
		$this->storedata($list);
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