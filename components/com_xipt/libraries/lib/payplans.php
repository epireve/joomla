<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptLibPayplans
{	
	//get profiletype information from payplans
	static public function getProfiletypeInfoFromPayplans()
	{
		if(!self::isPayplansExists())
			return false;
			
		$param 					= array();
		$param['profiletype'] 	= XiptLibProfiletypes::getDefaultProfiletype();
		$param['plan'] 			= '';
		$param['planid'] 		= 0;
		$param['planSelected'] 	= false;
		
		$mySess 		= JFactory::getSession();
		$planSetInSess 	= $mySess->has('PAYPLANS_REG_PLANID','XIPT');

		$planid  = null;
		// use saved in session
		if($planSetInSess){
			$planid = $mySess->get('PAYPLANS_REG_PLANID',0,'XIPT');
		}
		
		if(!$planid)
			return $param;
				    
		$param['planid']       = $planid;
		$param['plan']         = self::getPlanName($planid);
		$param['profiletype']  = self::getProfiletype();
		$param['planSelected'] = true;
		
		return $param;	
	}	

	//get plan name as per planid
	static public function getPlanName($planid)
	{
		include_once JPATH_ROOT .DS. 'components' .DS. 'com_payplans' .DS. 'includes' .DS. 'api.php';
		$planInstance = PayplansApi::getPlan($planid);
		return $planInstance->getTitle();
	}
	
	//get profpiletype as per planid
	//payplans itself set PT in session
	static public function getProfiletype()
	{
	    $defaultPtype = XiptLibProfiletypes::getDefaultProfiletype();
	    
		$session 	= JFactory::getSession();
		$pid		= $session->get('SELECTED_PROFILETYPE_ID', 0, 'XIPT');
		
		if(!$pid)
			return $defaultPtype;
			
		return $pid;
	}
	
	//Function to display plan selection message manner in configuration
	static public function getPayplansMessage()
	{
		$data   = self::getProfiletypeInfoFromPayplans();
		
	    $msgOption  = XiptFactory::getSettings('subscription_message','b');
	    $pTypeName  = XiptLibProfiletypes::getProfiletypeName($data['profiletype']);
	    
    	if($msgOption==='pl')
        	return  XiptText::sprintf('COM_XIPT_ALREADY_SELECTED_PLAN_AS_ONLYPLAN',$data['plan']);
                
        if($msgOption==='pt')
            return  XiptText::sprintf('COM_XIPT_ALREADY_SELECTED_PLAN_AS_ONLY_PTYPE',$pTypeName);                
    	
        if($msgOption==='no')
    		return false;
    		
        return XiptText::sprintf('COM_XIPT_ALREADY_SELECTED_PLAN_AS_BOTH',$data['plan'],$pTypeName);
	}
	
	//check the exsistance of payplans(defined by itself)
	static public function isPayplansExists()
	{
		if(defined('PAYPLANS_LOADED'))
			return true;
		return false;		
	}
	
}