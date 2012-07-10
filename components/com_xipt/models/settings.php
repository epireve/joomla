<?php

/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// Disallow direct access to this file
if(!defined('_JEXEC')) die('Restricted access');

class XiptModelSettings extends XiptModel
{	
	function getParams($refresh = false)
	{
		//for testing purpose clean cache
		$refresh = XiptLibJomsocial::cleanStaticCache();
		if(isset($this->_params) && $refresh === false)
			return $this->_params;

		return $this->_params = $this->loadParams('settings', 'params');
	}
}