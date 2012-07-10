<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class uploadavatar extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}
	
	function checkCoreApplicable($data)
	{
		$ptype = $this->getCoreParams('core_profiletype',XIPT_PROFILETYPE_ALL);

		//All means applicable
		if(XIPT_PROFILETYPE_ALL == $ptype)
			return true;

		//profiletype matching
		$userpt = JFactory::getSession()->get('sessionpt', false, 'XIPT');
		
		if(XiptLibProfiletypes::getUserData($data['userid']) == $ptype || $userpt == $ptype)
			return true;

		return false;
	}
	
	function isApplicableOnSelfProfiletype($resourceAccesser)
	{
		$aclSelfPtype = $this->getACLAccesserProfileType();
		
		$sessionPid   = JFactory::getSession()->get('sessionpt', false, 'XIPT');
		if($sessionPid)
			$selfPid = $sessionPid;
		else
			$selfPid	  = XiptLibProfiletypes::getUserData($resourceAccesser,'PROFILETYPE');
			
		if(in_array($aclSelfPtype, array(XIPT_PROFILETYPE_ALL,$selfPid)))
			return true;

		return false;
	}
	
	function checkAclApplicable(&$data)
	{
		$session	= JFactory::getSession();
		$permission = $this->aclparams->get('upload_avatar_at_registration',false);
		$post		= JRequest::get('post');
			
		// When user login then force to upload avatar
		$userId = JFactory::getUser()->id;
		
		if(!empty($userId) && $data['task'] === 'logout'){
			$session->clear('uploadAvatar','XIPT');
			return false;
		}
		if(!empty($userId) && $data['task'] !== 'uploadavatar'){
			//get login user avatar
			$userAvatar = CFactory::getUser($userId)->_avatar;
			//if avatar is deafaul then force to upload avatar
			if(JString::stristr( $userAvatar , 'components/com_community/assets/default.jpg') || empty($userAvatar)) {
				$session->set('uploadAvatar',true,'XIPT');
				return true;
			}
			else 
				return false;
		}
				
		if($permission && $session->get('uploadAvatar',false,'XIPT') 
			&& isset($post['action']) && $post['action'] === 'doUpload'){
			$session->clear('uploadAvatar','XIPT');
			$session->clear('sessionpt','XIPT');
		}
		//if user login and have a avatar then not apply
		if($userId && $permission){
		 	return false; 
		}
		
		//On Registeration Time:: if user come to uoload avatr then all link are disable untill user not upload avatar
		if($permission && $session->get('uploadAvatar',false,'XIPT') && $data['task'] !== 'registeravatar'){
			return true;
		}
		
		// When not registered than dont follow this rule until reach at upload avatar page through ragistration
		if('com_community' != $data['option'] && 'community' != $data['option']){
			return false;
		}

		// Set session variable at registration time
		if('register'== $data['view'] && $data['task'] === 'registeravatar'){
			if(!isset($post['action']) || (isset($post['action']) && $post['action'] != 'doUpload')){
				$session->set('uploadAvatar',true,'XIPT');
			}	
			//XiTODO::add javascript for Click on upload button with image path.(without image-path does nt submit form)
		}
		
		// if you click on "SKIP" url then apply rule and not redirect to success  
		if($permission && 'register' == $data['view'] 
		&& $data['task'] == 'registersucess' && $session->get('uploadAvatar',false,'XIPT')){
				return true;
		}
		return false;
	}

	public function getDisplayMessage()
	{
		$session	= JFactory::getSession();
		if($session->get('uploadAvatar',false,'XIPT')){
			//return XiptText::_('PLEASE_UPLOAD_AVATR_FOR_COMPLEATE_RAGISTRATION');
			return parent::getDisplayMessage();
		}	
	}

	
	public function getRedirectUrl()
	{
		$session = JFactory::getSession();
		$userId  = JFactory::getUser()->id;
		// if user is login and not uploaded avatar then redierct to upload avatar page
		if($session->get('uploadAvatar',false,'XIPT') && !empty($userId)){
			return "index.php?option=com_community&view=profile&task=uploadAvatar";
		}
		// if new-user is registering then all url link redirect to upload avatar after fill-up all info
		if($session->get('uploadAvatar',false,'XIPT')){	
			return "index.php?option=com_community&view=register&task=registerAvatar";
		}
		
		return "index.php?option=com_community&view=register&task=registerSucess";
	}
}
