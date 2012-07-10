<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.folder' );
		
$xiptPath = JPATH_ROOT.DS.'components'.DS.'com_xipt';

if(!JFolder::exists($xiptPath))
	return false;
	
//load language
$language = JFactory::getLanguage();
$language->load('mod_xipt_conditional');

//get user instance
$user	= JFactory::getUser();

// Include the helper functions only once
require_once (dirname(__FILE__).DS.'helper.php');

echo XiptConditionalModHelper::displayModules($user->id, $params);