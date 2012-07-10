<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupRuleJsversion extends XiptSetupBase
{
	function isApplicable()
	{
		if(XiptHelperJomsocial::isSupportedJS())
			return false;
		return true;
 	}
	
	function getMessage()
	{
		if($this->isRequired()){		
			$requiredSetup['done']     = true;			
			$requiredSetup['message']  = XiptText::_("UPGRADE_JOMSOCIAL_VERSION_1_8_x_IS_SUPPORTED");
			return $requiredSetup;
		}
	}
}