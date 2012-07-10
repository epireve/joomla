<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Profile
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once ( JPATH_ROOT .DS.'components'.DS.'com_community'.DS.'models'.DS.'models.php');

jimport( 'joomla.filesystem.file');

// Deprecated since 1.8.x to support older modules / plugins
CFactory::load( 'tables' , 'connect' );

class CommunityModelConnect extends JCCModel
{

	/**
	 * Constructor
	 */
	public function CommunityModelBulletins()
	{
		parent::JCCModel();
		
		$mainframe = JFactory::getApplication();
		
		// Get pagination request variables
 	 	$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
	    $limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');
	    
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	public function updateConnectUserId( $connectid , $type , $userid )
	{
		$db		= JFactory::getDBO();
		
		$query	= 'UPDATE ' . $db->nameQuote( '#__community_connect_users' ) . ' '
				. 'SET ' . $db->nameQuote('userid') . '=' . $db->Quote( $userid ) . ' '
				. 'WHERE ' . $db->nameQuote( 'connectid' ) . '=' . $db->Quote( $connectid ) . ' '
				. 'AND ' . $db->nameQuote('type') . '=' . $db->Quote( $type );
		$db->setQuery( $query );
		$db->query();
		
		return $this;
	}
	
	public function isAssociated( $userId )
	{
		$db		= JFactory::getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_connect_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userId );
				
		$db->setQuery( $query );
		
		$exist	= ( $db->loadResult() > 0 ) ? true : false;
		return $exist;
	}
	
	public function statusExists( $status , $userId )
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__community_activities' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'actor' ) . '=' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote( 'app' ) . '=' . $db->Quote( 'profile' )
				. 'AND ' . $db->nameQuote( 'title' ) . '=' . $db->Quote( '{actor} ' . $status );

		$db->setQuery( $query );

		$exist	= ( $db->loadResult() > 0 ) ? true : false;
		return $exist;
	}
}
