<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableInvitation extends JTable
{
	var $id			= null;
	
	/**
	 * Callback method
	 **/	 	 	
	var $callback	= null;
	
	/**
	 * Unique identifier for the current invitation
	 **/	 	
	var $cid		= null;
	
	/**
	 * Comma separated values for user id's
	 **/	 	
	var $users		= null;
		
	public function __construct( &$db )
	{
		parent::__construct( '#__community_invitations' , 'id' , $db );
	}
	
	/**
	 * Override parent's method as the loading method will be based on the
	 * unique callback and cid
	 **/	 	 
	public function load( $callback , $cid )
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' WHERE '
				. $db->nameQuote( 'callback' ) . '=' . $db->Quote( $callback ) . ' '
				. 'AND ' . $db->nameQuote( 'cid' ) . '=' . $db->Quote( $cid );
		$db->setQuery( $query );
		$result	= $db->loadAssoc();

		$this->bind( $result );
	}
	
	/**
	 * Retrieves invited members from this table
	 * 
	 * @return	Array	$users	An array containing user id's	 	 
	 **/
	public function getInvitedUsers()
	{
		$users	= explode( ',' , $this->users );
		
		return $users;
	}

}