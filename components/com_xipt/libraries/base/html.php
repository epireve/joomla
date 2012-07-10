<?php
/**
* @copyright	Copyright (C) 2009 - 2009 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @contact		shyam@joomlaxi.com
*/

defined('_JEXEC') or die();


class XiptHtml extends JHTML
{	
	/**
	 * Class loader method
	 *
	 * Additional arguments may be supplied and are passed to the sub-class.
	 * Additional include paths are also able to be specified for third-party use
	 *
	 * @param	string	The name of helper method to load, (prefix).(class).function
	 *                  prefix and class are optional and can be used to load custom
	 *                  html helpers.
	 */
	function _( $type )
	{
		//Initialise variables
		$prefix = 'XiptHtml';
		$file   = '';
		$func   = $type;

		$extraArgs = func_get_args();
		
		// Check to see if we need to load a helper file
		$parts = explode('.', $type);

		switch(count($parts))
		{
			case 3 :
			{
				$prefix		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[0] );
				$file		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[1] );
				$func		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[2] );
			} break;

			case 2 :
			{
				$file		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[0] );
				$func		= preg_replace( '#[^A-Z0-9_]#i', '', $parts[1] );
			} break;
		}

		$className	= $prefix.ucfirst($file);

		if (!class_exists( $className , true ))
		{
			jimport('joomla.filesystem.path');
			
			$xiptHtmlPath	=	XIPT_FRONT_PATH_LIBRARY.DS.'html';
			
			if ($path = JPath::find(self::addIncludePath($xiptHtmlPath), strtolower($file).'.php'))
			{
				require_once $path;

				//if class does not exist at our end then handle it by joomla
				//2nd argument true will autoload class if autoload concept exist
				if (!class_exists( $className , true ))
					return call_user_func_array( array( 'JHTML', '_' ), $extraArgs );
			}
			//if file not found at our end then handle it by joomla
			else
				return call_user_func_array( array( 'JHTML', '_' ), $extraArgs );
		}

		if (is_callable( array( $className, $func ) ))
		{
			$temp = func_get_args();
			array_shift( $temp );
			$args = array();
			foreach ($temp as $k => $v) {
			    $args[] = &$temp[$k];
			}
			return call_user_func_array( array( $className, $func ), $args );
		}
		else
			return call_user_func_array( array( 'JHTML', '_' ), $extraArgs );
	}
}