<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CLimitsHelper
{
	static public function exceededPhotoUpload( $id , $type = PHOTOS_USER_TYPE )
	{
		// @rule: Administrator should not be restricted
		CFactory::load( 'helpers' , 'owner' );
		if( COwnerHelper::isCommunityAdmin() )
		{
			return false;
		}

		// Get the configuration for uploader tool
		$config			= CFactory::getConfig();
		$model			= CFactory::getModel( 'photos' );
		$photoLimit		= $config->get( 'photouploadlimit' );
		
		if( $type == PHOTOS_GROUP_TYPE )
			$photoLimit	= $config->get('groupphotouploadlimit');
	
		$totalPhotos	= $model->getPhotosCount( $id , $type );
	
		if( $totalPhotos >= $photoLimit && $photoLimit != 0 )
		{
			return true;
		}
		
		return false;
	}
	
	static public function exceededGroupCreation( $userId )
	{
		// Get the configuration for group creation
		$config		= CFactory::getConfig();
		$model		= CFactory::getModel( 'groups' );
			
		$groupLimit	= $config->get('groupcreatelimit');
		$totalGroup	= $model->getGroupsCreationCount($userId);		
	
		if($totalGroup >= $groupLimit && $groupLimit != 0 )
		{
			return true;
		}
		
		return false;
	}
	
	static public function exceededVideoUpload( $userId, $type = VIDEO_USER_TYPE )
	{
		$config		= CFactory::getConfig();
		$model		= CFactory::getModel( 'videos' );
		
		if( $type == VIDEO_GROUP_TYPE )
		{
			$videoLimit	= $config->get('groupvideouploadlimit');
			$totalVideos= $model->getVideosCount( $userId , VIDEO_GROUP_TYPE );
		} else {
			$videoLimit	= $config->get( 'videouploadlimit' );
			$totalVideos= $model->getVideosCount( $userId , VIDEO_USER_TYPE );
		}
	
		if( $totalVideos >= $videoLimit && $videoLimit != 0 )
		{
			return true;
		}
		
		return false;
	}

	static public function exceededEventCreation( $userId )
	{
		// Get the configuration for group creation
		$config		= CFactory::getConfig();
		$model		= CFactory::getModel( 'events' );

		$eventLimit	= $config->get('eventcreatelimit');
		$totalEvent	= $model->getEventsCreationCount($userId);

		if($totalEvent >= $eventLimit && $eventLimit != 0 )
		{
			return true;
		}

		return false;
	}
}

/**
 * Deprecated since 1.8
 * Use CLimitsHelper::exceededPhotoUpload instead. 
 */
function cExceededPhotoUploadLimit( $id , $type = PHOTOS_USER_TYPE )
{
	return CLimitsHelper::exceededPhotoUpload( $id , $type );
}

/**
 * Deprecated since 1.8
 * Use CLimitsHelper::exceededGroupCreation instead. 
 */
function cExceededGroupCreationLimit( $userId )
{
	return CLimitsHelper::exceededGroupCreation( $userId );
}

/**
 * Deprecated since 1.8
 * Use CLimitsHelper::exceededPhotoUpload instead. 
 */
function cExceededVideoUploadLimit( $userId, $type = VIDEO_USER_TYPE )
{
	return CLimitsHelper::exceededVideoUpload( $userId , $type );
}