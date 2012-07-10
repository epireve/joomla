<?php
/**
 * @package   gantry
 * @subpackage html.layouts
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.gantrylayout');

/**
 *
 * @package gantry
 * @subpackage html.layouts
 */
class GantryLayoutDoc_Tag extends GantryLayout {
    var $render_params = array(
        'classes'       =>  null
    );
    function render($params = array()){
        global $gantry;

        $fparams = $this-> _getParams($params);

    ob_start();
	//XHTML LAYOUT
?><?php if(strlen($fparams->classes) > 0):?>class="<?php echo $fparams->classes; ?>"<?php endif;?><?php
	return ob_get_clean();
    }
}