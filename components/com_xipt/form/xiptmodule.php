<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JFormFieldXiptmodule extends JFormField
{
	public $type = 'xiptModule'; 
	
	function getInput()
	{
		// get array of all visible profile types (std-class)
		$moduleArray = self::getAllModules(1);
		
		return JHTML::_('select.genericlist',  $moduleArray, $this->name, null, 'id', 'title', $this->value);
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
}