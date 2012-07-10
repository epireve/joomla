<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupRuleAdminapproval extends XiptSetupBase
{
	function isApplicable()
	{	
		$plugin = XiptHelperUtils::getPluginStatus('xi_adminapproval');
		
		if(XIPT_JOOMLA_15){
			if($plugin && $plugin->published == 1)
				return false;
		}
		else{	
			if($plugin && $plugin->enabled == 1)
				return false;	
		}
			
		$ptypeArray	= XiptHelperProfiletypes::getProfileTypeArray();
		foreach($ptypeArray as $ptype){
			if(XiptHelperProfiletypes::getProfileTypeData($ptype, 'approve') == true)
				return true;
		}
		
		return false;
	}
	
	
	function getMessage()
	{
		if($this->isRequired()){		
			$requiredSetup['done']     = true;			
			$requiredSetup['message']  = XiptText::_("INSTALL_OR_ENABLE_ADMIN_APPROVAL_PLUGIN");
			return $requiredSetup;
		}
	}
}