<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class CommunityViewProfile extends CommunityView
{

	/**
	 * Displays the viewing profile page.
	 * 	 	
	 * @access	public
	 * @param	array  An associative array to display the fields
	 */	  	
	public function profile(& $data)
	{
		$mainframe      = JFactory::getApplication();
		$friendsModel	= CFactory::getModel('friends');

		$showfriends    = JRequest::getVar('showfriends', false);
		$userid         = JRequest::getVar('userid' , '');
		$user           = CFactory::getUser($userid);
		$linkUrl	= CRoute::_('index.php?option=com_community&view=profile&userid='.$user->id);
		
		$document = JFactory::getDocument();
		$document->setTitle( JText::sprintf( 'COM_COMMUNITY_USERS_FEED_TITLE' , $user->getDisplayName() ) );
		$document->setDescription( JText::sprintf('COM_COMMUNITY_USERS_FEED_DESCRIPTION', $user->getDisplayName() , $user->lastvisitDate ) );
		$document->setLink( $linkUrl );
		
		include_once(JPATH_COMPONENT . DS.'libraries'.DS.'activities.php');
		$act = new CActivityStream();
		
		$friendIds	= $friendsModel->getFriendIds($user->id);
		$friendIds  = $showfriends ? $friendIds : null;
		$rows       = $act->getFEED($user->id, $friendIds, null, $mainframe->getCfg('feed_limit'));
		
		// add the avatar image
		$rssImage = new JFeedImage();
		$rssImage->url = $user->getThumbAvatar();
		$rssImage->link = $linkUrl;
		$rssImage->width  = 64;
		$rssImage->height = 64;
		$document->image = $rssImage;

		CFactory::load( 'helpers' , 'string' );
		CFactory::load( 'helpers' , 'time' );
		
		foreach($rows->data as $row){
			if($row->type != 'title') {
			
			    // Get activities link
			    $pattern	= '/<a href=\"(.*?)\"/';
			    preg_match_all($pattern, $row->title, $matches);
				 
				// Use activity owner link when activity link is not available
				if( !empty($matches[1][1]) )
				{
					$linkUrl	= $matches[1][1];
				}
				else if( !empty($matches[1][0]) )
				{ 
					$linkUrl	= $matches[1][0];
				}
				
				// load individual item creator class
				$item = new JFeedItem();
				$item->title 		= $row->title;
				$item->link 		= $linkUrl;
				$item->description 	= "<img src=\"{$row->favicon}\" alt=\"\" />&nbsp;".$row->title;
				$item->date			= CTimeHelper::getDate($row->createdDateRaw)->toRFC822();
				$item->category   	= '';//$row->category;
			
				$item->description = CString::str_ireplace('_QQQ_', '"', $item->description);
				// Make sure url is absolute
				$pattern			= '/href="(.*?)index.php/';
				$replace			= 'href="' . JURI::base() . 'index.php';
				$string				= $item->description;
				$item->description = preg_replace($pattern, $replace, $string); 
	
				// loads item info into rss array
				$document->addItem( $item );
			}
		}
		
	}
}
?>
