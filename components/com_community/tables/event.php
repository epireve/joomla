<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once ( JPATH_ROOT .DS.'components'.DS.'com_community'.DS.'models'.DS.'models.php');

// Include interface definition
CFactory::load( 'models' , 'tags' );

class CTableEvent extends CTableCache
	implements  CGeolocationInterface, CTaggable_Item
{

	var $id 			= null;
	var $catid			= null;
	var $contentid			= null;
	var $type 			= null;
	var $title 			= null;
        var $summary                    = null;
	var $description		= null;
	var $location			= null;
	var $creator			= null;
	var $startdate			= null;
	var $enddate			= null;
  	var $permission			= null;
  	var $avatar			= null;
  	var $thumb			= null;
  	var $invitedcount		= null;
  	var $confirmedcount		= null;
  	var $declinedcount		= null;
  	var $maybecount			= null;
  	var $created			= null;
  	var $hits			= null;
  	var $published			= null;
  	var $wallcount			= null;
  	var $ticket			= null;
  	var $allowinvite		= null;
  	var $offset			= null;

  	/* Implement geolocation */
  	var $latitude	= null;
  	var $longitude	= null;

	var $_pagination	= '';
	
	/**
	 * Constructor
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__community_events', 'id', $db );
		
		// Get cache object.
 	 	$oCache = CCache::inject($this);
 	 	// Remove video cache on every delete & store
 	 	$oCache->addMethod(CCache::METHOD_DEL, CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_EVENTS, COMMUNITY_CACHE_TAG_EVENTS_CAT));
 	 	$oCache->addMethod(CCache::METHOD_STORE, CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_EVENTS, COMMUNITY_CACHE_TAG_EVENTS_CAT));

		// Set default timezone to current user's timezone
		$my = CFactory::getUser();
 	 	$this->offset = $my->getParam('timezone');
	}

	/**
	 * Binds an array into this object's property
	 *
	 * @access	public
	 * @param	$data	mixed	An associative array or object
	 **/
	public function bind($data)
	{
		$status	= parent::bind($data);

		$this->_fixDates();
				
		return $status;
	}

	// @legacy 
	// Fix events prior to 2.0 so that we get the proper offset of the event.
	// This should be removed in 2.4 or later.
	private function _fixDates()
	{
		if( $this->offset === null && !empty($this->id) )
		{
			// Add proper offset by getting the author's offset.
			$this->offset	= CFactory::getUser( $this->creator )->getTimezone();
			
			// Set Start date
			$date			= JFactory::getDate( $this->startdate );
			$date->setOffset( $this->offset );
			$this->startdate	= $date->toMySQL( true );
			
			unset( $date );

			$date			= JFactory::getDate( $this->enddate );
			$date->setOffset( $this->offset );
			$this->enddate	= $date->toMySQL( true );
			
			// Set 
			$this->store();
		}
	}
	
	public function load( $id = null )
	{
		$status	= parent::load( $id );

		$this->_fixDates();
		
		return $status;
	}
	
	public function check()
	{
		// Santinise data
		$safeHtmlFilter		= CFactory::getInputFilter();
		$this->title		= $safeHtmlFilter->clean($this->title);
		
		// Allow html tags
		$config = CFactory::getConfig();
		$safeHtmlFilter		= CFactory::getInputFilter( $config->get('allowhtml') );
		$this->description 	= $safeHtmlFilter->clean($this->description);
		
		return true;
	}
	
	public function store()
	{
		if (!$this->check()) {
			return false;
		}
		
		if( empty($this->latitude) || empty($this->longitude) )
		{		
			$this->resolveLocation($this->location);
		}
		
		return parent::store();
	}
	
	/**
	 * Make sure hits are user and session sensitive
	 */	 	
	public function hit()
	{
		$session = JFactory::getSession();
		if( $session->get('view-event-'. $this->id, false) == false ) {
			parent::hit();
		}
		$session->set('view-event-'. $this->id, true);
	}
	
	public function getStartTime()
	{
		$edate  = new JDate($this->startdate);
		return $edate->toFormat('%H:%M');
	}
	
	public function getEndTime()
	{
		$edate  = new JDate($this->enddate);
		return $edate->toFormat('%H:%M');
	}
	
	/**
	 * Retrieves the starting date of an event.
	 *
	 * @param	boolean	$formatted Determins whether to call deprecated method.
	 * @return	JDate
	 **/
	public function getStartDate( $formatted = true )
	{
		if( $formatted )
		{
			return $this->_getStartDate();
		}

		$date	= JFactory::getDate( $this->startdate );
		return $date;
	}
	
	/**
	 * Deprecated since 2.x
	 * 
	 * This method was used in place of getStartDate prior to 2.x.
	 **/	 	 	 	 	 	
	public function _getStartDate()
	{
		$edate  = new JDate($this->startdate);
		return $edate->toFormat('%Y-%m-%d');
	}

	/**
	 * Retrieves the ending date of an event.
	 *
	 * @param	boolean	$formatted Determins whether to call deprecated method.
	 * @return	JDate
	 **/
	public function getEndDate( $formatted = true )
	{
		if( $formatted )
		{
			return $this->_getEndDate();
		}
		
		$date	= JFactory::getDate( $this->enddate );
		return $date;
	}
	
	/**
	 * Deprecated since 2.x
	 * 
	 * This method was used in place of getStartDate prior to 2.x.
	 **/	 	 	 	 	 	
	public function _getEndDate()
	{
		$edate  = new JDate($this->enddate);
		return $edate->toFormat('%Y-%m-%d');
	}

	/**
	 * Return the full URL path for the specific image
	 *
	 * @param	string	$type	The type of avatar to look for 'thumb' or 'avatar'
	 * @return string	The category name
	 **/
	public function getAvatar()
	{
		// Get the avatar path. Some maintance/cleaning work: We no longer store
		// the default avatar in db. If the default avatar is found, we reset it
		// to empty. In next release, we'll rewrite this portion accordingly.
		// We allow the default avatar to be template specific.
		if ($this->avatar == 'components/com_community/assets/event.png')
		{
			$this->avatar = '';
			$this->store();
		}
		CFactory::load('helpers', 'url');
		$avatar	= CUrlHelper::avatarURI($this->avatar, 'event.png');
		
		return $avatar;
	}
	
	/**
	 * Return full uri path of the thumbnail
	 */	 	
	public function getThumbAvatar()
	{
		if ($this->thumb == 'components/com_community/assets/event_thumb.png')
		{
			$this->thumb = '';
			$this->store();
		}
		CFactory::load('helpers', 'url');
		$thumb	= CUrlHelper::avatarURI($this->thumb, 'event_thumb.png');
		
		return $thumb;
	}

	/**
	 *	Set the avatar for for specific group
	 *
	 * @param	appType		Application type. ( users , groups )
	 * @param	path		The relative path to the avatars.
	 * @param	type		The type of Image, thumb or avatar.
	 *
	 **/
	public function setImage(  $path , $type = 'thumb' )
	{
		CError::assert( $path , '' , '!empty' , __FILE__ , __LINE__ );

		$db			=& $this->getDBO();

		// Fix the back quotes
		$path		= JString::str_ireplace( '\\' , '/' , $path );
		$type		= JString::strtolower( $type );

		// Test if the record exists.
		$oldFile	= $this->$type;

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
	    }

	    if( $oldFile )
		{
			// File exists, try to remove old files first.
			$oldFile	= JString::str_ireplace( '/' , DS , $oldFile );

			// If old file is default_thumb or default, we should not remove it.
			// Need proper way to test it
			if(!JString::stristr( $oldFile , 'event.png' ) && !JString::stristr( $oldFile , 'event_thumb.png' ) )
			{
				jimport( 'joomla.filesystem.file' );
				JFile::delete($oldFile);
			}
		}
		$this->$type   = $path;
		$this->store();

	}
	
	public function setConfirmedCount($addCount = 1)
	{
		$this->confirmedcount = $this->confirmedcount + $addCount;
		$this->store();
	}

	public function deleteAllMembers()
	{
		$db =& JFactory::getDBO();
		
		$query	= 'DELETE FROM ' . $db->nameQuote('#__community_events_members') 
				. ' WHERE ' . $db->nameQuote( 'eventid' ) . '=' . $db->Quote( $this->id );
				
		$db->setQuery( $query );
		$db->Query();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return true;
	}
	public function deletePendingMember(){

		$db =& JFactory::getDBO();
		$now = new JDate();
		
		$query	= 'DELETE ' . $db->nameQuote('member') . ' FROM ' . $db->nameQuote('#__community_events_members')
				.' member INNER JOIN ' . $db->nameQuote('#__community_events')
				.' event ON member.' . $db->nameQuote('eventid').' = event.' . $db->nameQuote('id')
				.' WHERE member.' . $db->nameQuote('status').'= ' . $db->Quote('0')
				.' AND event.' . $db->nameQuote('enddate').' < ' . $db->Quote( $now->toMySQL() );
		$db->setQuery( $query );
		$db->Query();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return true;
	}
	/**
	 * Delete group's wall
	 *
	 * param	string	id The id of the group.
	 *
	 **/
	public function deleteWalls()
	{
		$db =& JFactory::getDBO();

		$sql = "DELETE

				FROM
						".$db->nameQuote("#__community_wall")."
				WHERE
						".$db->nameQuote("contentid")." = ".$db->quote($this->id)." AND
						".$db->nameQuote("type")." = ".$db->quote('events');
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

		return true;
	}

        public function deleteActivity()
        {
            $db =& JFactory::getDBO();

		$sql = "DELETE

				FROM
						".$db->nameQuote("#__community_activities")."
				WHERE
						".$db->nameQuote("cid")." = ".$db->quote($this->id)." AND
						".$db->nameQuote("app")." LIKE ".$db->quote('%events%');

		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

		return true;
        }

	public function getCreator()
	{
		$user		= CFactory::getUser( $this->creator );
		return $user;
	}
	
	public function getCategoryName()
	{
		$category	=& JTable::getInstance( 'EventCategory' , 'CTable' );
		$category->load( $this->catid );

		return $category->name;
	}

	public function getCreatorName()
	{
		$user		= CFactory::getUser( $this->creator );
		return $user->getDisplayName();
	}

	/**
	 * Returns the members list for the specific groups
	 *
	 * @access public
	 * @param	string Category Id
	 * @returns string	Category name
	 **/
	public function getAdmins( $limit = 0 , $randomize = false )
	{
		$mainframe = JFactory::getApplication();
		$limit		= ($limit != 0) ? $limit : $mainframe->getCfg('list_limit');
		$limitstart	= JRequest::getInt( 'limitstart' , 0 );

		$query	= 'SELECT ' . $this->_db->nameQuote('memberid') . ' AS id, ' . $this->_db->nameQuote('status') . ' AS statusCode FROM '
				. $this->_db->nameQuote('#__community_events_members')
				. ' WHERE ' . $this->_db->nameQuote('eventid') . ' = ' . $this->_db->Quote($this->id)
				. ' AND '. $this->_db->nameQuote('permission') . ' IN (1,2)';

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
				. $this->_db->nameQuote('#__community_events_members')
				. ' WHERE ' . $this->_db->nameQuote('eventid') . ' = ' . $this->_db->Quote($this->id)
				. ' AND ' . $this->_db->nameQuote('permission') . ' IN (1,2)';
		$this->_db->setQuery( $query );
		$total	= $this->_db->loadResult();
		
		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			
			$this->_pagination	= new JPagination( $total , $limitstart , $limit);
		}
		
		return $result;
	}

	public function getAdminsCount()
	{
		$query	= 'SELECT count(a.memberid) FROM '
				. $this->_db->nameQuote('#__community_events_members') . ' AS a '
				. ' INNER JOIN ' . $this->_db->nameQuote('#__users') . ' AS b '
				. ' WHERE b.' . $this->_db->nameQuote('id').'=a.' . $this->_db->nameQuote('memberid')
				. ' AND a.' . $this->_db->nameQuote('eventid').'=' . $this->_db->Quote( $this->id )
				. ' AND a.' . $this->_db->nameQuote('permission').' IN (1,2)';

		$this->_db->setQuery( $query );
		$result	= $this->_db->loadResult();

		if($this->_db->getErrorNum())
		{
			JError::raiseError( 500, $this->_db->stderr());
		}

		return $result;
	}

	public function getPagination()
	{		
		return $this->_pagination;
	}
	
	public function getMembers( $status, $limit = 0, $randomize = false, $pendingApproval = false )
	{
		$mainframe	= JFactory::getApplication();
		$limit		= ($limit != 0 || is_null($limit)) ? $limit : $mainframe->getCfg('list_limit');
		$limitstart	= JRequest::getInt( 'limitstart' , 0 );
				
		CFactory::load('helpers', 'event');
		
		$query	= 'SELECT ' . $this->_db->nameQuote('memberid') . ' AS id, ' . $this->_db->nameQuote('status') . ' AS statusCode FROM '
				. $this->_db->nameQuote('#__community_events_members').' AS a '
				. ' JOIN '  .$this->_db->nameQuote('#__users') . ' AS b ON a.memberid=b.id AND b.block=0 '
				. ' WHERE ' . $this->_db->nameQuote('eventid') . ' = ' . $this->_db->Quote($this->id)
				. ' AND a.' . $this->_db->nameQuote('status') . ' = ' . $this->_db->Quote($status);

		if($randomize)
		{
			$query	.= ' ORDER BY RAND() ';
		}

		if( !is_null($limit) )
		{
			$query	.= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$this->_db->setQuery( $query );
		$result	= $this->_db->loadObjectList();

		if($this->_db->getErrorNum())
		{
			JError::raiseError( 500, $this->_db->stderr());
		}


		$query	= 'SELECT COUNT(1) FROM '
				. $this->_db->nameQuote('#__community_events_members')
				. ' WHERE ' . $this->_db->nameQuote('eventid') . ' = ' . $this->_db->Quote($this->id)
				. ' AND ' . $this->_db->nameQuote('status') . ' = ' . $this->_db->Quote($status);
		$this->_db->setQuery( $query );
		$total	= $this->_db->loadResult();
		
		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			
			$this->_pagination	= new JPagination( $total , $limitstart , $limit);
		}

		return $result;
	}
	
	// for open invite, no invite request
	public function inviteRequestCount()
	{
		$query	= 'SELECT count(a.memberid) FROM '
				. $this->_db->nameQuote('#__community_events_members') . ' AS a '
				. ' INNER JOIN ' . $this->_db->nameQuote('#__users') . ' AS b '
				. ' WHERE b.' . $this->_db->nameQuote('id').'=a.' . $this->_db->nameQuote('memberid')
				. ' AND a.' . $this->_db->nameQuote('eventid').'=' . $this->_db->Quote( $this->id )
				. ' AND a.' . $this->_db->nameQuote('status').'=' . $this->_db->Quote( COMMUNITY_EVENT_STATUS_REQUESTINVITE ) . ' ';
	
		$this->_db->setQuery( $query );
		$result	= $this->_db->loadResult();

		if($this->_db->getErrorNum())
		{
			JError::raiseError( 500, $this->_db->stderr());
		}

		return $result;
	}
	
	public function getMembersCount( $status , $type = 'all', $pendingApproval = false )
	{
		$query	= 'SELECT count(a.memberid) FROM '
				. $this->_db->nameQuote('#__community_events_members') . ' AS a '
				. 'INNER JOIN ' . $this->_db->nameQuote('#__users') . ' AS b '
				. 'WHERE b.' . $this->_db->nameQuote('id').'=a.' . $this->_db->nameQuote('memberid')
				. ' AND a.' . $this->_db->nameQuote('eventid').'=' . $this->_db->Quote( $this->id ) 
				. ' AND b.block=0';
        
		/*
		if($type != 'all')
        {
          if($type == 'join')
              $query  .= 'AND a.invited_by = ' . $this->_db->Quote('0');
          else if($type == 'invite')
              $query  .= 'AND a.invited_by != ' . $this->_db->Quote('0');
        }
		
		/*
        if($pendingApproval)
            $query  .= 'AND a.`approval` = ' . $this->_db->Quote('1');
		*/
		
		CFactory::load('helpers', 'event');
		//$statusCode	= CEventHelper::getStatusCode($status);
		$query	.= ' AND a.' . $this->_db->nameQuote('status').' = ' . $this->_db->Quote($status);

		$this->_db->setQuery( $query );
		$result	= $this->_db->loadResult();

		if($this->_db->getErrorNum())
		{
			JError::raiseError( 500, $this->_db->stderr());
		}

		return $result;
	}
	
	public function getMemberStatus($userid)
	{
		if($userid == 0) return false;
		$member	=& JTable::getInstance( 'EventMembers' , 'CTable' );
		$member->load($userid, $this->id);
		
		return $member->status;
	}
	
	/**
	 * Return true if event is in the past
	 * @return boolean
	 */
	public function isExpired()
	{
		$date		= new JDate( $this->enddate );
		$current	=& JFactory::getDate();
		
		return $current->toUnix( true ) > $date->toUnix( true );
	}
	
	
	/**
	 * Return true if the user is admin for the event
	 * @param int $userid
	 * @return boolean
	 */
	public function isAdmin($userid)
	{
		if($userid == 0) return false;

		$member	=& JTable::getInstance( 'EventMembers' , 'CTable' );
		$member->load($userid, $this->id);

		return ($member->permission == '1' || $member->permission == '2');
	}
	
	/**
	 * Return true is user is creator of the event.
	 * Since 2.4, there is no more event creator concept. Event admins will
	 * have the same privilege as the creator
	 * @deprecated
	 * @param type $userId
	 * @return type 
	 */
	public function isCreator( $userId )
	{
		return $this->isAdmin($userId);
	}
	
	/**
	 * Return the status of this user related to this event
	 * 0: invited
	 * 1: attend
	 * 2: won't attend
	 * 3: maybe
	 * 4: blocked from attending
	 * 5: requesting invite
	 * 6: no relation	 	 	 	 	 	 	 
	 */	 	
	public function getUserStatus($userid)
	{
		$member	=& JTable::getInstance( 'EventMembers' , 'CTable' );
		$member->load($userid, $this->id);
		
		// No relation
		if($member->id == 0){
			return COMMUNITY_EVENT_STATUS_NOTINVITED;
		}
		
		return $member->status;
	}
	
	public function upgradeWallToStream(){
		$this->eventActivitiesMigrate();
	}
	
	private function eventActivitiesMigrate(){
		$db		= JFactory::getDBO();
		
		if(!COwnerHelper::isCommunityAdmin()){
			//only admin can migrate
			//return false;
		}
		/* To check what is the version of jomsocial for current db */
		$query	= 'SELECT * FROM '.$db->nameQuote('#__community_activities')
				. ' WHERE '.$db->nameQuote('params').' LIKE ' . $db->Quote( '%events.wall.create%' ).' AND '.$db->nameQuote('params').' LIKE ' . $db->Quote( '%eventid='.$this->id.'%' );
		
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
				
				//getting the group id out from the param
				$decoded_params = (array)json_decode($result['params']);//explode('=',$result['params']);
				
				//depends on the version, some is encoded in json, hence result is different
				if(isset($decoded_params['event_url'])){
					$group_url = $decoded_params['event_url'];
					$group_url_arr = explode('=' , $group_url);
					$wall_activity_match[$result['id']] = $decoded_params['wallid'];
					$group_id = $group_url_arr[count($group_url_arr)-1];
				}else{
					$group_url_arr = explode('=' , $result['params']);
					$wall_activity_match[$result['id']] = trim($group_url_arr[count($group_url_arr)-1]);
					$group_id = $result['cid'];
				}
				
				$result['eventid'] = $group_id; // set group id
				$result['target'] = $group_id; // set target as group id
				$result['cid'] = $group_id; // set cid as group id			
				$result['params'] = ''; //empty params
				$result['like_id'] = $result['id']; // set like_id as id
				$result['comment_id'] = $result['id']; // set comment id to the current row id
				$result['groupid'] = 0;
				$result['app'] = 'events.wall';
				$result['like_type'] = 'events.wall';
				$result['comment_type'] = 'events.wall';
			}
			
			// Lets update the converted row into the 2.4 format!
			foreach ($results as $res){
				$tmp_res = $res;
				unset($tmp_res['created']);//created no need to update
				$tmp_result = (object)$tmp_res;
				$db->updateObject( '#__community_activities', $tmp_result, 'id' );			
			}
			
			/* lets update the wall content */			
			if(!empty($wall_activity_match)){
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

						$data = array(
									'contentid' => $activity_id,
									'post_by' => $comment->creator,
									'ip' => '',//leave empty because the ip is not stored in 2.2.x
									'comment' => $comment->text,
									'date' => $date,
									'published' => 1,
									'type' => 'events.wall'
								);
				
						$tmp_data = (object)$data;
						$db->insertObject('#__community_wall', $tmp_data);
					}
				}
			}
		}
	}

	/**
	 * Check if the given user is a member of the event
	 * A member is basically someone who has marked their attendance
	 * @param	string	userid
	 */
	public function isMember($userid)
	{
		// A site guest is clearly not a member
		if($userid == 0)
		{
			return false;
		}

		$member	=& JTable::getInstance( 'EventMembers' , 'CTable' );
		$member->load($userid, $this->id);
		
		if($member->id == '0')
		{
            return false;
        }
        
        return true;
	}

	/**
	 * Check if the given user was pending approval
	 * @param	string	userid
	 */
	public function isPendingApproval($userid)
	{
		// guest is not a member of any group
		if($userid == 0)
			return false;

		$member	=& JTable::getInstance( 'EventMembers' , 'CTable' );
		$member->load($userid, $this->id);

        if($member->id == 0)
        {
            return false;
        }
        else
        {
		    return ($member->status == COMMUNITY_EVENT_STATUS_REQUESTINVITE);
        }
	}


	public function addWallCount()
	{
		$query	= 'UPDATE ' . $this->_db->nameQuote( '#__community_events' ) . ' '
				. 'SET ' . $this->_db->nameQuote('wallcount').' = ( ' . $this->_db->nameQuote('wallcount').' + 1 ) '
				. 'WHERE '. $this->_db->nameQuote( 'id' ) .'='. $this->_db->Quote($this->id);
		$this->_db->setQuery( $query );
		$this->_db->query();
		if($this->_db->getErrorNum())
		{
			JError::raiseError( 500, $this->_db->stderr());
		}
		$this->wallcount++;
	}

	public function substractWallCount()
	{
		$query	= 'UPDATE ' . $this->_db->nameQuote( '#__community_events' ) . ' '
				. 'SET ' . $this->_db->nameQuote('wallcount').' = ( ' . $this->_db->nameQuote('wallcount').' - 1 ) '
				. 'WHERE '. $this->_db->nameQuote( 'id' ) .'='. $this->_db->Quote($this->id);
		$this->_db->setQuery( $query );
		$this->_db->query();
		if($this->_db->getErrorNum())
		{
			JError::raiseError( 500, $this->_db->stderr());
		}
		$this->wallcount--;
	}
	
	/**
	 * Recalculate event guest stats
	 */
	public function updateGuestStats()
	{
		$countFields = array(
					'confirmedcount' => COMMUNITY_EVENT_STATUS_ATTEND,
					'declinedcount' => COMMUNITY_EVENT_STATUS_WONTATTEND,
					'maybecount' => COMMUNITY_EVENT_STATUS_MAYBE,
					'invitedcount' => COMMUNITY_EVENT_STATUS_INVITED	);
		
		// update all 4 count fields
		foreach($countFields as $key => $value)
		{
			$query	= 'SELECT count(*) FROM ' . $this->_db->nameQuote( '#__community_events_members' ) . ' '
					. ' WHERE ' 
					. 		$this->_db->nameQuote( 'status' ) . '=' . $this->_db->Quote( $value )
					. ' AND '
					. 		$this->_db->nameQuote( 'eventid' ) . '=' . $this->_db->Quote( $this->id );
					
			$this->_db->setQuery( $query );
			$this->$key = $this->_db->loadResult();
		}
	}
	
	/** Interface fucntions **/
	
	
	public function resolveLocation($address)
	{
		CFactory::load('libraries', 'mapping');
		$data = CMapping::getAddressData($address);
		
		// reset it to null;
		$this->latitude 	= COMMUNITY_LOCATION_NULL;
		$this->longitude	= COMMUNITY_LOCATION_NULL;
		
		if($data){
			if($data->status == 'OK')
			{
				$this->latitude  = $data->results[0]->geometry->location->lat;
				$this->longitude = $data->results[0]->geometry->location->lng; 
			}
		}
	}

	/**
	 * Remove guest from events
	 *
	 **/
	public function removeGuest($guestId, $eventId){
		$db		=&	JFactory::getDBO();

		$query	=	"DELETE FROM " . $db->nameQuote("#__community_events_members")
					. " WHERE " . $db->nameQuote("memberid") . "=" . $db->quote($guestId)
					. " AND " . $db->nameQuote("eventid") . "=" . $db->quote($eventId) ;

		$db->setQuery($query);

		$db->Query();

		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

		return true;
	}
	
	/**
	 * Override default delete method so that we can remove necessary data for Events.
	 *
	 *	@params	null
	 *	@return	boolean	True on success false otherwise.	 	 
	 **/	 	 	
	public function delete( $id = null )
	{
		$this->deleteAllMembers();
		$this->deleteWalls();
                $this->deleteActivity();
		return parent::delete( $id );
	}
	
	/**
	 * Retrieves the URL to the current event.
	 **/	 	
	public function getLink()
	{
		CFactory::load( 'helpers' , 'event' );
		
		$handler	= CEventHelper::getHandler( $this );
		return $handler->getFormattedLink( 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $this->id );
	}

	/**
	 * Retrieves the URL to the current event.
	 **/	 	
	public function getGuestLink( $status = COMMUNITY_EVENT_STATUS_ATTEND )
	{
		CFactory::load( 'helpers' , 'event' );
		
		$handler	= CEventHelper::getHandler( $this );
		return $handler->getFormattedLink( 'index.php?option=com_community&view=events&task=viewguest&eventid=' . $this->id . '&type=' . $status );
	}



	/**
	 * Return the title of the object
	 */
	public function tagGetTitle()
	{
		return $this->title;
	}

	/**
	 * Return the HTML summary of the object
	 */
    public function tagGetHtml()
	{
		return '';
	}

	/**
	 * Return the internal link of the object
	 *
	 */
	public function tagGetLink()
	{
		return $this->getViewURI();
	}
	
	/**
	 * Return true if the user is allow to modify the tag
	 *
	 */
	public function tagAllow($userid)
	{
		return $this->isAdmin($userid);
	}
}
