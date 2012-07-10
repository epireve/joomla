<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Groups
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once ( JPATH_ROOT .DS.'components'.DS.'com_community'.DS.'models'.DS.'models.php');

// Deprecated since 1.8.x to support older modules / plugins
CFactory::load( 'tables' , 'event' );
CFactory::load( 'tables' , 'eventcategory' );
CFactory::load( 'tables' , 'eventmembers' );
CFactory::load( 'helpers' , 'event' );

jimport( 'joomla.utilities.date' );
class CommunityModelEvents extends JCCModel
implements CGeolocationSearchInterface , CNotificationsInterface
{
	/**
	 * Configuration data
	 *
	 * @var object	JPagination object
	 **/
	var $_pagination	= '';

	/**
	 * Configuration data
	 *
	 * @var object	JPagination object
	 **/
	var $total			= '';

	/**
	 * member count data
	 *
	 * @var int
	 **/
	var $membersCount	= array();

	/**
	 * Constructor
	 */
	public function CommunityModelEvents()
	{
		parent::JCCModel();

		$mainframe = JFactory::getApplication();

		// Get pagination request variables
 	 	$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
		$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Method to retrieve total events for a specific group
	 * 
	 * @param	int		$groupId	The unique group id.
	 * @return	array	$result		An array of result.
	 **/	 	 	 	 	
	public function getTotalGroupEvents( $groupId )
	{
		CFactory::load( 'helpers' , 'event' );
		$db		=& $this->getDBO();
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_events' ) . ' WHERE '
				. $db->nameQuote( 'type' ) . '=' . $db->Quote( CEventHelper::GROUP_TYPE ) . ' AND '
				. $db->nameQuote( 'contentid' ) . '=' . $db->Quote( $groupId );
		$db->setQuery( $query );
		$result	= $db->loadResult();

		return $result;
	}
	
	/**
	 * Method to retrieve events for a specific group
	 * 
	 * @param	int		$groupId	The unique group id.
	 * @return	array	$result		An array of result.
	 **/	 	 	 	 	
	public function getGroupEvents( $groupId , $limit = 0 )
	{
		CFactory::load( 'helpers' , 'event' );
		$db		=& $this->getDBO();
		
		$pastDate = CTimeHelper::getLocaleDate();
                
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_events' ) . ' WHERE '
				. $db->nameQuote( 'type' ) . '=' . $db->Quote( CEventHelper::GROUP_TYPE ) . ' AND '
				. $db->nameQuote( 'contentid' ) . '=' . $db->Quote( $groupId ) . ' '
                                . ' AND ' . $db->nameQuote('enddate').' > ' . $db->Quote( $pastDate->toMySQL(true) ) . ' '
				. 'ORDER BY ' . $db->nameQuote( 'startdate' ) . ' ASC ';

		if( $limit != 0 )
		{
			$query	.= 'LIMIT 0,' . $limit;
		}
		
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		//update the event member numbers to exclude the blocked ones
		if(!empty($result)){
			foreach($result as $k => $r){
				$query = "SELECT COUNT(*)
						FROM #__community_events_members a
						JOIN #__users b ON a.memberid=b.id
						WHERE status=1 AND b.block=0 AND eventid=".$db->Quote($r->id);
				$db->setQuery( $query );
				$result[$k]->confirmedcount = $db->loadResult();
			}
		}

		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_events' ) . ' WHERE '
				. $db->nameQuote( 'type' ) . '=' . $db->Quote( CEventHelper::GROUP_TYPE ) . ' AND '
				. $db->nameQuote( 'contentid' ) . '=' . $db->Quote( $groupId );

		$db->setQuery( $query );		
		$total	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		if( empty($this->_pagination) )
		{
			$limit		= $this->getState('limit');
			$limitstart = $this->getState('limitstart');
			jimport('joomla.html.pagination');

			$this->_pagination	= new JPagination( $total, $limitstart, $limit );
		}
				
		return $result;
	}
	
	/**
	 * Returns an object of events which the user has registered.
	 *
	 * @access	public
	 * @param	string 	User's id.
	 * @param	string 	sorting criteria.
	 * @returns array  An objects of event fields.
	 */
	public function getEvents( $categoryId = null, $userId = null , $sorting = null, $search = null, $hideOldEvent = true, $showOnlyOldEvent = false, $pending = null, $advance = null , $type = CEventHelper::ALL_TYPES , $contentid = 0 , $limit = null )
	{
		$db	    =&	$this->getDBO();
		$join	    =	'';
		$extraSQL   =	'';

		if( !empty($userId) )
		{
			$join	    =	'LEFT JOIN ' . $db->nameQuote('#__community_events_members') . ' AS b ON a.' . $db->nameQuote('id').'=b.' . $db->nameQuote('eventid');
			$extraSQL   .= ' AND b.' . $db->nameQuote('memberid').'=' . $db->Quote($userId)
                                       .'AND b.' . $db->nameQuote('status') . '<' . $db->Quote(2);
		}
		
		if( !empty($search) )
		{
			$extraSQL   .= ' AND a.' . $db->nameQuote('title').' LIKE ' . $db->Quote( '%' . $search . '%' );
		}
		
		if( !empty($categoryId) && $categoryId != 0 )
		{
			$extraSQL   .= ' AND a.' . $db->nameQuote('catid').'=' . $db->Quote($categoryId);
		}

		if( !is_null( $pending ) && !empty($userId) )
		{
			$extraSQL   .= ' AND b.' . $db->nameQuote('status').'=' . $db->Quote($pending);
		}

		/* Begin : ADVANCE SEARCH */
		if( !empty($advance) )
		{
			if( !empty($advance['startdate']) )
			{
				$startDate	=   CTimeHelper::getDate( strtotime($advance['startdate']) );

				$extraSQL	.=  ' AND a.' . $db->nameQuote('startdate').' >= ' . $db->Quote( $startDate->toMySQL() );

			}
			else if(!isset($advance['date'])) // If empty, don't select the past event
			{
				$now		=   CTimeHelper::getDate();
				$extraSQL	.=  ' AND a.' . $db->nameQuote('enddate').' >= ' . $db->Quote( $now->toMySQL() );				
			}
			
			if( !empty($advance['date'])){ // to get event within this date
				$between_date		=   date( 'Ymd',strtotime($advance['date']) );
				$extraSQL	.=  ' AND DATE_FORMAT(a.' . $db->nameQuote('startdate').',"%Y%m%d") <= ' . $db->Quote( $between_date ).
								' AND DATE_FORMAT(a.' . $db->nameQuote( 'enddate' ).',"%Y%m%d") >= ' . $db->Quote( $between_date ) ;
				
				$hideOldEvent = false; //show old event as well
			}

			if( !empty($advance['enddate']) )
			{
				$endDate	=   CTimeHelper::getDate( strtotime($advance['enddate']) );

				$extraSQL	.=  ' AND a.' . $db->nameQuote('startdate').' <= ' . $db->Quote( $endDate->toMySQL() );
			}

			/* Begin : SEARCH WITHIN */
			if( !empty($advance['radius']) && !empty($advance['fromlocation']) ){

				$longitude  =	null;
				$latitude   =	null;

				CFactory::load('libraries', 'mapping');
				$data = CMapping::getAddressData( $advance['fromlocation'] );

				if($data){
					if($data->status == 'OK')
					{
						$latitude  = (float) $data->results[0]->geometry->location->lat;
						$longitude = (float) $data->results[0]->geometry->location->lng;
					}
				}

				$now = new JDate();

				$lng_min = $longitude - $advance['radius'] / abs(cos(deg2rad($latitude)) * 69);
				$lng_max = $longitude + $advance['radius'] / abs(cos(deg2rad($latitude)) * 69);
				$lat_min = $latitude - ($advance['radius'] / 69);
				$lat_max = $latitude + ($advance['radius'] / 69);

				$extraSQL   .=	' AND a.' . $db->nameQuote('longitude').' > ' . $db->quote($lng_min)
						. ' AND a.' . $db->nameQuote('longitude').' < ' . $db->quote($lng_max)
						. ' AND a.' . $db->nameQuote('latitude').' > ' . $db->quote($lat_min)
						. ' AND a.' . $db->nameQuote('latitude').' < ' . $db->quote($lat_max);

			}
			/* End : SEARCH WITHIN */
		}
		/* End : ADVANCE SEARCH */

		$limitstart =   $this->getState('limitstart');
		$limit	    =   $limit === null ? $this->getState('limit') : $limit;

		if( $type != CEventHelper::ALL_TYPES )
		{
			$extraSQL   .=  ' AND a.' . $db->nameQuote('type').'=' . $db->Quote( $type );
			$extraSQL   .=  ' AND a.' . $db->nameQuote('contentid').'=' . $contentid;
		}

		if( $type == CEventHelper::GROUP_TYPE || $type == CEventHelper::ALL_TYPES )
		{
			// @rule: Respect group privacy
			$join		.=  ' LEFT JOIN ' . $db->nameQuote('#__community_groups') . ' AS g';
			$join 		.= ' ON g.' . $db->nameQuote('id').' = a.' . $db->nameQuote('contentid');
			
			if( $type != CEventHelper::GROUP_TYPE )
			{
				$extraSQL	.= ' AND (g.' . $db->nameQuote('approvals').' = ' . $db->Quote('0').' OR g.' . $db->nameQuote('approvals').' IS NULL';
				
				if( !empty($userId ) )
				{
					$extraSQL	.= ' OR b.' . $db->nameQuote('memberid').'=' . $db->Quote( $userId );
				}
				$extraSQL	.= ')';
			}
		}

		$orderBy    =	'';
		$total	    =	0;

		switch($sorting)
		{			
			case 'latest':
				if( empty($orderBy) )
					$orderBy	= ' ORDER BY a.' . $db->nameQuote('created').' DESC';
				break;
			case 'alphabetical':
				if( empty($orderBy) )
					$orderBy	= ' ORDER BY a.' . $db->nameQuote('title').' ASC';
				break;
			case 'startdate':
				if( empty($orderBy) )
					$orderBy	= ' ORDER BY a.startdate ASC';
				break;
			default:
				$orderBy	= ' ORDER BY a.' . $db->nameQuote('startdate').' ASC';
				break;
		}
		
		$now = new JDate();

		CFactory::load( 'helpers' , 'time');
		$pastDate = CTimeHelper::getLocaleDate();
		
		if( $hideOldEvent )
		{
			$extraSQL .= ' AND a.' . $db->nameQuote('enddate').' > ' . $db->Quote( $pastDate->toMySQL(true) );
		}

		if( $showOnlyOldEvent )
		{	
			$extraSQL .= ' AND a.' . $db->nameQuote('enddate').' < ' . $db->Quote( $pastDate->toMySQL(true) );
		}
		
		$limit	= empty($limit) ? 0 : $limit;
				
		$query	= 'SELECT DISTINCT a.* FROM '
				. $db->nameQuote('#__community_events') . ' AS a '
				. $join
				. 'WHERE a.' . $db->nameQuote('published').'=' . $db->Quote( '1' )
				. $extraSQL
				. $orderBy
				. ' LIMIT ' . $limitstart . ', ' . $limit;

		$db->setQuery( $query );		
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		$query	= 'SELECT COUNT(DISTINCT(a.`id`)) FROM '
				. $db->nameQuote('#__community_events') . ' AS a '
				. $join
				. 'WHERE a.' . $db->nameQuote('published').'=' . $db->Quote( '1' )
				. $extraSQL;

		$db->setQuery( $query );
		$this->total	= $db->loadResult();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		$query	= 'SELECT COUNT(DISTINCT(a.' . $db->nameQuote('id').')) FROM ' . $db->nameQuote('#__community_events') . ' AS a '
				. $join
				. 'WHERE a.' . $db->nameQuote('published').'=' . $db->Quote( '1' ) . ' '
				. $extraSQL;

		$db->setQuery( $query );
		$total	= $db->loadResult();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');

			$this->_pagination	= new JPagination( $total, $limitstart, $limit );
		}

		return $result;
	}
	
	
	/**
	 * Return an array of ids the user responded to
	 * @param type $userid
	 * @return type 
	 * 
	 */
	public function getEventIds($userId)
	{
		$db		=& $this->getDBO();
		$query		= 'SELECT DISTINCT a.'.$db->nameQuote('id').' FROM ' . $db->nameQuote('#__community_events') . ' AS a '
				. ' LEFT JOIN ' . $db->nameQuote('#__community_events_members') . ' AS b '
				. ' ON a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('eventid')
				. ' WHERE '
				. ' ( '
				. '   b.'.$db->nameQuote('status').'=' . $db->Quote( '1' )
				. '		OR '
				. '	  b.'.$db->nameQuote('status').'=' . $db->Quote( '2' )
				. '		OR '
				. '	  b.'.$db->nameQuote('status').'=' . $db->Quote( '3' )
				. ' ) '
				. ' AND b.memberid=' . $db->Quote($userId);
	
		$db->setQuery( $query );
		$eventsid		= $db->loadResultArray();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		return $eventsid;

	}

	/**
	 * Return the number of groups count for specific user
	 **/
	public function getEventsCount( $userId )
	{
		// guest obviously has no group
		if($userId == 0)
			return 0;

		$now	=&  JFactory::getDate();
		$db	=&  $this->getDBO();
		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote( '#__community_events_members' ) . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote( '#__community_events' ) . ' AS b '
				. ' ON b.' . $db->nameQuote('id').'=a.' . $db->nameQuote('eventid')
				. ' AND b.' . $db->nameQuote('enddate').' > ' . $db->Quote( $now->toMySQL() )
				. ' WHERE a.' . $db->nameQuote('memberid').'=' . $db->Quote( $userId ) . ' '
				. ' AND a.' . $db->nameQuote('status').' IN (' . $db->Quote( COMMUNITY_EVENT_STATUS_ATTEND ) . ',' . $db->Quote( COMMUNITY_EVENT_STATUS_INVITED ) .')';
				
		$db->setQuery( $query );
		$count	= $db->loadResult();

		return $count;
	}
	
	/**
	 * Return the number of groups cretion count for specific user
	 **/
	public function getEventsCreationCount( $userId )
	{
		// guest obviously has no events
		if($userId == 0)
			return 0;

		$db		=& $this->getDBO();

		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote( '#__community_events' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'creator' ) . '=' . $db->Quote( $userId );
		$db->setQuery( $query );

		$count	= $db->loadResult();

		return $count;
	}

	/**
	 * Returns the count of the members of a specific group
	 *
	 * @access	public
	 * @param	string 	Group's id.
	 * @return	int	Count of members
	 */
	public function getMembersCount( $id )
	{
		$db	=& $this->getDBO();

		if( !isset($this->membersCount[$id] ) )
		{
			$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote('#__community_events_members') . ' '
					. 'WHERE ' . $db->nameQuote('eventid').'=' . $db->Quote( $id ) . ' '
					. 'AND ' . $db->nameQuote( 'status' ) . ' IN ('.COMMUNITY_EVENT_STATUS_INVITED.','.COMMUNITY_EVENT_STATUS_ATTEND.','.COMMUNITY_EVENT_STATUS_MAYBE.')';

			$db->setQuery( $query );
			$this->membersCount[$id]	= $db->loadResult();

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
			}
		}
		return $this->membersCount[$id];
	}

	/**
	 * Loads the categories
	 *
	 * @access	public
	 * @returns Array  An array of categories object
	 */
	public function getCategories( $type = CEventHelper::PROFILE_TYPE, $catId = COMMUNITY_ALL_CATEGORIES )
	{
		$db		=& $this->getDBO();
		$where	= '';
		$join	= '';
		
		if( $catId !== COMMUNITY_ALL_CATEGORIES )
		{
			if( $catId === COMMUNITY_NO_PARENT )
			{
				$where	=   'WHERE a.' . $db->nameQuote('parent').'=' . $db->Quote( COMMUNITY_NO_PARENT ) . ' ';
			}
			else
			{
				$where	=   'WHERE a.' . $db->nameQuote('parent').'=' . $db->Quote( $catId ) . ' ';
			}
		}
		
		if( $type != CEventHelper::ALL_TYPES )
		{
			$where	.= ' AND b.' . $db->nameQuote('type').'=' . $db->Quote( $type ) . ' ';
		}
		else
		{
			// @rule: Respect group privacy
			$join	=  ' LEFT JOIN ' . $db->nameQuote('#__community_groups') . ' AS g';
			$join 	.= ' ON b.' . $db->nameQuote('contentid').' = g.' . $db->nameQuote('id');
			$where  .= ' AND (g.' . $db->nameQuote('approvals').' = ' . $db->Quote(0) .' OR g.' . $db->nameQuote('approvals').' IS NULL) ';
		}

		$now	=   new JDate();
		$query	=   'SELECT a.*, COUNT(b.' . $db->nameQuote('id').') AS count '
			    . 'FROM ' . $db->nameQuote('#__community_events_category') . ' AS a '
			    . ' LEFT OUTER JOIN ' . $db->nameQuote( '#__community_events' ) . ' AS b '
			    . ' ON a.' . $db->nameQuote('id').'=b.' . $db->nameQuote('catid')
			    . ' AND b.' . $db->nameQuote('enddate').' > ' . $db->Quote($now->toMySQL())
			    . ' AND b.' . $db->nameQuote('published').'=' . $db->Quote( '1' )
			    . $join
			    . $where 
			    . 'GROUP BY a.' . $db->nameQuote('id').' ORDER BY a.' . $db->nameQuote('name').' ASC';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}

	/**
	 * Returns the category's group count
	 *
	 * @access  public
	 * @returns Array  An array of categories object
	 * @since   Jomsocial 2.4
	 **/
	function getCategoriesCount()
	{
		$db	 =& $this->getDBO();
		$now =  new JDate();

		$query = "SELECT c.id, c.parent, c.name, count(e.id) AS total, c.description
				  FROM " . $db->nameQuote('#__community_events_category') . " AS c
				  LEFT JOIN " . $db->nameQuote('#__community_events'). " AS e ON e.catid = c.id
							AND e." . $db->nameQuote('published') . "=" . $db->Quote( '1' ) . "
							AND	e." . $db->nameQuote('enddate')." > " . $db->Quote($now->toMySQL()) . "
				  GROUP BY c.id
				  ORDER BY c.name";

		$db->setQuery( $query );
		$result	= $db->loadObjectList('id');

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}

	/**
	 * Returns the category name of the specific category
	 *
	 * @access public
	 * @param	string Category Id
	 * @returns string	Category name
	 **/
	public function getCategoryName( $categoryId )
	{
		CError::assert($categoryId, '', '!empty', __FILE__ , __LINE__ );
		$db		=& $this->getDBO();

		$query	= 'SELECT ' . $db->nameQuote('name') . ' '
				. 'FROM ' . $db->nameQuote('#__community_events_category') . ' '
				. 'WHERE ' . $db->nameQuote('id') . '=' . $db->Quote( $categoryId );
		$db->setQuery( $query );

		$result	= $db->loadResult();

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		CError::assert( $result , '', '!empty', __FILE__ , __LINE__ );
		return $result;
	}

	/**
	 * Check if the given group name exist.
	 * if id is specified, only search for those NOT within $id
	 */
	public function isEventExist( $title, $location, $startdate, $enddate, $id=0 ) {
		$db		=& $this->getDBO();

		$starttime	=   CTimeHelper::getDate( strtotime( $startdate ) );
		$endtime	=   CTimeHelper::getDate( strtotime( $enddate ) );

		$strSQL	= 'SELECT count(*) FROM ' . $db->nameQuote('#__community_events')
			. ' WHERE ' . $db->nameQuote('title') . ' = ' . $db->Quote( $title )
			. ' AND ' . $db->nameQuote('location') . ' = ' . $db->Quote( $location )
			. ' AND ' . $db->nameQuote('startdate') . ' = ' . $db->Quote( $starttime->toMySQL() )
			. ' AND ' . $db->nameQuote('enddate') . ' = ' . $db->Quote( $endtime->toMySQL() )
			. ' AND ' . $db->nameQuote('id') . ' != ' . $db->Quote( $id ) ;


		$db->setQuery( $strSQL );
		$result	= $db->loadResult();

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}

	/**
	 * Delete group's wall
	 *
	 * param	string	id The id of the group.
	 *
	 **/
	public function deleteGroupWall($gid)
	{
		$db = JFactory::getDBO();

		$sql = 'DELETE FROM '.$db->nameQuote("#__community_wall")."
				WHERE
						".$db->nameQuote("contentid")." = ".$db->quote($gid)." AND
						".$db->nameQuote("type")." = ".$db->quote('groups');
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

		return true;
	}
	
	/* Implement interfaces */
	
	/**
	 * caller should verify that the address is valid
	 */	 	
	public function searchWithin($address, $distance)
	{
		$db = JFactory::getDBO();
		
		$longitude = null;
		$latitude = null;
		
		CFactory::load('libraries', 'mapping');
		$data = CMapping::getAddressData($address);
		
		if($data){
			if($data->status == 'OK')
			{
				$latitude  = (float) $data->results[0]->geometry->location->lat;
				$longitude = (float) $data->results[0]->geometry->location->lng; 
			}
		}
		
		if(is_null($latitude) || is_null($longitude)){
			return $null;
		}
		
		/*
		 * code from 
		 * http://blog.fedecarg.com/2009/02/08/geo-proximity-search-the-haversine-equation/
		 * 
		 */	
		
		$radius = 20; // in miles
		
		$lng_min = $longitude - $radius / abs(cos(deg2rad($latitude)) * 69);
		$lng_max = $longitude + $radius / abs(cos(deg2rad($latitude)) * 69);
		$lat_min = $latitude - ($radius / 69);
		$lat_max = $latitude + ($radius / 69);
		
		$now = new JDate();
		$sql = "SELECT *

				FROM
						".$db->nameQuote("#__community_events")."
				WHERE
						".$db->nameQuote("longitude")." > ".$db->quote($lng_min)." AND
						".$db->nameQuote("longitude")." < ".$db->quote($lng_max)." AND
						".$db->nameQuote("latitude")." > ".$db->quote($lat_min)." AND
						".$db->nameQuote("latitude")." < ".$db->quote($lat_max)." AND
						".$db->nameQuote("enddate")." > ".$db->quote($now->toMySQL());
	
		$db->setQuery($sql);
		$results = $db->loadObjectList();
		
		return $results;
	}

	/**
	 *	Get the pending invitations
	 *
	 */
	public function getPending($userId){
		if($userId == 0){
			return null;
		}

		$limit		= $this->getState('limit');
		$limitstart = $this->getState('limitstart');

		$db		=&	JFactory::getDBO();

		$query	=	'SELECT a.*, b.' . $db->nameQuote('title').', b.' . $db->nameQuote('thumb')
					. ' FROM ' . $db->nameQuote("#__community_events_members") . ' AS a, '
					. $db->nameQuote("#__community_events") . ' AS b'
					. ' WHERE a.' . $db->nameQuote('memberid').'=' . $db->Quote($userId)
					. ' AND a.' . $db->nameQuote('eventid').'=b.' . $db->nameQuote('id')
					. ' AND b.' . $db->nameQuote('published').'=' . $db->Quote( 1 )
					. ' AND a.' . $db->nameQuote('status').'=' . $db->Quote( COMMUNITY_EVENT_STATUS_INVITED )					
					. ' AND b.' . $db->nameQuote('enddate').'>= NOW()'
					. ' ORDER BY a.' . $db->nameQuote('id').' DESC'
					. " LIMIT {$limitstart}, {$limit}";
					
		$db->setQuery($query);

		if( $db->getErrorNum() ){
			JError::raiseError(500, $db->stderr());
		}

		$result = $db->loadObjectList();
		
		return $result;
	}

	/**
	 * Check if I was invited and if yes return true
	 * If Event Id is provided, will return the invitation informations
	 *
	 */
	public function isInvitedMe($invitationId=0, $userId=0, $eventId=0){
		$db		=&	$this->getDBO();

		if( $eventId == 0 )
		{
		    $query	=   "SELECT COUNT(*) FROM "
				    . $db->nameQuote("#__community_events_members")
				    . " WHERE " . $db->nameQuote("id") . "=" . $db->Quote($invitationId)
				    . " AND " . $db->nameQuote("memberid") . "=" . $db->Quote($userId)
				    . " AND " . $db->nameQuote("status") . "=" . $db->Quote(COMMUNITY_EVENT_STATUS_INVITED);

		    $db->setQuery($query);

		    $status = ($db->loadResult() > 0) ? true : false;

		    if ($db->getErrorNum()){
			    JError::raiseError(500, $db->stderr());
		    }

		    return $status;
		}
		else
		{
		    $query	=   "SELECT * FROM "
				    . $db->nameQuote("#__community_events_members")
				    . " WHERE " . $db->nameQuote("memberid") . "=" . $db->Quote($userId)
				    . " AND " . $db->nameQuote("eventid") . "=" . $db->Quote($eventId)
				    . " AND " . $db->nameQuote("status") . "=" . $db->Quote(COMMUNITY_EVENT_STATUS_INVITED)
				    . " AND " . $db->nameQuote("invited_by") . "!=" . $db->Quote($userId)
				    . " AND " . $db->nameQuote("invited_by") . "!=" . $db->Quote(0);

		    $db->setQuery($query);

		    $result = $db->loadObjectList();

		    return $result;
		}
	}

	/**
	 * Return the count of the user's friend of a specific event
	 */
	public function getFriendsCount( $userid, $eventid )
	{
		$db	=& $this->getDBO();

		$query	=   'SELECT COUNT(DISTINCT(a.' . $db->nameQuote('connect_to').')) AS id  FROM ' . $db->nameQuote('#__community_connection') . ' AS a '
			    . ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
			    . ' INNER JOIN ' . $db->nameQuote( '#__community_events_members' ) . ' AS c '
			    . ' ON a.' . $db->nameQuote('connect_from').'=' . $db->Quote( $userid )
			    . ' AND a.' . $db->nameQuote('connect_to').'=b.' . $db->nameQuote('id')
			    . ' AND c.' . $db->nameQuote('eventid').'=' . $db->Quote( $eventid ) . ' '
			    . ' AND a.' . $db->nameQuote('connect_to').'=c.' . $db->nameQuote('memberid')
			    . ' AND a.' . $db->nameQuote('status').'=' . $db->Quote( '1' ) . ' '
			    . ' AND c.' . $db->nameQuote('status').'=' . $db->Quote( '1' );

		$db->setQuery( $query );

		$total = $db->loadResult();

		return $total;
	}

	public function getInviteListByName($namePrefix ,$userid, $cid, $limitstart = 0, $limit = 8){
		$db	=& $this->getDBO();

		$andName = '';
		$config = CFactory::getConfig();
		$nameField = $config->getString('displayname');
		if(!empty($namePrefix)){
			$andName	= ' AND b.' . $db->nameQuote( $nameField ) . ' LIKE ' . $db->Quote( '%'.$namePrefix.'%' ) ;
		}
		$query	=   'SELECT DISTINCT(a.'.$db->nameQuote('connect_to').') AS id  FROM ' . $db->nameQuote('#__community_connection') . ' AS a '
			    . ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
			    . ' ON a.'.$db->nameQuote('connect_from').'=' . $db->Quote( $userid )
			    . ' AND a.'.$db->nameQuote('connect_to').'=b.'.$db->nameQuote('id')
			    . ' AND a.'.$db->nameQuote('status').'=' . $db->Quote( '1' )
				. ' AND b.'.$db->nameQuote('block').'=' .$db->Quote('0') 
				. ' WHERE NOT EXISTS ( SELECT d.'.$db->nameQuote('blocked_userid') . ' as id'
									. ' FROM '.$db->nameQuote('#__community_blocklist') . ' AS d  '
									. ' WHERE d.'.$db->nameQuote('userid').' = '.$db->Quote($userid)
									. ' AND d.'.$db->nameQuote('blocked_userid').' = a.'.$db->nameQuote('connect_to').')'
				. ' AND NOT EXISTS (SELECT e.'.$db->nameQuote('memberid') . ' as id'
									. ' FROM '.$db->nameQuote('#__community_events_members') . ' AS e  ' 
									. ' WHERE e.'.$db->nameQuote('eventid').' = '.$db->Quote($cid)
									. ' AND e.'.$db->nameQuote('status').' = '.$db->Quote(COMMUNITY_EVENT_STATUS_ATTEND)
									. ' AND e.'.$db->nameQuote('memberid').' = a.'.$db->nameQuote('connect_to')
				.')' 
				. $andName
				. ' ORDER BY b.' . $db->nameQuote($nameField)
				. ' LIMIT ' . $limitstart.','.$limit
				;
		$db->setQuery( $query );
		$friends = $db->loadResultArray();
		
		//calculate total		
		$query	=   'SELECT COUNT(DISTINCT(a.'.$db->nameQuote('connect_to').'))  FROM ' . $db->nameQuote('#__community_connection') . ' AS a '
			    . ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
			    . ' ON a.'.$db->nameQuote('connect_from').'=' . $db->Quote( $userid )
			    . ' AND a.'.$db->nameQuote('connect_to').'=b.'.$db->nameQuote('id')
			    . ' AND a.'.$db->nameQuote('status').'=' . $db->Quote( '1' )
				. ' AND b.'.$db->nameQuote('block').'=' .$db->Quote('0') 
				. ' WHERE NOT EXISTS ( SELECT d.'.$db->nameQuote('blocked_userid') . ' as id'
									. ' FROM '.$db->nameQuote('#__community_blocklist') . ' AS d  '
									. ' WHERE d.'.$db->nameQuote('userid').' = '.$db->Quote($userid)
									. ' AND d.'.$db->nameQuote('blocked_userid').' = a.'.$db->nameQuote('connect_to').')'
				. ' AND NOT EXISTS (SELECT e.'.$db->nameQuote('memberid') . ' as id'
									. ' FROM '.$db->nameQuote('#__community_events_members') . ' AS e  ' 
									. ' WHERE e.'.$db->nameQuote('eventid').' = '.$db->Quote($cid)
									. ' AND e.'.$db->nameQuote('status').' = '.$db->Quote(COMMUNITY_EVENT_STATUS_ATTEND)
									. ' AND e.'.$db->nameQuote('memberid').' = a.'.$db->nameQuote('connect_to')
				.')' 
				. $andName;
		$db->setQuery( $query );
		$this->total	=  $db->loadResult();
		
		return $friends;	
	}	
	/**
	 * Return the title of the event id
	 */
	public function getTitle( $eventid )
	{
		$db	=& $this->getDBO();

		$query	=   'SELECT ' . $db->nameQuote('title').' FROM ' . $db->nameQuote('#__community_events')
					. " WHERE " . $db->nameQuote("id") . "=" . $db->Quote($eventid);

		$db->setQuery( $query );

		$title = $db->loadResult();

		return $title;
	}

	/**
	 * Count total pending event invitations.
	 *
	 **/
	public function countPending($id){
		$db = & $this->getDBO();

		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote( '#__community_events_members' ) . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote( '#__community_events' ) . ' AS b '
				. ' ON b.' . $db->nameQuote('id').'=a.' . $db->nameQuote('eventid')
				. ' AND b.' . $db->nameQuote('published').'=' . $db->Quote( 1 )
				. ' WHERE a.' . $db->nameQuote('memberid').'=' . $db->Quote( $id )
				. ' AND a.' . $db->nameQuote('status').'=' . $db->Quote( COMMUNITY_EVENT_STATUS_INVITED ) . ' '
				. ' AND b.' . $db->nameQuote('enddate').'>= NOW()'
				. ' ORDER BY a.' . $db->nameQuote('id').' DESC';

		$db->setQuery($query);
		
		if ($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}

		return $db->loadResult();
	}
	
	/*
	 * get days with event within the month
	 *
	 */
	 public function getMonthlyEvents($month,$year){
		$db	=& $this->getDBO();
		
		$query = "	SELECT DISTINCT DATE_FORMAT( ". $db->nameQuote('startdate').", '%d' ) AS date
					FROM #__community_events
					WHERE DATE_FORMAT( ". $db->nameQuote('startdate').", '%Y%c' ) = ".$db->Quote( $year.$month )."";
		
		$db->setQuery($query);
		
		if ($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}

		return $db->loadObjectList();
	 }

    /**
     * @deprecated Since 2.0
     */
	public function getThumbAvatar($id, $thumb)
	{
		CFactory::load('helpers', 'url');
		$thumb	= CUrlHelper::avatarURI($thumb, 'event_thumb.png');
		
		return $thumb;
	}

	/**
	 * Return events search total
	 *
	 */
	public function getEventsSearchTotal()
	{
		return $this->total;
	}

	/**
	 * Returns a list of pending event invites
	 *
	 * @param	int	$userId	The number of event invites to lookup for this user.
	 * 
	 * @return	int Total number of invites		 		 		 
	 **/	 	
	public function getTotalNotifications( $userId )
	{
		return (int) $this->countPending( $userId );
	}
}

