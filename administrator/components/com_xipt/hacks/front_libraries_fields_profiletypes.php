<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
if(!defined('_JEXEC')) die('Restricted access');
require_once (JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php');
 
class CFieldsProfiletypes
{
	var $_field;
	
	function __construct()
	{
		$this->_field = XiptFieldsProfiletypesBase::getInstance();
	}
	
	/* if data not available,
	 * then find user's profiletype and return
	 * else present defaultProfiletype to community
	 *
	 * So there will be always a valid value returned
	 * */
	function formatData($value=0)
	{
	   return $this->_field->formatData($value);
	}
	/*
	 * Convert stored profileType ID to profileTypeName
	 *
	 * */
	function getFieldData($value = 0)
	{
		return $this->_field->getFieldData($value);
	}
	
	/*
	 * Generate input HTML for field
	 */
	function getFieldHTML($field ,$required )
	{
		return $this->_field->getFieldHTML($field ,$required );
	}
	
	// Just an validation
	function isValid($value,$required)
	{
		return $this->_field->isValid($value,$required);	    
	}
	
}
