<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptFieldsProfiletypesJs18 extends XiptFieldsProfiletypesBase
{
	function getTemplateValue($value,$userid)
	{
		// during registration
        if($this->_view =='register'){
            $pID = XiptFactory::getPluginHandler()->getRegistrationPType();
		    $tName = XiptLibProfiletypes::getProfileTypeData($pID,'template');
		    return $tName;
        }
		
        if($value)
            $tName=$value;
        else
        {
	        //a valid or default value
	        $tName = XiptLibProfiletypes::getUserData($userid,'TEMPLATE');
        }
        return $tName;
	}
	
	function getFieldData( $value = 0 )
	{
		$pID = $value;
		
		if(!$pID){
			//get value from profiletype field from xipt_users table
			//not required to get data from getUser() fn b'coz we call this fn in 
			//getViewableprofile only.
			$userid = JRequest::getVar('userid',0);
			XiptError::assert($userid,XiptText::_("USERID $userid DOES_NOT_EXIST"), XiptError::ERROR);
			$pID = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
		}
		
		$pName = XiptLibProfiletypes::getProfiletypeName($pID);
		return $pName;
	}	
	
	/* if data not available,
	 * then find user's profiletype and return
	 * else present defaultProfiletype to community
	 *
	 * So there will be always a valid value returned
	 * */
	function formatData($value=0)
	{
	    $pID = $value;
		
		if(!$pID){
			//get value from profiletype field from xipt_users table
			$userid = JRequest::getVar('userid',0);
			$pID = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
		}
		return $pID;
	}	
} 
