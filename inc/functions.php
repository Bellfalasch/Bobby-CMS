<?php
	// Get correct ip even if user is on a proxy
	// - http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
	function getRealIpAddr() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		} 
		return $ip;
	}

	// If the array sent in contains any items an error-block will be printed out.
	function outputErrors($errors)
	{
		if (!empty($errors)) {
			echo "
				<div class='errors'>
					<h4>Something went wrong:</h4>
					<ul>";
			foreach( $errors as $err_row ) {
				echo "
						<li><i>&times;</i> ", $err_row, "</li>";
			}
			echo "
					</ul>
				</div>";
		}
	}

	// If the array sent in contains any items an error-block will be printed out.
	function printDebugger()
	{
		global $SYS_debug;
		$debug = $SYS_debug;

		if (!empty($debug)) {
			echo "
				<div class='debugger'>
					<h4>DEBUGGER:</h4>
					<ul>";
			foreach( $debug as $debug_row ) {
				echo "
						<li><i>&times;</i> ", $debug_row, "</li>";
			}
			echo "
					</ul>
				</div>";
		}
	}

	// Fire an error which easily can be checked for and/or printed out.
	function pushError($string)
	{
		global $SYS_errors;
		array_push($SYS_errors, $string);
	}

	// Put a post in the debug-array
	function pushDebug($string)
	{
		global $SYS_debug;
		array_push($SYS_debug, $string);
	}

	// TRANSACTIONS:

	// Print any transactions-error from the database.
	function printError_tran()
	{
		global $SYS_errors_tran;
		outputErrors($SYS_errors_tran);
	}

	// Unique function for filling up on MySQL TRANSACTON-errors
	function pushError_tran($string)
	{
		global $SYS_errors_tran;
		array_push($SYS_errors_tran, $string);
	}


	/**
	* Returns day name and month name in Norwegian instead of English on an English system.
	*
	* @param string $what	What name do you want extracted? D = Day, M = Month (supports lowercase as well)
	* @param date $thedate	The date (in correct dateformat) you want to extract name from.
	* @return string $what
	*/
	function strDateToNorwegian($what, $thedate)
	{
		// From day number set day name
		$daynum = date("N", strtotime($thedate) );
		switch($daynum) {
			case 1: $day = "Mandag"; break;
			case 2: $day = "Tirsdag"; break;
			case 3: $day = "Onsdag"; break;
			case 4: $day = "Torsdag"; break;
			case 5: $day = "Fredag"; break;
			case 6: $day = "Lørdag"; break;
			case 7: $day = "Søndag"; break;
			default: $day = "Ukjent"; break;
		}

		// From month number set month name
		$monthnum = date("n", strtotime($thedate) );
		switch($monthnum) {
			case 1: $month = "Januar"; break;
			case 2: $month = "Februar"; break;
			case 3: $month = "Mars"; break;
			case 4: $month = "April"; break;
			case 5: $month = "Mai"; break;
			case 6: $month = "Juni"; break;
			case 7: $month = "Juli"; break;
			case 8: $month = "August"; break;
			case 9: $month = "September"; break;
			case 10: $month = "Oktober"; break;
			case 11: $month = "November"; break;
			case 12: $month = "Desember"; break;
			default: $month = "Ukjent"; break;
		}

		// Depending on what we wanted to get from the function return that
		$returnthis = '';
		switch( strtoupper($what) )
		{
			case 'M': $returnthis = $month; break;
			case 'D': $returnthis = $day; break;
		}
		return $returnthis;
	}


	function randomAlphaNum($length)
	{
		$rangeMin = pow(36, $length-1); //smallest number to give length digits in base 36 
		$rangeMax = pow(36, $length)-1; //largest number to give length digits in base 36 
		$base10Rand = mt_rand($rangeMin, $rangeMax); //get the random number 
		$newRand = base_convert($base10Rand, 10, 36); //convert it 

		return $newRand; //spit it out 
	}

	// Native PHP 5 validation of e-mail. If on older system use the commented out line.
	function isValidEmail($email)
	{
		//if(preg_match("/[.+a-zA-Z0-9_-]+@[a-zA-Z0-9-]+.[a-zA-Z]+/", $email) > 0)
		if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
			return true;
		else
			return false;
	}

	// Simple fetch form-field with isset-check. Always returns a string. Never empty or null.
	function formGet($field)
	{
		if (isset($_POST[$field]))
			return removeHtmlEntities( trim($_POST[$field]) );
		else
			return '';
	}

	// Simple fetch get/url-parameter with isset-check. Always returns a string. Never empty or null.
	function qsGet($field)
	{
		if (isset($_GET[$field]))
			return removeHtmlEntities( trim($_GET[$field]) );
		else
			return '';
	}

	// Simple validation of length of a string (support for form handling).
	function isValidLength($string, $min, $max)
	{
		if (mb_strlen($string,'UTF-8') >= $min && mb_strlen($string,'UTF-8') <= $max)
			return true;
		else
			return false;
	}

	// Return true if positive number, or false if negative number!
	function sign( $number ) { 
		return ( $number > 0 ) ? 1 : ( ( $number < 0 ) ? -1 : 0 );
	}

	// Just somethink quick I used for some special SQL-need where I need to remove a lot of html formating that was common.
	function removeHtmlEntities($string) {
		$tmp = $string;
		$tmp = str_replace('&amp;','&',$tmp);
		$tmp = str_replace('&quot;','"',$tmp);
		$tmp = str_replace('&#039;',"'",$tmp);
		$tmp = str_replace('&apos;',"'",$tmp);
		$tmp = str_replace('&lt;','<',$tmp);
		$tmp = str_replace('&gt;','>',$tmp);

		return $tmp;
	}

	// Useful functions to be used for image uploading that need not be changed.
	function getExtension ($str) {
		$i = strrpos ($str, '.');
		if ( !$i ) { return ''; }
		$l = strlen ($str) - $i;
		$ext = substr ($str, $i + 1, $l);
		return strtolower ($ext);
	}


	function createPic ($img_name, $filename, $new_w, $new_h) {
		 
		$ext = getExtension ($img_name);
		
		if ( !strcmp ('jpg', $ext) || !strcmp ('jpeg',$ext) ) {
			$src_img = imagecreatefromjpeg ($img_name);
		}
		if( !strcmp ('png', $ext) ) {
			$src_img = imagecreatefrompng ($img_name);
			imagealphablending ($src_img, false);
			imagesavealpha ($src_img, true);
		}
		if( !strcmp ('gif', $ext) ) {
			$src_img = imagecreatefromgif ($img_name);
			imagealphablending ($src_img, false);
			imagesavealpha ($src_img, true);
		}
		
		
		$old_x = imagesx ($src_img);
		$old_y = imagesy ($src_img);
			
		$ratio1 = $old_x / $new_w;
		$ratio2 = $old_y / $new_h;
		
		if ( $ratio1 > $ratio2 )	{
			$thumb_w = $new_w;
			$thumb_h = $old_y / $ratio1;
		} else {
			$thumb_h = $new_h;
			$thumb_w = $old_x / $ratio2;
		}
		
		$dst_img = imagecreatetruecolor ($thumb_w,$thumb_h);
		
		imagecopyresampled ($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y); 
		
		if ( !strcmp ('png', $ext) ) {
			imagepng ($dst_img, $filename, 0);
		}
		elseif ( !strcmp ('gif', $ext) ) {
			imagegif ($dst_img, $filename);
		} else {
			imagejpeg ($dst_img, $filename, 100);
		}
		
		imagedestroy ($dst_img); 
		imagedestroy ($src_img); 
	}


	function setTransparency($new_image,$image_source) {
			   
		$transparencyIndex = imagecolortransparent($image_source);
		$transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);
		
		if ($transparencyIndex >= 0) {
			$transparencyColor    = imagecolorsforindex($image_source, $transparencyIndex);   
		}
				   
		$transparencyIndex    = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
		imagefill($new_image, 0, 0, $transparencyIndex);
		imagecolortransparent($new_image, $transparencyIndex);
			   
	} 


	function cropPic ($cropped_image_name, $image, $width, $height, $start_width, $start_height, $scale){
		
		$ext = getExtension ($image);
		
		$newImageWidth = ceil ($width * $scale);
		$newImageHeight = ceil ($height * $scale);
			
		if ( !strcmp ('gif', $ext) ) {
			$source = imagecreatefromgif ($image);
		}
		elseif( !strcmp ('png', $ext) ) {
			$source = imagecreatefrompng ($image);
		} else {
			# !strcmp ('jpg', $ext) || !strcmp ('jpeg',$ext)
			$source = imagecreatefromjpeg ($image);
		}
		
		$newImage = imagecreatetruecolor ($newImageWidth, $newImageHeight);
		
		setTransparency ($newImage, $source); 
			
		imagecopyresampled ($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
		
		if ( !strcmp ('png', $ext) ) {
			imagepng ($newImage, $cropped_image_name, 0);
		}
		elseif ( !strcmp ('gif', $ext) ) {
			imagegif ($newImage, $cropped_image_name);
		} else {
			imagejpeg ($newImage, $cropped_image_name, 100);
		}

		return $cropped_image_name;
	}


	function getHeight ($image) {
		$sizes = getimagesize ($image);
		$height = $sizes[1];
		return $height;
	}


	function getWidth ($image) {
		$sizes = getimagesize ($image);
		$width = $sizes[0];
		return $width;
	}

	function Strip_filename ($filename) {
		$result = null;
		$temp    = $filename;
		$temp    = strtolower($temp);
		
		$find    = array ('Ã¦' ,  'Ã¸', 'Ã¥' , 'Ã†' , 'Ã˜' , 'Ã…' , '  ', ' ');
		$replace = array ('ae', 'oe', 'aa', 'ae', 'oe', 'aa', ' ' , '_');
		$temp    = str_replace ($find, $replace, $temp);
			
		for ( $i = 0; $i < strlen ($temp); $i++ ) {
			if ( preg_match ('([0-9]|[a-z]|_)', $temp[$i]) ) {
				$result = $result . $temp[$i];
			}
		}
		
		$find    = array ('__');
		$replace = array ('_',);
		$result    = str_replace ($find, $replace, $result);
		
		return $result;
	}

	function sendamail($from_name, $from_mail, $title, $content, $to_mail)
	{
		$headers  = 'From: ' . $from_name . ' <' . $from_mail . '>' . "\r\n";
		$headers .= 'Bounce-to:  ' . $from_name . ' <' . $from_mail . '>' . "\r\n";
		$headers .= 'Return-Path: ' . $from_name . ' <' . $from_mail . '>' . "\r\n";
		$headers .= 'Reply-To: ' . $from_name . ' <' . $from_mail . '>' . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
//		$headers .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n";

		$formsent = mail($to_mail, $title, $content, $headers);

		return $formsent;
	}

?>