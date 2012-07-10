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
class CommunityViewReports extends JView
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 * 
	 * @param	string template	Template file name
	 **/	 	
	public function display( $tpl = null )
	{
		//$reports	= $this->get( 'Reports' );
		//$pagination	= $this->get( 'Pagination' );
		
		$model = $this->getModel( 'Reports' );
		$reports	= $model->getReports();
		$pagination	= $model->getPagination();

		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');

		if( $this->getLayout() == 'childs' )
		{
			$this->_displayChilds( $tpl );
			return;
		}
		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_REPORTS'), 'reports' );

		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
		JToolBarHelper::divider();
		JToolBarHelper::trash('removeReport', JText::_('COM_COMMUNITY_DELETE'));
		JToolBarHelper::trash('purgeProcessed', JText::_('COM_COMMUNITY_REPORTS_PURGE_COMPLETED') , false );
		
 		$this->assignRef( 'reports'		, $reports );
 		$this->assignRef( 'pagination'	, $pagination );
		parent::display( $tpl );
	}
	
	public function _displayChilds( $tpl )
	{
		$reportId	= JRequest::getVar( 'reportid' , '' , 'GET' );

		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_REPORTS_VIEWING_REPORTERS'), 'reports' );

		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
		JToolBarHelper::divider();
		JToolBarHelper::trash('removeReport', JText::_('COM_COMMUNITY_DELETE'));
		$report		=& JTable::getInstance( 'reports' , 'CommunityTable' );
		$report->load( $reportId );
				
		$model		= $this->getModel( 'Reporters' );
		$reporters	= $model->getReporters( $reportId );
		$pagination	= $model->getPagination();

		$this->assignRef( 'reporters' , $reporters );
		$this->assignRef( 'pagination'	, $pagination );
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

	}
}
