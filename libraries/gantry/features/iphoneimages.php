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
class GantryFeatureIphoneImages extends GantryFeature {
    var $_feature_name = 'iphoneimages';
	
	function isEnabled() {
		global $gantry;
		
		if (!$gantry->browser) return false;
		
		$prefix = $gantry->get('template_prefix');
		$cookiename = $prefix.$gantry->browser->platform.'-switcher';
		$cookie = $gantry->retrieveTemp('platform', $cookiename);
		
		if ($cookie != '' && !$cookie || !$gantry->get($gantry->browser->platform.'-enabled') || !$this->get('enabled')) return false;
		else return true;
	}

	function init() {
        global $gantry;

		$gantry->addInlineScript($this->_js());

	}
	
	function _js() {
		global $gantry;
		
		$percentage = $this->get('percentage');
		$minWidth = $this->get('minWidth');
		
		return "
			window.addEvent('load', function() {
				var winsize = window.getSize().size;
				var imgs = $$('img').each(function(img) {
					var size = {}, backup = {};
					
					size = {
						width: img.getProperty('width') || img.getStyle('width').toInt() || img.offsetWidth,
						height: img.getProperty('height') || img.getStyle('height').toInt() || img.offsetHeight
					};
					backup = size;
					size = {
						width: size.width - (size.width * ".$percentage." / 100),
						height: size.height - (size.height * ".$percentage." / 100)
					};

					if (size.width > winsize.x) {
						var width = backup.width - (backup.width - winsize.x);
						var height = width * backup.height / backup.width;
						size = {
							width: width - 30,
							height: height - 30
						}
					}
					if (backup.width > ".$minWidth." && backup.width != 0) {
						img.setProperty('width', size.width).setProperty('height', size.height).setStyles(size);
					}
				});
			});
		";
	}

}