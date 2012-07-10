<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

function com_uninstall() 
{
	require_once ( JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'defines.community.php');
	
	//for Joomla 1.6, in xml file, community has been renamed to JomSocial during installing plugins
	if (JVERSION >= '1.6'){
		$asset	= JTable::getInstance('Asset');
		if ($asset->loadByName('com_community')) {
			$asset->delete();
		}
	}		
	$db =& JFactory::getDBO();	
	
	//remove jomsocialuser plugin during uninstall to prevent error during login/logout of joomla.
	$query = 'DELETE FROM ' 
			. $db->nameQuote(PLUGIN_TABLE_NAME) . ' '
		 	. 'WHERE ' . $db->nameQuote('element') . '=' . $db->quote('jomsocialuser') . ' AND '
		 	. $db->nameQuote('folder') . '=' . $db->quote('user');

	$db->setQuery($query);
	$db->query();
	if (JVERSION >= '1.6'){
		$plugin_path = JPATH_ROOT.DS.'plugins'.DS.'user'.DS.'jomsocialuser';	
	} else {
		$plugin_path = JPATH_ROOT.DS.'plugins'.DS.'user';	
	}
	
	if(JFile::exists($plugin_path . DS .'jomsocialuser.php'))
	{
		JFile::delete($plugin_path . DS .'jomsocialuser.php');
	}
	
	if(JFile::exists($plugin_path . DS .'jomsocialuser.xml'))
	{
		JFile::delete($plugin_path . DS .'jomsocialuser.xml');
	}
	
	removeBackupTemplate('blueface');
	removeBackupTemplate('bubble');
	removeBackupTemplate('blackout');

	return true;   
}

function removeBackupTemplate($templateName)
{
	$templatesPath = JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'templates' . DS;
	if(JFolder::exists($templatesPath)){
		$backups = JFolder::folders($templatesPath, '^' . $templateName . '_bak[0-9]');
	
		foreach($backups as $backup)
		{
			JFolder::delete($templatesPath . $backup);
		}
	}
}