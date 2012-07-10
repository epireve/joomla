<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableMemberListCriteria extends JTable
{
	var $id				= null;
	var $listid			= null;
	var $field			= null;
	var $condition		= null;
	var $value			= null;
	var $type			= null;
	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_memberlist_criteria' , 'id' , $db );
	}
}