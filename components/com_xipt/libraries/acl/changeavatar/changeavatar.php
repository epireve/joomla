<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class changeavatar extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}
	
	function checkAclApplicable(&$data)
	{
		/*we will expect that view and task should be given
		 * and from parsing we will find out that is this request for me
		 */
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;

		$permission=$this->aclparams->get('restrict_user_at_registration',false);
		//check whether to restrict at registration time or not
		if('profile' == $data['view'] && $data['task'] == 'uploadavatar')
		 		return true;

		if($permission==true)
		{
			if('register'== $data['view'] && $data['task'] == 'registeravatar')
				return true;
		}

		return false;
	}

	public function getDisplayMessage()
	{
		$option	= JRequest::getVar('option');
		$view	= JRequest::getVar('view');
		$task 	= JRequest::getVar('task');

		// if user has to upload avatr then redirect him to registersuccess page otherwise
		// get url from params
		if($option=='com_community' && $view=='register' && $task=='registerAvatar')
			return '';
		else
			return parent::getDisplayMessage();
	}


	public function getRedirectUrl()
	{
		$option	= JRequest::getVar('option');
		$view	= JRequest::getVar('view');
		$task 	= JRequest::getVar('task');

		// if user has to upload avatr then redirect him to registersuccess page otherwise
		// get url from params
		if($option=='com_community' && $view=='register' && $task=='registerAvatar')
			return "index.php?option=com_community&view=register&task=registerSucess";
		else
			return parent::getRedirectUrl();
	}
}
