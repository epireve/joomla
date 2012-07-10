<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CMessaging 
{
	/**
	 * Load messaging javascript header
	 */	 	
	public function load()
	{
		if( !defined( 'CMESSAGING_LOADED' ) ) 
		{
			$config	= CFactory::getConfig();
			include_once(JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');
			
			$js = 'assets/window-1.0';
			$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
			CAssets::attach($js, 'js');

			$js = 'assets/script-1.2';
			$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
			CAssets::attach($js, 'js');
		
			$css = 'assets/window.css';
			CAssets::attach($css, 'css');

			CFactory::load( 'libraries' , 'template' );
			CTemplate::addStyleSheet( 'style' );
// 			$css = '/templates/'.$config->get('template').'/css/style.css';
// 			CAssets::attach($css, 'css');
		
		}
	}
	
	/**
	 * Get link to popup window 
	 */	 	
	public function getPopup($id)
	{
		CMessaging::load();
		return "joms.messaging.loadComposeWindow('{$id}')";
	}
	
	public function send( $data ) {
		//notifyEmailMessage
	}
}