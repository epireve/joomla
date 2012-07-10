<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
jimport( 'joomla.utilities.arrayhelper');

class CommunityViewFriends extends CommunityView
{
	
	public function friends($data = null){
		
		$mainframe  = JFactory::getApplication();
		$document   = JFactory::getDocument();
		
		$id         = JRequest::getCmd('userid', 0 );
		$sorted	    = JRequest::getVar( 'sort' , 'latest' , 'GET' );
		$filter	    = JRequest::getWord( 'filter' , 'all' , 'GET' );
		$isMine	    = ( ($id == $my->id) && ($my->id != 0) );
		
		$my	    = CFactory::getUser();
		$id         = $id == 0 ? $my->id : $id;
		$user	    = CFactory::getUser($id);
		$friends    = CFactory::getModel('friends');
		$blockModel	= CFactory::getModel('block');

		$document->setLink(CRoute::_('index.php?option=com_community'));
				
		CFactory::load('helpers', 'friends');
		$rows 		= $friends->getFriends( $id , $sorted , true , $filter );
			
		// Hide submenu if we are viewing other's friends
		if( $isMine )
		{
			$document->setTitle(JText::_('COM_COMMUNITY_FRIENDS_MY_FRIENDS'));
		}
		else
		{			
			$document->setTitle(JText::sprintf('COM_COMMUNITY_FRIENDS_ALL_FRIENDS', $user->getDisplayName()));
		}

		$sortItems =  array(
							'latest' 		=> JText::_('COM_COMMUNITY_SORT_RECENT_FRIENDS') , 
 							'online'		=> JText::_('COM_COMMUNITY_ONLINE') );
							
		$resultRows = array();

		// @todo: preload all friends
		foreach($rows as $row)
		{
			$user = CFactory::getUser($row->id);

			$obj = clone($row);
			$obj->friendsCount  = $user->getFriendCount(); 
			$obj->profileLink	= CUrlHelper::userLink($row->id);
			$obj->isFriend		= true;
			$obj->isBlocked		= $blockModel->getBlockStatus($user->id,$my->id);
			$resultRows[] = $obj;
		}
		unset($rows);
            	
		foreach($resultRows as $row){ 
			if( !$row->isBlocked ) {
				// load individual item creator class
				$item = new JFeedItem();
				$item->title 		= strip_tags($row->name);
				$item->link 		= CRoute::_('index.php?option=com_community&view=profile&userid='.$row->id);  
				$item->description 	= '<img src="' . JURI::base() . $row->_thumb . '" alt="" />&nbsp;'.$row->_status;
				$item->date			= $row->lastvisitDate;
				$item->category   	= '';//$row->category;
				
				$item->description = CString::str_ireplace('_QQQ_', '"', $item->description);
				// Make sure url is absolute
				$item->description = CString::str_ireplace('href="/', 'href="'. JURI::base(), $item->description); 
	
				// loads item info into rss array
				$document->addItem( $item );
			}
		}
	   

    }
}