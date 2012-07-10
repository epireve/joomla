<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

/**
 * Configuration view for Jom Social
 */
class CommunityViewMemberlist extends JView
{
	public function display( $tmpl = null )
	{
		$mainframe	=& JFactory::getApplication();
		$memberlist	= $this->get( 'MemberList' );
		$pagination	= $this->get( 'Pagination' );

		$ordering		= $mainframe->getUserStateFromRequest( "com_community.memberlist.filter_order",		'filter_order',		'a.title',	'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( "com_community.memberlist.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$search			= $mainframe->getUserStateFromRequest( "com_community.memberlist.search", 'search', '', 'string' );
		$object			= JRequest::getVar( 'object' , '' );
		$requestType	= JRequest::getVar( 'tmpl' );
		
		$this->assignRef( 'requestType'	, $requestType );
		$this->assignRef( 'object'		, $object );
		$this->assignRef( 'memberlist'	, $memberlist );
		$this->assignRef( 'ordering'	, $ordering );
		$this->assignRef( 'orderDirection'	, $orderDirection );
		$this->assignRef( 'memberlist'	, $memberlist );
		$this->assignRef( 'pagination'	, $pagination );
		parent::display( $tmpl );
	}
	
	public function setToolBar()
	{
		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_MEMBERLIST'), 'memberlist');
		
		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList( JText::_('COM_COMMUNITY_MEMBERLIST_DELETION_WARNING') , 'delete' , JText::_('COM_COMMUNITY_DELETE') );
	}
}