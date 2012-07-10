<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class JButtonKoowa extends JButton {

	function fetchButton($type = 'koowa', $name = 'custom', $ref = '#', $onclick=null, $data=null, $classes = null) {
		$this->_name = $name;

		$text  = JText::_(ucfirst($name));

        if ($classes) $classes = ' class="'.$classes.'"';
        if ($onclick) $onclick = ' onclick="'.$onclick.'"';
        if ($data) $data = ' data="'.$data.'"';

		$html  = "<a href=\"$ref\"".$onclick.$classes.$data.">\n";
 		$html .= "$text\n";
		$html .= "</a>\n";

		return $html;
	}

	function fetchId($type, $name) {
		return 'toolbar-'.$name;
	}
	
	function render(&$definition){
		/*
		 * Initialize some variables
		 */
		$html	= null;
		$id		= call_user_func_array(array(&$this, 'fetchId'), $definition);
		$action	= call_user_func_array(array(&$this, 'fetchButton'), $definition);

		// Build id attribute
		if ($id) {
			$id = "id=\"$id\"";
		}
		
		$classes = isset($definition[3]) ? ' '.$definition[3] : '';

		// Build the HTML Button
		$html	.= "<td class=\"button$classes\" $id>\n";
		$html	.= $action;
		$html	.= "</td>\n";

		return $html;
	}

}
