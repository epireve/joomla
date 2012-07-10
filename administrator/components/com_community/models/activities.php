<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );


class CommunityModelActivities extends JModel
{
	/**
	 * Configuration data
	 * 
	 * @var object	JPagination object
	 **/
	var $_pagination;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$mainframe	=& JFactory::getApplication();

		// Call the parents constructor
		parent::__construct();

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( 'com_community.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	/**
	 * Retrieves the JPagination object
	 *
	 * @return object	JPagination object	 	 
	 **/	 	
	public function &getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
	}

	public function _buildQuery()
	{
		$db			=& JFactory::getDBO();
		$actor		= JRequest::getVar( 'actor' , '' );
		$archived	= JRequest::getInt( 'archived' , 0 );
		$app		= JRequest::getVar( 'app' , 'none' );
		$where		= array();
		
		CFactory::load( 'helpers' , 'user' );
		$userId		= cGetUserId( $actor );

		if( $userId != 0 )
		{
			$where[]	= 'actor=' . $db->Quote( $userId ) . ' ';
		}

		if( $archived != 0 )
		{
			$archived	= $archived - 1;
			$where[]	= 'archived=' . $db->Quote( $archived ) . ' ';
		}

		if( $app != 'none' )
		{
			
			$where[]	= 'app=' . $db->Quote( $app );
		}
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_activities' );
		
		if( !empty($where) )
		{
			for( $i = 0; $i < count( $where ); $i++ )
			{
				if( $i == 0 )
				{
					$query	.= ' WHERE ';
				}
				else
				{
					$query	.= ' AND ';
				}
				$query	.= $where[ $i ];
			}
		}
		
		$query	.= ' ORDER BY created DESC';
		return $query;
	}
	
	public function getFilterApps()
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT DISTINCT app FROM ' . $db->nameQuote( '#__community_activities' );
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( $db->getErrorNum() )
			return false;

		return $result;
	}
	
	public function getActivities()
	{
		if(empty($this->_data))
		{
			$query			= $this->_buildQuery();
			$this->_data	= $this->_getList( $query , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}
	
	public function delete( $activityId )
	{
		$db		=& JFactory::getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__community_activities' ) . ' WHERE '
				. $db->nameQuote( 'id' ) . '=' . $db->Quote( $activityId );
		$db->setQuery( $query );
		$db->Query();
		
		if( $db->getErrorNum() )
			return false;

		return true;
	}
	
	public function purge()
	{
		$db		=& JFactory::getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__community_activities' );
		$db->setQuery( $query );

		$db->Query();
		
		if( $db->getErrorNum() )
			return false;

		return true;
	}
}