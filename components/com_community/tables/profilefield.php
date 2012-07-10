<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableProfileField extends JTable
{
	var $id 			= null;
	var $type			= null;
	var $ordering		= null;
	var $published		= null;
	var $min			= null;
	var $max			= null;
	var $name			= null;
	var $tips			= null;
	var $visible		= null;
	var $required		= null;
	var $searchable		= null;
	var $options		= null;
	var $fieldcode		= null;
	var $registration  	= null;

	public function __construct( &$db )
	{
		parent::__construct( '#__community_fields', 'id', $db );
	}	
}
