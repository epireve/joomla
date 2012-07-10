<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

require_once JPATH_SITE.DS.'components'.DS.'com_xipt'.DS.'includes.php';

$controller	= JRequest::getCmd('view', 'registration');
$controller	= JString::strtolower( $controller );	
$class	= 'XiptController' . JString::ucfirst( $controller );
	
// Test if the object really exists in the current context
if(!class_exists($class, true))
	XiptError::raiseError(__CLASS__.'.'.__LINE__, sprintf(XiptText::_("INVALID_CONTROLLER_OBJECT_CLASS_DEFINITION_DOES_NOT_EXISTS_IN_THIS_CONTEXT"),$class));
		
$controller	= new $class();
	
// Perform the Request task
$task = JRequest::getCmd('task','display');		
	
// Task's are methods of the controller. Perform the Request task
$controller->execute( $task );
	
// Redirect if set by the controller
$controller->redirect();
