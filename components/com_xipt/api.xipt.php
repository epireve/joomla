<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


require_once JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php';

class XiptAPI
{
    
	/**
	 * Collect User's Profiletype from user-id
	 * 
	 * @param $userid
	 * @param $what (default value is "id", you can ask for "name" too)
	 * @return int (id) or String (Name)
	 */
	function getUserProfiletype($userid, $what='id')
	{
	    $pID = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
	    if($what == 'id')
	        return $pID;

	    //else
        return XiptLibProfiletypes::getProfiletypeName($pID);
	}
	
	/**
	 * Use this function to update user's profiletype
	 * @param $userId			: Which user's profiletype should be updated
	 * @param $profiletypeId	: New Profiletype ID
	 * @param $reset			: Which attributes should be reset, default is ALL
	 * 							  or you can use (profiletype, jusertype, avatar, group, privacy etc.)
	 * @return unknown_type
	 */
	function setUserProfiletype($userId, $profiletypeId, $reset = 'ALL')
	{
		return XiptLibProfiletypes::updateUserProfiletypeData($userId,$profiletypeId,null, $reset);
	}

	/**
	 * Gives all the profiletypes attributes -
	 *  
	 *  - if "id" is not given then returns all profiletypes
	 *   
	 * @param $id
	 * @param $filter : Associative array to define conditions
	 * @return Array of Profiletype Objects
	 */
	function getProfiletypeInfo($id=0, $filter=array())
	{
		//$filter = array('published'=>$onlypublished);
	    $allPT = XiptLibProfiletypes::getProfiletypeArray($filter);

	    //no profiletype available
	    if(!$allPT)
	        return null;
	        
	    //no id, return all
	    if(!$id || $id < 0)
	        return $allPT;
	        
	    //return specfic array
	    if(isset($allPT[$id])){
	        //return always an array
	        $retVal[] = $allPT[$id];
	           return $retVal;
	     }

	    // invalid id 
	    return null;  
	}
	
	/*
	 * Returns default profiletype 
	 */
	function getDefaultProfiletype()
	{
		return XiptLibProfiletypes::getDefaultProfiletype();
	}
	
	/**
	 * returns user information
	 * @param $userid : 
	 * @param $what : can be 'PROFILETYPE' or 'TEMPLATE'
	 * @return unknown_type
	 */
	function getUserInfo($userid, $what='PROFILETYPE')
	{
		return XiptLibProfiletypes::getUserData($userid,$what);		
	}
	
	
	/**
	 * Returns any global configuration settings in JSPT 
	 * @param $paramName : the value of which variable you require
	 * @param $defaultValue
	 * @return unknown_type
	 */
	function getGlobalConfig($paramName='', $defaultValue=0)
	{
		if($paramName === '')
			return null;
			
		return XiptFactory::getSettings($paramName ,$defaultValue);
	}
	
/*
	 * Get Profile Type Name
	 * @profileTypeId, Profile Id
	 */
	function getProfileTypeName($profileTypeId) {
		
		return XiptLibProfiletypes::getProfiletypeName($profileTypeId);
	}
	
	/*
	 * return array of all published Profile Type id
	 * @filter, use for filter Profile Type 
	 */
	function getProfileTypeIds($filter= '') {
		
		return XiptLibProfiletypes::getProfiletypeArray($filter);
	}
	
	/*
	 * return JomSocial Profile Fields
	 */
	function getJSProfileFields($fieldId=0) {
		return XiptLibJomsocial::getFieldObject($fieldId);
	}
	
	/*
	 * filter Profile-Type fields according to Profile Type
	 */

	function filterProfileTypeFields(&$fields, $selectedProfiletypeID, $from) {
		
		return XiptFactory::getInstance('profilefields','model')
							->getFieldsForProfiletype($fields, $selectedProfiletypeID, $from);
	}
}
