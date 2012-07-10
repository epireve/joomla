<?php
/**
 * Joomla! version check and Admin Tools integration for AdminPraise 3
 * @author Nicholas K. Dionysopoulos
 * @license GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

$hasAdminTools = false;
$hasUpdate = false;
$newVersion = null;

// First, check that we have PHP 5.1.6+ and Admin Tools installed
jimport('joomla.filesystem.file');
$hasAdminTools = JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'helpers'.DS.'jsonlib.php');

if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	// No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}

// Old PHP version detected. No go
if(!version_compare($version, '5.1.6', '>=')) $hasAdminTools = false;

// Check if Admin Tools is enabled
if($hasAdminTools) {
	jimport('joomla.application.component.helper');
	if (!JComponentHelper::isEnabled('com_admintools', true))
	{
		$hasAdminTools = false;
	}
}

if($hasAdminTools) {
	// Joomla! 1.6 detection
	if(!defined('ADMINTOOLS_JVERSION'))
	{
		jimport('joomla.filesystem.file');
		if(!version_compare( JVERSION, '1.6.0', 'ge' )) {
			define('ADMINTOOLS_JVERSION','15');
		} else {
			define('ADMINTOOLS_JVERSION','16');
		}
	}

	// If JSON functions don't exist, load our compatibility layer
	if( (!function_exists('json_encode')) || (!function_exists('json_decode')) )
	{
		include_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'helpers'.DS.'jsonlib.php';
	}

	// Get the version information
	require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_admintools'.DS.'models'.DS.'jupdate.php';
	$model = new AdmintoolsModelJupdate();
	$uinfo = $model->getUpdateInfo();
	
	if($uinfo->status == true) {
		// We have an update!
		$hasUpdate = true;
		$newVersion = $uinfo->version;
	}
}

if($hasUpdate) {
	$AP3JoomlaVersionMessage = JText::sprintf('JOOMLA UPDATE FOUND', $newVersion);
} else {
	$AP3JoomlaVersionMessage = JText::_('Version') . " " . JVERSION;
}