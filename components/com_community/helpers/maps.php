<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

class CMapsHelper
{
	/**
	 *	Returns an object of data containing user's address information
	 *
	 *	@access	static
	 *	@params	int	$userId
	 *	@return stdClass Object	 	 	 
	 **/	 
	static public function getAddress( $userId )
	{
		$user			= CFactory::getUser( $userId );
		$config			= CFactory::getConfig();

		$obj			= new stdClass();
		$obj->street	= $user->getInfo( $config->get('fieldcodestreet') );
		$obj->city		= $user->getInfo( $config->get('fieldcodecity') );
		$obj->state		= $user->getInfo( $config->get('fieldcodestate') );
		$obj->country	= $user->getInfo( $config->get('fieldcodecountry') );
	
		return $obj;
	}
}