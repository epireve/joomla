<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version   3.2.11 September 8, 2011
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
class GantryFeatureBelatedPNG extends GantryFeature {
    var $_feature_name = 'belatedPNG';

    function isEnabled(){
        return true;
    }
    function isInPosition($position) {
        return false;
    }

	function isOrderable(){
		return false;
	}
    
	function init() {
        global $gantry;
		
		if ($gantry->browser->name == 'ie' && $gantry->browser->shortversion == '6') {
			$fixes = $gantry->belatedPNG;
			
			$gantry->addScript('belated-png.js');
			$gantry->addInlineScript($this->_belatedPNG($fixes));
		}
	}
	
	function _belatedPNG($fixes) {
		if (!is_array($fixes) || count($fixes) == 0) $fixes = array('.png');
		$fixes = implode("', '", $fixes);
		
		$js = "
			window.addEvent('domready', function() {
				var pngClasses = ['$fixes'];
				pngClasses.each(function(fixMePlease) {
					DD_belatedPNG.fix(fixMePlease);
				});
			});
		";
		
		return $js;
	}
}