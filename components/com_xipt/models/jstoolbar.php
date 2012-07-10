<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Disallow direct access to this file
if(!defined('_JEXEC')) die('Restricted access');

class XiptModelJSToolbar extends XiptModel
{
	/**
	 * Returns the Application name
	 * @return string
	 **/
	function getMenu($menuId=null, $limit=null, $limitstart=null)
	{
		static $result=null;
		if($result== null){
			$query = new XiptQuery();
			
		if(XIPT_JOOMLA_15){
			$result = $query->select('*')
			        		->from('#__menu')
						    ->where(" `menutype` = 'jomsocial' and `parent` = 0")
							->order('ordering')
							->limit($limit,$limitstart)
							->dbLoadQuery("","")
							->loadObjectList('id');		
		}
		else{	
			$result = $query->select('*')
			        		->from('#__menu')
						    ->where(" `menutype` = 'jomsocial' and `parent_id` = 1")
							->order('ordering')
							->limit($limit,$limitstart)
							->dbLoadQuery("","")
							->loadObjectList('id');	
		}		
		}
		
		if($menuId == null && $result)
			return $result;
			
		if(isset($result[$menuId]) && !empty($result[$menuId]))
			return $result[$menuId];
		else
			return false;
	}
	
	function getProfileTypes($menuid)
	{
		if(isset($this->_ptypes[$menuid]))
			return $this->_ptypes[$menuid];
			
		$query = new XiptQuery();
		return  $this->_ptypes[$menuid] = $query->select('profiletype')
					 						 ->from('#__xipt_jstoolbar')
					 						 ->where(" `menuid` = $menuid ")
					 						 ->dbLoadQuery("", "")
			  		 						 ->loadResultArray();		
	}
}