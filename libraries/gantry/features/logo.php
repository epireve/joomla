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
class GantryFeaturelogo extends GantryFeature {
	var $_feature_name = 'logo';
    var $_autosize = false;

    
	function render($position="") {
        global $gantry;


        // default location for custom icon is {template}/images/logo/logo.png, with 'perstyle' it's
        // located in {template}/images/logo/styleX/logo.png
        if ($gantry->get("logo-autosize")) {

            jimport ('joomla.filesystem.file');

            $path = $gantry->templatePath.DS.'images'.DS.'logo';
            $logocss = $gantry->get('logo-css','body #rt-logo');

            // get proper path based on perstyle hidden param
            $path = (intval($gantry->get("logo-perstyle",0))===1) ? $path.DS.$gantry->get("cssstyle").DS : $path.DS;
            // append logo file
            $path .= 'logo.png';

            // if the logo exists, get it's dimentions and add them inline
            if (JFile::exists($path)) {
                $logosize = getimagesize($path);
                if (isset($logosize[0]) && isset($logosize[1])) {
                    $gantry->addInlineStyle($logocss.' {width:'.$logosize[0].'px;height:'.$logosize[1].'px;}');
                }
            } 
         }

	    ob_start();
	    ?>
			<div class="rt-block">
    	    	<a href="<?php echo $gantry->baseUrl; ?>" id="rt-logo"></a>
    		</div>
	    <?php
	    return ob_get_clean();
	}
}