<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CFileHelper
{
	/**
	 * Upload a file
	 * @param	string	$source			File to upload
	 * @param	string	$destination	Upload to here
	 * @return True on success
	 */
	static public function upload( $source , $destination )	
	{
		$err		= null;
		$ret		= false;
	
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		
		// Load configurations.
		$config		= CFactory::getConfig();
	
		// Make the filename safe
		jimport('joomla.filesystem.file');
	
		if (!isset($source['name']))
		{
			JError::raiseNotice(100, JText::_('COM_COMMUNITY_INVALID_FILE_REQUEST'));
			return $ret;
		}
	
		$source['name']	= JFile::makeSafe($source['name']);
	
		if (is_dir($destination)) {
			jimport('joomla.filesystem.folder');
			JFolder::create( $destination, (int) octdec($config->get('folderpermissionsvideo')));
			JFile::copy( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'index.html' , $destination  . DS . 'index.html' );
			$destination = JPath::clean($destination . DS . strtolower($source['name']));
		}
	
		if (JFile::exists($destination))
		{
			JError::raiseNotice(100, JText::_('COM_COMMUNITY_FILE_EXISTS'));
			return $ret;
		}
	
		if (!JFile::upload($source['tmp_name'], $destination))
		{
			JError::raiseWarning(100, JText::_('COM_COMMUNITY_UNABLE_TO_UPLOAD_FILE'));
			return $ret;
		}
		else
		{
			$ret = true;
			return $ret;
		}
	
	}
	
	static public function getRandomFilename( $directory, $filename = '' , $extension = '', $length = 11 )
	{
		if( JString::strlen($directory) < 1)
			return false;
	
		$directory = JPath::clean($directory);
		
		// Load configurations.
		$config		= CFactory::getConfig();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
	
		if (!JFile::exists($directory))
		{
			JFolder::create( $directory, (int) octdec($config->get('folderpermissionsvideo')) );
			JFile::copy( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'index.html' , $directory . DS . 'index.html' );
		}
	
		if (strlen($filename) > 0)
			$filename	= JFile::makeSafe($filename);
	
		if (!strlen($extension) > 0)
			$extension	= '';
	
		$dotExtension 	= $filename ? JFile::getExt($filename) : $extension;
		$dotExtension 	= $dotExtension ? '.' . $dotExtension : '';
	
		$map			= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$len 			= strlen($map);
		$stat			= stat(__FILE__);
		$randFilename	= '';
	
		if(empty($stat) || !is_array($stat))
			$stat = array(php_uname());
	
		mt_srand(crc32(microtime() . implode('|', $stat)));
		for ($i = 0; $i < $length; $i ++) {
			$randFilename .= $map[mt_rand(0, $len -1)];
		}
	
		$randFilename .= $dotExtension;
	
		if (JFile::exists($directory . DS . $randFilename)) {
			cGenRandomFilename($directory, $filename, $extension, $length);
		}
	
		return $randFilename;
	}

}

/**
 * Deprecated since 1.8.x
 * Use CFileHelper::upload instead
 **/ 
function cUploadFile($source, $destination)
{
	return CFileHelper::upload( $source , $destination );
}

/**
 * Deprecated since 1.8.x
 * Use CFileHelper::getRandomFilename instead
 **/
function cGenRandomFilename($directory, $filename = '' , $extension = '', $length = 11)
{
	return CFileHelper::getRandomFilename( $directory , $filename , $extension , $length );
}