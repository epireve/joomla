<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupRuleDefaultprofiletype extends XiptSetupBase
{
 	function isRequired()
 	{
 		$defaultProfiletypeID = XiptFactory::getSettings('defaultProfiletypeID', 0);
 		
 		if($defaultProfiletypeID && XiptLibProfiletypes::validateProfiletype($defaultProfiletypeID))
 			return false;
 			
 		return true;
 	}
	
 	function doApply()
 	{
 		JFactory::getApplication()->redirect(XiptRoute::_("index.php?option=com_xipt&view=settings", false));
 	}
 	
	function getMessage()
	{
		$requiredSetup = array();
		if($this->isRequired())
		{
			$link = XiptRoute::_("index.php?option=com_xipt&view=setup&task=doApply&name=defaultprofiletype",false);
			$requiredSetup['message']  = '<a href="'.$link.'">'.XiptText::_("PLEASE_CLICK_HERE_TO_SET_DEFAULT_PROFILETYPE").'</a>';
			$requiredSetup['done']  = false;
		}
		
		else
		{
			$requiredSetup['message'] = XiptText::_("DEFAULT_PROFILETYPE_EXIST");
			$requiredSetup['done'] = true;
		}
			
		return $requiredSetup;
	}
	
	function isApplicable()
	{
		$ptypes = XiptHelperProfiletypes::getProfileTypeArray();
		if($ptypes)
			return true;
			
		return false;
	}
}