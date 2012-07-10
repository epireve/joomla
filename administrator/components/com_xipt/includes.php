<?php 
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

if(defined('DEFINE_ADMIN_INCLUDES'))
	return;

define('DEFINE_ADMIN_INCLUDES','DEFINE_ADMIN_INCLUDES');

//	This is file for BACKEND only, should be included in starting file only.
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
require_once JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php';

XiptLoader::addAutoLoadViews(XIPT_ADMIN_PATH_VIEWS, JRequest::getCmd('format','html'),	'Xipt');
XiptLoader::addAutoLoadFolder(XIPT_ADMIN_PATH_CONTROLLERS, 'Controller',	'Xipt');

// include JomSocial files
if(!JFolder::exists(JPATH_ROOT.DS.'components'.DS.'com_community')){
	$option=JRequest::getVar('option','');
	if($option=='com_xipt'){
		JFactory::getApplication()->redirect("index.php",XiptText::_("PLEASE_INSTALL_JOMSOCIAL"));
	}
	return false;
}