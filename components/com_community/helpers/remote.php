<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CRemoteHelper
{
	// Return true if Curl library is installed
	static public function curlExists()
	{
		return function_exists('curl_init');
	}
	
	// Return content of the given url
	static public function getContent($url , $raw = false , $headerOnly = false)
	{
		if (!$url)
			return false;
		
		if (function_exists('curl_init'))
		{
			$ch			= curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, true );
			
			if($raw){
				curl_setopt($ch, CURLOPT_BINARYTRANSFER, true );
			}

			$response	= curl_exec($ch);
			
			$curl_errno	= curl_errno($ch);
			$curl_error	= curl_error($ch);
			
			if ($curl_errno!=0)
			{
				$mainframe	= JFactory::getApplication();
				$err		= 'CURL error : '.$curl_errno.' '.$curl_error;
				$mainframe->enqueueMessage($err, 'error');
			}
			
			$code		= curl_getinfo( $ch , CURLINFO_HTTP_CODE );

			// For redirects, we need to handle this properly instead of using CURLOPT_FOLLOWLOCATION
			// as it doesn't work with safe_mode or openbase_dir set.
			if( $code == 301 || $code == 302 )
			{
				list( $headers , $body ) = explode( "\r\n\r\n" , $response , 2 );
				
				preg_match( "/(Location:|URI:)(.*?)\n/" , $headers , $matches );
				
				if( !empty( $matches ) && isset( $matches[2] ) )
				{
					$url	= JString::trim( $matches[2] );
					curl_setopt( $ch , CURLOPT_URL , $url );
					curl_setopt( $ch , CURLOPT_RETURNTRANSFER, 1);
					curl_setopt( $ch , CURLOPT_HEADER, true );
					$response	= curl_exec( $ch );
				}
			}
			
			
			if(!$raw){
				list( $headers , $body )	= explode( "\r\n\r\n" , $response , 2 );
			}
			
			$ret	= $raw ? $response : $body;
			$ret	= $headerOnly ? $headers : $ret;
			
			curl_close($ch);
			return $ret;
		}
	
		// CURL unavailable on this install
		return false;
	}
	
	// Return result of a POST
	static public function post($url, $data)
	{
		if (!$url && !$data)
			return false;
	
		$response = '';
		if (function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'));
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$response = curl_exec($ch);
			$response_code = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
		}
		else
		{
			$dataLength	= JString::strlen(implode('&', $data));
			$parsedUrl	= parse_url( $url );
			$fp			= fsockopen( $parsedUrl['host'], 80, $errno, $errstr, 30);
	
			if (!$fp)
			{
				return false; // Error
			}
			else
			{
				$out	 = 'POST ' . (isset($parsedUrl['path']) ? $parsedUrl['path'] : '/')
						. (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '')
						. ' HTTP/1.0' . "\r\n";
				$out	.= 'Host: ' . $parsedUrl['host'] . "\r\n";
				$out	.= "Content-Type: application/x-www-form-urlencoded\r\n";
				$out	.= 'Content-Length: ' . $dataLength . "\r\n";
				$out	.= 'Accept-Charset: UTF-8' . "\r\n";
				$out	.= 'Connection: Close' . "\r\n\r\n";
				$out	.= $data;
				fwrite($fp, $out);
				while(!feof($fp)) {
					$response .= fgets($fp, 128);
				}
				fclose($fp);
				if( $contents ) {
					list($headers, $content) = explode( "\r\n\r\n", $contents, 2 );
					$response_code = strpos( $headers, '200 OK' );
				}
			}
		}
		return $response;
	}
}

/**
 * Deprecated since 1.8
 */
function cIsCurlExists()
{
	return CRemoteHelper::curlExists();
}

/**
 * Deprecated since 1.8
 */
function cRemoteGetContent($url)
{
	return CRemoteHelper::getContent( $url );
}

/**
 * Deprecated since 1.8
 */
function cRemotePost($url, $data)
{
	return CRemoteHelper::post( $url , $data );
}