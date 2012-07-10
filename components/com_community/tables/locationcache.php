<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

class CTableLocationCache extends JTable
{
	var $id		= null;
	var $address =	null;
	var $latitude=	null;
	var $longitude=	null;
	var $data=	null;
	var $status=	null;
	var $created=	null;

	/**
	 * Constructor
	 */	 	
	public function __construct( &$db ) {
		parent::__construct( '#__community_location_cache', 'id', $db );
	}
	
	/**
	 * Onload, we try to load existing data, if any. If not, query from Google
	 */	 	
	public function load($address)
	{	
		// lowercase the incoming address
		$address = JString::strtolower( $address );
		
		$db =& JFactory::getDBO();
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_location_cache');
		$query	.= ' WHERE ';
		$query	.= $db->nameQuote('address') . '= ' . $db->quote($address);
		$query	.= ' LIMIT 1';
		$db->setQuery( $query );
		$obj = $db->loadObject();
		
		if($obj){
			$this->bind($obj);
		}
		else
		{
			CFactory::load('libraries', 'mapping');
			$data = CMapping::getAddressData($address);
			
			$this->address 	= $address;
			$this->latitude 	= COMMUNITY_LOCATION_NULL;
			$this->longitude 	= COMMUNITY_LOCATION_NULL; 
			$this->data = '';
			$this->status = 'ZERO_RESULTS';
			
			if($data != null)
			{
				require_once (AZRUL_SYSTEM_PATH.DS.'pc_includes'.DS.'JSON.php');
		
				$json = new Services_JSON();
				$content = $json->encode($data);
				
				if($data->status == 'OK'){
					$this->latitude 	= $data->results[0]->geometry->location->lat;
					$this->longitude 	= $data->results[0]->geometry->location->lng;
					$this->data		=  $content;
					$this->status = $data->status;
				}
			}
			
			$date = new JDate();
			$this->created = $date->toMySQL(true);
			
			$this->store();
		}
		
		return true;
	}
}