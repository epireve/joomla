<?php
/**
 * @author Daniel Dimitrov http://compojoom.com
 * @copyright	Copyright (C) 2009 JoomlaPraise. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('Restricted Access');


class JElementRedirect extends JElement
{
	/**
	* Element type
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Redirect';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$mainframe = JFactory::getApplication();
		if($mainframe->scope != 'com_adminpraise') {
			$mainframe->redirect('index.php?option=com_adminpraise&view=settings');
		}
	}
}
