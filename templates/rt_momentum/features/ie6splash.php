<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version   1.3 October 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Reaction Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');
/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureIE6Splash extends GantryFeature {
    var $_feature_name = 'ie6splash';
    
    
    function isEnabled(){
    	if ($this->get('enabled')) {
        	return true;
        }
    }
    
    function isInPosition($position) {
        return false;
    }
    function isOrderable(){
        return true;
    }
    
    function init() {
        global $gantry;
        
        if (JRequest::getString('tmpl')!='unsupported' && $gantry->browser->name == 'ie' && $gantry->browser->shortversion == '6') {
            header("Location: ".$gantry->baseUrl."?tmpl=unsupported");
        }
    }
}