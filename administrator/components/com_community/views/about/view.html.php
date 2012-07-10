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
class CommunityViewAbout extends JView
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

		$parser		=& JFactory::getXMLParser('Simple');
		$xml		= JPATH_COMPONENT . DS . 'community.xml';
		
		$parser->loadFile( $xml );

		$doc		=& $parser->document;
		
		$element	=& $doc->getElementByPath( 'version' );
		$version	= $element->data();

		$this->assign( 'version'	, $version );
		
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
		JToolBarHelper::title( JText::_('COM_COMMUNITY_ABOUT_JOM_SOCIAL'), 'about' );
		
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
	}
}