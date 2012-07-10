<?php
/**
 * @category	User
 * @package		JomSocial
 * @copyright (C) 2008 - 2010 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

include_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

class plgUserJomSocialUser extends JPlugin
{
	function plgUserJomSocialUser(& $subject, $config)
	{
		
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'featured.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'videos.php');
		require_once(JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'events'.DS.'router.php');
		jimport('joomla.filesystem.folder');
		parent::__construct($subject, $config);
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @access	public
	 * @param 	array 	holds the user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function onLoginUser($user, $options)
	{
		CFactory::load( 'helpers' , 'user' );
		
		$id		= cGetUserId( $user['username'] );

		CFactory::setActiveProfile( $id );

		return true;
	}
	
	/**
	 * This method should handle any login logic and report back to the subject
	 * For Joomla 1.6, onLoginUser is now onUserLogin
	 *
	 * @access	public
	 * @param 	array 	holds the user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	function onUserLogin($user, $options)
	{		
		
		return $this->onLoginUser($user, $options);
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @access public
	 * @param array holds the user data
	 * @return boolean True on success
	 * @since 1.5
	 */
	function onLogoutUser($user)
	{
		CFactory::unsetActiveProfile();
		
		return true;
	}
	
	/**
	 * This method should handle any logout logic and report back to the subject
	 * For Joomla 1.6, onLogoutUser is now onUserLogout
	 *
	 * @access	public
	 * @param 	array 	holds the user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	function onUserLogout($user)
	{		
		return $this->onLogoutUser($user);
	}
	/* Delete front page member cache.
	 *
	 */	 
	function onAfterStoreUser()
	{
		CCache::remove(array(COMMUNITY_CACHE_TAG_MEMBERS));
	}
	
	/**
	 * Delete front page member cache.
	 * For Joomla 1.6, onAfterStoreUser is now onUserAfterSave
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	function onUserAfterSave()
	{		
		$this->onAfterStoreUser();
	}
	/* Delete front page member cache.
	 *
	 */	
	function onAfterDeleteUser()
	{
		CCache::remove(array(COMMUNITY_CACHE_TAG_MEMBERS));
	}

	/**
	 * Delete front page member cache.
	 * For Joomla 1.6, onAfterDeleteUser is now onUserAfterDelete
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	function onUserAfterDelete()
	{		
		$this->onAfterDeleteUser();
	}
	
	function onBeforeDeleteUser($user)
	{
		$mainframe =& JFactory::getApplication();
		$this->deleteFromCommunityEvents( $user );
		$this->deleteFromCommunityUser($user);
		$this->deleteFromCommunityWall($user);
		$this->deleteFromCommunityGroup($user);
		$this->deleteFromCommunityDiscussion($user, $groups);
		$this->deleteFromCommunityPhoto($user);
		$this->deleteFromCommunityMsg($user);
		$this->deleteFromCommunityProfile($user);
		$this->deleteFromCommunityConnection($user);
		$this->deleteFromCommunityApps($user);
		$this->deleteFromCommunityActivities($user);
		$this->deleteFromCommunityVideos($user);
		$this->deleteFromCommunityConnectUsers($user);
		$this->deleteFromCommunityFeatured($user, $groups, $albums, $videos);
		
		if($this->params->get('delete_jommla_contact', 0))
		{
			$this->deleteFromJoomlaContactDetails($user);
		}
	}
	/**
	 * To handle onBeforeDeleteUser event
	 * For Joomla 1.6, onBeforeDeleteUser is now onUserBeforeDelete
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	function onUserBeforeDelete($user)
	{		
		$this->onBeforeDeleteUser($user);
	}
	
	/**
	 * Remove association when a user is removed
	 **/	 	
	function deleteFromCommunityConnectUsers( $user )
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__community_connect_users') . ' '
				. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $user['id'] );
		$db->setQuery( $query );
		$db->query();
	
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}	
	}
	
	function deleteFromCommunityUser($user){		
		$db =& JFactory::getDBO();
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_users")." 
				WHERE 
						".$db->nameQuote("userid")." = ".$db->quote($user['id']);
						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}		
	}
	
	function deleteFromCommunityWall($user){		
		$db =& JFactory::getDBO();
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_wall")." 
				WHERE 
						(".$db->nameQuote("contentid")." = ".$db->quote($user['id'])." OR 
						".$db->nameQuote("post_by")." = ".$db->quote($user['id']).") AND
						".$db->nameQuote("type")." = ".$db->quote('user');						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
	}
	
	function deleteFromCommunityDiscussion($user, $gids){		
		$db =& JFactory::getDBO();
		
		if(!empty($gids)){
			$sql = "SELECT 
							".$db->nameQuote("id")." 						
					FROM 
							".$db->nameQuote("#__community_groups_discuss")." 
					WHERE 
							".$db->nameQuote("groupid")." IN (".$gids.")";						
			$db->setQuery($sql);
			$row = $db->loadobjectList();
			if($db->getErrorNum()){
				JError::raiseError( 500, $db->stderr());
			}
			
			if(!empty($row)){		
				$count = 0;
				$scount = sizeof($row) - 1;
				$ids = "";
				foreach($row as $data){
					$ids .= $data->id;
					if($count < $scount){
						$ids .= ",";
					}
					$count++;
				}
			}			
			$condition 	= $db->nameQuote("creator")." = ".$db->quote($user['id'])." OR 
						".$db->nameQuote("groupid")." IN (".$gids.")";
		}else{
			$condition 	= $db->nameQuote("creator")." = ".$db->quote($user['id']);
		}
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_groups_discuss")." 
				WHERE 
						".$condition;						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		if(!empty($ids)){
			$condition = "(".$db->nameQuote("post_by")." = ".$db->quote($user['id'])." OR 
						   ".$db->nameQuote("contentid")." IN (".$ids."))";
		}else{		
			$condition = $db->nameQuote("post_by")." = ".$db->quote($user['id']);
		}
		
		$sql = "DELETE 
					
				FROM 
						".$db->nameQuote("#__community_wall")." 
				WHERE 
						".$condition." AND 
						".$db->nameQuote("type")." = ".$db->quote('discussions');				
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
	}
	
	function deleteFromCommunityPhoto($user){		
		$db =& JFactory::getDBO();
		//mark photos for deletion
		$sql	= 'UPDATE ' . $db->nameQuote('#__community_photos')
				.' SET ' . $db->nameQuote('albumid') . '=' . $db->Quote(0)
				.' WHERE ' . $db->nameQuote("creator")." = ".$db->quote($user['id']);
		
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		//remove user's albums
		$sql = "SELECT 
						".$db->nameQuote("id")." 
				FROM 
						".$db->nameQuote("#__community_photos_albums")." 
				WHERE 
						".$db->nameQuote("creator")." = ".$db->quote($user['id']);
						
		$db->setQuery($sql);
		$albums = $db->loadobjectList();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		$album	=& JTable::getInstance( 'Album' , 'CTable' );
		CFactory::load( 'libraries' , 'featured' );

		if(!empty($albums)){		
			foreach($albums as $data){
				$album->load( $data->id );
				$album->delete();
				// @rule: remove from featured item if item is featured
				$featured	= new CFeatured( FEATURED_ALBUMS );
				$featured->delete( $album->id );
				
			}
		}
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_photos_tokens")." 
				WHERE 
						".$db->nameQuote("userid")." = ".$db->quote($user['id']);
						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		return $albums;
	}
			
	function deleteFromCommunityMsg($user){		
		$db =& JFactory::getDBO();
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_msg")." 
				WHERE 
						".$db->nameQuote("from")." = ".$db->quote($user['id']);						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_msg_recepient")." 
				WHERE 
						".$db->nameQuote("msg_from")." = ".$db->quote($user['id'])." OR 
						".$db->nameQuote("to")." = ".$db->quote($user['id']);
						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
	}
	
	/**
	 * Remove all events related to the user that is being removed.
	 *
	 *	@param	Array	An array of user's information
	 *	@return	null	 	 
	 **/	 	 	
	public function deleteFromCommunityEvents( $user )
	{
		$db		=& JFactory::getDBO();
		$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__community_events' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'creator' ) . '=' . $db->Quote( $user['id'] );
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();
		
		$event			=& JTable::getInstance( 'Event' , 'CTable' );
		$eventMembers	=& JTable::getInstance( 'EventMembers' , 'CTable' );
		
		// @rule: Delete all events created by this user.
		if( $rows )
		{
			foreach($rows as $row )
			{	
				$event->load( $row->id );
				$event->delete();
			}
		}
		unset( $rows );
		
		// @rule: Delete all events participated by this user.
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_events_members' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $user['id'] );
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();
		
		if( $rows )
		{
			foreach( $rows as $row )
			{
				$event->load( $row->eventid );
				$eventMembers->load( $user['id'] , $row->eventid );
				
				$eventMembers->delete();
				$event->updateGuestStats();
			}
		}
	}
	
	function deleteFromCommunityGroup($user){		
		$db =& JFactory::getDBO();
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_groups_bulletins")." 
				WHERE 
						".$db->nameQuote("created_by")." = ".$db->quote($user['id']);
						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}		
		
		$sql = "SELECT 
						".$db->nameQuote("id")." 						
				FROM 
						".$db->nameQuote("#__community_groups")." 
				WHERE 
						".$db->nameQuote("ownerid")." = ".$db->quote($user['id']);						
		$db->setQuery($sql);
		$row = $db->loadobjectList();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		if(!empty($row)){		
			$count = 0;
			$scount = sizeof($row) - 1;
			$ids = "";
			foreach($row as $data){
				$ids .= $data->id;
				if($count < $scount){
					$ids .= ",";
				}
				$count++;
			}
					
			$sql = "DELETE 
					
					FROM 
							".$db->nameQuote("#__community_groups_members")." 
					WHERE 
							".$db->nameQuote("groupid")." IN (".$ids.") OR 
							".$db->nameQuote("memberid")." = ".$db->Quote($user['id']);						
			$db->setQuery($sql);
			$db->Query();
			if($db->getErrorNum()){
				JError::raiseError( 500, $db->stderr());
			}
		}
					
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_groups")." 
				WHERE 
						".$db->nameQuote("ownerid")." = ".$db->quote($user['id']);						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_wall")." 
				WHERE 
						".$db->nameQuote("post_by")." = ".$db->quote($user['id'])." AND
						".$db->nameQuote("type")." = ".$db->quote('groups');						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		$ids = empty($ids)? "" : $ids;
		
		return $ids;
	}
	
	function deleteFromCommunityProfile($user){		
		$db =& JFactory::getDBO();
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_fields_values")." 
				WHERE 
						".$db->nameQuote("user_id")." = ".$db->quote($user['id']);
						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
	}
	
	function deleteFromCommunityConnection($user){		
		$db =& JFactory::getDBO();
		
		$sql = "SELECT 
						a.".$db->nameQuote("connect_from")." 						
				FROM 
						".$db->nameQuote("#__community_connection")." a
			INNER JOIN 
						".$db->nameQuote("#__community_connection")." b ON a.".$db->nameQuote("connect_from")."=b.".$db->nameQuote("connect_to")."
				WHERE 
						a.".$db->nameQuote("connect_to")." = ".$db->quote($user['id']) ." AND 
						b.".$db->nameQuote("connect_from")." = ".$db->quote($user['id']);						
		$db->setQuery($sql);
		$row = $db->loadobjectList();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		if(!empty($row)){
			$count = 0;
			$scount = sizeof($row) - 1;
			$ids = "";				
			foreach($row as $data){
				$ids .= $data->connect_from;
				if($count < $scount){
					$ids .= ", ";
				}
				$count++;
			}		
			
			$sql = "UPDATE
							".$db->nameQuote("#__community_users")." 				
					SET 
							".$db->nameQuote("friendcount")." = ".$db->nameQuote("friendcount")." - 1 
					WHERE 
							".$db->nameQuote("userid")." IN (".$ids.")";						
			$db->setQuery($sql);
			$db->Query();
			if($db->getErrorNum()){
				JError::raiseError( 500, $db->stderr());
			}
		}
		
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_connection")." 
				WHERE 
						".$db->nameQuote("connect_from")." = ".$db->quote($user['id'])." OR 
						".$db->nameQuote("connect_to")." = ".$db->quote($user['id']);						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
	}
	
	function deleteFromCommunityApps($user){		
		$db =& JFactory::getDBO();
				
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_apps")." 
				WHERE 
						".$db->nameQuote("userid")." = ".$db->quote($user['id']);						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
	}
	
	function deleteFromCommunityActivities($user)
	{		
		$db =& JFactory::getDBO();
				
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_activities")." 
				WHERE 
						(".$db->nameQuote("actor")." = ".$db->quote($user['id'])." OR 
						".$db->nameQuote("target")." = ".$db->quote($user['id']).") AND 
						".$db->nameQuote("archived")." = ".$db->quote(0);								
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
	}
	
	function deleteFromCommunityVideos($user)
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'SELECT '.$db->nameQuote('id').' FROM ' . $db->nameQuote('#__community_videos')
				. ' WHERE '.$db->nameQuote('creator').' = ' . $db->quote($user['id']);
		$db->setQuery($query);
		$videos = $db->loadResultArray();
		
		$query	= 'DELETE FROM ' . $db->nameQuote('#__community_videos')
				. ' WHERE '.$db->nameQuote('creator').' = ' . $db->quote($user['id']);
		$db->setQuery($query);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		$videoLib 	= new CVideoLibrary();
		
		// Converted Videos Folder
		$videoFolder	= $videoLib->videoRootHome . DS . $user['id'];
		if(JFolder::exists($videoFolder)) {
			JFolder::delete($videoFolder);
		}
		// Original Videos Folder
		$videoFolder	= $videoLib->videoRootOrig . DS . $user['id'];
		if(JFolder::exists($videoFolder)) {
			JFolder::delete($videoFolder);
		}
		
		return $videos;
	}
	
	function deleteFromCommunityFeatured($user, $groups, $albums, $videos)
	{    	
    	//delete featured user
		$featured = new CFeatured( FEATURED_USERS );    	
    	if(!empty($user))
    	{
    		$featured->delete($user['id']);
		}
		
		//delete featured groups
		$featured	= new CFeatured( FEATURED_GROUPS );    	
    	if(!empty($groups))
    	{
    		$groupIds = explode(",", $groups);    		
    		foreach($groupIds as $groupId)
    		{
    			$featured->delete($groupId);
    		}
		}
		
		//delete featured albums
		$featured	= new CFeatured( FEATURED_ALBUMS );    	
    	if(!empty($albums))
    	{
    		foreach($albums as $albumId)
    		{
    			$featured->delete($albumId);
    		}
		}
		
		//delete featured albums
		$featured	= new CFeatured( FEATURED_VIDEOS );    	
    	if(!empty($videos))
    	{
    		foreach($videos as $videoId)
    		{
    			$featured->delete($videoId);
    		}
		}
	}
	
	function deleteFromJoomlaContactDetails($user)
	{
		$db =& JFactory::getDBO();
				
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__contact_details")." 
				WHERE 
						".$db->nameQuote("user_id")." = ".($user['id']);								
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
	}
}
