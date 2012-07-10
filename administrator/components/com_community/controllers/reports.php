<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Jom Social Component Controller
 */
class CommunityControllerReports extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
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
		
		//$model		=& $this->getModel( 'Reporters' );
		$model		=& $this->getModel( 'Reports' );

		$view->setModel( $model );

		//add reporter model
		$modelReporter		=& $this->getModel( 'Reporters' );
		$view->setModel( $modelReporter );
		
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
	
	public function removeReport()
	{
		$mainframe	=& JFactory::getApplication();
		$ids		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$count		= count($ids);

		$row		=& JTable::getInstance( 'Reports', 'CommunityTable' );

		foreach( $ids as $id )
		{
			$row->load( $id );
			if( !$row->deleteChilds() || !$row->delete( $id ) )
			{
				// If there are any error when deleting, we just stop and redirect user with error.
				$message	= JText::_('COM_COMMUNITY_REPORTS_DELETE_ERROR');
				$mainframe->redirect( 'index.php?option=com_community&view=reports' , $message);
				exit;
			}
		}
		$message	= JText::sprintf( '%1$s Report(s) successfully removed.' , $count );
		$mainframe->redirect( 'index.php?option=com_community&view=reports' , $message );
	}
	
	/**
	 * Ajax functiion to handle ajax calls
	 */	 	
	public function ajaxPerformAction( $actionId, $ignore = 0 )
	{
		$objResponse	= new JAXResponse();
		$output			= '';

		// Require Jomsocial core lib
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

		$language	=& JFactory::getLanguage();
		
		$language->load( 'com_community' , JPATH_ROOT );
		
		// Get the action data
		$action	=& JTable::getInstance( 'ReportsActions' , 'CommunityTable' );
		$action->load( $actionId );

		// Get the report data
		$report	=& JTable::getInstance( 'Reports' , 'CommunityTable' );
		$report->load( $action->reportid );

		$actions    =	'<input type="button" class="button" onclick="cWindowHide(); location.reload();" value="' . JText::_('COM_COMMUNITY_CLOSE') . '"/>';

		if( $ignore != 0 )
		{
			$report->status	= 2;
			$report->store();
			$output	    =	JText::_('COM_COMMUNITY_REPORTS_IGNORED_MESSAGE');			

			$objResponse->addScriptCall('cWindowAddContent', $output, $actions);

			return $objResponse->sendResponse();
		}

				
		$method		= explode( ',' , $action->method );
		$args		= explode( ',' , $action->parameters );

		if( is_array( $method ) && $method[0] != 'plugins' )
		{	
			$controller	= JString::strtolower( $method[0] );
			
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . 'controller.php' );
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . $controller . '.php' );
			
			$controller	= JString::ucfirst( $controller );
			$controller	= 'Community' . $controller . 'Controller';
			$controller	= new $controller();

			$output		= call_user_func_array( array( &$controller , $method[1] ) , $args );
		}
		else if( is_array( $method ) && $method[0] == 'plugins' )
		{
			// Application method calls
			$element	= JString::strtolower( $method[1] );
			
			require_once( CPluginHelper::getPluginPath('community',$element) . DS . $element . '.php' );
			
			$className	= 'plgCommunity' . JString::ucfirst( $element );

			$output		= call_user_func_array( array( $className , $method[2] ) , $args );
		}

		$objResponse->addScriptCall('cWindowAddContent', $output, $actions);

		$report->status	= 1;
		$report->store();
		
		return $objResponse->sendResponse();
	}
	
	public function purgeProcessed()
	{
		$mainframe	=& JFactory::getApplication();
		$model		=& $this->getModel( 'Reports' );
		
		if( $model->purgeProcessed() )
		{
			$message	= JText::_('COM_COMMUNITY_PROCESSED_REPORTS_PURGE_ERROR');
			
		}
		else
		{
			$message	= JText::_('COM_COMMUNITY_PROCESSED_REPORTS_PURGE_ERROR');
		}
		
		$mainframe->redirect( 'index.php?option=com_community&view=reports' , $message );
	}
}
