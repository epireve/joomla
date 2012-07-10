<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die();

jimport( 'joomla.filesystem.folder' );
jimport('joomla.filesystem.file');


require_once JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php';

function com_uninstall()
{
	XiptHelperUnhook::uncopyHackedFiles();
	
	// disable plugins
	XiptHelperUnhook::disable_plugin('xipt_system');
	XiptHelperUnhook::disable_plugin('xipt_community');
	
	XiptHelperUnhook::disableCustomFields();
}
