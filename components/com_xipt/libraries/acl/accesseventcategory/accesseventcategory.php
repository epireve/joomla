<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class accesseventcategory extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];
	}
	
	function checkAclViolation(&$data)
	{	
		$resourceAccesser 	= XiptAclBase::getResourceAccesser($data);		
		
		if(XiptAclBase::isApplicableOnSelfProfiletype($resourceAccesser) === false)
			return true; 
		
		if($this->isApplicableForEventCategory($data)=== true)
			return false;
				
		return true;
	}
	
	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('events' != $data['view'])
			return false;

		if($data['task'] === 'viewevent')
				return true;

		return false;
	}
	
	function isApplicableForEventCategory($data)
	{
		$eventId	= isset($data['eventid'])? $data['eventid'] : 0;
		$eventId	= JRequest::getVar('eventid' , $eventId, 'REQUEST');
		$db 		= JFactory::getDBO();
		$query		= 'SELECT '.$db->nameQuote('catid')
						.' FROM '.$db->nameQuote('#__community_events')
						.' WHERE '.$db->nameQuote('id').' = '.$db->Quote($eventId);

		$db->setQuery( $query );
		$result = $db->loadObject();
		if(!$result)
			return false;
		$aclevent=$this->aclparams->get('event_category');
		if ($aclevent === $result->catid)
			return true;
			
		return false;
	}

}
