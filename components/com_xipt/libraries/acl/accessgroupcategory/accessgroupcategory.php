<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class accessgroupcategory extends XiptAclBase
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
		
		if($this->isApplicableForGroupCategory($data)=== true)
			return false;
				
		return true;
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
	
	function isApplicableForGroupCategory($data)
	{
		$groupId	= isset($data['groupid'])? $data['groupid'] : 0;
		$groupId	= JRequest::getVar('groupid' , $groupId, 'REQUEST');
		$db 		= JFactory::getDBO();
		$query		= 'SELECT '.$db->nameQuote('categoryid')
						.' FROM '.$db->nameQuote('#__community_groups')
						.' WHERE '.$db->nameQuote('id').' = '.$db->Quote($groupId);

		$db->setQuery( $query );
		$result = $db->loadObject();
		if(!$result)
			return false;
		$aclgroup=$this->aclparams->get('group_category');
		if ($aclgroup === $result->categoryid)
			return true;
			
		return false;
	}

}
