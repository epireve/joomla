<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');
// Include interface definition
CFactory::load( 'models' , 'tags' );

class CTableAlbum 
	extends CTableCache
	implements CTaggable_Item
{
	var $id 			= null;
	
	/** Album cover , FK to the photo id **/
	var $photoid 		= null;
	var $creator		= null;
  	var $name			= null;
  	var $description	= null;
	var $permissions	= null;
	var $created		= null;
	var $path			= null;
	var $type			= null;
	var $groupid		= null;
	var $location		= null;
	var $latitude		= null;
	var $longitude		= null;
	var $hits           = 0;
	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_photos_albums', 'id', $db );
		
		// General Permission as initial permission
		$this->permissions	= 0;
		$my					= CFactory::getUser();
		$params				= $my->getParams();
		if ($my->id > 0 && $params->get('privacyPhotoView'))
		{
			$this->permissions	= $params->get('privacyPhotoView');
		}
		
		// Get cache object.
 	 	$oCache = CCache::inject($this);
 	 	// Remove photo cache on album delete.
 	 	$oCache->addMethod(CCache::METHOD_DEL, CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_PHOTOS));
 	 	$oCache->addMethod(CCache::METHOD_STORE, CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_ALBUMS));
	}

	/**
	 *	Allows us to test if the user has access to the album
	 **/	 	
	public function hasAccess( $userId , $permissionType )
	{
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'helpers' , 'group' );

		// @rule: For super admin, regardless of what permission, they should be able to access
		if( COwnerHelper::isCommunityAdmin() )
			return true;

		switch( $this->type )
		{
			case PHOTOS_USER_TYPE:
			
				if( $permissionType == 'upload' )
				{
					return $this->creator == $userId; 
				}

				if( $permissionType == 'deletephotos' )
				{
					return $this->creator == $userId;
				}
								
			break;
			case PHOTOS_GROUP_TYPE:
				CFactory::load( 'models' , 'groups' );
				$group	=&JTable::getInstance( 'Group' , 'CTable' );
				$group->load( $this->groupid );

				if( $permissionType == 'upload' )
				{				
					return CGroupHelper::allowManagePhoto( $group->id );
				}
				
				if( $permissionType == 'deletephotos' )
				{
					return $this->creator == $userId || $group->isAdmin( $userId );
				}
				
				return false;
			break;
		}
	}
	
	/**
	 * Return the path to the cover photo
	 * If no cover photo is specifies, we just load the first photo in the album 	 
	 */	 	
	public function getCoverThumbPath()
	{
		$photoModel = CFactory::getModel('photos');
		$photo = $photoModel->getPhoto($this->photoid);
		
		// If this photo doesn't exist, we need to select a new valid one
		// @todo: test and see if the photo actually exist
		
		
		return $photo->getThumbURI();
	}
	
	/*
	 * @since 2.4
	 * @return the path of the photos of the album cover in full size
	 */
	 public function getRawCoverThumbPath(){
		$photoModel = CFactory::getModel('photos');
		$photo = $photoModel->getPhoto($this->photoid);
		
		return $photo->getImageURI();
	 }
	
	public function getCoverThumbURI()
	{
		return $this->getCoverThumbPath();
	}
	
	/**
	 * Return the number of photos in this album
	 */	 	
	public function getPhotosCount()
	{
		$model = CFactory::getModel('photos');
		return $model->getPhotosCount();
	}
	
	public function getURI()
	{
		$uri		= 'index.php?option=com_community&view=photos&task=album&albumid=' . $this->id;
		
		switch( $this->type )
		{
			case PHOTOS_USER_TYPE:
				$uri	.= '&userid=' . $this->creator;
				break;
			case PHOTOS_GROUP_TYPE:
				$uri	.= '&groupid=' . $this->groupid;
				break;
		}
		
		return $uri;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * Delete an album
	 * Set all photo within the album to have albumid = 0
	 * Do not yet delete the photos, this could be very slow on an album that 
	 * has huge amount of photos	 	 	 
	 */	 	
	public function delete()
	{
		
		$db	=& JFactory::getDBO();		
		$strSQL	= 'UPDATE ' . $db->nameQuote('#__community_photos')
				.' SET ' . $db->nameQuote('albumid') . '=' . $db->Quote(0)
				.' WHERE ' . $db->nameQuote('albumid') . '=' . $db->Quote($this->id) ;

		$db->setQuery($strSQL);
		$result	= $db->query();
					
		// The whole local folder should be deleted, regardless of the storage type
		// BUT some old version of JomSocial might store other photo in the same 
		// folder, we check in db first
		$strSQL	= 'SELECT count(*) FROM ' . $db->nameQuote('#__community_photos')
				.' WHERE ' . $db->nameQuote('image'). ' LIKE ' . $db->Quote('%'.dirname( $this->path ).'%') ;
		$db->setQuery($strSQL);
		$result	= $db->loadResult();

		if($result == 0)
		{
			JFolder::delete( JPATH_ROOT . DS . rtrim( $this->path , '/' ) . DS . $this->id );
		}
		
		// We need to delete all activity stream related to this album
		CFactory::load('libraries', 'activities');
		CActivityStream::remove('photos' , $this->id );
		
		return parent::delete();
	}
	
	public function check()
	{
		// Santinise data
		$safeHtmlFilter		= CFactory::getInputFilter();
		$this->name			= $safeHtmlFilter->clean($this->name);
		$this->description 	= $safeHtmlFilter->clean($this->description);
		
		return true;
	}
	
	public function store()
	{
		if (!$this->check()) {
			return false;
		}
		
		return parent::store();
	}

	/**
	 * Override parent's hit method as we don't really want to
	 * carefully increment it every time a photo is viewed.
	 **/
	public function hit()
	{
		$session =& JFactory::getSession();

		if( $session->get('album-view-'. $this->id, false) == false )
		{
			parent::hit();
			$session->set('album-view-'. $this->id, true);
		}
	}

	/**
	 * Return the title of the object
	 */
	public function tagGetTitle()
	{
		return $this->description;
	}

	/**
	 * Return the HTML summary of the object
	 */
	public function tagGetHtml()
	{
		return '';
	}

	/**
	 * Return the internal link of the object
	 *
	 */
	public function tagGetLink()
	{
		return $this->getViewURI();
	}

	/**
	 * Return true if the user is allow to modify the tag
	 *
	 */
	public function tagAllow($userid)
	{
		// @todo: only admin and album owner can edit this
		return true;
	}
}