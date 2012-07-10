<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
if(!defined('_JEXEC')) die('Restricted access');
 
class XiptControllerJSToolbar extends XiptController 
{
	function edit($id=0)
	{
		$id = JRequest::getVar('id', $id);					
		return $this->getView()->edit($id,'edit');				
	}
	
	function save($post=null)
	{
		if($post===null)
			$post	= JRequest::get('post');	
			
		$menuid 	  	 = isset($post['id'])? $post['id'] : 0;		
		$otherMenuid 	 = isset($post['menuIds'])? $post['menuIds'] : array(); 
		$menuPtype	 	 = isset($post['profileTypes'])? $post['profileTypes'] : array();
		$allTypes 	 	 = XiptHelperProfiletypes::getProfileTypeArray();
		$model 		 	 = $this->getModel();
		
		// menuid is also selected in otherMenuid then no need to add
		if(!in_array($menuid, $otherMenuid)) 
			array_push($otherMenuid, $menuid);
			
		//remove all rows related to specific menu id		
		foreach($otherMenuid as $id)
			$model->delete(array('menuid'=> $id));	
		
		$msg  = XiptText::_('MENU_SAVED');
		$link = XiptRoute::_('index.php?option=com_xipt&view=jstoolbar', false);
		$this->setRedirect($link,$msg);
		
		//if all selected, return true		
		if(array_diff($allTypes, $menuPtype) == array())
			return true;
		
		$ptypesToChange = array_diff($allTypes, $menuPtype); 
		foreach($ptypesToChange as $type){				
			foreach($otherMenuid as $id)
				if($this->getModel()->save(array('menuid'=>$id,'profiletype'=>$type)) ==false)
			  		return false;
		}
		
		return true;		
	}
}