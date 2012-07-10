<?php
/**
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class CLimitsLibrary
{
	public function exceedDaily( $view , $userId = null )
	{
		$my		= CFactory::getUser( $userId );
		
		// Guests shouldn't be even allowed here.
		if( $my->id == 0 )
		{
			return true;
		}

		$view		= JString::strtolower( $view );
		
		// We need to include the model first before using ReflectionClass so that the model file is included.
		$model		= CFactory::getModel( $view );
		
		// Since the model will always return a CCachingModel which is a proxy,
		// for the real model, we can't really test what type of object it is.
		$modelClass	= 'CommunityModel' . ucfirst( $view );

		$reflection	= new ReflectionClass( $modelClass );
		if( !$reflection->implementsInterface( 'CLimitsInterface' ) )
		{
			return false;
		}
		
		
		$config		= CFactory::getConfig();
		$total		= $model->getTotalToday( $my->id );
		$max		= $config->getInt( 'limit_' . $view . '_perday' );

		return ( $total >= $max && $max != 0 );
	}
}