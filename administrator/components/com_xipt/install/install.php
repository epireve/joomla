<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
if(!defined('_JEXEC')) die('Restricted access');


require_once JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_xipt' .DS. 'install' .DS. 'helper.php';

function com_install()
{	
//	if(XiptHelperInstall::check_version() == false)
//		JError::raiseWarning('INSTERR', "XIPT Only support Jomsocial 1.8 or greater releases");

	XiptHelperInstall::ensureXiptVersion();
	
	if(XiptHelperInstall::setup_database() == false)
		JError::raiseError('INSTERR', "Not able to setup JSPT database correctly");

	/* When you have old JSPT version
	 * then apply migration on 'avatar' coloumn of 'xipt_profiletype' table
	 */ 	 
	XiptHelperInstall::changeDefaultAvatar();
	
	if(XiptHelperInstall::copyAECfiles() == false)
		JError::raiseError('INSTERR', "Not able to replace MI files, Check permissions.");
	
	if(XiptHelperInstall::installExtensions() == false){
		JError::raiseError('INSTERR', "NOT ABLE TO INSTALL EXTENSIONS");
		return false;
	}	
	XiptHelperInstall::show_instruction();
	XiptHelperInstall::updateXiptVersion();
	return true;
}
