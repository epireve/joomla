<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementXiptmodule extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'xiptModule';

	function fetchElement($name, $value, &$node, $control_name)
	{	
		$modulesHtml = $this->getModulesHtml($name, $value, $control_name);

		return $modulesHtml;
	}
	
	function getAllModules($published = '')
	{
		$query = new XiptQuery();
		$query->select('*');
		$query->from('#__modules');
		$query->where("`published` = $published");
		
		$query->order('ordering');	
		$modules =$query->dbLoadQuery("","")->loadObjectList();			 	    	
		
		return $modules;	
	}
	
	function getModulesHtml($name, $value, $control_name)
	{
		$modules = self::getAllModules(1);
		$html   = '';
		
		$html  .= '<select id="'.$control_name.'['.$name.']" name="'.$control_name.'['.$name.']">';
				
		foreach($modules as $m) {			
			$title		= $m->title;
			$id			= $m->id;
		    
		    $selected	= ( JString::trim($id) == $value ) ? ' selected="true"' : '';
			$html	.= '<option value="' . $id . '"' . $selected . '>' . $title . '</option>';
		}
		
		$html	.= '</select>';	
		$html   .= '<span id="errprofiletypemsg" style="display: none;">&nbsp;</span>';
		
		return $html;
	}
}