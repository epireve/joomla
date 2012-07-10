<?php
 /**
  * @version   $Id$
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');
 
class GantryFeatureCompositeOverrides extends GantryFeature {
    var $_feature_name = 'compositeoverrides';

    function isEnabled() {
        return true;
    }

    function isInPosition($position) {
        return false;
    }

	function init() {
        if (!defined('ROKCOMMON_LIB_PATH')) define('ROKCOMMON_LIB_PATH', JPATH_SITE . '/libraries/rokcommon');
        if (is_file(ROKCOMMON_LIB_PATH.'/include.php'))
        {
            include(ROKCOMMON_LIB_PATH.'/include.php');
        }
        if (defined('ROKCOMMON') && class_exists('RokCommon_Composite')){
            RokCommon_Composite::addPackagePath('mod_rokgallery', JPATH_SITE . '/templates/rt_momentum/overrides/mod_rokgallery/templates',20);
        }
	}

}