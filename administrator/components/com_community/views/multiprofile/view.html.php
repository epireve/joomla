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

class CommunityViewMultiProfile extends JView
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 * 
	 * @param	string template	Template file name
	 **/	 	
	public function display( $tpl = null )
	{
		JHTML::_('behavior.tooltip', '.hasTip');
		
		if( $this->getLayout() == 'edit' )
		{
			$this->_displayEditLayout( $tpl );
			return;
		}


		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_CONFIGURATION_MULTIPROFILES'), 'multiprofile' );

		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('publish', JText::_('COM_COMMUNITY_PUBLISH'));
		JToolBarHelper::unpublishList('unpublish', JText::_('COM_COMMUNITY_UNPUBLISH'));
		JToolBarHelper::divider();
		JToolBarHelper::trash('delete', JText::_('COM_COMMUNITY_DELETE'));
		JToolBarHelper::addNew('add', JText::_('COM_COMMUNITY_NEW'));
		
		$profiles	= $this->get( 'MultiProfiles' );
		$pagination	= $this->get( 'Pagination' );
		
		$mainframe			= JFactory::getApplication();
		
		$ordering			= $mainframe->getUserStateFromRequest( "com_community.multiprofile.filter_order",		'filter_order',		'ordering',	'cmd' );
		$orderingDirection	= $mainframe->getUserStateFromRequest( "com_community.multiprofile.filter_order_Dir",	'filter_order_Dir',	'ASC',			'word' );
		
 		$this->assignRef( 'profiles'	, $profiles );
		$this->assignRef( 'ordering'	, $ordering );
		$this->assignRef( 'orderingDirection'	, $orderingDirection );
		
		$this->assignRef( 'pagination'	, $pagination );
		parent::display( $tpl );
	}

	public function _displayEditLayout( $tpl )
	{
		JToolBarHelper::title( JText::_('COM_COMMUNITY_CONFIGURATION_MULTIPROFILES') , 'multiprofile' );
		
 		// Add the necessary buttons
 		JToolBarHelper::back('Back' , 'index.php?option=com_community&view=multiprofile');
 		JToolBarHelper::divider();
		JToolBarHelper::save();

		$id				= JRequest::getVar( 'id' , '' , 'REQUEST' );
		$multiprofile	=& JTable::getInstance( 'MultiProfile' , 'CTable' );
		$multiprofile->load( $id );

		$profile	= $this->getModel( 'Profiles' );
		$fields		= $profile->getFields();

		$config		= CFactory::getConfig();
		
		$this->assignRef( 'multiprofile', $multiprofile );
		$this->assignRef( 'fields'		, $fields );
		$this->assignRef( 'config'		, $config );
 		
 		parent::display( $tpl );
	}
	
	public function getWatermarkLocations()
	{
		$locations	= array(
			JHTML::_('select.option', 'top', 'Top'),
			JHTML::_('select.option', 'right', 'Right'),
			JHTML::_('select.option', 'bottom', 'Bottom'),
			JHTML::_('select.option', 'left', 'Left'),
		);
		return $locations;
	}

	/**
	 * Return the total number of users for specific profile
	 **/	 	
	public function getTotalUsers( $profileId )
	{
		$db		=& JFactory::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__community_users') . ' WHERE ' . $db->nameQuote('profile_id') . '=' . $db->Quote( $profileId );
		$db->setQuery( $query );
		return $db->loadResult();
	}
	
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/	 	
	public function getPublish( &$row , $type , $ajaxTask )
	{
	
		$imgY	= 'tick.png';
		$imgX	= 'publish_x.png';
		
		$image	= $row->$type ? $imgY : $imgX;
		
		$alt	= $row->$type ? JText::_('COM_COMMUNITY_PUBLISHED') : JText::_('COM_COMMUNITY_UNPUBLISH');
		
		$href = '<a class="jgrid" href="javascript:void(0);" onclick="azcommunity.togglePublish(\'' . $ajaxTask . '\',\'' . $row->id . '\',\'' . $type . '\');">';

		if(C_JOOMLA_15==0){
			$state = $row->$type ? 'publish' : 'unpublish';
			$href .= '<span class="state '.$state.'"><span class="text">'.$alt.'</span></span></a>';
		}
		else{

			$href  .= '<span><img src="images/' . $image . '" border="0" alt="' . $alt . '" /></span></a>';
		}

		return $href;
	}
	
	/**
	 * Method to get the publish status HTML
	 *
	 * @param	object	Field object
	 * @param	string	Type of the field
	 * @param	string	The ajax task that it should call
	 * @return	string	HTML source
	 **/	 	
	public function getItemsPublish( $isPublished , $fieldId )
	{
		$imgY	= 'tick.png';
		$imgX	= 'publish_x.png';
		$image	= '';
		
		if( $isPublished )
		{
			$image	= $imgY;
		}
		else
		{
			$image	= $imgX;
		}
		
		$href = '<a href="javascript:void(0);" onclick="azcommunity.toggleMultiProfileChild(' . $fieldId . ');"><img src="images/' . $image . '" border="0" /></a>';
		return $href;
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