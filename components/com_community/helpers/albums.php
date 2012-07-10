<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

abstract class CAlbumsHelperHandler
{
	protected $type 	= '';
	protected $model 	= '';
	protected $album	= '';
	protected $my		= '';
	
	const PRIVACY_PUBLIC	= '0';
	const PRIVACY_MEMBERS	= '20';
	const PRIVACY_FRIENDS	= '30';
	const PRIVACY_PRIVATE	= '40';
	
	public function __construct( CTableAlbum $album )
	{
		$this->my		= CFactory::getUser();
		$this->model	= CFactory::getModel( 'photos' );
		$this->album	= $album;
	}

	abstract public function isPublic();
	abstract public function showActivity();
}

class CAlbumsGroupHelperHandler extends CAlbumsHelperHandler
{
	public function __construct( CTableAlbum $album )
	{
		parent::__construct( $album );
	}

	/**
	 * Determines whether the current album is public or not
	 * 
	 * @params
	 * @return Boolean	True upon success	 	 	 
	 **/		
	public function isPublic( )
	{
		$my		= CFactory::getUser();
		$group	=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $this->album->groupid );
		
		if( $group->approvals == COMMUNITY_PRIVATE_GROUP && !$group->isMember($my->id) )
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Determines whether the activity stream content for the photo related items should be shown or 
	 * hidden depending on the user's privacy settings
	 * @params
	 * @return Boolean	True upon success
	 **/	
	public function showActivity()
	{
		$group	=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $this->album->groupid );
		
		return $group->approvals == COMMUNITY_PUBLIC_GROUP;
	}
}

class CAlbumsUserHelperHandler extends CAlbumsHelperHandler
{
	public function __construct( CTableAlbum $album )
	{
		parent::__construct( $album );
	}

	/**
	 * Determines whether the current album is public or not
	 * 
	 * @params
	 * @return Boolean	True upon success	 	 	 
	 **/	
	public function isPublic( )
	{
		$my	= CFactory::getUser();
		
		switch( $this->album->permissions )
		{
			case self::PRIVACY_PRIVATE:
				return $my->id == $this->album->creator;
				break;
			case self::PRIVACY_FRIENDS:
				CFactory::load( 'helpers' , 'friends' );
				
				return CFriendsHelper::isConnected( $my->id , $this->album->creator );
				break;
			case self::PRIVACY_MEMBERS:
				
				if( $my->id != 0 )
				{
					return true;
				}
				
				break;
			case self::PRIVACY_PUBLIC:
				return true;
				break;
		}
		return false;
	}
	
	/**
	 * Determines whether the activity stream content for the photo related items should be shown or 
	 * hidden depending on the user's privacy settings
	 * @params
	 * @return Boolean	True upon success
	 **/
	public function showActivity()
	{
		$permission	= $this->album->permissions;
		$my			= CFactory::getUser();
		
		switch( $permission )
		{
			case PRIVACY_MEMBERS:
				$show	= $my->id != 0;
			break;
			case PRIVACY_FRIENDS:
				CFactory::load( 'helpers' , 'friends' );
				$show	= CFriendsHelper::isConnected( $my->id , $this->album->creator );
			break;
			case PRIVACY_PRIVATE:
				$show	= $my->id == $this->album->creator;
			break;				
			case PRIVACY_PUBLIC:
			default:
				$show	= true;
			break;
		}
		return $show;
	}
}

class CAlbumsHelper
{
	var $handler	= '';
	var $id			= '';

	/**
	 *
	 * @param mixed $id either album id OR CTableAlbum object
	 *
	 */
	public function __construct( $id )
	{
		$this->id		= $id;
		$this->handler	= $this->_getHandler();
	}
	
	public function isPublic()
	{
		return $this->handler->isPublic();
	}
	
	public function showActivity()
	{
		return $this->handler->showActivity();
	}
	
	private function _getHandler()
	{
		// The $this->id could be a CTableAlbum object, in which case,
		// No need to load, just link it back
		if( is_object($this->id) )
		{
			$album = $this->id;
		}
		else
		{
			$album	=& JTable::getInstance( 'Album' , 'CTable' );
			$album->load( $this->id );
		}
		

		if( $album->type == PHOTOS_USER_TYPE )
		{
			$handler = new CAlbumsUserHelperHandler( $album );
		}
		else
		{
			$handler = new CAlbumsGroupHelperHandler( $album );
		}
		
		return $handler;
	}
}