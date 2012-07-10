<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class creategroup extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];
	}
	
	function checkAclViolation(&$data)
	{
		$resourceAccesser 	= $this->getResourceAccesser($data);
		
		$maxmimunCount = $this->aclparams->get('creategroup_limit',0);
		$aclgroup      = $this->aclparams->get('group_category');
		if($aclgroup)
			$catId		   = JRequest::getVar('categoryid' , 0 , 'REQUEST');
		else 
			$catId		   = JRequest::getVar('categoryid' , $aclgroup , 'REQUEST');
		
		$count = $this->getFeatureCounts($resourceAccesser,$catId);		
		
		if ($aclgroup === $catId && $count >= $maxmimunCount)
			return true;
			
		return false;
	}
	
	function getFeatureCounts($resourceAccesser,$catId)
	{
		if($catId)
			$condition = "AND `categoryid`= $catId";
		else
			$condition = '';
			
		$query = new XiptQuery();
    	
    	return $query->select('COUNT(*)')
    				 ->from('#__community_groups')
    				 ->where(" `ownerid` = $resourceAccesser $condition ")
    				 ->dbLoadQuery("","")
    				 ->loadResult();
	}


	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('groups' != $data['view'])
			return false;

		if($data['task']=='create')
				return true;

		return false;
	}
}
