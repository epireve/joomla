<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	FieldValue 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableFieldValue extends JTable
{
	var $id 		= null;
	var $user_id	= null;
	var $field_id	= null;
	var $value		= null;
	var $access		= null;

	public function __construct( &$db )
	{
		parent::__construct( '#__community_fields_values', 'id', $db );
	}
	
	public function load( $userId , $fieldId )
	{
		$db		= $this->getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_fields_values' ) . ' '
				. 'WHERE ' . $db->nameQuote('field_id') . ' = ' . $db->Quote( $fieldId ) . ' AND '
				. $db->nameQuote('user_id') . '=' . $db->Quote( $userId );
		$db->setQuery( $query );
		$result	= $db->loadObject();

		return $this->bind( $result );
	}
}
