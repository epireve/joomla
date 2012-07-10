<?php
/**
 * CHANGELOG
 *
 * @package		gantry
 * @version		3.2.11 September 8, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>

1. Copyright and disclaimer
----------------


2. Changelog
------------
This is a non-exhaustive changelog for Gantry, inclusive of any alpha, beta, release candidate and final versions.

Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

------- 3.2.7 Release [15-Jul-2011] -------
^ Prepped for Joomla 1.7
+ Updated Google Webfonts

------- 3.2.6 Release [1-Jun-2011] -------
# Fix for missing calendar icons in edit screen
# Fix for assignment badges in opera
+ Added custom gantry field in order to have imagelist styled properly

------- 3.2.5 Release [1-Jun-2011] -------
# Fix for missing Webfonts
# Fix for Gantry Registry Formaters

------- 3.2.4 Release [20-Apr-2011] -------
+ Added support for https in webfonts feature
+ Added missing Google webfont names
+ Added support for Joomla 1.6 built in updater
^ Cleaned up plugin a bit

------- 3.2.3 Release [06-Apr-2011] -------
^ Reworked SmartLoad to take into account inline style or custom width/height images attribute
- Smartload no longer take in consideration img tags with no width and height attributes set. It's just not consistent across browsers and acting randomly at each page load.
+ Added Category GantryField
# Fixed gantry template detection in template manager when cache is enabled
^ Changed way the redirect happens for gantry template styles under template manager
+ Added URL changing for gantry templates styles under template style manager
# Fix for Joomla 1.6.2 removing mootools from backend by default.

------- 3.2.2 Release [31-Mar-2011] -------
# Fix for imenu.js not working properly
# Fix for Smartloader Feature
# Fix for page suffix not being added to body class
# Fix for error displaying template params when gantry cache is enabled
# Fix for disableing the component on the front page
# Fix for error displaying template params when gantry cache is enabled
# Fix for backwards dashes in parameter names
^ Change to temp var for viewswitcher cookie

------- 3.2.1 Release [18-Mar-2011] -------
+ Added pin on Tips to follow you while scrolling the page
# Fix for presets when Toggles involved
# Fallback support for Hathor admin style


------- 3.2.0 Release [04-Mar-2011] -------
! Changelog Creation