<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CGroupHelper
{
	static public function getMediaPermission( $groupId )
	{
		// load COwnerHelper::isCommunityAdmin()
		CFactory::load( 'helpers' , 'owner' );
		$my	= CFactory::getUser();
		
		$isSuperAdmin		= COwnerHelper::isCommunityAdmin();
		$isAdmin			= false;
		$isMember			= false;
		$waitingApproval	= false;
			
		// Load the group table.
		$groupModel	= CFactory::getModel( 'groups' );
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $groupId );
		$params		= $group->getParams();	
		
		if(!$isSuperAdmin)
		{
			$isAdmin	= $groupModel->isAdmin( $my->id , $group->id );			
			$isMember	= $group->isMember( $my->id );
			
			//check if awaiting group's approval
			if( $groupModel->isWaitingAuthorization( $my->id , $group->id ) )
			{
				$waitingApproval	= true;
			}
		}
		
		$permission = new stdClass();
		$permission->isMember 			= $isMember;
		$permission->waitingApproval 	= $waitingApproval;
		$permission->isAdmin 			= $isAdmin;
		$permission->isSuperAdmin 		= $isSuperAdmin;
		$permission->params 			= $params;	
		$permission->privateGroup		= $group->approvals;
		
		return $permission;
	}
	
	static public function allowViewMedia( $groupId )
	{
		if(empty($groupId))
		{
			return false;
		}
		
		//get permission
		$permission = CGroupHelper::getMediaPermission($groupId);
		
		if($permission->privateGroup)
		{
			if($permission->isSuperAdmin || ($permission->isMember && !$permission->waitingApproval) )
			{
				$allowViewVideos = true;
			}
			else
			{			
				$allowViewVideos = false;	
			}
		}
		else
		{
			$allowViewVideos = true;
		}
		
		return $allowViewVideos;
	}
	
	static public function allowManageVideo( $groupId )
	{
		$allowManageVideos = false;
		
		//get permission
		$permission = CGroupHelper::getMediaPermission($groupId);
		
		$videopermission	= $permission->params->get('videopermission' , GROUP_VIDEO_PERMISSION_ADMINS );

		//checking for backward compatibility
                if($videopermission == GROUP_VIDEO_PERMISSION_ALL)
                {
                    $videopermission = GROUP_VIDEO_PERMISSION_MEMBERS;
                }
                
		if($videopermission == GROUP_VIDEO_PERMISSION_DISABLE)
		{
			$allowManageVideos = false;
		}
		else if( ($videopermission == GROUP_VIDEO_PERMISSION_MEMBERS && $permission->isMember && !$permission->waitingApproval) || $permission->isAdmin || $permission->isSuperAdmin )
		{
			$allowManageVideos = true;
		}
		
		return $allowManageVideos;
	}
	
	static public function allowManagePhoto($groupId)
	{
		$allowManagePhotos = false;
		
		//get permission
		$permission = CGroupHelper::getMediaPermission($groupId);
		
		$photopermission	= $permission->params->get('photopermission' , GROUP_PHOTO_PERMISSION_ADMINS );

                //checking for backward compatibility
                if($photopermission == GROUP_PHOTO_PERMISSION_ALL)
                {
                    $photopermission = GROUP_PHOTO_PERMISSION_MEMBERS;
                }
                
		if($photopermission == GROUP_PHOTO_PERMISSION_DISABLE)
		{
			$allowManagePhotos = false;
		}
                
		else if( ($photopermission == GROUP_PHOTO_PERMISSION_MEMBERS && $permission->isMember && !$permission->waitingApproval) || $permission->isAdmin || $permission->isSuperAdmin )
		{
			$allowManagePhotos = true;
		}
		
		return $allowManagePhotos;
	}
	static public function allowManageEvent( $userId , $groupId , $eventId )
	{
		CFactory::load( 'helpers' , 'owner' );
		$user		= CFactory::getUser( $userId );
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$event		=&JTable::getInstance( 'Event' , 'CTable' );
		
		$event->load( $eventId );
		$group->load( $groupId );
		
		if( COwnerHelper::isCommunityAdmin() || $group->isAdmin( $user->id ) || $event->isCreator( $user->id ) )
		{
			return true;
		}
		return false;
	}
	
	static public function allowCreateEvent( $userId , $groupId )
	{
		CFactory::load( 'helpers' , 'owner' );
		$user		= CFactory::getUser( $userId );
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $groupId );
		$params		= $group->getParams();
		
		if( COwnerHelper::isCommunityAdmin() )
		{
			return true;
		}

		if( $group->isAdmin( $user->id ) && ( $params->get('eventpermission') == GROUP_EVENT_PERMISSION_ALL || $params->get('eventpermission') == GROUP_EVENT_PERMISSION_ADMINS ) )
		{
			return true;
		}
		
		if( $group->isMember( $user->id ) && $params->get('eventpermission') == GROUP_EVENT_PERMISSION_ALL )
		{
			return true;
		}
		
		return false;
	}
	
	static public function allowPhotoWall($groupid)
	{
		$permission = CGroupHelper::getMediaPermission($groupid);
		
		if( $permission->isMember || $permission->isAdmin || $permission->isSuperAdmin )
		{
			return true;
		}
		return false;
	}
}

/**
 * Deprecated since 1.8
 * Use CGroupHelper::getMediaPermission instead. 
 */
function _cGetGroupMediaPermission($groupId)
{
	return CGroupHelper::getMediaPermission( $groupId );
}

/**
 * Deprecated since 1.8
 * Use CGroupHelper::allowViewMedia instead. 
 */
function cAllowViewMedia($groupId)
{
	return CGroupHelper::allowViewMedia( $groupId );
}

/**
 * Deprecated since 1.8
 * Use CGroupHelper::allowManageVideo instead. 
 */
function cAllowManageVideo($groupId)
{
	return CGroupHelper::allowManageVideo( $groupId );
}

/**
 * Deprecated since 1.8
 * Use CGroupHelper::allowManagePhoto instead. 
 */
function cAllowManagePhoto($groupId)
{
	return CGroupHelper::allowManagePhoto( $groupId );
}

/**
 * Deprecated since 1.8
 * Use CGroupHelper::allowPhotoWall instead. 
 */
function cAllowPhotoWall($groupId)
{
	return CGroupHelper::allowPhotoWall( $groupId );
}