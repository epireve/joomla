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
class CommunityViewApplications extends JView
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
		JToolBarHelper::title( JText::_('COM_COMMUNITY_APPLICATIONS'), 'applications' );

		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
		// Add the necessary buttons
// 		JToolBarHelper::publishList('publish', JText::_('COM_COMMUNITY_PUBLISH'));
// 		JToolBarHelper::unpublishList('unpublish', JText::_('COM_COMMUNITY_UNPUBLISH'));
// 		JToolBarHelper::divider();
// 		JToolBarHelper::trash('removefield', JText::_('COM_COMMUNITY_DELETE'));
// 		JToolBarHelper::addNew('newfield', JText::_('COM_COMMUNITY_NEW_FIELD'));
	}
}