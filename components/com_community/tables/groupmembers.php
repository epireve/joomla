<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableGroupMembers extends JTable
{
	var $groupid		= null;
	var $memberid		= null;
	var $approved		= null;
	var $permissions	= null;
	
	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_groups_members', 'id', $db );
	}

	/**
	 * Method to test if a specific user is already registered under a group
	 * 
	 * @return boolean True if user is registered and false otherwise
	 **/	 	
	public function exists()
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_groups_members' )
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->groupid ) . ' '
				. 'AND ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $this->memberid );
				
		$db->setQuery( $query );
		
		$return	= ( $db->loadResult() >= 1 ) ? true : false;

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}		
		return $return;
	}

	/**
	 * Overrides Joomla's JTable load as this table has composite keys and need
	 * to be loaded differently	 
	 *
	 * @access	public
	 *
	 * @return	boolean	True if successful
	 */
	public function load( $memberId , $groupId )
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_groups_members' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $groupId ) . ' '
				. 'AND ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $memberId );
		$db->setQuery( $query );
		
		$result	= $db->loadAssoc();
		$this->bind( $result );
	}
	
	/**
	 * Overrides Joomla's JTable store as this table has composite keys
	 * 
	 * @param	string	User's id
	 * @param	string	Group's id
	 * @return boolean True if user is registered and false otherwise
	 **/	
	public function store()
	{
		$db		=& $this->getDBO();
		
		if( !$this->exists() )
		{
 			$data			= new stdClass();

 			foreach( get_object_vars($this) as $property => $value )
 			{
 				// We dont want to set private properties
				if( JString::strpos( JString::strtolower($property) , '_') === false )
				{
					$data->$property	= $value;
				}
			}
			return $db->insertObject( '#__community_groups_members' , $data );
		}
		else
		{
			$query	= 'UPDATE ' . $db->nameQuote( '#__community_groups_members' ) . ' '
					. 'SET ' . $db->nameQuote( 'approved' ) . '=' . $db->Quote( $this->approved ) . ', '
					. $db->nameQuote( 'permissions' ) . '=' . $db->Quote( $this->permissions ) . ' '
					. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->groupid ) . ' '
					. 'AND ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $this->memberid );
			$db->setQuery( $query );
			$db->query();
	
			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
				return false;
			}
			return true;
		}
	}

	/**
	 * Approve the member
	 * 
	 **/	 	
	public function approve()
	{
		$db		=& $this->getDBO();
		
		$query	= 'UPDATE ' . $db->nameQuote( '#__community_groups_members' ) . ' SET '
				. $db->nameQuote( 'approved' ) . '=' . $db->Quote( '1' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->groupid ) . ' '
				. 'AND ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $this->memberid );

		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		// Update user group list
		$user = CFactory::getUser( $this->memberid );
		$user->updateGroupList();
		
	}
}