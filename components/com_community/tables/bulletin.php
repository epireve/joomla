<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableBulletin extends JTable
{

	var $id 		= null;
	var $groupid	= null;
	var $created_by	= null;
	var $published	= null;
	var $title		= null;
	var $message	= null;
	var $date		= null;

	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_groups_bulletins', 'id', $db );
	}
	
	public function store()
	{
		if (!$this->check()) 
		{
			return false;
		}
		return parent::store();
	}
	
	public function check()
	{	
		$config = CFactory::getConfig();
		$safeHtmlFilter = CFactory::getInputFilter( $config->get('allowhtml'));
		$this->title	= $safeHtmlFilter->clean($this->title);
		$this->message	= $safeHtmlFilter->clean($this->message);
		
		return true;
	}
}