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

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');


/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureInactive extends GantryFeature {
    var $_feature_name = 'inactive';

    function init() {
        global $gantry, $Itemid;

        $enabled = $this->get('enabled');
        if($enabled) {
            $menus = &JSite::getMenu();
            $menu  = $menus->getActive();
            if (null == $menu){
                $menuitem = $this->get('menuitem');
                $menus->setActive($menuitem);
                $Itemid = $menuitem;
            }
        }
    }
}