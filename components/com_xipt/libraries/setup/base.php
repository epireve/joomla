<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');


abstract class XiptSetupBase
{
	function isRequired()
	{
		return true;
	}
	
	function doApply()
	{
		return true;
	}
	
	function isApplicable()
	{
		return true;
	}
	
	function doRevert()
	{
		return true;
	}
	
	function getHelpMsg($ruleName)
	{
		// XITODO : handle errors 
		$msgFile = dirname(__FILE__).DS.'rule'.DS.$ruleName.'.html';
		ob_start();
		include_once($msgFile);
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
}