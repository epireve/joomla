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

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'apps.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'profile.php' );

/**
 * Configuration view for Jom Social
 */
class CommunityViewUsers extends JView
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 * 
	 * @param	string template	Template file name
	 **/	 	
	public function display( $tpl = null )
	{
		if( $this->getLayout() == 'edit' )
		{
			$this->_displayEditLayout( $tpl );
			return;
		}

		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_USERS'), 'users' );

		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME') , 'index.php?option=com_community');
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'export' , 'csv' , 'csv' , JText::_( 'COM_COMMUNITY_USERS_EXPORT_TO_CSV' ) );
		JToolBarHelper::trash('delete', JText::_('COM_COMMUNITY_DELETE'));
		$search		= JRequest::getVar( 'search' , '' );
		$model		=& $this->getModel( 'Users' );
		
		$users		=& $model->getAllUsers();
		$pagination	=& $model->getPagination();
		$mainframe	=& JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest( "com_community.users.filter_order",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "com_community.users.filter_order_Dir",	'filter_order_Dir',	'',			'word' );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		
		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');
		
		$usertype			= JRequest::getVar('usertype' , 'all' , 'POST' );
		
		$multiprofileModel	= & $this->getModel( 'Multiprofile' );
		$profileTypes		= $multiprofileModel->getMultiprofiles();
		$profileType		= JRequest::getVar( 'profiletype' , 'all' , 'POST' );

		$this->assignRef( 'profileType' , $profileType	);
		$this->assignRef( 'profileTypes', $profileTypes );
		$this->assignRef( 'search'		, $search );
		$this->assignRef( 'usertype'	, $usertype );
		$this->assignRef( 'users' 		, $users );
		$this->assignRef( 'lists' 		, $lists );
		$this->assignRef( 'pagination'	, $pagination );
		
		
		parent::display( $tpl );
	}

	public function element( $tpl = null )
	{
		/*$model		=& $this->getModel( 'users' );
		$userData = $model->getUsers();
		$info = array();

		foreach( $userData as $data )
		{
		    $info[] = array('id' => $data->id, 'name' => $data->name, 'username' => $data->username) ;

		}

		$this->assignRef( 'info'	, $info );
		*/
	    if( $this->getLayout() == 'edit' )
		{
			$this->_displayEditLayout( $tpl );
			return;
		}

		// Set the titlebar text
		JToolBarHelper::title( JText::_('COM_COMMUNITY_USERS'), 'users' );

		// Add the necessary buttons
		JToolBarHelper::back( JText::_('COM_COMMUNITY_HOME') , 'index.php?option=com_community');
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'export' , 'csv' , 'csv' , JText::_( 'COM_COMMUNITY_USERS_EXPORT_TO_CSV' ) );
		JToolBarHelper::trash('delete', JText::_('COM_COMMUNITY_DELETE'));
		$search		= JRequest::getVar( 'search' , '' );
		$model		=& $this->getModel( 'Users' );

		$users		=& $model->getAllUsers();
		$pagination	=& $model->getPagination();
		$mainframe	=& JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest( "com_community.users.filter_order",		'filter_order',		'a.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "com_community.users.filter_order_Dir",	'filter_order_Dir',	'',			'word' );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');

		$usertype			= JRequest::getVar('usertype' , 'all' , 'POST' );

		$multiprofileModel	= & $this->getModel( 'Multiprofile' );
		$profileTypes		= $multiprofileModel->getMultiprofiles();
		$profileType		= JRequest::getVar( 'profiletype' , 'all' , 'POST' );

		$this->assignRef( 'profileType' , $profileType	);
		$this->assignRef( 'profileTypes', $profileTypes );
		$this->assignRef( 'search'		, $search );
		$this->assignRef( 'usertype'	, $usertype );
		$this->assignRef( 'users' 		, $users );
		$this->assignRef( 'lists' 		, $lists );
		$this->assignRef( 'pagination'	, $pagination );

		parent::display( $tpl );
	}

	public function _displayEditLayout( $tpl )
	{
		// Load frontend language file.
		$lang	=& JFactory::getLanguage();
		$lang->load('com_community' , JPATH_ROOT );
		//Load com user language file for J!1.6
		$lang->load('com_users' , JPATH_ROOT);

		$userId		= JRequest::getVar( 'id' , '' , 'REQUEST' );
		$user		= CFactory::getUser( $userId );
		
		// Set the titlebar text
		JToolBarHelper::title( $user->username , 'users' );
		
 		// Add the necessary buttons
 		JToolBarHelper::back('Back' , 'index.php?option=com_community&view=users');
 		JToolBarHelper::divider();
 		JToolBarHelper::cancel('removeavatar',JText::_('COM_COMMUNITY_USERS_REMOVE_AVATAR') );
		JToolBarHelper::save();
 		
		
		$model      = CFactory::getModel( 'Profile' );
		$profile	= $model->getEditableProfile( $user->id , $user->getProfileType() );

		$config		=& CFactory::getConfig();
		
		$params		= $user->getParams();
		$userDST	= $params->get('daylightsavingoffset' );
		$offset		= (!empty($userDST) ) ? $userDST : $config->get( 'daylightsavingoffset' );
		
		$counter	= -4;
		$options	= array();
		for( $i=0 ; $i <= 8; $i++ , $counter++ )
		{
			$options[]	= JHTML::_( 'select.option' , $counter , $counter );
		}
		$offsetList	= JHTML::_(	'select.genericlist',  $options , 'daylightsavingoffset', 'class="inputbox" size="1"', 'value', 'text', $offset );	

		$user->profile	=& $profile;
 		$this->assignRef( 'user' , $user );

		$params = $user->getParameters(true);

		//Joomla 1.6 patch to display extra params
		if(C_JOOMLA_15==0){
			
			CFactory::load( 'libraries' , 'jform' );
			
			$params = &CJForm::getInstance('editDetails', JPATH_ADMINISTRATOR.'/components/com_users/models/forms/user.xml');
			$vals = $user->getParameters();	
			$vals = $vals->toArray();
						
			//set data for the form				
			foreach($vals as $k => $v){
				$params->setValue($k , 'params' , $v);
			}
			
		}

 		$this->assignRef( 'params' , $params );
 		$this->assignRef( 'offsetList'	, $offsetList );
 		
 		parent::display( $tpl );
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

		$image	= $row->$type ? $imgX : $imgY;

		$alt	= $row->$type ? JText::_('COM_COMMUNITY_PUBLISHED') : JText::_('COM_COMMUNITY_UNPUBLISH');

		$href = '<a class="jgrid" href="javascript:void(0);" onclick="azcommunity.togglePublish(\'' . $ajaxTask . '\',\'' . $row->id . '\',\'' . $type . '\');">';

		if(C_JOOMLA_15==0)
		{
			$state = $row->$type ? 'unpublish' : 'publish';
			$href .= '<span class="state '.$state.'"><span class="text">'.$alt.'</span></span></a>';
		}
		else
		{
			$href  .= '<span><img src="images/' . $image . '" border="0" alt="' . $alt . '" /></span></a>';
		}

		return $href;
	}
	
	public function getConnectType( $userId )
	{
		$model	=& $this->getModel( 'Users' );
		
		$type	= $model->getUserConnectType( $userId );

		$image	= '';
		switch( $type )
		{
			case 'facebook':
				$image	= '<img src="' . rtrim( JURI::root() , '/' ) . '/administrator/components/com_community/assets/icons/facebook.gif" />';
				break;
			case 'joomla':
			default:
				$image	= '<img src="' . rtrim( JURI::root() , '/' ) . '/administrator/components/com_community/assets/icons/joomla.gif" />';
				break;
		}
		return $image;
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