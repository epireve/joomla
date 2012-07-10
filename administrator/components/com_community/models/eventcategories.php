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

class CommunityModelEventCategories extends JModel
{
	/**
	 * Configuration data
	 * 
	 * @var object	JPagination object
	 **/	 	 	 
	var $_pagination;

	/**
	 * Configuration data
	 * 
	 * @var int	Total number of rows
	 **/
	var $_total;

	/**
	 * Configuration data
	 * 
	 * @var int	Total number of rows
	 **/
	var $_data;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$mainframe = JFactory::getApplication();

		// Call the parents constructor
		parent::__construct();

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'com_community.eventcategories.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		//$limitstart	= $mainframe->getUserStateFromRequest( 'com_community.limitstart', 'limitstart', 0, 'int' );
		$limitstart	= JRequest::getVar('limitstart', 0);

		// In case limit has been changed, adjust limitstart accordingly
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
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
	}

	/**
	 * Build the SQL query string
	 *
	 * @access	private
	 * @return	string	SQL Query string	 
	 */
	public function _buildQuery()
	{		
		$db				= JFactory::getDBO();
		$mainframe		= JFactory::getApplication();
		$ordering		= $mainframe->getUserStateFromRequest( "com_community.eventcategories.filter_order",		'filter_order',		'name',	'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( "com_community.eventcategories.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		
		switch( $ordering )
		{
			case 'members':
				$orderby		= ' ORDER BY memberscount '. $orderDirection;
				break;
			case 'groups':
				$orderby		= ' ORDER BY groupscount '. $orderDirection;
				break;  
			case 'id':
				$orderby		= ' ORDER BY id '. $orderDirection;
				break;
			default:
				$orderby		= ' ORDER BY name '. $orderDirection;
				break;
		}
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_events_category' ) . 'GROUP BY id' 
				. ' ORDER BY '.$ordering.' '.$orderDirection;//.= $orderby;
				
		return $query;
	}

	/**
	 * Returns the Groups Categories list
	 *
	 * @return Array An array of group category objects
	 **/
	public function getCategories()
	{
		$mainframe = JFactory::getApplication();
		
		if(empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data	= $this->_getList( $this->_buildQuery() , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}
	
	/**
	 * Returns the Groups Categories list
	 *
	 * @return Array An array of group category objects
	 **/
	public function getCategoriesCount()
	{
		$db	= JFactory::getDBO();
				
		$query = 'SELECT `catid`, COUNT(*) AS `count` FROM ' . $db->nameQuote( '#__community_events' ) . ' GROUP BY ' . $db->nameQuote( 'catid' );
		$db->setQuery($query);
		$result = $db->loadObjectList('catid');
		
		return $result;		
	}
}