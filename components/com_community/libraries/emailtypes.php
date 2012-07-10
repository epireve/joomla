<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	user 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

class CEmailTypes {
	var $_emailtypes		= array();	
	var $_adminonlygroups = array();
	
	public function CEmailTypes()
	{	
		$this->_emailtypes	= array();
		//load default email types
		$this->loadDefault();
	}
	
	public function isAdminOnlyGroup($group){
		return (isset($this->_adminonlygroups[$group])) ? TRUE : FALSE;
	}
	
	public function getEmailTypes(){
		return $this->_emailtypes;
	}
	
	public function loadDefault(){
	
		$config = CConfig::getInstance();
		
		//add default group
		$groups = array();
		$groups['ADMIN'] 	= 	array('COM_COMMUNITY_EMAILGROUP_ADMIN', TRUE);
		$groups['PROFILE'] 	= 	array('COM_COMMUNITY_EMAILGROUP_PROFILE', FALSE);
		if($config->get('enablegroups')){
			$groups['GROUPS'] 	= 	array('COM_COMMUNITY_EMAILGROUP_GROUPS', FALSE);
		}
		if($config->get('enableevents')){
			$groups['EVENTS'] 	= 	array('COM_COMMUNITY_EMAILGROUP_EVENTS', FALSE);
		}
		if($config->get('enablevideos')){
			$groups['VIDEOS'] 	= 	array('COM_COMMUNITY_EMAILGROUP_VIDEOS', FALSE);
		}
		if($config->get('enablephotos')){
			$groups['PHOTOS'] 	= 	array('COM_COMMUNITY_EMAILGROUP_PHOTOS', FALSE);
		}
		
		$groups['OTHERS'] 	= 	array('COM_COMMUNITY_EMAILGROUP_OTHERS', FALSE);
					
					
					
		foreach ($groups as $key => $desc){
			$this->addGroup($key,$desc[0], $desc[1]);
		}
		$types = array();
		
		//Admin
		$types[]	= array('ADMIN','etype_groups_notify_admin','COM_COMMUNITY_EMAILTYPE_GROUPS_CREATION_MODERATION_REQUIRED','COM_COMMUNITY_EMAILTYPE_GROUPS_CREATION_MODERATION_REQUIRED_TIPS', TRUE);
		$types[]	= array('ADMIN','etype_user_profile_delete','COM_COMMUNITY_EMAILTYPE_PROFILE_DELETE','COM_COMMUNITY_EMAILTYPE_PROFILE_DELETE_TIPS', TRUE);
		$types[]	= array('ADMIN','etype_system_reports_threshold','COM_COMMUNITY_EMAILTYPE_REPORTS_THRESHOLD','COM_COMMUNITY_EMAILTYPE_REPORTS_THRESHOLD_TIPS', TRUE);
		
		//Profile 
		//$types[]	= array('PROFILE','etype_profile_submit_wall_comment','COM_COMMUNITY_EMAILTYPE_PROFILE_WALLCOMMENT','COM_COMMUNITY_EMAILTYPE_PROFILE_WALLCOMMENT_TIPS');
		$types[]	= array('PROFILE','etype_profile_status_update','COM_COMMUNITY_EMAILTYPE_PROFILE_STATUSUPDATE','COM_COMMUNITY_EMAILTYPE_PROFILE_STATUSUPDATE_TIPS');
		//Friends
		$types[]	= array('PROFILE','etype_friends_request_connection','COM_COMMUNITY_EMAILTYPE_FRIENDS_INVITE','COM_COMMUNITY_EMAILTYPE_FRIENDS_INVITE_TIPS');
		$types[]	= array('PROFILE','etype_friends_create_connection','COM_COMMUNITY_EMAILTYPE_FRIENDS_CONNECTION','COM_COMMUNITY_EMAILTYPE_FRIENDS_CONNECTION_TIPS');
		$types[]	= array('PROFILE','etype_inbox_create_message','COM_COMMUNITY_EMAILTYPE_OTHERS_INBOXMSG','COM_COMMUNITY_EMAILTYPE_OTHERS_INBOXMSG_TIPS');
		
		
		if($config->get('enablegroups')){
			//Groups 
	//		$types[]	= array('GROUPS','etype_groups_submit_wall_comment','COM_COMMUNITY_EMAILTYPE_GROUPS_WALLCOMMENT','COM_COMMUNITY_EMAILTYPE_GROUPS_WALLCOMMENT_TIPS');
			$types[]	= array('GROUPS','etype_groups_invite','COM_COMMUNITY_EMAILTYPE_GROUPS_INVITE','COM_COMMUNITY_EMAILTYPE_GROUPS_INVITE_TIPS');
			$types[]	= array('GROUPS','etype_groups_discussion_reply','COM_COMMUNITY_EMAILTYPE_GROUPS_DISCUSSIONREPLY','COM_COMMUNITY_EMAILTYPE_GROUPS_DISCUSSIONREPLY_TIPS');
			$types[]	= array('GROUPS','etype_groups_wall_create','COM_COMMUNITY_EMAILTYPE_GROUPS_WALLUPDATE','COM_COMMUNITY_EMAILTYPE_GROUPS_WALLUPDATE_TIPS');
			$types[]	= array('GROUPS','etype_groups_create_discussion','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWDISCUSSION','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWDISCUSSION_TIPS');
			$types[]	= array('GROUPS','etype_groups_create_news','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWBULLETIN','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWBULLETIN_TIPS');
			$types[]	= array('GROUPS','etype_groups_create_album','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWALBUM','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWALBUM_TIPS');
			$types[]	= array('GROUPS','etype_groups_create_video','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWVIDEO','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWVIDEO_TIPS');
			$types[]	= array('GROUPS','etype_groups_create_event','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWEVENT','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWEVENT_TIPS');
			$types[]	= array('GROUPS','etype_groups_sendmail','COM_COMMUNITY_EMAILTYPE_GROUPS_MASSEMAIL','COM_COMMUNITY_EMAILTYPE_GROUPS_MASSEMAIL_TIPS');
			$types[]	= array('GROUPS','etype_groups_member_approved','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWMEMBER','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWMEMBER_TIPS');
			$types[]	= array('GROUPS','etype_groups_member_join','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWMEMBER_REQUEST','COM_COMMUNITY_EMAILTYPE_GROUPS_NEWMEMBER_REQUEST_TIPS');
			$types[]	= array('GROUPS','etype_groups_notify_creator','COM_COMMUNITY_EMAILTYPE_GROUPS_CREATION_APPROVED','COM_COMMUNITY_EMAILTYPE_GROUPS_CREATION_APPROVED_TIPS');
		}
		
		if($config->get('enableevents')){
			//Events 
	//		$types[]	= array('EVENTS','etype_events_submit_wall_comment','COM_COMMUNITY_EMAILTYPE_EVENTS_WALLCOMMENT','COM_COMMUNITY_EMAILTYPE_EVENTS_WALLCOMMENT_TIPS');
			$types[]	= array('EVENTS','etype_events_invite','COM_COMMUNITY_EMAILTYPE_EVENTS_INVITATION','COM_COMMUNITY_EMAILTYPE_EVENTS_INVITATION_TIPS');
			$types[]	= array('EVENTS','etype_events_invitation_approved','COM_COMMUNITY_EMAILTYPE_EVENTS_INVITATION_APPROVED','COM_COMMUNITY_EMAILTYPE_EVENTS_INVITATION_APPROVED_TIPS');
			$types[]	= array('EVENTS','etype_events_sendmail','COM_COMMUNITY_EMAILTYPE_EVENTS_MASSEMAIL','COM_COMMUNITY_EMAILTYPE_EVENTS_MASSEMAIL_TIPS');
			$types[]	= array('EVENTS','etype_events_notify_admin','COM_COMMUNITY_EMAILTYPE_EVENTS_CREATION_MODERATION_REQUIRED','COM_COMMUNITY_EMAILTYPE_EVENTS_CREATION_MODERATION_REQUIRED_TIPS');
		}
		if($config->get('enablevideos')){
			//Videos 
			$types[]	= array('VIDEOS','etype_videos_submit_wall','COM_COMMUNITY_EMAILTYPE_VIDEOS_WALLCOMMENT','COM_COMMUNITY_EMAILTYPE_VIDEOS_WALLCOMMENT_TIPS');
		}
		if($config->get('enablephotos')){
			//Photos
			$types[]	= array('PHOTOS','etype_photos_submit_wall','COM_COMMUNITY_EMAILTYPE_PHOTOS_WALLCOMMENT','COM_COMMUNITY_EMAILTYPE_PHOTOS_WALLCOMMENT_TIPS');
			$types[]	= array('PHOTOS','etype_photos_tagging','COM_COMMUNITY_EMAILTYPE_PHOTOS_TAG','COM_COMMUNITY_EMAILTYPE_PHOTOS_TAG_TIPS');
		}
		//Others
		$types[]	= array('OTHERS','etype_system_bookmarks_email','COM_COMMUNITY_EMAILTYPE_OTHERS_BOOKMARKS','COM_COMMUNITY_EMAILTYPE_OTHERS_BOOKMARKS_TIPS');
		$types[]	= array('OTHERS','etype_system_messaging','COM_COMMUNITY_EMAILTYPE_OTHERS_SYSTEMMSG','COM_COMMUNITY_EMAILTYPE_OTHERS_SYSTEMMSG_TIPS');
		foreach ($types as $type){
			
			$adminOnly=(isset($type[4])) ? $type[4] : FALSE;
			$this->addEmailType($type[0],$type[1],$type[2],$config->get($type[1],0),$type[3], $adminOnly);
		}
	}
	/**
	 * Function to add new email type.
	 * param - groupKey : string - the key of the group
	 *       - EmailTypeID : string - the unique key of the email type
	 *       - description : string - the label of the email type
	 *       - value	: int - the configured value (enable/disable) of the email type
	 *       - tips	: string - the tips of the email type
	 */
	public function addEmailType($groupKey, $EmailTypeID, $description='', $value=0, $tips='', $adminOnly=FALSE){
		if(array_key_exists($groupKey, $this->_emailtypes))
		{
			$tbGroup	=& $this->_emailtypes[strtoupper($groupKey)];
								
			$child	= new stdClass();
			$child->description		= $description;
			$child->value			= $value;
			$child->tips			= $tips;
			$child->adminOnly		= $adminOnly;
			
			$tbGroup->child[$EmailTypeID]	= $child;
		}	
	}
	/**
	 * Function to add new email group.
	 * param - key : string - the key of the group
	 *       - description : string - the label of the group name
	 */
	public function addGroup($key, $description='', $adminOnly=FALSE)
	{
		if(! array_key_exists($key, $this->_emailtypes))
		{
	    	$newGroup				= new stdClass();
			$newGroup->description	= $description;
			$newGroup->child		= array();
			if($adminOnly) {
				$this->_adminonlygroups[$description] = $description;
			}
		
			$this->_emailtypes[strtoupper($key)]	= $newGroup;
		}
	}
	
	/**
	 * Function used to remove email group and its associated email types.
	 * param - key : string - the key of the group
	 */
	public function removeGroup($key)
	{
		if(array_key_exists($key, $this->_emailtypes))
		{		
			unset($this->_emailtypes[strtoupper($key)]);
		}
	}		
	
	/**
	 * Function used to remove an email type
	 * param - groupKey : string - the key of the group
	 *       - EmailTypeID : string - the unique key of the email type
	 */
	public function removeEmailType($groupKey, $EmailTypeID)
	{
		if(array_key_exists($groupKey, $this->_emailtypes))
		{
			$tbGroup	=& $this->_emailtypes[strtoupper($groupKey)];
			$childItem	=& $tbGroup->child;				
			if (is_array($EmailTypeID)){
				if(array_key_exists($EmailTypeID, $childItem))
				{
					unset($childItem[$EmailTypeID]);
				}
			}
		}
	}
	
	/**
	 * Function used to convert email types to params string
	 */	
	public function convertToParamsString(){
		$ret = "";
		foreach($this->_emailtypes as $group){
			foreach($group->child as $id => $type){
				$ret .= $id . "=" . $type->value . "\n";
			}
		}
		return $ret;
	}
}