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
class CommunityViewEvents extends JView
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 * 
	 * @param	string template	Template file name
	 **/	 	
	public function display( $tpl = null )
	{
		$document	= JFactory::getDocument();
                $config		=& CFactory::getConfig();
		
		// Get required data's
		$events		= $this->get( 'Events' );
		$categories	= $this->get( 'Categories' );
		$pagination	= $this->get( 'Pagination' );

		$mainframe	=& JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest( "com_community.events.filter_order",		'filter_order',		'a.title',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "com_community.events.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( "com_community.events.search", 'search', '', 'string' );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		
		// We need to assign the users object to the groups listing to get the users name.
		for( $i = 0; $i < count( $events ); $i++ )
		{
			$row                =&  $events[$i];
			$row->user          =   JFactory::getUser( $row->creator );
                        
                        // Truncate the description
                        CFactory::load( 'helpers' , 'string' );
                        $row->description   =   CStringHelper::truncate( $row->description, $config->get('tips_desc_length') );
		}

		$catHTML	= $this->_getCategoriesHTML( $categories );

		$this->assignRef( 'events' 		, $events );
		$this->assignRef( 'categories' 	, $catHTML);
		$this->assignRef( 'search'		, $search );
		$this->assignRef( 'lists'		, $lists );
		$this->assignRef( 'pagination'	, $pagination );

		parent::display( $tpl );
	}

	
	public function _getCategoriesHTML( $categories )
	{
		// Check if there are any categories selected
		$category	= JRequest::getInt( 'category' , 0 );

		$select	= '<select name="category" onchange="submitform();">';

		$select	.= ( $category == 0 ) ? '<option value="0" selected="true">' : '<option value="0">';
		$select .= JText::_('COM_COMMUNITY_EVENTS_ALL') . '</option>';
		
		for( $i = 0; $i < count( $categories ); $i++ )
		{
			$selected	= ( $category == $categories[$i]->id ) ? ' selected="true"' : '';
			$select	.= '<option value="' . $categories[$i]->id . '"' . $selected . '>' . $categories[$i]->name . '</option>';
		}
		$select	.= '</select>';
		
		return $select;
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
		}else{
			$href  .= '<span><img src="images/' . $image . '" border="0" alt="' . $alt . '" /></span></a>';
		}

		
		return $href;
	}
	
	public function setToolBar()
	{
		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_EVENTS'), 'events');
		
		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME'), 'index.php?option=com_community');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList( JText::_('COM_COMMUNITY_EVENTS_DELETE_WARNING') , 'deleteEvent' , JText::_('COM_COMMUNITY_DELETE') );
		JToolBarHelper::divider();
		JToolBarHelper::publishList( 'publish' , JText::_('COM_COMMUNITY_PUBLISH') );
		JToolBarHelper::unpublishList( 'unpublish' , JText::_('COM_COMMUNITY_UNPUBLISH') );
	}
}