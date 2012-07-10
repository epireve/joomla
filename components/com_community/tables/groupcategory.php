<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableGroupCategory extends CTableCache
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
		parent::__construct( '#__community_groups_category', 'id', $db );

		// Get cache object.
 	 	$oCache = CCache::inject($this);

		// Remove groups category cache on every delete & store
 	 	$oCache->addMethod(CCache::METHOD_DEL, CCache::ACTION_REMOVE, array( COMMUNITY_CACHE_TAG_GROUPS_CAT));
 	 	$oCache->addMethod(CCache::METHOD_STORE, CCache::ACTION_REMOVE, array( COMMUNITY_CACHE_TAG_GROUPS_CAT));
	}

	public function delete( $id )
	{
		$db	=& JFactory::getDBO();
		
		// Check if any groups are assigned into this category
		$strSQL		= 'SELECT COUNT(*) FROM ' . $db->nameQuote('#__community_groups') . ' '
					. 'WHERE ' . $db->nameQuote('categoryid') . '=' . $db->Quote($id);
		$db->setQuery( $strSQL );
		$count		= $db->loadResult();
	
		if($count <= 0)
		{
			// Only delete if no groups are assigned to this category.
			parent::delete( $id );
			return true;
		}
		
		return false;
	}
}