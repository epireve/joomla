<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class joingroup extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}
	
	function checkAclViolation(&$data)
	{
		$resourceAccesser 	= $this->getResourceAccesser($data);
		
		$maxmimunCount = $this->aclparams->get('joingroup_limit',0);
		$aclgroup      = $this->aclparams->get('group_category');
		$groupid   	   = $data['args'][0];
		
		if($aclgroup)
			$catId	   = $this->getCategoryId($groupid);
		else
			$catId	   = 0;
		
		$count = $this->getFeatureCounts($resourceAccesser,$catId);		
		
		if($aclgroup == $catId && $count >= $maxmimunCount)
			return true;
			
		return false;
	}
	
	function getFeatureCounts($resourceAccesser,$catId)
	{
		
		if($catId)
			$condition = "WHERE `categoryid`= $catId";
    	else
			$condition = '';
		$db		=JFactory::getDBO();
		
		$query	= ' SELECT COUNT(*) FROM ' 
				. $db->nameQuote( '#__community_groups_members' )
				. ' WHERE ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $resourceAccesser )
				. ' AND ' . $db->nameQuote( 'approved' ) . '=' . $db->Quote( '1' )
				. ' AND ' . $db->nameQuote('groupid') . 'IN'
				. ' (SELECT id FROM '
				. $db->nameQuote( '#__community_groups' )
				. "$condition)";
				
		$db->setQuery( $query );
		$result	= $db->loadResult();
		return $result;
	}


	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('groups' != $data['view'])
			return false;

		$task = array('ajaxshowjoingroup', 'ajaxsavejoingroup', 'ajaxjoingroup');
		if(in_array($data['task'], $task))
				return true;

		return false;
	}

	function getCategoryId($groupid)
	{
		$query = new XiptQuery();
    	
		return $query->select('categoryid')
						->from('#__community_groups')
						->where("`id` = $groupid")
						->dbLoadQuery("","")
	    				->loadResult();
	}
}
