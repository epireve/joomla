<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version		3.2.11 September 8, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('GANTRY_VERSION') or die();
gantry_import('core.gantryfeature');


/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureTouchMenu extends GantryFeature {
    var $_feature_name = 'touchmenu';

    function isEnabled() {
        global $gantry;
		if (!isset($gantry->browser)) return false;
		if ($gantry->browser->platform != 'iphone' && $gantry->browser->platform != 'ipad' && $gantry->browser->platform != 'android') return false;
		
        $menu_enabled = $gantry->get('touchmenu-enabled');
		
		$prefix = $gantry->get('template_prefix');
		$cookiename = $prefix.$gantry->browser->platform.'-switcher';
		$cookie = $gantry->retrieveTemp('platform', $cookiename);


        if (1 == (int)$menu_enabled && $cookie == 1 && $gantry->get($gantry->browser->platform.'-enabled')) return true;
        return false;
    }
	function isOrderable(){
		return false;
	}
	
	function init() {
        global $gantry;
		$selected_menu = $gantry->get('menu-type');
		
        if ($gantry->get('iphone-enabled') && $gantry->browser->platform == 'iphone' || $gantry->browser->platform == 'ipad' || $gantry->browser->platform == 'android') {
		    $position = $gantry->get('touchmenu-position', 'mobile-navigation');
			//$gantry->set('menu-type', 'touchmenu');
			$gantry->set('touchmenu-position', $position);
            $gantry->set('touchmenu-theme', 'touch');
			$gantry->addInlineScript("var animation = '" . $gantry->get('touchmenu-animation', 'cube') . "';");
			$gantry->addScript('imenu.js');
		}
	}
    
    function isInPosition($position){
        if ($this->getPosition() == $position) return true;
        return false;
    }

	
	function render($position="") {


        global $gantry;


		if ($gantry->browser->platform != 'iphone' && $gantry->browser->platform != 'ipad' && $gantry->browser->platform != 'android') return false;
	    gantry_import('facets.menu.gantrymenu');

        $params = $gantry->getParams($this->_feature_name, true);
        $module_params = '';
        foreach($params as $param_name => $param_value){
            $module_params .=  $param_name."=". $param_value['value']."\n";
        }
        $passing_params = new JParameter($module_params);
        $gantrymenu = GantryMenu::getInstance($passing_params);

        return $gantrymenu->render($passing_params);

	}
}