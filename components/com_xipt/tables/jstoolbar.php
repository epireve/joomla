<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Disallow direct access to this file
if(!defined('_JEXEC')) die('Restricted access');

class XiptTableJSToolbar extends XiptTable
{
	function load($id)
	{
		if( $id ){
			return parent::load( $id );
		}
		
		$this->id			= 0;
		$this->menuid		= '';
		$this->profiletype	= '';
		return true;
	}
	
	function __construct()
	{
		parent::__construct('#__xipt_jstoolbar','id');
	}
	
}
