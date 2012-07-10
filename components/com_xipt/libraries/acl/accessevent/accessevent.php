<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class accessevent extends XiptAclBase
{
	
	function getResourceOwner($data)
	{
		$eventId	= isset($data['eventid']) ? $data['eventid'] : 0;
		$eventId	= JRequest::getVar( 'eventid' , $eventId, 'REQUEST');
		$ownerid	= $this->getownerId($eventId);
		return $ownerid;
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

    function getownerId($id)
    {
    	$query = new XiptQuery();
    	
    	return $query->select('creator')
    				 ->from('#__community_events')
    				 ->where(" `id` = $id ")
    				 ->dbLoadQuery("","")
    				 ->loadResult();
    }

}