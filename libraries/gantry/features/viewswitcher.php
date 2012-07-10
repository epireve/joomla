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
class GantryFeatureViewSwitcher extends GantryFeature {
	var $_platform = "";
	
	function isEnabled() {
		global $gantry;
		if (!$gantry->browser) return false;
		
		$this->_platform = $gantry->browser->platform;
		if ($gantry->get($this->_platform.'-enabled')) return true;
		else return false;
	}
	
	function init() {
		global $gantry;
		
		$prefix = 'viewswitcher-'.$gantry->get('template_prefix');
		$cookiename = $prefix.$this->_platform.'-switcher';
		$tempkey_name = $gantry->get('template_prefix').$this->_platform.'-switcher';
		$cookie = JRequest::getVar($cookiename, false, 'COOKIE', 'STRING');
		
		if (!strlen($cookie) || $cookie === false) {
			setcookie($cookiename, "1", 0, $gantry->getCookiePath());
			$cookie = "1";
		}
		
		$gantry->addTemp('platform', $tempkey_name, $cookie);
        $gantry->addTemp('platform', $cookiename, $cookie);  // for backwards compat
		
		if ($gantry->get($this->_platform . '-switcher-enabled')) $gantry->addDomReadyScript($this->_js($cookie, $cookiename,$tempkey_name));
	}

    function getPosition() {
		$this->_feature_name = $this->_platform.'-switcher';
        return $this->get('position');
    }
	
	function render($position="") {
		global $gantry;
		
		if (!$gantry->get($gantry->browser->platform . '-switcher-enabled')) return "";
		
		$prefix = 'viewswitcher-'.$gantry->get('template_prefix');
		$cookiename = $prefix.$gantry->browser->platform.'-switcher';
		$cookie = JRequest::getVar($cookiename, false, 'COOKIE', 'STRING');
        $tempkey_name = $gantry->get('template_prefix').$this->_platform.'-switcher';
		$cls = (!$gantry->retrieveTemp('platform', $cookiename)) ? 'off' : 'on';
		
	    ob_start();
	    ?>
		<div class="clear"></div>
		<a href="#" id="gantry-viewswitcher" class="<?php echo $cls; ?>"><span>Switcher</span></a>
		<?php
	    return ob_get_clean();
	}
	
	function _js($cookie, $cookiename, $tempkey) {
		global $gantry;
		if ($cookie === false || $cookie == '1' || $gantry->retrieveTemp('platform', $tempkey) == "1") $cookie = 0;
		else $cookie = 1;

		return "
			var switcher = document.id('gantry-viewswitcher');
			if (switcher) {
				switcher.addEvent('click', function(e) {
					e.stop();
					switcher[('".$cookie."' == '0') ? 'addClass' : 'removeClass']('off');
					Cookie.write('".$cookiename."', '".$cookie."', {path: '".$gantry->getCookiePath()."'});
					window.location.reload();
				});
			}
		";
	}
}