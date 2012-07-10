<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */  

// no direct access
defined('_JEXEC') or die('Restricted access'); 
jimport( 'joomla.application.component.view'); 

require_once( COMMUNITY_COM_PATH . DS . 'helpers' . DS . 'string.php' );

class CommunityViewSearch extends CommunityView
{

	public function display($data = null)
	{
		$mainframe  = JFactory::getApplication();
		$document 	= JFactory::getDocument();
		
		$model      = CFactory::getModel( 'search' );
		$members    = $model->getPeople();

		// Prepare feeds		
// 		$document->setTitle($title);
       	
		foreach($members as $member)
		{   
			$user   = CFactory::getUser($member->id);
			$friendCount = JText::sprintf( (CStringHelper::isPlural($user->getFriendCount())) ? 'COM_COMMUNITY_FRIENDS_COUNT_MANY' : 'COM_COMMUNITY_FRIENDS_COUNT', $user->getFriendCount());

			$item = new JFeedItem();
			$item->title 		= $user->getDisplayName();
			$item->link 		= CRoute::_('index.php?option=com_community&view=profile&userid='.$user->id);  
			$item->description 	= '<img src="' . $user->getThumbAvatar() . '" alt="" />&nbsp;' . $friendCount; 
			$item->date         = '';
			
			$item->description = CString::str_ireplace('_QQQ_', '"', $item->description);
			// Make sure url is absolute
            $item->description  = CString::str_ireplace('href="/', 'href="'. JURI::base(), $item->description);
      
            $document->addItem( $item );
		}

	}
	
	public function browse(){
		return $this->display();
	}
}
