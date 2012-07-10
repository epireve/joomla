<?php
/**
 * @package	JomSocial
 * @subpackage Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view');

class CommunityView extends JView
{
	var $_info 	= array();
	var $_warning 	= array();
	var $_error 	= array();
	var $_submenu	= array();
	var $title 	= '';
	var $_mini	= '';
	var $params	= array();
	var $_showMiniHeaderUser = '';
	
	public function __construct($config = array())
	{
		$mainframe =& JFactory::getApplication();
		$this->my =& JFactory::getUser();
		parent::__construct($config);

		$view = JRequest::getVar('view' , '' , 'REQUEST' );
	}
	
	/**
	 * 
	 **/	 	
	public function setTitle($title) {
		CFactory::load( 'helpers' , 'string' );
		$this->title = CStringHelper::escape($title);
	}
	
	/**
	 * Append the given message into a global info messages
	 * @param string	the final message	 
	 */	 	
	public function addInfo($message)
	{
		$mainframe	=& JFactory::getApplication();
		$mainframe->enqueueMessage($message);
	}
	
	/**
	 * Adds a pathway item to the breadcrumbs
	 **/
	public function addPathway( $text , $link = '' )
	{
		// Set pathways
		$mainframe		=& JFactory::getApplication();
		$pathway		=& $mainframe->getPathway();
		
		$pathwayNames	= $pathway->getPathwayNames();
		
		// Test for duplicates before adding the pathway
		if( !in_array( $text , $pathwayNames ) )
		{
			$pathway->addItem( $text , $link );
		}
	}
	
	/**
	 * Display no access notice
	 */	 	
	public function noAccess($notice = '')
	{
		$tmpl	= new CTemplate();
		$notice	= empty( $notice ) ? JText::_('COM_COMMUNITY_NOT_ALLOWED_TO_VIEW_PAGE') : $notice;
		
		$tmpl->set( 'notice' , $notice );										
		echo $tmpl->fetch('notice.access');
	}
	
	/**
	 * Append the given message into a global warning messages
	 * @param string	the final message	 
	 */	 
	public function addWarning($message){
		//$this->_warning[] = $message;
		$mainframe =& JFactory::getApplication();
		$mainframe->enqueueMessage($message, 'notice');
	}
	
	public function attachMiniHeaderUser( $userId )
	{
		$this->_showMiniHeaderUser	= $userId;
	}
	
	/**
	 *
	 */	 	
	public function addSubmenuItem($link='', $title='', $onclick='', $isAction=false, $childItem='')
	{
		$obj = new stdClass();
		$obj->link = $link;

		// If onclick is used, completely ignore the following
		if( !empty($onclick) )
		{
			$obj->onclick = $onclick;
		}
		else
		{
			// We need to get the view & task from the link that is provided in $link
			// Remove default 'index.php?option=com_community&' from $link
			$link  = CString::str_ireplace('index.php?option=com_community&', '', $link);
			$links = explode('&', $link);
	
			// Pre-set the task so that links that does not contain task will not fail.
			$obj->task = '';
			foreach( $links as $row )
			{
				$active = explode('=', $row);
				
				if($active[0] == 'view')
					$obj->view = $active[1];
	
				if($active[0] == 'task')
					$obj->task = $active[1];
			}
		}

		$obj->action	= $isAction;
		$obj->title		= $title;
		$obj->childItem = $childItem;

		$this->_submenu[] = $obj;
	}

	/**
	 * Deprecated since 2.2
	 * Should use CToolbarLibrary::getHTML instead
	 **/	 	 	 		
	public function showToolbar($data = null)
	{
		CFactory::load( 'libraries' , 'toolbar' );

		$toolbar_lib = CToolbarLibrary::getInstance();
		echo $toolbar_lib->getHTML( $this->_showMiniHeaderUser );
	}

	/*
	 * Temporary replacement as we don't want
	 * showToolbar() to load all the unnecessary
	 * scripts & styles.
	 *
	 **/
	public function showToolbarMobile()
	{
		$tmpl = new CTemplate();
		$searchform =	$tmpl->fetch('search.form');
		$tmpl->set( 'searchform', $searchform );
		echo $tmpl->fetch('toolbar.index');
	}
	
	/**
	 *
	 */	 	
	public function showSubmenu( )
	{
		$submenu = &$this->_submenu;

		if (!empty($submenu))
		{
	
			$view   = JRequest::getVar('view' , '' , 'GET');
			$task   = JRequest::getVar('task' , '' , 'GET');
		
			// Shift action menu items to the back
			$i=0; $shifted=0; $total=count($submenu);
			while($i+$shifted<$total)
			{
				if ($submenu[$i]->action)
				{
					$menu = array_splice($submenu, $i, 1);
					$submenu = array_merge($submenu, $menu);
					$i--; $shifted++;
				}
				$i++;
			}

			$tmpl = new CTemplate();
			$tmpl ->set('submenu', $submenu);
			$tmpl ->set('view', $view);
			$tmpl ->set('task', $task);
			echo $tmpl->fetch('toolbar.submenu');
			unset($tmpl);
		}
	}
	
	/**
	 * Return the page header
	 * @return	string	 
	 */	 	
	public function showHeader($title, $icon = null, $buttons = null)
	{
		$view = JRequest::getVar('view', 'frontpage', 'REQUEST');
		$task = JRequest::getVar('task', '', 'REQUEST');
		
	    $style = '';
	    if ( $icon != null )
	    {
		    $style .= ' toolbar-icon-'.$icon;
		}
	        
	    $showHead = true;
	    
	    // Do not add header to know page, profile
		if(($view == 'profile' && ($task == '' || $task == 'display')) || $view == 'frontpage')
		{
			$showHead = false;
		}

		if($showHead)
		{
			$tmpl = new CTemplate();
			$tmpl->set('style', $style);
			$tmpl->set('title', $title);
			
			echo $tmpl->fetch('toolbar.header');
		}
	}
	
	/**
	 * Return page submenu
	 */	 	
	public function getSubMenu(){
	}
	
	/**
	 * Get the processed content
	 * 	 
	 * @param	string	$tplName	method name to call
	 * @param	array	$data		data for the template
	 * @param	string	$cached		should we result be cached?
	 * @return	string				the final output 	 
	 */	 	
	public function get($tplName, $data=null, $cached=false){

		if(!empty($tplName) && is_callable(array($this, $tplName))){
			
			ob_start();
			$this->$tplName($data);
			$html = ob_get_contents();
			ob_end_clean();
			
			
			$info 		= '';
			if(!empty($this->_info)){
				foreach($this->_info as $msg){
					$info .= $this->info($msg);
				}
			}
			
			$warning 	= '';
			if(!empty($this->_warning)){
				foreach($this->_warning as $msg){
					$warning .= $this->warning($msg);
				}
			}
			
			$error 		= '';
			$messages = array($error, $warning, $info);
			
			// append all warning, error and info
			$html = CString::str_ireplace(array('{error}','{warning}', '{info}'), $messages, $html);
			return $html;
			
		} else {
			JError::raiseError( 500, JText::sprintf('COM_COMMUNITY_VIEW_NOT_FOUND' , $tplName) );
		}
		
	}
	
	/**
	 * Show profile with limited access view.
	 * Just the box with links to add as friend etc
	 */
	public function showLimitedProfile( $userid )
	{
		$my	  = CFactory::getUser();
		$user = CFactory::getUser($userid);
		$mainframe =& JFactory::getApplication();
		$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS'), 'notice');
		$tmpl = new CTemplate();
					
		$userInfo = $this->_prepUser($user);
		if(! empty($userInfo))
		{	
			$tmpl->set('data', $userInfo);
			$tmpl->set('sortings', '');// add this variable to avoid error thrown.																
			$tmpl->set('featuredList' , '');
			$tmpl->set('isCommunityAdmin','');
			$tmpl->set('my',$my);
			echo $tmpl->fetch('people.browse');
		}
	}
	
	/**
	 * Check if current user has the correct permission to view the page.
	 * We will validate access based on the current profile privacy setting and
	 * the access type give. Should be called by view
	 * 
	 * @param string type The access type, one of CUser param variables or 
	 * 	 mine (active profile = my id)/registered(any registered user)
	 * @param bool $showWarning 	 	 	 	 	 
	 * @return	bool true if access is OK	 
	 */	 	
	public function accessAllowed($type = '', $showWarning = true)
	{
		if(empty($type))
			return true;

		$my 	= CFactory::getUser();
		
		$userid		= JRequest::getVar( 'userid' , '' );
		$user		= CFactory::getUser($userid);
		
		
		// @rule: For site administrators / community admin, we should allow access
		// no matter what the privacy is.
		CFactory::load( 'helpers' , 'owner' );
		if( COwnerHelper::isCommunityAdmin() )
		{
			return true;
		}
		
		if($type == 'registered')
		{
			if(!$my->id){
				$mainframe =& JFactory::getApplication();
				$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS'), 'notice');
				$this->noAccess();
				return false;
			} else
				return true;
		}
		
		// you can always view your own profile
		if(COwnerHelper::isMine($my->id, $user->id)){
			return true;
		}
		
		$param =& $user->getParams();
		
		if($type == 'mine')
			$access = PRIVACY_PRIVATE;
		else
			$access = $param->get($type);

		switch($access){
			case PRIVACY_PUBLIC:
				return true;
				break;
				
			case PRIVACY_MEMBERS:
				if( $my->id == 0 )
				{
					$mainframe =& JFactory::getApplication();
					$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS'), 'notice');				
					$tmpl = new CTemplate();
					$notice		= '';
					
					if($type == 'privacyProfileView')
					{
						$userInfo = $this->_prepUser($user);
						if(! empty($userInfo))
						{	
							$tmpl->set('data', $userInfo);
							$tmpl->set('sortings', '');
							$tmpl->set('featuredList' , '');
							$tmpl->set('isCommunityAdmin','');
							$tmpl->set('my' , $my );
							echo $tmpl->fetch('people.browse');
						}
						else							
							//user object not found.
							$notice	= JText::_('COM_COMMUNITY_NOT_ALLOWED_TO_VIEW_PAGE');
							
							$tmpl->set( 'notice' , $notice );
							echo $tmpl->fetch('notice.access');
							
					}				
					else
					{																
						$this->noAccess();
					}
					return false;
				}
				return true;	
				break;
				
			case PRIVACY_FRIENDS:
				CFactory::load('helpers', 'friends');

				if( !CFriendsHelper::isConnected($my->id, $user->id) )
				{
					$mainframe =& JFactory::getApplication();
					$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS'), 'notice');
					$tmpl = new CTemplate();
					
					if($type == 'privacyProfileView')
					{
						$userInfo = $this->_prepUser($user);
						if(! empty($userInfo))
						{	
							$tmpl->set('data', $userInfo);
							$tmpl->set('sortings', '');// add this variable to avoid error thrown.																
							$tmpl->set('featuredList' , '');
							$tmpl->set('isCommunityAdmin','');
							$tmpl->set('my',$my);
							echo $tmpl->fetch('people.browse');
						}
						else
						{							
							//user object not found.
							$this->noAccess();
						}
					}	
					else
					{
						$this->noAccess();
					}
					return false;
				}
				else				
					return true;
				
				break;
				
			case PRIVACY_PRIVATE:

				if($my->id != $user->id)
				{
					$mainframe =& JFactory::getApplication();
					$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS'), 'error');
					$this->noAccess();
					return false;
				} else
					return true;
					
				break;
		}
			
		return true;
	}

	/**
	 * Test if the application is viewable by the current browser.
	 * 
	 * @param string $privacy The privacy settings for the app.
	 * @return	bool true if access is OK	 
	 */	 	
	public function appPrivacyAllowed( $privacy = 0 )
	{
		if( $privacy == 0 )
		{
			return true;
		}

		$my 		= CFactory::getUser();
		$userid		= JRequest::getVar( 'userid' , '' );
		$user		= CFactory::getUser($userid);
		
		$mainframe 	=& JFactory::getApplication();
		
		switch( $privacy )
		{
			case PRIVACY_APPS_FRIENDS:
				if( !CFriendsHelper::isConnected($my->id, $user->id) )
				{
					return false;
				}
				break;
			case PRIVACY_APPS_SELF:
				if( $my->id != $user->id )
				{
					return false;
				}
				break;
		}
		return true;
	}

	/**
	 * Show profile miniheader
	 */
	public function _getMiniHeader()
	{
		CFactory::load( 'libraries' , 'miniheader' );
		
	    $my 	= CFactory::getUser();
	    $mh 	= new CMiniHeader();
	    return $mh->showMiniHeader($my->id);
	}
	
	/**
	 *
	 */
	public function _newNotification()
	{
		$my 	= CFactory::getUser();

		$inboxModel		= CFactory::getModel( 'inbox' );
		$friendModel	= CFactory::getModel ( 'friends' );
		$eventModel		= CFactory::getModel( 'events' );
		
		$filter = array();
		$filter ['user_id'] = $my->id;
		$inboxAlert		= $inboxModel->countUnRead( $filter );
		$frenAlert		= $friendModel->countPending( $my->id );
		$eventAlert		= $eventModel->countPending( $my->id );
		
		return ($inboxAlert + $frenAlert + $eventAlert);
	} 
	
	
	/**
	 * enqueue redirect for unpublish groups
	 */	 	
	protected function _redirectUnpublishGroup()
	{
		$mainframe =& JFactory::getApplication();
		$mainframe->redirect( CRoute::_('index.php?option=com_community&view=groups') , JText::_('COM_COMMUNITY_GROUPS_UNPUBLISH_WARNING') );
	}
		 	
	
	/**
	 * This function will prep user info so that it can display user mini header in privacy warning template.
	 * Do not call this function outside this view.php	 	 
	 */	 	 	
	
	public function _prepUser( $user )
	{			
		if(! empty($user))
		{
			$obj = new stdClass();	
			$my					= CFactory::getUser();			
			$user				= CFactory::getUser( $user->id );
			$obj->friendsCount  = $user->getFriendCount();
			$obj->user			= $user;
			$obj->profileLink	= CUrl::build( 'profile' , '' , array( 'userid' => $user->id ) );
			$isFriend 			= CFriendsHelper::isConnected( $user->id, $my->id );
			
			$obj->addFriend 	= ((! $isFriend) && ($my->id != 0) && $my->id != $user->id) ? true : false;
			return array( $obj );
		}
		
		return false;

	}

	/*
	 * This function will return the apps' links
	 */
	public function getAppSearchLinks( $currentApp = NULL )
	{
		$apps	=   array(
				JText::_('COM_COMMUNITY_SEARCH_USERS_TITLE')	=>	CRoute::_('index.php?option=com_community&view=search'),
				JText::_('COM_COMMUNITY_SEARCH_GROUPS_TITLE')	=>	CRoute::_('index.php?option=com_community&view=groups&task=search'),
				JText::_('COM_COMMUNITY_SEARCH_VIDEOS_TITLE')	=>	CRoute::_('index.php?option=com_community&view=videos&task=search'),
				JText::_('COM_COMMUNITY_SEARCH_EVENTS_TITLE')	=>  CRoute::_('index.php?option=com_community&view=events&task=search')
			    );

		$key	= array_key_exists( $currentApp, $apps);

		if( $key )
		{
			unset( $apps[$currentApp] );
		}

		return $apps;
	}
	
	/**
	 * Cached the function call
	 */	 	
	protected function _cachedCall($func, $params, $id, $tags)
	{
                $jConfig    =&  JFactory::getConfig();
                
                // Check if the caching is enabled
                if($jConfig->getValue('caching')){
                        $cache = CFactory::getFastCache();
		
                        $cacheid = __FILE__ . __LINE__ . $func . md5(serialize($params) . serialize($id) ) ;

                        if( $result = $cache->get( $cacheid ) )
                        {
                                return $result;
                        }
                        
                        $result = call_user_func_array(array($this, $func), $params);
                        $cache->store($result, $cacheid, $tags);
                }else{
                        $result = call_user_func_array(array($this, $func), $params);
                }
		
		return $result;
	}

	public function attachHeaders()
	{
		$document	= JFactory::getDocument();
		$config		= CFactory::getConfig();

		$my 		= CFactory::getUser();
		$userid		= JRequest::getVar( 'userid' , '' );
		$user		= CFactory::getUser($userid);

		if( $document->getType() != 'html' )
		{
			return;
		}
		
		$document	= JFactory::getDocument();
		$js = '<script type=\'text/javascript\'>';
		$js .= '/*<![CDATA[*/';
		$js .= 'var js_viewerId  = '. $my->id .'; ';
		$js .= 'var js_profileId = '. $user->id .';';
		$js .= '/*]]>*/';
		$js .= '</script>';
		$document->addCustomTag($js);
		
		// This need to be loaded so the popups will work correctly in notification window
		CFactory::load( 'libraries' , 'window' );
		CWindow::load();
		
		CFactory::load( 'libraries' , 'minitip' );
		CMinitip::load();
		
		CFactory::load( 'libraries' , 'template' );
		CTemplate::addStylesheet( 'style' );

		$templateParams = CTemplate::getTemplateParams();
		CTemplate::addStylesheet( 'style.' . $templateParams->get('colorTheme') );

		// Load rtl stylesheet
		if ($document->direction=='rtl')
		{
			CTemplate::addStylesheet( 'style.rtl' );
		}

		$template = new CTemplateHelper;
		$styleIE7 = $template->getTemplateAsset('styleIE7', 'css');
		$styleIE6 = $template->getTemplateAsset('styleIE6', 'css');

		$css = '<!-- Jom Social -->
				<!--[if IE 7.0]>
				<link rel="stylesheet" href="'. $styleIE7->url . '" type="text/css" />
				<![endif]-->
				<!--[if lte IE 6]>
				<link rel="stylesheet" href="'. $styleIE6->url . '" type="text/css" />
				<![endif]-->';

		$document->addCustomTag( $css );
		
		$css = 'assets/autocomplete.css';
		CAssets::attach($css, 'css');
		
		// Required, but added in default template
		//$css = 'assets/tipsy-0.1.7/src/stylesheets/tipsy.css';
		//CAssets::attach($css, 'css');
		
		// Load joms.ajax
		CTemplate::addScript('joms.ajax');
	}
}
