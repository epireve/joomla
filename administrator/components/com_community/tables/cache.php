<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

if( !class_exists( 'CTableCache') )
{
	class CTableCache extends JTable
	{	
		/**
		 *	check for cache remove.
		 **/
		public function delete( $oid=null )
	    {
			$this->deleteCache(CCache::METHOD_DEL);
	
	        return parent::delete($oid);
	    }
	    
		/**
		 *	check for cache remove.
		 **/
		
		public function save( $source, $order_filter='', $ignore='' )
	    {
			$this->deleteCache(CCache::METHOD_SAVE);
	
	        return parent::save( $source, $order_filter, $ignore);
		
		}
		
		/**
		 *	check for cache remove.
		 **/
		 
		public function store( $updateNulls=false )
		{
			$this->deleteCache(CCache::METHOD_STORE);
			
			return parent::store($updateNulls);
		}
		
		
		/**
		 *	Delete cahche on request.
		 **/
		public function deleteCache($method)
		{
			if ($oCache = CCache::load($this)) {
				if ($aSetting = $oCache->getMethod($method)) {
					$oCache->remove($aSetting['tag']);
		        }
	        }
		}
	
	}
}