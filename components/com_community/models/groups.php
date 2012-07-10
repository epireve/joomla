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
CFactory::load( 'tables' , 'group' );
CFactory::load( 'tables' , 'bulletin' );
CFactory::load( 'tables' , 'groupinvite' );
CFactory::load( 'tables' , 'groupmembers' );
CFactory::load( 'tables' , 'discussion' );
CFactory::load( 'tables' , 'category' );

class CommunityModelGroups extends JCCModel
implements CLimitsInterface, CNotificationsInterface
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
	public function CommunityModelGroups()
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
	 	 	
		// Get cache object.
 	 	$oCache = CCache::inject($this);
 	 	$oCache->addMethod('setImage', CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_GROUPS));
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
	 * Deprecated since 1.8, use $groupd->updateStats()->store();
	 */
	public function substractMembersCount( $groupId )
	{
		$this->addWallCount($groupId);
	}
	
	/**
	 * Deprecated since 1.8, use $groupd->updateStats()->store();
	 */
	public function addDiscussCount( $groupId )
	{
		$this->addWallCount($groupId);
	}
	
	/**
	 * Deprecated since 1.8, use $groupd->updateStats()->store();
	 */
	public function substractDiscussCount( $groupId )
	{
		$this->addWallCount($groupId);
	}

	/**
	 * Retrieves the most active group throughout the site.
	 * @param   none
	 *
	 * @return  CTableGroup The most active group table object.
	 **/
	public function getMostActiveGroup()
	{
		$db		=& $this->getDBO();

		$query	= 'SELECT '.$db->nameQuote('cid').' FROM '.$db->nameQuote('#__community_activities')
				. ' WHERE '.$db->nameQuote('app').'=' . $db->Quote( 'groups' )
				. ' GROUP BY '.$db->nameQuote('cid')
				. ' ORDER BY COUNT(1) DESC '
				. ' LIMIT 1';
		$db->setQuery( $query );

		$id		= $db->loadResult();

		$group	=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $id );

		return $group;
	}
	
	public function getGroupInvites( $userId , $sorting = null )
	{
		$db			=& $this->getDBO();
		$extraSQL	= ' AND a.userid=' . $db->Quote($userId);
		$orderBy	= '';
		$limit			= $this->getState('limit');
		$limitstart 	= $this->getState('limitstart');
		$total			= 0;
		
		
		switch($sorting)
		{
			
			case 'mostmembers':
				// Get the groups that this user is assigned to
				$query		= 'SELECT a.'.$db->nameQuote('groupid').' FROM ' . $db->nameQuote('#__community_groups_invite') . ' AS a '
							. ' LEFT JOIN ' . $db->nameQuote('#__community_groups_members') . ' AS b '
							. ' ON a.'.$db->nameQuote('groupid').'=b.'.$db->nameQuote('groupid')
							. ' WHERE b.'.$db->nameQuote('approved').'=' . $db->Quote( '1' )
							. $extraSQL; 

				$db->setQuery( $query );
				$groupsid		= $db->loadResultArray();
				
				if($db->getErrorNum())
				{
					JError::raiseError( 500, $db->stderr());
				}
				
				if( $groupsid )
				{
					$groupsid		= implode( ',' , $groupsid );
	
					$query			= 'SELECT a.* '
									. ' FROM ' . $db->nameQuote('#__community_groups_invite') . ' AS a '
									. ' INNER JOIN '.$db->nameQuote('#__community_groups').' AS b '
									. ' ON a.'.$db->nameQuote('groupid').'=b.'.$db->nameQuote('id')
									. ' WHERE a.'.$db->nameQuote('groupid').' IN (' . $groupsid . ') '
									. ' ORDER BY b.'.$db->nameQuote('membercount').' DESC '
									. ' LIMIT ' . $limitstart . ',' . $limit;	
				}
				break;
			case 'mostdiscussed':
				if( empty($orderBy) )
					$orderBy	= ' ORDER BY b.'.$db->nameQuote('discusscount').' DESC ';
			case 'mostwall':
				if( empty($orderBy) )
					$orderBy	= ' ORDER BY b.'.$db->nameQuote('wallcount').' DESC ';
			case 'alphabetical':
				if( empty($orderBy) )
					$orderBy	= 'ORDER BY b.'.$db->nameQuote('name').' ASC ';
			case 'mostactive':
				//@todo: Add sql queries for most active group
			
			default:
				if( empty($orderBy) )
					$orderBy	= ' ORDER BY b.'.$db->nameQuote('created').' DESC ';

				$query	= 'SELECT distinct a.* FROM '
						. $db->nameQuote('#__community_groups_invite') . ' AS a '
						. ' INNER JOIN ' . $db->nameQuote( '#__community_groups' ) . ' AS b ON a.'.$db->nameQuote('groupid').'=b.'.$db->nameQuote('id')
						. ' INNER JOIN ' . $db->nameQuote('#__community_groups_members') . ' AS c ON a.'.$db->nameQuote('groupid').'=c.'.$db->nameQuote('groupid')
						. ' AND c.'.$db->nameQuote('approved').'=' . $db->Quote( '1' )
						. ' AND b.'.$db->nameQuote('published').'=' . $db->Quote( '1' ) . ' '
						. $extraSQL
						. $orderBy
						. 'LIMIT ' . $limitstart . ',' . $limit;
				break;
		}
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		$query	= 'SELECT COUNT(distinct b.'.$db->nameQuote('id').') FROM ' . $db->nameQuote('#__community_groups_invite') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote( '#__community_groups' ) . ' AS b '
				. ' ON a.'.$db->nameQuote('groupid').'=b.'.$db->nameQuote('id')
				. ' INNER JOIN ' . $db->nameQuote('#__community_groups_members') . ' AS c '
				. ' ON a.'.$db->nameQuote('groupid').'=c.'.$db->nameQuote('groupid')
				. ' WHERE b.'.$db->nameQuote('published').'=' . $db->Quote( '1' )
				. ' AND c.'.$db->nameQuote('approved').'=' . $db->Quote( '1' )
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
			
			$this->_pagination	= new JPagination( $total , $limitstart , $limit );
		}
		
		return $result;
	}
	
	/**
	 * Return an array of ids the user belong to
	 * @param type $userid
	 * @return type 
	 * 
	 */
	public function getGroupIds($userId)
	{
		$db		=& $this->getDBO();
		$query		= 'SELECT DISTINCT a.'.$db->nameQuote('id').' FROM ' . $db->nameQuote('#__community_groups') . ' AS a '
				. ' LEFT JOIN ' . $db->nameQuote('#__community_groups_members') . ' AS b '
				. ' ON a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('groupid')
				. ' WHERE b.'.$db->nameQuote('approved').'=' . $db->Quote( '1' )
				. ' AND b.memberid=' . $db->Quote($userId);
	
		$db->setQuery( $query );
		$groupsid		= $db->loadResultArray();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		return $groupsid;

	}
	
	/**
	 * Returns an object of groups which the user has registered.
	 * 	 	
	 * @access	public
	 * @param	string 	User's id.
	 * @returns array  An objects of custom fields.	 
	 * @todo: re-order with most active group stays on top	 
	 */	 
	public function getGroups( $userId = null , $sorting = null , $useLimit = true )
	{
		$db		=& $this->getDBO();

		$extraSQL	= '';
		
		
		if( !is_null($userId) )
		{
			$extraSQL	= ' AND b.memberid=' . $db->Quote($userId);
		}

		$orderBy	= '';
		
		$limitSQL = '';
		$total		= 0;
		$limit		= $this->getState('limit');
		$limitstart = $this->getState('limitstart');
		if($useLimit){
			$limitSQL	= ' LIMIT ' . $limitstart . ',' . $limit ; 
		}
		
		
		switch($sorting)
		{
			case 'mostmembers':
				// Get the groups that this user is assigned to
				$query		= 'SELECT a.'.$db->nameQuote('id').' FROM ' . $db->nameQuote('#__community_groups') . ' AS a '
							. ' LEFT JOIN ' . $db->nameQuote('#__community_groups_members') . ' AS b '
							. ' ON a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('groupid')
							. ' WHERE b.'.$db->nameQuote('approved').'=' . $db->Quote( '1' )
							. $extraSQL; 

				$db->setQuery( $query );
				$groupsid		= $db->loadResultArray();
				
				if($db->getErrorNum())
				{
					JError::raiseError( 500, $db->stderr());
				}
				
				if( $groupsid )
				{
					$groupsid		= implode( ',' , $groupsid );
	
					$query			= 'SELECT a.* '
									. 'FROM ' . $db->nameQuote('#__community_groups') . ' AS a '
									. ' WHERE a.'.$db->nameQuote('published').'=' . $db->Quote( '1' )
									. ' AND a.'.$db->nameQuote('id').' IN (' . $groupsid . ') '
									. ' ORDER BY a.'.$db->nameQuote('membercount').' DESC '
									. $limitSQL;	
				}
				break;
			case 'mostdiscussed':
				if( empty($orderBy) )
					$orderBy	= ' ORDER BY a.'.$db->nameQuote('discusscount').' DESC ';
			case 'mostwall':
				if( empty($orderBy) )
					$orderBy	= ' ORDER BY a.'.$db->nameQuote('wallcount').' DESC ';
			case 'alphabetical':
				if( empty($orderBy) )
					$orderBy	= 'ORDER BY a.'.$db->nameQuote('name').' ASC ';
			case 'mostactive': 
				//@todo: Add sql queries for most active group
			default:
				if( empty($orderBy) )
					$orderBy	= ' ORDER BY a.created DESC ';

				$query	= 'SELECT a.* FROM '
						. $db->nameQuote('#__community_groups') . ' AS a '
						. ' INNER JOIN ' . $db->nameQuote('#__community_groups_members') . ' AS b '
						. ' ON a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('groupid')
						. ' AND b.'.$db->nameQuote('approved').'=' . $db->Quote( '1' )
						. ' AND a.'.$db->nameQuote('published').'=' . $db->Quote( '1' ) . ' '
						. $extraSQL
						. $orderBy
						. $limitSQL;
				break;
		}
		if ($sorting == 'mostactive')
		{
			$query =  ' SELECT *, ' .$db->nameQuote('cid').', COUNT('.$db->nameQuote('cid').') AS '.$db->nameQuote('count')
			. ' FROM '.$db->nameQuote('#__community_activities').' AS a'
			. ' INNER JOIN	'.$db->nameQuote('#__community_groups').' AS b'
		        . ' ON a.'.$db->nameQuote('cid').' = b.'.$db->nameQuote('id')
			. ' INNER JOIN ' . $db->nameQuote('#__community_groups_members') . ' AS c '
			. ' ON b.'.$db->nameQuote('id').'= c.'.$db->nameQuote('groupid')
			. ' WHERE a.'.$db->nameQuote('app').' = '.$db->quote('groups')
			. ' AND b.'.$db->nameQuote('published').' = '.$db->quote('1')
			. ' AND a.'.$db->nameQuote('archived').' = '.$db->quote('0')
			. ' AND a.'.$db->nameQuote('cid').' != '.$db->quote('0')	
			. ' AND c.'.$db->nameQuote('approved').'=' . $db->Quote( '1' )	
			. ' AND c.memberid=' . $db->Quote($userId)
			. ' GROUP BY a.'.$db->nameQuote('cid')
			. ' ORDER BY '.$db->nameQuote('count').' DESC'
			. $limitSQL;
		}  
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote('#__community_groups') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__community_groups_members') . ' AS b '
				. ' WHERE a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('groupid')
				. ' AND a.'.$db->nameQuote('published').'=' . $db->Quote( '1' ) . ' '
				. ' AND b.'.$db->nameQuote('approved').'=' . $db->Quote( '1' )
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
			
			$this->_pagination	= new JPagination( $total , $limitstart , $limit );
		}
		
		return $result;
	}

	/**
	 * Return the number of groups count for specific user
	 **/	 	
	public function getGroupsCount( $userId )
	{
		// guest obviously has no group
		if($userId == 0)
		{
			return 0;
		}

		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' 
				. $db->nameQuote( '#__community_groups_members' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote( 'approved' ) . '=' . $db->Quote( '1' );
		$db->setQuery( $query );
		$count	= $db->loadResult();

		return $count;
	}

	public function getTotalToday( $userId )
	{	
		$date	= & JFactory::getDate();
		$db		= & JFactory::getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_groups' ) . ' AS a '
				. ' WHERE a.'.$db->nameQuote('ownerid').'=' . $db->Quote( $userId )
				. ' AND TO_DAYS(' . $db->Quote( $date->toMySQL( true ) ) . ') - TO_DAYS( DATE_ADD( a.'.$db->nameQuote('created').' , INTERVAL ' . $date->getOffset() . ' HOUR ) ) = '.$db->Quote(0);
		$db->setQuery( $query );
		
		$count		= $db->loadResult();
		
		return $count;
	}
	/**
	 * Return the number of groups cretion count for specific user
	 **/	 	
	public function getGroupsCreationCount( $userId )
	{
		// guest obviously has no group
		if($userId == 0)
			return 0;
			
		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote( '#__community_groups' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'ownerid' ) . '=' . $db->Quote( $userId );				
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
			$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote('#__community_groups_members') . ' '
					. 'WHERE '.$db->nameQuote('groupid').'=' . $db->Quote( $id ) . ' '
					. 'AND ' . $db->nameQuote( 'approved' ) . '=' . $db->Quote( '1' );
			
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
	 * Return the count of the user's friend of a specific group
	 */
	public function getFriendsCount( $userid, $groupid )
	{
		$db	=& $this->getDBO();

		$query	=   'SELECT COUNT(DISTINCT(a.'.$db->nameQuote('connect_to').')) AS id  FROM ' . $db->nameQuote('#__community_connection') . ' AS a '
			    . ' INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
			    . ' INNER JOIN ' . $db->nameQuote( '#__community_groups_members' ) . ' AS c '
			    . ' ON a.'.$db->nameQuote('connect_from').'=' . $db->Quote( $userid )
			    . ' AND a.'.$db->nameQuote('connect_to').'=b.'.$db->nameQuote('id')
			    . ' AND c.'.$db->nameQuote('groupid').'=' . $db->Quote( $groupid )
			    . ' AND a.'.$db->nameQuote('connect_to').'=c.'.$db->nameQuote('memberid')
			    . ' AND a.'.$db->nameQuote('status').'=' . $db->Quote( '1' )
			    . ' AND c.'.$db->nameQuote('approved').'=' . $db->Quote( '1' );

		$db->setQuery( $query );

		$total = $db->loadResult();

		return $total;
	}

	public function getInviteFriendsList($userid, $groupid){
		$db	=& $this->getDBO();

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
									. ' FROM '.$db->nameQuote('#__community_groups_members') . ' AS e  ' 
									. ' WHERE e.'.$db->nameQuote('groupid').' = '.$db->Quote($groupid)
									. ' AND e.'.$db->nameQuote('memberid').' = a.'.$db->nameQuote('connect_to')
				.')' ;
		
		$db->setQuery( $query );

		$friends = $db->loadResultArray();

		return $friends;	
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
									. ' FROM '.$db->nameQuote('#__community_groups_members') . ' AS e  ' 
									. ' WHERE e.'.$db->nameQuote('groupid').' = '.$db->Quote($cid)
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
									. ' FROM '.$db->nameQuote('#__community_groups_members') . ' AS e  ' 
									. ' WHERE e.'.$db->nameQuote('groupid').' = '.$db->Quote($cid)
									. ' AND e.'.$db->nameQuote('memberid').' = a.'.$db->nameQuote('connect_to')
				.')' 
				. $andName;
		
		$db->setQuery( $query );
		$this->total	=  $db->loadResult();
		
		return $friends;	
	}

	/**
	 * Return an object of group's invitors
	 */
	public function getInvitors( $userid, $groupid )
	{
		if($userid == 0)
		{
		    return false;
		}

		$db	=&  $this->getDBO();

		$query	=   'SELECT DISTINCT(' . $db->nameQuote( 'creator' ) . ') FROM ' . $db->nameQuote('#__community_groups_invite') . ' '
			    . 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $groupid ) . ' '
			    . 'AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userid );

		$db->setQuery( $query );

		$results  =	$db->loadObjectList();

		return $results;
	}

	/**
	 * Returns All the groups
	 *
	 * @access	public
	 * @param	string 	Category id
	 * @param	string	The sort type
	 * @param	string	Search value
	 * @return	Array	An array of group objects
	 */	 
	public function getAllGroups( $categoryId = null , $sorting = null , $search = null , $limit = null , $skipDefaultAvatar = false )
	{
		$db		=& $this->getDBO();
		
		$extraSQL	= '';
		$pextra		= '';
		
		if( is_null( $limit ) )
		{
			$limit		= $this->getState('limit');
		}
		$limit	= ($limit < 0) ? 0 : $limit;
		$limitstart = $this->getState('limitstart');
		
		// Test if search is parsed
		if( !is_null( $search ) )
		{
			$extraSQL	.= ' AND a.'.$db->nameQuote('name').' LIKE ' . $db->Quote( '%' . $search . '%' ) . ' ';
		}

		if( $skipDefaultAvatar )
		{
			$extraSQL	.= ' AND ( a.'.$db->nameQuote('thumb').' != ' . $db->Quote( DEFAULT_GROUP_THUMB ) . ' AND a.'.$db->nameQuote('avatar').' != ' . $db->Quote( DEFAULT_GROUP_AVATAR ) . ' )';
		}
		$order	=''; 
		switch ( $sorting )
		{
			case 'alphabetical':
				$order		= ' ORDER BY a.'.$db->nameQuote('name').' ASC ';
				break;
			case 'mostdiscussed':
				$order	= ' ORDER BY '.$db->nameQuote('discusscount').' DESC ';
				break;
			case 'mostwall':
				$order	= ' ORDER BY '.$db->nameQuote('wallcount').' DESC ';
				break;
			case 'mostmembers':
				$order	= ' ORDER BY '.$db->nameQuote('membercount').' DESC ';
				break;
			default:
				$order	= 'ORDER BY a.'.$db->nameQuote('created').' DESC ';
				break;
// 			case 'mostactive':
// 				$order	= ' ORDER BY count DESC';
// 				break;
		}

		if( !is_null($categoryId) && $categoryId != 0 )
		{
			$extraSQL	.= ' AND a.'.$db->nameQuote('categoryid').'=' . $db->Quote($categoryId) . ' ';
		//	$pextra		.= ' WHERE a.categoryid=' . $db->Quote($categoryId);
		}
		
		/*
		// Super slow query
        $query = 'SELECT a.*,'
				. 'COUNT(DISTINCT(b.memberid)) AS membercount,'
				. 'COUNT(DISTINCT(c.id)) AS discussioncount,'
				. 'COUNT(DISTINCT(d.id)) AS wallcount '
				. 'FROM ' . $db->nameQuote( '#__community_groups' ) . ' AS a ' 
        		. 'INNER JOIN ' . $db->nameQuote( '#__community_groups_members' ) . ' AS b '
        		. 'ON a.id=b.groupid '
        		. $extraSQL
        		. 'AND b.approved=' . $db->Quote( '1' ) . ' '
        		. 'AND a.published=' . $db->Quote( '1' ) . ' '
        		. 'LEFT JOIN ' . $db->nameQuote( '#__community_groups_discuss' ) . ' AS c '
        		. 'ON a.id=c.groupid '
        		. 'AND c.parentid=' . $db->Quote( '0' ) . ' '
        		. 'LEFT JOIN ' . $db->nameQuote( '#__community_wall') . ' AS d '
        		. 'ON a.id=d.contentid '
				. 'AND d.type=' . $db->Quote( 'groups' ) . ' '
				. 'AND d.published=' . $db->Quote( '1' ) . ' '
                . 'GROUP BY a.id '
                . $order
				. ' LIMIT ' . $limitstart . ',' . $limit;

		$db->setQuery( $query );
		$result	= $db->loadObjectList();
		*/

		if ($sorting == 'mostactive')
		{
			$query = ' SELECT *, ' .$db->nameQuote('cid').', COUNT('.$db->nameQuote('cid').') AS '.$db->nameQuote('count')
				    . ' FROM '.$db->nameQuote('#__community_activities').' AS qx'
				    . ' INNER JOIN	'.$db->nameQuote('#__community_groups').' AS a ON qx.'.$db->nameQuote('cid').' = a.'.$db->nameQuote('id')
				    . ' WHERE qx.'.$db->nameQuote('app').' = '.$db->quote('groups')
				    . ' AND a.'.$db->nameQuote('published').' = '.$db->quote('1')
				    . ' AND qx.'.$db->nameQuote('archived').' = '.$db->quote('0')
				    . ' AND qx.'.$db->nameQuote('cid').' != '.$db->quote('0')
					. $extraSQL
				    . ' GROUP BY qx.'.$db->nameQuote('cid')
				    . ' ORDER BY '.$db->nameQuote('count').' DESC'
				    . ' LIMIT '.$limitstart .' , '.$limit;
		}
		else
		{
			$query = 'SELECT * FROM '.$db->nameQuote('#__community_groups').' as a WHERE a.'.$db->nameQuote('published').'='.$db->Quote('1') .' ' 
					. $extraSQL 
					. $order
					. " LIMIT $limitstart , $limit";
		}
        
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();
		
		if(!empty($rows)){		
			//count members, some might be blocked, so we want to deduct from the total we currently have
			foreach($rows as $k => $r){
				$query = "SELECT COUNT(*) 
						  FROM #__community_groups_members a 
						  JOIN #__users b ON a.memberid=b.id 
						  WHERE `approved`='1' AND b.block=0 AND groupid=".$db->Quote($r->id);
 				$db->setQuery( $query );
 				$rows[$k]->membercount = $db->loadResult();
			}
		}		


// 		if(!empty($rows)){
// 			for($i = 0; $i < count($rows); $i++){
// 				
// 				// Count no of members
// 				$query = "SELECT COUNT(*) FROM #__community_groups_members WHERE `approved`='1' "
// 					. " AND groupid=".$db->Quote($rows[$i]->id);
// 				$db->setQuery( $query );
// 				$rows[$i]->membercount = $db->loadResult();
// 				
// 				// Count wall post
// 				$query = "SELECT COUNT(*) FROM #__community_wall WHERE "
// 					. " `contentid`=".$db->Quote($rows[$i]->id)
// 					. " AND type=".$db->Quote('groups')
// 					. " AND published=".$db->Quote('1');
// 					
// 				$db->setQuery( $query );
// 				$rows[$i]->wallcount = $db->loadResult();
// 				
// 				// Count discussion
// 				$query = "SELECT groupid FROM #__community_groups_discuss "
// 					. " WHERE groupid=".$db->Quote($rows[$i]->id)
// 					. " AND parentid=" . $db->Quote( '0' );
// 				$db->setQuery( $query );
// 				$rows[$i]->discussioncount = $db->loadResult();
// 			}
// 		}

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		$query	= 'SELECT COUNT(*) FROM '.$db->nameQuote('#__community_groups').' AS a '
				. 'WHERE a.'.$db->nameQuote('published').'=' . $db->Quote( '1' )
				. $extraSQL;

		$db->setQuery( $query );
		$this->total	=  $db->loadResult();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			
			$this->_pagination	= new JPagination( $this->total , $limitstart , $limit);
		}
		
		return $rows;
	}
	
	/**
	 * Returns an object of group
	 * 	 	
	 * @access	public
	 * @param	string 	Group Id
	 * @returns object  An object of the specific group	 
	 */
	public function & getGroup( $id )
	{
		$db		=& $this->getDBO();

		$query	= 'SELECT a.*, b.'.$db->nameQuote('name').' AS ownername , c.'.$db->nameQuote('name').' AS category FROM ' 
				. $db->nameQuote('#__community_groups') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b '
				. ' INNER JOIN ' . $db->nameQuote('#__community_groups_category') . ' AS c '
				. ' WHERE a.'.$db->nameQuote('id').'=' . $db->Quote( $id ) . ' '
				. ' AND a.'.$db->nameQuote('ownerid').'=b.'.$db->nameQuote('id')
				. ' AND a.'.$db->nameQuote('categoryid').'=c.'.$db->nameQuote('id');

		$db->setQuery( $query );
		$result	= $db->loadObject();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}

	/**
	 * Loads the categories
	 * 	 	
	 * @access	public
	 * @returns Array  An array of categories object	 
	 */
	public function getCategories( $catId = COMMUNITY_ALL_CATEGORIES )
	{
		$db	=&  $this->getDBO();

		$where	=   '';
		
		if( $catId !== COMMUNITY_ALL_CATEGORIES && ($catId != 0 || !is_null($catId )))
		{
			if( $catId === COMMUNITY_NO_PARENT )
			{
				$where	=   'WHERE a.'.$db->nameQuote('parent').'=' . $db->Quote( COMMUNITY_NO_PARENT ) . ' ';
			}
			else
			{
				$where	=   'WHERE a.'.$db->nameQuote('parent').'=' . $db->Quote( $catId ) . ' ';
			}
		}

		$query	=   'SELECT a.*, COUNT(b.'.$db->nameQuote('id').') AS count '
			    . ' FROM ' . $db->nameQuote('#__community_groups_category') . ' AS a '
			    . ' LEFT JOIN ' . $db->nameQuote( '#__community_groups' ) . ' AS b '
			    . ' ON a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('categoryid')
			    . ' AND b.'.$db->nameQuote('published').'=' . $db->Quote( '1' ) . ' '
			    . $where
			    . ' GROUP BY a.'.$db->nameQuote('id').' ORDER BY a.'.$db->nameQuote('name').' ASC';

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
		$db	=&  $this->getDBO();
		
		$query = "SELECT c.id, c.parent, c.name, count(g.id) AS total, c.description
				  FROM " . $db->nameQuote('#__community_groups_category') . " AS c
				  LEFT JOIN " . $db->nameQuote('#__community_groups'). " AS g ON g.categoryid = c.id
							AND g." . $db->nameQuote('published') . "=" . $db->Quote( '1' ) . "
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
				. 'FROM ' . $db->nameQuote('#__community_groups_category') . ' '
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
	 * Returns the members list for the specific groups
	 * 
	 * @access public
	 * @param	string Category Id
	 * @returns string	Category name
	 **/	 	 	 	 	 	
	public function getAdmins( $groupid , $limit = 0 , $randomize = false )
	{
		CError::assert( $groupid , '', '!empty', __FILE__ , __LINE__ );
		
		$db		=& $this->getDBO();

		$limit		= ($limit === 0) ? $this->getState('limit') : $limit;
		$limitstart = $this->getState('limitstart');
		
		$query	= 'SELECT a.'.$db->nameQuote('memberid').' AS id, a.'.$db->nameQuote('approved').' , b.'.$db->nameQuote('name').' as name FROM '
				. $db->nameQuote('#__community_groups_members') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b '
				. ' WHERE b.'.$db->nameQuote('id').'=a.'.$db->nameQuote('memberid')
				. ' AND a.'.$db->nameQuote('groupid').'=' . $db->Quote( $groupid )
				. ' AND a.'.$db->nameQuote('permissions').'=' . $db->Quote( '1' );
		
		if($randomize)
		{
			$query	.= ' ORDER BY RAND() ';
		}
		
		if( !is_null($limit) )
		{
			$query	.= ' LIMIT ' . $limitstart . ',' . $limit;
		}
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote('#__community_groups_members') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b '
				. ' WHERE b.'.$db->nameQuote('id').'=a.'.$db->nameQuote('memberid')
				. ' AND a.'.$db->nameQuote('groupid').'=' . $db->Quote( $groupid )
				. ' AND a.'.$db->nameQuote('permissions').'=' . $db->Quote( '1' );
		
		$db->setQuery( $query );
		$total	= $db->loadResult();
		$this->total	= $total;
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			
			$this->_pagination	= new JPagination( $total , $limitstart , $limit);
		}

		return $result;
	}
	
	/**
	 * Returns the members list for the specific groups
	 * 
	 * @access public
	 * @param	string Category Id
	 * @returns string	Category name
	 **/
	public function getAllMember($groupid){
	    CError::assert( $groupid , '', '!empty', __FILE__ , __LINE__ );
	    $db		=& $this->getDBO();

	    $query	= 'SELECT a.'.$db->nameQuote('memberid').' AS id, a.'.$db->nameQuote('approved').' , b.'.$db->nameQuote('name').' as name , a.'. $db->nameQuote('permissions') .' as permission FROM '
				. $db->nameQuote('#__community_groups_members') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b '
				. ' WHERE b.'.$db->nameQuote('id').'=a.'.$db->nameQuote('memberid')
				. ' AND a.'.$db->nameQuote('groupid').'=' . $db->Quote( $groupid )
				. ' AND b.'.$db->nameQuote('block').'=' . $db->Quote( '0' ) . ' '
				. ' AND a.'.$db->nameQuote('permissions').' !=' . $db->quote( COMMUNITY_GROUP_BANNED );
	    $db->setQuery( $query );
	    $result	= $db->loadObjectList();
		$this->total = count($result);
	    return $result;
	}
	public function getMembers( $groupid , $limit = 0 , $onlyApproved = true , $randomize = false , $loadAdmin = false )
	{
		CError::assert( $groupid , '', '!empty', __FILE__ , __LINE__ );
		
		$db		=& $this->getDBO();
                $config	= CFactory::getConfig();
		$limit		= ($limit === 0) ? $this->getState('limit') : $limit;
		$limitstart = $this->getState('limitstart');
		
		$query	= 'SELECT a.'.$db->nameQuote('memberid').' AS id, a.'.$db->nameQuote('approved').' , b.'.$db->nameQuote($config->get( 'displayname')).' as name FROM'
				. $db->nameQuote('#__community_groups_members') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b '
				. ' WHERE b.'.$db->nameQuote('id').'=a.'.$db->nameQuote('memberid')
				. ' AND a.'.$db->nameQuote('groupid').'=' . $db->Quote( $groupid )
				. ' AND b.'.$db->nameQuote('block').'=' . $db->Quote( '0' ) . ' '
				. ' AND a.'.$db->nameQuote('permissions').' !=' . $db->quote( COMMUNITY_GROUP_BANNED );
		
		if( $onlyApproved )
		{
			$query	.= ' AND a.'.$db->nameQuote('approved').'=' . $db->Quote( '1' );
		}
		else
		{
			$query	.= ' AND a.'.$db->nameQuote('approved').'=' . $db->Quote( '0' );
		}
		
		if( !$loadAdmin )
		{
			$query	.= ' AND a.'.$db->nameQuote('permissions').'=' . $db->Quote( '0' ); 
		}
		
		if( $randomize )
		{
			$query	.= ' ORDER BY RAND() ';
		}
		else
		{
			
			$query	.= ' ORDER BY b.`' . $config->get( 'displayname') . '`';
		}

		if( !is_null($limit) )
		{
			$query	.= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote('#__community_groups_members') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b '
				. ' WHERE b.'.$db->nameQuote('id').'=a.'.$db->nameQuote('memberid')
				. ' AND a.'.$db->nameQuote('groupid').'=' . $db->Quote( $groupid )
				. ' AND b.'.$db->nameQuote('block').'=' . $db->Quote( '0' );

		if( $onlyApproved )
		{
			$query	.= ' AND a.'.$db->nameQuote('approved').'=' . $db->Quote( '1' );
		}
		else
		{
			$query	.= ' AND a.'.$db->nameQuote('approved').'=' . $db->Quote( '0' );
		}

		if( !$loadAdmin )
		{
			$query	.= ' AND a.'.$db->nameQuote('permissions').'=' . $db->Quote( '0' ); 
		}

		$db->setQuery( $query );
		$total		= $db->loadResult();
		$this->total	= $total;
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			
			$this->_pagination	= new JPagination( $total , $limitstart , $limit);
		}

		return $result;
	}
	
	/**
	 * Check if the given group name exist.
	 * if id is specified, only search for those NOT within $id	 
	 */	 	
	public function groupExist($name, $id=0) {
		$db		=& $this->getDBO();
		
		$strSQL	= 'SELECT COUNT(*) FROM '.$db->nameQuote('#__community_groups')
			. ' WHERE '.$db->nameQuote('name').'=' . $db->Quote( $name ) 
			. ' AND '.$db->nameQuote('id').'!='. $db->Quote( $id ) ;


		$db->setQuery( $strSQL );
		$result	= $db->loadResult();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		return $result;
	}

	public function getMembersId( $groupid , $onlyApproved = false )
	{
		$db		=& $this->getDBO();

		$query	= 'SELECT a.'.$db->nameQuote('memberid').' AS id FROM '
				. $db->nameQuote('#__community_groups_members') . ' AS a '
				. 'WHERE a.'.$db->nameQuote('groupid').'=' . $db->Quote( $groupid );

		if( $onlyApproved )
			$query	.= ' AND ' . $db->nameQuote( 'approved' ) . '=' . $db->Quote( '1' );

		$db->setQuery( $query );
		$result	= $db->loadResultArray();
		
		return $result;
	}
	
	public function updateGroup($data)
	{
		$db		=& $this->getDBO();
		
		if($data->id == 0)
		{
			// New record, insert it.
			$db->insertObject( '#__community_groups' , $data );

			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
			}
			$data->id				= $db->insertid();
			
			// Insert an object for this user in the #__community_groups_members as well
			$members				= new stdClass();
			$members->groupid		= $data->id;
			$members->memberid		= $data->ownerid;

									
			// Creator should always be 1 as approved as they are the creator.
			$members->approved		= 1;
			$members->permissions	= 'admin';

			$db->insertObject( '#__community_groups_members' , $members );

			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
			}
		}
		else
		{
			// Old record, update it.
			$db->updateObject( '#__community_groups' , $data , 'id');
		}
		return $data->id;
	}

	/**
	 *	Set the avatar for for specific group
	 *	
	 * @param	appType		Application type. ( users , groups )
	 * @param	path		The relative path to the avatars.
	 * @param	type		The type of Image, thumb or avatar.
	 *
	 **/	 	 
	public function setImage(  $id , $path , $type = 'thumb' )
	{
		CError::assert( $id , '' , '!empty' , __FILE__ , __LINE__ );
		CError::assert( $path , '' , '!empty' , __FILE__ , __LINE__ );
		
		$db			=& $this->getDBO();
		
		// Fix the back quotes
		$path		= CString::str_ireplace( '\\' , '/' , $path );
		$type		= JString::strtolower( $type );
		
		// Test if the record exists.
		$query		= 'SELECT ' . $db->nameQuote( $type ) . ' FROM ' . $db->nameQuote( '#__community_groups' )
					. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		
		$db->setQuery( $query );
		$oldFile	= $db->loadResult();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
	    }
	    
	    if( !$oldFile )
	    {
	    	$query	= 'UPDATE ' . $db->nameQuote( '#__community_groups' ) . ' '
	    			. 'SET ' . $db->nameQuote( $type ) . '=' . $db->Quote( $path ) . ' '
	    			. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
	    	$db->setQuery( $query );
	    	$db->query( $query );

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
		    }
		}
		else
		{

	    	$query	= 'UPDATE ' . $db->nameQuote( '#__community_groups' ) . ' '
	    			. 'SET ' . $db->nameQuote( $type ) . '=' . $db->Quote( $path ) . ' '
	    			. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
	    	$db->setQuery( $query );
	    	$db->query( $query );

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
		    }
		    
			// File exists, try to remove old files first.
			$oldFile	= CString::str_ireplace( '/' , DS , $oldFile );

			// If old file is default_thumb or default, we should not remove it.
			// Need proper way to test it
			if(!JString::stristr( $oldFile , 'group.jpg' ) && !JString::stristr( $oldFile , 'group_thumb.jpg' ) &&
			   !JString::stristr( $oldFile , 'default.jpg' ) && !JString::stristr( $oldFile , 'default_thumb.jpg' ) )
			{
				jimport( 'joomla.filesystem.file' );
				JFile::delete($oldFile);
			}
		}
		
		return $this;
	}
		
	public function removeMember( $data )
	{
		$db	=& $this->getDBO();
		
		$strSQL	= 'DELETE FROM ' . $db->nameQuote('#__community_groups_members') . ' '
				. 'WHERE ' . $db->nameQuote('groupid') . '=' . $db->Quote( $data->groupid ) . ' '
				. 'AND ' . $db->nameQuote('memberid') . '=' . $db->Quote( $data->memberid );
		
		$db->setQuery( $strSQL );
		$db->query();

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
	}

	/**
	 * Check if the user is a group creator 
	 */
	public function isCreator( $userId , $groupId )
	{
		// guest is not a member of any group
		if($userId == 0)
			return false;
			
		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_groups' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $groupId ) . ' '
				. 'AND ' . $db->nameQuote( 'ownerid' ) . '=' . $db->Quote( $userId );
		$db->setQuery( $query );
		
		$isCreator	= ( $db->loadResult() >= 1 ) ? true : false;
		return $isCreator;
	}

	/**
	 * Check if the user is invited in the group
	 */
	public function isInvited($userid, $groupid)
	{
		if($userid == 0)
		{
		    return false;
		}

		$db	=&  $this->getDBO();

		$query	=   'SELECT * FROM ' . $db->nameQuote('#__community_groups_invite') . ' '
			    . 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $groupid ) . ' '
			    . 'AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userid );

		$db->setQuery( $query );

		$isInvited	= ( $db->loadResult() >= 1 ) ? true : false;

		return $isInvited;
	}

	/**
	 * Check if the user is a group admin 
	 */	 	
	public function isAdmin($userid, $groupid)
	{
		if($userid == 0)
			return false;
			
		$db		=& $this->getDBO();

		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote('#__community_groups_members') . ' '
				. ' WHERE ' . $db->nameQuote('groupid') . '=' . $db->Quote($groupid) . ' '
				. ' AND ' . $db->nameQuote('memberid') . '=' . $db->Quote($userid) 
				. ' AND '.$db->nameQuote('permissions').'=' . $db->Quote( '1' );
				
		$db->setQuery( $query );
		$isAdmin	= ( $db->loadResult() >= 1 ) ? true : false;

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		//@remove: in newer version we need to skip this test as we were using 'admin'
		// as the permission for the creator
		if( !$isAdmin )
		{
			$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_groups' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $groupid ) . ' '
					. 'AND ' . $db->nameQuote( 'ownerid' ) . '=' . $db->Quote( $userid );
			$db->setQuery( $query );
			
			$isAdmin	= ( $db->loadResult() >= 1 ) ? true : false;

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
			}
			
			// If user is admin, update necessary records
			if( $isAdmin )
			{
				$members	=& JTable::getInstance( 'GroupMembers' , 'CTable' );
				$members->load( $userid , $groupid );
				$members->permissions	= '1';
				$members->store();
			}
		}
		
		return $isAdmin;
	}
	
	/**
	 * Check if the given user is a member of the group
	 * @param	string	userid	
	 * @param	string	groupid	 	 
	 */	 	
	public function isMember($userid, $groupid) {
		
		// guest is not a member of any group
		if($userid == 0)
			return false;
			
		$db	=& $this->getDBO();
		$strSQL	= 'SELECT COUNT(*) FROM ' . $db->nameQuote('#__community_groups_members') . ' '
				. 'WHERE ' . $db->nameQuote('groupid') . '=' . $db->Quote($groupid) . ' '
				. 'AND ' . $db->nameQuote('memberid') . '=' . $db->Quote($userid)
				. 'AND ' . $db->nameQuote( 'approved' ) .'=' . $db->Quote( '1' );
				
		$db->setQuery( $strSQL );
		$count	= $db->loadResult();
		return $count;
	}
	
	/**
	 * See if the given user is waiting authorization for the group
	 * @param	string	userid	
	 * @param	string	groupid	 	 
	 */	 	
	public function isWaitingAuthorization($userid, $groupid) {
		// guest is not a member of any group
		if($userid == 0)
			return false;
			
		$db	=& $this->getDBO();
		$strSQL	= 'SELECT COUNT(*) FROM `#__community_groups_members` '
				. 'WHERE ' . $db->nameQuote('groupid') . '=' . $db->Quote($groupid) . ' '
				. 'AND ' . $db->nameQuote('memberid') . '=' . $db->Quote($userid)
				. 'AND ' . $db->nameQuote('approved') . '=' . $db->Quote(0);
				
		$db->setQuery( $strSQL );
		$count	= $db->loadResult();
		return $count;
	}

	/**
	 * Gets the groups property if it requires an approval or not.
	 * 
	 * param	string	id The id of the group.
	 * 
	 * return	boolean	True if it requires approval and False otherwise
	 **/	
	public function needsApproval( $id )
	{
		$db		=& $this->getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'approvals' ) . ' FROM '
				. $db->nameQuote( '#__community_groups' ) . ' WHERE '
				. $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
				
		$db->setQuery( $query );
		$result	= $db->loadResult();
		
		return ( $result == '1' );
	}
	
	/**
	 * Sets the member data in the group members table
	 * 
	 * param	Object	An object that contains the fields value
	 * 	 
	 **/	 	
	public function approveMember( $groupid , $memberid )
	{
		$db		=& $this->getDBO();

		$query	= 'UPDATE ' . $db->nameQuote( '#__community_groups_members' ) . ' SET '
				. $db->nameQuote( 'approved' ) . '=' . $db->Quote( '1' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $groupid ) . ' '
				. 'AND ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $memberid );

		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}
	
	/**
	 * Delete group's bulletin 
	 * 
	 * param	string	id The id of the group.
	 * 	 
	 **/
	public function deleteGroupBulletins($gid)
	{
		$db = JFactory::getDBO();
				
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_groups_bulletins")." 
				WHERE 
						".$db->nameQuote("groupid")." = ".$db->quote($gid);
						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		return true;
	}
	
	/**
	 * Delete group's member
	 * 
	 * param	string	id The id of the group.
	 * 	 
	 **/
	public function deleteGroupMembers($gid)
	{
		$db = JFactory::getDBO();
				
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_groups_members")." 
				WHERE 
						".$db->nameQuote("groupid")."=".$db->quote($gid);						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
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
	public function deleteGroupWall($gid)
	{
		$db = JFactory::getDBO();
				
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_wall")." 
				WHERE 
						".$db->nameQuote("contentid")." = ".$db->quote($gid)." AND
						".$db->nameQuote("type")." = ".$db->quote('groups');						
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}

                //Remove Group info from activity stream
                $sql = "Delete FROM " .$db->nameQuote("#__community_activities"). "
                        WHERE ". $db->nameQuote("groupid") . " = ".$db->Quote($gid);

                $db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		return true;
	}
	
	/**
	 * Delete group's discussion
	 * 
	 * param	string	id The id of the group.
	 * 	 
	 **/
	public function deleteGroupDiscussions($gid)
	{
		$db = JFactory::getDBO();
	
		$sql = "SELECT 
						".$db->nameQuote("id")." 						
				FROM 
						".$db->nameQuote("#__community_groups_discuss")." 
				WHERE 
						".$db->nameQuote("groupid")." = ".$gid;						
		$db->setQuery($sql);
		$row = $db->loadobjectList();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		if(!empty($row))
		{
			$ids_array = array();	
			foreach($row as $tempid)
			{
				array_push($ids_array, $tempid->id);
			}
			$ids = implode(',', $ids_array);
		}			
					
		$sql = "DELETE 
				
				FROM 
						".$db->nameQuote("#__community_groups_discuss")." 
				WHERE 
						".$db->nameQuote("groupid")." = ".$gid;				
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		if(!empty($ids))
		{				
			$sql = "DELETE 
					
					FROM 
							".$db->nameQuote("#__community_wall")." 
					WHERE 
							".$db->nameQuote("contentid")." IN (".$ids.") AND 
							".$db->nameQuote("type")." = ".$db->quote('discussions');				
			$db->setQuery($sql);
			$db->Query();
			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
			}
		}
		
		return true;
	}
	
	/**
	 * Delete group's media
	 * 
	 * param	string	id The id of the group.
	 * 	 
	 **/	
	public function deleteGroupMedia($gid)
	{		
		$db 			= JFactory::getDBO();
		$photosModel	= CFactory::getModel( 'photos' );
		$videoModel		= CFactory::getModel( 'videos' );
		
		// group's photos removal.
		$albums			=& $photosModel->getGroupAlbums($gid , false, false, 0);		
		foreach ($albums as $item)
		{
			$photos			= $photosModel->getAllPhotos($item->id, PHOTOS_GROUP_TYPE);
			
			foreach ($photos as $row)
			{
				$photo	=& JTable::getInstance( 'Photo' , 'CTable' );
				$photo->load($row->id);
				$photo->delete();
			}
			
			//now we delete group photo album folder
			$album	=& JTable::getInstance( 'Album' , 'CTable' );
			$album->load($item->id);
			$album->delete();
		}
		
		//group's videos
		CFactory::load('libraries', 'storage');
		CFactory::load('libraries','featured');
		$featuredVideo	= new CFeatured(FEATURED_VIDEOS);
		$videos			= $videoModel->getGroupVideos($gid);
		
		foreach($videos as $vitem)
		{
			if (!$vitem) continue;
				
			$video		= JTable::getInstance( 'Video' , 'CTable' );
			$videaId	= (int) $vitem->id;
						
			$video->load($videaId);
									
			if($video->delete())
			{
				// Delete all videos related data											
				$videoModel->deleteVideoWalls($videaId);				
				$videoModel->deleteVideoActivities($videaId);
								
				//remove featured video								
				$featuredVideo->delete($videaId);				
																
				//remove the physical file				
				$storage = CStorage::getStorage($video->storage);
				if ($storage->exists($video->thumb))
				{
					$storage->delete($video->thumb);
				}
								
				if ($storage->exists($video->path))
				{
					$storage->delete($video->path);
				}
			}
			
		}
		
		return true;
	}
	
	/*
	 * group category id - int
	 * list of group ids - string, separate by comma
	 * limit - int
	 */
	
	public function getGroupLatestDiscussion($category = 0, $groupids = '', $limit = '')
	{
	    $db 		= JFactory::getDBO();
	    $config		= CFactory::getConfig();
		$mainframe	= JFactory::getApplication();
		
		// Get pagination request variables
	    $limit  = (empty($limit)) ? $mainframe->getCfg('list_limit') : $limit;

		// Filter category
		$idswhere = '';
		if( !empty($groupids))
		{
			$idswhere = ' AND b.`id` IN (' . $groupids . ')';
		}
		
		$query	 = 'SELECT a.'.$db->nameQuote('id').', a.'.$db->nameQuote('groupid').', a.'.$db->nameQuote('creator').', a.'.$db->nameQuote('title').',a.'.$db->nameQuote('message').', a.'.$db->nameQuote('lastreplied');
		$query	.= ' FROM '.$db->nameQuote('#__community_groups_discuss').' AS a ';
		$query	.= '	JOIN (';
		$query	.= '	SELECT b.'.$db->nameQuote('id');
		$query	.= '	FROM '.$db->nameQuote('#__community_groups').' AS b';
		$query	.= '	WHERE ';
		$query	.= '		b.'.$db->nameQuote('published').' = 1';
		$query	.= '		AND ';
		$query	.= '		b.'.$db->nameQuote('approvals').' = 0';
		$query	.= '		'.$idswhere;
		$query	.= '	) AS c ON c.'.$db->nameQuote('id').' = a.'.$db->nameQuote('groupid');
		
		$query  .= ' order by a.'. $db->nameQuote('lastreplied'). ' desc';
		if(! empty($limit))
		{
		    $query  .= ' LIMIT '. $limit;
		}
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
		
	}
	
	/**
	 * Return the name of the group id
	 */
	public function getGroupName( $groupid )
	{
		$session = JFactory::getSession();
		$data = $session->get('groups_name_'.$groupid);
		if($data)
		{
			return $data;
		}
		$db	=& $this->getDBO();

		$query	=   'SELECT ' . $db->nameQuote('name').' FROM ' . $db->nameQuote('#__community_groups')
					. " WHERE " . $db->nameQuote("id") . "=" . $db->Quote($groupid);

		$db->setQuery( $query );

		$name = $db->loadResult();
		
		$session->set('groups_name_'.$groupid, $name);
		return $name;
	}


    /**
     * @deprecated Since 2.0
     */
	public function getThumbAvatar($id, $thumb)
	{
		CFactory::load('helpers', 'url');
		$thumb	= CUrlHelper::avatarURI($thumb, 'group_thumb.png');
		
		return $thumb;
	}


	public function getBannedMembers( $groupid, $limit=0, $randomize=false )
	{
		CError::assert( $groupid , '', '!empty', __FILE__ , __LINE__ );

		$db	    =&	$this->getDBO();

		$limit	    =	($limit === 0) ? $this->getState('limit') : $limit;
		$limitstart =	$this->getState('limitstart');

		$query	    =	'SELECT a.'.$db->nameQuote('memberid').' AS id, a.'.$db->nameQuote('approved').' , b.'.$db->nameQuote('name').' as name '
				. ' FROM '. $db->nameQuote('#__community_groups_members') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b '
				. ' WHERE b.'.$db->nameQuote('id').'=a.'.$db->nameQuote('memberid')
				. ' AND a.'.$db->nameQuote('groupid').'=' . $db->Quote( $groupid )
				. ' AND a.'.$db->nameQuote('permissions').'=' . $db->Quote( COMMUNITY_GROUP_BANNED );

		if( $randomize )
		{
			$query	.=  ' ORDER BY RAND() ';
		}

		if( !is_null($limit) )
		{
			$query	.=  ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$db->setQuery( $query );

		$result	    =   $db->loadObjectList();

		if( $db->getErrorNum() )
		{
			JError::raiseError( 500, $db->stderr() );
		}

		$query	    =	'SELECT COUNT(*) FROM '
				. $db->nameQuote('#__community_groups_members') . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b '
				. ' WHERE b.'.$db->nameQuote('id').'=a.'.$db->nameQuote('memberid')
				. ' AND a.'.$db->nameQuote('groupid').'=' . $db->Quote( $groupid ) . ' '
				. ' AND a.'.$db->nameQuote('permissions').'=' . $db->Quote( COMMUNITY_GROUP_BANNED );

		$db->setQuery( $query );
		$total		=   $db->loadResult();
		$this->total	=   $total;

		if( $db->getErrorNum() ) {
			JError::raiseError( 500, $db->stderr() );
		}

		if( empty($this->_pagination) )
		{
			jimport( 'joomla.html.pagination' );
			$this->_pagination  =	new JPagination( $total, $limitstart, $limit );
		}

		return $result;
	}

	public function getGroupsSearchTotal()
	{
		return $this->total;
	}
	public function getGroupChildId($gid){
	   
	    $db = JFactory::getDBO();
	    CFactory::load( 'libraries' , 'activities' );
	    $sql = "SELECT
						".$db->nameQuote("id")."
				FROM
						".$db->nameQuote("#__community_groups_discuss")."
				WHERE
						".$db->nameQuote("groupid")." = ".$db->Quote($gid);
		$db->setQuery($sql);
		$row = $db->loadobjectList();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

	    $sql = "SELECT
						".$db->nameQuote("id")."
				FROM
						".$db->nameQuote("#__community_groups_bulletins")."
				WHERE
						".$db->nameQuote("groupid")." = ".$db->Quote($gid);
		$db->setQuery($sql);
		$bulletin = $db->loadobjectList();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	    $sql = "SELECT
						".$db->nameQuote("id")."
				FROM
						".$db->nameQuote("#__community_wall")."
				WHERE
						".$db->nameQuote("contentid")." = ".$db->Quote($gid);
		$db->setQuery($sql);
		$wall = $db->loadobjectList();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		$row = array_merge($row, array_merge($bulletin,$wall));
		
		if(!empty($row))
		{
			$ids_array = array();
			foreach($row as $tempid)
			{
				array_push($ids_array, $tempid->id);
			}
			$ids = implode(',', $ids_array);
			$ids .= ','.$gid;
			//Remove All groupActivity stream
			CActivityStream::removeGroup($ids);
		}
	}
        public function countPending($userId){

            $db = & $this->getDBO();

            $query	= 'SELECT COUNT(*) FROM '
			. $db->nameQuote('#__community_groups_invite') . ' AS a '
			. ' INNER JOIN ' . $db->nameQuote( '#__community_groups' ) . ' AS b ON a.'.$db->nameQuote('groupid').'=b.'.$db->nameQuote('id')
                        . ' AND a.' .$db->nameQuote('userid'). '=' . $db->Quote($userId);
            
            $db->setQuery($query);

		if ($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}

            return $db->loadResult();
        }

        public function getTotalNotifications( $userId )
	{
                $allGroups      =   $this->getAdminGroups( $userId , COMMUNITY_PRIVATE_GROUP);
                
                $privateGroupRequestCount=0;

                foreach($allGroups as $groups){
                    $member     =    $this->getMembers( $groups->id , 0, false );
                    
                    if(!empty($member))
                    {
                            $privateGroupRequestCount += count($member);
                    }
                }
		return (int) $this->countPending( $userId ) + $privateGroupRequestCount;
	}

        public function getAdminGroups( $userId, $privacy = NULL )
        {
            $extraSQL = NULL;
            $db		=& $this->getDBO();
            
            if( $privacy == COMMUNITY_PRIVATE_GROUP )
            {
                $extraSQL = ' AND a.'.$db->nameQuote('approvals').'=' . $db->Quote( '1' );
            }

            if( $privacy == COMMUNITY_PUBLIC_GROUP )
            {
                $extraSQL = ' AND a.'.$db->nameQuote('approvals').'=' . $db->Quote( '0' );
            }
            $query	=   'SELECT a.* FROM '
                            . $db->nameQuote('#__community_groups') . ' AS a '
                            . ' INNER JOIN ' . $db->nameQuote('#__community_groups_members') . ' AS b '
                            . ' ON a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('groupid')
                            . ' AND b.'.$db->nameQuote('approved').'=' . $db->Quote( '1' )
                            . ' AND b.'.$db->nameQuote('permissions').'=' . $db->Quote( '1' )
                            . ' AND a.'.$db->nameQuote('published').'=' . $db->Quote( '1' )
                            . ' AND b.'.$db->nameQuote('memberid').'=' . $db->Quote($userId)
                            . $extraSQL;

            $db->setQuery( $query );
            $result	= $db->loadObjectList();

            if($db->getErrorNum())
            {
                        JError::raiseError( 500, $db->stderr());
            }
            return $result;
        }
}