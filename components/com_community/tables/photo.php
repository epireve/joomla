<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_community'.DS.'tables');

class CTablePhoto extends CTableCache
{
	var $id 		= null;
	var $albumid 	= null;
  	var $caption 	= null;
	var $permissions = null;
	var $created 	= null;
	var $thumbnail 	= null;
	var $image 		= null;
	var $creator 	= null;
	var $published 	= null;
	var $original	= null;
	var $filesize	= null;
	var $storage	= 'file';
	var $hits		= 0;
	var $ordering	= null;
	var $status		= null;
	
	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_photos', 'id', $db );
		
		// Get cache object.
 	 	$oCache = CCache::inject($this);
 	 	// Remove photo cache on every delete & store
 	 	$oCache->addMethod(CCache::METHOD_DEL, CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_PHOTOS));
 	 	$oCache->addMethod(CCache::METHOD_STORE, CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_PHOTOS));
	}

	/**
	 *	Allows us to test if the user has access to the album
	 **/	 	
	public function hasAccess( $userId , $permissionType )
	{
		CFactory::load( 'helpers' , 'owner' );
	
		// @rule: For super admin, regardless of what permission, they should be able to access
		if( COwnerHelper::isCommunityAdmin() )
			return true;

		$album	=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $this->albumid );
		
		switch( $album->type )
		{
			case PHOTOS_USER_TYPE:
			
				if( $permissionType == 'delete' )
				{
					return $this->creator == $userId; 
				}
				
			break;
			case PHOTOS_GROUP_TYPE:
				
				CFactory::load( 'models' , 'groups' );
				$group	=&JTable::getInstance( 'Group' , 'CTable' );
				$group->load( $album->groupid );
				
				if( $permissionType == 'delete' )
				{
					return $album->creator == $userId || $group->isAdmin( $userId );
				}
				
				return false;
			break;
		}
	}

	public function getNextPhoto()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_photos' ) . ' '
			    . 'WHERE ' . $db->nameQuote( 'albumid' ) . '=' . $db->Quote( $this->albumid ) . ' '
			    . 'ORDER BY ' . $db->nameQuote( 'ordering' ) . ' ASC '
			    . 'LIMIT 1';

		$db->setQuery( $query );
		$result	= $db->loadObject();
		
		$photo	= JTable::getInstance( 'Photo' , 'CTable' );
		$photo->bind( $result );
		
		return $photo;
	}
	
	public function check()
	{
		CFactory::load( 'helpers', 'string');

		// Santinise data
		$safeHtmlFilter		= CFactory::getInputFilter();
		$this->caption 	= CStringHelper::nl2br($safeHtmlFilter->clean($this->caption));
		
		return true;
	}
	
	/**
	 * Overrides parent store function as we need to clean up some variables
	 **/
	public function store()
	{
		if (!$this->check()) {
			return false;
		}
		
		$this->image		= CString::str_ireplace( '\\' , '/' , $this->image );
		$this->thumbnail	= CString::str_ireplace( '\\' , '/' , $this->thumbnail );
		$this->original		= CString::str_ireplace( '\\' , '/' , $this->original );

		return parent::store();
	}
	
	/**
	 * Delete the photo, the original and the actual resized photos and thumbnails
	 */	 	
	public function delete()
	{
		
		CFactory::load('libraries', 'storage');
		$storage = CStorage::getStorage($this->storage);
		$storage->delete($this->image);
		$storage->delete($this->thumbnail);
		JFile::delete(JPATH_ROOT.DS.$this->original);
		
		// If the original path is empty, we can delete it too
		$files = JFolder::files( dirname(JPATH_ROOT.DS.$this->original) );
		if(empty($files) && !empty($this->original)){
			JFolder::delete( dirname(JPATH_ROOT.DS.$this->original) );
		}
		
		// if the photo is the album cover, set the album cover as default 0
		$album	=& JTable::getInstance('Album', 'CTable');
		$album->load($this->albumid);
		$album->set('photoid', 0);
		$album->store();
		
		// delete the tags
		CFactory::load('libraries', 'phototagging');
		CPhotoTagging::removeTagByPhoto($this->id);
		
		// delete the activities
		CFactory::load('libraries', 'activities');
		CActivities::remove('photos', $this->id);
		
		// delete the comments
		$wall	= CFactory::getModel('wall');
		$wall->deleteAllChildPosts($this->id, 'photos');
		
		return parent::delete();
	}
	/**
	 * Return the exact URL  
	 */	 	
	public function getThumbURI()
	{
		// CDN or local
		$config	= CFactory::getConfig();
		$file   = $this->thumbnail;

		// Remote storage
		if($this->storage != 'file')
		{
			CFactory::load('libraries', 'storage');
			$storage = CStorage::getStorage($this->storage);
			$uri = $storage->getURI($file);
			return $uri;
		}
		
		// Default avatar
		if (empty($file) || !JFile::exists(JPATH_ROOT.DS.$file))
		{
			CFactory::load('helpers', 'template');
			$template = new CTemplateHelper();
			$asset = $template->getTemplateAsset('photo_thumb.png', 'images');
			$uri = $asset->url;
			return $uri;
		}

		// Strip cdn path if exists.
		// Note: At one point, cdn path was stored along with the avatar path
		//       in the db which is the mistake we are trying to rectify here.
		$file   = str_ireplace($config->get('photocdnpath'), '', $file);

		// CDN or local
		$baseUrl = $config->get('photobaseurl') or
		$baseUrl = JURI::root();
		$uri = str_replace('\\', '/', rtrim($baseUrl, '/') . '/' . ltrim($file, '/'));
		return $uri;
	}
	
	/**
	 * Return unencoded version of thr uri
	 */
	public function getRawPhotoURI()
	{
		$album	=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $this->albumid );
		
		$url	= 'index.php?option=com_community&view=photos&task=photo&albumid=' . $album->id;
		$url	.= $album->groupid ? '&groupid=' . $album->groupid : '&userid=' . $album->creator;
		$url	.= '#photoid='.$this->id;
		return $url;
		
	}
	
	public function getPhotoURI($external = false , $xhtml = true)
	{
		return $this->getPhotoLink($external, $xhtml);
	}
	
	public function getPhotoLink( $external = false , $xhtml = true )
	{
		$url = $this->getRawPhotoURI();
		
		if( $external )
		{
			return CRoute::getExternalURL( $url ) . '#photoid=' . $this->id;
		}

		return CRoute::_( $url , $xhtml ) . '#photoid=' . $this->id;
	}
	
	/**
	 * Return the exist URL to be displayed.
	 * @param size string, (normal, origianl, small)	 
	 */	 	
	public function getImageURI($size = 'normal')
	{	
		CFactory::load('libraries', 'storage');
		$uri = '';
		if($this->storage != 'file')
		{
			$storage = CStorage::getStorage($this->storage);
			$uri = $storage->getURI($this->image);
		}
		else
		{
			$config		= CFactory::getConfig();
			$baseUrl	= $config->get('photobaseurl');

			if( !empty( $baseUrl) )
			{
				if(JFile::exists (JPATH_ROOT . DS .$this->image))
				{
					//$uri	= rtrim( $baseUrl , '/' ) . '/' . $this->image;
					
					// Strip cdn path if necessary
					$path = $this->image;		
					
					$cdnpath = $config->get('photocdnpath');
					if($cdnpath)
					{
						$path = str_replace($cdnpath, '',$path);
					}
					$path	= ltrim( $path , '/' );
					$uri 	=  rtrim( $baseUrl, '/' ) . '/' . $path;
				}
				else
				{
					$uri	= JURI::root() . 'index.php?option=com_community&view=photos&task=showimage&tmpl=component&imgid='. $this->image;
				}
			}
			else
			{
				// If the resized photos exist, use it instead
				if(JFile::exists (JPATH_ROOT . DS .$this->image))
				{
					$uri = JURI::root() .'/'. $this->image;
				}
				else 
				{
					$uri = JURI::root() . 'index.php?option=com_community&view=photos&task=showimage&tmpl=component&imgid='. $this->image;
				}
			}
		}
		return $uri;
	}
	
	/**
	 * Return the URI of the original photo
	 */	 	
	public function getOriginalURI()
	{
		if( JFile::exists( JPATH_ROOT . DS . $this->original) )
		{
			return rtrim( JURI::root() , '/' ) . '/' . $this->original;
		}
		
		// If original url was deleted, we use the resized image as the original photo
		return rtrim( JURI::root() , '/' ) . '/' . $this->image;
	}
	
	// Load the Photo object given the image path
	public function loadFromImgPath($path)
	{
		$db	=& JFactory::getDBO();
		
		$strSQL	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__community_photos') 
				. 'WHERE ' . $db->nameQuote('image') . '=' . $db->Quote($path) ;

		//echo $strSQL;
		$db->setQuery($strSQL);
		$result	= $db->loadObject();
		
		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}

		// Backward compatibility because anything prior to this version uses id
		// @since 1.6
		if( !$result )
		{
			$id		= $path;
			$this->load( $id );
			return $id;
		}

		$this->load($result->id);
		return $result;
	}
	
	/**
	 * Override parent's hit method as we don't really want to
	 * carefully increment it every time a photo is viewed.
	 **/
	public function hit()
	{
		$session =& JFactory::getSession();

		//@rule: We need to test if the album for this photo also has the hits.
		// otherwise it would not tally when photo has large hits but not the album.
		// The album hit() will take care of the album hits session correctly.
		$album  =& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $this->albumid );
		$album->hit();
		
		if( $session->get('photo-view-'. $this->id, false) == false )
		{
			parent::hit();
			$session->set('photo-view-'. $this->id, true);
		}
	}

	/**
	 * Set the ordering of the current photo based on the maximum
	 * ordering in the photos table for the current album.	 
	 **/
	public function setOrdering()
	{
		$db	=& JFactory::getDBO();

		$this->ordering	= $this->getNextOrder( $db->nameQuote( 'albumid' ) . '=' . $db->Quote( $this->albumid ) );
	}

        public function lastId()
        {
            $db	=& JFactory::getDBO();

            $query = 'SELECT '. $db->nameQuote('id') .' FROM '. $db->nameQuote('#__community_photos'). ' ORDER BY ' .$db->nameQuote('id') .' DESC' ;
           
            $db->setQuery( $query );
            $result	= $db->loadObject();

            return $result->id;
        }
}