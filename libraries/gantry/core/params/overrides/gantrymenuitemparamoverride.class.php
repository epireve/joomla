<?php
/**
 * @package   gantry
 * @subpackage core.params
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.params.gantryparamoverride');

/**
 * @package   gantry
 * @subpackage core.params
 */
class GantryMenuItemParamOverride extends GantryParamOverride {
    function populate(){
        global $gantry;

        if ($gantry->currentMenuItem == null){
            if (array_key_exists('inactive-enabled',$gantry->_working_params) && $gantry->_working_params['inactive-enabled']['value']){
                $gantry->currentMenuItem = (int)$gantry->_working_params['inactive-menuitem']['value'];        
            }
            else {
                $gantry->currentMenuItem = $gantry->defaultMenuItem;
            }
        }

        if (!empty($gantry->currentMenuTree)) {
            foreach ($gantry->currentMenuTree as $treeitem) {
                self::_populateSingleItem($treeitem);
                if ($treeitem == $gantry->currentMenuItem){
                    break;
                }
            }
        }
        else {
            self::_populateSingleItem($gantry->currentMenuItem);
        }

    }

    function _populateSingleItem($itemId) {
        global
        $gantry;

        $site = JFactory::getApplication();
        $template = $site->getTemplate(true);
        $menus = &JSite::getMenu();
        $current_menu_item = $menus->getItem($itemId);
        if (empty($current_menu_item )){
            $current_menu_item = $menus->getDefault();
        }


        $menu_params = GantryTemplate::getTemplateParams((int)$current_menu_item->template_style_id);
        $array = $menu_params->toArray();
        $menu_params = new JRegistry();
        $menu_params->loadArray(gantry_flattenParams($array));

        foreach($gantry->_preset_names as $param_name) {
            $menuitem_param_name = $param_name;
            if (in_array($param_name, $gantry->_setbyoverride) && $menu_params->get($menuitem_param_name, null) != null) {
                $param =& $gantry->_working_params[$param_name];
                $menuitem_value = $menu_params->get($menuitem_param_name);
                $menuitem_preset_params = $gantry->_getPresetParams($param['name'],$menuitem_value);
                foreach($menuitem_preset_params as $menuitem_preset_param_name => $menuitem_preset_param_value) {
                    if (!is_null($menuitem_preset_param_value)){
                        $gantry->_working_params[$menuitem_preset_param_name]['value'] = $menuitem_preset_param_value;
                        $gantry->_working_params[$menuitem_preset_param_name]['setby'] = 'menuitem';
                    }
                }
            }
        }
            // set individual values
        foreach($gantry->_param_names as $param_name) {
            $menuitem_param_name = $param_name;
            if (in_array($param_name, $gantry->_setbyoverride) && $menu_params->get($menuitem_param_name, null) != null) {
                $param =& $gantry->_working_params[$param_name];
                $menuitem_value =  $menu_params->get($menuitem_param_name);
                if (!is_null($menuitem_value)){
                    $gantry->_working_params[$param['name']]['value'] = $menuitem_value;
                    $gantry->_working_params[$param['name']]['setby'] = 'menuitem';
                }
            }
        }

    }
}