<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.cache.cache');

/**
 * Handle cache generate / remove in the model class & cache remove in cac
 *
 */
class CCache
{
	// Cache action constant
	const ACTION_SAVE    = 'save'; 
	const ACTION_REMOVE	 = 'delete';

	// Table's method name for cache
	const METHOD_DEL     = 'delete';
	const METHOD_SAVE	 = 'save'; 
	const METHOD_STORE   = 'store';

	public $aSetting  = array();

    // Pass in any obj to find the embeded caching object
    // This is to make sure there is a standard way to embed and use this caching object in any object
    public static function load($obj) {
        if (isset($obj->oCache) && $obj->oCache instanceof CCache) {
            return $obj->oCache;
        } else {
            return FALSE;
        }
    }

    // Pass in any obj to embed the caching object
    // This is to make sure there is a standard way to embed and use this caching object in any object
    public static function inject($obj) {
        $obj->oCache = new self();
        return $obj->oCache;
    }
    
    // Add method that need to be cache.
    public function addMethod($method, $action, $tag)
    {
    	$this->aSetting[$method] = array('action' => $action, 'tag' => $tag);
	}
	
	// Get the method cache flag.
	public function getMethod($method)
	{
		if (isset($this->aSetting[$method])) {
			return $this->aSetting[$method];
		} else {
			return FALSE;
		}
	}
	
	// Delete cache.
	public function remove($tag)
	{
		$oZendCache = CFactory::getCache('core');
		
		if ($tag == COMMUNITY_CACHE_TAG_ALL) {
			$oZendCache->clean(Zend_Cache::CLEANING_MODE_ALL);
		} else {
			$oZendCache->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, $tag);
		}
	}
	
}


class CFastCache {
	
	private $_handler = null;
	private $_caching = false;
	
	/**
	 * Constructor
	 *
	 */	 
	public function __construct($handler)
	{
		$this->_handler = $handler;
		$this->_caching = extension_loaded('apc');
	}
	
	
	/**
	 * Generate unique cache id
	 */	 	
	private function _getCacheId($id){
		return 'jomsocial-'.md5($id);
	}
	
	/**
	 * STore the cache
	 */	 	
	public function store($data, $id, $group=null){
		if(!$this->_caching)
			return;
			
		$cacheid = $this->_getCacheId($id);
		if(!is_null($group)){
			// Store the id list in the group list
			$tags = apc_fetch('jomsocial-tags');
			
			// If tags is missing, the whole cache might be invalid, clear it all
			if(!is_array($tags)){
				apc_clear_cache('user');
				$tags = array();
			}
			
			foreach($group as $tag){
				if(!isset($tags[$tag])){
					$tags[$tag] = array();
				}
				
				if(! in_array($cacheid, $tags[$tag]) )
					$tags[$tag][] = $cacheid;
			}
			
			// Store this key back
			apc_store('jomsocial-tags', $tags, 0 );
			
		}
		// Do not use any group
		return apc_store($cacheid, $data);
	}
	
	/**
	 * Return the cache
	 */	 	
	public function get($id){
		if(!$this->_caching)
			return FALSE;
			
		$cacheid = $this->_getCacheId($id);
		
		// Do not use any group
		return apc_fetch($cacheid);
	}
	
	/**
	 * Clean the cache. If group is specified, clean only the group
	 */	 	
	public function clean($group=null, $mode='group'){
		if(!$this->_caching)
			return;
			
		if(!is_null($group)){
			$tags = apc_fetch('jomsocial-tags');
			
			if(is_null($tags)){
				apc_clear_cache('user');
				$tags = array();
			}
			
			// @todo: for each cache id, we should clear it from other tag list as well
			foreach($group as $tag){
				if(!empty($tags[$tag])){
					foreach($tags[$tag] as $id){
						apc_delete($id);
					}
				}
			}
		} else {
			// Clear everything
			apc_clear_cache('user');
		}
		return true;
	}
}