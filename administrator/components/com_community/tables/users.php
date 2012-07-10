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
 * Jom Social Table Model
 */
class CommunityTableUsers extends JTable
{
	var $userid			= null;
	var $status			= null;
	var $points			= null;
	var $posted_on		= null;
	var $avatar			= null;
	var $thumb			= null;
	var $invite			= null;
	var $params			= null;
	var $view			= null;
	var $friendcount	= null;
	
	public function __construct(&$db)
	{
		parent::__construct('#__community_users','userid', $db);
	}

	public function getFriendCount()
	{
		$db		=& JFactory::getDBO();
				
		$query = 'SELECT COUNT(*) FROM '.$db->nameQuote( '#__community_connection').' AS ' . $db->nameQuote('a');		
		$query .= ' INNER JOIN '.$db->nameQuote( '#__users').' AS ' . $db->nameQuote('b') . ' ON ' . $db->nameQuote('a') . '.' .$db->nameQuote('connect_to') . ' = ' . $db->nameQuote('b') . '.' . $db->nameQuote('id');
		$query .= ' WHERE ' . $db->nameQuote('a') . '.' . $db->nameQuote('connect_from') . ' = '.$db->Quote( $this->userid );
		$query .= ' AND ' . $db->nameQuote('a'). '.' . $db->nameQuote('STATUS') . ' = ' . $db->Quote(1);

		$db->setQuery( $query );
		$count	= $db->loadResult();
		
		return $count;
	}
}