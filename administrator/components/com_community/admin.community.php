<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

// @todo: Do some check if user is really allowed to access this section of the back end.
// Just in case we need to impose ACL on the component

// During ajax calls, the following constant might not be called
if( !defined('JPATH_COMPONENT') )
{
	define( 'JPATH_COMPONENT' , dirname( __FILE__ ) );
}

// Load necessary language file since we dont store it in the language folder
$lang =& JFactory::getLanguage();
$lang->load( 'com_community', JPATH_ROOT . DS . 'administrator' );

//check php version
$installedPhpVersion	= floatval(phpversion());
$supportedPhpVersion	= 5;

$install 				= JRequest::getVar('install', '', 'REQUEST');
$view 	 				= JRequest::getVar('view', '', 'GET');
$task	 				= JRequest::getVar('task' , '' , 'REQUEST');

if($task == 'reinstall')
{
	jimport( 'joomla.filesystem.file' );
	$destination = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS;
	$buffer = "installing";	
	JFile::write($destination.'installer.dummy.ini', $buffer);
}

//install
if(((file_exists(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'installer.dummy.ini') || $install) && $view!='maintenance' && $task != 'azrul_ajax') || ($installedPhpVersion < $supportedPhpVersion))
{
	require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'installer.helper.php');
	require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'installer.template.php');
	
	$step 		= JRequest::getVar('step', '', 'post');
	$helper 	= new communityInstallerHelper;
	$display 	= new communityInstallerDisplay();
	$template	= new communityInstallerTemplate();
	
	if($installedPhpVersion < $supportedPhpVersion)
	{
		
		$html 		= communityInstallerHelper::getErrorMessage(101, $installedPhpVersion);
		$status		= false;
		$nextstep 	= 0;
		$title 		= JText::_('COM_COMMUNITY_INSTALLATION_JOMSOCIAL');
		$install 	= 1;
		$substep	= 0;
	}
	else
	{
		if(!empty($step))
		{
			$progress 	= $helper->install($step);
			$html 		= $progress->message;
			$status		= $progress->status;
			$nextstep 	= $progress->step;
			$title 		= $progress->title;
			$install 	= $progress->install;
			$substep	= isset($progress->substep) ? $progress->substep : 0 ;
		}
		else
		{
			$nextstep = 1;
			$verifier = new communityInstallerVerifier();
			$imageTest = $verifier->testImage();
			
			$template = new communityInstallerTemplate();
			$html	= $template->getHTML('welcome', $imageTest);
			
			$status 	= true;			
			$title 		= 'JomSocial Installer';
			$install 	= 1;
			$substep	= 0;
		}
	}
		
	//$display->cInstallDraw($html, $nextstep, $title, $status, $install, $substep);
	$template->cInstallDraw($html, $nextstep, $title, $status, $install, $substep);
	
	return;	
}

if(file_exists(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_jsupdater' . DS . 'jsupdater.dummy.ini'))
{
	$mainframe	=& JFactory::getApplication();
	$mainframe->redirect( 'index.php?option=com_jsupdater' );
}

// Load JomSocial core file
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

// Load any helpers
require_once( JPATH_COMPONENT . DS . 'helpers' . DS . 'community.php' );

// Load any defined properties
require_once( JPATH_COMPONENT . DS . 'defines.php' );

// Require the base controller
require_once( JPATH_COMPONENT . DS . 'controllers' . DS . 'controller.php' );

// Set the tables path
JTable::addIncludePath( JPATH_COMPONENT . DS . 'tables' );

// Load the template library
CFactory::load('libraries', 'template');

// Get the task
$task	= JRequest::getCmd( 'task' , 'display' );

// Load the required libraries
if( !defined( 'JAX_SITE_ROOT' ) )
{
	//require_once( JPATH_PLUGINS . DS . 'system' . DS . 'pc_includes' . DS . 'ajax.php' );
	require_once(AZRUL_SYSTEM_PATH . DS . 'pc_includes' .DS. 'ajax.php');
}

// Let's test if the task is azrul_ajax , we skip the controller part at all.
if( isset( $task ) && ( $task == 'azrul_ajax' ) )
{
	require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'ajax.community.php' );
}
else
{

	// Load AJAX library for the back end.
	//$jax	= new JAX( rtrim( JURI::root() , '/') . '/plugins/system/pc_includes' );
	$jax		= new JAX( AZRUL_SYSTEM_LIVE . '/pc_includes' );
	$jax->setReqURI( rtrim( JURI::root() , '/' ). '/administrator/index.php' );
	
	// @rule: We do not want to add these into tmpl=component or no_html=1 in the request.
	if( JRequest::getVar('no_html' , '' ) != 1 && JRequest::getVar( 'tmpl' , '' ) != 'component' )
	{
		// Override previously declared jax_live_site stuffs
		if( !$jax->process() )
		{
			echo $jax->getScript();
		}
	}

	// We treat the view as the controller. Load other controller if there is any.
	$controller	= JRequest::getWord( 'view' , 'community' );

	if( !empty( $controller ) )
	{
		$controller	= JString::strtolower( $controller );
		$path		= COMMUNITY_CONTROLLERS . DS . $controller . '.php';
	
		// Test if the controller really exists
		if( file_exists( $path ) )
		{
			require_once( $path );
		}
		else
		{
			JError::raiseError( 500 , JText::_('COM_COMMUNITY_CONTROLLER_NOT_EXISTS') );
		}
	}
	
	$class	= 'CommunityController' . JString::ucfirst( $controller );
	
	//check if zend plugin is enalble.
	$zend = JPluginHelper::getPlugin('system', 'zend');	
	if(empty($zend))
	{
		$message 		= JText::_('COM_COMMUNITY_ZEND_PLUGIN_DISABLED');
		$instruction 	= JText::_('COM_COMMUNITY_ZEND_PLUGIN_DISABLED_INSTRUCTION');
		$mainframe 		= JFactory::getApplication();
		$mainframe->enqueueMessage($message, 'error');
	}
	
	
	// Test if the object really exists in the current context
	if( class_exists( $class ) )
	{
		$controller	= new $class();
	}
	else
	{
		// Throw some errors if the system is unable to locate the object's existance
		JError::raiseError( 500 , 'Invalid Controller Object. Class definition does not exists in this context.' );
	}
	
	// Task's are methods of the controller. Perform the Request task
	$controller->execute( $task );
	
	// Redirect if set by the controller
	$controller->redirect();
}


