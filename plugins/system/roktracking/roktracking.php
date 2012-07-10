<?php
/**
 * @package RokTracking System Plugin - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

jimport('joomla.plugin.plugin');

class plgSystemRokTracking extends JPlugin
{

	var $trackusers;
	var $trackadmins;
	var $userpurgedays;
	var $adminpurgedays;

	function plgSystemRokTracking(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->trackusers = $this->params->get('trackusers', 1);
		$this->trackadmins = $this->params->get('trackadmins', 1);
		$this->userpurgedays = intval($this->params->get('userpurgedays', 14));
		$this->adminpurgedays = intval($this->params->get('adminpurgedays', 14));
	}


	function onAfterRoute()
	{
		$mainframe = JFactory::getApplication();

		// is user in admin area?
		if($mainframe->isAdmin()) {
			// in admin area
			plgSystemRokTracking::_trackAdmin();

		} else {
			// in user area
			plgSystemRokTracking::_trackUser();
		}
		
		
	}

	function _purgeData() {
		
		$db = JFactory::getDBO();
		$query = 'delete from #__rokuserstats where timestamp <= date_sub(current_timestamp,interval '.$this->userpurgedays.' day)';
		$db->setQuery($query);
		$db->query();

		$query = 'delete from #__rokadminaudit where timestamp <= date_sub(current_timestamp,interval '.$this->adminpurgedays.' day)';
		$db->setQuery($query);
		$db->query();
	
	}
	
	function _trackUser() {

		if ($this->trackusers) {
			$user = &JFactory::getUser();
			$session =& JFactory::getSession();
			
			$ipaddress = $_SERVER['REMOTE_ADDR'];
			if (!$ipaddress) $ipaddress = "Unknown";
			$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$uri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
			
			$data = new stdClass();
			$data->user_id = $user->id;
			$data->ip = $ipaddress;
			$data->session_id = $session->getId();
			$data->page = $uri;
			$data->referrer = $referrer;
			
			$db = JFactory::getDBO();
			$db->insertObject( '#__rokuserstats', $data, 'id' );
		}	
		
	
	}
	
	function _trackAdmin() {

		$option = JRequest::getCmd('option');

		// in com_cpanel - flush data
		if ($option == '') {
			$this->_purgeData();
		}

		
		if ($this->trackadmins) {
	
			$task = JRequest::getString('task','');
			$view = JRequest::getString('view','');
			$layout = JRequest::getString('layout','');
			$form = JRequest::getVar('jform','');
			$user = &JFactory::getUser();
			$session =& JFactory::getSession();
			
			$name = isset($form['name']) ? $form['name'] : '';
			$title = isset($form['title']) ? $form['title'] : false; 
			
			if (!$title) $title = $name;

	        if (strpos($task,".")) {
	            $task = substr($task,strpos($task,".")+1);
	        }

			if ($task == '') $task = $layout;
			
			
			$ipaddress = $_SERVER['REMOTE_ADDR'];
			if (!$ipaddress) $ipaddress = "Unknown";
			if ($option == '') $option = 'com_cpanel';
			$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$uri = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
	//		
			$cid = JRequest::getVar('id');
			
			if (is_array($cid)) $cid = $cid[0];
			
			$data = new stdClass();
			$data->user_id = $user->id;
			$data->ip = $ipaddress;
			$data->session_id = $session->getId();
			$data->option = $option;
			$data->task = $task;
			$data->page = $uri;
			$data->referrer = $referrer;
			$data->title = $title;
			$data->cid = $cid;
			
			//var_dump ($data);exit;
			
			
			$db = JFactory::getDBO();
			$db->insertObject( '#__rokadminaudit', $data, 'id' );
		}
		
	}
	


	
}
