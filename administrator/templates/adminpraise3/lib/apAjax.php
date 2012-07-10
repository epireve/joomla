<?php
require_once('framework.php');
$allowedActions = array('unpublishModule');

$action = JRequest::getVar('action');

if(!in_array($action, $allowedActions)) {
	die('Invalid action');
}

switch($action) {
	case 'unpublishModule':
		unpublishModule();
		break;
}

function unpublishModule() {
	$moduleId = JRequest::getInt('id');
	require_once 'modules.php';
	if (AdminPraiseModules::unpublishModule($moduleId) != true) {
		echo JText::_('There was a problem deleting the module');
	}
}
?>
