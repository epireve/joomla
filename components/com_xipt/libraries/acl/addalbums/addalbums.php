<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class addalbums extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}
	
	function getFeatureCounts($resourceAccesser,$resourceOwner,$otherptype,$aclSelfPtype)
	{
		$query = new XiptQuery();
    	
    	return $query->select('COUNT(*)')
    				 ->from('#__community_photos_albums')
    				 ->where(" `creator` = $resourceAccesser ")
    				 ->dbLoadQuery("","")
    				 ->loadResult();    				 
	}


	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('photos' != $data['view'])
			return false;

		if('newalbum' != $data['task'])
			return false;

		return true;
	}

}
