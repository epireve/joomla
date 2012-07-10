<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );

/**
 * Facebook Namespace Plugin
 */
class plgSystemJomsocialConnect extends JPlugin
{
	function plgSystemJomsocialConnect(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onAfterRender()
	{
		$fbml	= '<html xmlns:fb="http://www.facebook.com/2008/fbml"';
		$html	= JResponse::getBody();
		
		$html	= JString::str_ireplace( '<html' , $fbml , $html );
		
		JResponse::setBody( $html );
	}
}
