<?php
/**
 * @package		JomSocial
 * @subpackage	Core
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
// Testing Merge

include_once( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');

function CommunityBuildRoute(&$query)
{
	$app = JFactory::getApplication();
	$segments = array();
	$config = CFactory::getConfig();
	$jconfig = JFactory::getConfig();
	$alias	= '';
	
	// Profile based,
	if(array_key_exists( 'userid', $query))
	{
		$user		= CFactory::getUser( $query['userid'] );
		
		// Since 1.8.x we will generate URLs based on the vanity url.
		$alias		= $user->getAlias();
		$segments[]	= $alias;
		unset($query['userid']);
	}

	// @rule: For those my-xxx tasks, we need 
	// to force the userid to be rewritten in the URL to maintain compatibility with
	// older URLs.
	if( isset( $query['task'] ) && empty( $alias) )
	{
		$userTasks	= array( 'myvideos' , 'myphotos' , 'myevents' , 'mygroups' );

		if( in_array( $query['task'] , $userTasks ) )
		{
			$user		= CFactory::getUser();
			$segments[]	= $user->getAlias();
		}
	}
	
	if(isset($query['view']))
	{
		if(empty($query['Itemid']))
		{
			$segments[] = $query['view'];
		}
		else
		{
			$menu = &JSite::getMenu();
			$menuItem = &$menu->getItem( $query['Itemid'] );
			
			if(!isset($menuItem->query['view']) || $menuItem->query['view'] != $query['view'])
			{
				$segments[] = $query['view'];
			}
		}
		unset($query['view']);
	}

	if(isset($query['task']))
	{
		switch( $query['task'] )
		{
			case 'viewgroup':
				$db	=& JFactory::getDBO();
				$groupid =   $query['groupid'];
				$groupModel = CFactory::getModel('groups');
				$group		=& JTable::getInstance( 'Group' , 'CTable' );
				$group->load($groupid);
				
				$segments[] = $query['task'];
				$groupName	= $group->name;
				
				

				if ($jconfig->get('unicodeslugs', 0) == 1) {
					$groupName = JFilterOutput::stringURLUnicodeSlug($groupName);
				}
				else {
					$groupName = JFilterOutput::stringURLSafe($groupName);
				}
	
				
				$segments[] = $groupid . '-' . $groupName;
	
				unset($query['groupid']);
			break;
			case 'viewevent':
				$id		= $query['eventid'];
				$event	=& JTable::getInstance( 'Event' , 'CTable' );
				$event->load( $id );

				$segments[] = $query['task'];
				$name		= $event->title;
				
				
				if ($jconfig->get('unicodeslugs', 0) == 1) {
					$name = JFilterOutput::stringURLUnicodeSlug($name);
				}
				else 
				{
					$name = JFilterOutput::stringURLSafe($name);
				}
				
				$name	= urlencode( $name );
				$name	= CString::str_ireplace('++', '+', $name);
				$segments[] = $event->id . '-' . $name;		
				unset( $query['eventid'] );
			break;
			case 'video':
				$videoModel	= CFactory::getModel('Videos');
				$videoid	= $query['videoid'];
				
				$video		=& JTable::getInstance( 'Video' , 'CTable' );
				$video->load( $videoid );
				
				// We need the task for video otherwise we cannot differentiate between myvideos
				// and viewing a video since myvideos also doesn't pass any tasks.
				$segments[] = $query['task'];
				
				$title		= trim( $video->title );
				if ($jconfig->get('unicodeslugs', 0) == 1) {
					$title = JFilterOutput::stringURLUnicodeSlug($title);
				}
				else 
				{
					$title = JFilterOutput::stringURLSafe($title);
				}

				$segments[]	= $video->id . '-' . $title;
				unset( $query['videoid'] );
			break;
			case 'viewdiscussion':
				$db	=& JFactory::getDBO();
				$topicId =   $query['topicid'];
				$discussionsModel = CFactory::getModel('discussions');
				$discussions =& JTable::getInstance( 'Discussion' , 'CTable' );
				$discussions->load($topicId);
				
				$segments[] = $query['task'];
				$discussionName	= $discussions->title;

				if ($jconfig->get('unicodeslugs', 0) == 1) {
					$discussionName = JFilterOutput::stringURLUnicodeSlug($discussionName);
				}
				else 
				{
					$discussionName = JFilterOutput::stringURLSafe($discussionName);
				}
				
				$segments[] = $topicId . '-' . $discussionName;
				unset($query['topicid']);
			break;
			case 'viewbulletin':
				$db	=& JFactory::getDBO();
				$bulletinid =   $query['bulletinid'];
				$bulletinsModel = CFactory::getModel('bulletins');
				$bulletins =& JTable::getInstance( 'Bulletin' , 'CTable' );
				$bulletins->load($bulletinid);
				
				$segments[] = $query['task'];
				$bullentinName	= $bulletins->title;
				

				if ($jconfig->get('unicodeslugs', 0) == 1) {
					$bullentinName = JFilterOutput::stringURLUnicodeSlug($bullentinName);
				}
				else 
				{
					$bullentinName = JFilterOutput::stringURLSafe($bullentinName);
				}
				
				$segments[] = $bulletinid . '-' . $bullentinName;
				unset($query['bulletinid']);
			break;
			default:
				if( $query['task'] != 'myphotos' && $query['task'] != 'mygroups' && $query['task'] != 'myevents' && $query['task'] != 'myvideos' && $query['task'] != 'invites' )
				{
					$segments[] = $query['task'];
				}
			break;
		}
		unset($query['task']);
	}

	return $segments;
}

function CommunityParseRoute($segments)
{
	$vars = array();
	include_once( JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
	$menu			=& JSite::getMenu();
	$selectedMenu	=& $menu->getActive();

	// We need to grab the user id first see if the first segment is a user
	// because once CFactory::getConfig is loaded, it will automatically trigger
	// the plugins. Once triggered, the getRequestUser will only get the current user.
	$count = count($segments);

	if(!empty($count) )
	{
		$alias		= $segments[0];
		$userid		= '';
		
		if( !empty( $alias ) )
		{
			// Check if this user exists in the alias
			$userid = CommunityGetUserId( $alias );

			// Joomla converts ':' to '-' when encoding and during decoding, 
			// it converts '-' to ':' back for the query string which will break things
			// if the username has '-'. So we do not have any choice apart from 
			// testing both this values until Joomla tries to fix this
			if( !$userid && JString::stristr( $alias , ':' ) )
			{
				$userid		= CommunityGetUserId( CString::str_ireplace( ':' , '-' , $alias ) );
			}

			// For users 
			if( !$userid )
			{
				
				if( JString::stristr( $alias , '-' ) )
				{
					$user		= explode( '-' , $alias );
		
					if( isset( $user[0] ) )
					{
						$userid	= $user[0];
					}
				}

				if( JString::stristr( $alias , ':' ) )
				{
					$user		= explode( '-' , CString::str_ireplace( ':' , '-' , $alias ) );
		
					if( isset( $user[0] ) )
					{
						$userid	= $user[0];
					}
				}
			}
		}

		if($userid != 0 )
		{
			array_shift($segments);
			$vars['userid'] = $userid;
			// if empty, we should display the user's profile
			if(empty($segments))
			{
				$vars['view'] = 'profile';
			}
		}
	}

	$count	= count($segments);
	if( !isset($selectedMenu) )
	{
		if( $count > 0 )
		{
			// If there are no menus we try to use the segments
			$vars['view']  = $segments[0];

			if(!empty($segments[1]))
			{
				$vars['task'] = $segments[1];
			}
			
			if(!empty($segments[2] ) && $segments[1] == 'viewgroup' )
			{
				$groupTitle 		= $segments[2];
				$vars['groupid']	= _parseGroup( $groupTitle );
			}
		}
		return $vars;
	}

	if( $selectedMenu->query['view'] == 'frontpage' )
	{
		// We know this is a frontpage view in the menu, try to get the 
		// view from the segments instead.
		if( $count > 0 )
		{
			$vars['view'] = $segments[0];
	
			if(!empty($segments[1]))
			{
				$vars['task'] = $segments[1];
			}
		}
	}
	else
	{
		$vars['view']	= $selectedMenu->query['view'];

		if( $count > 0 )
		{
			$vars['task']	= $segments[0];
		}
	}  


	// In case of video view, the 'task' (video) has been removed during
	// BuildRoute. We need to detect if the segment[0] is actually a 
	// permalink to the actual video, and add the proper task
	if($vars['view'] == 'videos' && (isset($vars['task']) && $vars['task'] != 'myvideos') ) 
	{
		$pattern = "'^[0-9]+'s";
		$videoTitle	= $segments[ count( $segments ) - 1 ];
		preg_match($pattern, $videoTitle, $matches);

		if($matches)
		{
			$vars['task'] = 'video';
		}
	}
	
	if( isset($vars['userid']) && $vars['view'] == 'photos' && !isset( $vars['task'] ) )
	{
		$vars['task']	= 'myphotos';
	}

	if( isset($vars['userid']) && $vars['view'] == 'groups' && !isset( $vars['task'] ) )
	{
		$vars['task']	= 'mygroups';
	}
	
	if( isset($vars['userid']) && $vars['view'] == 'events' && !isset( $vars['task'] ) )
	{
		$vars['task']	= 'myevents';
	}
	
	if( isset($vars['userid']) && $vars['view'] == 'videos' && !isset( $vars['task'] ) )
	{
		$vars['task']	= 'myvideos';
	}
	
	// In case users try to access http://site.com/community/my-xxx.html directly, it should also work
	if( isset( $vars['task'] ) && $vars['task'] == 'my:groups' )
	{
		$vars['view']	= 'groups';
		$vars['task']	= 'mygroups';
	}

	if( isset( $vars['task'] ) && $vars['task'] == 'my:videos' )
	{
		$vars['view']	= 'videos';
		$vars['task']	= 'myvideos';
	}
	
	if( isset( $vars['view'] ) && $vars['view'] == 'my:photos' )
	{
		$vars['view']	= 'photos';
		$vars['task']	= 'myphotos';
	}

	if( isset( $vars['view'] ) && $vars['view'] == 'my:events' )
	{
		$vars['view']	= 'events';
		$vars['task']	= 'myevents';
	}
	
	// If the task is video then, query the last segment to grab the video id
	if( isset($vars['task'] ) && $vars['task'] == 'video' )
	{
		$videoTitle	= $segments[ count( $segments ) - 1 ];
		$titles		= explode('-', $videoTitle);
		$vars['videoid'] = $titles[0];
	}
	
	// If the task is viewgroup then, query the last segment to grab the group id
	if( isset($vars['task'] ) && $vars['task'] == 'viewgroup' )
	{
		$groupTitle = $segments[count($segments) - 1];
		$vars['groupid'] = _parseGroup( $groupTitle );
	}

	// If the task is viewevent then, query the last segment to grab the eventid
	if( isset($vars['task'] ) && $vars['task'] == 'viewevent' )
	{
		$title		= $segments[ count($segments ) - 1 ];
		$titles		= explode( '-' , $title );

		// @rule: Joomla replaces - with : for the first occurence. So we need to replace the first : to -
		if( count($titles) == 1 )
		{
			$titles	= explode( '-' , CString::str_ireplace( ':' , '-' , $title ) );
		}
		$vars['eventid']	= $titles[ 0 ];
	}
	
	// If the task is viewdiscussion then, query the last segment to grab the topic id
	if( isset($vars['task'] ) && $vars['task'] == 'viewdiscussion' ){
		$discussionTitle	= $segments[count($segments) - 1];
		$titles 			= explode('-', $discussionTitle);

		// @rule: Joomla replaces - with : for the first occurence. So we need to replace the first : to -
		if( count($titles) == 1 )
		{
			$titles	= explode( '-' , CString::str_ireplace( ':' , '-' , $discussionTitle ) );
		}
		$vars['topicid'] = $titles[0];
	}
	
	// If the task is viewgroup then, query the last segment to grab the group id
	if( isset($vars['task'] ) && $vars['task'] == 'viewbulletin' ){
		$bulletinTitle	= $segments[count($segments) - 1];
		$titles 		= explode('-', $bulletinTitle);

		// @rule: Joomla replaces - with : for the first occurence. So we need to replace the first : to -
		if( count($titles) == 1 )
		{
			$titles	= explode( '-' , CString::str_ireplace( ':' , '-' , $bulletinTitle ) );
		}
		$vars['bulletinid'] = $titles[0];
	}

	return $vars;
}

function & _parseGroup( $title )
{
	$titles 	= explode('-', $title);
	$groupId	= $titles[0];
	
	return $groupId;
}

function CommunityGetUserId( $alias )
{
	$db			=& JFactory::getDBO();
	$query		= 'SELECT ' . $db->nameQuote('userid').' FROM ' . $db->nameQuote('#__community_users')
					.' WHERE ' . $db->nameQuote('alias').'=' . $db->Quote( $alias );
	$db->setQuery($query);
	$id = $db->loadResult();

	// The alias not found, could be caused by Joomla rewriting - into :
	// Replace the first : into - and search again
	if(empty($id)){
		$pattern = '/([0-9]*)(:)/i';
		$replacement = '$1-';

		// Replace only the first occurance of : into -
		$alias = preg_replace($pattern, $replacement, $alias, 1);
		
		$query		= 'SELECT ' . $db->nameQuote('userid')
					.' FROM ' . $db->nameQuote('#__community_users').' WHERE ' . $db->nameQuote('alias').'=' . $db->Quote( $alias );
		$db->setQuery($query);
		$id = $db->loadResult();
	}

	return $id;
}