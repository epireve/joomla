<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(dirname(dirname(__FILE__)).'/help/menus.helper.php');
require_once(dirname(dirname(__FILE__)).'/help/menu.class.php');

require_once(dirname(__FILE__).'/ap_avatar.php');

class AdminPraise3Tools extends JObject {

	protected static $_instance;
	
	var $_db			= null;
	var $_user			= null;
	var $_template_name	= null;
	var $_template_path = null;
	var $_menu			= null;
	var $params			= array();
	var $headBuffer		= null;
	var $scriptBuffer	= null;
	var $scriptSafe		= null;

	private function AdminPraise3Tools() {
		$this->_db = &JFactory::getDBO();
		$this->_initVariables();
		$this->_generateHead();
	}
	
	public static function &getInstance() {
		if (AdminPraise3Tools::$_instance === null) :
			AdminPraise3Tools::$_instance = new AdminPraise3Tools();	
		endif;
	
		return AdminPraise3Tools::$_instance;	
	}
	
	function setParam($name, $value) {
		$this->params[$name] = $value;
	}
	
	function get($name) {
		if (isset($this->params[$name])) :
			return $this->params[$name];
		else :
			return null;
		endif;
	}
	
	private function _initVariables() {
		$document = &JFactory::getDocument();
		$user =&JFactory::getUser();
		
		$this->set('_template_name', $document->template);
		
		$template_path = JPATH_ADMINISTRATOR.'/templates/'.$document->template;
		$this->set('_template_path', $template_path);
		
		// set custom template theme for user
		if( !is_null( JRequest::getCmd('templateTheme', NULL))) :
			$user->setParam($document->template.'_theme', JRequest::getCmd('templateTheme'));
			$user->save(true);
		endif;
		
		$this->_user = $user;
		
		if($user->getParam($document->template.'_theme')) {
			$theme = $user->getParam($document->template.'_theme');
			$document->params->set('templateTheme', $theme);
			$this->setParam('templateTheme', $theme);
		}
				
		$logoFile = 'templates/'. $document->template .'/images/logo-'.$document->params->get('templateTheme').'.png';
		
		$avatarFile = 'templates/'. $document->template .'/images/avatar.png';
		
		$profileLink = "<a href=\"" . JURI::root() . "administrator/index.php?option=com_users&view=user&task=edit&tmpl=component&cid[]=" . $user->get('id') . "\" class=\"modal\" rel=\"{handler: 'iframe', size: {x: 900, y: 550}}\">". $user->get('username') ."</a>";
		
		$profileAvatar = "<a href=\"" . JURI::root() . "administrator/index.php?option=com_users&view=user&task=edit&tmpl=component&cid[]=" . $user->get('id') . "\" class=\"modal\" rel=\"{handler: 'iframe', size: {x: 900, y: 550}}\"><img src=\"". $avatarFile ."\" title=\"". JText::_( 'PROFILE' ) . " " . $user->get('username') ."\"/></a>";
		
		$this->setParam('browser', $this->getBrowserAgent());
		$this->setParam('logoFile',	$logoFile);
		$this->setParam('profileLink',	$profileLink);
		$this->setParam('profileAvatar',	APAvatar::find($document->params->get('showAvatar')));
		
		$this->setParam('ap_task_set', 	JRequest::getCmd('ap_task') != null);
		$this->setParam('ap_task',     	JRequest::getCmd('ap_task'));
		$this->setParam('option',      	JRequest::getCmd('option'));
		$this->setParam('task',      	JRequest::getCmd('task'));
		$this->setParam('view',        	JRequest::getCmd('view'));
		$this->setParam('client',      	JRequest::getCmd('client'));
		$this->setParam('section',     	JRequest::getCmd('section'));
		$this->setParam('scope',       	JRequest::getCmd('scope'));
		$this->setParam('menutype',    	JRequest::getCmd('menutype'));
		$this->setParam('type',    		JRequest::getCmd('type'));
		
		//Template Params
		$this->setParam('templateColor',	$document->params->get('templateColor'));
		$this->setParam('templateTheme',	$document->params->get('templateTheme'));
		
		$this->setParam('shortHeader',    		$document->params->get('shortHeader', 0));
		$this->setParam('fixedHeader',    		$document->params->get('fixedHeader', 0));
		$this->setParam('showMyEditor',    		$document->params->get('showMyEditor', 1));
		$this->setParam('showQuickAdd',    		$document->params->get('showQuickAdd', 1));
		$this->setParam('showComponentList',    $document->params->get('showComponentList', 1));
		$this->setParam('showSideComponentList',    $document->params->get('showSideComponentList', 1));
		$this->setParam('showBottomComponentList',    $document->params->get('showBottomComponentList', 1));
		$this->setParam('switchSidebar',    	$document->params->get('switchSidebar', 0));
		$this->setParam('bottomStatus',    		$document->params->get('bottomStatus', 0));
		$this->setParam('showBreadCrumbs',    	$document->params->get('showBreadCrumbs', 0));
		$this->setParam('showChildren',    		$document->params->get('showChildren', 0));
		$this->setParam('showFootMods',    		$document->params->get('showFootMods', 0));
		$this->setParam('showSubmenu',    		$document->params->get('showSubmenu', 0));
		$this->setParam('showStatusBar',    	$document->params->get('showStatusBar', 0));
		$this->setParam('fontSize',    			$document->params->get('fontSize'));
		$this->setParam('minWidth',    			$document->params->get('minWidth'));
		$this->setParam('sidebarWidth',    		$document->params->get('sidebarWidth'));
		$this->setParam('altToolbar',    	$document->params->get('altToolbar', 0));
		$this->setParam('fallbackTemplate',    	$document->params->get('fallbackTemplate', 0));
		
		$fallbackComponents = explode(',', $document->params->get('fallbackComponents'));
		$this->setParam('fallbackComponents',	$fallbackComponents);
		
		$this->setParam('menusAcl',    			$document->params->get('menusAcl', 0));
		$this->setParam('sectionsAcl',    		$document->params->get('sectionsAcl', 0));
		$this->setParam('categoriesAcl',    	$document->params->get('categoriesAcl', 0));
		$this->setParam('articlesAcl',    		$document->params->get('articlesAcl', 0));
		$this->setParam('componentsAcl',    	$document->params->get('componentsAcl', 0));
		$this->setParam('modulesAcl',    		$document->params->get('modulesAcl', 0));
		$this->setParam('pluginsAcl',    		$document->params->get('pluginsAcl', 0));
		$this->setParam('templatesAcl',    		$document->params->get('templatesAcl', 0));
		$this->setParam('usersAcl',    			$document->params->get('usersAcl', 0));
		$this->setParam('adminAcl',    			$document->params->get('adminAcl', 0));
		$this->setParam('installAcl',    		$document->params->get('installAcl', 0));
		
		$this->setParam('flexicontentAcl',    	$document->params->get('flexicontentAcl', 0));
		$this->setParam('jseblodAcl',    		$document->params->get('jseblodAcl', 0));
		$this->setParam('joomailerAcl',    		$document->params->get('joomailerAcl', 0));
		$this->setParam('k2Acl',    			$document->params->get('k2Acl', 0));
		$this->setParam('kunenaAcl',    			$document->params->get('kunenaAcl', 0));
		$this->setParam('ninjaboardAcl',    		$document->params->get('ninjaboardAcl', 0));
		$this->setParam('phocagalleryAcl',    	$document->params->get('phocagalleryAcl', 0));
		$this->setParam('projectforkAcl',    	$document->params->get('projectforkAcl', 0));
		$this->setParam('sobi2Acl',    			$document->params->get('sobi2Acl', 0));
		$this->setParam('sobiproAcl',    			$document->params->get('sobiproAcl', 0));
		$this->setParam('tiendaAcl',    		$document->params->get('tiendaAcl', 0));
		$this->setParam('virtuemartAcl',    	$document->params->get('virtuemartAcl', 0));
		$this->setParam('zooAcl',    			$document->params->get('zooAcl', 0));
		
		$this->setParam('mainColor',    		$document->params->get('mainColor', ''));
		$this->setParam('secondColor',    		$document->params->get('secondColor', ''));
		$this->setParam('backgroundColor',    	$document->params->get('backgroundColor', ''));
		$this->setParam('linkColor',    		$document->params->get('linkColor', ''));
		
		for($x = 0; $x < 11; $x++)
        {
            $this->setParam('custom'.$x.'Acl', 	$document->params->get('custom'.$x.'Acl', 0));
            $this->setParam('custom'.$x.'Name',	$document->params->get('custom'.$x.'Name'));
            $this->setParam('custom'.$x.'Link',	$document->params->get('custom'.$x.'Link'));
        }
		
		if($this->get('mainColor') || $this->get('secondColor') || $this->get('backgroundColor')) :
			$customColors = 1;
		else :
			$customColors = 0;
		endif;
		$this->setParam('customColors', $customColors);
		

		
		$wideComponents = explode(',', $document->params->get('wideComponents'));
		$this->setParam('wideComponents',	$wideComponents);
		
		$showSidebar = $document->params->get('showSidebar', 0);
		
		if (in_array($this->get('option'), $wideComponents)) {
			$showSidebar = 0;
		} 
		else if(($this->get('task') =="edit") || ($this->get('task') =="add")){
			$showSidebar = 0;
		}
		if($this->get('option') =="com_cpanel" && !$this->get('ap_task_set')){
			$showSidebar = 0;
		}
		
		$this->setParam('showSidebar',	$showSidebar);
		
		if($this->get('option') == "com_projectfork"){
			$db =& JFactory::getDBO();
			$sql = 'SELECT name FROM #__pf_themes WHERE is_default=1';
			$db->setQuery($sql);
			$pfTheme = $db->loadResult(); 
		}else{
			$pfTheme = "";
		}
		
		$this->setParam('pfTheme',	$pfTheme);
		
		if(($pfTheme == "steel") && ($this->get('option') == "com_projectfork")){
			$steelActive = 1;
		} else {
			$steelActive = 0;
		}
		
		$this->setParam('steelActive',	$steelActive);
	}
	
	function checkLogin() {
		$mainframe = &JFactory::getApplication();
		DEFINE('GOTOSTARTPAGE_COOKIE', 'ap_gotostartpage');
		DEFINE('LOGINPAGELOCATION_COOKIE', 'ap_loginpagelocation');
		DEFINE('STARTPAGE_COOKIE', 'ap_startpage');

		$gotostartpage = @$_COOKIE[GOTOSTARTPAGE_COOKIE];

		if($gotostartpage)
		{
			setcookie(GOTOSTARTPAGE_COOKIE, 0);

			$uri = JFactory::getURI();
			$url = $uri->toString();
			$loginpagelocation = @$_COOKIE[LOGINPAGELOCATION_COOKIE];
			$loginpagelocationuri = new JURI($loginpagelocation);
			$query = $loginpagelocationuri->getQuery();
			if($query && strpos($query, 'com_login') === FALSE)
			{
				if($loginpagelocation && $url != $loginpagelocation)
				{
					$mainframe->redirect($loginpagelocation);
				}
			}
			else
			{
				$startpage = @$_COOKIE[STARTPAGE_COOKIE];
				if($startpage && $url != $startpage)
				{
					$mainframe->redirect($startpage);
				}
			}
		}
	}
	
	function fadeOutJS() {
		$js = "
		<script type='text/javascript'>
			window.addEvent('domready',function() {
			        var div = $('hiddenDiv').setStyles({
			                display:'block',
			                opacity: 1
			        }); 
			        
			        new Fx.Style('hiddenDiv', 'opacity', {duration:3000, onComplete:
					function() {
			        	new Fx.Style('hiddenDiv', 'opacity', {duration:3000}).start(0);
			        }}).start(1);
			}); 
		</script>
		";
		return $js;
	}
	
	function _generateHead() {
		$document = &JFactory::getDocument();
		$lnEnd	= $document->_getLineEnd();
		$tab	= $document->_getTab();
		$tagEnd	= ' />';
		$buffer	= '';
		
		// Generate base tag (need to happen first)
		$base = $document->getBase();
		if (!empty($base)) {
			$buffer .= $tab.'<base href="'.$document->getBase().'" />'.$lnEnd;
		}
		
		// Generate META tags (needs to happen as early as possible in the head)
		foreach ($document->_metaTags as $type => $tag)
		{
			foreach ($tag as $name => $content)
			{
				if ($type == 'http-equiv') {
					$buffer .= $tab.'<meta http-equiv="'.$name.'" content="'.$content.'"'.$tagEnd.$lnEnd;
				}
				else if ($type == 'standard') {
					$buffer .= $tab.'<meta name="'.$name.'" content="'.$content.'"'.$tagEnd.$lnEnd;
				}
			}
		}
		
		$buffer .= $tab.'<meta name="description" content="'.$document->getDescription().'" />'.$lnEnd;
		$buffer .= $tab.'<meta name="generator" content="'.$document->getGenerator().'" />'.$lnEnd;
		$buffer .= $tab.'<title>'.htmlspecialchars($document->getTitle()).'</title>'.$lnEnd;
		
		// Generate link declarations
		foreach ($document->_links as $link) {
			$buffer .= $tab.$link.$tagEnd.$lnEnd;
		}
		
		// Generate stylesheet links
		foreach ($document->_styleSheets as $strSrc => $strAttr)
		{
			$buffer .= $tab . '<link rel="stylesheet" href="'.$strSrc.'" type="'.$strAttr['mime'].'"';
			if (!is_null($strAttr['media'])){
				$buffer .= ' media="'.$strAttr['media'].'" ';
			}
			if ($temp = JArrayHelper::toString($strAttr['attribs'])) {
				$buffer .= ' '.$temp;;
			}
			$buffer .= $tagEnd.$lnEnd;
		}
		
		// Generate stylesheet declarations
		foreach ($document->_style as $type => $content)
		{
			$buffer .= $tab.'<style type="'.$type.'">'.$lnEnd;
		
			// This is for full XHTML support.
			if ($document->_mime == 'text/html') {
				$buffer .= $tab.$tab.'<!--'.$lnEnd;
			} else {
				$buffer .= $tab.$tab.'<![CDATA['.$lnEnd;
			}
		
			$buffer .= $content . $lnEnd;
		
			// See above note
			if ($document->_mime == 'text/html') {
				$buffer .= $tab.$tab.'-->'.$lnEnd;
			} else {
				$buffer .= $tab.$tab.']]>'.$lnEnd;
			}
			$buffer .= $tab.'</style>'.$lnEnd;
		}
		
		// Generate script file links
		$scriptbuffer = "\n";
		$moo = false;
		
		foreach ($document->_scripts as $strSrc => $strType) {
			
			$scriptbuffer .= '	<script type="'.$strType.'" src="'.$strSrc.'"></script>'.$lnEnd;
		}
		
		// Generate script declarations
		foreach ($document->_script as $type => $content)
		{
			$scriptbuffer .= '	<script type="'.$type.'">'.$lnEnd;
		
			// This is for full XHTML support.
			if ($document->_mime != 'text/html') {
				$scriptbuffer .= '		<![CDATA['.$lnEnd;
			}
		
			$scriptbuffer .= $content.$lnEnd;
		
			// See above note
			if ($document->_mime != 'text/html') {
				$scriptbuffer .= '		// ]]>'.$lnEnd;
			}
			$scriptbuffer .= '		</script>'.$lnEnd;
		}	
		
		foreach($document->_custom as $custom) {
			$buffer .= $tab.$custom.$lnEnd;
		}
		
		//Check if its safe to load scripts at the bottom or not
		$safe = ( in_array( JRequest::getCmd('option', 'com_login'), explode( ',', str_replace(' ', '', $document->params->get('safeComponents')) ) )&&$document->params->get('jsAtBottom', 0) ) ? true : false ;
		if(!$safe)
		{
			$buffer .= $scriptbuffer;
			$scriptbuffer = null;
		}
		
		$this->set('scriptSafe', $safe);
		$this->set('headBuffer', $buffer);
		$this->set('scriptBuffer', $scriptbuffer);
		
	}
	
	function generateStyles() {
		$output = '';
		
		if($this->get('fontSize')) 
			$output .= ".adminlist,.admintable,#component-list{font-size:".$this->get('fontSize').";}\n";
			
		if($this->get('minWidth'))
			$output .= "#minwidth-body{min-width: ".$this->get('minWidth').";}";
			
		if($this->get('sidebarWidth')) :
			$output .= "#ap-sidebar{width: ".($this->get('sidebarWidth')-2)."em;}\n";
			$output .= "#ap-mainbody .mr20, #ap-footer .mr20{margin-right: ".$this->get('sidebarWidth')."em;}";
		endif;
			
		if ($output)
			$output = "<style type=\"text/css\">\n".$output."\n</style>\n";
		
		return $output;
	
	}
	
	function generateLoginStyles() {
	
		$output = '';
		
		if($this->get('linkColor')) 
			$output .= "a,a:hover{color:".$this->get('linkColor')."\n";
			
		if($this->get('customColors') && $this->get('templateTheme') == "theme1") :
			$output .= "#login.theme1 h3{background-color:".$this->get('mainColor').";border-color:".$this->get('mainColor').";color:#FFF;}";
			$output .= "body#login.theme1{background-color:".$this->get('backgroundColor').";}";
			$output .= ".login{background-color:".$this->get('backgroundColor').";}";
		elseif($this->get('customColors') && $this->get('templateTheme') == "theme2") :
			$output .= "#login.theme2 h3{background-color:".$this->get('mainColor').";}";
			$output .= "body#login.theme2{background-color:".$this->get('backgroundColor').";}";
		elseif($this->get('customColors') && $this->get('templateTheme') == "theme3") :
			$output .= "#login.theme3 h3{background-color:".$this->get('mainColor').";}";
			$output .= "body#login.theme3{background-color:".$this->get('backgroundColor').";}";
			$output .= ".theme3 a{color:".$this->get('linkColor').";}";
		elseif($this->get('customColors') && $this->get('templateTheme') == "theme4") :
			$output .= "#login.theme4 h3{background-color:".$this->get('mainColor').";}";
			$output .= "body#login.theme5{background-color:".$this->get('backgroundColor').";}";
		elseif($this->get('customColors') && $this->get('templateTheme') == "theme5") :
			$output .= ".theme5 #ap-submenu,.theme5 #ap-mainmenu li.active a,.theme5 #ap-sidemenu li.active a,.theme5 #ap-mainmenu li a:hover,.theme5 #ap-sidemenu li a:hover,.theme5 .tool-title{background-color:".$this->get('mainColor').";}";
			$output .= ".theme5 .panel h3,#login.theme5 h3{background-color:".$this->get('secondColor').";}";
			$output .= "body#login.theme5{background-color:".$this->get('backgroundColor').";}";
		endif;
		
			
		if($this->get('sidebarWidth')) :
			$output .= "#ap-sidebar{width: ".($this->get('sidebarWidth')-2)."em;}\n";
			$output .= "#ap-mainbody .mr20, #ap-footer .mr20{margin-right: ".$this->get('sidebarWidth')."em;}";
		endif;
			
		if ($output)
			$output = "<style type=\"text/css\">\n".$output."\n</style>\n";
		
		return $output;
		
	}
	
	function renderTemplateModule($name) {
		$path = $this->_template_path.'/modules/'.$name;
		$file_path = $path.'/'.$name.'.php';
		$tmpl_path = $path.'/tmpl';
		
		if (!file_exists($file_path))
			return false;

		ob_start();
		include($file_path);
		$buffer = ob_get_contents();
		ob_end_clean();
		
		return $buffer;
	}
	
	function getBrowserAgent() {
		jimport('joomla.environment.browser');
		$browser = JBrowser::getInstance();
		
		$agent_string = $browser->getAgentString();
		
		if(stripos($agent_string,'firefox') !== false) :
			$agent = 'firefox';
		elseif(stripos($agent_string, 'chrome') !== false) :
			$agent = 'chrome';
		elseif(stripos($agent_string, 'msie 9') !== false) :
			$agent = 'ie9';
		elseif(stripos($agent_string, 'msie 8') !== false) :
			$agent = 'ie8';
		elseif(stripos($agent_string, 'msie 7') !== false) :
			$agent = 'ie7';
		elseif(stripos($agent_string, 'msie 6') !== false) :
			$agent = 'ie6';
		elseif(stripos($agent_string,'iphone') !== false || stripos($agent_string,'ipod') !== false) :
			$agent = 'iphone';
		elseif(stripos($agent_string,'ipad') !== false) :
			$agent = 'ipad';
		elseif(stripos($agent_string,'blackberry') !== false) :
			$agent = 'blackberry';
		elseif(stripos($agent_string,'palmos') !== false) :
			$agent = 'palm';
		elseif(stripos($agent_string,'android') !== false) :
			$agent = 'android';
		elseif(stripos($agent_string,'safari') !== false) :
			$agent = 'safari';
		elseif(stripos($agent_string, 'opera') !== false) :
			$agent = 'opera';
		else :
			$agent = null;
		endif;
	
		return $agent;
	}
	
}