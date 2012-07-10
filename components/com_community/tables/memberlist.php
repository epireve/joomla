<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableMemberList extends JTable
{
	var $id				= null;
	var $title			= null;
	var $description	= null;
	var $condition		= null;
	var $avataronly		= null;
	var $created		= null;
	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_memberlist' , 'id' , $db );
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function getCriterias()
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM '
				. $db->nameQuote( '#__community_memberlist_criteria' ) . ' WHERE '
				. $db->nameQuote( 'listid' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();
		
		$childs	= array();
		
		foreach( $rows as $row )
		{
			$criteria	=& JTable::getInstance( 'MemberListCriteria' , 'CTable' );
			$criteria->load( $row->id );
			$childs[]	= $criteria;
		}
		
		return $childs;
	}
	
	public function delete()
	{
		//Delete criterias first.
		$criterias	= $this->getCriterias();
		
		foreach( $criterias as $criteria )
		{
			$criteria->delete();
		}
		
		return parent::delete();
	}
}