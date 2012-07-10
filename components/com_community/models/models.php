<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Put all shared data access here
 */ 
class JCCModel extends JModel {

	public function JCCModel(){
	parent::__construct();
	
	}
	
	public function &getNotes(){
		return array("test", "whatever");
	}
	
	public function &getSample()
	{
		$s = array("test", "whatever");
		return $s;
	}
	
	public function store()
	{
		// check if some core interface is implemented and execute them
		// use simple method check for now
		// @todo: use PHP5 reflection api.
		
			
		return parent::store();
	}
	
}

interface CGeolocationInterface
{
    public function resolveLocation($address);
}

interface CGeolocationSearchInterface
{
	public function searchWithin($address, $distance);
}

interface CLimitsInterface
{
	public function getTotalToday( $userId );
}

interface CNotificationsInterface
{
	public function getTotalNotifications( $userId );
}