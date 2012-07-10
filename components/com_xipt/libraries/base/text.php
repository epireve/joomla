<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptText
{
//	public function __call($name, $arguments){
//		echo "calling XiptText $name";
//        return forward_static_call_array(array('JText',$name), $arguments);
//    }
//
//    /**  As of PHP 5.3.0  */
//    public static function __callStatic($name, $arguments) {
//    	echo "calling static XiptText $name";
//        return forward_static_call_array(array('JText',$name), $arguments);
//    }
	
	function sprintf($string)
	{
		$args = func_get_args();
		return call_user_func_array(array('JText','sprintf'), $args);
	}
    
	static function _($string, $jsSafe = false)
	{
    	$string='COM_XIPT_'.$string;
        return JText::_($string, $jsSafe);
    }
}