<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableGroup extends CTableCache
{

	var $id				= null;
	var $published		= null;
	var $ownerid 		= null;
	var $categoryid 	= null;
	var $name			= null;
	var $description	= null;
	var $email			= null;
	var $website 		= null;
	var $approvals 		= null;
	var $created 		= null;
  	var $avatar			= null;
  	var $thumb			= null;
  	var $discusscount	= null;
  	var $wallcount		= null;
  	var $membercount	= null;
  	var $params			= null;
  	var $_pagination	= null;
	var $storage		= null;
	
	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_groups', 'id', $db );
 	 	
		// Get cache object.
 	 	$oCache = CCache::inject($this);
 	 	
		// Remove groups cache on every delete & store
 	 	$oCache->addMethod(CCache::METHOD_DEL, CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_GROUPS, COMMUNITY_CACHE_TAG_GROUPS_CAT));
 	 	$oCache->addMethod(CCache::METHOD_STORE, CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_GROUPS, COMMUNITY_CACHE_TAG_GROUPS_CAT));
	}

	public function getPagination()
	{
		return $this->_pagination;
	}
	
	public function updateMembers()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT m.* FROM '
				. $db->nameQuote('#__community_groups_members') . ' AS m'
				. ' LEFT JOIN '
				. $db->nameQuote('#__users') . ' AS u ON u.id = m.memberid'
				. ' WHERE ' . $db->nameQuote('u.block') . ' = ' . $db->quote(0)
				. ' AND ' . $db->nameQuote('m.groupid') . ' = ' . $db->quote($this->id)
				. ' AND ' . $db->nameQuote('m.approved') . ' = ' . $db->quote(1);
		$db->setQuery();
		$row	= $db->loadResult();
	}
	
	/**
	 * Update all internal count without saving them
	 */	 	
	public function updateStats()
	{
		if( $this->id != 0 )
		{
			$db	=& JFactory::getDBO();
			
			// @rule: Update the members count each time stored is executed.
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__community_groups_members' ) . ' AS a '
					. 'JOIN '. $db->nameQuote( '#__users' ). ' AS b ON a.'.$db->nameQuote('memberid').'=b.'.$db->nameQuote('id')
					. 'AND b.'.$db->nameQuote('block').'=0 '
					. 'WHERE ' . $db->nameQuote('groupid') .'=' . $db->Quote( $this->id ) . ' '
					. 'AND ' . $db->nameQuote('approved'). '=' . $db->Quote( '1' ) . ' '
					. 'AND permissions!=' . $db->Quote(COMMUNITY_GROUP_BANNED);
			
			$db->setQuery( $query );
			$this->membercount	= $db->loadResult();

			// @rule: Update the discussion count each time stored is executed.
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__community_groups_discuss' ) . ' '
					. 'WHERE '. $db->nameQuote('groupid') .'=' . $db->Quote( $this->id );

			$db->setQuery( $query );
			$this->discusscount	= $db->loadResult();

			// @rule: Update the wall count each time stored is executed.
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__community_activities' ) . ' '
					. 'WHERE ' . $db->nameQuote('cid'). '=' . $db->Quote( $this->id ) . ' '
					. 'AND '. $db->nameQuote('app') .'=' . $db->Quote( 'groups.wall' );

			$db->setQuery( $query );
			$this->wallcount	= $db->loadResult();
		}
	}
	
	public function check()
	{
		// Santinise data
		$safeHtmlFilter		= CFactory::getInputFilter();
		$this->name		= $safeHtmlFilter->clean($this->name);
		$this->email 		= $safeHtmlFilter->clean($this->email);
		$this->website 		= $safeHtmlFilter->clean($this->website);

		// Allow html tags
		$config			= CFactory::getConfig();
		$safeHtmlFilter		= CFactory::getInputFilter( $config->get('allowhtml') );
		$this->description 	= $safeHtmlFilter->clean($this->description);
		
		return true;
	}
	
	/**
	 * Binds an array into this object's property
	 *
	 * @access	public
	 * @param	$data	mixed	An associative array or object
	 **/
	public function store()
	{
		if (!$this->check()) {
			return false;
		}
		
		// Update activities as necessary
		$activityModel = CFactory::getModel('activities');
		$activityModel->update( array('groupid' => $this->id), array('group_access' => $this->approvals) );
		
		return parent::store();
	}

	/**
	 * Return the category name for the current group
	 * 
	 * @return string	The category name
	 **/
	public function getCategoryName()
	{
		$category	=& JTable::getInstance( 'GroupCategory' , 'CTable' );
		$category->load( $this->categoryid );

		return $category->name;
	}

	/**
	 * Return the full URL path for the specific image
	 * 
	 * @param	string	$type	The type of avatar to look for 'thumb' or 'avatar'. Deprecated since 1.8 
	 * @return string	The avatar's URI
	 **/
	public function getAvatar()
	{
		
		// Get the avatar path. Some maintance/cleaning work: We no longer store
		// the default avatar in db. If the default avatar is found, we reset it
		// to empty. In next release, we'll rewrite this portion accordingly.
		// We allow the default avatar to be template specific.
		if ($this->avatar == 'components/com_community/assets/group.jpg')
		{
			$this->avatar = '';
			$this->store();
		}

		// For group avatars that are stored in a remote location, we should return the proper path.
		if( $this->storage != 'file' && !empty($this->avatar) )
		{
			$storage = CStorage::getStorage($this->storage);
			return $storage->getURI( $this->avatar );
		}
		
		CFactory::load('helpers', 'url');
		$avatar	= CUrlHelper::avatarURI($this->avatar, 'group.png');
		
		return $avatar;
	}

	public function getThumbAvatar()
	{
		if ($this->thumb == 'components/com_community/assets/group_thumb.jpg')
		{
			$this->thumb = '';
			$this->store();
		}

		// For group avatars that are stored in a remote location, we should return the proper path.
		if( $this->storage != 'file' && !empty($this->thumb) )
		{
			$storage = CStorage::getStorage($this->storage);
			return $storage->getURI( $this->thumb );
		}
		
		CFactory::load('helpers', 'url');
		$thumb	= CUrlHelper::avatarURI($this->thumb, 'group_thumb.png');
		
		return $thumb;
	}
	
	/**
	 * Return the owner's name for the current group
	 * 
	 * @return string	The owner's name
	 **/	 	
	public function getOwnerName()
	{
		$user		= CFactory::getUser( $this->ownerid );
		return $user->getDisplayName();
	}

	public function getParams()
	{
		$params	= new CParameter( $this->params );
		
		return $params;
	}
	
	/**
	 * Method to determine whether the specific user is a member of a group
	 * 
	 * @param	string	User's id
	 * @return boolean True if user is registered and false otherwise
	 **/
	public function isMember( $userid )
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' 
				. $db->nameQuote( '#__community_groups_members') . ' '
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $userid )
				. 'AND ' . $db->nameQuote( 'approved' ) . '=' . $db->Quote( '1' );
		$db->setQuery( $query );

		$status	= ( $db->loadResult() > 0 ) ? true : false;

		return $status;
	}

	public function isBanned( $userid )
	{
		$db	=&  $this->getDBO();

		$query	=   'SELECT COUNT(*) FROM '
			    . $db->nameQuote( '#__community_groups_members') . ' '
			    . 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->id ) . ' '
			    . 'AND ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $userid )
			    . 'AND ' . $db->nameQuote( 'permissions' ) . '=' . $db->Quote( COMMUNITY_GROUP_BANNED );

		$db->setQuery( $query );

		$status	= ( $db->loadResult() > 0 ) ? true : false;

		return $status;
	}

	public function isAdmin( $userid )
	{
		if($this->id ==0)
			return false;
		
		if($userid == 0)
			return false;
		
		// the creator is also the admin
		if($userid == $this->ownerid)
			return true;

		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' 
				. $db->nameQuote( '#__community_groups_members') . ' '
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $userid )
				. 'AND ' . $db->nameQuote( 'approved' ) . '=' . $db->Quote( '1' )
				. 'AND ' . $db->nameQuote( 'permissions' ) . '=' . $db->Quote( COMMUNITY_GROUP_ADMIN );
		$db->setQuery( $query );

		$status	= ( $db->loadResult() > 0 ) ? true : false;

		return $status;
	}
	
	public function getLink( $xhtml = false )
	{
		$link	= CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $this->id , $xhtml );
		return $link;
	}
	
	public function getMembersCount()
	{
		return $this->membercount;
	}
	
	/**
	 * Determines if the current group is a private group.
	 **/	 	 	
	public function isPrivate()
	{
		return $this->approvals == COMMUNITY_PRIVATE_GROUP;
	}

	/**
	 * Determines if the current group is a public group.
	 **/	
	public function isPublic()
	{
		return $this->approvals == COMMUNITY_PUBLIC_GROUP;
	}
	
	/**
	 * Return true if the user is allow to modify the tag
	 */
	public function tagAllow($userid)
	{
		return $this->isAdmin($userid);
	}
	
	/**
	 * Return the title of the object
	 */
	public function tagGetTitle()
	{
		return $this->title;
	}
	
	/**
	 * Allows caller to bind parameters from the request
	 * @param	array 	$params		An array of values which keys should match with the parameter.
	 */
	public function bindRequestParams()
	{
		// Default to current params
		$params		= new CParameter( $this->params );
		
		$discussordering			= JRequest::getVar( 'discussordering' , DISCUSSION_ORDER_BYLASTACTIVITY , 'REQUEST' );
		$params->set('discussordering' , $discussordering );
		
		$photopermission			= JRequest::getVar( 'photopermission' , GROUP_PHOTO_PERMISSION_ADMINS , 'REQUEST' );
		$params->set('photopermission' , $photopermission );
		
		$videopermission			= JRequest::getVar( 'videopermission' , GROUP_PHOTO_PERMISSION_ADMINS , 'REQUEST' );
		$params->set('videopermission' , $videopermission );

		$eventpermission			= JRequest::getVar( 'eventpermission' , GROUP_EVENT_PERMISSION_ADMINS , 'REQUEST' );
		$params->set('eventpermission' , $eventpermission );
					
		$grouprecentphotos			= JRequest::getInt( 'grouprecentphotos' , GROUP_PHOTO_RECENT_LIMIT , 'REQUEST' );
		$params->set('grouprecentphotos' , $grouprecentphotos );
		
		$grouprecentvideos			= JRequest::getInt( 'grouprecentvideos' , GROUP_VIDEO_RECENT_LIMIT , 'REQUEST' );
		$params->set('grouprecentvideos' , $grouprecentvideos );			
		
		$grouprecentevent			= JRequest::getInt( 'grouprecentevents' , GROUP_EVENT_RECENT_LIMIT , 'REQUEST' );
		$params->set('grouprecentevents' , $grouprecentevent );

		$newmembernotification		= JRequest::getInt( 'newmembernotification' , '1' , 'REQUEST' );
		$params->set('newmembernotification' , $newmembernotification );
		
		$joinrequestnotification	= JRequest::getInt( 'joinrequestnotification' , '1' , 'REQUEST' );
		$params->set('joinrequestnotification' , $joinrequestnotification );
		
		$wallnotification			= JRequest::getInt( 'wallnotification' , '1' , 'REQUEST' );
		$params->set('wallnotification' , $wallnotification );
		
		$this->params	= $params->toString();
		
		return true;
	}

	/**
	 * Allows caller to update the owner name
	 */
	public function updateOwner( $oldOwner , $newOwner )
	{
		if( $oldOwner == $newOwner )
		{
			return true;
		}
		
		// Add member if member does not exist.
		if( !$this->isMember( $newOwner , $this->id ) )
		{
			$data 				= new stdClass();
			$data->groupid		= $this->id;
			$data->memberid		= $newOwner;
			$data->approved		= 1;
			$data->permissions	= 1;
			
			// Add user to group members table
			$this->addMember( $data );
			
			// Add the count.
			$this->updateStats( $group->id );
		}
		else
		{
			// If member already exists, update their permission
                        
			$member	=& JTable::getInstance( 'GroupMembers' , 'CTable' );
			$member->load( $group->id , $newOwner );
			$member->permissions	= '1';

			$member->store();
                
                         
                         
		}
	}

	/**
	 * 
	 */
	public function addMember( $data )
	{
		$db	=& $this->getDBO();
		
		// Test if user if already exists
		if( !$this->isMember($data->memberid, $data->groupid) )
		{
			$db->insertObject('#__community_groups_members' , $data);
			$this->updateStats();
		}
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		return $data;
	}

	public function deleteMember($gid,$memberid){
		$db = JFactory::getDBO();

	    $sql = "DELETE FROM ". $db->nameQuote("#__community_groups_members")."
		    WHERE " .$db->nameQuote("groupid") ."=" .$db->quote($gid). "
		    AND " .$db->nameQuote("memberid"). "=" .$db->quote($memberid);

	    $db->setQuery($sql);
	    $db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

	    return true;
	}
        
	public function getAdmins( $limit = 0 , $randomize = false )
	{
		$mainframe      = JFactory::getApplication();
		$limit		= ($limit != 0) ? $limit : $mainframe->getCfg('list_limit');
		$limitstart	= JRequest::getInt( 'limitstart' , 0 );

		$query	= 'SELECT ' . $this->_db->nameQuote('memberid') . ' AS id, ' . $this->_db->nameQuote('approved') . ' AS statusCode FROM '
				. $this->_db->nameQuote('#__community_groups_members')
				. ' WHERE ' . $this->_db->nameQuote('groupid') . ' = ' . $this->_db->Quote($this->id)
				. ' AND '. $this->_db->nameQuote('permissions') . ' IN (1,2)';

		if($randomize)
		{
			$query	.= ' ORDER BY RAND() ';
		}

		if( !is_null($limit) )
		{
			$query	.= ' LIMIT ' . $limit;
		}
		$this->_db->setQuery( $query );
		$result	= $this->_db->loadObjectList();

		if($this->_db->getErrorNum())
		{
			JError::raiseError( 500, $this->_db->stderr());
		}

		$query	= 'SELECT COUNT(1) FROM '
				. $this->_db->nameQuote('#__community_groups_members')
				. ' WHERE ' . $this->_db->nameQuote('groupid') . ' = ' . $this->_db->Quote($this->id)
				. ' AND ' . $this->_db->nameQuote('permissions') . ' IN (1,2)';
		$this->_db->setQuery( $query );
		$total	= $this->_db->loadResult();
		
		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			
			$this->_pagination	= new JPagination( $total , $limitstart , $limit);
		}
		
		return $result;
	}

	public function setImage( $path , $type = 'thumb' )
	{
		CError::assert( $path , '' , '!empty' , __FILE__ , __LINE__ );

		$db			=& $this->getDBO();

		// Fix the back quotes
		$path		= CString::str_ireplace( '\\' , '/' , $path );
		$type		= JString::strtolower( $type );

		// Test if the record exists.
		$oldFile	= $this->$type;

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		if($oldFile)
		{
			// File exists, try to remove old files first.
			$oldFile	= CString::str_ireplace( '/' , DS , $oldFile );

			// If old file is default_thumb or default, we should not remove it.
			//
			// Need proper way to test it
			if(!JString::stristr( $oldFile , 'group.jpg' ) && !JString::stristr( $oldFile , 'group_thumb.jpg' ) && !JString::stristr( $oldFile , 'default.jpg' ) && !JString::stristr( $oldFile , 'default_thumb.jpg' ) )
			{
				jimport( 'joomla.filesystem.file' );
				JFile::delete($oldFile);
			}
		}
		$this->$type   = $path;
		$this->store();

	}
	
	/**
	 * In 2.4, wall is removed and converted to stream data
	 * On first load, import old wall to stream data
	 */
	public function upgradeWallToStream()
	{
		$params		= new CParameter( $this->params );
		if( $params->get('stream') != 1 )
		{
			$this->groupActivitiesMigrate();
			/*
			 UPDATE `jos_community_activities` as a
				SET 
				a.`title` = a.`content` , 
				a.`content` = '', 
				a.`groupid`= a.`cid`,
				a.`comment_type` = 'groups.wall',
				a.`comment_id` = a.`id`,
				a.`like_type` = 'groups.wall',
				a.`like_id` = a.`id`,
				a.`params` = ''

			WHERE a.`app` = 'groups.wall' AND a.`groupid` IS NULL
			 

			$query	= 'UPDATE '. $this->_db->nameQuote('#__community_activities') . ' as a '
					.' SET '
					.' a.'. $this->_db->nameQuote('title'). ' = '. $this->_db->nameQuote('content'). ' , '
					.' a.'. $this->_db->nameQuote('content'). ' = '. $this->_db->Quote(''). ', '
					.' a.'. $this->_db->nameQuote('groupid'). '= a.'. $this->_db->nameQuote('cid'). ', '
					.' a.'. $this->_db->nameQuote('comment_type'). ' = '. $this->_db->Quote('groups.wall'). ', '
					.' a.'. $this->_db->nameQuote('comment_id'). ' = a.'. $this->_db->nameQuote('id'). ', '
					.' a.'. $this->_db->nameQuote('like_type'). ' = '. $this->_db->Quote('groups.wall'). ', '
					.' a.'. $this->_db->nameQuote('like_id'). ' = a.'. $this->_db->nameQuote('id'). ', '
					.' a.'. $this->_db->nameQuote('params'). ' = '. $this->_db->Quote('')
					.' WHERE '
					.' a.'. $this->_db->nameQuote('app'). ' = '. $this->_db->Quote('groups.wall')
					.' AND a.'. $this->_db->nameQuote('groupid'). ' IS NULL ';

			$this->_db->setQuery($query);
			$this->_db->Query();
			*/
			// Mark this group as upgraded
			$params		= new CParameter( $this->params );
			$params->set('stream' , 1 );
			$this->params	= $params->toString();

			// Store will upgrade save the params AND update stream group_access data
			$this->store();
		}
	}
	
	private function groupActivitiesMigrate(){
		$db		= JFactory::getDBO();
		
		/* To check what is the version of jomsocial for current db */
		$query	= 'SELECT * FROM '.$db->nameQuote('#__community_activities')
				. ' WHERE '.$db->nameQuote('params').' LIKE ' . $db->Quote( '%group.wall.create%' ).' AND '.$db->nameQuote('params').' LIKE ' . $db->Quote( '%groupid='.$this->id.'%' );
		
		$db->setQuery( $query );
		$results = $db->loadobjectList();
		
		if(!empty($results)){
			// format : wall_activity_match['activity id'] = wall id
			$wall_activity_match = array(); // this is used to match id from activities with wall id
			
			foreach ($results as &$result){
				// update the info
				$result = (array)$result;
				//change content to title
				$result['title'] = $result['content'];
				$result['content'] = ''; //empty content after assigned to title
				
				$wall_activity_match[$result['id']] = $result['comment_id'];
				
				//getting the group id out from the param
				$decoded_params = (array)json_decode($result['params']);//explode('=',$result['params']);
				if(isset($decoded_params['group_url'])){
					$group_url = $decoded_params['group_url'];
					$group_url_arr = explode('=' , $group_url);
				}else{
					$group_url_arr = explode('=' , $result['params']);
				}
				$group_id = $group_url_arr[count($group_url_arr)-1];
				$result['groupid'] = $group_id; // set group id
				$result['target'] = $group_id; // set target as group id
				$result['cid'] = $group_id; // set cid as group id			
				$result['params'] = ''; //empty params
				$result['like_id'] = $result['id']; // set like_id as id
				$result['comment_id'] = $result['id']; // set comment id to the current row id
				$result['eventid'] = 0;
				$result['like_type'] = 'groups.wall';
				$result['comment_type'] = 'groups.wall';
			}
			
			//echo '> Start to convert 2.2.x activities table<br/>';
			//echo '2.2.x has '.count($results).' activities to be converted.<br/>';
			// Lets update the converted row into the 2.4 format!
			foreach ($results as $res){
				$tmp_res = $res;
				unset($tmp_res['created']);//created no need to update
				//echo 'Converting activity #'.$res['id'].' -- ';
				$tmp_result = (object)$tmp_res;
				$db->updateObject( '#__community_activities', $tmp_result, 'id' );
				if($db->getErrorNum())
				{
					//echo 'Failed <br/>';
				}else{
					//echo 'Succeed <br/>';
				}
				
			}
			//echo '> 2.2.x activities table conversion ends<br/><br/><br/>';
			
			/* lets update the wall content */			
			if(!empty($wall_activity_match)){
				//echo '> Start to convert 2.2.x wall table<br/> ';
				// narrow down the search with array
				$in = implode(',',$wall_activity_match);
			
				$query	= 'SELECT * FROM '.$db->nameQuote('#__community_wall')
						. ' WHERE '.$db->nameQuote('id').' IN (' . $in .' ) ';
						
				$db->setQuery( $query );

				$results = $db->loadobjectList();
				
				foreach($results as $result){
					//extract the comments if there is any
					$pos = strpos($result->comment, '<comment>');
					
					if(!$pos){
						continue;
					}
					
					list($str,$comments) = explode('<comment>',$result->comment);
					$comments_arr = json_decode(strip_tags(trim($comments),'</comment>'));
					
					//delete this record... optional
					
					$activity_id = array_search($result->id,$wall_activity_match); 
					
					foreach($comments_arr as $comment){
						$dateObject = CTimeHelper::getDate($comment->date);
						$date = (C_JOOMLA_15==1)?gmdate('Y-m-d H:i:s', $comment->date):$dateObject->Format('Y-m-d H:i:s');
						
						//echo 'Inserting new wall base on wall #'.$result->id.' -- date: '.$date.' == ';
						
						$data = array(
									'contentid' => $activity_id,
									'post_by' => $comment->creator,
									'ip' => '',//leave empty because the ip is not stored in 2.2.x
									'comment' => $comment->text,
									'date' => $date,
									'published' => 1,
									'type' => 'groups.wall'
								);
				
						$tmp_data = (object)$data;
						$db->insertObject('#__community_wall', $tmp_data);
						if($db->getErrorNum())
						{
							//echo 'Failed <br/>';
						}else{
							//echo 'Succeed <br/>';
						}
					}
				}
				//echo '> 2.2.x wall table conversion ends<br/><br/><br/>';
			}
		}
	}
}
