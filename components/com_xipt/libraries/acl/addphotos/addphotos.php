<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class addphotos extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['userid'];	
	}

	function getFeatureCounts($resourceAccesser,$resourceOwner,$otherptype,$aclSelfPtype)
	{
		$photoModel		=& CFactory::getModel('photos');
		return $photoModel->getPhotosCount($resourceAccesser);
	}

	public function handleViolation($info)
	{
		$msg  = $this->getDisplayMessage();
		$task = array('ajaxpreview', 'jsonupload');
		
		if(in_array($info['task'], $task)){
			$nextUpload	= JRequest::getVar('nextupload');
			echo 	"{\n";
			echo "error: 'true',\n";
			echo "msg: '" . $msg . "'\n,";
			echo "nextupload: '" . $nextUpload . "'\n";
			echo "}";
			exit;
		}

		//let parent handle it
		parent::handleViolation($info);
	}


	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;
//XITODO : message is not appearing.		
		if('photos' != $data['view'])
			return false;

		$task = array('uploader', 'jsonupload', 'addnewupload', 'ajaxpreview', 'ajaxuploadphoto', 'multiupload');
		if(in_array($data['task'], $task))
				return true;

		return false;
	}

}
