<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

require_once JPATH_ROOT.DS.'includes'.DS.'application.php';
require_once JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_xipt'.DS.'install'.DS.'helper.php';

class XiptHelperUnhook 
{	
	function disable_plugin($pluginname)
	{
		// XITODO : remove this function
		return XiptHelperUtils::changePluginState($pluginname, 0);
	} 
	
	function disableCustomFields()
	{
		$query = new XiptQuery();
		return $query->update('#__community_fields')
					 ->set(" `published` = 0 ")
					 ->where(" `type` = 'profiletypes' ", 'OR')
					 ->where(" `type` = 'templates' ", 'OR')
					 ->dbLoadQuery("","")
					 ->query();
	} 
	
	
	function uncopyHackedFiles()
	{
		$filestoreplace = XiptHelperInstall::_getJSPTFileList();
	   
		if($filestoreplace) 
		foreach($filestoreplace AS $sourceFile => $targetFile)
		{
			$targetFileBackup = $targetFile.'.jxibak';
			// delete this file
			// Only delete if you have backup copy
			if(JFile::exists($targetFile) && JFile::exists($targetFileBackup))
			{
				JFile::delete($targetFile);
				JFile::move($targetFileBackup,$targetFile) || XiptError::raiseError('XIPT-UNINSTALL-ERROR','Not able to restore backup : '.__LINE__) ;
			}		
		}
		// TODO : also remove previous profiletypes and template library fields files
	
	}
	
	function unCopyXIPTFilesFromJomSocial()
	{
		$COMMUNITY_PATH_FRNTEND = JPATH_ROOT .DS. 'components' . DS . 'com_community';
		
		$targetFile = $COMMUNITY_PATH_FRNTEND.DS.'libraries'.DS.'fields'.DS.'profiletypes.php';
		if(JFile::exists($targetFile))
			JFile::delete($targetFile) || XiptError::raiseError('XIPT-UNINSTALL-ERROR','Not able to restore backup:' .__LINE__) ;
		
		$targetFile = $COMMUNITY_PATH_FRNTEND.DS.'libraries'.DS.'fields'.DS.'templates.php';
		if(JFile::exists($targetFile))
			JFile::delete($targetFile) || XiptError::raiseError('XIPT-UNINSTALL-ERROR','Not able to restore backup : '.__LINE__) ;
		return;
	}
	
		
	
}
