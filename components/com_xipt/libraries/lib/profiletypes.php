<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptLibProfiletypes
{
	/**
	 * This function will not change user's profiletype
	 * It only updates user's data, do not add profiletypes
	 * @param $userid
	 * @param $oldData
	 * @param $newData
	 * @return unknown_type
	 */
	function updateUserProfiletypeFilteredData($userid, $filter, $oldData, $newData)
	{
		XiptError::assert($userid, XiptText::_("USERID $userid IS_NOT_VALID"), XiptError::ERROR);
		$uModel = XiptFactory::getInstance('Users','model');
		
		foreach($filter as $feature)
		{
			switch($feature)
			{
				case 'template':
					$template = $newData['template'];
					$ptype    =  XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
					$uModel->save(	array('userid' => $userid,'profiletype'=>$ptype,'template'=>$template),
								 	$userid
								 );
					XiptLibJomsocial::updateCommunityCustomField($userid,$template,TEMPLATE_CUSTOM_FIELD_CODE);
					break;
					
				case 'jusertype' :
					$newJUtype 	= $newData['jusertype']; 
					XiptLibJomsocial::updateJoomlaUserType($userid,$newJUtype);
					break;
					
				case 'avatar' :
					$newAvatar 	= $newData['avatar'];
					XiptLibJomsocial::updateCommunityUserDefaultAvatar($userid,$newAvatar);
					break;
				
				case 'watermark' :
					$newWatermark 	= $newData['watermark']; 
					XiptLibJomsocial::updateCommunityUserWatermark($userid,$newWatermark);
					break;

				case 'group' :
					$newGroup 	= $newData['group'];
					$oldGroup	= $oldData['group'];
					XiptLibJomsocial::updateCommunityUserGroup($userid,$oldGroup, $newGroup);
					break;
				
				case 'privacy':
					$newPrivacy = $newData['privacy'];
					XiptError::assert($newPrivacy);
					$newPrivacy = $newPrivacy->toArray();
							
					XiptLibJomsocial::updateCommunityUserPrivacy($userid,$newPrivacy);
					break;
					
				default:
					XiptError::assert(0, XiptText::_("NOT_A_VALID_OPTION_TO_FILTER"), XiptError::ERROR);
					break;
			}
		}

		//IMP : Reseting user already loaded information
		XiptLibJomsocial::reloadCUser($userid);
	}
	
	/** 
	 * This function is used to update user's profiletype
	 * and its associated data. 
	 * @param $userid
	 * @param $ptype
	 * @param $template
	 * @param $what
	 * @return unknown_type
	 */
	function updateUserProfiletypeData($userid, $ptype, $template, $what='ALL')
	{
		XiptError::assert($userid, XiptText::_("USERID $userid IS_NOT_VALID"), XiptError::ERROR);
		$uModel = XiptFactory::getInstance('Users','model');
		//store prev profiletype
		//IMP : must be first line, as we want to store prev profiletype
		$prevProfiletype = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
		
		if($what == 'profiletype' || $what == 'ALL')
		{
			// trigger an API for before profile type updation
			$dispatcher = JDispatcher::getInstance();
			$userInfo['userid'] 	= $userid;
			$userInfo['oldPtype']	= $prevProfiletype;
			$userInfo['newPtype']	= &$ptype;
			
			/* we are sending refrence of new ptype
			* this should be validate before save 
			*/
			$dispatcher->trigger( 'onBeforeProfileTypeChange',array($userInfo));
			
			// validate profile type, may be changed in event triggered
			if(XiptLibProfiletypes::validateProfiletype($ptype)==false)
				$ptype  = XiptLibProfiletypes::getDefaultProfiletype();
		
			if(!$template) 
				$template = XiptLibProfiletypes::getProfileTypeData($ptype,'template');
				
			//set profiletype and template for user in #__xipt_users table	
			$result = $uModel->save(array('userid' => $userid,'profiletype'=>$ptype,'template'=>$template),
								 	$userid
								 );

			//set profiletype and template field in #__community_fields_values table
			// also change the user's type in profiletype field.
			XiptLibJomsocial::updateCommunityCustomField($userid,$template,TEMPLATE_CUSTOM_FIELD_CODE);
			XiptLibJomsocial::updateCommunityCustomField($userid,$ptype,PROFILETYPE_CUSTOM_FIELD_CODE);
			
			// trigger an API for after profile type updation
			/* send success result */
			//send the result as true
			$dispatcher->trigger( 'onAfterProfileTypeChange',array($ptype,$result));
		}

		$feature=array();
		$oldData=array();
		$newData=array();
		
		//set usertype acc to profiletype in #__user table
		if($what == 'ALL' || $what == 'jusertype')
		{
			$feature[]='jusertype';
			$oldData['jusertype']=self::getProfiletypeData($prevProfiletype,'jusertype');
			$newData['jusertype']=self::getProfiletypeData($ptype,'jusertype');
		}
			
		//set user avatar in #__community_users table
		if($what == 'ALL'  || $what == 'avatar')
		{
			$feature[]='avatar';
			$oldData['avatar'] = self::getProfiletypeData($prevProfiletype,'avatar');
			$newAvatar	 = self::getProfiletypeData($ptype,'avatar');
			if(JString::stristr( $newAvatar , 'components/com_community/assets/user.png'))
				$newAvatar = '';
			$newData['avatar'] = $newAvatar;
		}

		//set user watermark
		if($what == 'ALL'  || $what == 'watermark')
		{
			$feature[]='watermark';
			$oldData['watermark'] = self::getProfiletypeData($prevProfiletype,'watermark');
			$newData['watermark'] = self::getProfiletypeData($ptype,'watermark');
		}
		
		//assign the default group
		if($what == 'ALL'  || $what == 'group')
		{
			$feature[]='group';
			$oldData['group'] = self::getProfiletypeData($prevProfiletype,'group');
			$newData['group'] = self::getProfiletypeData($ptype,'group');
		}
			
		//set privacy data
		if($what == 'ALL'  || $what == 'privacy')
		{
			$feature[] = 'privacy';
			$pModel = XiptFactory::getInstance('profiletypes','model');
			$oldPrivacy	= $pModel->loadParams($prevProfiletype,'privacy');
			$newPrivacy	= $pModel->loadParams($ptype,'privacy');
			
			$oldData['privacy']	=$oldPrivacy;
			$newData['privacy']	= $newPrivacy;
		}
			
		self::updateUserProfiletypeFilteredData($userid,$feature,$oldData,$newData);
		return true;
	}
	

	function getDefaultProfiletype()
	{		
		$refresh = XiptLibJomsocial::cleanStaticCache();
		static $defaultProfiletypeID = null;
		if($defaultProfiletypeID && $refresh === false)
			return $defaultProfiletypeID;
		
		$defaultProfiletypeID = XiptFactory::getSettings('defaultProfiletypeID');
		if($defaultProfiletypeID)
			return  $defaultProfiletypeID;
		
		echo XiptFactory::getSettings()->render();
		XiptError::raiseWarning('DEF_PTYPE_REQ','DEFAULT PROFILE TYPE REQUIRED');
	}
	
	
	function getDefaultTemplate()
	{
		$config	        = CFactory::getConfig();
	    $defaultValue   =  $config->get('template');
	    return $defaultValue;
	}
			
	function getProfiletypeName( $id = 0)
	{
		$val = XiptHelperProfiletypes::getProfileTypeName($id);
		return $val;
	}

	//return array of all published profile type id
	function getProfiletypeArray($filter='')
	{
		//XITODO : we need to add $visible pTypes as per request.move this to model, implement WHERE
		$results = XiptFactory::getInstance('profiletypes','model')->loadRecords(0);
		
		if(empty($filter))
			return $results;
			
		foreach($results as $result){
			foreach($filter as $key => $val){
				if($result->$key != $val){
					unset($results[$result->id]);
					break;
				}
			}
		}	
		
		return $results;
	}
	

	/**
	 * 	 This function return's user's data from tables
	 * @param $userid
	 * @param $what
	 * @return unknown_type
	 */
	//XITODO : move to user model
	function getUserData($userid, $what='PROFILETYPE')
	{
		$results=array();

		switch($what)
	    {
	        case 'PROFILETYPE':
	        	if($userid == 0 )
					return XiptFactory::getSettings('guestProfiletypeID', XiptFactory::getSettings('defaultProfiletypeID', 0));
		        $getMe	       = PROFILETYPE_FIELD_IN_USER_TABLE;
                $defaultValue  = XiptLibProfiletypes::getDefaultProfiletype();
                break;
                
	        case 'TEMPLATE':
                $getMe	= TEMPLATE_FIELD_IN_USER_TABLE;
                $allTemplates = XiptHelperJomsocial::getTemplatesList();
       		    $pID          = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
       		    $defaultValue = XiptLibProfiletypes::getProfileTypeData($pID,'template');

        		//else get system template
        		if(in_array($defaultValue,$allTemplates)===false)
			        $defaultValue   =  XiptLibProfiletypes::getDefaultTemplate();

                break;
                
	        default :
	            XiptError::raiseError('XIPT-SYSTEM-ERROR','XIPT System Error');
	    }

		$results = XiptFactory::getInstance('users','model')->loadRecords(0);
				
		// not a valid result OR value not set
		if(!$results || isset($results[$userid]) == false){
		    return $defaultValue;
		}
		
		$what = strtolower($what);
		return $results[$userid]->$what;
	}
	
	/**
	 * @param $id 	: profile-type ID
	 * @param $what : attribute required, default is name
	 * @return unknown_type
	 */
	/*function getProfiletypeData($id=0, $what='name')
	{
		$cache =  JFactory::getCache('com_xipt');
		return $cache->call(array('XiptLibProfiletypes','_getProfiletypeData'),$id=0, $what);
	}*/
	
	function getProfiletypeData($id = 0, $what = 'name')
	{

		$val = XiptHelperProfiletypes::getProfileTypeData($id, $what);
		return $val;
	}
	
	// returns all user of profiletype
	function getAllUsers($pid)
	{
		$results 	  = XiptFactory::getInstance('users', 'model')->loadRecords(0);
		$defaultPtype = self::getDefaultProfiletype();
		
		$defaultPtypeCheck = $pid;
		if($defaultPtype == $pid)
			$defaultPtypeCheck = 0;
			
		foreach($results as $result){
			if($result->profiletype == $pid 
				|| $result->profiletype == $defaultPtypeCheck)
				continue;
				
			unset($results[$result->userid]);			
		}

		return array_keys($results);
	}
		
	//call fn to get fields related to ptype in getviewable and geteditable profile fn
	function filterCommunityFields($userid, &$fields, $from)
	{
	    //durin loadAllfields no user id avaialble
	    // so we pick the pType from registration 
	    if($userid == 0 )
	        $pTypeID = XiptFactory::getPluginHandler()->getRegistrationPType();
	    else
	        $pTypeID = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
     
		//(fields) Privacy should not be applicable on Admin
//	    if(XiptHelperUtils::isAdmin($userid) == true){
//	    	return true;
//	    }
    	
	    // filter the fields as per profiletype
		$model = XiptFactory::getInstance('Profilefields','model');
	    $model->getFieldsForProfiletype($fields, $pTypeID, $from);
	}
	
	
    // Checks if given avatar is default profiletype avatar
    // or default of one of ProfileType? 
	function isDefaultAvatarOfProfileType($path, $isDefaultCheckRequired = false)
	{
		//if default check required 
		//we should not ignore case for windows 
		if($isDefaultCheckRequired)
		{
			$val1 = JString::stristr(DEFAULT_AVATAR,$path);
			$val2 = JString::stristr(DEFAULT_AVATAR_THUMB,$path);
			if( $val1 || $val2 )
				return true;
		}	
		
		//if user avatar contains "STORAGE_PATH/avatar_" then it is default avatar
		if(JString::stristr($path,PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH.DS.'avatar_'))
			return true;
		
		static $allAvatars = null ;
		//it will improve the performance
		if($allAvatars == null)
		{
			$searchFor 	= 'avatar';
			$allAvatars = array();
			$records = XiptFactory::getInstance('profiletypes', 'model')->loadRecords(0);
			foreach($records as $record)
				array_push($allAvatars, $record->$searchFor);
				
			if(empty($allAvatars))
				return true;
		}
			
		foreach($allAvatars as $av)
		{   
			if(empty($av))
		       continue;

	    	if(JString::stristr($av ,$path))
				return true;
			if(JString::stristr($path, XiptHelperImage::getThumbAvatarFromFull($av)))
				return true;
		}
		
		return false;
	}
	
    /**
     * If profiletype exist and published return true
     * else return false,
     * IMP : If empty profiletype returns false
     *
     * @param $profileTypeID
     * @return boolean
     */
    function validateProfiletype($profileTypeID, $filter=array('published'=>1))
	{
		if(empty($profileTypeID))
			return false;
		
		$allProfileTypes = XiptLibProfiletypes::getProfiletypeArray($filter);
		
		if(empty($allProfileTypes))
			return false;
			
		$profiletypeIDs = array_keys($allProfileTypes);
		if(in_array($profileTypeID, $profiletypeIDs))
			return true;

		return false;
	}
	
	function getParams($id,$what='params')
	{
		$model = XiptFactory::getInstance('Profiletypes','model');
		$params = $model->loadParams($id,$what);
		return $params;		
	}
	
}

