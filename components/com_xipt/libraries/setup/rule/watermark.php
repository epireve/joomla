<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupRuleWatermark extends XiptSetupBase
{
	function isRequired()
	{
		$ptypeArray	= XiptHelperProfiletypes::getProfileTypeArray();
		$globalWM	= XiptFactory::getSettings('show_watermark',0);
				
		if($globalWM)
			return false;
			
		foreach($ptypeArray as $ptype)
		{
			$watermarkParams = XiptLibProfiletypes::getParams($ptype,'watermarkparams');
		
			if($watermarkParams->get('enableWaterMark',0) == true)
				return true;
		}
		
		return false;		
	}
	
	function getMessage()
	{
		if($this->isRequired()){		
			$requiredSetup['done']  = true;			
			$requiredSetup['message']  = XiptText::_("WATER_MARKING_IS_NOT_ENABLED_IN_SETTINGS_BUT_ENABLE_FOR_PROFILE_TYPES");
			return $requiredSetup;
		}
	}
}