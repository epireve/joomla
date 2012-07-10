<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	user 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

class CMiniHeader
{

	public function load()
	{
		$jspath = JPATH_BASE.DS.'components'.DS.'com_community';
		include_once($jspath.DS.'libraries'.DS.'core.php');
		include_once($jspath.DS.'libraries'.DS.'template.php');
		$config	= CFactory::getConfig();

		$js = 'assets/window-1.0';
		$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
		CAssets::attach($js, 'js');

		$js = 'assets/script-1.2';
		$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
		CAssets::attach($js, 'js');

		$css = 'assets/window.css';
		CAssets::attach($css, 'css');
	
		CFactory::load( 'libraries' , 'template' );
		CTemplate::addStyleSheet( 'style' );
	}
						
	public function showMiniHeader($userId)
	{
		CMiniHeader::load();
		CFactory::load( 'helpers' , 'friends' );
		CFactory::load( 'helpers' , 'owner' );
		$option		= JRequest::getVar( 'option', '' , 'REQUEST' );
		JFactory::getLanguage()->load( 'com_community' );	
		
	    $my 	= CFactory::getUser();
        $config	= CFactory::getConfig();

	    if ( !empty( $userId ) )
		{
			$user 			= CFactory::getUser( $userId );

			CFactory::load( 'libraries' , 'messaging' );
			$sendMsg	= CMessaging::getPopup ( $user->id );
        	$tmpl		= new CTemplate();
        	$tmpl->set( 'my' 		, $my);
        	$tmpl->set( 'user' 		, $user);
        	$tmpl->set( 'isMine'	, COwnerHelper::isMine($my->id, $user->id));
        	$tmpl->set( 'sendMsg'	, $sendMsg );
        	$tmpl->set( 'config'	, $config );
        	$tmpl->set( 'isFriend'	, CFriendsHelper::isConnected ( $user->id, $my->id ) && $user->id != $my->id );
        	
        	$showMiniHeader = $option=='com_community' ? $tmpl->fetch('profile.miniheader') : '<div id="community-wrap" style="min-height:50px;">' . $tmpl->fetch('profile.miniheader') . '</div>' ;
        	
        	return $showMiniHeader;
        }
	}

	public function showGroupMiniHeader( $groupId )
	{
		CMiniHeader::load();

		$option		= JRequest::getVar( 'option', '' , 'REQUEST' );
		JFactory::getLanguage()->load( 'com_community' );	
		
		CFactory::load( 'models' , 'groups' );
		
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $groupId );
		$my 		= CFactory::getUser();
		
		// @rule: Test if the group is unpublished, don't display it at all.
		if( !$group->published )
		{
			return '';
		}
		
		
	    if ( !empty( $group->id ) && $group->id != 0 )
		{
        	$isMember	= $group->isMember($my->id);
			$config		= CFactory::getConfig();
			$tmpl		= new CTemplate();
        	$tmpl->set( 'my' 		, $my );
        	$tmpl->set( 'group' 	, $group );
        	$tmpl->set( 'isMember'	, $isMember );
        	$tmpl->set( 'config'	, $config );
        	
        	$showMiniHeader = $option=='com_community' ? $tmpl->fetch('groups.miniheader') : '<div id="community-wrap" style="min-height:50px;">' . $tmpl->fetch('groups.miniheader') . '</div>' ;
        	
        	return $showMiniHeader;
        }
	}
	
}