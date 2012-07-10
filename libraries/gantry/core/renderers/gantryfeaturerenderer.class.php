<?php
/**
 * @package     gantry
 * @subpackage  core.renderers
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('GANTRY_VERSION') or die();
/**
 * @package     gantry
 * @subpackage  core.renderers
 */
class GantryFeatureRenderer {
    // wrapper for feature display
    function display($feature_name, $layout = 'basic') {
        global
        $gantry;
        $feature = $gantry->_getFeature($feature_name);
        $rendered_feature = "";
        if (method_exists($feature, 'isEnabled') && $feature->isEnabled() && method_exists($feature, 'render')) {
            $rendered_feature = $feature->render();
        }
        $contents = $rendered_feature . "\n";
        $output = $gantry->renderLayout('feature_' . $layout, array('contents' => $contents));
        return $output;
    }
}