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
class CommunityViewMaintenance extends JView
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 * 
	 * @param	string template	Template file name
	 **/	 	
	public function display( $tpl = null )
	{
		$groupsModel	=& $this->getModel( 'Groups' );
		$usersModel		=& $this->getModel( 'Users' , false );
		
		$groups			= $groupsModel->getAllGroups();

		$isLatest		= ($groupsModel->isLatestTable() && $usersModel->isLatestTable()) ? true : false;
		
		$this->assign( 'groups'		, $groups );
		$this->assign( 'isLatest'	, $isLatest );
		parent::display( $tpl );
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
		JToolBarHelper::title( JText::_( 'COM_COMMUNITY_MAINTENANCE' ), 'profiles' );
		
		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME') , 'index.php?option=com_community');
	}
}