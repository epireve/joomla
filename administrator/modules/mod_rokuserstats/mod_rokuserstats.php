<?php
/**
 * @package RokUserStats - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );

$doc =& JFactory::getDocument();
$doc->addStyleSheet('modules/mod_rokuserstats/tmpl/rokuserstats.css');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

//$rssurl	= $params->get('rssurl', '');
//$rssrtl	= $params->get('rssrtl', 0);

$rows = rokUserStatsHelper::getRows();

require(JModuleHelper::getLayoutPath('mod_rokuserstats'));
