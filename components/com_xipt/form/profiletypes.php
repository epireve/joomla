<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.form.formfield');
//JFormHelper::loadFieldClass('Profiletypes');

class JFormFieldProfiletypes extends JFormField
{
	public $type = 'Profiletypes';
		
	function getInput(){

		$none = new stdClass();
		$none->id = -1;
		$none->name=XiptText::_('none');
		// get array of all visible profile types (std-class)
		$pTypeArray = XiptLibProfiletypes::getProfiletypeArray(array('published'=>1, 'visible'=>1));
		
		//add multiselect option
		$attr = $this->multiple ? ' multiple="multiple"' : '';
		
		if($attr == null){
			// add none option in profile-type array
			array_unshift($pTypeArray,$none);
		}
		
		return JHTML::_('select.genericlist',  $pTypeArray, $this->name, $attr, 'id', 'name', $this->value);
	}
}