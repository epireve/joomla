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
 * Jom Social Component Controller
 */
class CommunityControllerConfiguration extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function ajaxResetPrivacy( $photoPrivacy = 0, $profilePrivacy = 0, $friendsPrivacy = 0 )
	{
		$response	= new JAXResponse();
		
		CFactory::load( 'helpers' , 'owner' );
		
		if( !COwnerHelper::isCommunityAdmin() )
		{
			$response->addScriptCall( JText::_('COM_COMMUNITY_NOT_ALLOWED'));
			return $response->sendResponse();
		}
		
		$model	= $this->getModel( 'Configuration' );
	
		$model->updatePrivacy( $photoPrivacy , $profilePrivacy , $friendsPrivacy );
		
		$response->addAssign( 'privacy-update-result', 'innerHTML' , JText::_('COM_COMMUNITY_FRONTPAGE_ALL_PRIVACY_RESET') );
		
		return $response->sendResponse();
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
			
			$network	=& $this->getModel( 'network' );
			$view->setModel( $network  , false );
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
	 * Method to save the configuration
	 **/	 	
	public function saveconfig()
	{
		// Test if this is really a post request
		$method	= JRequest::getMethod();
		
		if( $method == 'GET' )
		{
			JError::raiseError( 500 , JText::_('COM_COMMUNITY_ACCESS_NOT_ALLOWED') );
			return;
		}
		
		$mainframe	=& JFactory::getApplication();		

		$model	=& $this->getModel( 'Configuration' );
		
		// Try to save configurations
		if( $model->save() )
		{
			$message	= JText::_('COM_COMMUNITY_CONFIGURATION_UPDATED');

			$model	=& $this->getModel( 'Network' );
			
			// Try to save network configurations
			if( $model->save() )
			{
				$mainframe->redirect( 'index.php?option=com_community&view=configuration', $message );
			}
			else
			{
				JError::raiseWarning( 100 , JText::_('COM_COMMUNITY_CONFIGURATION_NETWORK_SAVE_FAIL') );
			}
		}
		else
		{
			JError::raiseWarning( 100 , JText::_('COM_COMMUNITY_CONFIGURATION_SAVE_FAIL') );
		}
	}
	
}