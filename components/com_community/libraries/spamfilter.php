<?php
/**
 * @package		JomSocial
 * @subpackage	Library 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

/**
 * Any child spamfilter service that wants to be added, should
 * implement CSpamFilter_Service
 **/  
interface CSpamFilter_Service
{
	public function __construct();
	
	/**
	 * Sets the author name
	 * 
	 * @param	string	$author	The author's name to be screened
	 **/
	public function setAuthor( $author );

	/**
	 * Sets the message data
	 * 
	 * @param	string	$message	The message to be screened	 	 
	 **/
	public function setMessage( $message );

	/**
	 * Sets the url of the item
	 * 
	 * @param	string	$url	The URL or permalink of the item.	 	 
	 **/
	public function setURL( $url = '' );

	/**
	 * Sets the type of the message
	 * 
	 * @param	string	$type	The filtering type.	 
	 **/
	public function setType( $type );

	/**
	 * Sets the author's email address
	 * 
	 * @param	string	$email	The author's email address	 	 
	 **/
	public function setEmail( $email );

	/**
	 * Sets the remote ip address
	 * 
	 * @param	string	$ip	The author's IP address	 	 
	 **/
	public function setIP( $ip );

	/**
	 * Sets the user agent string
	 * 
	 * @param	string	$useragent	The author's user agent string.	 	 
	 **/
	public function setUserAgent( $useragent );

	/**
	 * Sets the user's referrer url.
	 * 
	 * @param	string	$referrer	The author's referrer.
	 **/
	public function setReferrer( $referrer );

	/**
	 * Sets the user agent string
	 * 
	 * @return	boolean	True if content is spam and false otherwise.	 	 
	 **/
	public function isSpam();
}

class CSpamFilter
{
	public function getFilter()
	{
		$config	= CFactory::getConfig();
		$filter	= $config->get( 'antispam_filter' );
		
		CFactory::load( 'libraries' , $filter );
		$class	= 'C' . ucfirst( $filter );
		$filterObject	= false;
		
		if( class_exists( $class ) )
		{
			$filterObject	= new $class();
		}
		
		return $filterObject;
	}
}