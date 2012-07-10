<?php
/**
 * @copyright (C) 2009 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
include_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'storage' . DS . 's3_lib.php');

class File_CStorage
{
	
	public function _init(){
	}
	
	/**
	 * Check if the given storage id exist. We perform local check via db since
	 * checking remotely is time consuming
	 * 
	 * @return true is file exits	 	 	 
	 **/	 	
	public function exists($storageid, $checkRemote = false)
	{
		return JFile::exists(JPATH_ROOT.DS.$storageid);
	}	
	
	/**
	 * Put the file into remote storage, 
	 * @return true if successful
	 */	
	public function put($storageid, $file)
	{
		$storageid = JPATH_ROOT.DS.$storageid;
		JFile::copy($file, $storageid);
		return true;
	
	
	}
	
	/**
	 * Retrive the file from remote location and store it locally
	 * @param storageid The unique file we want to retrive
	 * @param file String	filename where we want to save the file	 	 
	 */	 	
	public function get($storageid, $file)
	{
		$storageid = JPATH_ROOT.DS.$storageid;
		JFile::copy($storageid, $file);
		return true;
	}

	/**
	 * Return the absolute URI path to the resource  
	 */	 	
	public function getURI($storageId)
	{
		$root = JString::rtrim(JURI::root(), '/');
		$storageId = JString::ltrim($storageId, '/');
		return $root. '/'. $storageId;
	}
	
	/**
	 * Remove the given file
	 */	 	
	public function delete($storageid)
	{
		$storageid = JPATH_ROOT.DS.$storageid;
		JFile::delete($storageid);
		return true;
	}
}

