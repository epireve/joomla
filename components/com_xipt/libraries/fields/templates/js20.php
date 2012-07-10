<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptFieldsTemplatesJs20 extends XiptFieldsTemplatesBase
{	
	function getFieldData( $field = array())
	{
		$tName = null; 
		if(!empty($field) && isset($field['value']))
            $tName = $field['value'];		
		
		if($tName == null){
			$userid = JRequest::getVar('userid',0);
			$tName = $this->getTemplateValue($tName,$userid);
		}
		
		return $tName;
	}
} 
