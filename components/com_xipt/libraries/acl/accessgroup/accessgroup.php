<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class accessgroup extends XiptAclBase
{
	function getResourceOwner($data)
	{
		$groupId	= isset($data['groupid'])? $data['groupid'] : 0;
		$groupId	= JRequest::getVar('groupid' , $groupId, 'REQUEST');

		$groupData  = CFactory::getModel('groups')->getGroup($groupId);
		$ownerid	= $groupData->ownerid;
		return $ownerid;
	}
	
	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('groups' != $data['view'])
			return false;

		if($data['task'] === 'viewgroup')
				return true;

		return false;
	}

}
