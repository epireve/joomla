<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */  

// no direct access
defined('_JEXEC') or die('Restricted access'); 
jimport( 'joomla.application.component.view');
include_once (COMMUNITY_COM_PATH.DS.'views'.DS.'videos'.DS.'view.php');

class CommunityViewVideos extends CommunityView
{
	var $_videoLib	= null;
	var $model		= '';
	
	public function CommunityViewVideos()
	{
		CFactory::load( 'helpers', 'videos' );
		CFactory::load( 'libraries' , 'videos' );
		$this->model	= CFactory::getModel('videos');
		$this->videoLib	= new CVideoLibrary();
	}

	public function display($data = null)
	{
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		
		$my			= CFactory::getUser();
		$userid		= JRequest::getInt( 'userid' , '' );
		$groupId	= JRequest::getInt( 'groupid', '', 'GET' );
		
		if( !empty($userid) ){  
			$user		= CFactory::getUser($userid);
			
			// Set document title
			CFactory::load( 'helpers' , 'owner' );
			$blocked	= $user->isBlocked();
			
			if( $blocked && !COwnerHelper::isCommunityAdmin() )
			{
				$tmpl	= new CTemplate();
				echo $tmpl->fetch('profile.blocked');
				return;
			}
		
			if($my->id == $user->id){
				$title	= JText::_('COM_COMMUNITY_VIDEOS_MY');
			}else{
				$title	= JText::sprintf('COM_COMMUNITY_VIDEOS_USERS_VIDEO_TITLE', $user->getDisplayName());
			}
			
		}else{
				$title  = JText::_('COM_COMMUNITY_VIDEOS_ALL_DESC');
		}
		
		
		// list user videos or group videos
		if( !empty($groupId) ){   
			$title		= JText::_('COM_COMMUNITY_SUBSCRIBE_TO_GROUP_VIDEOS_FEEDS');
			$group		= JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );
			
			CFactory::load( 'helpers' , 'owner' );
			$isMember	= $group->isMember( $my->id );
			$isMine		= ($my->id == $group->ownerid);
			if( !$isMember && !$isMine && !COwnerHelper::isCommunityAdmin() && $group->approvals == COMMUNITY_PRIVATE_GROUP )
			{
				echo JText::_('COM_COMMUNITY_GROUPS_PRIVATE_NOTICE');
				return;
			}
			
			$tmpVideos	= $this->model->getGroupVideos( $groupId, '', $mainframe->getCfg('feed_limit') );
			$videos		= array();
			foreach ($tmpVideos as $videoEntry)
			{
				$video	=& JTable::getInstance('Video','CTable');
				$video->bind( $videoEntry );
				$videos[]	= $video;
			}
			
		}else{
		
			$filters		= array
			(
				'creator'	=> $userid,
				'status'	=> 'ready',
				'groupid'	=> 0,
				'limit'		=> $mainframe->getCfg('feed_limit'),
				'limitstart'=> 0,
				'sorting'	=> JRequest::getVar('sort', 'latest')
			);
			
			// list all user videos & all group videos
			if( empty($userid) ){
					unset($filters['creator']); 
					unset($filters['groupid']);
			}
			
			$videos = array();
			$tmpVideos = $this->model->getVideos($filters, true);
			
			foreach ($tmpVideos as $videoEntry)
			{
				$video	=& JTable::getInstance('Video','CTable');
				$video->bind( $videoEntry );
				$videos[]	= $video;
			}
		}
		
		$videosCount	= count($videos);
		$feedLimit		= $mainframe->getCfg('feed_limit');
		$limit			= ($videosCount < $feedLimit) ? $videosCount : $feedLimit;
		
		// Prepare feeds
		$document->setTitle($title);
		
		for($i = 0; $i < $limit; $i++)
		{
			$video = $videos[$i];
			
			$item = new JFeedItem();
			$item->title 		= $video->getTitle();
			$item->link 		= $video->getURL();
			$item->description 	= '<img src="' . $video->getThumbnail() . '" alt="" />&nbsp;'.$video->getDescription();
			$item->date			= $video->created;
			$item->author		= $video->getCreatorName();

			$item->description = CString::str_ireplace('_QQQ_', '"', $item->description);
			
			if( !empty($video->id) )
				$document->addItem( $item );
		}

	}
	
	public function myvideos(){
		return $this->display();
	}
	
}
