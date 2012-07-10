<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableActivity extends JTable
{
	
	public $id 		= null;
	public $actor 	= null;
	public $target 	= null;
	public $title 	= null;
	public $content = null;
	public $app 	= null;
	public $cid 	= null;
	
	/* Action verbs, http://activitystrea.ms/registry/verbs/ */
	public $verb 	= null;
	
	/* Object, http://activitystrea.ms/registry/object_types/ 
	 * for now, must match one of our tablse
	 */
	public $object 	= null;
	
	/* Object ids */
	public $groupid = null;
	public $eventid = null;
	public $group_access = null;
	public $event_access = null;
	
	public $created = null;
	public $access 	= null;
	public $params 	= null;
	public $points 	= null;
	public $archived  = null;
	public $location  = null;
	public $latitude  = null;
	public $longitude = null;
	
	public $comment_id 	 = null;
	public $comment_type = null;
	public $like_id 	 = null;
	public $like_type 	 = null;

	/*
	 * Fields below is bind by /libraries/activities.php during the getActivities
	 * query. It won't be available if we just call JTable::getInstance();
	 */
	private $_daydiff 		= null;
	private $_comment_count = null;
	private $_comment_date = null;
	private $_comment_last	= null;
	private $_comment_last_by = null;
	private $_comment_last_id = null;
	
	private $_likes = null;
	
	
	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_activities', 'id', $db );
	}

	public function load($id, $loadDayDiff = false)
	{
	
		if(!$loadDayDiff){
			return parent::load($id);
		}
		
		// Require day diff
		$db	 	= &$this->getDBO();
		$date	= CTimeHelper::getDate(); 
		
		$sql = 'SELECT a.*, TO_DAYS('.$db->Quote($date->toMySQL(true)).') -  TO_DAYS( DATE_ADD(a.' . $db->nameQuote('created').', INTERVAL '.$date->getOffset()." HOUR ) ) as '_daydiff' "
			.' FROM ' . $db->nameQuote('#__community_activities') . ' as a '
			." WHERE " . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $sql );
		$result	= $db->loadObject();
		parent::bind($result);
		return (!is_null($result));
	}
	
	/**
	 * Bind all private date before we pass it to Joomla
	 */	 	
	public function bind($data)
	{
		foreach($data as $key => $val){
			// Only set the property if they are part of this class
			// This helps in development where new table column might be
			// in the db, but the code might refer to the old table structure
			if(property_exists( $this, $key )){
				$this->$key = $val;
			}
		}
		return parent::bind($data);
	}
	
	/**
	 * Return the difference (in days) from theis activity and today
	 */	 	
	public function getDayDiff()
	{
		return $this->_daydiff;
	}
	
	/**
	 * Return CLocation object,
	 * for now, just return the address	 
	 */	 	
	public function getLocation()
	{
		if(!empty($this->location)){
			// If only valid location needed, we should check with CMapping library
			return $this->location;
		}
		
		return '';
	}
	
	/**
	 * Return true if 'like' system is allowed in this activity
	 */
	public function allowLike()
	{
		// A guest naturally cannot like
		$my = CFactory::getUser();
		if($my->id == 0){return false;}

		return (!empty($this->like_id) && !empty($this->like_type));
	}
	
	/**
	 * Return current like count
	 */
	public function getLikeCount()
	{
		// explode on empty string still gives us 1 array item
		$likes = trim($this->_likes, ', ');
		if(empty($likes)){
			return 0;
		}
		$likes = explode(',', $likes);
		return count($likes);
	}
	
	/**
	 * Return true if comment is allowed here
	 * 
	 * If the comment_id and comment_type is specified, we can assume the caommenting is
	 * allowed	 	 
	 */	 	
	public function allowComment()
	{
//		// A guest naturally cannot comment
//		$my = CFactory::getUser();
//		if($my->id == 0){return false;}

		return (!empty($this->comment_id) && !empty($this->comment_type));
	}
	
	/**
	 * Return number of comments for this stream
	 */
	public function getCommentCount()
	{
		return $this->_comment_count;
	}
	
	/**
	 *
	 */
	public function getLastComment()
	{
		//return $this->_comment_last;
		CFactory::load('libraries', 'wall');
		$wall = new stdClass();
		$wall->id = $this->_comment_last_id;
		$wall->date = $this->_comment_date;
		$wall->post_by = $this->_comment_last_by;
		$wall->comment = $this->_comment_last;

		return CWall::formatComment($wall);
	}
	
	/**
	 * Return the id of the last replier
	 */
	public function getLastCommentBy($wall)
	{
		return $this->_comment_last_by;
	}	 	
	
	
	/**
	 * Store the data
	 */
	public function store()
	{
		CFactory::load('helpers','time');
		$today = CTimeHelper::getDate();
		$this->created = $today->toMySQL();
		
		// If this is a new stream, we need to trigger an event
		if(is_null($this->id))
		{
			// Trigger for onBeforeStreamCreate event.
			CFactory::load( 'libraries' , 'apps' );
			$appsLib	=& CAppPlugins::getInstance();
			$appsLib->loadApplications();		
			
			$params		= array();
			$params[]	= &$this;
			
			$result			= $appsLib->triggerEvent( 'onBeforeStreamCreate' , $params);
	
			if( in_array( true , $result ) || empty($result) )
			{
				return parent::store();
			} 
			// A plugin disallow stream insert
			return false;
		}
		
		return parent::store();
	}
	
	/**
	 * Deletes specific activity from the system.
	 * 
	 * @param	string	$app	Unique application string
	 */
	public function delete( $app )
	{
		// Trigger for onBeforeStreamDelete event.
		CFactory::load( 'libraries' , 'apps' );
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();		
		
		$params		= array();
		$params[]	= &$this;
		
		$result			= $appsLib->triggerEvent( 'onBeforeStreamDelete' , $params);

		if( in_array( true , $result ) || empty($result) )
		{
		    if( $this->cid!= 0 && strpos($this->title,'{multiple}') ){
			// Delete related activities
			$db		= JFactory::getDBO();
			$query	= 'DELETE FROM ' . $db->nameQuote( '#__community_activities' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'app' ) . '=' . $db->Quote( $app ) . ' '
					. 'AND ' . $db->nameQuote( 'cid' ) . '=' . $db->Quote( $this->cid );
			$db->setQuery( $query );
			$db->Query();
		    }
			// Delete the current activity
			parent::delete();

			return true;
		} 
		// A plugin disallow stream insert
		return false;
	}
	/**
	 * Return 1 if user already liked this activity
	 * 
	 */	 	
	
	public function userLiked($userId){
		// Check if user already like
		$likesInArray	=   explode( ',', trim( $this->_likes, ',' ));
		
		if( in_array( $userId, $likesInArray ) )
		{
			// Return 1, the user is liked
			return COMMUNITY_LIKE;
		}
		// Return -1 as neutral
		return COMMUNITY_UNLIKE;	
	}	
}