<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class addvideos extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}
	
	function getFeatureCounts($resourceAccesser,$resourceOwner,$otherptype,$aclSelfPtype)
	{
		$query = new XiptQuery();
    	
    	return $query->select('COUNT(*)')
    				 ->from('#__community_videos')
    				 ->where(" `creator` = $resourceAccesser ", 'AND')
    				 ->where(" `published` = '1' ", 'AND')
    				 ->where(" `status` = 'ready' ")
    				 ->dbLoadQuery("","")
    				 ->loadResult();
	}


	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('videos' != $data['view'])
			return false;

		if($data['task'] == 'ajaxaddvideo' || $data['task'] == 'ajaxuploadvideo' || $data['task'] == 'ajaxlinkvideopreview')
				return true;

		return false;
	}

	function aclAjaxBlock($msg)
	{
		$objResponse   	= new JAXResponse();
		
		$objResponse->addScriptCall('cWindowShow', '','', 430, 80);
		return parent::aclAjaxBlock($msg, $objResponse);
	}
}
