<?php
/**
 * @category 	Library
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );

class CPluginHelper extends JPluginHelper {
	function getPluginPath($type, $plugin = null) {
		if (!$plugin) {
			return JPATH_PLUGINS . DS . $type;
		}
		//Joomla 1.5 and older
		if (C_JOOMLA_15) {
			// joomla 1.5 keeps all plugins in the same plugin type folder
			return JPATH_PLUGINS . DS . $type;
		}
		//Joomla 1.6 and later
			//joomla 1.6 keeps plugin in seperated folders
		return JPATH_PLUGINS . DS . $type . DS . $plugin;
	}
	
	function getPluginURI($type, $plugin = null) {
		if (!$plugin) {
			return '/plugins/' . $type;
		}
		//Joomla 1.5 and older
		if (C_JOOMLA_15) {
			// joomla 1.5 keeps all plugins in the same plugin type folder
			return '/plugins/' . $type;
		}
		//Joomla 1.6 and later
			//joomla 1.6 keeps plugin in seperated folders
		return '/plugins/' . $type . '/' . $plugin;
	}
}

?>
