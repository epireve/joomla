<?php
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class likeevent extends XiptAclBase
{
	function getResourceOwner($data)
	{
		$eventId	= isset($data['args'][1]) ? $data['args'][1] : 0;
		$ownerid	= $this->getownerId($eventId);
		return $ownerid;
	}
	
	function aclAjaxBlock($msg)
	{
		$objResponse = new JAXResponse();
		$title		 = XiptText::_('CC_PROFILE_VIDEO');
		$objResponse->addScriptCall('cWindowShow', '', $title, 430, 80);
		return parent::aclAjaxBlock($msg, $objResponse);
	}  
	  
	function checkAclApplicable(&$data)
	{
		if('com_community' == $data['option'] && 'system' == $data['view']
		    && ($data['task'] == 'ajaxlike' || $data['task'] == 'ajaxdislike') 
		    && $data['args'][0] == 'events')
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
