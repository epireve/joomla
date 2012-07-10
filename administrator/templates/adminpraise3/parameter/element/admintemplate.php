<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class JElementAdminTemplate extends JElement
{

	var	$_name = 'admintemplate';

	function fetchElement($name, $value, &$node, $control_name) {
		include_once(JPATH_ADMINISTRATOR.'/components/com_templates/helpers/template.php');
    
		$template_path = JPATH_ADMINISTRATOR.'/templates';
		$templates = TemplatesHelper::parseXMLTemplateFiles($template_path);
		
		$options = array();
		$options[] = JHTML::_('select.option', '', JText::_('DEFAULT'));
		foreach($templates as $t) :
			$options[] = JHTML::_('select.option', $t->directory, $t->name);
		endforeach;

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]', 'class="inputbox"', 'value', 'text', $value );
		
	}
	
}
