<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

if(!defined('_JEXEC')) die('Restricted access');

class XiptControllerAclRules extends XiptController 
{
    function add()
	{				
		return $this->getView()->add();
	}
	
	
	function edit($id=0, $acl=0)
	{
		$id = JRequest::getVar('id', $id);
		$acl = JRequest::getVar('acl', $acl) ;
		
		$view = $this->getView();

		XiptError::assert($acl || $id, XiptText::_("NOT_DEFINED $id or $acl"), XiptError::ERROR);
		
		if($acl)
			$aclObject = XiptAclFactory::getAclObject($acl);
		else
			$aclObject = XiptAclFactory::getAclObjectFromId($id);

		XiptError::assert(isset($aclObject), XiptText::_("NOT_ABLE_TO_CREATE_ACL_OBJECT"), XiptError::ERROR);
			
		$aclObject->load($id);

		$data = $aclObject->getObjectInfoArray();
		$data['id'] = $id;
		
		return $view->edit($data);
	}
	
	// XITODO :need test case
	function processSave($id=0, $post=null)
	{
		//save aclparam and core param in individual columns		
		$id 	 = JRequest::getVar('id', $id);
		$data 	 = array();
		if($post === null)	$post	= JRequest::get('post');
		
		$model 	= $this->getModel();		
		
		// Get the complete INI string of params
		$param = new XiptParameter();
		$post['coreparams']['core_display_message'] = base64_encode($post['coreparams']['core_display_message']);
		$param->loadArray($post['coreparams'],'xipt_coreparams');
		$data['coreparams']	= $param->toString('XiptINI' , 'xipt_coreparams' );
		$data['aclname'] 	= $post['aclname'];
		$data['rulename']	= $post['rulename'];
		$data['published'] 	= $post['published'];
		
		$aclObject = XiptAclFactory::getAclObject($data['aclname']);
		$data['aclparams'] = $aclObject->collectParamsFromPost($post);
		
		// Save it // XITODO : clean it
		if(!($info['id'] = $model->save($data,$id)) )
			$info['msg'] = XiptText::_('ERROR_IN_SAVING_RULE');
		else
			$info['msg'] = XiptText::_('RULE_SAVED');	

		return $info;
	}
	
	function save()
	{
		$data = $this->processSave();
		$link = XiptRoute::_('index.php?option=com_xipt&view=aclrules', false);		
		$this->setRedirect($link, $data['msg']);		
	}
	
	function apply()
	{
		$data = $this->processSave();
		$link = XiptRoute::_('index.php?option=com_xipt&view=aclrules&task=edit&id='.$data['id'], false);
		$this->setRedirect($link, $data['msg']);				
	}
	

	function remove($ids=array())
	{
		$ids	= JRequest::getVar('cid', $ids, 'post', 'array');
		$count	= count($ids);
		
		$i = 1;
		foreach( $ids as $id ){		
			if(!$this->getModel()->delete( $id )){
				// If there are any error when deleting, we just stop and redirect user with error.
				$message	= XiptText::_('ERROR_IN_REMOVING_RULE');
				$this->setRedirect( 'index.php?option=com_xipt&view=aclrules' , $message);
				return false;
			}
			$i++;
		}
		
		$message	= $count.' '.XiptText::_('RULE_REMOVED');		
		$link = XiptRoute::_('index.php?option=com_xipt&view=aclrules', false);
		$this->setRedirect($link, $message);
	}			
}
