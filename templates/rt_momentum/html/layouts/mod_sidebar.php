<?php
/**
 * @package   gantry
 * @subpackage html.layouts
 * @version   1.3 October 12, 2011
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
class GantryLayoutMod_Sidebar extends GantryLayout {
    var $render_params = array(
        'contents'       =>  null,
        'position'      =>  null,
        'gridCount'     =>  null,
        'pushPull'      =>  '',
        'extraClass' => ''
    );
    function render($params = array()){
        global $gantry;

        $rparams = $this->_getParams($params);
        ob_start();
    // XHTML LAYOUT

	$classes = trim('rt-grid-'.trim($rparams->gridCount." ".$rparams->pushPull." ".$rparams->extraClass));
	$classes = preg_replace('/\s\s+/', ' ', $classes);
?>
            <div class="<?php echo $classes;?>">
                <div class="rt-sidebar-surround page-block">
                    <div id="rt-<?php echo $rparams->position; ?>">
                        <?php echo $rparams->contents; ?>
                    </div>
                </div>
            </div>

<?php
        return ob_get_clean();
    }
}