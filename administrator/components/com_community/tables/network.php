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
class CommunityTableNetwork extends JTable
{
	var $name		= null;
	var $params		= null;
	
	public function __construct(&$db)
	{
		parent::__construct( '#__community_config' , 'name' , $db );
	}
	
	/**
	 * Save the configuration
	 **/	 	
	public function store()
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_config') . ' '
				. 'WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote( 'network' );
		$db->setQuery( $query );

		$count	= $db->loadResult();

		$data	= new stdClass();
		$data->name		= 'network';
		$data->params	= $this->params;
		
		if( $count > 0 )
		{
			return $db->updateObject( '#__community_config' , $data , 'name' );
		}

		return $db->insertObject( '#__community_config' , $data, 'name' );
	}
	
}