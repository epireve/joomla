<?php

/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CMyBlog extends JObject
{
	var $config	= null;
	
	/**
	 * Return reference to the current object
	 * 	 
	 * @return	object		JParams object	 
	 */
	public function &getInstance()
	{
		static $instance = false;

		if(!$instance)
		{
			jimport('joomla.filesystem.file');
			
			// @rule: Test if My Blog really exists.
			$file	= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_myblog' . DS . 'config.myblog.php';

			if( JFile::exists($file) )
			{
				require_once( $file );

				$instance			= new CMyBlog();
				$instance->config	= new MYBLOG_Config();
			}
		}
		
		return $instance;
	}
	
	/**
	 * Get the status if user is allowed to post
	 * Return: boolean
	 **/
	public function userCanPost( $id = 0 )
	{
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'functions.myblog.php' );
		return myGetUserCanPost( $id );
	}
	
	/**
	 * Retrieves the correct Itemid for My Blog
	 **/	 
	public function getItemId()
	{
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_myblog' . DS . 'functions.myblog.php' );
		
		return myGetItemId();
	}
}
