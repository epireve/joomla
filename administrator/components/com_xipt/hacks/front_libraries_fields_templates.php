<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// no direct access
if(!defined('_JEXEC')) die('Restricted access');
require_once (JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php');
 
class CFieldsTemplates
{
	var $_field;
	
	function __construct()
	{
		$this->_field = XiptFieldsTemplatesBase::getInstance();		
	}

	//TODO : add FormatData and Validate
	
	function getFieldData( $value )
	{
		return $this->_field->getFieldData($value);		
	}
	
	function getFieldHTML($field, $required )
	{
		return $this->_field->getFieldHTML($field, $required);
	}
}
