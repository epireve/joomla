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
class CommunityTableUserPoints extends JTable
{
	var $id					= null;
	var $rule_name			= null;
	var $rule_description	= null;
	var $rule_plugin		= null;
	var $action_string		= null;
	var $component			= null;
	var $access				= null;
	var $points				= null;
	var $published			= null;
	var $system				= null;

	public function __construct(&$db)
	{
		parent::__construct('#__community_userpoints','id', $db);
	}
	
	/**
	 * Bind AJAX data into object's property
	 * 
	 * @param	array	data	The data for this field
	 **/
	public function bindAjaxPost( $data )
	{
		// @todo: Need to check if all fields are valid!
		//$this->rule_name		= trim($data['rule_name']);
		//$this->rule_description	= trim($data['rule_description']);
		//$this->rule_plugin		= trim($data['rule_plugin']);
		$this->access 			= trim($data['access']);
		$this->points			= trim($data['points']);
		$this->published		= trim($data['published']);
		
	}	
	
	public function isRuleExist($rule)
	{
		$db		=& $this->getDBO();
		$db		=& JFactory::getDBO();
		
		$query = 'SELECT count(' . $db->nameQuote('id') . ' ) as '. $db->nameQuote('count') . '  FROM `#__community_userpoints`';
		$query .= ' WHERE ' . $db->nameQuote('action_string') . ' = '.$db->Quote($rule);

		$db->setQuery( $query );
		$count	= $db->loadResult();
		
		return ($count > 0) ? true : false;		
	}
	
}