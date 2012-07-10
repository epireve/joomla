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
class CommunityControllerMaintenance extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
		$this->enableAzrulSystem();
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

		$model		=& $this->getModel( 'groups' );
		$userModel	=& $this->getModel( 'users' );
		
		if( $model && $userModel )
		{
			$view->setModel( $model , $viewName );
			$view->setModel( $userModel , $viewName );
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
	
	public function ajaxPatchFriendTable()
	{
		$objResponse	= new JAXResponse();
		
		$db		=& JFactory::getDBO();
		
		$model	=& $this->getModel( 'Users' );
		$fields = $model->_getFields();
		
		if(! array_key_exists( 'friendcount' , $fields ) )
		{
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_users' ) . ' '
					. 'ADD '. $db->nameQuote('friendcount') . ' INT( 11 ) NOT NULL DEFAULT ' . $db->Quote('0') . ' AFTER ' . $db->nameQuote('view');
			$db->setQuery( $query );
			$db->query();			
		}				
	
		$objResponse->addScriptCall( 'joms.jQuery("#progress-status").append("<div>Community User Table Updated</div>");' );
		
		return $objResponse->sendResponse();
	}	
	
	public function ajaxPatchTable()
	{
		$objResponse	= new JAXResponse();
		
		$model	=& $this->getModel( 'Groups' );
		$fields = $model->_getFields();		
		
		$db		=& JFactory::getDBO();
		
		if((!array_key_exists( 'membercount' , $fields )) || (!array_key_exists( 'wallcount' , $fields )) || (!array_key_exists( 'discusscount' , $fields ))) {
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_groups' ) . ' '
					. 'ADD ' . $db->nameQuote('discusscount') . ' INT( 11 ) NOT NULL DEFAULT '. $db->Quote(0) .' AFTER ' . $db->nameQuote('thumb') . ' , '
					. 'ADD '. $db->nameQuote('wallcount') . ' INT( 11 ) NOT NULL DEFAULT ' . $db->Quote(0) . ' AFTER ' . $db->nameQuote('discusscount') . ' , '
					. 'ADD ' . $db->nameQuote('membercount') . ' INT( 11 ) NOT NULL DEFAULT ' . $db->Quote(0) . ' AFTER ' . $db->nameQuote('wallcount');
			$db->setQuery( $query );
			$db->query();
		}

		if(! $this->_isExistTableColumnIndex('#__community_fields', 'fieldcode')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_fields' ) . ' ADD INDEX ('. $db->nameQuote('fieldcode') . ')';
			$db->setQuery( $query );
			$db->query();
		}
		
		if(! $this->_isExistTableColumnIndex('#__community_fields_values', 'user_id')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_fields_values' ) . ' ADD INDEX (' . $db->nameQuote('user_id') . ')';
			$db->setQuery( $query );
			$db->query();
		}

		if(! $this->_isExistTableColumnIndex('#__community_fields_values', 'field_id')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_fields_values') . ' ADD INDEX (' . $db->nameQuote('field_id') . ')';
			$db->setQuery( $query );
			$db->query();
		}

		if(! $this->_isExistTableColumnIndex('#__community_apps', 'userid')){
			$query	= 'ALTER TABLE ' . $db->nameQuote('#__community_apps') . ' ADD INDEX (' . $db->nameQuote('userid') . ')';
			$db->setQuery( $query );
			$db->query();
		}

		if(! $this->_isExistTableColumnIndex('#__community_connection', 'connect_from')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_connection' ) . ' ADD INDEX (' . $db->nameQuote('connect_from') . ')';
			$db->setQuery( $query );
			$db->query();
		}
		
		if(! $this->_isExistTableColumnIndex('#__community_connection', 'connect_to')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_connection' ) . ' ADD INDEX (' . $db->nameQuote('connect_to') . ')';
			$db->setQuery( $query );
			$db->query();
		}
		
		if(! $this->_isExistTableColumnIndex('#__community_connection', 'status')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_connection' ) . ' ADD INDEX (' . $db->nameQuote('status'). ')';
			$db->setQuery( $query );
			$db->query();
		}

		if(! $this->_isExistTableColumnIndex('#__community_groups_members', 'approved')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_groups_members' ) . ' ADD INDEX (' . $db->nameQuote('approved') . ')';
			$db->setQuery( $query );
			$db->query();
		}

		if(! $this->_isExistTableColumnIndex('#__community_photos', 'creator')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_photos' ) . ' ADD INDEX (' . $db->nameQuote('creator') . ')';
			$db->setQuery( $query );
			$db->query();
		}
		
		if(! $this->_isExistTableColumnIndex('#__community_activities', 'created')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_activities' ) . ' ADD INDEX (' . $db->nameQuote('created') . ')';
			$db->setQuery( $query );
			$db->query();
		}
		
		if(! $this->_isExistTableColumnIndex('#__community_activities', 'archived')){
			$query	= 'ALTER TABLE ' . $db->nameQuote( '#__community_activities' ) . ' ADD INDEX (' . $db->nameQuote('archived') . ')';
			$db->setQuery( $query );
			$db->query();
		}				
	
		$objResponse->addScriptCall( 'joms.jQuery("#progress-status").append("<div>Tables Updated</div>");' );
		
		return $objResponse->sendResponse();
	}
	
	/*
	 * Check table column index whether exists or not.
	 * index name == column name.	 
	 */	 
	public function _isExistTableColumnIndex($tablename, $columnname){
	
	
		$db		=& JFactory::getDBO();
		
		$query	= 'SHOW INDEX FROM ' . $db->nameQuote( $tablename );

		$db->setQuery( $query );
		
		$indexes	= $db->loadObjectList();

		foreach( $indexes as $index )
		{
			$result[ $index->Key_name ]	= $index->Column_name;
		}
		
		if(array_key_exists($columnname, $result)){
			return true;
		}
		
		return false;
	}
	
	public function ajaxPatch()
	{
		$objResponse	= new JAXResponse();
		
		$model			=& $this->getModel( 'Groups' );
		$groups			= $model->getAllGroups();
		
		for( $i = 0; $i < count($groups); $i++ )
		{
			$objResponse->addScriptCall( 'jax.call("community","admin,maintenance,ajaxPatchGroup","' . $groups[$i]->id . '");');
		}
		
		//patch for user friend count.
		$uModel			=& $this->getModel( 'Users' );
		$users			= $uModel->getAllCommunityUsers();
		
		for( $i = 0; $i < count($users); $i++ )
		{
			$objResponse->addScriptCall( 'jax.call("community","admin,maintenance,ajaxPatchFriend","' . $users[$i]->userid . '");');
		}		

		return $objResponse->sendResponse();
	}
	
	public function ajaxPatchFriend( $userId )
	{
		$objResponse	= new JAXResponse();
		
		$row		=& JTable::getInstance( 'users', 'CommunityTable' );
		$row->load( $userId );
		
		$row->friendcount	= $row->getFriendCount();

		$row->store();
		
		$objResponse->addScriptCall( 'joms.jQuery("#progress-status").append("<div>User ID <strong>' . $row->userid . '</strong> Updated</div>");' );
		
		return $objResponse->sendResponse();
	}

	public function ajaxPatchGroup( $groupId )
	{
		$objResponse	= new JAXResponse();
		
		$row		=& JTable::getInstance( 'groups', 'CommunityTable' );
		$row->load( $groupId );
		
		$row->discusscount	= $row->getDiscussCount();
		$row->membercount	= $row->getMembersCount();
		$row->wallcount		= $row->getWallCount();

		$row->store();
		
		$objResponse->addScriptCall( 'joms.jQuery("#progress-status").append("<div>Group <strong>' . $row->name . '</strong> Updated</div>");' );
		
		return $objResponse->sendResponse();
	}
	
	public function ajaxPatchPrivacy( $limit = 1 )
	{
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
		
		$limitstart		= $limit - 1;

		$model			=& $this->getModel( 'users' );
		$userId			= $model->getSiteUsers( $limitstart , 1 );
				
		$objResponse	= new JAXResponse();
		
		$objResponse->addScriptCall( 'joms.jQuery("#no-progress").css("display","none");');
		$db				=& JFactory::getDBO();
		
		if( !empty( $userId ) )
		{
			$user		= CFactory::getUser( $userId );
			
			$params		= $user->getParams();
			
			// Fix old privacy issues.
			if($params->get('privacyPhotoView') == 1 )
			{
				$params->set('privacyPhotoView' , 0);
			}
			

			$query = 'UPDATE ' . $db->nameQuote( '#__community_photos' ) . ' '
					. 'SET ' . $db->nameQuote( 'permissions' ) . '=' . $params->get('privacyPhotoView') . ' '
					. 'WHERE ' . $db->nameQuote( 'creator' ) . '=' . $db->Quote( $user->id );
			$db->setQuery( $query );
			$db->query();
			
			$query = 'UPDATE ' . $db->nameQuote( '#__community_photos_albums' ) . ' '
					. 'SET ' . $db->nameQuote( 'permissions' ) . '=' . $params->get('privacyPhotoView') . ' '
					. 'WHERE ' . $db->nameQuote( 'creator' ) . '=' . $db->Quote( $user->id );
			$db->setQuery( $query );
			$user->save('params');

			
			$status		= '';
						
			if( $db->query() )
			{
				$status	= '<span style=\"color: green;\">' . JText::_('COM_COMMUNITY_SUCCESS') . '</span>';
			}
			else
			{
				$status	= '<span style=\"color: red;\">' . JText::_('COM_COMMUNITY_NOT_SUCCESS') . '</span>';
			}
			
			$objResponse->addScriptCall( 'joms.jQuery("#progress-status").append("<div>' . JText::sprintf('Updating user id <strong>%1$s</strong>. %2$s' , $user->id , $status ) . '</div>");');
	 		$objResponse->addScriptCall( 'jax.call("community","admin,maintenance,ajaxPatchPrivacy" , "' . ( $limit + 1 ) . '");');
		}
		else
		{
			// Just to make sure that we remove all references to 'all' once the last ajax query is called.
			$query		= 'UPDATE ' . $db->nameQuote( '#__community_photos' ) . ' '
						. 'SET ' . $db->nameQuote( 'permissions' ) . '=' . $db->Quote( 0 ) . ' '
						. 'WHERE ' . $db->nameQuote('permissions') . '=' . $db->Quote( 'all' );
			$db->setQuery( $query );
			$db->query();
			
			$query		= 'UPDATE ' . $db->nameQuote( '#__community_photos_albums' ) . ' '
						. 'SET ' . $db->nameQuote( 'permissions' ) . '=' . $db->Quote( '0' ) . ' '
						. 'WHERE ' . $db->nameQuote('permissions') . '=' . $db->Quote( 'all' );
			$db->setQuery( $query );
			$db->query();
			
			$objResponse->addScriptCall( 'joms.jQuery("#progress-status").append("<div style=\"font-weight:700;\">' . JText::_('COM_COMMUNITY_UPDATED') . '</div>");');
		}

 		$objResponse->sendResponse();
	}
	
	public function enableAzrulSystem()
	{
		$db =& JFactory::getDBO();
		
		$sql = ' UPDATE ' . $db->nameQuote(PLUGIN_TABLE_NAME) 
			 . ' SET ' . $db->nameQuote(EXTENSION_ENABLE_COL_NAME) . ' = ' . $db->quote('1') 
			 . ' WHERE ' . $db->nameQuote('element') . ' = ' . $db->quote('azrul.system');
		
		$db->setQuery($sql);
		$db->Query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		
		return TRUE;
	}
}