<?php
/**
 * @package		ZEND library
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');


/** Define some constants that can be used by the system **/
if( !defined( 'ZEND_PATH' ) )
{
	// Get the real system path.
	$system	= rtrim(  dirname( __FILE__ ) , '/' );

	define( 'ZEND_PATH' , $system );
} 

//for pre-install script call during installing zend packages. (J1.6 compatibility)

if (!defined( 'ZEND_INSTALLER' )) {
	define( 'ZEND_INSTALLER' , 1 );
	class plgsystemzendInstallerScript {
	
		public function __construct($instance){
			
		}
		public function preflight($route, $instance) {
			
			if (JFile::exists(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'zend'.DS.'zend.xml')){
				file_put_contents(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'zend'.DS.'zend.xml','<?xml version="1.0" encoding="utf-8"?><install version="1.5" type="plugin" group="system" method="upgrade"></install>');
			}
			return true;
		}
	}
}