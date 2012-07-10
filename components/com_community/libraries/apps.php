<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS  . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

CFactory::load( 'events' , 'router' );
CFactory::load( 'libraries' , 'formelement' );

class CAppPlugins extends JObject
{
	var $name;
	var $triggerCount = 0;

	public function __construct($name){
		$this->name = $name;
		parent::__construct($name);
	}
	
	private function CAppPlugins($name){
		$this->__construct($name);
	}
	
	public function &getInstance(){
		static $instance;
		if(!$instance){
			$instance = new CAppPlugins('CAppPlugins');
		}
		return $instance;
	}
	
	/**
	 * Used to include specific applications that are enabled on specific users.
	 * So that the page will be optimized because files will not be included.	 
	 **/	 	
	public function loadApplications()
	{
		static $loaded = false;
		// Although JPluginHelper will load it only once, we need to track it to
		// enable a trigger to our plugins
		if( !$loaded ){
			JPluginHelper::importPlugin('community');
			$this->triggerEvent('onAfterAppsLoad', null);
		} 
		return true;
	}
	
	
	public function _getPlgFromDispatcher($appName){
		$dispatcher = & CDispatcher::getInstanceStatic();
		$observers =& $dispatcher->getObservers();
		foreach( $observers as $key => $val) {
			$obj =& $observers[$key];
			if( is_object($obj) ) {
				//wrapper object to access protected properties
				$plgObjWrapper = new CPluginWrapper($obj);
				if( (JString::strtolower($plgObjWrapper->getPluginName()) == JString::strtolower($appName))
					&& $plgObjWrapper->getPluginType() == 'community' ) {
					
					return $obj;
				}
			}
		}
		
		return null;
	}
	
	/**
	 * @note to dev, internal use only. Subject to change in future revison	 
	 */	 	
	public function renderPlugin($plgObj, $userid)
	{
		//wrapper for JPlugin to access protected properties
		$plgObjWrapper = new CPluginWrapper($plgObj);
		$arrayParams = null;
		
		$appsModel 	= CFactory::getModel('apps');
		$user = CFactory::getUser($userid);
		
		$app	= JTable::getInstance( 'App' , 'CTable' );
		$app->loadUserApp($userid, $plgObjWrapper->getPluginName());

		$core			= $plgObj->params->get( 'coreapp' );
		
		// Format the applications with proper heading
		$obj			= new stdclass();
		$obj->title		= JText::_( $appsModel->getAppTitle( $plgObjWrapper->getPluginName() ) );
		$obj->name		= $plgObjWrapper->getPluginName();
		$obj->data		= $plgObj->onProfileDisplay( $arrayParams );
		$obj->core		= false;
		
		if( $core )
		{
			$obj->position	= $plgObj->params->get( 'position' , 'content' );
		}
		else
		{
			$obj->position	= !empty($app->position) ? $app->position: $plgObj->params->get('position', 'content') ;
		}
		
		$obj->id		= !empty($app->id) ? $app->id: 0 ;
		$obj->hasConfig	= $appsModel->hasConfig(  $plgObjWrapper->getPluginName() );
		
		return $obj;
	}
	
	/**
	 * Trigger a single plugin, support only onProfileDisplay for now
	 * @note to dev, internal use only. Subject to change in future revison	 
	 */	 	
	public function triggerPlugin($triggerName, $pluginName, $userid)
	{
		$dispatcher = & CDispatcher::getInstanceStatic();
		$observers =& $dispatcher->getObservers();
		// Load Core applications		
		for( $i = 0; $i < count($observers); $i++ )
		{
			$plgObj = $observers[$i];
			if( is_object($plgObj) )
			{
				//wrapper object to access protected properties
				$plgObjWrapper = new CPluginWrapper($plgObj);
				
				if( $plgObjWrapper->getPluginType() == 'community'
				    && ($plgObj->params != null) 
					&& method_exists($plgObj, $triggerName )
					&& ($plgObjWrapper->getPluginName() == $pluginName ))
				{
					$obj = $this->renderPlugin($plgObj, $userid);
					return $obj;
				}
			}
		}
		
	}
	
	/**
	 * Used to trigger profile display event
	 * @param	string	eventName (will always be 'onProfileDisplay')
	 * @param	array	params to pass to the function
	 * 
	 * returns	array of content string 	 	 	 	 
	 **/
	public function _triggerOnProfileDisplay($event='onProfileDisplay' )
	{
		$mainframe  =& JFactory::getApplication();
		$dispatcher = & CDispatcher::getInstanceStatic();
		$observers =& $dispatcher->getObservers();
		$content = '';
		
		$arrayParams = null;
		
		// @todo: Fix ordering so we only load according to user ordering or site wide plugin ordering.
		// as we cannot use the mainframe to trigger because all the data are already outputed, no way to manipulate it.
		if($event == 'onProfileDisplay')
		{
			$appsModel = CFactory::getModel('apps');
			
			// Get the current viewed user
			$userid			= JRequest::getInt( 'userid' , '' );
			$user			= CFactory::getUser($userid);
			$applications	= $appsModel->getUserApps( $user->id );
			
			// Just in case the apps are returning data to the view, we need to return them
			$content		= array();
			
			// Load Core applications		
			for( $i = 0; $i < count($observers); $i++ )
			{
				$plgObj = $observers[$i];
				if( is_object($plgObj) )
				{
					$plgObjWrapper = new CPluginWrapper($plgObj);
					if( $plgObjWrapper->getPluginType() == 'community'
					    && ($plgObj->params != null) 
						&& ($plgObj->params->get('coreapp', 0) == 1) 
						&& method_exists($plgObj, 'onProfileDisplay' ))
					{
						
						// Need to skip 'walls' plugin for 2.2 upwards
						if($plgObjWrapper->getPluginName() != 'walls')
						{
							$obj = $this->renderPlugin($plgObj, $userid);
							$obj->core = true;
							$content[] = $obj;
						}
					}
				}
			}				
			
			// Load user application
			$appCount = count($applications);
			for( $i = 0; $i < $appCount; $i++)
			{
				$application =& $applications[$i]; 
				$plgObj 	 = $this->_getPlgFromDispatcher($application->apps);
				$plgObjWrapper = new CPluginWrapper($plgObj);
				// Don't load core apps
				if( $plgObj != null && $plgObj->params->get('coreapp' , 0) != 1 )
				{
					// Don't load core applications
					if( method_exists($plgObj, 'onProfileDisplay' ) )
					{
						// Need to skip 'walls' plugin for 2.2 upwards
						if($plgObjWrapper->getPluginName() != 'walls')
						{
							$content[] = $this->renderPlugin($plgObj, $userid);
						}
					}
				}
			}
		}
		
		return $content;
	}

	/**
	 * Used to trigger onFormDisplay event trigger.
	 * @param	string	eventName (will always be 'onFormDisplay')
	 * @param	array	params to pass to the function
	 * 
	 * returns	Array	result	An array of CFormElement objects.
	 **/
	public function _triggerOnFormDisplay( $event , $arrayParams )
	{
		$dispatcher	=& CDispatcher::getInstanceStatic();
		$observers =& $dispatcher->getObservers();
		$systemObj	= new CEventTrigger();
		$result		= call_user_func_array(array(&$systemObj, $event), $arrayParams);
		$result		= is_null( $result ) ? array() : $result;
		
		for( $i = 0; $i < count($observers); $i++ )
		{
			$plgObj = $observers[$i];
			if( is_object($plgObj) )
			{
				$plgObjWrapper = new CPluginWrapper($plgObj);
				if( $plgObjWrapper->getPluginType() == 'community' && method_exists($plgObj, $event ))
				{
					$content = call_user_func_array(array(&$plgObj, $event), $arrayParams);
					
					// Merge it to flatten the layers.
					if(is_array($content)){
						$result	= array_merge( $result , $content );
					}
				}
			}
		}

		return $result;
	}
	
	/**
	 * Used to trigger applications
	 * @param	string	eventName
	 * @param	array	params to pass to the function
	 * @param	bool	do we need to use custom user ordering ?
	 * 
	 * returns	Array	An array of object that the caller can then manipulate later.	 	 	 	 
	 **/	 	
	public function triggerEvent( $event , $arrayParams = null , $needOrdering = false )
	{
		$mainframe  =& JFactory::getApplication();
		$dispatcher =& CDispatcher::getInstanceStatic();
		$content = array();

		// Avoid problem with php 5.3
		if(is_null($arrayParams)){
			$arrayParams = array();
		}
		
		// @todo: Fix ordering so we only load according to user ordering or site wide plugin ordering.
		// as we cannot use the mainframe to trigger because all the data are already outputed, no way to manipulate it.
		switch( $event )
		{
			case 'onProfileDisplay':
				$content	= $this->_triggerOnProfileDisplay($event );
			break;
			case 'onFormDisplay':
				// We need to merge the arrays
				$content	= $this->_triggerOnFormDisplay( $event , $arrayParams );
			break;
			default:
				// Trigger system events
				$systemObj = new CEventTrigger();
				call_user_func_array(array(&$systemObj, $event), $arrayParams);
				$observers =& $dispatcher->getObservers();
				//$dispatcher->trigger( $event , $arrayParams );
				for( $i = 0; $i < count($observers); $i++ )
				{
					$plgObj = $observers[$i];
					if( is_object($plgObj))
					{
						$plgObjWrapper = new CPluginWrapper($plgObj);
						if( $plgObjWrapper->getPluginType() == 'community' 
							&& method_exists($plgObj, $event )
							&& ( $plgObjWrapper->getPluginName() != 'input' ) ) 
						{
							// Format the applications with proper heading
							
							
							// Trigger external apps
							$content[] = call_user_func_array(array(&$plgObj, $event), $arrayParams);
						}
					}
				}

			break;
		}
		
		$this->triggerCount++;
		return $content;
	}
	
	/**
	 * Method to include the ajax function names
	 **/
	public function loadAjaxPlugins()
	{
		//@todo: need to fetch plugins from database.

		$ajaxCall	= JRequest::getVar('func');
		
		$func		= explode(',', $ajaxCall);
		
		// We expect the second argument is the plugin name so we use it as the plugin name.
		$func		= JString::trim($func[1]);

		$jaxFile	= JPATH_PLUGINS . DS . 'community' . DS . $func . DS . 'jax.' . $func . '.php';
		
		//possible plugin path in Joomla 1.6
		$jaxFile16	= JPATH_PLUGINS . DS . 'community' . DS . $func . DS . $func . DS . 'jax.' . $func . '.php';
		
		jimport('joomla.filesystem.file');
		// Ajax file definitions should all be in JOOMLA/plugins/community/APPLICATION/jax.APPLICATION.php
		if( JFile::exists( $jaxFile ) )
		{
			require_once( $jaxFile );
		}
		else if( JFile::exists( $jaxFile16 ) )
		{
			require_once( $jaxFile16 );
		}
		else
		{
			// @todo: log any plugin not exists for debugging purposes?
		}
		
	}
	/**
	 * Calls the plugins AJAX methods
	 * 
	 * @param none
	 **/	 	 	 	
	public function ajax()
	{
		
	}
	
	/**
	 * Return the plugin object
	 * @param	string	app name
	 * @param	int		unique app id, unique per user	 	 
	 */
	public function &get($pluginsName= '', $id=0, $params=array())
	{
		static $pluginInstances;
		
		if(!$pluginInstances)
		{
			$pluginInstances = array();
		}
		
		if(empty($pluginsName))
		{
			$appModel = CFactory::getModel('apps');
			$pluginsName = $appModel->getAppName($id); 
		}
		
		if(!isset($pluginInstances[$pluginsName.$id]))
		{
			// Load applications since this is an ajax request, we need to load them.
			$this->loadApplications();
			
			// Try to get the plugin from the dispatcher
			$plg		= $this->_getPlgFromDispatcher($pluginsName);

			$pluginInstances[ $pluginsName . $id ]	= $plg;
		}
		return $pluginInstances[$pluginsName.$id];
	}
	
	/**
	 *
	 */
	public function showAbout($appName)
	{
		$app	= CFactory::getModel('apps');
		$appObj = $app->getAppInfo($appName);
		
		// Load application language so that JText can properly translate it.
		CAppPlugins::loadLanguage( 'plg_' . $appObj->name , JPATH_ADMINISTRATOR );
		
		// Trim out tab's in xml files.
		$appObj->description	= JString::trim( $appObj->description );
		
		$tmpl	=   new CTemplate();
		return $tmpl	->set( 'app'	, $appObj )
				->fetch( 'apps.about' );
	}
	
	/**
	 *
	 */
	public function removeApp($userid, $appName){
		$app = CFactory::getModel('apps');
		$html = $app->deleteApp($userid, $appName);
		return;
	}
	
	/*
	 * Retrieves HTML output for privacy settings 
	 */
	public function showPrivacy($appName)
	{
		$app	= CFactory::getModel('apps');
		$my		=& JFactory::getUser();

		$selected	= $app->getPrivacy( $my->id , $appName );

		$showCheck0 = ( $selected == 0 ) ? ' checked="checked"' : '' ;
		$showCheck1 = ( $selected == 10 ) ? ' checked="checked"' : '' ;
		$showCheck2 = ( $selected == 20 ) ? ' checked="checked"' : '' ;

		$tmpl	=   new CTemplate();		
		return	$tmpl	->set( 'showCheck0' , $showCheck0 )
				->set( 'showCheck1' , $showCheck1 )
				->set( 'showCheck2' , $showCheck2 )
				->set( 'appName'    , $appName )
				->fetch( 'apps.privacy' );
	}
	
	/**
	 * Get config html from the xml file
	 */	
	public function showConfig($pluginsName, $params=array()){
	}
	
	
	/**
	 * Load and return the $param object for the given plugin
	 */
	public function loadConfig($pluginsName, $params=array()){
	}
	
	/**
	 * Return true if the apps is already installed by the  given user
	 */	 	
	public function isInstalled($userid, $appname){
		$app = CFactory::getModel('apps');
		
	}

	public function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR){
		if (empty($extension)) {
			return false;
		}
		return JFactory::getLanguage()->load(strtolower($extension), $basePath, null, false, false);
	}
}