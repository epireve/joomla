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
class CommunityTableGroups extends JTable
{
	var $id				= null;
	var $published		= null;
	var $ownerid		= null;
	var $categoryid		= null;
	var $name			= null;
	var $description	= null;
	var $email			= null;
	var $website		= null;
	var $approvals		= null;
	var $created		= null;
	var $avatar			= null;
	var $thumb			= null;
	var $discusscount	= null;
	var $membercount	= null;
	var $wallcount		= null;
	
	public function __construct(&$db)
	{
		parent::__construct('#__community_groups','id', $db);
	}

	public function getWallCount()
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_wall') . ' '
				. 'WHERE ' . $db->nameQuote( 'contentid' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'groups' ) . ' '
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
	
	/**
	 *  Deprecated since 2.2.x
	 *  Use CTableGroup instead
	 */
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

	/**
	 *  Deprecated since 2.2.x
	 *  Use CTableGroup instead
	 */
	public function addMembersCount( $groupId )
	{
		$db		=& $this->getDBO();
				
		$query	= 'UPDATE ' . $db->nameQuote( '#__community_groups' )
				. 'SET ' . $db->nameQuote( 'membercount' ) . '= (' . $db->nameQuote('membercount'). ' +1) '
				. 'WHERE ' . $db->nameQuote('id') . '=' . $db->Quote( $groupId );
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

	/**
	 * Return the full URL path for the specific image
	 * 
	 * @param	string	$type	The type of avatar to look for 'thumb' or 'avatar'. Deprecated since 1.8 
	 * @return string	The avatar's URI
	 **/
	public function getAvatar( $type = 'thumb' )
	{
		if( $type == 'thumb' )
		{
			return $this->getThumbAvatar();
		}
		
		// Get the avatar path. Some maintance/cleaning work: We no longer store
		// the default avatar in db. If the default avatar is found, we reset it
		// to empty. In next release, we'll rewrite this portion accordingly.
		// We allow the default avatar to be template specific.
		if ($this->avatar == 'components/com_community/assets/group.jpg')
		{
			$this->avatar = '';
			$this->store();
		}
		CFactory::load('helpers', 'url');
		$avatar	= CUrlHelper::avatarURI($this->avatar, 'groupAvatar.png');
		
		return $avatar;
	}

	public function getThumbAvatar()
	{
		if ($this->thumb == 'components/com_community/assets/group_thumb.jpg')
		{
			$this->thumb = '';
			$this->store();
		}
		CFactory::load('helpers', 'url');
		$thumb	= CUrlHelper::avatarURI($this->thumb, 'groupThumbAvatar.png');
		
		return $thumb;
	}
}