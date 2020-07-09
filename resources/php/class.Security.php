<?php

class Security {

	/**
	* Security class
	*/
	CONST MAXINT  = 9999999;

	public function __construct() {
		$securing = true;
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
	* Sanitizes user-input
	* @param string
	* @return string
	*/
	
	public function sanitize($string,$method='',$buffer=255) 
	{
		
		$data = '';
		$strbf = '';
		
		switch($method) {
			
			case 'alpha':
				$this->data =  preg_replace('/[^a-zA-Z]/','', $string);
			break;
			
			case 'num':
			
			if($string > self::MAXINT) {
				return false;
				} else {
				$this->data =  preg_replace('/[^0-9]/','', $string);
			}
				
			break;
			
			case 'unicode':
				$this->data =  preg_replace("/[^[:alnum:][:space:]]/u", '', $string);
			break;
			
			case 'encode':
				$this->data =  htmlspecialchars($string,ENT_QUOTES,'UTF-8');
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
				$search  = ['`','"',',','\'',';','.','$','%'];
				$replace = ['','','','','','','',''];
				$this->data = str_replace($search,$replace,$string);
			break;
			
			default:
			return $this->data;
			
			}
		return $this->data;
	}
}

?>
