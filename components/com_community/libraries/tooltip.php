<?php

/**
 * Return avatar tooltip title
 * @todo: this is perfect candidate for caching
 * 
 * @param	row		user object   
 */ 
function cAvatarTooltip( &$row ){
	$user			= CFactory::getUser($row->id);
	return $user->getDisplayName();
	
	/*
	$numFriends		= $user->getFriendCount();

	if($user->isOnline()) 
		$isOnline = '<img style="vertical-align:middle;padding: 0px 4px;" src="'.JURI::base().'components/com_community/assets/status_online.png" />'. JText::_('COM_COMMUNITY_ONLINE');
	else
		$isOnline = '<img style="vertical-align:middle;padding: 0px 4px;" src="'.JURI::base().'components/com_community/assets/status_offline.png" />'.JText::_('COM_COMMUNITY_OFFLINE');
	
	CFactory::load( 'helpers' , 'string');
	$html  = $row->getDisplayName() . '::';
	$html .= $user->getStatus().'<br/>';
	$html .= '<hr noshade="noshade" height="1"/>';
	$html .= $isOnline. ' | <img style="vertical-align:middle;padding: 0px 4px;" src="'.JURI::base().'components/com_community/assets/default-favicon.png" />'.JText::sprintf( (CStringHelper::isPlural($numFriends)) ? 'COM_COMMUNITY_FRIENDS_COUNT_MANY' : 'COM_COMMUNITY_FRIENDS_COUNT', $numFriends);
	return htmlentities($html, ENT_COMPAT, 'UTF-8');
	 */
}
