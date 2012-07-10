<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'tables' . DS . 'cache.php' );
/**
 * Jom Social Table Model
 */
class CommunityTableConfiguration extends CTableCache
{
	var $name		= null;
	var $params		= null;
	
	public function __construct(&$db)
	{
		parent::__construct( '#__community_config' , 'name' , $db );
		
		// Get cache object.
 	 	$oCache = CCache::inject($this);
 	 	// Remove all cache on configuration change.
 	 	$oCache->addMethod(CCache::METHOD_STORE, CCache::ACTION_REMOVE, COMMUNITY_CACHE_TAG_ALL);
	}
}