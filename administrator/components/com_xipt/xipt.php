<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*@TODO : Include all helper files or other files in one common file and include that file
**/

// no direct access
if(!defined('_JEXEC')) die('Restricted access');

// add include files
require_once JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_xipt'.DS.'includes.php';

// check for jom social supported version and show message
if(!XiptHelperJomsocial::isSupportedJS()){
	$msg = "ERROR : The JomSocial Current Version used by you is not supported for ProfileTypes.";
	JFactory::getApplication()->enqueueMessage($msg);	 
}

$controller	= JRequest::getCmd('view', 'cpanel');
$controller	= JString::strtolower( $controller );	
$class	= 'XiptController' . JString::ucfirst( $controller );
	
	// Test if the object really exists in the current context
	if(!class_exists($class, true))
		XiptError::raiseError(__CLASS__.'.'.__LINE__, sprintf(XiptText_("INVALID_CONTROLLER_OBJECT_CLASS_DEFINITION_DOES_NOT_EXISTS_IN_THIS_CONTEXT"),$class));
		
$controller	= new $class();

// Perform the Request task
$task = JRequest::getCmd('task','display');	
		
// Task's are methods of the controller. Perform the Request task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();
