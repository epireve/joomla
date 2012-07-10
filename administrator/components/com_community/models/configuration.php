<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class CommunityModelConfiguration extends JModel
{
	/**
	 * Configuration data
	 * 
	 * @var object
	 **/	 	 	 
	var $_params;

	/**
	 * Configuration for ini path
	 * 
	 * @var string
	 **/	 	 	 
// 	var $_ini	= '';

	/**
	 * Configuration for xml path
	 * 
	 * @var string
	 **/	 	 	 
	var $_xml	= '';
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$mainframe	=& JFactory::getApplication();

		// Test if ini path is set
// 		if( empty( $this->_ini ) )
// 		{
// 			$this->_ini	= JPATH_COMPONENT . DS . 'config.ini';
// 		}

		// Test if ini path is set
		if( empty( $this->_xml ) )
		{
			$this->_xml	= JPATH_COMPONENT . DS . 'config.xml';
		}
		
		// Call the parents constructor
		parent::__construct();

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( 'com_community.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	private function _updateUserPrivacy( $key , $value )
	{
		$db		= $this->getDBO();
		
		$seperate_char = (JOOMLA_LEGACY_VERSION)?'=':':';
		$quote = (JOOMLA_LEGACY_VERSION)?'':"\"";
		$left_exp = $quote.$key.$quote . $seperate_char;
		$right_exp = $quote.$value.$quote;
		// Update photos privacy
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.'0') . ',' . $db->Quote( $left_exp . $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.$quote.'0'.$quote) . ',' . $db->Quote( $left_exp . $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );
		
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.'10') . ',' . $db->Quote( $left_exp. $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.$quote.'10'.$quote) . ',' . $db->Quote( $left_exp. $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );
		
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.'20') . ',' . $db->Quote( $left_exp. $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.$quote.'20'.$quote) . ',' . $db->Quote( $left_exp. $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );

		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.'30') . ',' . $db->Quote( $left_exp. $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.$quote.'30'.$quote) . ',' . $db->Quote( $left_exp. $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );

		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.'40') . ',' . $db->Quote( $left_exp. $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') . ' SET params=replace(params,' . $db->Quote( $left_exp.$quote.'40'.$quote) . ',' . $db->Quote( $left_exp. $right_exp ) . ')';
		$db->setQuery( $query );
		$db->Query( );
	}
		
	public function updatePrivacy( $photoPrivacy = 0, $profilePrivacy = 0, $friendsPrivacy = 0 )
	{
		$db		= $this->getDBO();
		
		$this->_updateUserPrivacy( 'privacyPhotoView' , $photoPrivacy );
		$this->_updateUserPrivacy( 'privacyProfileView' , $profilePrivacy );
		$this->_updateUserPrivacy( 'privacyFriendsView' , $friendsPrivacy );
		
		return true;
	}
	
	/**
	 * Returns the configuration object
	 *
	 * @return object	JParameter object
	 **/	 
	public function getParams()
	{
		// Test if the config is already loaded.
		if( !$this->_params )
		{
			jimport( 'joomla.filesystem.file');
			$ini	= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'default.ini';
			$data	= JFile::read($ini);
			
			// Load default configuration
			$this->_params	= new CParameter( $data );

			$config		=& JTable::getInstance( 'configuration' , 'CommunityTable' );
			$config->load( 'config' );
			
			// Bind the user saved configuration.
			$this->_params->bind( $config->params );
		}
		return $this->_params;
	}
	
	public function save()
	{
		jimport('joomla.filesystem.file');
		CFactory::load('helpers', 'string');

		$config	=& JTable::getInstance( 'configuration' , 'CommunityTable' );
		$config->load( 'config' );
		
		
		$params	= new JParameter( $config->params );

		$postData	= JRequest::get( 'post' , 2 );
		
		$token		= JUtility::getToken();
		unset($postData[$token]);

		foreach( $postData as $key => $value )
		{
			if( $key != 'task' && $key != 'option' && $key != 'view' && $key != $token )
			{
				$params->set( $key , $value );
			}
		}
		$config->params	= $params->toString();

		// Save it
		if(!$config->store() )
		{
			return false;
		}
		return true;
	}
	
	public function updateTemplate( $template )
	{
		jimport('joomla.filesystem.file');
		CFactory::load('helpers', 'string');

		$config	=& JTable::getInstance( 'configuration' , 'CommunityTable' );
		$config->load( 'config' );
		
		$params	= new JParameter( $config->params );
		$params->set( 'template' , $template );
		
		$config->params	= $params->toString();

		// Save it
		if(!$config->store() )
		{
			return false;
		}
		return true;
	}
}