<?php
/**
 * @version		$Id: radio.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Framework
 * @subpackage	Parameter
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * Renders a radio element
 *
 * @package		Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementXiradio extends JElement
{
	/**
	* Element name
	*
	* @access	public
	* @var		string
	*/
	public $_name = 'Radio';

	public function fetchElement($name, $value, &$node, $control_name)
	{
		$options = array ();
		foreach ($node->children() as $option)
		{
			$val	= $option->attributes('value');
			$text	= $option->data();
			$class = ($node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="radio"');
			$options[] = JHtml::_('select.option', $val, $text);
		}
		return '<div class="paramValue">'.JHtml::_('select.radiolist', $options, ''.$control_name.'['.$name.']', '', 'value', 'text', $value, $control_name.$name, true).'</div>';
	}
}
