<?php
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class deleteprofilevideo extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}
	
	function checkAclApplicable(&$data)
	{
		if('com_community' == $data['option']
		    	&& 'profile' == $data['view']
		    	&& $data['task'] == 'ajaxremovelinkprofilevideo')
			return true;

		return false;
	}


}

