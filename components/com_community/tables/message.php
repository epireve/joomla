<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableMessage extends JTable
{
	var $id 		= null;
	var $from 		= null;
	var $parent		= null;
	var $deleted	= null;
	var $from_name	= null;
	var $posted_on	= null;
	var $subject	= null;
	var $body		= null;	
	
	/**
	 * Constructor
	 */	 	
	public function __construct( &$db ) {
		parent::__construct( '#__community_msg', 'id', $db );
	}
}
