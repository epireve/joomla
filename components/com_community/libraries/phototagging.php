<?php
/**
 * @copyright (C) 2009 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * Allow any part of the system to add user reporting feature.  
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Set the tables path
//JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'tables' );
require_once(JPATH_ROOT .DS. 'components' .DS. 'com_community' .DS. 'libraries' .DS. 'core.php');

class CPhotoTagging
{
	var $friendList	= null;
	var $_error		= null;
	
	/**
	 * private method. Used to append zero into a string.
	 */	 	
	public function _appendZero($val)
	{
		if(JString::strlen($val) == 1)
		{
			return '00' . $val;	
		}
		else if(JString::strlen($val) == 2) 
		{
			return '0' . $val;	
		}
		else
		{
			return $val;
		}
	}
	
	/**
	 *
	 */
	public function getError()
	{
		return $this->_error;
	} 
	 	 	 	
	
	/**
	 *
	 *
	 */
	public function isTagExists($photoId, $userId)
	{
		CFactory::load( 'models' , 'phototagging' );
		$tagModel = CFactory::getModel('phototagging');
		
		//reset the error message.
		$this->_error	= null;
		
		if($tagModel->isTagExists($photoId, $userId))
		{
			$this->_error	= JText::_('COM_COMMUNITY_PHOTO_TAG_EXIST');
			return true;
		}	
	} 
	
	/**
	 * Method use to create a new tag on a photo 	 
	 * @object - $tagObj
	 * @param	$photoId - Current photo id being tag.
	 * @param	$userId - User that tagged into photo	 
	 * @param	$posX - x-coodinate, in percentage value (3 digit)
	 * @param	$posY - y-coodinate, in percentage value (3 digit)
	 * @param	$w - width (optional) (3 digit)
	 * @param	$h - height (optional) (3 digit)	 	 	  	 
	 **/	
	//function addTag( $photoId, $userId, $posX, $posY, $w=0, $h=0)
	public function addTag( $tagObj )
	{
		CFactory::load( 'models' , 'phototagging' );
		$tagModel = CFactory::getModel('phototagging');
		
		//reset the error message.
		$this->_error	= null;
		
		if($tagModel->isTagExists($tagObj->photoId, $tagObj->userId))
		{
			$this->_error	= JText::_('COM_COMMUNITY_PHOTO_TAG_EXIST');
			return 0;
		}
		
		// form the position string.
		// format : xxx.xx,yyy.yy,www.ww,hhh.hh
		$position	= round($tagObj->posX, 2) . ',' . round($tagObj->posY, 2) . ',' . round($tagObj->width, 2) . ',' . round($tagObj->height, 2);				
		$tagId		= 0;
		
		if($tagModel->addTag($tagObj->photoId , $tagObj->userId , $position)->return_value['addTag'])
		{						
			$tagId	= $tagModel->getTagId($tagObj->photoId, $tagObj->userId);
		}
		else
		{
			$this->_error	= $tagModel->getError();
		}
		
		return $tagId;
	}
	
	/**
	 * Method use to create a remove a tagged user from photo 	 
	 * 
	 * @param	$photoId - Current photo id being tag.
	 * @param	$userId - User that tagged into photo.
	 **/	
	public function removeTag( $photoId, $userId)
	{
		CFactory::load( 'models' , 'phototagging' );
		$tagModel = CFactory::getModel('phototagging');
		
		//reset the error message.
		$this->_error	= null;		
		
		if($tagModel->removeTag( $photoId, $userId ))
		{
			return true;
		}
		else
		{
			$this->_error	= $tagModel->getError();
			return false;
		}
	}
	
	public function removeTagByPhoto($photoId)
	{
		CFactory::load( 'models' , 'phototagging' );
		$tagModel = CFactory::getModel('phototagging');
		
		//reset the error message.
		$this->_error	= null;		
		
		if($tagModel->removeTagByPhoto( $photoId ))
		{
			return true;
		}
		else
		{
			$this->_error	= $tagModel->getError();
			return false;
		}
	}
	
	/**
	 * Method use to get all the tagged users from a photo  	 
	 * 
	 * @param	$photoId - Current photo id being tag.
	 * @param	$userId - User that tagged into photo.
	 **/	
	public function getTaggedList( $photoId )
	{
		CFactory::load( 'models' , 'phototagging' );
		$tagModel = CFactory::getModel('phototagging');
		
		$config	= CFactory::getConfig();
		
		$taggedList	= $tagModel->getTaggedList( $photoId );
		$result		= null;
		
		for($i=0; $i<count($taggedList);$i++)
		{
			$tagItem			=& $taggedList[$i];
			
			// format : xxx.xx,yyy.yy,www.ww,hhh.hh
			$position			= explode(',', $tagItem->position);
			
			$tagItem->posx		= $position[0];
			$tagItem->posy		= $position[1];
			$tagItem->width		= $position[2];
			$tagItem->height	= $position[3];
		}
				
		return $taggedList;
	}
	
	/**
	 * Method use to get all friend list belong to current logged in user which
	 * excluded those already tagged in the current viewing photo
	 * @param	$photoId - Current photo id being tag.
	 * @param	$userId - User that tagged into photo.
	 **/	
	public function getFriendList( $photoId )
	{
		if(empty($this->friendList))
		{
			CFactory::load( 'models' , 'phototagging' );
			$tagModel = CFactory::getModel('phototagging');
			$this->friendList	= $tagModel->getFriendList($photoId);
		}

		return $this->friendList;
	}		

}
?>