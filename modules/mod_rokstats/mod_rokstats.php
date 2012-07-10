<?php
/**
 * @package RokStats - RocketTheme
 * @version 1.7.0 November 1, 2011
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );

$doc =& JFactory::getDocument();
$doc->addStyleSheet('modules/mod_rokstats/tmpl/rokstats.css');

require_once (dirname(__FILE__).DS.'helper.php');
$rows = rokUserStatsHelper::getRows($params);

require(JModuleHelper::getLayoutPath('mod_rokstats'));
