<?php
/**
 * @category	Azrul System Helper
 * @package		Azrul System Mambot
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

if( !defined( 'AZRUL_SYSTEM_LIVE' ) )
{
	if(basename(dirname(dirname(dirname(__FILE__)))) == 'plugins'){
	//Joomla 1.5 and above
		define( 'AZRUL_SYSTEM_LIVE' , rtrim( JURI::root() , '/' ) . '/plugins/system' );
	} else {
		define( 'AZRUL_SYSTEM_LIVE' , rtrim( JURI::root() , '/' ) . '/plugins/system/azrul.system' );
	}
}

if( !defined( 'AZRUL_BASE_LIVE' ) )
{
	define( 'AZRUL_BASE_LIVE' , rtrim( JURI::root() , '/' ) );
}

function azrulGetJoomlaVersion()
{
	static $version;

	if( !isset( $version ) )
	{
		$jversion		= new Jversion();
		$helpVersion	= $jversion->getHelpVersion();
		$version		= null;
		
		if( $helpVersion == '.16')
		{
			$version	= '1.6';
		}
		if( $helpVersion == '.15')
		{
			$version	= '1.5';
		}
	}
	return $version;
}
