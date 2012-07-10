<?php
/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$params->def('menutype', 			'mainmenu');
$params->def('class_sfx', 			'');
$params->def('menu_images', 		0);
$params->def('startLevel', 		0);
$params->def('endLevel', 			0);
$params->def('showAllChildren', 	0);

require_once(dirname(__FILE__)."/lib/includes.php");
$rnm = new RokNavMenu($params->toArray());
$rnm->initialize();
$output = $rnm->render();
$output = trim($output);
echo $output;