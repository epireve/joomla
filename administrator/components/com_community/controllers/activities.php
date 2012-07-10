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

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

/**
 * Jom Social Component Controller
 */
class CommunityControllerActivities extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
	}
	
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
			
			$users	=& $this->getModel( 'users' );
			$view->setModel( $users  , false );
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
	
	public function delete()
	{
		$mainframe	=& JFactory::getApplication();
		$model		=& $this->getModel( 'activities' );
		$id			= JRequest::getVar( 'cid' , '' , 'post' );
		$errors		= false;
		$message	= JText::_('COM_COMMUNITY_ACTIVITIES_DELETED');
		if( empty($id) )
		{
			JError::raiseError( '500' , JText::_('COM_COMMUNITY_INVALID_ID') );
		}
		
		for( $i = 0; $i < count($id); $i++ )
		{
			if( !$model->delete( $id[ $i ] ) )
			{
				$errors	= true;
			}
		}

		if( $errors )
		{
			$message	= JText::_('COM_COMMUNITY_ACTIVITIES_DELETING_ERROR');
		}
		$mainframe->redirect( 'index.php?option=com_community&view=activities' , $message );
	}
	
	public function purge()
	{
		$mainframe	=& JFactory::getApplication();
		$model		=& $this->getModel( 'activities' );
		$message	= JText::_('COM_COMMUNITY_ACTIVITIES_PURGED');

		if( !$model->purge() )
		{
			$message	= JText::_('COM_COMMUNITY_ACTIVITIES_DELETING_ERROR');
		}
		$mainframe->redirect( 'index.php?option=com_community&view=activities' , $message );
	}
}