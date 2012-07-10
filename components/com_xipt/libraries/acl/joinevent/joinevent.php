<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class joinevent extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}
	
	function checkAclViolation(&$data)
	{
		$resourceAccesser 	= $this->getResourceAccesser($data);
		
		$maxmimunCount = $this->aclparams->get('joinevent_limit',0);
		$aclevent      = $this->aclparams->get('event_category');
		$eventid	= isset($data['eventid'])? $data['eventid'] : 0;
		$eventid	= JRequest::getVar('eventid' , $eventid, 'REQUEST');
		
		if($aclevent)
			$catId	   = $this->getCategoryId($eventid);
		else
			$catId	   = 0;
		
		$count = $this->getFeatureCounts($resourceAccesser,$catId);		
		
		if($aclevent == $catId && $count >= $maxmimunCount)
			return true;
			
		return false;
	}
	
	function getFeatureCounts($resourceAccesser,$catId)
	{	
		$condition = '';
		
		if($catId)
			$condition = "WHERE `catid`= $catId";
    				 
		$db		=JFactory::getDBO();
		
		$query	= ' SELECT COUNT(*) FROM ' 
				. $db->nameQuote( '#__community_events_members' )
				. ' WHERE ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $resourceAccesser )
				. ' AND ' . $db->nameQuote( 'status' ) . '=' . $db->Quote( '1' )
				. ' AND ' . $db->nameQuote('eventid') . 'IN'
				. ' (SELECT id FROM '
				. $db->nameQuote( '#__community_events' )
				. "$condition)";
				
		$db->setQuery( $query );
		$result	= $db->loadResult();
		return $result;
	}


	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('events' != $data['view'])
			return false;

		if($data['task']=='updatestatus')
				return true;

		return false;
	}

	function getCategoryId($eventid)
	{
		$query = new XiptQuery();
    	
		return $query->select('catid')
						->from('#__community_events')
						->where("`id` = $eventid")
						->dbLoadQuery("","")
	    				->loadResult();
	}
}
