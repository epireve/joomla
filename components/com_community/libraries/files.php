<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.utilities.utility');

class CFiles
{
	var $_path		= '';

	var $_allowed	= '';
	var $_maxsize	= '';
	
	public function CFiles( $path , $force = true )
	{


		// If path is not defined, we assume that it is the default path used
		if( is_null($path) )
		{
			//@todo: make this configurable?
			$this->_path		= JPATH_BASE . DS . 'images' . DS . 'files';
		}
		else
		{
			$this->_path	= $path;
			
			if( !JFile::exists( $this->_path ) && ($force) )
			{
				JFolder::create( $this->_path );
				JFile::copy( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'index.html' , $this->_path . DS . 'index.html' );
			}
		}
		$this->_init();
	}


	public function log($content)
	{
 		$filename = JPATH_BASE . '/community_debug.txt';
		$handle	= fopen($filename, 'w');
		ob_start();
		var_dump($content);
		$contents	= ob_get_contents();
		ob_end_clean();
	
		fwrite($handle, $contents);
		fclose($handle);

	}
	
	public function _init()
	{
		// Size in kilobytes
		//@todo: make this configurable?
		$this->_maxsize		= '51200';

		$this->_allowed	= array();
		
		//@todo: configurable icon templates?
		$icons			= 'components/com_community/templates/default/images/icons/files';
		
		$this->_allowed['application/pdf']		= $icons . '/pdf.gif';
		$this->_allowed['application/zip']		= $icons . '/zip.gif';
		$this->_allowed['application/msword']	= $icons . '/word.gif';
		$this->_allowed['text/html']			= $icons . '/html.gif';
		$this->_allowed['text/plain']			= $icons . '/text.gif';
	}
	
	public function upload($file)
	{
		// Get the file size to kilobytes
		$size	= round($file['size'] / 1024);
		
		// Test if extension is allowed.
		if( !array_key_exists( $file['type'] , $this->_allowed ) || ($size > $this->_maxsize) )
		{
			//$this->log($file['type']);
			// Display error.
			return false;
		}
		$filename	= JString::trim($file['name']);
		
		// Use uniqid to set the filename.
		$filename	= uniqid();

		JFile::upload( $file['tmp_name'] , $this->_path . DS . $filename );
		
		$fileObj	= new stdClass();

		$fileObj->type			= $file['type'];
		$fileObj->source		= $filename;
		$fileObj->thumbnail		= $this->_allowed[$file['type']];
		
		return $fileObj;
	}
}

/**
 * Maintain classname compatibility with JomSocial 1.6 below
 */ 
class CFilesLibrary extends CFiles
{}