<?php
/**
 * @package		Gantry Template Framework - RocketTheme
 * @version		1.3 October 12, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureStyleDeclaration extends GantryFeature {
    var $_feature_name = 'styledeclaration';

    function isEnabled() {
        global $gantry;
        $menu_enabled = $this->get('enabled');

        if (1 == (int)$menu_enabled) return true;
        return false;
    }
    
function init() {
        global $gantry;
		$browser = $gantry->browser;

        // COLORCHOOSER
		
		// Main	
        $css = '#rt-bg-surround, .readonstyle-button #rt-main-column .readon, .roknewspager .readon {background-color:'.$gantry->get('main-background').';}'."\n";
        $css .= 'body .roknewspager-li, body .roknewspager-pages, body .roknewspager-overlay {background-color:'.$gantry->get('main-background').';}'."\n";
        $css .= '.module-title .pointer {border-color:'.$gantry->get('main-background').';}'."\n";
        $css .= '#rt-bottom, .main-bg a:hover, #rt-footer, .main-bg, .roknewspager .roknewspager-content, .readonstyle-button .main-bg .readon span, .readonstyle-button .main-bg .readon .button {color:'.$gantry->get('main-text').';}'."\n";
		$css .= '.main-bg a, .roknewspager .roknewspager-content a {color:'.$gantry->get('main-link').';}'."\n";
        $css .= '.main-bg .readon {background-color:'.$gantry->get('main-link').';}'."\n";
        $css .= '.main-bg .readon span, .main-bg .readon .button {color:'.$gantry->get('main-text').';}'."\n";

        // Page 
        $css .= '#rt-page-surround {background-color:'.$gantry->get('page-background').';}'."\n";
        $css .= '.page-block .rt-block {background-color:'.$gantry->get('page-accent').';}'."\n";
        $css .= 'a, .readonstyle-link .readon .button {color:'.$gantry->get('page-link').';}'."\n";
		$css .= '#rt-body-surround, a:hover {color:'.$gantry->get('page-text').';}'."\n";

        // Showcase 
        $css .= '#rt-header, #rt-header a:hover, #rt-showcase, #rt-showcase a:hover, #rt-feature, #rt-feature a:hover {color:'.$gantry->get('showcaseblock-text').';}'."\n";
        $css .= '#rt-header a, #rt-showcase a, #rt-feature a {color:'.$gantry->get('showcaseblock-link').';}'."\n";

        // Primary
        $css .= '#rt-logo-surround, .menutop li.active.root, .menutop li.root:hover, .menutop li.f-mainparent-itemfocus, .rt-splitmenu .menutop li.active, .rt-splitmenu .menutop li:hover, .fusion-submenu-wrapper, .roknewspager .active .roknewspager-h3, .roknewspager .active .roknewspager-div, .readonstyle-button .readon, .roktabs ul, .roktabs-wrapper .active-arrows, .readonstyle-button #rt-main-column .readon:hover, .module-content ul.menu, .rg-grid-view .tag, .rg-list-view .tag, .rg-detail-slicetag .tag, .rg-detail-filetag .tag, #rt-accessibility #rt-buttons .button, .rg-ss-progress {background-color:'.$gantry->get('primary-color').';}'."\n";
        $css .= '.menutop li > .item, .roknewspager .active .roknewspager-content, body ul.roknewspager-numbers li, body .roknewspager-numbers li.active, #rt-body-surround .roknewspager .roknewspager-h3 a, .readonstyle-button .readon span, .readonstyle-button .readon .button, .roktabs-wrapper .roktabs-links ul, #rt-body-surround .module-content ul.menu a:hover, .rg-grid-view .tag, .rg-list-view .tag, .rg-detail-slicetag .tag, .rg-detail-filetag .tag {color:'.$gantry->get('primary-text').';}'."\n";
		$css .= '.roknewspager .active a, .module-content ul.menu a, .module-content ul.menu .item, .module-content ul.menu .separator {color:'.$gantry->get('primary-link').';}'."\n";
        $css .= 'body ul.triangle-small li:after, body ul.triangle li:after, body ul.triangle-large li:after {border-left-color:'.$gantry->get('primary-color').';}'."\n";
        $css .= 'body ul.checkmark li:after, body ul.circle-checkmark li:before, body ul.square-checkmark li:before, body ul.circle-small li:after, body ul.circle li:after, body ul.circle-large li:after {border-color:'.$gantry->get('primary-color').';}'."\n";

        if ($gantry->browser->platform == 'iphone'){
            $css .= 'body #rt-menu {background-color:'.$gantry->get('primary-color').' !important;}'."\n";
            $css .= '#idrops li.root-sub a, #idrops li.root-sub span.separator, #idrops li.root-sub.active a, #idrops li.root-sub.active span.separator {color: '.$gantry->get('primary-color').' !important;}'."\n";
        }
        
        // Bottom
        $css .= 'body {background-color:'.$gantry->get('bottomblock-background').';}'."\n";
        $css .= '#rt-copyright, #rt-copyright a:hover {color:'.$gantry->get('bottomblock-text').';}'."\n";
        $css .= '#rt-copyright a {color:'.$gantry->get('bottomblock-link').';}'."\n";

        // Module Variations
        $css .= '#rt-body-surround .box1 .rt-block, .readonstyle-button .main-bg .box1 .readon {background-color:'.$gantry->get('main-background').';}'."\n";
        $css .= '.box1, .box1 .readon span, .box1 .readon .button, .box1 a:hover, #rt-page-surround .box1 a:hover {color:'.$gantry->get('main-text').';}'."\n";
        $css .= '.box1 a {color:'.$gantry->get('main-link').';}'."\n";
        $css .= '.main-bg .box1 .rt-block {background-color:'.$gantry->get('page-accent').';}'."\n";
        $css .= '.main-bg .box1 a {color:'.$gantry->get('page-link').';}'."\n";
        $css .= '.main-bg .box1, .main-bg .box1 a:hover, #rt-bottom.main-bg .box1 a:hover, #rt-footer.main-bg .box1 a:hover {color:'.$gantry->get('page-text').';}'."\n";
        $css .= '#rt-content-top .ribbon .module-surround, #rt-content-bottom .ribbon .module-surround, .box2 .rt-block, .box3 .rt-block, .title2 .module-title, .readonstyle-button #rt-main-column .box1 .readon {background-color:'.$gantry->get('primary-color').';}'."\n";
        $css .= '#rt-content-top .ribbon .rt-block, #rt-content-bottom .ribbon .rt-block, .box2, .box2 a:hover, .box3, .box3 a:hover, .ribbon #roktwittie, .title2 h2.title, .ribbon #roktwittie .title, .ribbon #roktwittie .roktwittie-infos, #rt-body-surround .ribbon a:hover {color:'.$gantry->get('primary-text').';}'."\n";
        $css .= '.box2 a, .box3 a, .ribbon a, .ribbon #roktwittie .roktwittie-infos a {color:'.$gantry->get('primary-link').';}'."\n";
        $css .= '.title2 .accent, body ul.checkmark li:after, body ul.circle-checkmark li:before, body ul.square-checkmark li:before, body ul.circle-small li:after, body ul.circle li:after, body ul.circle-large li:after {border-color:'.$gantry->get('primary-color').';}'."\n";
		$css .= 'body ul.triangle-small li:after, body ul.triangle li:after, body ul.triangle-large li:after {border-left-color:'.$gantry->get('primary-color').';}'."\n";
        $css .= '.main-overlay-dark.readonstyle-button .main-bg .box1 .readon:hover, .main-overlay-light.readonstyle-button .main-bg .box1 .readon:hover {background-color:'.$gantry->get('primary-color').';}'."\n";
        $css .= '.readonstyle-button .main-bg .box1 .readon:hover span, .readonstyle-button .main-bg .box1 .readon:hover .button {color:'.$gantry->get('primary-text').';}'."\n";

        // Gradients
        $css .= '.grad-bottom {'.$this->_createGradient('top', $gantry->get('page-background'), '0', '0%', $gantry->get('page-background'), '1', '100%').'}'."\n";
        $css .= '.pattern-gradient {'.$this->_createGradient('top', $gantry->get('main-background'), '0', '0%', $gantry->get('main-background'), '1', '100%').'}'."\n";
        
        // Backgrounds
        $css .= $this->buildBackground();

        // Static file
        if ($gantry->get('static-enabled')) {
            // do file stuff
            jimport('joomla.filesystem.file');
            $filename = $gantry->templatePath.DS.'css'.DS.'static-styles.css';

            if (file_exists($filename)) {
                if ($gantry->get('static-check')) {
                    //check to see if it's outdated

                    $md5_static = md5_file($filename);
                    $md5_inline = md5($css);

                    if ($md5_static != $md5_inline) {
                        JFile::write($filename, $css);
                    }
                }
            } else {
                // file missing, save it
                JFile::write($filename, $css);
            }
            // add reference to static file
            $gantry->addStyle('static-styles.css',99);

        } else {
            // add inline style
            $gantry->addInlineStyle($css);
        }
        

		$this->_disableRokBoxForiPhone();

		// Style Inclusion
		$cssstyle = $gantry->get('cssstyle');
		$gantry->addStyle($cssstyle.".css");
		$gantry->addStyle('overlays.css');
		$bodystyle = $gantry->get('body-background');
		$gantry->addStyle('bodystyle-'.$bodystyle.'.css');
		if ($gantry->get('typography-enabled')) $gantry->addStyle('typography.css');
		if ($gantry->get('extensions')) $gantry->addStyle('extensions.css');
        if ($gantry->get('extensions')) $gantry->addStyle('extensions-overlays.css');
		if ($gantry->get('extensions')) $gantry->addStyle('extensions-body-'.$bodystyle.'.css');
		if ($gantry->get('thirdparty')) $gantry->addStyle('thirdparty.css');

	}

    function buildBackground(){
        global $gantry;

        if (!$gantry->get('background-enabled') && ($gantry->browser->platform != 'iphone')) return "";

        $source = $width = $height = "";

        $background = str_replace("&quot;", '"', str_replace("'", '"', $gantry->get('background-image')));
        $data = json_decode($background);

        if (!$data){
            if (strlen($background)) $source = $background;
            else return "";
        } else {
            $source = $data->path;
        }

        if (substr($gantry->baseUrl, 0, strlen($gantry->baseUrl)) == substr($source, 0, strlen($gantry->baseUrl))){
            $file = JPATH_ROOT . DS . substr($source, strlen($gantry->baseUrl));
        } else {
            $file = JPATH_ROOT . DS . $source;
        }

        if (isset($data->width) && isset($data->height)){
            $width = $data->width;
            $height = $data->height;
        } else {
            $size = @getimagesize($file);
            $width = $size[0];
            $height = $size[1];
        }


        if (!preg_match('/^\//', $source))
        {
            $source = JURI::root(true).'/'.$source;
        }

        $output = "";
        $output .= "#rt-bg-image {background: url(".$source.") 50% 0 no-repeat;}"."\n";
        $output .= "#rt-bg-image {position: absolute; width: ".$width."px;height: ".$height."px;top: 0;left: 50%;margin-left: -".($width / 2)."px;}"."\n";

        $file = preg_replace('/\//i', DS, $file);

        return (file_exists($file)) ? $output : '';
    }

    function _createGradient($direction, $from, $fromOpacity, $fromPercent, $to, $toOpacity, $toPercent){
        global $gantry;
        $browser = $gantry->browser;

        $fromColor = $this->_RGBA($from, $fromOpacity);
        $toColor = $this->_RGBA($to, $toOpacity);
        $gradient = $default_gradient = '';

        $default_gradient = 'background: linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';

        switch ($browser->engine) {
            case 'gecko':
                $gradient = ' background: -moz-linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';
                break;

            case 'webkit':
                if ($browser->shortversion < '5.1'){
                    
                    switch ($direction){
                        case 'top':
                            $from_dir = 'left top'; $to_dir = 'left bottom'; break;
                        case 'bottom':
                            $from_dir = 'left bottom'; $to_dir = 'left top'; break;
                        case 'left':
                            $from_dir = 'left top'; $to_dir = 'right top'; break;
                        case 'right':
                            $from_dir = 'right top'; $to_dir = 'left top'; break;
                    }
                    $gradient = ' background: -webkit-gradient(linear, '.$from_dir.', '.$to_dir.', color-stop('.$fromPercent.','.$fromColor.'), color-stop('.$toPercent.','.$toColor.'));';
                } else {
                    $gradient = ' background: -webkit-linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';
                }
                break;

            case 'presto':
                $gradient = ' background: -o-linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';
                break;

            case 'trident':
                if ($browser->shortversion >= '10'){
                    $gradient = ' background: -ms-linear-gradient('.$direction.', '.$fromColor.' '.$fromPercent.', '.$toColor.' '.$toPercent.');';
                } else if ($browser->shortversion <= '6'){
                    $gradient = $from;
                    $default_gradient = '';
                } else {

                    $gradient_type = ($direction == 'left' || $direction == 'right') ? 1 : 0;
                    $from_nohash = str_replace('#', '', $from);
                    $to_nohash = str_replace('#', '', $to);

                    if (strlen($from_nohash) == 3) $from_nohash = str_repeat(substr($from_nohash, 0, 1), 6);
                    if (strlen($to_nohash) == 3) $to_nohash = str_repeat(substr($to_nohash, 0, 1), 6);

                    if ($fromOpacity == 0 || $fromOpacity == '0' || $fromOpacity == '0%') $from_nohash = '00' . $from_nohash;
                    if ($toOpacity == 0 || $toOpacity == '0' || $toOpacity == '0%') $to_nohash = '00' . $to_nohash;

                    $gradient = " filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#".$from_nohash."', endColorstr='#".$to_nohash."',GradientType=".$gradient_type." );";

                    $default_gradient = '';
                    
                }
                break;

            default:
                $gradient = $from;
                $default_gradient = '';
                break;
        }

        return  $default_gradient . $gradient;
    }

    function _HEX2RGB($hexStr, $returnAsString = false, $seperator = ','){
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
        $rgbArray = array();
    
        if (strlen($hexStr) == 6){
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3){
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return false;
        }
    
        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
    }
    
    function _RGBA($hex, $opacity){
        return 'rgba(' . $this->_HEX2RGB($hex, true) . ','.$opacity.')';
    }

	function _disableRokBoxForiPhone() {
		global $gantry;

		if ($gantry->browser->platform == 'iphone') {
			$gantry->addInlineScript("window.addEvent('domready', function() {\$\$('a[rel^=rokbox]').removeEvents('click');});");
		}
	}

}