<?php
/**
 * @category	Plugins
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');
// Must global variable $jaxFuncNames to add function
// declaration to Community API.
global $jaxFuncNames;

// First argument should always be plugins to let Community know that its a plugin AJAX call.
// Second argument should be the plugin name, for instance 'profile'
// Third argument should be the plugin's function name to be called.
// It must be comma separated.
$jaxFuncNames[]	= 'plugins,profile,test';
$jaxFuncNames[]	= 'plugins,profile,saveProfile';
$jaxFuncNames[] = 'plugins,walls,ajaxSaveWall';
$jaxFuncNames[] = 'plugins,walls,ajaxRemoveWall';
$jaxFuncNames[] = 'plugins,walls,ajaxAddComment';
$jaxFuncNames[] = 'plugins,walls,ajaxRemoveComment';
