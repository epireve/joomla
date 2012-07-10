<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

define( 'COMMUNITY_ASSETS_PATH' 	, JPATH_COMPONENT . DS . 'assets' );
define( 'COMMUNITY_ASSETS_URL' 		, JURI::base() . 'components/com_community/assets' );
define( 'COMMUNITY_BASE_PATH'		, dirname( JPATH_BASE ) . DS . 'components' . DS . 'com_community' );
define( 'COMMUNITY_BASE_ASSETS_PATH', JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'assets' );
define( 'COMMUNITY_BASE_ASSETS_URL'	, JURI::root() . 'components/com_community/assets' );
define( 'COMMUNITY_CONTROLLERS' , JPATH_COMPONENT . DS . 'controllers' );

jimport('joomla.version');
$version = new JVersion();
$joomla_ver = $version->getHelpVersion();

if ($joomla_ver<= '0.15') {
	define('JOOMLA_LEGACY_VERSION',1);
}
else
{
	define('JOOMLA_LEGACY_VERSION',0);
}