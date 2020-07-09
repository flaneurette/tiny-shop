<?php

namespace security\images;

###########################################################################
##                                                                       ##
##  Copyright 2008-2019 Alexandra van den Heetkamp.                      ##
##                                                                       ##
##  Secure upload class. This class scans images for vulnerabilities,    ##
##  viral vectors and exploits before processing and storing them.       ##
##                                                                       ##
##  @todo log, process and report found viral vectors and attacks.       ##
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

class ImageSecurityCheck 
{
	const MAXFILESIZE 	= 1000000;

	private $sieve 		= 0;  // Empty sieve 
	private $slots 		= 50; // Maximum number of upload slots.

	public function __construct($params = array()) 
	{ 
		$this->init($params);
		$this->allocateSlots();
	}

	public function __destruct()
	{
		$this->delimiters = array();
		$this->xssSignatures = array();
		$this->htmlSignatures = array();
		$this->exploitSignatures = array();
	}

	public function fullScan() 
	{
		$this->mimeScan();
		$this->binaryScan();
		$this->processImage();
		$this->showmessage();
	}

	/**
	* Initializes object.
	* @param array $params
	* @throws Exception
	*/	
    public function init($params)
    {
		try {
			isset($params['image'])  ? $this->image  = $params['image'] : false; 
			isset($params['path'])   ? $this->path   = $params['path']  : false; 
			isset($params['thumb'])  ? $this->thumb  = $params['thumb'] : $this->thumb  = false;
			isset($params['width'])  ? $this->width  = $params['width'] : $this->width  = false;
			isset($params['height']) ? $this->height = $params['height']: $this->height = false;

			$this->receivedMime  = $_FILES[$this->image]['type']; 
			$this->receivedSize  = $_FILES[$this->image]['size'];
			$this->temporaryFile = $_FILES[$this->image]['tmp_name'];
			$this->temporaryFileName = $_FILES[$this->image]['tmp_name'];

			$this->xssSignaturesCount = count($this->xssSignatures); 
			$this->delimiterCount = count($this->delimiters); 
			$this->htmlSignaturesCount = count($this->htmlSignatures); 
			$this->exploitSignaturesCount = count($this->exploitSignatures);
			// throw new Exception("Image not uploaded, please try again.");
		} catch(Exception $e) {
			$this->sessionmessage('Problem initializing:'.$e->getMessage());
		}
    }

	/**
	* Allocates the maximum upload slots.
	* @return mixed boolean, void.
	*/
	private function allocateSlots()
	{
		if(isset($_SESSION['current_slot'])) 
		{ 
			if($_SESSION['current_slot'] >= $this->slots) { 
				$this->sessionmessage('Upload slots exceeded.'); 
				return FALSE; 
				} else { 
				$_SESSION['current_slot']++; 
			} 
		} else { 
			$_SESSION['current_slot'] = 1; 
		} 
	}

	/**
	* Store session messages
    * @param string $value
	* @return void
	*/ 
	private function sessionmessage($value) 
	{ 
		if(isset($_SESSION['upload_message'])) { 
			array_push($_SESSION['upload_message'],$value);  
		} else { 
			$_SESSION['upload_message'] = array(); 
		} 
	} 

	/**
	* Dumps session messages
	* @return void
	*/	
	private function showmessage() 
	{ 
		if(isset($_SESSION['upload_message'])) { 
			echo "<pre>"; 
			echo "<strong>Message:</strong>\r\n"; 
			foreach($_SESSION['upload_message'] as $message) { 
				echo $message . "\r\n" ; 
			} echo "</pre>"; 
		} 
	} 
	/**
	* Clears session messages
	* @return void
	*/	
	public function clearmessages() 
	{
		$_SESSION['upload_message'] = array(); 
	}

	/**
	* Converts integer file size to human readable.
	* @param size.
	* @return string.
	*/	
	public function convert($size) 
	{ 
		$unit = array('b','kb','mb','gb','tb','pb'); 
		return @round($size / pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
	} 

	/**
	* Sanitizes the image file name and creates a UUID.
	* @return string safefile
	*/	
	public function sanitizeImage()
	{
		// Remove extensions 
		$safefile = str_replace($this->mimes('mimes'),'',trim(basename($this->temporaryFileName))); 

		// Sanitize file name 
		$safefile = preg_replace('/[^a-zA-Z0-9]/','',$safefile) . '-'; 

		// Generate entropy 
		$entropy = array(mt_rand(0,0xffff), 
				mt_rand(0,0xffff), 
				mt_rand(0,0xffff), 
				mt_rand(0,0xffff), 
				mt_rand(0,0xffff), 
				mt_rand(0,0xffff) 
		); 
	 
		shuffle($entropy);  
		// Create pseudo UUID 
		$safefile .= $entropy[0].'-'; 
		$safefile .= $entropy[1].'-'; 
		$safefile .= $entropy[2]; 

		return $safefile;
	}

	/**
	* Performs a preliminary binary scan on the image file source. It checks file headers, markers, EXIF data and footers. It then calls scanSource() for a complete vector source scan.
	* @return boolean
	*/	
	public function binaryScan() 
	{
		// Check image byte markers proceed by parsing the tmp file into a string for inspection. 
		if (function_exists('file_get_contents')) {
			$this->readfile = file_get_contents($this->temporaryFile);
			} else {
			$this->readfile = fread(fopen($this->temporaryFile, 'rb'), filesize($this->temporaryFile));
		}
		
		 if($this->readfile) {  
			// It says it's an specific image, let's check if that is true!
			$chunk = strtolower(bin2hex($this->readfile));
			$normalize = $this->mimes('normalize');

			switch($this->mime) { 
				// We allow for 16 bit padding 
				case $normalize[0]: 
				// GIF marker. 
					if(!preg_match("/474946/msx",substr($chunk,0,16)) && $this->mime == 'image/gif') {
						$this->sessionmessage('GIF marker not detected!'); 
						return FALSE; 
					} 
				break; 
				case $normalize[1]: 
					// JFIF header 
					if(!preg_match("/ff(d8|d9|c0|c2|c4|da|db|dd)/msx",substr($chunk,0,16)) && $this->mime == 'image/jpeg') { 
						$this->sessionmessage('JFIF marker not detected!'); 
							return FALSE; 
							// preg_match('/[{0001}-{0022}]/u', $chunk);
					} 
					// JFIF footer 
					if(!preg_match("/ffd9/",substr($chunk,strlen($chunk)-32,32)) && $this->mime == 'image/jpeg') { 
							$this->sessionmessage('JFIF footer incomplete!'); 
							return FALSE; 
					} 
					// Proceed to read EXIF data, if available. 
					if (function_exists('exif_read_data')) { 
						$exif = exif_read_data($this->temporaryFile, 0, true); 
						if($exif["FILE"]["MimeType"] != $this->mime) { 
							$this->sessionmessage('Mime discrepancy!'); 
							return FALSE; 
						}
					}
				break; 
				case $normalize[2]: 
					// PNG marker 
					if(!preg_match("/504e47/",substr($chunk,0,16)) && $this->mime == 'image/png') {
						$this->sessionmessage('PNG marker not detected!'); 
						return FALSE; 
					} 
				break; 
			} 
			// Do a complete file scan now, since the headers might be bogus. 
			$this->scanSource($chunk);  

		} else { 
		// We can't read. No good, exit. 
		$this->sessionmessage('Unable to read binary data.'); 
		return FALSE; 
		}
	}

	public function mimeScan()
	{
		// Mime-type assignment
		if(in_array($this->receivedMime,$this->mimes('png'))) { 
			$this->mime = 'image/png'; 
			} elseif(in_array($this->receivedMime,$this->mimes('jpg'))) { 
			$this->mime = 'image/jpeg'; 
			} else { 
			$this->mime = $this->receivedMime; // unknown or gif.
		} 
		// Check preliminary mime-type 
		if(!in_array($this->mime,$this->mimes('allowed'))) { 
			$this->sessionmessage('Mime-type not allowed.'); 
			return FALSE; 
		} 
		// Check allowed image size. 
		if($this->receivedSize > self::MAXFILESIZE) { 
			$this->sessionmessage('Upload size ceiling reached.'); 
			return FALSE; 
		} 
	}

	/**
	* Performs a complete recursive source scan. It aims at finding vulnerabilities by signature comparison.
	* @param string 
	* @return boolean
	*/	
	public function scanSource($string)
	{  
			if(is_array($string)) { 
				array_map('scanSource',$string); 
				// we are recursive. 
				} else {  
					// Scan string from a gif, jpg, png or apng (animated png) temporary source. 
					// For speed, we only try to find delimiters in the first test. 
					if(preg_match("/(3c|c2bc|2575|5c75|253363|26233630|50413d3d|2b4144772d)([a-z-0-9]|5c3f|25|23|5c2b41434d2d|3c|21|5c24|5c2a|40)/mx",$string)) { 
		 
						// Found a delimiter, suggesting that we might got a malicious file. This is not certain, because some binary data can contain delimiters for mapping. 
						// Continue for two deep scans to mitigate any false positives.
						for($i=0; $i < $this->xssSignaturesCount; $i++) { 
							// Run the delimiter matrices 
							for($j=0; $j < $this->delimiterCount; $j++) { 
								if(stristr($string, $this->xssSignatures[$i].$this->delimiters[$j]) !== FALSE) { 
								// Found something, increment our sieve. 
								$this->sieve++; 
								break; 
								} 
							}	 
						 // Run the html signatures 
						for($k=0; $k < $this->htmlSignaturesCount; $k++) { 
							if(stristr($string, $this->xssSignatures[$i].$this->htmlSignatures[$k]) !== FALSE) { 
								// Found something, increment our sieve.
								$this->sieve++; 
							break; 
							} 
						} 
					}  

			if($this->sieve >= 1) { 
				$this->sessionmessage('Upload sieve contains malicious data!'); 
				return FALSE; // We got one or more hit. returning. 
			} else { 
				// Continue one final test and inspect unsafe keywords. We found delimiters, so something seems wrong. Figure out what is.  
					for($i=0; $i < $this->exploitSignaturesCount; $i++) { if(stristr($string, $this->exploitSignatures[$i]) !== FALSE) { 
					// We found something, increment our sieve. 
					$this->sieve++; 
					break; 
					} 
				} 
				if($this->sieve >= 1) { 
					// 99,99% certain this file is malicious. Returning to abort. 
					$this->sessionmessage('Upload sieve contains malicious data.'); 
					return FALSE; 
					} else { 
					// Tests passed, continue for re-sampling. 
					return TRUE; 
				} 
			}  
			} else { 
			// We return, since no delimiter has been found. 
			return TRUE;
			} 
		} 
	}

	public function processImage() 
	{
		$safefile  = $this->sanitizeImage();
		$normalize = $this->mimes('normalize');

		list($w, $h) = getimagesize($this->temporaryFileName); 
		// If thumbnail, get new size. 
		if($this->thumb == true) { 
			$this->new_width  = $this->width; 
			$this->new_height = $this->height; 
			} else { 
			$this->new_width  = $w; 
			$this->new_height = $h; 
		} 

		// Check if GD is available 
		$gdcheck = gd_info(); 
		$gd = TRUE; 

		// Re-sample. 
		switch($this->mime) { 
			case $normalize[0]:  
			if(isset($gdcheck["GIF Create Support"]) == true) { 
				$ext = '.gif'; 
				$image = imagecreatefromgif($this->temporaryFileName); 
				$resampled = imagecreatetruecolor($this->new_width, $this->new_height); 
				imagecopyresampled($resampled, $image, 0,0,0,0, $this->new_width, $this->new_height, $w, $h); 
				imagegif($resampled, $this->path . $safefile . $ext); 
				$endsize = filesize($this->path . $safefile . $ext); 
			} else { 
			$gd = FALSE; 
			}   
			break;  
			case $normalize[1]: 
			if(isset($gdcheck["JPG Support"]) == true || isset($gdcheck["JPEG Support"]) == true) { 
				$ext = '.jpg'; 
				$image = imagecreatefromjpeg($this->temporaryFileName); 
				$resampled = imagecreatetruecolor($this->new_width, $this->new_height); 
				imagecopyresampled($resampled, $image, 0,0,0,0, $this->new_width, $this->new_height, $w, $h); 
				imagejpeg($resampled, $this->path . $safefile . $ext, 100); 
				$endsize = filesize($this->path . $safefile . $ext); 
			} else { 
			$gd = FALSE; 
			}  
			break;  
			case $normalize[2]:  
			if(isset($gdcheck["PNG Support"]) == true) { 
				$ext = '.png'; 
				$resampled = imagecreatetruecolor($this->new_width, $this->new_height); 
				$image = imagecreatefrompng($this->temporaryFileName); 
				imagealphablending($image, true); 
				imagesavealpha($image, true); 
				imagecopyresampled($resampled, $image, 0,0,0,0, $this->new_width, $this->new_height, $w, $h); 
				imagepng($resampled, $this->path . $safefile . $ext, 100); 
				$endsize = filesize($this->path  . $safefile . $ext); 
			} else { 
			$gd = FALSE; 
			}
		break;  
		default: 
			$this->sessionmessage('Unsupported format'); 
			return FALSE; 
		} 
		 
		if($gd == FALSE || $endsize == 0) { 
			move_uploaded_file($this->temporaryFileName, $this->path . $safefile . '-verbatim-'. $ext); 
			$this->sessionmessage('GD unsupported, but image processed.'); 
			return TRUE; 
			} else { 
			imagedestroy($resampled); 
			return TRUE; 
		}
	}

	public function mimes($which)
	{
		switch($which) {
		case 'allowed':
			return array( 
			'image/gif', 
			'image/x-gif', 
			'image/agif', 
			'image/x-png', 
			'image/png', 
			'image/a-png', 
			'image/apng', 
			'image/jpg', 
			'image/jpe', 
			'image/jpeg', 
			'image/pjpeg', 
			'image/x-jpeg' 
			); 
		break;
		case 'png':
			return array( 
			'image/x-png', 
			'image/png', 
			'image/a-png', 
			'image/apng' 
			); 
		break;
		case 'jpg':
			return array( 
			'image/jpg', 
			'image/jpe', 
			'image/jpeg', 
			'image/pjpeg', 
			'image/x-jpeg' 
			); 
		break;
		case 'mimes':
			return array( 
			'.gif', 
			'.jpg', 
			'.png', 
			'.apng' 
			); 
		break;
		case 'normalize':
			return array( 
			'image/gif', 
			'image/jpeg', 
			'image/png' 
			); 
		break;
		}
	}

	/**
	* Matrices
	* The matrices contain vectors in hex format, which will be compared against a hex conversion of the binary source.
	* We encoded the vectors to avoid false positives on computers and because it looks neater. 
	* To see what the vectors/signatures contain, simply 'hex2bin($string)' them in a PHP foreach loop.
	*/

	/**
	* Encoded delimiter signatures, a vector array to detect XSS, CSRF and SQLi vulnerabilities.
	* @var array
	*/
	public $delimiters = array(
				'3f706870',
				'6a7370',
				'262378',
				'262330',
				'23212f',
				'40696d',
				'2f2a',
				'3c3c',
				'253d',
				'2521',
				'2540',
				'5c25',
				'3f3d',
				'3f2f'
			);

	/**
	* Encoded HTML,a vector array to detect XSS.
	* @var array
	*/		
	public $xssSignatures = array(
				'c2bc',
				'2575',
				'5c75',
				'253363',
				'26233630',
				'50413d3d',
				'2b4144772d'
			);

	/**
	* Encoded UTF7 virus/exploit signatures, a vector array to detect XSS.
	* @var array
	*/
	public $utf7Signatures = array(
				'2b4146732d',
				'2b4148302d',
				'2b4148732d',
				'2b4144302d',
				'2b4146302d',
				'2b4144732d',
				'2b4144342d',
				'2b4144772d',
				'2b4143492d',
				'2b4147412d',
				'2b4146382d',
				'2b41434d2d',
				'2b4145412d',
				'2b4143452d',
				'2b4143512d',
				'2b4143552d',
				'2b41436f2d',
				'2b4143592d',
				'2b4146342d',
				'2b4148342d'
			);

	/**
	* Encoded vulnerability vector array to detect HTML Injection.
	* @var array
	*/
	public $htmlSignatures = array(
				'6576656e74736f75726365',
				'626c6f636b71756f7465',
				'66696763617074696f6e',
				'7465787461726561',
				'696e6966696e7479',
				'6e6f736372697074',
				'6461746167726964',
				'646174616c697374',
				'21646f6374797065',
				'6b6579626f617264',
				'6f707467726f7570',
				'70726f6772657373',
				'6669656c64736574',
				'636f6c67726f7570',
				'636f6d6d616e64',
				'6973696e646578',
				'6163726f6e796d',
				'64657461696c73',
				'6267736f756e64',
				'6f7665726c6179',
				'73656374696f6e',
				'73756d6d617279',
				'61727469636c65',
				'63617074696f6e',
				'61646472657373',
				'646f6374797065',
				'666967757265',
				'686561646572',
				'6f7074696f6e',
				'666f6f746572',
				'6f626a656374',
				'696672616d65',
				'706572736f6e',
				'737061636572',
				'6f7574707574',
				'736372697074',
				'7374726f6e67',
				'627574746f6e',
				'63616e766173',
				'736f75726365',
				'6d7374796c65',
				'6170706c6574',
				'6867726f7570',
				'6b657967656e',
				'73656c656374',
				'6c6567656e64',
				'706172616d',
				'72616e6765',
				'6c6162656c',
				'696e707574',
				'71756f7465',
				'7468656164',
				'766964656f',
				'6d65746572',
				'6c6162656c',
				'7374796c65',
				'6d6f766572',
				'656d626564',
				'617564696f',
				'7469746c65',
				'74626f6479',
				'736d616c6c',
				'74666f6f74',
				'6173696465',
				'7461626c65',
				'73616d70',
				'7370616e',
				'72756279',
				'74696d65',
				'68656164',
				'63697465',
				'636f6465',
				'61757468',
				'666f726d',
				'6d726f77',
				'626f6479',
				'61626272',
				'61726561',
				'62617365',
				'6d617271',
				'6c616e67',
				'63726564',
				'6c696e6b',
				'6d61726b',
				'6d656e75',
				'6d657461',
				'73706f74',
				'68746d6c',
				'6d617468',
				'6e6f7465',
				'62616e',
				'776272',
				'616262',
				'666967',
				'766172',
				'737667',
				'6b6264',
				'6e6176',
				'6d6170',
				'64666e',
				'6b6264',
				'696e73',
				'737570',
				'696d67',
				'64656c',
				'646976',
				'737562',
				'62646f',
				'636f6c',
				'707265',
				'6832',
				'666e',
				'6833',
				'6d6f',
				'6262',
				'656d',
				'646c',
				'6c68',
				'6474',
				'6272',
				'6464',
				'6831',
				'6872',
				'7472',
				'7474',
				'7270',
				'7468',
				'7464',
				'7274',
				'6834',
				'756c',
				'6836',
				'6835',
				'215b',
				'6c69',
				'6f6c',
				'212d',
				'61',
				'62',
				'70',
				'71',
				'69'
				);

	/**
	* Encoded virus/exploit signatures, a vector array for GNU/Linux command injection.
	* @var array
	*/
	public $exploitSignatures = array(
				'24485454505f53455353494f4e5f56415253',
				'24485454505f524551554553545f56415253',
				'6d6f76655f75706c6f616465645f66696c65',
				'66696c655f6765745f636f6e74656e7473',
				'66696c655f7075745f636f6e74656e7473',
				'24485454505f434f4f4b49455f56415253',
				'24485454505f434f4f4b49455f56415253',
				'24485454505f5345525645525f56415253',
				'6469736b5f746f74616c5f7370616365',
				'70726f635f6765745f20737461747573',
				'7365745f66696c655f627566666572',
				'6469736b5f667265655f7370616365',
				'6573636170657368656c6c617267',
				'24485454505f4745545f56415253',
				'636c656172737461746361636865',
				'6573636170657368656c6c636d64',
				'70726f635f7465726d696e617465',
				'24485454505f454e565f56415253',
				'70617273655f696e695f66696c65',
				'6469736b667265657370616365',
				'75706c6f616465645f66696c',
				'636f6e74656e742d74797065',
				'66696c655f657869737473',
				'65786563757461626c65',
				'687474702d6571756976',
				'70726f635f636c6f7365',
				'7368656c6c5f65786563',
				'6765745f6c6f61646564',
				'46494c45494e464f5f',
				'66696c656d74696d65',
				'667061737374687275',
				'667472756e63617465',
				'777269746561626c65',
				'66696c657065726d73',
				'66696c656f776e6572',
				'66696c6567726f7570',
				'66696c65696e6f6465',
				'70726f635f6f70656e',
				'66696c656174696d65',
				'66696c656374696d65',
				'245f53455353494f4e',
				'736574636f6f6b6965',
				'70726f635f6e696365',
				'245f52455155455354',
				'7661725f64756d70',
				'73657373696f6e5f',
				'66696c6573697a65',
				'726561646c696e6b',
				'245f534552564552',
				'7265616466696c65',
				'70617468696e666f',
				'7265616c70617468',
				'66696c6574797065',
				'7265616461626c65',
				'6c6f636174696f6e',
				'66756e6374696f6e',
				'7061737374687275',
				'7772697461626c65',
				'626173656e616d65',
				'646f63756d656e74',
				'24474c4f42414c53',
				'245f434f4f4b4945',
				'6c696e6b696e666f',
				'66707574637376',
				'666e6d61746368',
				'706870696e666f',
				'696e695f676574',
				'696e695f736574',
				'73796d6c696e6b',
				'6469726e616d65',
				'746d7066696c65',
				'245f46494c4553',
				'7265706c616365',
				'7669727475616c',
				'74656d706e616d',
				'696e636c756465',
				'72657175697265',
				'66676574637376',
				'6c6368677270',
				'6c63686f776e',
				'72656e616d65',
				'77696e646f77',
				'657363617065',
				'636f6f6b6965',
				'756e6c696e6b',
				'70636c6f7365',
				'726577696e64',
				'24504154485f',
				'667772697465',
				'626173653634',
				'66666c757368',
				'667363616e66',
				'64656c657465',
				'666765747373',
				'73797374656d',
				'66636c6f7365',
				'706870637265',
				'726d646972',
				'6674656c6c',
				'66696c655f',
				'6667657463',
				'756d61736b',
				'63686d6f64',
				'6368677270',
				'7768696c65',
				'63686f776e',
				'666f637573',
				'706f70656e',
				'6d6f757365',
				'746f756368',
				'6667657473',
				'6672656164',
				'666f70656e',
				'6670757473',
				'245f454e56',
				'6673746174',
				'666c6f636b',
				'667365656b',
				'756e736574',
				'6d6b646972',
				'6c73746174',
				'245f474554',
				'6576616c',
				'676c6f62',
				'6674705f',
				'7061636b',
				'65786563',
				'66696c65',
				'6d61696c',
				'6c6f6164',
				'66656f66',
				'73746174',
				'7068705f',
				'6c696e6b',
				'6c696e6b',
				'626c7572',
				'636f7079',
				'66696c65',
				'726567',
				'646972',
				'6f625f'
				);

}

?>
