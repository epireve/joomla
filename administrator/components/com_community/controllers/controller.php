<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Jom Social Base Controller
 */
class CommunityController extends JController
{
	public function __construct()
	{
		parent::__construct();
				
		// Only process this if task != azrul_ajax
		$task	= JRequest::getCmd( 'task' , '' );

		// Add some javascript that may be needed
		$document	=& JFactory::getDocument();

		if( $task != 'azrul_ajax' )
		{
			$document->addScript( COMMUNITY_BASE_ASSETS_URL . '/joms.jquery.js' );
			$document->addScript( COMMUNITY_BASE_ASSETS_URL . '/window-1.0.js' );
			$document->addScript( COMMUNITY_ASSETS_URL . '/admin.js' );
		}
		// Attach the Front end Window CSS
		$css		= rtrim( JURI::root() , '/' ) . '/components/com_community/assets/window.css';
		$document->addStyleSheet( $css );

		// Attach the back end css
		$css		= COMMUNITY_ASSETS_URL . '/default.css';
		$document->addStyleSheet( $css );
		
	}

	/**
	 * Method to display the specific view
	 *
	 **/	 	
	public function display()
	{
		$viewName	= JRequest::getCmd( 'view' , 'community' );

		// Set the default layout and view name
		$layout		= JRequest::getCmd( 'layout' , 'default' );

		// Get the document object
		$document	=& JFactory::getDocument();

		// Get the view type
		$viewType	= $document->getType();
		
		// Get the view
		$view		=& $this->getView( $viewName , $viewType );

		$model		=& $this->getModel( $viewName );
		
		if( $model )
		{
			$view->setModel( $model , $viewName );
		}

		// Set the layout
		$view->setLayout( $layout );

		// Display the view
		$view->display();
		
		// Display Toolbar. View must have setToolBar method
		if( method_exists( $view , 'setToolBar') )
		{
			$view->setToolBar();
		}
	}

	/**	
	 * Save the publish status
	 *	 	
	 * @access public
	 *
	 **/
	public function savePublish( $tableClass = 'CommunityTable' )
	{
		$mainframe	=& JFactory::getApplication();
		
		// Determine the view.
		$viewName	= JRequest::getCmd( 'view' , 'configuration' );
		
		// Determine whether to publish or unpublish
		$state	= ( JRequest::getWord( 'task' , '' ) == 'publish' ) ? 1 : 0;

		$id		= JRequest::getVar( 'cid', array(), 'post', 'array' );

		$count	= count($id);

		$table	=& JTable::getInstance( $viewName , $tableClass );		
		$table->publish( $id , $state );

		switch ($state)
		{
			case 1:
				$message = JText::sprintf('Item(s) successfully Published', $count);
				break;
			case 0:
				$message = JText::sprintf('Item(s) successfully Unpublished', $count);
				break;
		}
		$mainframe->redirect( 'index.php?option=com_community&view=' . $viewName , $message );
	}

	/**
	 * AJAX method to toggle publish status
	 * 
	 * @param	int	id	Current field id
	 * @param	string field	The field publish type
	 * 
	 * @return	JAXResponse object	Azrul's AJAX Response object
	 **/
	public function ajaxTogglePublish( $id, $field , $viewName )
	{
		$user	=& JFactory::getUser();

		// @rule: Disallow guests.
		if ( $user->get('guest'))
		{
			JError::raiseError( 403, JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN') );
			return;
		}

		$response	= new JAXResponse();

		// Load the JTable Object.
		$row	=& JTable::getInstance( $viewName , 'CommunityTable' );
		$row->load( $id );

		if( $row->$field )
		{
			$row->$field	= 0;
			$row->store();
			$image			= 'publish_x.png';
		}
		else
		{
			$row->$field	= 1;
			$row->store();
			$image			= 'tick.png';
		}
		// Get the view
		$view		=& $this->getView( $viewName , 'html' );

		$html	= $view->getPublish( $row , $field , $viewName . ',ajaxTogglePublish' );
	   	
	   	$response->addAssign( $field . $id , 'innerHTML' , $html );
	   	
	   	return $response->sendResponse();
	}
	public function cacheClean($cacheId){
		$cache = CFactory::getFastCache();

		$cache->clean($cacheId);
	}
}