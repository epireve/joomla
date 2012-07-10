<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupRulePayplans extends XiptSetupBase
{
	function isRequired()
	{	
		$params = XiptFactory::getSettings();
		$integrate_with 		= $params->get('integrate_with', 0);
		$subscription_integrate = $params->get('subscription_integrate', 0);
		if($integrate_with == 'aec' || $subscription_integrate == false)
			return false;
			
		$app = $this->getXiptApps();
		if($app)
			return false;
			
		return true;
	}
	
	function doApply()
	{	
		$app = JFactory::getApplication();
		$app->enqueueMessage(XiptText::_('CREATE_JOOMLAXI_PROFILETYPE_APPS_FOR_EACH_PROFILETYPE'));
		$app->redirect(XiptRoute::_("index.php?option=com_payplans&view=app", false));
	}
	
	function isApplicable()
	{
		return XiptLibPayplans::isPayplansExists();
	}
	
	function getMessage()
	{
		$requiredSetup = array();
		if($this->isRequired())
		{
			$link = XiptRoute::_("index.php?option=com_xipt&view=setup&task=doApply&name=payplans",false);
			$requiredSetup['message']  = '<a href="'.$link.'">'.XiptText::_("PLEASE_CLICK_HERE_TO_CREATE_APPS_IN_PAYPLANS").'</a>';
			$requiredSetup['done']  = false;
		}
		
		else
		{
			$requiredSetup['message']  = XiptText::_("PAYPLANS_IS_ALREADY_IN_SYNC");
			$requiredSetup['done'] = true;
		}
			
		return $requiredSetup;
	}
	
	function getXiptApps()
	{
		$query = new XiptQuery();
    	
    	return $query->select('*')
    				 ->from('#__payplans_app')
    				 ->where(" `type` = 'xiprofiletype' ")
    				 ->dbLoadQuery("","")
    				 ->loadResult();
	}
}