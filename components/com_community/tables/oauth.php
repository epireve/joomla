<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableOauth extends JTable
{
	var $userid			= null;
	var $requesttoken	= null;
	var $accesstoken 	= null;
	var $app            = null;
	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_oauth', 'id', $db );
	}

	public function load( $userId , $app )
	{
		$db		=& JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote('userid') . '=' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote('app') . '=' . $db->Quote( $app );
		$db->setQuery( $query );
		$result	= $db->loadAssoc();

		$this->bind( $db->loadAssoc() );

		if( $result )
			return true;

		return false;
	}

	public function delete()
	{
		$db		=& $this->getDBO();
		$query  = 'DELETE FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $this->userid );
		$db->setQuery( $query );
		return $db->Query();
	}
	
	public function store()
	{
		$db		=& $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $this->userid );

		$db->setQuery($query);
		$result	= $db->loadResult();

		if( !$result )
		{
			$obj			= new stdClass();
			
			$obj->userid		= $this->userid;
			$obj->requesttoken	= $this->requesttoken;
			$obj->accesstoken   = $this->accesstoken;
			$obj->app           = $this->app;
			return $db->insertObject( $this->_tbl , $obj );
		}

 		// Existing table, just need to update
		return $db->updateObject( $this->_tbl , $this, 'userid' , false );
	}
}
