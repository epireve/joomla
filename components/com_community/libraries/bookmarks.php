<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class CBookmarks
{
	var $_bookmarks		= array();
	var $currentURI	= null;
	
	public function CBookmarks( $currentURI )
	{
		$this->currentURI	= urlencode( $currentURI );
		$this->_addDefaultBookmarks();
	}
	
	public function _addDefaultBookmarks()
	{
		$imageURL	= JURI::root() . 'components/com_community/templates/default/images/bookmarks/';
		
		$this->add( 'Facebook' , 'facebook' , 'http://www.facebook.com/sharer.php?u={uri}' );
		$this->add( 'del.icio.us' , 'delicious' , 'http://delicious.com/save?url={uri}' );
		$this->add( 'Digg' , 'digg' , 'http://digg.com/submit?phase=2&amp;url={uri}' );
		$this->add( 'Windows Live' , 'live' , 'https://skydrive.live.com/sharefavorite.aspx%2f.SharedFavorites?url={uri}' );
		$this->add( 'Stumbleupon' , 'stumbleupon' , 'http://www.stumbleupon.com/submit?url={uri}' );
		$this->add( 'Furl' , 'furl' , 'http://furl.net/storeIt.jsp?u={uri}' );
		$this->add( 'Blinklist' , 'blinklist' , 'http://blinklist.com/blink?u={uri}' );
		$this->add( 'G Bookmarks' , 'google' , 'http://www.google.com/bookmarks/mark?op=edit&bkmk={uri}' );
		$this->add( 'Diigo' , 'diigo' , 'http://secure.diigo.com/post?url={uri}' );
		$this->add( 'My Space' , 'myspace' , 'http://www.myspace.com/Modules/PostTo/Pages/?l=3&u={uri}' );
		$this->add( 'Twitter' , 'twitter' , 'http://twitter.com/home?status={uri}' );
		$this->add( 'Xanga' , 'xanga' , 'http://www.xanga.com/private/editorx.aspx?u={uri}' );
		$this->add( 'Bebo' , 'bebo' , 'http://www.bebo.com/c/share?Url={uri}' );
		$this->add( 'Twine' , 'twine' , 'http://www.twine.com/bookmark/basic?u={uri}' );
		$this->add( 'Blogmarks' , 'blogmarks' , 'http://blogmarks.net/my/new.php?mini=1&url={uri}' );
		$this->add( 'Faves' , 'faves' , 'http://faves.com/Authoring.aspx?u={uri}' );
		$this->add( 'AIM' , 'aim' , 'http://share.aim.com/share/?url={uri}' );
		$this->add( 'Technorati' , 'technorati' , 'http://www.technorati.com/faves?add={uri}' );
		$this->add( 'LinkedIn' , 'linkedin' , 'http://www.linkedin.com/shareArticle?mini=true&url={uri}' );
		$this->add( 'Y! Bookmarks' , 'ybookmarks' , 'http://bookmarks.yahoo.com/toolbar/savebm?opener=tb&u={uri}' );
		$this->add( 'Newsvine' , 'newsvine' , 'http://www.newsvine.com/_tools/seed&save?popoff=0&u={uri}' );
	}
	
	public function getTotalBookmarks()
	{
		return count($this->_bookmarks );
	}
	
	/**
	 * Add sharing sites into bookmarks
	 * @params	string	$providerName	Pass the provider name to be displayed
	 * @params	string	$imageURL	 	 Image that needs to be displayed beside the provider
	 * @params	string	$apiURL			Api URL that JomSocial should link to	 
	 **/	 
	public function add( $providerName , $className , $apiURL )
	{
		$apiURL				= CString::str_ireplace( '{uri}' , $this->currentURI , $apiURL );
		$obj				= new stdClass();
		$obj->name			= $providerName;
		$obj->className		= $className;
		$obj->link			= $apiURL;
		
		$this->_bookmarks[ JString::strtolower( $providerName ) ]	= $obj;
	}

	/**
	 * Remove sharing site from bookmarks
	 * @params	string	$providerName	Pass the provider name to be displayed
	 **/
	public function remove( $providerName )
	{
		$providerName	= JString::strtolower( $providerName );
		
		if( isset( $this->_bookmarks[ $providerName ] ) )
		{
			unset( $this->_bookmarks[ $providerName ] );
			return true;
		}
		return false;
	}
	
	public function getBookmarks()
	{
		return $this->_bookmarks;
	}
	
	public function getHTML()
	{
		$config	= CFactory::getConfig();
		
		if( $config->get('enablesharethis') )
		{
			$tmpl	= new CTemplate();
			
			$tmpl->set( 'uri' , $this->currentURI );
			return $tmpl->fetch( 'bookmarks' );
		}
		else
		{
			return '';
		}
	}
}