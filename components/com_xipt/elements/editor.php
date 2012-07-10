<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementEditor extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Editor';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$value = base64_decode($value);
		//$value = html_entity_decode($value);
		$editor = JFactory::getEditor();
		ob_start();
		echo $editor->display( $control_name.'['.$name.']',  htmlspecialchars($value, ENT_QUOTES),
								'350', '200', '60', '20', array('pagebreak', 'readmore') ) ;
		$html = ob_get_contents();
		
		ob_clean();
		return $html;
	}
}