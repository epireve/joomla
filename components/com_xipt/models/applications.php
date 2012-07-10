<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Disallow direct access to this file
if(!defined('_JEXEC')) die('Restricted access');

class XiptModelApplications extends XiptModel
{
	/**
	 * Returns the Application name
	 * @return string
	 **/
	function getPlugin($pluginId=null, $indexBy=XIPT_JOOMLA_EXT_ID, $limit=null, $limitstart=null)
	{		
		static $result=null;
		if($result== null){
			$query = new XiptQuery();
			
			if (XIPT_JOOMLA_15){
				$result = $query->select('*')
				        		->from('#__plugins')
							    ->where(" `folder` = 'community' ")
								->order('ordering')
								->limit($limit,$limitstart)
								->dbLoadQuery("","")
								->loadObjectList($indexBy);
			}
			else{
				$result = $query->select('*')
				        		->from('#__extensions')
							    ->where(" `folder` = 'community' ")
								->order('ordering')
								->limit($limit,$limitstart)
								->dbLoadQuery("","")
								->loadObjectList($indexBy);
			}			
		}
		
		if($pluginId == null && $result)
			return $result;
			
		if(isset($result[$pluginId]) && !empty($result[$pluginId]))
			return $result[$pluginId];
		else
			return false;
	}
	
	function getProfileTypes($aid)
	{
		if(isset($this->_ptypes[$aid]))
			return $this->_ptypes[$aid];
			
		$query = new XiptQuery();
		return  $this->_ptypes[$aid] = $query->select('profiletype')
					 						 ->from('#__xipt_applications')
					 						 ->where(" `applicationid` = $aid ")
					 						 ->dbLoadQuery("", "")
			  		 						 ->loadResultArray();		
	}
}