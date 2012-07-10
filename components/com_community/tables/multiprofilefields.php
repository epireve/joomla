<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableMultiProfileFields extends JTable
{
	var $id			= null;
	var $parent		= null;
	var $field_id	= null;
  	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_profiles_fields', 'id', $db );
	}
}