<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableGroupInvite extends JTable
{
	var $groupid	= null;
	var $userid		= null;
	var $creator	= null;
	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_groups_invite' , 'groupid' , $db );
	}
	
	public function isOwner()
	{
		$my		= CFactory::getUser();

		return $my->id == $this->userid;
	}
	
	public function exists()
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_groups_invite' )
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->groupid ) . ' '
				. 'AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $this->userid );
				
		$db->setQuery( $query );
		
		$return	= ( $db->loadResult() >= 1 ) ? true : false;

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}		
		return $return;
	}

	public function load( $groupId , $userId )
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $groupId ) . ' '
				. 'AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userId );
		$db->setQuery( $query );

		$result	= $db->loadAssoc();
		
		$this->bind( $result );
		
		if( $result )
			return true;
		
		return false;
	}

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
			return $db->insertObject( '#__community_groups_invite' , $data );
		}
		else
		{
			$query	= 'UPDATE ' . $db->nameQuote( '#__community_groups_invite' ) . ' '
					. 'SET ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->groupid ) . ', '
					. $db->nameQuote( 'userid' ) . '=' . $db->Quote( $this->userid ) . ' ,'
					. $db->nameQuote( 'creator' ) . '=' . $db->Quote( $this->creator ) . ' '
					. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->groupid ) . ' '
					. 'AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $this->userid );
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
	
	public function delete()
	{
		$db		=& $this->getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote( $this->_tbl ) . ' WHERE '
				. $db->nameQuote('groupid'). '=' . $db->Quote( $this->groupid ) . ' AND '
				. $db->nameQuote('userid') .'=' . $db->Quote( $this->userid );
		$db->setQuery( $query );
		return $db->Query();
	}
}