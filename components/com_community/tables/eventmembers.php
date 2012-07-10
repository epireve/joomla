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

class CTableEventMembers extends JTable
{
	var $id				= null;
	var $eventid		= null;
	var $memberid		= null;
	var $status			= null;
	var $permission		= null;
	var $invited_by		= null;
	var $created		= null;

	/**
	 * Constructor
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__community_events_members', 'id', $db );
	}

	/**
	 * Overrides Joomla's JTable load as this table has composite keys and need
	 * to be loaded differently
	 *
	 * @access	public
	 *
	 * @return	boolean	True if successful
	 */
	public function load( $memberId , $eventId )
	{
		$query	= 'SELECT * FROM ' . $this->_db->nameQuote( '#__community_events_members' ) . ' '
				. 'WHERE ' . $this->_db->nameQuote( 'eventid' ) . '=' . $this->_db->Quote( $eventId ) . ' '
				. 'AND ' . $this->_db->nameQuote( 'memberid' ) . '=' . $this->_db->Quote( $memberId );
		$this->_db->setQuery( $query );

		$result	= $this->_db->loadAssoc();

		if(empty($result))
		{
		    $result['id'] 			= '0';
		    $result['eventid'] 		= $eventId;
		    $result['memberid'] 	= $memberId;
		    $result['status'] 		= '0';
		    $result['permission'] 	= '0';
		    $result['invitedby'] 	= '0';
		    $result['created'] 		= '0';

		}
		
		$this->bind( $result );
	}
	
	/**
	 * Method to test if a specific user is already registered under a event
	 *
	 * @return boolean True if user is registered and false otherwise
	 **/
	public function exists()
	{
		$query	= 'SELECT COUNT(id) FROM ' . $this->_db->nameQuote( '#__community_events_members' )
				. ' WHERE ' . $this->_db->nameQuote( 'eventid' ) . '=' . $this->_db->Quote( $this->eventid )
				. ' AND ' . $this->_db->nameQuote( 'memberid' ) . '=' . $this->_db->Quote( $this->memberid );

		$this->_db->setQuery( $query );

		$return	= ( $this->_db->loadResult() >= 1 ) ? true : false;

		if($this->_db->getErrorNum())
		{
			JError::raiseError( 500, $this->_db->stderr());
		}
		return $return;
	}
	
	/**
	 * Overrides Joomla's JTable store as this table has composite keys
	 **/
	public function store()
	{
		if( ! $this->exists() )
		{
 			$data			= new stdClass();

 			foreach( get_object_vars($this) as $property => $value )
 			{
 				// We dont want to set private properties
				if( JString::strpos( JString::strtolower($property) , '_') === false || $property == 'invited_by')
				{
					$data->$property	= $value;
				}
			}
			return $this->_db->insertObject( '#__community_events_members' , $data );
		}
		else
		{
			$query	= 'UPDATE ' . $this->_db->nameQuote( '#__community_events_members' ) . ' '
					. 'SET ' . $this->_db->nameQuote( 'status' ) . '=' . $this->_db->Quote( $this->status ) . ', '
					. $this->_db->nameQuote( 'permission' ) . '=' . $this->_db->Quote( $this->permission ) . ', '
					. $this->_db->nameQuote( 'invited_by' ) . '=' . $this->_db->Quote( $this->invited_by ) . ' '
					. 'WHERE ' . $this->_db->nameQuote( 'eventid' ) . '=' . $this->_db->Quote( $this->eventid ) . ' '
					. 'AND ' . $this->_db->nameQuote( 'memberid' ) . '=' . $this->_db->Quote( $this->memberid );

			$this->_db->setQuery( $query );
			$this->_db->query();

			if($this->_db->getErrorNum())
			{
				JError::raiseError( 500, $this->_db->stderr());
				return false;
			}
			return true;
		}
	}
	
	public function invite()
	{
		$this->status = COMMUNITY_EVENT_STATUS_INVITED;
	}

        public function attend()
        {
            $this->status = COMMUNITY_EVENT_STATUS_ATTEND;
        }
	
}