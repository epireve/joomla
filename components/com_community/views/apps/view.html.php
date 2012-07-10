<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
jimport( 'joomla.utilities.arrayhelper');

class CommunityViewApps extends CommunityView
{
	/**
	 * Deprecated since 2.2.x
	 * Use index.php?option=com_community&view=profile&task=editPage instead	 	 	 
	 */	 	
	public function edit()
	{
		$mainframe	= JFactory::getApplication();
		$mainframe->redirect( CRoute::_( 'index.php?option=com_community&view=profile&task=editPage' , false ) );
	}

	/**
	 * Browse all available apps
	 */	 	
	public function browse($data)
	{
		$this->addPathway( JText::_('COM_COMMUNITY_APPS_BROWSE') );
		
		// Load window library
		CFactory::load( 'libraries' , 'window' );
		
		// Load necessary window css / javascript headers.
		CWindow::load();
		
		$mainframe =& JFactory::getApplication();
		$my		= CFactory::getUser();
		
		
		$pathway 	=& $mainframe->getPathway();

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_APPS_BROWSE'));

		// Attach apps-related js
		$this->showSubMenu();
	
		// Get application's favicon
		$addedAppCount	= 0;
		foreach( $data->applications as $appData )
		{	
			if( JFile::exists( CPluginHelper::getPluginPath('community',$appData->name) . DS . $appData->name . DS . 'favicon_64.png' ) )
			{
				$appData->appFavicon	= rtrim(JURI::root(),'/') . CPluginHelper::getPluginURI('community',$appData->name) .'/' . $appData->name . '/favicon_64.png';
			}
			else
			{
				$appData->appFavicon	= rtrim(JURI::root(),'/') . '/components/com_community/assets/app_favicon.png';
			}
			
			// Get total added applications
			$addedAppCount	= $appData->added == 1 ? $addedAppCount+1 : $addedAppCount;
		}
		
		$tmpl	=   new CTemplate();
		echo $tmpl  ->set( 'applications'   , $data->applications )
			    ->set( 'pagination'	    , $data->pagination )
			    ->set( 'addedAppCount'  , $addedAppCount )
			    ->fetch( 'applications.browse' );
	}
	
	public function ajaxBrowse($data)
	{
		$mainframe =& JFactory::getApplication();
		$my		= CFactory::getUser();

		// Get application's favicon
		$addedAppCount	= 0;

		foreach( $data->applications as $appData )
		{	
			if( JFile::exists( CPluginHelper::getPluginPath('community',$appData->name) . DS . $appData->name . DS . 'favicon_64.png' ) )
			{
				$appData->favicon['64'] = rtrim(JURI::root(),'/') . CPluginHelper::getPluginURI('community',$appData->name) . '/' . $appData->name . '/favicon_64.png';
			}
			else
			{
				$appData->favicon['64'] = rtrim(JURI::root(),'/') . '/components/com_community/assets/app_avatar.png';
			}
			// Get total added applications
			//$addedAppCount	= $appData->added == 1 ? $addedAppCount+1 : $addedAppCount;
		}

		$tmpl	=   new CTemplate();
		echo $tmpl  ->set( 'apps'	, $data->applications )
			    ->set( 'itemType'	, 'browse')
			    ->fetch( 'application.item' );		
	}

	public function _addSubmenu()
	{
		$this->addSubmenuItem('index.php?option=com_community&view=apps', JText::_('COM_COMMUNITY_APPS_MINE') );
	}

	public function showSubmenu(){
		$this->_addSubmenu();
		parent::showSubmenu();
	}
}
