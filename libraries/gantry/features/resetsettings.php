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
class GantryFeatureResetSettings extends GantryFeature {
    var $_feature_name = 'resetsettings';
	
	function render($position="") {
		global $gantry;
	    ob_start();
	    ?>
		<div class="clear"></div>
		<span  id="gantry-resetsettings">
			[ <a href="<?php echo JROUTE::_($gantry->addQueryStringParams($gantry->getCurrentUrl($gantry->_setbyurl),array('reset-settings'=>''))); ?>"><?php echo $this->get('text'); ?></a> ]
		</span>
		<div class="clear"></div>
		<?php
	    return ob_get_clean();
	}
}