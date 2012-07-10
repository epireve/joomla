<?php
/**
 * @package   gantry
 * @subpackage bootstrap
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();
global $gantry;

$gantry_path_j15 = JPATH_SITE . '/components/com_gantry/gantry.php';
$gantry_path_j16 = JPATH_SITE . '/libraries/gantry/gantry.php';

$gantry_path = '';
if (version_compare(JVERSION, '1.5', '>=') && version_compare(JVERSION, '1.6', '<')) {
    $gantry_path = $gantry_path_j15;
}
else if (version_compare(JVERSION, '1.6', '>=')) {
    $gantry_path = $gantry_path_j16;
}

if (!file_exists($gantry_path)) {
    echo JText::_('Unable to find Gantry library.  Please make sure you have it installed.');
    die;
}
require_once($gantry_path);

$app = JFactory::getApplication();
if (!$app->isAdmin()){
    $app->triggerEvent('onGantryTemplateInit', array($filename));
}