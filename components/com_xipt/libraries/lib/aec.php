<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptLibAec
{
	//
	static public function getProfiletypeInfoFromAEC($usage=0)
	{
		if(!self::isAecExists())
			return false;
			
		$param 					= array();
		$param['profiletype'] 	= XiptLibProfiletypes::getDefaultProfiletype();
		$param['plan'] 			= '';
		$param['planid'] 		= 0;
		$param['planSelected'] 	= false;
		
		$mySess 		= JFactory::getSession();
		$planSetInSess 	= $mySess->has('AEC_REG_PLANID','XIPT');

		//if user is requesting to change plan then prefer it
		$planid  = $usage = JRequest::getInt( 'usage', $usage, 'REQUEST');
		// if no prefered plan, then use saved in session
		if($usage == 0  && $planSetInSess){
			$planid = $mySess->get('AEC_REG_PLANID',0,'XIPT');
		}
		
		if(!$planid)
			return $param;
				    
		$param['planid']       = $planid;
		$param['plan']         = self::getPlanName($planid);
		$param['profiletype']  = self::getProfiletype($planid);
		$param['planSelected'] = true;
		
		//also set data in session
		$mySess->set('AEC_REG_PLANID',$planid, 'XIPT');
		return $param;	
	}
	
	static public function getPlanName($planid)
	{
		//check existance of plan
		if(!self::isPlanExists($planid))
			return XiptText::_('INVALID_PLAN');
			
		$result = self::getPlan($planid);

		return $result->name;
	}
	
	static public function getProfiletype( $planid )
	{
	    $defaultPtype = XiptLibProfiletypes::getDefaultProfiletype();

	    //get MI of given planid;
		$planMIs = self::getMIntegration($planid);
		
		//check existance of plan and its microintegration
		if(!self::isPlanExists($planid) || count($planMIs)<= 0 )
			return $defaultPtype;
		
		$validMIs = self::getExistingMI($planMIs);
		
		$query  = new XiptQuery();
		$result = $query->select('profiletype')
						->from('#__xipt_aec')
						->where(" planid IN (". implode(',', $validMIs).") ")
						->dbLoadQuery()
						->loadResult();
	
		if(!$result)
			return $defaultPtype;

		return $result;
	}
	
	static public function getMIntegration($planid)
	{
	 	$result = self::getPlan($planid);

		if(!isset($result->micro_integrations))
			return array();

		return unserialize(base64_decode($result->micro_integrations));
	}
	
	//Function to display plan selection message manner in configuration
	static public function getAecMessage()
	{
		$data   = self::getProfiletypeInfoFromAEC();
	    $msgOption = XiptFactory::getSettings('subscription_message','b');
	    $pTypeName = XiptLibProfiletypes::getProfiletypeName($data['profiletype']);
	    
    	if($msgOption==='pl')
        	return  XiptText::sprintf('COM_XIPT_ALREADY_SELECTED_PLAN_AS_ONLYPLAN',$data['plan']);
                
        if($msgOption==='pt')
            return  XiptText::sprintf('COM_XIPT_ALREADY_SELECTED_PLAN_AS_ONLY_PTYPE',$pTypeName);                
    	
        if($msgOption==='no')
    		return false;
    		
        return XiptText::sprintf('COM_XIPT_ALREADY_SELECTED_PLAN_AS_BOTH',$data['plan'],$pTypeName);
	}
	
	static public function isPlanExists($planid)
	{
		$result = self::getPlan($planid);		
		return ($result ? true : false);
	}
	
	static public function isAecExists()
	{
		$aecFront = JPATH_ROOT . DS . 'components' . DS . 'com_acctexp';
		$tables = array('#__acctexp_plans', '#__acctexp_microintegrations', '#__xipt_aec');
		
		if(!JFolder::exists($aecFront))
			return false;
			
		foreach($tables as $table){
			if(!XiptHelperTable::isTableExist($table))
					return false;
		}
			
		return true;		
	}

	static public function getExistingMI( $planMIs )
	{		
		$query = new XiptQuery();
		return $query->select('id')
					 ->from('#__acctexp_microintegrations')
					 ->where(" id IN (". implode(',', $planMIs).") ")
					 ->dbLoadQuery()
					 ->loadResultArray();
	}
	
	static public function getPlan($planid)
	{
		$query  = new XiptQuery();
		$result = $query->select('*')
						->from('#__acctexp_plans')
						->dbLoadQuery()
						->loadObjectList('id');
		
		if(!isset($result[$planid])) 
			return null; 

		return $result[$planid]; 
	}
	
//	static function getVersion()
//	{
//		$parser		= JFactory::getXMLParser('Simple');
//		$xml		= JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_acctexp'.DS.'acctexp.xml';
//		
//		//aec 0.14.xx have manifest.xml instead of accexp.xml
//		$manifest	= JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_acctexp'.DS.'manifest.xml';
//		if(JFile::exists($manifest))
//			$xml = $manifest;
//			
//		$parser->loadFile( $xml );
//		$order	= array();
//		$childrens = $parser->document->children();
//		$groups = array();
//		foreach($childrens as $child){
//			if($child->name() == 'version') 
//				$aecversion  =	$child->data();
//							
//		}
//		
//		return $aecversion;
//	}
	
}