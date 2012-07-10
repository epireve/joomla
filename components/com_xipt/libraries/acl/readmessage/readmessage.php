<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class readmessage extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}
	
	function checkAclApplicable(&$data)
	{
		if('com_community' == $data['option'] && 'inbox' == $data['view'] )
		    	if($data['task'] === 'read')
			return true;
			
		return false;
	}
}