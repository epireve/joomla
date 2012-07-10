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

class CommunityModelReports extends JModel
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

		$query		= 'SELECT * FROM '
					. $db->nameQuote( '#__community_reports' ) . ' ' 
					. 'ORDER BY created';

		return $query;
	}
		 	
	/**
	 * Returns the Groups Categories list
	 *
	 * @return Array An array of group category objects
	 **/
	public function &getReports()
	{
		$mainframe	=& JFactory::getApplication();
		
		if(empty($this->_data))
		{
			$db				=& JFactory::getDBO();
			$query			= $this->_buildQuery();
			$data			= $this->_getList( $query , $this->getState( 'limitstart' ) , $this->getState( 'limit') );
			
			// Append the actions to the reports
			for( $i = 0; $i < count( $data ); $i++ )
			{
				$row			=& $data[ $i ];
				
				// Get the actions
				$query			= 'SELECT * FROM ' . $db->nameQuote( '#__community_reports_actions' ) . ' '
								. 'WHERE ' . $db->nameQuote( 'reportid' ) . '=' . $db->Quote( $row->id );
				$db->setQuery( $query );
				$row->actions	= $db->loadObjectList();
				
				// Get the reporters
				$query			= 'SELECT * FROM ' . $db->nameQuote( '#__community_reports_reporter' ) . ' '
								. 'WHERE ' . $db->nameQuote( 'reportid' ) . '=' . $db->Quote( $row->id);
								
				$db->setQuery( $query );
				$row->reporters	= $db->loadObjectList();
			}
			$this->_data	= $data;
		}
		return $this->_data;
	}
	
	public function purgeProcessed()
	{
		$db			=& JFactory::getDBO();
		
		$query		= 'DELETE FROM ' . $db->nameQuote( '#__community_reports' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'status' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$db->query();
		return true;
	}
}