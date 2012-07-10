<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptControllerConfiguration extends XiptController
{
	//Need to override, as we dont have model
	public function getModel($modelName=null)
	{
		// support for parameter
		if($modelName===null || $modelName === $this->getName())
			return parent::getModel('profiletypes');

		return parent::getModel($modelName);
	}

	function edit($id=0)
	{
		$id = JRequest::getVar('id', $id);
		return $this->getView()->edit($id,'edit');
	}

	function save($id=0, $postData=null)
	{
		$id	= JRequest::getVar('id', $id);
		if($postData === null)
			$postData	= JRequest::get('post', JREQUEST_ALLOWRAW );

		// unset the data which is not required
		unset($postData[JUtility::getToken()]);
		unset($postData['option']);
		unset($postData['task']);
		unset($postData['view']);
		unset($postData['id']);
		
		$pModel	= $this->getModel();

		$save = $pModel->saveParams($postData, $id, 'params');

		// Try to save configurations
		if(XiptError::assert($save , XiptText::_( 'UNABLE_TO_SAVE_CONFIGURATION_INTO_DATABASE_PLEASE_ENSURE_THAT_THE_TABLE_JOS_COMMUNITY_CONFIG_EXISTS' ), XiptError::WARNING) == false)
			return false;

		$link = XiptRoute::_('index.php?option=com_xipt&view=configuration', false);
		$msg  = XiptText::_('CONFIGURATION_UPDATED');
		$this->setRedirect($link,$msg);
		return true;
	}

	function reset($id=0)
	{
		//XITODO : what to do if invalid id comes 
		$id		= JRequest::getVar( 'profileId',$id);
		$pModel	= $this->getModel();

		// Try to save configurations
		$save = $pModel->save(array('params'=>''), $id);
		if(XiptError::assert($save , XiptText::_( 'UNABLE_TO_RESET_PROFILETYPE_INTO_DATABASE_PLEASE_ENSURE_THAT_THE_TABLE_JOS_XIPT_PROFILETYPES_EXISTS' ), XiptError::WARNING) == false)
			return false;
		
		$link 	= XiptRoute::_('index.php?option=com_xipt&view=configuration', false);
		$msg 	= XiptText::_('PROFILETYPE_HAS_BEEN_RESET');
		$this->setRedirect($link,$msg);
		return true;
	}
}