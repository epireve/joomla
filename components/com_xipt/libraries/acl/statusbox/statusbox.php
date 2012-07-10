<?php
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class statusbox extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}
	
	function checkAclApplicable(&$data)
	{
		//check data value for JomSocial 2.x.x
		if('com_community' == $data['option']
		    	&& 'system' == $data['view']
		    	&& $data['task'] == 'ajaxstreamadd')
				return true;
		
		
		if('com_community' == $data['option']
		    	&& 'status' == $data['view']
		    	&& $data['task'] == 'ajaxupdate')
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
