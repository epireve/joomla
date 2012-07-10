<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * This is the entry point for the AJAX calls.
 * We filter this out instead so that the AJAX methods can be called within the
 * controller.
 **/
function communityAjaxEntry( $func , $args = null )
{
	// The first index will always be 'admin' to distinguish between admin ajax
	// calls and front end ajax calls.
	// $method[0]	= 'admin'
	// $method[1]	= CONTROLLER
	// $method[2]	= CONTROLLER->METHOD
	$calls		= explode( ',' , $func );

	if( is_array( $calls ) && $calls[0] == 'admin' )
	{
		$func		= $_REQUEST['func'];
		
		$method		= explode( ',' , $func );
		
		$controller	= JString::strtolower( $method[1] );
		
		require_once( JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php' );
		
		$controller	= JString::ucfirst( $controller );

		$controller	= 'CommunityController' . $controller;
		$controller	= new $controller();
		
		$output		= call_user_func_array( array( &$controller , $method[2] ) , $args );

		return $output;
	}
}