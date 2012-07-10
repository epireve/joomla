<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class CommunityBaseController extends JController
{
	var $_modelInstances = array();
	var $_libraryInstances = array();
	var $_viewInstances = array();
	var $_name;
	private $_icon = 'generic';
	var $my = null;
	
	public function getName(){
		return $this->_name;
	}
	
	public function CommunityBaseController($config = array()){
		                              
		if(!empty($config)){
			$this->_name = $config['name'];
		}
		$this->my =& JFactory::getUser();
	}
	
	/**
	 * Deprecated since 1.8.x
	 */	 	
	public function _notify($cmd, $from, $to, $subject, $body, $template='', $params = '')
	{
		CFactory::load( 'libraries' , 'notification' );
		return CNotificationLibrary::add( $cmd , $from , $to , $subject , $body , $template , $params );
	}
	
	/**
	 * A guest trying to use registered-only part of the system via ajax. Display
	 * a link to register	  
	 */	 	
	public function ajaxBlockUnregister()
	{
		$objResponse	= new JAXResponse();
		$uri			= CFactory::getLastURI();
		$uri			= base64_encode($uri);
		$config			= CFactory::getConfig();

		$usersConfig =& JComponentHelper::getParams('com_users');

		$tmpl			= new CTemplate();
		$tmpl->set( 'uri'	    , $uri );
		$tmpl->set( 'config'	    , $config );
		$tmpl->set( 'usersConfig'   , $usersConfig );
		$html			= $tmpl->fetch( 'block.unregistered' );

                $objResponse->addScriptCall('if(!joms.jQuery("#cWindow").length){cWindowShow("","",450, 100);}');
		$objResponse->addScriptCall('cWindowAddContent', $html);

		return $objResponse->sendResponse();
	}
	
	/**
	 * Block user access to the  controller method.
	 */	 	
	public function blockUserAccess()
	{
		$document 	= JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN'));
		//echo JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN');
		
		$tmpl = new CTemplate();
		$notice	= JText::_('COM_COMMUNITY_NOT_ALLOWED_TO_VIEW_PAGE');
		
		$tmpl->set( 'notice' , $notice );
		echo $tmpl->fetch('notice.access');
		
		return true;

	}
	
	// Block non-login mebers
	public function blockUnregister( $uri=null )
	{
		$my		= CFactory::getUser();
		$config	= CFactory::getConfig();
		
		if($my->id == 0)
		{
			$config	= CFactory::getConfig();
			
			if(empty($uri))
			{
				$uri	= CRoute::getURI( false );
			}
			$uri	= base64_encode($uri);
			$tmpl	= new CTemplate();

			$fbHtml	= '';

			if( $config->get('fbconnectkey') && $config->get('fbconnectsecret') )
			{
				CFactory::load( 'libraries' , 'facebook' );
				$facebook	= new CFacebook( FACEBOOK_LOGIN_NOT_REQUIRED );
				$fbHtml		= $facebook->getButtonHTML();
			}
			$tmpl->set( 'fbHtml' , $fbHtml );						
			$tmpl->set( 'return' , $uri );
			$tmpl->set( 'config' , $config );
			$html	= $tmpl->fetch( 'guests.denied' );
			echo $html;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Return the view object, which will output the final html. The view object
	 * is a singleton
	 * 	 	 
	 * @param	string		view name
	 * #param	string		view class prefix, optional	 
	 * @param	string		document type, html/pdf/etc/
	 * @return	object		the view object	 
	 */	 	
	public function getView($viewName ='frontpage', $prefix = '', $viewType = '')
	{
		return CFactory::getView($viewName, $prefix, $viewType);
	}
	
	
	public function loadHelper($name){
		include_once(JPATH_COMPONENT.DS.'helpers'.DS.$name.'.php');
	}
	
	public function getLibrary( $name = '', $prefix = '', $config = array() ){
		if(!isset($this->_libraryInstances[$name]))
		{
			include_once(JPATH_COMPONENT.DS.'libraries'.DS.$name.'.php');
			$classname = 'CommunityLib'.$name;
			$this->_libraryInstances[$name] = new $classname;
		}
		return $this->_libraryInstances[$name];
	}
	
	//debug data
	private function _dump(&$data){
	
	    echo '<pre>';
		print_r($data);
		echo '</pre>';
		exit;
		
	}
	
	/**
	 * Return the model object, responsible for all db manipulation. Singleton
	 * 	 
	 * @param	string		model name
	 * @param	string		any class prefix
	 * @return	object		model object	 
	 */	 	
	public function getModel( $name = '', $prefix = '', $config = array() )
	{
		return CFactory::getModel($name, $prefix, $config);
	}
	
	// Our own display function
	public function display($cachable = false)
	{
		$document = JFactory::getDocument();

		$viewType	= $document->getType();	
 		$viewName	= JRequest::getCmd( 'view', $this->getName() );
 		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		
		$view = & $this->getView( $viewName, '', $viewType);

		// Display the view
		if ($cachable) {
			global $option;
			$cache =& JFactory::getCache($option, 'view');
			$cache->get($view, 'display');
		} else {
			$view->profile();
		}
	}
	
	/**
	 * Execute a request
	 */	 	
	public function execute($task='')
	{
		$mainframe  =& JFactory::getApplication();
		$document 	= JFactory::getDocument();
		$my 		=& JFactory::getUser();
		$pathway 	=& $mainframe->getPathway();
		
		$menus		=& JSite::getMenu();
		$menuitem	=& $menus->getActive();

		$userId		= JRequest::getInt( 'userid','','GET');
		$tmpl		= JRequest::getVar( 'tmpl','' , 'REQUEST');
		$format		= JRequest::getVar( 'format' , '' , 'REQUEST' );
		$nohtml		= JRequest::getVar( 'no_html' , '' , 'REQUEST' );
		
		if($tmpl != 'component' && $format != 'feed'  && $format != 'ical' && $nohtml != 1 && $format != 'raw' )
		{
			// This is to fix MSIE that has incorrect user agent because jquery doesn't detect correctly.
			$ieFix = "<!--[if IE 6]><script type=\"text/javascript\">var jomsIE6 = true;</script><![endif]-->";
			$document->addCustomTag($ieFix);
		}
		
		// Add custom css for the specific view if needed.
		$config   = CFactory::getConfig();
		$viewName = JString::strtolower( JRequest::getVar('view' , '' , 'REQUEST' ) );
		jimport( 'joomla.filesystem.file' );
		
		if ($config->get('enablecustomviewcss'))
		{
			CTemplate::addStylesheet($viewName);
		}

		$env = CTemplate::getTemplateEnvironment();

		$html = '<div id="community-wrap" class="on-' . $env->joomlaTemplate . ' ' . $document->direction . '">';
		
		// Build the component menu module
		ob_start();
			CTemplate::renderModules('js_top');
		$moduleHTML = ob_get_contents();
		ob_end_clean();
		$html .= $moduleHTML;
		
		// Build the content HTML
		CFactory::load( 'helpers' , 'azrul' );
		
		if (!empty($task) && method_exists($this, $task))
		{
			ob_start();
				if (method_exists($this , '_viewEnabled') && !$this->_viewEnabled())
				{
					echo (property_exists( $this , '_disabledMessage') ) ? $this->_disabledMessage : JText::_('COM_COMMUNITY_CONTROLLER_FUNCTION_DISABLED_WARNING');
				}
				else
				{
					$this->$task();
				}
			$output = ob_get_contents();
			ob_end_clean();
		} else {
			ob_start();
				$this->display();
			$output = ob_get_contents();
			ob_end_clean();
		}
		
		// Build toolbar HTML
		ob_start();
			$view =& $this->getView(JRequest::getCmd('view', 'frontpage'));
			
			// Do not rely on the toolbar to include necessary css or javascripts.
			$view->attachHeaders();
			
			// Display the site's toolbar.
			$view->showToolbar();
			
			
			// Header title will use view->title. If not specified, we will
			// use current page title
			$headerTitle = !empty($view->title) ? $view->title : $document->getTitle();
			$view->showHeader($headerTitle, $this->_icon );
			
		$header = ob_get_contents();
		ob_end_clean();
		$html .= $header;  

		// @rule: Super admin should always be allowed regardless of their block status
		// block member to access profile owner details
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'libraries' , 'block' );
		$getBlockStatus = new blockUser();
		$blocked = $getBlockStatus->isUserBlocked( $userId, $viewName );		
		if ($blocked)
		{
			if (COwnerHelper::isCommunityAdmin())
			{
				$mainframe =& JFactory::getApplication();
				$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_YOU_ARE_BLOCKED_BY_USER', 'error'));				
			} else {
				$tmpl	 = new CTemplate();
				$output	.= $tmpl->fetch( 'block.denied' );
			}
        }
		
		// Build the component bottom module
		ob_start();
			CTemplate::renderModules('js_bottom');
		$moduleHTML = ob_get_contents();
		ob_end_clean();

		$html .= $output.$moduleHTML.'</div>';
		
		CFactory::load( 'helpers' , 'string' );
		$html = CStringHelper::replaceThumbnails($html);
		$html = CString::str_ireplace(array('{error}', '{warning}', '{info}'), '', $html);

		// Trigger onModuleDisplay()
		CFactory::load( 'libraries' , 'apps' );
		$appsLib = CAppPlugins::getInstance();
		$appsLib->loadApplications();		

		$moduleHTML = $appsLib->triggerEvent( 'onModuleRender');
		$mods = array();
		if (is_array($moduleHTML))
		{
			foreach($moduleHTML as $modules){
				if (is_array($modules))
				{
					foreach($modules as $position => $content){
						if(empty($mods[$position]))
								$mods[$position] = '';

						$mods[$position] .= $content;
					}
				}
			}
		}

		foreach($mods as $position => $module){
			$html = str_replace('<!-- '.$position. ' -->', $module, $html);
		}

		// Display viewType switcher
		// $browser = CBrowser::getInstance();
		// if( $browser->_mobile )
		// {
		// 	CFactory::load('libraries', 'mobile');

		// 	ob_start();
		// 		CMobile::showSwitcher();
		// 		$switcherHTML = ob_get_contents();	
		// 	ob_end_clean();

		// 	$html .= $switcherHTML;
		// }

		echo $html;
	}

	public function executeMobile($task='')
	{
		CFactory::load('libraries', 'mobile');

		$view =& $this->getView(JRequest::getCmd('view', 'frontpage'));

		// Fetch content
		ob_start();
			if(!empty($task) && method_exists($this, $task))
			{
				$this->$task();
			} else {
				$this->display();
			}
			$content = ob_get_contents();
		ob_end_clean();

		// Swap joomla document with
		// CDocumentMobile or CDocumentMobileAjax
		$document = JFactory::getDocument();

		if (JRequest::getCmd('section')=='content')
		{
			$document = new CDocumentMobileAjax($document);
			echo $content;
		} else {
			$document = new CDocumentMobile($document);			

			// Add our scripts & stylesheets
			CTemplate::addScript('script.mobile-1.0');
			CTemplate::addScript('joms.ajax');
			CTemplate::addStylesheet('style.mobile');

			// Fetch toolbar
			ob_start();
				$view->showToolbarMobile();
				$toolbar = ob_get_contents();
			ob_end_clean();

			// Fetch switcher		
			ob_start();
				CMobile::showSwitcher();
				$switcher = ob_get_contents();
			ob_end_clean();

			$tmpl = new CTemplate();
			$tmpl->set('toolbar' , $toolbar);
			$tmpl->set('content' , $content);
			$tmpl->set('switcher', $switcher);
			echo $tmpl->fetch('mobile.index');	
		}
	}

	/**
	 * Execute ajax request
	 */	 	
	public function executeAjax($method, $ajaxArg)
	{
		$filter	    =	JFilterInput::getInstance();
		$method	    =	$filter->clean( $method, 'string' );
		$ajaxArg	    =	$filter->clean( $ajaxArg, 'string' );
		
		if(!empty($method) && method_exists($this, $method))
		{
			$this->$method($ajaxArg);
			//call_user_func('$this->'.$method, $ajaxArg);
		}
		else
		{
			$this->display();
		}
	}
		
	/**
	 * restrict blocked user to access owner details
	 */	 	
	public function ajaxBlock()
	{
		$objResponse	= new JAXResponse();
		$uri			= CFactory::getLastURI();
		$uri			= base64_encode($uri);
		$config			= CFactory::getConfig();
		$tmpl			= new CTemplate();
		$tmpl->set( 'uri' , $uri );
		$tmpl->set( 'config'	, $config );
		$html			= $tmpl->fetch( 'block.denied' );

		$objResponse->addScriptCall('cWindowAddContent', $html);

		return $objResponse->sendResponse();
	}
	
	/**
	 * restrict user to block community admin
	 */
	public function ajaxRestrictBlockAdmin()
	{   
		$config		= CFactory::getConfig(); 
		$response	= new JAXResponse();
		
		$actions	= '<form name="jsform-profile-ajaxblockuser" method="post" action="" style="float:right;">';
		$actions	.= '<input type="button" class="button" onclick="cWindowHide();return false;" name="cancel" value="'.JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON').'" />'; 
		$actions	.= '</form>';   

		$html = JText::_('COM_COMMUNITY_CANNOT_BLOCK_COMMUNITY_ADMIN');
		$response->addScriptCall('cWindowAddContent', $html);
		$response->addAssign('cwin_logo', 'innerHTML', $config->get('sitename'));

		$response->sendResponse();
	}	 	
	 
	public function cacheClean($cacheId){
		$cache = CFactory::getFastCache();

		$cache->clean($cacheId);
	}

}
