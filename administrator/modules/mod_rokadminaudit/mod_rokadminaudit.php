<?php
/**
 * @package RokAdminAudit - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );
// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$doc =& JFactory::getDocument();

// offsets
$start = intval(JRequest::getInt('start', 0));
$limit = intval(JRequest::getInt('limit', $params->get('limit', 5)));
$details = JRequest::getString('details', $params->get('detail_filter', 'low'));

$rowsList = rokAdminAuditHelper::getRows($params, $start, $limit, $details);
$rows = $rowsList['rows'];
$count = $rowsList['count'];

$doc->addStyleSheet('modules/mod_rokadminaudit/tmpl/rokadminaudit.css');
$doc->addScript('modules/mod_rokadminaudit/tmpl/js/MC-Audit.js');
$doc->addScriptDeclaration("window.addEvent('domready', function(){
	new RokAudit('rok-audit', {start: ".$start.", limit: ".$limit.", details: '".$details."', amount: ".$count.", url: 'index.php?process=ajax&model=module&moduleid=".$module->id."'});
});");

require(JModuleHelper::getLayoutPath('mod_rokadminaudit'));
