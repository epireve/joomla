<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// include joomla plugin framework
jimport( 'joomla.plugin.plugin' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

if(!JFolder::exists(JPATH_ROOT.DS.'components'.DS.'com_xipt'))
	return false;

if(!JFile::exists(JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php'))
 	return false;
 			
$includeXipt=require_once (JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php');	
 		
if($includeXipt === false)
	return false;
	
class plgCommunityxipt_community extends CApplications
{
	private $_pluginHandler;
	var $_name = 'xipt_community';
	
	function plgCommunityxipt_community( $subject, $params )
	{
		parent::__construct( $subject, $params );
		$this->_pluginHandler = XiptFactory::getPluginHandler();
	}
	
	/**
	 * This function will store user's registration information
	 * in the tables, when Community User object is created
	 * @param $cuser
	 * @return true
	 */
	function onProfileCreate($cuser)
	{
		return true;	
	}
	
	/*
	 * This function require to protect deletion of
	 * default avatar of profiletype because JS
	 * delete non-default avatar when user change his avatar
	 * so in that case if that user has any ptype default avatar
	 * then our ptype avatar will be deleted.
	 * 
	 * This function also ensures that when remove pciture is used by admin
	 * on custom avatar, then we need to add default avatar of profiletype to user
	 * not the jomsocial default avatar
	 */
	function onProfileAvatarUpdate($userid, $old_avatar_path, $new_avatar_path)
	{	    
	    // When admin is removing a user's avatar
		// we need to apply default avatar of profiletype
		$isAdmin = XiptHelperUtils::isAdmin(JFactory::getUser()->id);
		$view    = JRequest::getVar('view','','GET');
		$task    = JRequest::getVar('task','','GET');

		$new_avatar_path = XiptHelperUtils::getRealPath($new_avatar_path);

		if($isAdmin && $view == 'profile' && $task == 'removepicture')
		{
			//setup $new_avatar
			$ptype  = XiptLibProfiletypes::getUserData($userid, 'PROFILETYPE');
			$avatar = XiptLibProfiletypes::getProfiletypeData($ptype, 'avatar');
			//if users avatar is custom avatar then thumb is stored as thumb_XXXX.png
			//else if it is a default avatar(JomSocial OR Profiletype) then stored as XXX_thumb.png
			//HERE the new_avatar will be default jomsocial avatar so search _thumb 
			if(JString::stristr($new_avatar_path,'thumb'))
				$new_avatar_path = XiptHelperImage::getThumbAvatarFromFull($avatar);
			else
				$new_avatar_path = $avatar;
		}
		
		//check if avatar is ptype default avatar
		if(XiptLibProfiletypes::isDefaultAvatarOfProfileType($old_avatar_path,false)){
			//HERE we should search for _thumb, not for thumb_
		/**XITODO:: Properly test following::
		* In JS2.2 :: When our default avatar is user.png then JS delete this avatar from(Community/assets/user.png)
		* becoz at delete time its only consider default.jpg (components/com_community/assets/default.jpg)as default value and 
		* if avatar is user.png (Community/assets/user.png) then this path is not set into database
		* but it saved by XiPT 3.1.
		* JS2.2 does not delete default.jpg(Community/assets/default.jpg) So we changed path.
		* (Not understanding:: Call 2 manish) :)
		*/			
			if (JString::stristr($old_avatar_path,'thumb')){
				//$old_avatar_path = DEFAULT_AVATAR_THUMB;
				$old_avatar_path = 'components/com_community/assets/default_thumb.jpg' ;
			}
			else{
				//$old_avatar_path = DEFAULT_AVATAR;
				$old_avatar_path = 'components/com_community/assets/default.jpg';
			}
		}		
		
		//Now apply watermark to images
		//	for that we don't require to add watermark
		//	XITODO : format it in proper way
		if(!XiptLibProfiletypes::getParams(XiptLibProfiletypes::getUserData($userid),'watermarkparams')->get('enableWaterMark',0))
			return true;
					
		//check if uploadable avatar is not default ptype avatar
		/**XITODO:: Properly testing following
		 * In JS 2.2:: user.png consider as a default avatar for every user and dont save this avatar path in community user table
		 * So XiPT 3.1 also consider user.png as default avatar
		 * But may be Xipt installed on existing data then at reset all time, may b apply watr-mrk on
		 * community/assets/defauult.jpg  (not usr.png).
		 * Need properly testing and if get above thing then restict. :)   
		 */
		if(XiptLibProfiletypes::isDefaultAvatarOfProfileType($new_avatar_path,true)){
			return true;
		}
		
		//check what is new image , if thumb or original
		$what = JString::stristr($new_avatar_path,'thumb')? 'thumb' : 'avatar';
		
		$watermarkInfo = XiptHelperImage::getWatermark($userid);
		if(false == $watermarkInfo)
			return true;
			
		XiptHelperImage::addWatermarkOnAvatar($userid,$new_avatar_path,$watermarkInfo,$what);
		return true;
	}

	function onAjaxCall($func, $args , $response)
	{
		return $this->_pluginHandler->onAjaxCall($func, $args, $response);
	}

	// update the configuration
	function onAfterConfigCreate($config)
	{
    	return 	 XiptLibJomsocial::updateCommunityConfig($config);
	}

	/**
	 * This function removes not allowed community apps form dispatcher
	 * as per user's profiletype
	 * @return true
	 */
	function onAfterAppsLoad()
	{
		// skip these calls from backend
		if(JFactory::getApplication()->isAdmin())
			return true;

		$dispatcher = JDispatcher::getInstance();
		
		// get userids of both users profile owner and profile visitor
		$selfUserid    = JFactory::getUser()->id;
		$othersUserid  = JRequest::getVar('userid',$selfUserid);
		
		//when user is not logged in and he is not visiting any profile, return true
		if($selfUserid == 0 && $othersUserid == 0)
			return true;

		// apply guest profile type for guest user
		$selfProfiletype    = XiptLibProfiletypes::getUserData($selfUserid, 'PROFILETYPE');
		$othersProfiletype 	= XiptLibProfiletypes::getUserData($othersUserid, 'PROFILETYPE');
		
		$blockDisplayApp    = XiptFactory::getSettings('jspt_block_dis_app', 0);
		
		/**
		 *  #1: block the display application of logged in user if the above param is set to yes
		 * #2: otherwise block display application of user whose profile is being visited
		 * #3: block the functional application of logged in user
		*/ 		
		$apps = $dispatcher->get('_observers');
		
		if($blockDisplayApp == BLOCK_DISPLAY_APP_OF_OWNER || $blockDisplayApp == BLOCK_DISPLAY_APP_OF_BOTH)
			XiptLibApps::filterCommunityApps($apps, $othersProfiletype, true);
			
		if($blockDisplayApp == BLOCK_DISPLAY_APP_OF_VISITOR || $blockDisplayApp == BLOCK_DISPLAY_APP_OF_BOTH)
			XiptLibApps::filterCommunityApps($apps, $selfProfiletype, true);

		XiptLibApps::filterCommunityApps($apps, $selfProfiletype,	  false);
		$dispatcher->set('_observers',$apps);
	    return true;
	}

	/**
	 * This function will ensure that who is not allowed to change template
	 * or profiletype the data should not be saved.
	 *
	 * @param $userId
	 * @param $fieldValueCodes
	 * @return true
	 */
	function onBeforeProfileUpdate($userid, $fieldValueCodes)
	{
		// We NEVER send false from here. If profiletype should not be changed then 
		// we simply store previous values. so correct values are always there during the 
		// after event
		
		// TODO : array_key_exists Check for both fields exist in array or not
		$profileTypeValue =& $fieldValueCodes[PROFILETYPE_CUSTOM_FIELD_CODE];
		$templateValue    =& $fieldValueCodes[TEMPLATE_CUSTOM_FIELD_CODE];
		
		// skip these calls from backend
		if(JFactory::getApplication()->isAdmin())
			return true;
			
		// the use is admin, might be editing from frontend return true
		if(XiptHelperUtils::isAdmin($userid))
			return true;

		// user is allowed or not.
        $allowToChangePType    = XiptFactory::getSettings('allow_user_to_change_ptype_after_reg',0);
        $oldPtype 			   = XiptLibProfiletypes::getUserData($userid, 'PROFILETYPE');
        $allowToChangeTemplate = XiptHelperProfiletypes::getProfileTypeData($oldPtype,'allowt');

        // not changing anything get data from table and set it
		if(!$allowToChangeTemplate || empty($templateValue)){
			//reset to old users value
			$templateValue = XiptLibProfiletypes::getUserData($userid,'TEMPLATE');			
			//if user is changing profiletype then we should pick the template as per profiletype			
			if($allowToChangePType && $oldPtype != $profileTypeValue)
				$templateValue = XiptLibProfiletypes::getProfiletypeData($profileTypeValue, 'template');
		}

		// not allowed to change profiletype, get data from table and set it
		if(!$allowToChangePType || !$profileTypeValue){
			$profileTypeValue = XiptLibProfiletypes::getUserData($userid,'PROFILETYPE');
		}

		return true;
	}
	
	/**
	 * The user data have been saved.
	 * We will save user's data (profiletype and template) into Xipt tables
	 * @param $userId
	 * @param $saveSuccess
	 * @return unknown_type
	 */
	function onAfterProfileUpdate($userId, $saveSuccess)
	{
		 // data was not saved, do nothing
	    if(false == $saveSuccess)
	        return true;

	    // the JomSocial already store values in field tables
	    // now we need to apply that information to our tables
//	    $cuser        = CFactory::getUser($userId);
//	    $profiletype  = $cuser->getInfo(PROFILETYPE_CUSTOM_FIELD_CODE);
//	    $template     = $cuser->getInfo(TEMPLATE_CUSTOM_FIELD_CODE);

		$profiletype  = XiptHelperUtils::getInfo($userId, PROFILETYPE_CUSTOM_FIELD_CODE);
	    $template     = XiptHelperUtils::getInfo($userId,TEMPLATE_CUSTOM_FIELD_CODE);
 		
	    // Changing Profile From Front End If The Template allow is None then Set Profiletype template.
	    // If Template is Allowed on Profile Type Then Take User Define Template From Front End. 
	    $oldPtype 			   = XiptLibProfiletypes::getUserData($userId, 'PROFILETYPE');
        $OldallowToChangeTemplate = XiptHelperProfiletypes::getProfileTypeData($oldPtype,'allowt');
        $NewallowToChangeTemplate = XiptHelperProfiletypes::getProfileTypeData($profiletype,'allowt');
	    
        //update profiletype only
	    XiptLibProfiletypes::updateUserProfiletypeData($userId,$profiletype,$template,'ALL');
	    
	    //update template seperately
	    $filter[] 				= 'template';
		$allowToChangeTemplate    = XiptFactory::getSettings('allow_templatechange',0);
	    if ( ($NewallowToChangeTemplate == 0 || $OldallowToChangeTemplate == 0) && $allowToChangeTemplate==0)
        	$newData['template']	= XiptLibProfiletypes::getProfiletypeData($profiletype, 'template');      
	    else
	    	$newData['template'] = $template;
	    
	    XiptLibProfiletypes::updateUserProfiletypeFilteredData($userId,$filter,null,$newData);
	    
	    $this->showActivity($userId, $profiletype, $oldPtype);
	    return true;
	}
	
    function onFormDisplay( $fieldName )
	{
		if($this->_pluginHandler->isPrivacyAllow()){
			$this->_pluginHandler->hidePrivacyElements();
		}

	}
	
	//to show change of profiletype as activity
	function showActivity($userid, $newPtype, $oldPtype)
	{
		if($newPtype === $oldPtype)
			return;
			
		$ptName = XiptHelperProfiletypes::getProfileTypeData($newPtype, 'name');
		$act = new stdClass();
		$act->cmd     = 'wall.write';
		$act->actor   = $userid;
		$act->target  = 0; // no target
		$changePt=XiptText::_('CHANGED_PROFILETYPE_TO');
		$act->title   = JText::_('{actor}'. $changePt.$ptName);
		$act->content = '';
		$act->app     = 'wall';
		$act->cid     = 0;
		  
		CFactory::load('libraries', 'activities');
		$act->comment_type  = 'xipt_community.myaction';
		$act->comment_id    = CActivities::COMMENT_SELF;
		
		$act->like_type     = 'xipt_community.myaction';
		$act->like_id     	= CActivities::LIKE_SELF;
		
		CActivityStream::add($act);
		return true;
	}
//	 function onFormSave($fieldName )
//	 {
//	 	//JFactory::getApplication()->enqueueMessage("Not chanage your Privacy");
//	 }
}
