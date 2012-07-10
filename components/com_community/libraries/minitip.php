<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CMinitip 
{
	/**
	 * Load messaging javascript header
	 */	 	
	public function load()
	{
		static $loaded = false;
		
		if( !$loaded )
		{
			$config	= CFactory::getConfig();
			
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
	
			$js = 'assets/minitip-1.0.js';
			CAssets::attach($js, 'js');

			$css = 'assets/minitip.css';
			CAssets::attach($css, 'css');
		}
	}
}