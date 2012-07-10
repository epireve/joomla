<?php

/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

$view	= JRequest::getCmd('view','cpanel');

// Load submenu's				
$views	= array(
					'cpanel'		=> 'Home',
					'setup'			=> 'Setup',
					'settings'		=> 'Settings',
					'profiletypes' 	=> 'Profile Types',
					'configuration'	=> 'JS Configuration',
					'jstoolbar'		=> 'JS Toolbar',
					'aclrules'		=> 'Acces Control',
					'profilefields'	=> 'Profile Field',
					'applications'	=> 'Applications'
					
				);
				
foreach( $views as $key => $val )
{
	$active	= ( $view == $key );
	JSubMenuHelper::addEntry( $val , 'index.php?option=com_xipt&view=' . $key , $active );
}