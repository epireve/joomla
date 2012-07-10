<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupHelper
{
	function getOrderedRules()
	{
		$parser		= JFactory::getXMLParser('Simple');
		$xml		= XIPT_FRONT_PATH_LIBRARY_SETUP . DS . 'order.xml';
	
		$parser->loadFile( $xml );
	
		$order	= array();
		$childrens = $parser->document->children();
		foreach($childrens as $child)
			$attr[] = $child->attributes();
			
		return $attr;
	}
}