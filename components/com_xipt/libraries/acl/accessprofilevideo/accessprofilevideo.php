<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class accessprofilevideo extends XiptAclBase
{
	function getResourceOwner($data)
	{
		$videoid	= $data['args'][0];
		$ownerid	= $this->getownerId($videoid);
		return $ownerid;
	}
	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		if('profile' != $data['view'])
			return false;

		if($data['task'] === 'ajaxplayprofilevideo')
				return true;

		return false;
	}

	function getownerId($id)
    {
    	$query = new XiptQuery();
    	
    	return $query->select('creator')
    				 ->from('#__community_videos')
    				 ->where(" `id` = $id ")
    				 ->dbLoadQuery("","")
    				 ->loadResult();
    }


	function aclAjaxBlock($msg)
	{
		$objResponse   	= new JAXResponse();
		$title		= XiptText::_('CC_PROFILE_VIDEO');
		$objResponse->addScriptCall('cWindowShow', '', $title, 430, 80);
		return parent::aclAjaxBlock($msg, $objResponse);
	}

}
