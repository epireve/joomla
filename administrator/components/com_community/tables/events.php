<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Jom Social Table Model
 */
class CommunityTableEvents extends JTable
{
	var $id				= null;
	var $catid			= null;
	var $contentid		= null;
	var $type			= null;
	var $title			= null;
	var $location		= null;
	var $description	= null;
	var $creator		= null;
	var $startdate		= null;
	var $enddate		= null;
	var $permission		= null;
	var $avatar			= null;
	var $invitedcount	= null;
	var $confirmedcount	= null;
	var $declinedcount 	= null;
	var $maybecount		= null;
	var $created		= null;
	var $hits			= null;
	var $published		= null;
	
	public function __construct(&$db)
	{
		parent::__construct('#__community_events','id', $db);
	}

	public function getWallCount()
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_wall') . ' '
				. 'WHERE ' . $db->nameQuote( 'contentid' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'events' ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );

		$db->setQuery( $query );
		$count	= $db->loadResult();
		
		return $count;
	}

	public function getDiscussCount()
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_groups_discuss') . ' '
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->id );

		$db->setQuery( $query );
		$count	= $db->loadResult();
		
		return $count;
	}

	public function isMember( $memberId , $groupId )
	{
		$db 		=& JFactory::getDBO();
		$query 	= 'SELECT * FROM ' . $db->nameQuote( '#__community_groups_members' ) . ' ' 
					. 'WHERE ' . $db->nameQuote( 'memberid' ) . '=' . $db->Quote( $memberId ) . ' '
					. 'AND ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $groupId );

		$db->setQuery( $query );
		
		$count 	= ( $db->loadResult() > 0 ) ? true : false;
		return $count;
	}
	
	public function addMember( $data )
	{
		$db	=& $this->getDBO();
		
		// Test if user if already exists
		if( !$this->isMember($data->memberid, $data->groupid) )
		{
			$db->insertObject('#__community_groups_members' , $data);
		}
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		return $data;
	}

	public function addMembersCount( $groupId )
	{
		$db		=& $this->getDBO();
				
		$query	= 'UPDATE ' . $db->nameQuote( '#__community_groups' )
				. 'SET ' . $db->nameQuote( 'membercount' ) . '= (membercount +1) '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $groupId );
		$db->setQuery( $query );
		$db->query();				

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}
		
	public function getMembersCount()
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_groups_members') . ' '
				. 'WHERE ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $this->id )
				. 'AND ' . $db->nameQuote( 'approved' ) . '=' . $db->Quote( '1' );

		$db->setQuery( $query );
		$count	= $db->loadResult();
		
		return $count;
	}
}