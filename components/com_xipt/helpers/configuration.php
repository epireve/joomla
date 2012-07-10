<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptHelperConfiguration 
{
	function getResetLinkArray()
	{
		$resetArray = array();
		$allPTypes  = XiptLibProfiletypes::getProfiletypeArray();
		if(!empty($allPTypes)) {
			foreach($allPTypes as $ptype) {
				if($ptype->params)
					$resetArray[$ptype->id] = true;
				else
					$resetArray[$ptype->id] = false;
			}
		}
		
		return $resetArray;
	}
}
