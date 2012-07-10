<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableFeatured extends JTable
{
	var $id			= null;
	var $cid		= null;
	var $created_by	= null;
	var $type		= null;
	var $created	= null;
	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_featured', 'id', $db );
	}
} 
