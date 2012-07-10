<?php
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class likeprofile extends XiptAclBase
{
	function getResourceOwner($data)
	{
		 return $data['args'][1];
	}
	
	function aclAjaxBlock($msg)
	{
		$objResponse   	= new JAXResponse();
		$title		= XiptText::_('CC_PROFILE_VIDEO');
		$objResponse->addScriptCall('cWindowShow', '', $title, 430, 80);
		return parent::aclAjaxBlock($msg, $objResponse);
	}  
	  
	function checkAclApplicable(&$data)
	{
		if('com_community' == $data['option'] && 'system' == $data['view']
		    	&& ($data['task'] == 'ajaxlike' || $data['task'] == 'ajaxdislike') 
		    	&& $data['args'][0] == 'profile')
			return true;

		return false;
	}
	
}
