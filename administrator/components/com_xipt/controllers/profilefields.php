<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptControllerProfileFields extends XiptController 
{	
	function edit($fieldId=0)
	{
		$fieldId = JRequest::getVar('id', $fieldId);		
		return $this->getView()->edit($fieldId,'edit');
	}
	
	//save fields which is not accsible , means in opposite form
	// like field1 is visible to ptype 1 not to ptype 2 and 3 , then store 2 and 3
	//by default all fields are visible to all ptype
	//if all is selected then store nothing
	//remove old fields
	
	function save($post=null)
	{
		if($post === null)
			$post	= JRequest::get('post');		
					
		$fid 	  	 = isset($post['id'])? $post['id'] : 0;		
		$otherFids 	 = isset($post['fieldIds'])? $post['fieldIds'] : array();		
		$allTypes 	 = XiptHelperProfiletypes::getProfileTypeArray();
		$fieldModel	 = $this->getModel();
		
		// fid is also selected in otherAid then no need to add
		if(!in_array($fid, $otherFids)) 
			array_push($otherFids, $fid);
			
		//remove all rows related to specific field id 
		// cleaning all data for storing new profiletype with fields
		foreach($otherFids as $id)		
			$fieldModel->delete(array('fid'=>$id));
		
		
		$categories		= XiptHelperProfilefields::getProfileFieldCategories();
		// for each category
		foreach($categories as $catIndex => $catInfo)
		{
			$controlName     = $catInfo['controlName'];
			$selectedPtypes  = isset($post[$controlName])? $post[$controlName] : array();		
			
			// if all profile types are selected
			$ptypesToChange = array_diff($allTypes, $selectedPtypes); 
			if($ptypesToChange == array())
				continue;
			
			// for each profile type			
			foreach($ptypesToChange as $type)
				foreach($otherFids as $id)
					if($this->getModel()->save(array('fid'=>$id, 'pid'=>$type, 'category'=>$catIndex)) == false)
						return false;											
		}
			
		$msg 	= XiptText::_('FIELDS_SAVED');	
		$link 	= XiptRoute::_('index.php?option=com_xipt&view=profilefields', false);
		$this->setRedirect($link,$msg);
		return true;
	}
}
