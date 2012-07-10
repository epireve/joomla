<?php
/**
 * @version		$Id:mod_menu.php 2463 2006-02-18 06:05:38 $
 * @package		Joomla.Administrator
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Render the module layout
if (file_exists($this->templatePath . '/html/mod_menu/default_enabled.php')) {
	$tmplpath = $this->templatePath . '/html/mod_menu/';
} else {
	$tmplpath = $this->templatePath . '/lib/menu/tmpl/';
}

$fullpath_menupath = $tmplpath.($enabled ? 'default_enabled' : 'default_disabled').'.php';

require  ($fullpath_menupath);

$menu->renderMenu('mctrl-menu', $enabled ? 'menutop level1' : 'menutop level1 disabled');
