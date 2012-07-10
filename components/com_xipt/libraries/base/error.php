<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptError extends JError
{
	const ERROR   = 1;
	const WARNING = 2;
	//XITODO : add assertError. assertWarn, assertMessage function
	function assert($condition, $msg = '', $type = self::ERROR)
	{
		if($condition)
			return true;
		if($type == self::ERROR)
			self::raiseError('XIPT-ERROR', $msg);

		self::raiseWarning('XIPT-WARNING', $msg);
	}
}