<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

class CMenuHelper
{
	/**
	 *	Returns an object of data containing user's address information
	 *
	 *	@access	static
	 *	@params	int	$userId
	 *	@return stdClass Object	 	 	 
	 **/
	static public function getComponentId()
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM '
				. $db->nameQuote( '#__components' ) . ' WHERE '
				. $db->nameQuote( 'option' ) . '=' . $db->Quote( 'com_community' ) . ' '
				. 'AND ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'option=com_community' );
	
		if(!C_JOOMLA_15){
			//component id is retrieved from #__extensions table, overwrite query
			$query	= 'SELECT ' . $db->nameQuote( 'extension_id' ) . ' FROM '
					. $db->nameQuote( '#__extensions' ) . ' WHERE '
					. $db->nameQuote( 'element' ) . '=' . $db->Quote( 'com_community' ) . ' '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'component' );
		}
		
		$db->setQuery( $query );
		return $db->loadResult();
	}
	
	static public function getMenuIdByTitle($title){
		$db		=& JFactory::getDBO();
		//component id is retrieved from #__extensions table, overwrite query
		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM '
				. $db->nameQuote( '#__menu' ) . ' WHERE '
				. $db->nameQuote( 'title' ) . '=' . $db->Quote( $title );
		
		$db->setQuery( $query );
		return $db->loadResult();
	}
	
	
	//to update parent_id and level field in the menu table because the store funtion wont work
	static public function alterMenuTable($id){
		$db		=& JFactory::getDBO();
		
		$data = new stdClass();
		$data -> id = $id;
		$data -> level = 1;
		$data -> parent_id = 1;
		$db->updateObject( '#__menu' , $data , 'id' );
	}
}