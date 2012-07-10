<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

//require_once( JApplicationHelper::getPath('toolbar_html') );

$view	= JRequest::getCmd('view','community');

JHTML::_('behavior.switcher');

// Load submenu's
				
$views	= array(
					'community'			=> 'community',
					'users'				=> 'users',
					'configuration'		=> 'community',
					'profiles' 			=> 'users',
					'groups'			=> 'groups',
					'groupcategories'	=> 'groups',
					'events'			=> 'events',
					'eventcategories'	=> 'events',
					'videoscategories'	=> 'community',
					'reports'			=> 'community',
					'userpoints'		=> 'users',
					'about'				=> 'community'
				);



$subViews['community']  = array(
					'community'			=> JText::_('COM_COMMUNITY_TOOLBAR_HOME'),
					'configuration'		=> JText::_('COM_COMMUNITY_TOOLBAR_CONFIGURATION'),
					'users'				=> JText::_('COM_COMMUNITY_TOOLBAR_USERS'),
					'groups'			=> JText::_('COM_COMMUNITY_TOOLBAR_GROUPS'),
					'events'			=> JText::_('COM_COMMUNITY_TOOLBAR_EVENTS'),
					'videoscategories'	=> JText::_('COM_COMMUNITY_TOOLBAR_VIDEO_CATEGORIES'),
					'reports'			=> JText::_('COM_COMMUNITY_TOOLBAR_REPORTINGS'),
					'about'				=> JText::_('COM_COMMUNITY_TOOLBAR_ABOUT')
				);

$subViews['users']  = array(
					'community'			=> JText::_('COM_COMMUNITY_TOOLBAR_HOME'),
					'users'				=> JText::_('COM_COMMUNITY_TOOLBAR_USERS'),
					'multiprofile'		=> JText::_('COM_COMMUNITY_TOOLBAR_MULTIPROFILES'),
					'profiles' 			=> JText::_('COM_COMMUNITY_TOOLBAR_CUSTOMPROFILES'),
					'userpoints'		=> JText::_('COM_COMMUNITY_TOOLBAR_USERPOINTS')
				);
				
$subViews['groups']  = array(
					'community'			=> JText::_('COM_COMMUNITY_TOOLBAR_HOME'),
					'groups'			=> JText::_('COM_COMMUNITY_TOOLBAR_GROUPS'),
					'groupcategories'	=> JText::_('COM_COMMUNITY_TOOLBAR_GROUP_CATEGORIES')
				);
				
$subViews['events']  = array(
					'community'			=> JText::_('COM_COMMUNITY_TOOLBAR_HOME'),
					'events'			=> JText::_('COM_COMMUNITY_TOOLBAR_EVENTS'),
					'eventcategories'	=> JText::_('COM_COMMUNITY_TOOLBAR_EVENT_CATEGORIES')
					);
				
$currentView    = '';

if(array_key_exists($view, $views))
{
	$currentView    = $views[$view];
}
				
if(! array_key_exists($currentView, $subViews))
{
    $currentView    = 'community';
}

foreach( $subViews[$currentView] as $key => $val )
{
	$active	= ( $view == $key );
	JSubMenuHelper::addEntry( $val , 'index.php?option=com_community&view=' . $key , $active );
}
