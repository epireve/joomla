<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class redirect extends XiptAclBase
{
	public function checkAclViolation($data)
	{

		$redirectUrl  	= XiptRoute::_($this->getRedirectUrl());
		$redirectURI 	= new JURI($redirectUrl);
		$redirectVar = $redirectURI->getQuery(true);

		foreach($redirectVar as $key=> $value)
		{
			if(array_key_exists($key, $data))
			{
				if($value !=  $data[$key])
					return true;
			}
		}

		return false;
	}

	function getResourceOwner($data)
	{
		return $data['userid'];	
	}

	function checkAclApplicable(&$data)
	{
		$user=JFactory::getUser();
		if(!$user->id)
			return false;

		if ('com_user' == $data['option'])
			return false;

		if('com_acctexp' == $data['option'])// && 'atexp' != $data['option'])
			return false;

		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
			return false;

		return true;
	}
}
