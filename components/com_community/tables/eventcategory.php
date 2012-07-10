<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once ( JPATH_ROOT .DS.'components'.DS.'com_community'.DS.'models'.DS.'models.php');

class CTableEventCategory extends CTableCache
{

	var $id		    =	null;
	var $parent	    =	null;
	var $name	    =	null;
	var $description    =	null;


	/**
	 * Constructor
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__community_events_category', 'id', $db );

		// Get cache object.
 	 	$oCache = CCache::inject($this);
 	 	// Remove video cache on every delete & store
 	 	$oCache->addMethod(CCache::METHOD_DEL, CCache::ACTION_REMOVE, array( COMMUNITY_CACHE_TAG_EVENTS_CAT ) );
 	 	$oCache->addMethod(CCache::METHOD_STORE, CCache::ACTION_REMOVE, array( COMMUNITY_CACHE_TAG_EVENTS_CAT ) );
	}
}