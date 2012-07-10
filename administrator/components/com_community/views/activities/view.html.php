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
class CommunityViewActivities extends JView
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 * 
	 * @param	string template	Template file name
	 **/	 	
	public function display( $tpl = null )
	{
		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');

		$model			=& $this->getModel( 'Activities' );
		$activities		= $model->getActivities();
		$userModel		=& $this->getModel( 'Users' , false );
		$currentUser	= JRequest::getVar( 'actor' , 0 , 'REQUEST' );
		$currentArchived= JRequest::getVar( 'archived', 0 , 'REQUEST' );
		$currentApp		= JRequest::getVar( 'app' , 'none' , 'REQUEST' );
		
		$filterApps		= $model->getFilterApps();
		$this->assignRef( 'filterApps'		, $filterApps );
		$this->assignRef( 'currentApp'		, $currentApp );
		$this->assignRef( 'currentUser'		, $currentUser );
		$this->assignRef( 'currentArchive'	, $currentArchived );
		$this->assignRef( 'pagination'		, $model->getPagination() );
		$this->assignRef( 'activities' 		, $activities );
		parent::display( $tpl );
	}

	public function _getUserLink( $id )
	{
		$user	= CFactory::getUser( $id );

		return '<a href="' . JURI::root() . 'index.php?option=com_community&view=profile&userid=' . $user->id . '" target="_blank">' . $user->getDisplayName() . '</a>';
	}
	
	/**
	 * Private method to set the toolbar for this view
	 * 
	 * @access private
	 * 
	 * @return null
	 **/	 	 
	public function setToolBar()
	{
		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_ACTIVITIES'), 'activities' );
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
 		JToolBarHelper::divider();
 		JToolBarHelper::trash('delete', JText::_('COM_COMMUNITY_DELETE'));
 		JToolBarHelper::trash('purge', JText::_('COM_COMMUNITY_ACTIVITIES_PURGE_ALL') , false );
	}
}