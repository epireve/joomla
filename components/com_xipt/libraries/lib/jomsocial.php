<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
if(!defined('_JEXEC')) die('Restricted access');

//This class contains all logic for XIPT & JomSocial & Joomla Table Communication

class XiptLibJomsocial
{
	//will return object of field as per fieldId
    function getFieldObject($fieldid=0)
	{
		$reset = self::cleanStaticCache();
		static $result = null;
		if(isset($result[$fieldid]) && $reset === false)
			return $result[$fieldid];
			
		$query  = new XiptQuery();
		$result = $query->select('*')
						->from('#__community_fields')
						->order('ordering')
						->dbLoadQuery()
						->loadObjectList('id');
						
		if($fieldid === 0)
			return $result;
			
		return $result[$fieldid];
	}
	
    //get required user info from community_users table
	function getUserDataFromCommunity($userid,$what)
	{
		XiptError::assert($what, XiptText::_("INFO_IS_EMPTY"), XiptError::ERROR);
		
		static $results = array();
		$reset = self::cleanStaticCache();
		
		if(isset($results[$userid][$what]) && $reset === false)
			return $results[$userid][$what];

		$query   = new XiptQuery();
		$results = $query->select('*')
						->from('#__community_users')
						->where("userid <= ($userid+50) OR userid >= ($userid-50)")
						->dbLoadQuery()
						->loadAssocList('userid');						
		
		return $results[$userid][$what];
	}
	
    /**
     * Save user's joomla-user-type
     * @param $userid
     * @param $newUsertype
     * @return true/false
     */
    function updateJoomlaUserType($userid, $newUsertype=JOOMLA_USER_TYPE_NONE)
	{
	    //do not change usertypes for admins
		if(XiptHelperUtils::isAdmin($userid)==true || (0 == $userid )||$newUsertype === JOOMLA_USER_TYPE_NONE){
		    return false;
		}

		//self::reloadCUser($userid);
		
		$user 			= CFactory::getUser($userid);
		$authorize		= JFactory::getACL();
		$user->set('usertype',$newUsertype);
		if (XIPT_JOOMLA_15){
			$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
		}
		else{
			$group = CACL::getInstance();
			$groups[]=$group->getGroupID($newUsertype);			
			JUserHelper::setUserGroups($userid,$groups);
		}
		
		$user->save();
		
		self::reloadCUser($userid);
		return true;
	}
	

	/**
	 * @param  $instance
	 * @return CConfig
	 */
	function updateCommunityConfig(&$instance, $userId = null)
	{
		// skip these calls from backend
		if(JFactory::getApplication()->isAdmin())
			return true;

		$loggedInUser = JFactory::getUser($userId);
			
		$pID = XiptLibProfiletypes::getUserData($loggedInUser->id,'PROFILETYPE');
		
		if(JRequest::getVar('view') === 'register'){
			$pluginHandler = XiptFactory::getPluginHandler();
			$pID 		   = $pluginHandler->getRegistrationPType();
		}
					
		
		XiptError::assert($pID, sprintf(XiptText::_("PID_IS_NOT_VALID"),$pID), XiptError::ERROR);
		$params = XiptLibProfiletypes::getParams($pID);

		if($params)
		{
			$allParams = $params->toArray();
		

			if(!empty($allParams)){
				foreach($allParams as $key => $value)
					$instance->set($key,$value);
			}

		}
		//means guest is looking user profile ,
		// so we will show them default template
		$visitingUser	= JRequest::getInt('userid',$loggedInUser->id);
	
		//$visitingUser = 0 means loggen-in-user is looking their own profile
		//so we set $visitingUser as logged-in-user
		//if in case user is already logged in
		//then honour his template else return and honour global settings
		if($visitingUser <= 0 )
			return true;
				
		//$visitingUser > 0 means a valid-user to visit profile
		//so we will show them profile in user template
		//so update the template in configuration	
		$template = XiptLibProfiletypes::getUserData($visitingUser,'TEMPLATE');

		//now update template @template
		if($template)
				$instance->set('template',$template);
			
		return true;
	}
	
	function updateCommunityCustomField($userId, $value, $what='')
	{
	    //ensure we are calling it for correct field
	    XiptError::assert($what == PROFILETYPE_CUSTOM_FIELD_CODE || $what == TEMPLATE_CUSTOM_FIELD_CODE
			, XiptText::_("CUSTOM_FIELD_DOES_NOT_EXIST"), XiptError::ERROR);

	    // find the profiletype or template field
	    // dont patch up the database.
	    $res	= XiptHelperJomsocial::getFieldId($what);
		
		// skip these calls from backend
		XiptError::assert($res);
		
		$field_id = $res;
		
		//if row does not exist
		$query   = new XiptQuery();
		$res	 = $query->select('*')
						->from('#__community_fields_values')
						->where(" user_id = $userId ", 'AND')
						->where(" field_id = $field_id ")
						->dbLoadQuery()
						->loadObject();
						
		$db = JFactory::getDBO();
		//record does not exist, insert it
		if(!$res)
		{
			$res			= new stdClass();
			$res->user_id 	= $userId;
			$res->field_id 	= $field_id;
			$res->value 	= $value;
			$db->insertObject('#__community_fields_values',$res,'id');
			
			if($db->getErrorNum()){
					XiptError::raiseError( __CLASS__.'.'.__LINE__, $db->stderr());
			}
			
			return true;
		}
		
		// change the type
		$res->user_id 	= $userId;
		$res->field_id 	= $field_id;
		$res->value 	= $value;
		$db->updateObject( '#__community_fields_values', $res, 'id');
		
		if($db->getErrorNum()){
				XiptError::raiseError(__CLASS__.'.'.__LINE__, $db->stderr());
		}
		
		return true;
	}
	
	/**
	 * This function add's given watermark to avatars
	 * 	IMP : If avatar, is default (JomSocil default or profiletypes default)
	 * 	then no action is taken
	 * 
	 * @param $userid
	 * @param $watermarkInfo
	 * @return unknown_type
	 */
	//Before take any action for watre-mark, avatar must be manipulat.
	function updateCommunityUserWatermark($userid,$watermark='')
	{
		// Get water is enable or disable
		$isWaterMarkEnable = XiptLibProfiletypes::getParams(XiptLibProfiletypes::getUserData($userid),'watermarkparams')->get('enableWaterMark',0);
		
		//update watermark on user's avatar
		$pTypeAvatar  	   = XiptLibJomsocial::getUserDataFromCommunity($userid, 'avatar');
		$pTypeThumbAvatar  = XiptLibJomsocial::getUserDataFromCommunity($userid, 'thumb');
			
		// no watermark on default avatars
		if(XiptLibProfiletypes::isDefaultAvatarOfProfileType($pTypeAvatar,true))
			return false;

		//no watermark then resotre backup avatar and return
		//if water-mark disable then restore avatar(hit both by resete & by update any user profile ) 
		if(false == $isWaterMarkEnable || $watermark == '')	
		{
			self::restoreBackUpAvatar($pTypeAvatar);
			self::restoreBackUpAvatar($pTypeThumbAvatar);
			return true;
		}
		
		//add watermark on user avatar image
		if($pTypeAvatar)
			XiptHelperImage::addWatermarkOnAvatar($userid,$pTypeAvatar,$watermark,'avatar');

		//add watermark on thumb image
		if($pTypeThumbAvatar)
			XiptHelperImage::addWatermarkOnAvatar($userid,$pTypeThumbAvatar,$watermark,'thumb');

		return true;
	}
	
	function restoreBackUpAvatar($currImagePath)
	{
		$currImagePath	= XiptHelperUtils::getRealPath($currImagePath);
		$avatarFileName = JFile::getName($currImagePath);
		if(JFile::exists(USER_AVATAR_BACKUP.DS.$avatarFileName) && JFile::copy(USER_AVATAR_BACKUP.DS.$avatarFileName,JPATH_ROOT.DS.$currImagePath))
			return true;

		if(JFactory::getConfig()->getValue('debug'))
			XiptError::raiseWarning("XIPT-SYSTEM-WARNING","User avatar {".USER_AVATAR_BACKUP.DS.$avatarFileName."} in backup folder does not exist.");
		return false;
	}
	/**
	 * It updates user's oldAvtar to newAvatars
	 * @param $userid
	 * @param $newAvatar
	 * @return unknown_type
	 */
	function updateCommunityUserDefaultAvatar($userid, $newAvatar)
	{
		/*
		 * IMP : Implemented in setup 
		 * we migrate profiletype avatars to profiletype-1, 2 etc.
		 * So that we do not need to tense about old avatar of profiletype
		 * */
		
		//reload : so that we do not override previous information if any updated in database.
		//self::reloadCUser($userid);
		$user    	= CFactory::getUser($userid);
		$userAvatar = $user->_avatar;
		
		//Before save, avatar path Change in URL formate
		$newAvatar	= XiptHelperUtils::getUrlpathFromFilePath($newAvatar);
		
		//We must enforce this as we never want to overwrite a custom avatar
		$isDefault	 = XiptLibProfiletypes::isDefaultAvatarOfProfileType($userAvatar,true);
//		$changeAvatarOnSyncUp = self::_changeAvatarOnSyncUp($userAvatar); 
		
		if($isDefault == false )
			return false;

		// we can safely update avatar so perform the operation		
//		$user->set('_avatar',$newAvatar);
//		$user->set('_thumb', XiptHelperImage::getThumbAvatarFromFull($newAvatar));
//		
//		if(!$user->save())
//		    return false;
//	

		$query = new XiptQuery();
		if(! $query->update('#__community_users')
			  	   ->set(" avatar = '$newAvatar' ")
			  	   ->set(" thumb = '".XiptHelperImage::getThumbAvatarFromFull($newAvatar)."' ")
			       ->where(" userid = $userid ")
			       ->dbLoadQuery()
			       ->query())
			       return false;
			  
		//enforce JomSocial to clean cached user
        self::reloadCUser($userid);
		return true;
	}

	
	/*This function set privacy for user as per his profiletype*/
	function updateCommunityUserPrivacy($userid,$myprivacy)
	{	
		// get params
		//self::reloadCUser($userid);
		$cuser    = CFactory::getUser($userid);
		$myparams = $cuser->getParams();
		// if privacy handle by end user then dont update privacy.
		if(false == XiptLibPluginhandler::isPrivacyAllow())
			return true;

		if(isset($myprivacy['jsPrivacyController']))
			unset($myprivacy['jsPrivacyController']);
			
		foreach( $myprivacy as $key => $val ){
			$myparams->set( $key , $val );
		}
		
		$params = $myparams->toString();
		$query = new XiptQuery();
		if(! $query->update('#__community_users')
			  	   ->set(" params = '$params' ")			  	   
			       ->where(" userid = $userid ")
			       ->dbLoadQuery()
			       ->query())
			       return false;
		/**
		 *  Update user Photo Privacy Setting in user Album Tables
		 */
		$params = $myparams->toArray();
		$query = new XiptQuery();
		if(! $query->update('#__community_photos_albums')
			  	   ->set(" permissions = '".$params['privacyPhotoView']."' ")			  	   
			       ->where(" creator = $userid ")
			       ->dbLoadQuery()
			       ->query())
			       return false;
		/**
		 *  Update user Vedio Privacy Setting in user Album Tables
		 */
		//$params = $myparams->toArray();
		$query = new XiptQuery();
		if(! $query->update('#__community_videos')
			  	   ->set(" permissions = '".$params['privacyVideoView']."'" )			  	   
			       ->where(" creator = $userid ")
			       ->dbLoadQuery()
			       ->query())
			       return false;
		//default Privacy of every custom field is "public"
		$query = new XiptQuery();
		if(! $query->update('#__community_fields_values')
			  	   ->set(" access = 0")   // XITODO : use constance intead of 0			  	   
			       ->where(" user_id = $userid ")
			       ->dbLoadQuery()
			       ->query())
			       return false;
	
//		if(!$cuser->save( 'params' ))
//			return false ;

		 //enforce JomSocial to clean cached user
   		self::reloadCUser($userid);	
		return true;
		
	}
	
	function updateCommunityUserGroup($userId,$oldGroup, $newGroup)
	{
		// remove from oldGroup
		if($oldGroup && self::_isMemberOfGroup($userId,$oldGroup))
			XiptLibJomsocial::_removeUserFromGroup($userId,$oldGroup);
		
		// add to newGroup
        if($newGroup && self::_isMemberOfGroup($userId,$newGroup)==false)
        	XiptLibJomsocial::_addUserToGroup($userId,$newGroup);
       
		// add to new group
		return true;
	}
	
	function _isMemberOfGroup($userid, $groupid)
	{
		$query  = new XiptQuery();
		$result = $query->select('memberid')
						->from('#__community_groups_members')
						->where(" memberid = $userid ", 'AND')
						->where(" groupid IN ( $groupid ) ")
						->limit(1)
						->dbLoadQuery()
						->loadResult();				
  		
  		return $result ? true : false ;
	}
	
	function _addUserToGroup( $userId , $groupIds)
	{
		if(empty($groupIds))
			return false;
			
		$groupIds	= explode(',',$groupIds);
		$groupModel	= CFactory::getModel( 'groups' );
	
        //if user has selected "none" just exit
		if( is_array($groupIds) == false || in_array(NONE, $groupIds) )
			return false;
	
	    foreach($groupIds as $k => $gid)
	    {
			if( $groupModel->isMember( $userId , $gid ) )
				continue;
			
			$group		= JTable::getInstance( 'Group' , 'CTable' );
			$member		= JTable::getInstance( 'GroupMembers' , 'CTable' );
			$group->load( $gid );
			
			// Set the properties for the members table
			$member->groupid	= $group->id;
			$member->memberid	= $userId;
	
			// @rule: If approvals is required, set the approved status accordingly.
			$member->approved	= 1; // SHOULD BE always 1  by SHYAM//( $group->approvals ) ? '0' : 1;
	
			//@todo: need to set the privileges
			$member->permissions	= '0';
			$store	= $member->store();
	
			// Assert if storing fails
			XiptError::assert( $store, XiptText::_("DATA_IS_NOT_STORED"), XiptError::ERROR);
	
			if($member->approved)
				self::addWallCount($gid);
			   
	        }
			return true;
	}
	    
	function _removeUserFromGroup($userId , $groupIds)
	{
		if(empty($groupIds))
			return false;
		
		$groupIds	= explode(',',$groupIds);
		$model		= CFactory::getModel( 'groups' );
		$group		= JTable::getInstance( 'Group' , 'CTable' );
		
		if( (is_array($groupIds)) == false )
			return false;

	    foreach($groupIds as $k => $gid)
	    {
			$group->load( $gid );
		
			// do not remove owner
			if($group->ownerid == $userId)
				continue;
			
			// if user is not member of group
			if(!$model->isMember($userId , $gid))
				continue;
			
			//load group member table
			$groupMember = JTable::getInstance( 'GroupMembers' , 'CTable' );
			$groupMember->load( $userId , $gid );
	
			$data			= new stdClass();
			$data->groupid	= $gid;
			$data->memberid	= $userId;
	
			//remove member
			$model->removeMember($data);
			self::addWallCount( $gid );
	    }
		return true;
	}
	
	function reloadCUser($userid)
	{
		if(!$userid)
			return false;
			
		$cuser = CFactory::getUser($userid, '');
		return CFactory::getUser($userid);		
	}
	
	function cleanStaticCache($set = null)
	{
		static $reset = false;
		
		if($set !== null)
			$reset = $set;
			
		return $reset;
	}
	// to save value of JS multiprofiletype in  _config table
	function saveValueinJSConfig($setValue=0)
	{  
		CFactory::load('helpers', 'string');
		$config	= JTable::getInstance( 'configuration' , 'CommunityTable' );
		$config->load( 'config' );
		$params	= new XiptParameter( $config->params );
		$params->set('profile_multiprofile',$setValue);
		$config->params	= $params->toString();
		if(!$config->store())
			return false;

		return true;
	}
	
	//this funtion is not supported by JS2.4++
	function addWallCount( $groupId )
	{
		$group	= JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $groupId );
		$group->store();
	}
}
