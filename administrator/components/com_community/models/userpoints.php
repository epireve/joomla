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

class CommunityModelUserPoints extends JModel
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
	 * Configuration data
	 * 
	 * @var int	Total number of rows
	 **/
	var $_ordering;	
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		global $option;
		$mainframe	=& JFactory::getApplication();

		// Call the parents constructor
		parent::__construct();

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( 'com_community.userpoints.limitstart', 'limitstart', 0, 'int' );
		
		// Get ordering element
		$filter_order		= $mainframe->getUserStateFromRequest( "com_community.userpoints.filter_order",		'filter_order',		'rule_plugin',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "com_community.userpoints.filter_order_Dir",	'filter_order_Dir',	'',			'word' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		
	}
	
	public function getOrdering()
	{
		if(empty($this->_ordering))
		{
			$this->_ordering = array('order' => $this->getState('filter_order'),
									 'order_Dir' => $this->getState('filter_order_Dir'));
		}
		
		return $this->_ordering;
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
		$db			=& JFactory::getDBO();
		
		$filter_order		= $this->getState( 'filter_order' );
		$filter_order_Dir	= $this->getState( 'filter_order_Dir' );
				
		
		$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;

		$query		= 'SELECT * FROM '
					. $db->nameQuote( '#__community_userpoints' ) . ' ' 
					. $orderby;

		return $query;
	}
		 	
	/**
	 * Returns the Groups Categories list
	 *
	 * @return Array An array of group category objects
	 **/
	public function &getUserPoints()
	{
		$mainframe	=& JFactory::getApplication();
		
		if(empty($this->_data))
		{
			$db				=& JFactory::getDBO();
			$query			= $this->_buildQuery();
			$data			= $this->_getList( $query , $this->getState( 'limitstart' ) , $this->getState( 'limit') );
			$this->_data	= $data;
		}
		return $this->_data;
	}
		
}