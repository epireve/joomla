<?php
/*
------------------------------------------------------------------------
* copyright	Copyright (C) 2010 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* Author : Team JoomlaXi @ Ready Bytes Software Labs Pvt. Ltd.
* Email  : shyam@joomlaxi.com
* License : GNU-GPL V2
* Websites: www.joomlaxi.com
*/

// no direct access
if(!defined('_JEXEC')) die('Restricted access');
class JElementUploadfile extends JElement
{
   public $_name = 'Upload';
   public function fetchElement($name, $value, &$node, $control_name)
   {
      $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
      return '<input type="file" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" />';
   }

}
?>