<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * Allow any part of the system to add user reporting feature.  
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Set the tables path
JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_community' . DS . 'tables' );

class CReporting
{
	var $sendEmail = true;
	var $actions = null;

	// Stores the report object when a report object is being created
	var $report		= null;
	
	/**
	 * Method to use to create a new report item. Before adding / registering 
	 * a report action, a report needs to be created first.
	 * 
	 * @param	$title	Title that is shown to back end administrator
	 * @param	$link	Link of the reported item
	 * @param	$message	Reported message
	 **/	 	
	public function createReport( $title , $link , $message )
	{
		// Create report item
		$report		=& JTable::getInstance( 'reports' , 'CommunityTable' );
		
		// Get the unique string for this report.
		$arg2				= JRequest::getVar( 'arg2' );
		$arg3				= JRequest::getVar( 'arg3' );
		$arg5				= JRequest::getVar( 'arg5' );
		$uniqueString		= md5( $arg2 . $arg3 . $arg5 );
		
		// Test if the report already exists as we do not want to
		// keep re-creating new reports
		$id					= $report->getId( $uniqueString );
		
		if( !$id )
		{
			// Get the unique string
			$report->uniquestring	= $uniqueString;
	
			// Set the status of this report which is by default 0
			$report->status		= '0';
			
			// Set the creation date
			$report->created	= gmdate( 'Y-m-d H:i:s' );
	
			// Report link item
			$report->link		= $link;
			
			// Store the report object
			if( !$report->store() )
			{
				// Error while trying to save the report object.
				return false;
			}
		}
		else
		{
			$report->load( $id );
		}
		
		// Set / Update reports title.
		//$report->title	= $title;
		$report->store();
		
		// Get reporter user id.
		$my					= CFactory::getUser();
		$ip					= JRequest::getVar( 'REMOTE_ADDR' , '' , 'SERVER' );
		
		// Add a new reporter item
		if( !$report->addReporter( $report->id , $my->id , $message , gmdate( 'Y-m-d H:i:s' ) , $ip ) )
		{
			// Error while trying to add a new reporter.
			return false;
		}

		// Set the report id.
		$this->report		= $report;
		
		return true;
	}
	
	/**
	 * Caller can specify what needed to be done by admin
	 * 
	 * @param $label 	The text label that is displayed
	 * @param $actionData	The function name that needs to be executed.
	 * @param $args	The arguments that needs to be parsed to the function
	 * @param $default	Sets whether the current action is the default action when X number of reports is reached.
	 * @return boolean True on success.
	 */	 	
	public function addAction( $label , $actionData , $args , $default = false )
	{
		$argsData	= '';

		// Get the reports count
		$count		= $this->report->getReportersCount();
		
		$config		= CFactory::getConfig();
		
		// Automatically execute the default method if exists
		if( $count >= $config->get( 'maxReport' ) && $config->get( 'maxReport' )!=0 )
		{
			// Execute this report method automatically since it has reached the threshold.
			// If no default method specified, we proceed with the normal reports.
			if( $this->executeDefaultAction( $args , $actionData ) )
			{
				return true;
			}
		}
		
		// Reformat the arguments.
		if( is_array( $args ) )
		{
			$argsCount	= count( $args );
			for($i = 0; $i < $argsCount; $i++ )
			{
				$argsData	.= $args[ $i ];
				$argsData	.= ( $i != ( $argsCount - 1 ) ) ? ',' : '';
			}
		}
		else
		{
			$argsData	= $args;
		}

		// Add action for the current report item
		$default	= ( $default ) ? 1 : 0;
		$this->report->addAction( $label , $actionData , $args , $default );
	}

	public function addActions( $actions )
	{
		$argsData	= '';

		// Get the reports count
		$count		= $this->report->getReportersCount();

		$config		= CFactory::getConfig();
		
		$this->report->addActions( $actions );
		// Automatically execute the default method if exists
		if( $count >= $config->get( 'maxReport' ) && $config->get( 'maxReport' )!=0 )
		{
			// Execute this report method automatically since it has reached the threshold.
			// If no default method specified, we proceed with the normal reports.
			if( $this->executeDefaultAction() )
			{
				return true;
			}
		}


	}
	
	/**
	 * Get the html code to be added to the page
	 * 
	 * @param	$reportFunc	String	The report function that needs to be called
	 * @param	$args	Array	An array of parameter values
	 * 
	 * return	$html	String
	 */	 	
	public function getReportingHTML( $reportText = '' , $reportFunc , $args )
	{
		$config		= CFactory::getConfig();
		$my			= CFactory::getUser();

		$reportText	= (!empty( $reportText ) ) ? $reportText : JText::_('COM_COMMUNITY_REPORT_THIS');
		
		if( !$config->get('enablereporting') || ( ( $my->id == 0 ) && ( !$config->get('enableguestreporting') ) ) )
		{
			return '';
		}
		
		$argsData	= '';
		$argsCount	= count( $args );
		for( $i = 0; $i < $argsCount; $i++ )
		{
			$argsData	.= "\'" . $args[ $i ] . "\'";
			$argsData	.= ( $i != ( $argsCount - 1) ) ? ',' : '';
		}
		
		$tmpl	= new CTemplate();
		return $tmpl->set( 'reportText' , $reportText )
					->set( 'reportFunc' , $reportFunc )
					->set( 'argsData' , $argsData )
					->fetch( 'reports.html' );
	}
	
	/**
	 * Executes a default action
	 */	 	
	public function executeDefaultAction()
	{
		$db 	=& JFactory::getDBO();

		// Send notification to specified emails notifying them the action has been taken.
		$jConfig	=& JFactory::getConfig();
		$config		= CFactory::getConfig();
		
		$from		= $jConfig->getValue( 'mailfrom' );
		$fromName	= $jConfig->getValue( 'fromname' );				
		$recipients	= $config->get( 'notifyMaxReport' );
		$recipients	= explode( ',' , $recipients );
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_reports_actions' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'reportid' ) . '=' . $db->Quote( $this->report->id ) . ' '
				. 'AND ' . $db->nameQuote( 'defaultaction' ) . '=' . $db->Quote( 1 ) . ' '
				. 'ORDER BY ' . $db->nameQuote('id')
				. ' LIMIT 1';
		
		$db->setQuery( $query );
		$result	= $db->loadObject();
		
		// No defaultaction specified for this current
		if( !$result )
			return false;

		
		// Execute the default action
		$method		= explode( ',' , $result->method );
		$args		= explode( ',' , $result->parameters );

		if( is_array( $method ) && $method[0] != 'plugins' )
		{	
			$controller	= JString::strtolower( $method[0] );
			
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . 'controller.php' );
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . $controller . '.php' );
			
			$controller	= JString::ucfirst( $controller );
			$controller	= 'Community' . $controller . 'Controller';

			$controller	= new $controller();
			
			if( method_exists( $controller , $method[1] ) )
			{		
				$resultData		= call_user_func_array( array( &$controller , $method[1] ) , $args ); 
			}
			else
			{
				// Continue adding the action as there might be changes to the method name.
				return false;
			}
		}
		else if( is_array( $method ) && $method[0] == 'plugins' )
		{
			// Application method calls
			$element	= JString::strtolower( $method[1] );
			
			require_once( CPluginHelper::getPluginPath('community',$element) . DS . $element . '.php' );
			
			$className	= 'plgCommunity' . JString::ucfirst( $element );
			
			$plugin		= new $className();
			
			if( method_exists( $plugin , $method[2] ) )
			{
				$resultData		= call_user_func_array( array( $plugin , $method[2] ) , $args );
			}
			else
			{
				// Continue adding the action as there might be changes to the method name.
				return false;
			}
		}
		else
		{
			return false;
		}

		// Send notification to specified emails notifying them the action has been taken.
		$jConfig	=& JFactory::getConfig();
		$config		= CFactory::getConfig();
		
		$from		= $jConfig->getValue( 'mailfrom' );
		$fromName	= $jConfig->getValue( 'fromname' );				
		$recipients	= $config->get( 'notifyMaxReport' );
		$recipients	= explode( ',' , $recipients );

		$subject	= JText::sprintf('COM_COMMUNITY_REPORT_THRESHOLD_REACHED_SUBJECT' , $this->report->link );
		
		CFactory::load( 'libraries' , 'notification' );
		
		$params			= new CParameter( '' );
		$params->set( 'url' , $this->report->link );

		CNotificationLibrary::add( 'etype_system_reports_threshold' , $from , $recipients , $subject , '' , 'reports.threshold' , $params );

		$this->report->status	= 1;
		$this->report->store();
		
		return true;
	}
}
/**
 * Maintain classname compatibility with JomSocial 1.6 below
 */ 
class CReportingLibrary extends CReporting
{}