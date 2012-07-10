<?php
/**
 * @copyright (C) 2009 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
include_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'storage' . DS . 's3_lib.php');

class S3_CStorage
{
	public $accessKey = null;
	public $secretKey = null;
	public $s3 = null;
	public $bucket	= null;
	public $useSSL = false;
	public $name = 's3';
	
	public function _init(){
		if($this->s3 == null){
			$config = CFactory::getConfig();
			$this->accessKey = $config->get('storages3accesskey');
			$this->secretKey = $config->get('storages3secretkey');
			$this->bucket = $config->get('storages3bucket');
			$this->s3	= new S3($this->accessKey, $this->secretKey, $this->useSSL);
		}
	}
	
	/**
	 * Check if the given storage id exist. We perform local check via db since
	 * checking remotely is time consuming
	 * 
	 * @return true is file exits	 	 	 
	 **/	 	
	public function exists($storageid, $checkRemote = false)
	{
		$item = JTable::getInstance( 'StorageS3' , 'CTable' );
		return $item->load($storageid);
	}	
	
	/**
	 * Put the file into remote storage, 
	 * @return true if successful
	 */	
	public function put($storageid, $file)
	{
		$this->_init();
		
		// Put our file (also with public read access)
		if ($this->s3->putObjectFile($file, $this->bucket, $storageid, S3::ACL_PUBLIC_READ)) {
			
			// Insert into our s3 database
			$item =& JTable::getInstance( 'StorageS3' , 'CTable' );
			$item->storageid = $storageid;
			$item->resource_path = $storageid;
			if( !$item->store() ){
				echo $item->getError(); 
			}
			return true;
		}
		return false;
	
	
	}
	
	public function delete($storageid)
	{
		if (is_array($storageid)) {
			$storageids = $storageid;
		} else {
			$storageids[] = $storageid;
		}
		$this->_init();
		foreach ($storageids as $storageid)
		{
			$this->s3->deleteObject($this->bucket, $storageid);
			$item =& JTable::getInstance( 'StorageS3' , 'CTable' );
			$item->load($storageid);
			$item->delete();
		}
		return true;
	}
	
	/**
	 * Retrive the file from remote location and store it locally
	 * @param storageid The unique file we want to retrive
	 * @param file String	filename where we want to save the file	 	 
	 */	 	
	public function get($storageid, $file)
	{
		$this->_init();
		
		// Put our file (also with public read access)
		if ($this->s3->getObject($this->bucket, $storageid, $file)) {
			return true;
		}
		return false;
	}

	/**
	 * Return the absolute URI path to the resource  
	 */	 	
	public function getURI($storageId)
	{
		$item = JTable::getInstance( 'StorageS3' , 'CTable' );
		$item->load($storageId);
		
		return 'http://'.$this->bucket . '.s3.amazonaws.com/' .$item->resource_path; 
	}
}


class CTableStorageS3 extends JTable 
{
	var $storageid 		= null;
	var $resource_path	= null;
	
	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_storage_s3', 'storageid', $db );
	}
	
	public function store()
	{
		$k = $this->_tbl_key;

		if( empty($this->$k)){
			return false;
		}
			
		$db =& $this->getDBO();

		$query = 'SELECT count(*)'
		. ' FROM '.$this->_tbl
		. ' WHERE '.$this->_tbl_key.' = '.$db->Quote($this->storageid);
		$db->setQuery($query);
		$isExist = $db->loadResult();
 
		if(!$isExist){
			$query = 'INSERT INTO ' .$this->_tbl
			. ' SET ' . $db->nameQuote('storageid') .'=' 	.$db->Quote($this->storageid)
			. ' , ' . $db->nameQuote('resource_path') .'= '.$db->Quote($this->resource_path);
			$db->setQuery($query);
			$db->query();
			if($db->getErrorNum())
			{
				JError::raiseError(500, $db->stderr());
			}
		} else {
			$query = 'UPDATE ' .$this->_tbl
			. ' SET ' . $db->nameQuote('resource_path') .'= '.$db->Quote($this->resource_path)
			. ' WHERE ' . $db->nameQuote('storageid') .'='   .$db->Quote($this->storageid);
			$db->setQuery($query);
			$db->query();
			if($db->getErrorNum())
			{
				JError::raiseError(500, $db->stderr());
			}
		}
		
		return true;

	}
	
	
	
}