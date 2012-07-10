<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CValidateHelper
{
	static public function email($data, $strict = false) 
	{
		$regex = $strict ? '/^([.0-9a-z_-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i' : '/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i'; 
		
		if(preg_match($regex, JString::trim($data), $matches))
		{
			return array($matches[1], $matches[2]); 
		}
		else
		{ 
			return false; 
		} 
	}

	static public function domain($address, $domain)
	{
		$regex = '/^([.0-9a-z_-]+)@'.$domain.'$/i'; 
		
		if(preg_match($regex, JString::trim($address), $matches))
		{
			return true; 
		}
		else
		{ 
			return false; 
		} 		
	}
	static public function alias( $username )
	{
		jimport( 'joomla.filesystem.folder' );
		
		$views		= JFolder::folders( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'views' );
	
		return !in_array( $username , $views );
	}
	
	static public function username( $username )
	{
		// Make sure the username is at least 1 char and contain no funny char
		return (!preg_match( "/[<>\"'%;()&]/i" , $username ) && JString::strlen( $username )  > 0 );
	}
	
	static public function url( $url )
	{
		//$regex = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';
		//$regex = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,6}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
		$regex = '/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_#]+$/i';

		if (preg_match($regex, JString::trim($url), $matches))
		{
			return true;
		}
		else
		{ 
			return false;
		}
	}
	
	/*
	 * Check whether the string is a phone number or not.
	 * Supported US phone number format : 
	 * 1-234-567-8901
	 * 1-234-567-8901 x1234
	 * 1-234-567-8901 ext1234
	 * 1 (234) 567-8901
	 * 1.234.567.8901
	 * 1/234/567/8901
	 * 12345678901 
	 *
	 */
	static public function phone($phone)
	{
		$regex = '/^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/i';
		
		if (preg_match($regex, JString::trim($phone), $matches))
		{ 
			return array($matches[1], $matches[2]); 
		}
		else
		{ 
			return false; 
		}
	}
	
	/*
	 * Check if the length of the string is between the given range
	 * @param : min (minimum length of string)
	 * @param : max (maximum length of string)
	 * @param : string
	 * @return : boolean
	 */
	static public function characterLength( $min, $max, $string ){
		$str_length = strlen($string);
		
		if($min > $max){
			return false;
		}
		
		if($str_length >= $min && $str_length <= $max){
			return true;
		}else{
			return false;
		}	
	}
}