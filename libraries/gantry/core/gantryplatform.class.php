<?php
/**
 * @package   gantry
 * @subpackage core
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

/**
 * @package   gantry
 * @subpackage core
 */
class GantryPlatform {

    var $php_version;
    var $platform;
    var $platform_version;
    var $jslib;
    var $jslib_version;
    var $jslib_shortname;
    var $_js_file_checks = array();

    function GantryPlatform(){
        $this->php_version = phpversion();
        $this->_getPlatformInfo();
    }

    function _getPlatformInfo(){
        // See if its joomla
        if (defined('_JEXEC') && defined('JVERSION')){
            $this->platform='joomla';
            if (version_compare(JVERSION, '1.5', '>=') && version_compare(JVERSION, '1.6', '<')){
                $this->platform_version = JVERSION;
                $this->_getJoomla15Info();
            }
            else if (version_compare(JVERSION, '1.6', '>=')){
                $this->platform_version = JVERSION;
                $this->_getJoomla16Info();
            }
            else {
                $this->_unsuportedInfo();
            }
        }
        else {
            $this->_unsuportedInfo();
        }
    }

    function _unsuportedInfo(){
        foreach (get_object_vars($this) as $var_name => $var_value){
            if (null == $var_value) $this->$var_name = "unsupported";
        }
    }

    // Get info for Joomla 1.5 versions
    function _getJoomla15Info(){
        $mainframe =& JFactory::getApplication();

        $this->jslib = 'mootools';

        $this->jslib_shortname= 'mt';

        $mootools_version = JFactory::getApplication()->get('MooToolsVersion', '1.11');
        if ($mootools_version != "1.11" || $mainframe->isAdmin()){
            $this->jslib_version = '1.2';
        }
        else {
            $this->jslib_version = '1.1';
        }

        // Create the JS checks for Joomla 1.5
        $this->_js_file_checks = array(
            '-'.$this->jslib.$this->jslib_version,
            '-'.$this->jslib_shortname.$this->jslib_version
        );
        if (JPluginHelper::isEnabled('system', 'mtupgrade')){
            $this->_js_file_checks[] = '-upgrade';
        }
        $this->_js_file_checks[] = '';
    }

    // Get info for Joomla 1.6 versions
    function _getJoomla16Info(){
        $this->jslib = 'mootools';
        $this->jslib_shortname = 'mt';
        $this->jslib_version = '1.2';
        $this->_js_file_checks = array(
            '-'.$this->jslib.$this->jslib_version,
            '-'.$this->jslib_shortname.$this->jslib_version,
            ''
        );
    }

        // Get info for Joomla 1.7 versions
    function _getJoomla17Info(){
        $this->jslib = 'mootools';
        $this->jslib_shortname = 'mt';
        $this->jslib_version = '1.2';
        $this->_js_file_checks = array(
            '-'.$this->jslib.$this->jslib_version,
            '-'.$this->jslib_shortname.$this->jslib_version,
            ''
        );
    }

    function getJSChecks($file, $keep_path = false){
        $checkfiles = array();
        $ext = substr($file, strrpos($file, '.'));
        $path = ($keep_path)?dirname($file).DS:'';
        $filename = basename($file, $ext);
        foreach($this->_js_file_checks as $suffix){
            $checkfiles[] = $path.$filename.$suffix.$ext;
        }
        return $checkfiles;
    }

    function getJSInit(){
        return $this->jslib_shortname . '_'. str_replace('.','_',$this->jslib_version);
    }
}