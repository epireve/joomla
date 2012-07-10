<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
if(!defined('_JEXEC')) die('Restricted access');
 
class XiptControllerApplications extends XiptController 
{
	function edit($id=0)
	{
		//XITODO : remove edit it
		$id = JRequest::getVar('id', $id);					
		return $this->getView()->edit($id,'edit');				
	}
	
	function save($post=null)
	{
		if($post===null)
			$post	= JRequest::get('post');	
			
		$aid 	  	 = isset($post['id'])? $post['id'] : 0;		
		$otherAid 	 = isset($post['appIds'])? $post['appIds'] : array(); 
		$appPtype	 = isset($post['profileTypes'])? $post['profileTypes'] : array();
		$allTypes 	 = XiptHelperProfiletypes::getProfileTypeArray();
		$model 		 = $this->getModel();
		
		// aid is also selected in otherAid then no need to add
		if(!in_array($aid, $otherAid)) 
			array_push($otherAid, $aid);
			
		//remove all rows related to specific app id		
		foreach($otherAid as $id)
			$model->delete(array('applicationid'=> $id));	
		
		$msg = XiptText::_('APPLICATION_SAVED');
		$link = XiptRoute::_('index.php?option=com_xipt&view=applications', false);
		$this->setRedirect($link,$msg);
		
		//if all selected, return true		
		if(array_diff($allTypes, $appPtype) == array())
			return true;
		
		$ptypesToChange = array_diff($allTypes, $appPtype); 
		foreach($ptypesToChange as $type){				
			foreach($otherAid as $id)
				if($this->getModel()->save(array('applicationid'=>$id,'profiletype'=>$type)) ==false)
			  		return false;
		}
		
		return true;		
	}
}