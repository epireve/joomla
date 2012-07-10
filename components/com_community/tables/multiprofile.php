<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableMultiProfile extends JTable
{
	var $id			= null;
	var $name		= null;
	var $description= null;
	var $approvals	= null;
	var $published	= null;
	var $avatar		= null;
	var $thumb		= null;
	var $created	= null;
  	var $watermark	= null;
  	var $watermark_hash		= null;
  	var $watermark_location	= null;
  	var $create_groups	= null;
        var $create_events	= null;
  	var $ordering		= null;
  	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_profiles', 'id', $db );
	}
	
	public function getWatermark()
	{
		return JURI::root() . $this->watermark;
	}
	
	/**
	 * Retrieve the profile type avatar
	 **/	 	
	public function getThumbAvatar()
	{
		if( empty($this->thumb) )
		{
			return JURI::root() . DEFAULT_USER_THUMB;
		}
		return JURI::root() . $this->thumb;		
	}

	/**
	 * Retrieve the profile type avatar
	 **/	 	
	public function getAvatar()
	{
		return JURI::root() . $this->avatar;		
	}

	public function getName()
	{
	    if( empty( $this->name ) )
	    {
	        return JText::_('COM_COMMUNITY_DEFAULT_PROFILE_TYPE');
		}
		
		return $this->name;
	}
	/**
	 * Retrieve a multiprofile mapping for a given multi profile.
	 * 
	 * @return	Object	An object of #__community_multiprofiles_fields	 	 
	 **/
	public function getChild( $fieldId , $multiprofileId )
	{		
		$db		=& JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote('#__community_profiles_fields')
				. ' WHERE ' . $db->nameQuote( 'field_id' ) . '=' . $db->Quote( $fieldId )
				. ' AND ' . $db->nameQuote( 'parent' ) . '=' . $db->Quote( $multiprofileId );
		$db->setQuery( $query );
		
		$result	= $db->loadObject();

		return $result;
	}
		 	
	/**
	 * Override paren't store method so we can do some checking with the watermarks.
	 * 
	 * @return	bool	True on success.	 	 
	 **/
	public function store()
	{
		if( !$this->id )
		{
			$this->ordering		= parent::getNextOrder();
		}
		
		parent::store();
	}
	
	/**
	 * Override parents delete method
	 **/	 	
	public function delete()
	{
		parent::delete();
		
		// @rule: Deleting a multiple profile should revert all users using it to the default profile
		$db		=& JFactory::getDBO();
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET ' . $db->nameQuote('profile_id') . '=' . $db->Quote( COMMUNITY_DEFAULT_PROFILE ) . ' '
				. 'WHERE ' . $db->nameQuote('profile_id') . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$db->Query();
		
		// @rule: Delete all childs related to this multiprofile
		$this->deleteChilds();
	}
	
	/**
	 * Delete all existing childs in commmunity_multiprofiles_fields
	 * 
	 * @return	bool	True on success.	 	 
	 **/
	public function deleteChilds()
	{
		$db		=& JFactory::getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote('#__community_profiles_fields') . ' WHERE ' . $db->nameQuote( 'parent' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$db->Query();
		
		return true;
	}
	
	public function isChild( $fieldId )
	{
		if( $this->id == 0 )
			return false;

		if( $this->getChild( $fieldId , $this->id ) )
		{
			return true;
		}

		return false;
	}
	
	/**
	 * Checks if the current profile type has users already assigned.
	 * 
	 * @params
	 * @return	Boolean	True if there are still users assigned to this profile type.	 	 	 
	 **/	 	
	public function hasUsers()
	{
		$db		=& JFactory::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__community_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'profile_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		
		$result	= $db->loadResult() > 0 ? true : false;
		
		return $result;
	}
	
	/**
	 * Check current avatar's hash
	 **/	
	public function isHashMatched( $profileType , $hash )
	{
		// @rule: backward compatibility. In 2.0, the hash column is a new column.
		if( empty($hash ) )
		{
			return true;
		}
		
		static $types	= array();
		$match			= true;

		if( empty($types) )
		{
			$model	= CFactory::getModel( 'Profile' );
			$rows	= $model->getProfileTypes();
			
			if( $rows )
			{
				foreach( $rows as $row )
				{
					$types[ $row->id ]	= $row;
				}
			}
		}

		if( isset( $types[ $profileType ] ) )
		{
			$match	= $types[ $profileType ]->watermark_hash == $hash;
		}

		return $match;
	}
	
	/**
	 * Updates existing default image that is already stored in the community_users table.
	 * 
	 * @param	String	$type	Type of image, thumb or avatar.
	 * @param	String	$oldPath	The path for the old image.
	 * 
	 * @return	Boolean		True on success false otherwise.	 	 
	 **/	 	
	public function updateUserDefaultImage( $type , $oldPath )
	{
		$db		=& JFactory::getDBO();
		$query	= 'UPDATE ' . $db->nameQuote( '#__community_users' ) . ' '
				. 'SET ' . $db->nameQuote( $type ) . '=' . $db->Quote( $this->$type ) . ' '
				. 'WHERE ' . $db->nameQuote( $type ) . '=' . $db->Quote( $oldPath );
		$db->setQuery( $query );
		$db->Query();

		// Remove the old files.
		$oldImagePath = JPATH_ROOT . DS . CString::str_ireplace( '/' , DS , $oldPath );
		
		if( JFile::exists( $oldImagePath ) )
		{
			JFile::delete( $oldImagePath );
		}

		if($db->getErrorNum())
		{
			return false;
	    }
	    
	    return true;
	}
	
	public function updateUserThumb( $user , $hashName )
	{
		$this->_updateUserWatermark( $user , 'thumb' , $hashName );
	}

	public function updateUserAvatar( $user , $hashName )
	{
		$this->_updateUserWatermark( $user , 'avatar' , $hashName );
	}
	
	private function _updateUserWatermark( $user , $type , $hashName )
	{
		$config		= CFactory::getConfig();
		
		// @rule: This is the original avatar path
		CFactory::load( 'helpers' , 'image' );
		$userImageType	= '_' . $type;
		$data			= @getimagesize( JPATH_ROOT . DS . CString::str_ireplace( '/' , DS , $user->$userImageType ) );
		$original		= JPATH_ROOT . DS . 'images' . DS . 'watermarks' . DS . 'original' . DS . md5( $user->id . '_' . $type ) . CImageHelper::getExtension( $data[ 'mime' ] );
		
		if( !$config->get('profile_multiprofile') || !JFile::exists( $original ) )
		{
			return false;
		}

		static $types	= array();

		if( empty($types) )
		{
			$model	= CFactory::getModel( 'Profile' );
			$rows	= $model->getProfileTypes();
			
			if( $rows )
			{
				foreach( $rows as $row )
				{
					$types[ $row->id ]	= $row;
				}
			}
		}
		$model			= CFactory::getModel( 'User' );


		if( isset( $types[ $user->_profile_id ] ) )
		{
			// Bind the data to the current object so we can access it here.
			$this->bind( $types[ $user->_profile_id ] );

			// Path to the watermark image.
			$watermarkPath	= JPATH_ROOT . DS . CString::str_ireplace('/' , DS , $this->watermark);

			// Retrieve original image info
			$originalData	= getimagesize( $original );

			// Generate image file name.
			$fileName	= ( $type == 'thumb' ) ? 'thumb_' : '';
			$fileName	.= $hashName;
			$fileName	.= CImageHelper::getExtension( $originalData[ 'mime' ] );
			
			// Absolute path to the image (local)
			$newImagePath	= JPATH_ROOT . DS . $config->getString('imagefolder') . DS . 'avatar' . DS . $fileName;

			// Relative path to the image (uri)
			$newImageUri	= $config->getString('imagefolder') . '/avatar/' . $fileName;

			// Retrieve the height and width for watermark and original image.
			list( $watermarkWidth , $watermarkHeight )	= getimagesize( $watermarkPath );
			list( $originalWidth , $originalHeight )	= getimagesize( $original );
			
			// Retrieve the proper coordinates to the watermark location
			$position	= CImageHelper::getPositions( $this->watermark_location , $originalWidth , $originalHeight , $watermarkWidth , $watermarkHeight );

			// Create the new image with the watermarks.
			CImageHelper::addWatermark( $original , $newImagePath , $originalData[ 'mime' ] , $watermarkPath , $position->x , $position->y , false );
			$model->setImage( $user->id , $newImageUri , $type );

			// Remove the user's old image
			$oldFile	= JPATH_ROOT . DS . CString::str_ireplace( '/' , DS , $user->$userImageType );	
			
			if( JFile::exists( $oldFile ) )
			{	
				JFile::delete($oldFile);
			}
			
			// We need to update the property in CUser as well otherwise when we save the hash, it'll
			// use the old user avatar.
			$user->set( $userImageType , $newImageUri );
			
			// We need to restore the storage method.
			$user->set( '_storage' , 'file' );
			
			// Update the watermark hash with the latest hash
			$user->set( '_watermark_hash' , $this->watermark_hash );
			$user->save();
			
		}
		return true;
	}
}