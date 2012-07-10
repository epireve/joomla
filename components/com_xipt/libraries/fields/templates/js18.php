<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/


// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptFieldsTemplatesJs18 extends XiptFieldsTemplatesBase
{
	function getFieldData( $value = null)
	{
		$tName = $value; 
	
		if($tName == null){
			$userid = JRequest::getVar('userid',0);
			$tName = $this->getTemplateValue($value,$userid);
		}
		
		return $tName;
	}
} 
