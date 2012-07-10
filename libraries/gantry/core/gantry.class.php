 <?php
/**
 * @package   gantry
 * @subpackage core
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.gantrytemplate');
gantry_import('core.gantryini');
gantry_import('core.gantrypositions');
gantry_import('core.gantrystylelink');
gantry_import('core.gantryplatform');
gantry_import('core.gantrybrowser');



/**
 * This is the base class for the Gantry framework.   It is the primary mechanisim for template definition
 *
 * @package gantry
 * @subpackage core
 */
class Gantry {
    static $instances = array();

    public static function getInstance($template_name)
    {
        if (!array_key_exists($template_name, self::$instances)) {
            self::$instances[$template_name] = new Gantry($template_name);
        }
        return self::$instances[$template_name];
    }

	// Cacheable
    /**
     *
     */
	var $basePath;
	var $baseUrl;
    var $templateName;
	var $templateUrl;
	var $templatePath;
    var $templateId;
	var $layoutPath;
    var $gantryPath;
    var $gantryUrl;
    var $layoutSchemas = array();
    var $mainbodySchemas = array();
    var $pushPullSchemas = array();
    var $mainbodySchemasCombos = array();
    var $default_grid = 12;
	var $presets = array();
	var $originalPresets = array();
	var $customPresets = array();
    var $dontsetinoverride = array();
    var $defaultMenuItem;
    var $currentMenuItem;
    var $currentMenuTree;
    var $template_prefix;
    var $custom_dir;
    var $custom_presets_file;
    var $positions = array();
    var $altindex = false;
    var $platform;

    // Not cacheable
    var $document;
    var $browser;
    var $language;
    var $session;
    var $currentUrl;

    // Private Vars
	/**#@+
     * @access private
     */


    // cacheable privates
	var $_template;
	var $_aliases = array();
	var $_preset_names = array();
	var $_param_names = array();
    var $_base_params_checksum = null;
	var $_setbyurl = array();
	var $_setbycookie = array();
	var $_setbysession = array();
	var $_setinsession = array();
	var $_setincookie = array();
    var $_setinoverride = array();
    var $_setbyoverride = array();
	var $_features = array();
    var $_ajaxmodels = array();
    var $_adminajaxmodels = array();
    var $_layouts = array();
	var $_bodyclasses = array();
	var $_classesbytag = array();
    var $_ignoreQueryParams = array('reset-settings');
    var $_config_vars = array(
        'layoutschemas'=>'layoutSchemas',
        'mainbodyschemas'=>'mainbodySchemas',
        'mainbodyschemascombos' => 'mainbodySchemasCombos',
        'pushpullschemas'=>'pushPullSchemas',
        'presets'=>'presets',
        'browser_params' => '_browser_params',
        'grid'=>'grid'
    );
    var $_working_params;

    // non cachable privates
	var $_bodyId = null;
    var $_browser_params = array();
    var $_menu_item_params = array();
    var $_scripts = array();
    var $_styles = array();
	var $_styles_available = array();
    var $_tmp_vars = array();
    var $adminElements = array();
	var $_params_hash;
	var $_featuresPosition;
	var $_featuresInstances = array();
	var $_parts_cache = true;
	var $_parts_to_cache = array('_featuresPosition', '_styles_available');
	var $_parts_cached = false;
    var $_browser_hash;
    var $_domready_script = '';
    var $_loadevent_script = '';
    /**#@-*/

    var $__cacheables = array(
            'basePath',
            'baseUrl',
            'templateName',
            'templateUrl',
            'templatePath',
            'layoutPath',
            'gantryPath',
            'gantryUrl',
            'layoutSchemas',
            'mainbodySchemas',
            'pushPullSchemas',
            'mainbodySchemasCombos',
            'default_grid',
            'presets',
            'originalPresets',
            'customPresets',
            'dontsetinoverride',
            'defaultMenuItem',
            'currentMenuItem',
            'currentMenuTree',
            'template_prefix',
            'custom_dir',
            'custom_presets_file',
            'positions',
            '_template',
			'_aliases',
            '_preset_names',
            '_param_names',
            '_base_params_checksum',
            '_setbyurl',
            '_setbycookie',
            '_setbysession',
            '_setinsession',
            '_setincookie',
            '_setinoverride',
            '_setbyoverride',
            '_features',
            '_ajaxmodels',
            '_adminajaxmodels',
            '_layouts',
            '_bodyclasses',
            '_classesbytag',
            '_ignoreQueryParams',
            '_config_vars',
            '_working_params',
            'platform'
        );

    function __sleep() {
        return $this->__cacheables;
    }

    function __wakeup() {
        // set the GRID_SYSTEM define;
        if (!defined('GRID_SYSTEM')) {
            define ('GRID_SYSTEM',$this->get('grid_system',$this->default_grid));
        }
    }
    /**
     * Constructor
     * @return void
     */
	function Gantry($template_name = null) {
        // load the base gantry path
        $this->gantryPath = realpath(dirname( __FILE__ ).DS."..");

        // set the base class vars
		$doc =& JFactory::getDocument();
		$this->document =& $doc;


        $this->browser = new GantryBrowser();


        $this->platform = new GantryPlatform();

		$this->basePath = JPATH_ROOT;
        if ($template_name == null){
            $this->templateName = $this->_getCurrentTemplate();
        }
        else {
            $this->templateName = $template_name;
        }
        $this->templatePath = JPATH_ROOT.DS.'templates'.DS.$this->templateName;
        $this->layoutPath = $this->templatePath.DS.'html'.DS.'layouts.php';
        $this->custom_dir = $this->templatePath.DS.'custom';
        $this->custom_presets_file = $this->custom_dir.DS.'presets.ini';
        $this->baseUrl = JURI::root(true)."/";
        $this->templateUrl = $this->baseUrl.'templates'."/".$this->templateName;

        if (version_compare( JVERSION, '1.5', '>=') && version_compare(JVERSION, '1.6', '<')) {
            $this->gantryUrl = $this->baseUrl.'components/com_gantry';
        }
        else if (version_compare(JVERSION, '1.6', '>=')) {
            $this->gantryUrl = $this->baseUrl.'libraries/gantry';
        }

        $this->defaultMenuItem = $this->_getDefaultMenuItem();
        $this->currentMenuItem = $this->defaultMenuItem;
        $this->_loadConfig();



		// Load up the template details
		$this->_template = new GantryTemplate();
		$this->_template->init($this);
        $this->_base_params_checksum = $this->_template->getParamsHash();

        // Put a base copy of the saved params in the working params
		$this->_working_params = $this->_template->getParams();
		$this->_param_names = array_keys($this->_template->getParams());
        $this->template_prefix =  $this->_working_params['template_prefix']['value'];

		// set the GRID_SYSTEM define;
        if (!defined('GRID_SYSTEM')) {
            define ('GRID_SYSTEM',$this->get('grid_system',$this->default_grid));
        }

		// process the presets
        if (!empty($this->presets)) {
			// check for custom presets
			$this->_customPresets();

            $this->_preset_names = array_keys($this->presets);
            //$wp_keys = array_keys($this->_template->params);
            //$this->_param_names = array_diff($wp_keys, $this->_preset_names);
        }

        $this->_loadLayouts();
		$this->_loadFeatures();
        $this->_loadAjaxModels();
        $this->_loadAdminAjaxModels();
		$this->_loadStyles();

        //$this->_checkAjaxTool();

        //$this->_checkLanguageFiles();

        // set up the positions object for all gird systems defined
        foreach(array_keys($this->mainbodySchemasCombos) as $grid){
            $this->positions[$grid] = GantryPositions::getInstance($grid);
        }

		// add GRID_SYSTEM class to body
		$this->addBodyClass("col".GRID_SYSTEM);
	}



    function adminInit() {
        $this->browser = new GantryBrowser();
        $this->_browser_hash = md5(serialize($this->browser));
        $this->platform = new GantryPlatform();
        $doc =& JFactory::getDocument();
        $this->document =& $doc;
    }
    
    /**
     * Initializer.
     * This should run when gantry is run from the front end in order and before the template file to
     * populate all user session level data
     * @return void
     */
    function init() {
        if (defined('GANTRY_INIT')) {
            return;
        }
        // Run the admin init
        if ($this->isAdmin()) {
            $this->adminInit();
            return;
        }
        define('GANTRY_INIT', "GANTRY_INIT");

        $cache = GantryCache::getInstance();

        // set the GRID_SYSTEM define;
        if (!defined('GRID_SYSTEM')) {
            define ('GRID_SYSTEM',$this->get('grid_system',$this->default_grid));
        }

        // Set the main class vars to match the call
        JHTML::_('behavior.mootools');
        $doc =& JFactory::getDocument();
        $this->document =& $doc;
        $this->language = $doc->language;
        $this->session =& JFactory::getSession();
        $this->baseUrl = JURI::root(true) . "/";
        $uri = JURI::getInstance();
        $this->currentUrl = $uri->toString();
        $this->templateUrl = $this->baseUrl.'templates'."/".$this->templateName;
        if (version_compare( JVERSION, '1.5', '>=') && version_compare(JVERSION, '1.6', '<')) {
            $this->gantryUrl = $this->baseUrl.'components/com_gantry';
        }
        else if (version_compare(JVERSION, '1.6', '>=')) {
            $this->gantryUrl = $this->baseUrl.'libraries/gantry';
        }

        // use any menu item level overrides
        $menus = &JSite::getMenu();
        $menu  = $menus->getActive();
        $this->currentMenuItem = ($menu != null)?$menu->id : null;
        $this->currentMenuTree = ($menu != null)?$menu->tree: array();

        // Populate all the params for the session
        $this->_populateParams();

        $this->browser = new GantryBrowser();
        $this->_browser_hash = md5(serialize($this->browser));
		
        $this->platform = new GantryPlatform();

        $this->_loadBrowserConfig();

    }

    function initTemplate(){

        $cache = GantryCache::getInstance();

        // Init all features
        foreach($this->getFeatures() as $feature){
            $feature_instance = $this->_getFeature($feature);
            if ($feature_instance->isEnabled() && method_exists( $feature_instance , 'init')) {
                $feature_instance->init();
            }
        }

		if (false !== ($parts = $cache->get($this->cacheKey('parts')))) {
			$this->_parts_cached = true;
			
			foreach ($parts as $part => $value) {
				$this->$part = $value;
			}
		}

        //add default gantry stylesheet
		$this->addStyle('gantry.css', 5);
		//add correct grid system css
		$this->addStyle('grid-'.GRID_SYSTEM.'.css',5);
		$this->addStyle('joomla.css',5);
    }

    function adminFinalize()
    {
        ksort($this->_styles);
        foreach ($this->_styles as $priorities) {
            foreach ($priorities as $css_file) {
                $this->document->addStyleSheet($css_file->url);
            }
        }
        foreach ($this->_scripts as $js_file) {
            $this->document->addScript($js_file);
        }

        $this->renderCombinesInlines();

    }

    function renderCombinesInlines(){
        $lnEnd = "\12";
        $tab = "\11";
        $tagEnd = ' />';
        $strHtml = '';

        // Generate domready script
        if (isset($this->_domready_script) && count($this->_domready_script)) {
            $strHtml .= 'window.addEvent(\'domready\', function() {' . $this->_domready_script . $lnEnd . '});'. $lnEnd;
        }

        // Generate load script
        if (isset($this->_loadevent_script) && count($this->_loadevent_script)) {
            $strHtml .= 'window.addEvent(\'load\', function() {' . $this->_loadevent_script . $lnEnd . '});'. $lnEnd;
        }

        $this->document->addScriptDeclaration($strHtml);
    }

    function finalize()
    {
        if (!defined('GANTRY_FINALIZED')) {
            // Run the admin init
            if ($this->isAdmin()) {
                $this->adminFinalize();
                return;
            }

            gantry_import('core.params.overrides.gantrycookieparamoverride');
            gantry_import('core.params.overrides.gantrysessionparamoverride');

            $cache = GantryCache::getInstance();
            if (!$this->_parts_cached) {
                $parts_cache = array();
                foreach ($this->_parts_to_cache as $part) {
                    $parts_cache[$part] = $this->$part;
                }
                if ($parts_cache) {
                    $cache->set($this->cacheKey('parts'), $parts_cache);
                }
            }

            // Finalize all features
            foreach ($this->getFeatures() as $feature) {
                $feature_instance = $this->_getFeature($feature);
                if ($feature_instance->isEnabled() && method_exists($feature_instance , 'finalize')) {
                    $feature_instance->finalize();
                }
            }

            $this->renderCombinesInlines();

            if (isset($_REQUEST['reset-settings'])) {
                GantrySessionParamOverride::clean();
                GantryCookieParamOverride::clean();
            }
            else {
                GantrySessionParamOverride::store();
                GantryCookieParamOverride::store();
            }



            if ($this->get("gzipper-enabled",false)) {
                gantry_import('core.gantrygzipper');
                GantryGZipper::processCSSFiles();
                GantryGZipper::processJsFiles();
            }
            else {
                ksort($this->_styles);
                foreach($this->_styles as $priorities){
                    foreach($priorities as $css_file) {
                        $this->document->addStyleSheet($css_file->url);
                    }
                }
                foreach($this->_scripts as $js_file){
                    $this->document->addScript($js_file);
                }
            }
            define('GANTRY_FINALIZED', true);
        }
        if ($this->altindex !== false) {
            $contents = ob_get_contents();
            ob_end_clean();
            ob_start();
            echo $this->altindex;
        }
    }

    function isAdmin(){
        $app =& JFactory::getApplication();
        return $app->isAdmin();
    }

    function get($param = false, $default = "") {
		if (array_key_exists($param, $this->_working_params)) $value = $this->_working_params[$param]['value'];
		else $value = $default;
		return $value;
	}

	function getDefault($param = false) {
		$value = "";
		if (array_key_exists($param, $this->_working_params)) $value = $this->_working_params[$param]['default'];
		return $value;
	}

    function getFeatures(){
        return array_keys($this->_features);
    }

	function set($param, $value=false) {
		$return = false;
		if (array_key_exists($param, $this->_working_params)){
			$this->_working_params[$param]['value'] = $value;
			$return = true;
		}
		return $return;
	}

    function getAjaxModel($model_name, $admin=false){
        $model_path = false;
        if ($admin) {
            if (array_key_exists($model_name, $this->_adminajaxmodels)){
                $model_path = $this->_adminajaxmodels[$model_name];
            }
        }
        else {
            if (array_key_exists($model_name, $this->_ajaxmodels)){
                $model_path = $this->_ajaxmodels[$model_name];
            }
        }
        return $model_path;
    }


	function getPositions($position = null, $pattern = null) {
		if ($position != null) {
			$positions = $this->_template->parsePosition($position, $pattern);
			return $positions;
		}
		return $this->_template->getPositions();
	}

	function getUniquePositions() {
		return $this->_template->getUniquePositions();
	}

    function getPositionInfo($position_name) {
		return $this->_template->getPositionInfo($position_name);
	}

    function getAjaxUrl(){
        $url = $this->baseUrl;
        $component_path = 'index.php?option=com_gantry&task=ajax&format=raw&template='.$this->templateName;
        if ($this->isAdmin()){
            $url .= 'administrator/'.$component_path;
        }
        else{
            $url .= $component_path;
        }
        return $url;
    }

	function getParams($prefix=null,$remove_prefix=false) {
        if (null==$prefix){
		    return $this->_working_params;
        }
        $params=array();
        foreach ($this->_working_params as $param_name => $param_value){
            $matches = array();
            if (preg_match("/^".$prefix."-(.*)$/", $param_name, $matches)){
                if ($remove_prefix){
                    $param_name = $matches[1];
                }
                $params[$param_name] = $param_value;
            }
        }
        return $params;
	}

    /**
     * Gets the current URL and query string and can ready it for more query string vars
     * @param array $ignore
     * @param bool $qs_preped
     * @return mixed|string
     */
    function getCurrentUrl($ignore=array()){
        gantry_import('core.utilities.gantryurl');

        $url = GantryUrl::explode($this->currentUrl);

        if (!empty($ignore) && array_key_exists('query_params', $url)) {
            foreach ($ignore as $k) {
               if (array_key_exists($k, $url['query_params'])) unset($url['query_params'][$k]);
            }
        }
        return GantryUrl::implode($url);
    }

    function addQueryStringParams($url, $params = array()) {
        gantry_import('core.utilities.gantryurl');
        return GantryUrl::updateParams($url, $params);
    }

    /**
     * @param  $positionStub
     * @param  $pattern
     * @return int
     */
    function countModules($positionStub, $pattern = null)
    {
        if (defined('GANTRY_FINALIZED')) return 0;
        $count = 0;

        if (array_key_exists($positionStub, $this->_aliases)) {
            return $this->countModules($this->_aliases[$positionStub]);
        }

        $positions = $this->getPositions($positionStub, $pattern);

        foreach ($positions as $position) {
            if (!$this->isAdmin()) {
                if ($this->document->countModules($position) || count($this->_getFeaturesForPosition($position)) > 0) $count++;
            }
            else {
                if ($this->_adminCountModules($position) || count($this->_getFeaturesForPosition($position)) > 0) $count++;
            }
        }
        return $count;
    }

    /**
     * @param  $positionStub
     * @param  $pattern
     * @return int
     */
    function countSubPositionModules($position, $pattern = null)
    {
        if (defined('GANTRY_FINALIZED')) return 0;

        $count = 0;

        if (array_key_exists($position, $this->_aliases)) {
            return $this->countSubPositionModules($this->_aliases[$position]);
        }

        if (!$this->isAdmin()) {
            if ($this->document->countModules($position) || count($this->_getFeaturesForPosition($position)) > 0)
            {
                $count += $this->document->countModules($position);
                $count += count($this->_getFeaturesForPosition($position));
            }
        }
        else {
            if ($this->_adminCountModules($position) || count($this->_getFeaturesForPosition($position)) > 0)
            {
                $count += $this->_adminCountModules($position);
                $count += count($this->_getFeaturesForPosition($position));
            }
        }
        return $count;
    }

	// wrapper for mainbody display
    function displayMainbody($bodyLayout = 'mainbody', $sidebarLayout = 'sidebar', $sidebarChrome = 'standard', $contentTopLayout = 'standard', $contentTopChrome = 'standard', $contentBottomLayout = 'standard', $contentBottomChrome = 'standard', $gridsize = null) {
        if (defined('GANTRY_FINALIZED')) return;
        gantry_import('core.renderers.gantrymainbodyrenderer');
        return GantryMainBodyRenderer::display($bodyLayout, $sidebarLayout, $sidebarChrome, $contentTopLayout, $contentTopChrome, $contentBottomLayout, $contentBottomChrome, $gridsize);
    }

    // wrapper for mainbody display
    function displayOrderedMainbody($bodyLayout = 'mainbody', $sidebarLayout = 'sidebar', $sidebarChrome = 'standard', $contentTopLayout = 'standard', $contentTopChrome = 'standard', $contentBottomLayout = 'standard', $contentBottomChrome = 'standard', $gridsize = null) {
        if (defined('GANTRY_FINALIZED')) return;
        gantry_import('core.renderers.gantryorderedmainbodyrenderer');
        return GantryOrderedMainBodyRenderer::display($bodyLayout, $sidebarLayout, $sidebarChrome, $contentTopLayout, $contentTopChrome, $contentBottomLayout, $contentBottomChrome, $gridsize);
    }

    // wrapper for display modules
    function displayModules($positionStub, $layout = 'standard', $chrome = 'standard', $gridsize = GRID_SYSTEM, $pattern = null) {
        if (defined('GANTRY_FINALIZED')) return;
        gantry_import('core.renderers.gantrymodulesrenderer');
        return GantryModulesRenderer::display($positionStub, $layout, $chrome, $gridsize, $pattern);
    }
        // wrapper for display modules
    function displayFeature($feature, $layout = 'basic') {
        if (defined('GANTRY_FINALIZED')) return;
        gantry_import('core.renderers.gantryfeaturerenderer');
        return GantryFeatureRenderer::display($feature, $layout);
    }


    function addTemp($namespace, $varname, &$variable) {
        if (defined('GANTRY_FINALIZED')) return;
        $this->_tmp_vars[$namespace][$varname] = $variable;
        return;
    }

    function &retrieveTemp($namespace, $varname, $default = null){
        if (defined('GANTRY_FINALIZED')) return;
        if (!array_key_exists($namespace,$this->_tmp_vars) ||!array_key_exists($varname, $this->_tmp_vars[$namespace])){
            return $default;
        }
        return  $this->_tmp_vars[$namespace][$varname];
    }

    function setBodyId($id = null){
    	$this->_bodyId = $id;
    }

    function addBodyClass($class) {
        if (defined('GANTRY_FINALIZED')) return;
    	$this->_bodyclasses[] = $class;
    }

    function addClassByTag($id , $class) {
        if (defined('GANTRY_FINALIZED')) return;
    	$this->_classesbytag[$id][] = $class;
    }

    function displayHead() {
        if (defined('GANTRY_FINALIZED')) return;
        //stuff to output that is needed by joomla
        echo '<jdoc:include type="head" />';
    }

    function displayBodyTag() {
        if (defined('GANTRY_FINALIZED')) return;
        $body_classes = array();
        foreach ($this->_bodyclasses as $param) {
        	$param_value = $this->get($param);
        	if ($param_value != "") {
            	$body_classes[] = strtolower(str_replace(" ","-",$param ."-".$param_value));
            } else {
            	$body_classes[] = strtolower(str_replace(" ","-",$param));
            }
        }
        return $this->renderLayout('doc_body', array('classes'=>implode(" ", $body_classes),'id'=>$this->_bodyId));
    }

    function displayClassesByTag($tag) {
        if (defined('GANTRY_FINALIZED')) return;
        $tag_classes = array();

        $output = "";

        if (array_key_exists($tag,$this->_classesbytag)) {
            foreach ($this->_classesbytag[$tag] as $param) {
                $param_value = $this->get($param);
                if ($param_value != "") {
                    $tag_classes[] = $param ."-".$param_value;
                } else {
                    $tag_classes[] = $param;
                }


            }
            $output = 'class="'.implode(" ", $tag_classes).'"';

        }
        return $this->renderLayout('doc_tag', array('classes'=>implode(" ", $tag_classes)));
    }

    // debug function for body
    function debugMainbody($bodyLayout = 'debugmainbody', $sidebarLayout = 'sidebar', $sidebarChrome = 'standard', $grid = null) {
        gantry_import('core.renderers.gantrydebugmainbodyrenderer');
        return GantryDebugMainBodyRenderer::display($bodyLayout, $sidebarLayout, $sidebarChrome, $grid);
    }

    	/* ------ Stylesheet Funcitons  ----------- */

    function addStyle($file = '', $priority=10, $template_files_override = false) {
        if (is_array($file)) return $this->addStyles($file, $priority);
        $type = 'css';
		

		$template_path = $this->templatePath.DS .$type.DS;
		$template_url = $this->templateUrl.'/css/';
		$gantry_path = $this->gantryPath.DS.$type.DS;
		$gantry_url = $this->gantryUrl.'/css/';

		$gantry_first_paths = array(
			$gantry_url => $gantry_path,
			$template_url => $template_path
		);

        $out_files = array();
        $ext = substr($file, strrpos($file, '.'));
        $filename = basename($file, $ext);
        $base_file = basename($file);
        $override_file = $filename . "-override" . $ext;

        // get browser checks and remove base files
        $checks = $this->_getBrowserBasedChecks(basename($file));
        unset($checks[array_search($base_file,$checks)]);
        
        $override_checks = $this->_getBrowserBasedChecks(basename($override_file));
        unset($override_checks[array_search($override_file,$override_checks)]);

        // check to see if this is a full path file
        $dir = dirname($file);
        if ($dir != ".") {
            // Add full url directly to document
            if (preg_match('/^http/', $file)) {
                $link = new GantryStyleLink('url','',$file);
                $this->_styles[$priority][]=$link;
                return;
            }

            // process a url passed file and browser checks   
            $url_path = $dir;
            $file_path = $this->_getFilePath($file);
            $file_parent_path = dirname($file_path);

            if (file_exists($file_parent_path) && is_dir($file_parent_path)) {
                $base_path = preg_replace("/\?(.*)/", '', $file_parent_path.DS.$base_file);
                // load the base file
                if (file_exists($base_path) && is_file($base_path) && is_readable($base_path)){
                   $out_files[$base_path] = new GantryStyleLink('local',$base_path, $file);
                }
                foreach ($checks as $check) {
                    $check_path = preg_replace("/\?(.*)/", '', $file_parent_path . DS . $check);
                    $check_url_path = $url_path . "/" . $check;
                    if (file_exists($check_path) && is_readable($check_path)) {
                        $out_files[$check] = new GantryStyleLink('local',$check_path, $check_url_path);
                    }
                }
            }
        }
        else {
            $base_override = false;
            $checks_override = array();

            // Look for an base override file in the template dir
            $template_base_override_file = $template_path.$override_file;
            if ($this->isStyleAvailable($template_base_override_file)) {
                $out_files[$template_base_override_file] = new GantryStyleLink('local',$template_base_override_file, $template_url.$override_file);
                $base_override = true;
            }

            // look for overrides for each of the browser checks
            foreach($override_checks as $check_index => $override_check) {
                $template_check_override = preg_replace("/\?(.*)/", '', $template_path.$override_check);
                $checks_override[$check_index] = false;  
                if ($this->isStyleAvailable($template_check_override)){
                    $checks_override[$check_index] = true;
                    if ($base_override){
                         $out_files[$template_check_override] = new GantryStyleLink('local',$template_check_override,$template_url.$override_check);
                    }
                }
            }

            if (!$base_override){
                // Add the base files if there is no  base -override
                foreach ($gantry_first_paths as $base_url => $path) {
                    // Add the base file
                    $base_path = preg_replace("/\?(.*)/", '', $path.$base_file);
                    // load the base file
                    if ($this->isStyleAvailable($base_path)){
                       $outfile_key = ($template_files_override)? $base_file : $base_path;
                       $out_files[$outfile_key] = new GantryStyleLink('local',$base_path,$base_url.$base_file);
                    }
                    
                    // Add the browser checked files or its override
                    foreach($checks as $check_index => $check) {
                        // replace $check with the override if it exists
                        if ($checks_override[$check_index]){
                            $check = $override_checks[$check_index];
                        }

                        $check_path = preg_replace("/\?(.*)/", '', $path.$check);

                        if ($this->isStyleAvailable($check_path)){
                            $outfile_key = ($template_files_override)? $check : $check_path;
                            $out_files[$outfile_key] = new GantryStyleLink('local',$check_path,$base_url.$check);
                        }
                    }
                }
            }
        }

        foreach ($out_files as $link) {
            $addit = true;
            foreach($this->_styles as $style_priority => $priority_links){
                $index = array_search($link, $priority_links);
                if ($index !== false){
                    if ($priority < $style_priority){
                        unset($this->_styles[$style_priority][$index]);
                    }
                    else {
                        $addit = false;
                    }
                }
            }
            if ($addit) {
                 if(!defined('GANTRY_FINALIZED')){


                    $this->_styles[$priority][] = $link;
                 }
                 else{
                     $this->document->addStyleSheet($link->url);
                 }
            }
        }

        //clean up styles
        foreach($this->_styles as $style_priority => $priority_links){
            if (count($priority_links) == 0){
                unset($this->_styles[$style_priority]);
            }
        }
    }

    function isStyleAvailable($path){
        if (isset($this->_styles_available[$path])){
            return true;
        }
        else if (file_exists($path) && is_file($path)){
            $this->_styles_available[$path] = $path;
            return true;
        }
        return false;
    }

	function addStyles($styles = array(),$priority=10) {
        if (defined('GANTRY_FINALIZED')) return;
		foreach($styles as $style) $this->addStyle($style, $priority);
	}

	function addInlineStyle($css = '') {
        if (defined('GANTRY_FINALIZED')) return;
		return $this->document->addStyleDeclaration($css);
	}

	function addScript($file = '') {

		if (is_array($file)) return $this->addScripts($file);
        $type = 'js';


        // check to see if this is a full path file
        $dir = dirname($file);
        if ($dir != ".") {
            // For remote url just add the url
            if (preg_match('/^http/',$file)){
                 $this->document->addScript($file);
                return;
            }

            // For local url path get the local path based on checks
            $url_path = $dir;
            $file_path = $this->_getFilePath($file);
            $url_file_checks = $this->platform->getJSChecks($file_path, true);
            foreach ($url_file_checks as $url_file){
                $full_path = realpath($url_file);
                if ($full_path !== false && file_exists($full_path)){
                    $check_url_path = $url_path.'/'.basename($url_file);
                    $this->_scripts[$full_path] = $check_url_path;
                    break;
                }
            }
            return;
        }

        $out_files = array();

        $paths = array(
           $this->templateUrl => $this->templatePath.DS.$type,
           $this->gantryUrl => $this->gantryPath.DS.$type
        );

		$checks = $this->platform->getJSChecks($file);
        foreach($paths as  $baseurl => $path){
            if (file_exists($path) && is_dir($path)){
                foreach($checks  as $check) {
                    $check_path = preg_replace("/\?(.*)/",'',$path.DS.$check);
                    $check_url_path = $baseurl ."/".$type."/".$check;
                    if (file_exists($check_path) && is_readable($check_path)){
                        if(!defined('GANTRY_FINALIZED'))
                            $this->_scripts[$check_path] = $check_url_path;
                        else
                            $this->document->addScript($check_url_path);
                        break(2);
                    }
                }
            }
        }
	}



	function addScripts($scripts = array()) {
        if (defined('GANTRY_FINALIZED')) return;
		foreach($scripts as $script) $this->addScript($script);
	}

	function addInlineScript($js = '') {
        if (defined('GANTRY_FINALIZED')) return;
		return $this->document->addScriptDeclaration($js);
		}

    function addDomReadyScript($js = '') {
		if (defined('GANTRY_FINALIZED')) return;
        if (!isset($this->_domready_script)) {
			$this->_domready_script = $js;
		} else {
			$this->_domready_script .= chr(13).$js;
		}
    }

    function addLoadScript($js = '') {
		if (defined('GANTRY_FINALIZED')) return;
        if (!isset($this->_loadevent_script)) {
			$this->_loadevent_script = $js;
		} else {
			$this->_loadevent_script .= chr(13).$js;
		}
    }

    function repopulateParams(){
        if ($this->isAdmin()){
            // get a copy of the params for working with on this call
		    $this->_working_params = $this->_template->getParams();
            gantry_import('core.params.overrides.gantrymenuitemparams');
            GantryMenuItemParams::populate();
        }
    }

    /**
     * @param string $layout the layout name to render
     * @param array $params all parameters needed for rendering the layout as an associative array with 'parameter name' => parameter_value
     * @return void
     */
    function renderLayout($layout_name, $params=array()){
        $layout = $this->_getLayout($layout_name);
        if ($layout === false){
            return "<!-- Unable to render layout... can not find layout class for " . $layout_name . " -->";
        }
        return $layout->render($params);
    }


    /**#@+
     * @access private
     */

    /**
     * @param  $url
     * @return string
     */
    function _getFilePath($url) {
        $uri	    =& JURI::getInstance();
		$base	    = $uri->toString( array('scheme', 'host', 'port'));
        $path       = JURI::Root(true);
	    if ($url && $base && strpos($url,$base)!==false) $url = preg_replace('|^'.$base.'|',"",$url);
	    if ($url && $path && strpos($url,$path)!==false) $url = preg_replace('|^'.$path.'|',"",$url);
	    if (substr($url,0,1) != DS) $url = DS.$url;
	    $filepath = JPATH_SITE.$url;
	    return $filepath;
	}

    /**
     * internal util function to get key from schema array
     * @param  $schemaArray
     * @return #Fimplode|?
     */
    function _getKey($schemaArray) {

        $concatArray = array();

        foreach ($schemaArray as $key=>$value) {
            $concatArray[] = $key . $value;
        }

        return (implode("-",$concatArray));
    }


    /**
     * @return #M#Vdb.loadResult|#P#Vdefault_item.id|int|?
     */
    function _getDefaultMenuItem(){
        if (!$this->isAdmin()){
            $menu   =& JSite::getMenu();
            $default_item = $menu->getDefault();
            return $default_item->id;
        }
        else
        {
            $db		=& JFactory::getDBO();
            $default = 0;
            $query = 'SELECT id'
                . ' FROM #__menu AS m'
                . ' WHERE m.home = 1';

            $db->setQuery( $query );
            $default = $db->loadResult();
            return $default;
        }
    }

    /**
     * @return void
     */
    function _loadConfig() {
        // Process the config
        $default_config_file = $this->gantryPath.DS.'gantry.config.php';
        if (file_exists($default_config_file) && is_readable($default_config_file)){
             include_once($default_config_file);
        }

        $template_config_file = $this->templatePath.DS.'gantry.config.php';
        if (file_exists($template_config_file   ) && is_readable($template_config_file)){
            /** @define "$template_config_file" "VALUE" */
            include_once($template_config_file);
        }

        if (isset($gantry_default_config_mapping)) {
           $temp_array = array_merge($this->_config_vars, $gantry_default_config_mapping);
           $this->_config_vars = $temp_array;
        }
        if (isset($gantry_config_mapping)){
           $temp_array = array_merge($this->_config_vars, $gantry_config_mapping);
           $this->_config_vars = $temp_array;
        }

        foreach($this->_config_vars as $config_var_name =>$class_var_name){
            $default_config_var_name = 'gantry_default_'.$config_var_name;
            if (isset($$default_config_var_name)){
                $this->$class_var_name = $$default_config_var_name;
                $this->__cacheables[] = $class_var_name;
            }
            $template_config_var_name = 'gantry_'.$config_var_name;
            if (isset($$template_config_var_name)){
                $this->$class_var_name = $$template_config_var_name;
                $this->__cacheables[] = $class_var_name;
            }
        }
    }

    /**
     * @return void
     */
    function _loadBrowserConfig() {

        $checks = array(
			$this->browser->name,
			$this->browser->platform,
			$this->browser->name . '_' . $this->browser->platform,
			$this->browser->name . $this->browser->shortversion,
			$this->browser->name . $this->browser->version,
			$this->browser->name . $this->browser->shortversion . '_' . $this->browser->platform,
			$this->browser->name . $this->browser->version . '_' . $this->browser->platform
		);


        foreach($checks as $check){
            if (array_key_exists($check, $this->_browser_params)){
                foreach($this->_browser_params[$check] as $param_name => $param_value) {
                    $this->set($param_name, $param_value);
                }
            }
        }
    }


    /**
     * @return void
     */
	function _customPresets() {
		$this->originalPresets = $this->presets;
		if (file_exists($this->custom_presets_file)) {

			$customPresets = GantryINI::read($this->custom_presets_file);
			$this->customPresets = $customPresets;
			$this->originalPresets = $this->presets;
			if (count($customPresets)) {
				$this->presets = $this->_array_merge_replace_recursive($this->presets, $customPresets);
				foreach($this->presets as $key => $preset) {
					uksort($preset, array($this, "_compareKeys"));
					$this->presets[$key] = $preset;
				}
			}

		}
	}

    /**
     * @param  $key1
     * @param  $key2
     * @return int
     */
	function _compareKeys($key1, $key2) {
		if (strlen($key1) < strlen($key2)) return -1;
		else if (strlen($key1) > strlen($key2)) return 1;
		else {
			if ($key1 < $key2) return -1;
			else return 1;
		}
	}

    /**
     * @param  $name
     * @param  $preset
     * @return array
     */
	function _getPresetParams($name,$preset){
		$return_params = array();
        if (array_key_exists($preset,$this->presets[$name])){
		    $preset_params = $this->presets[$name][$preset];
            foreach ($preset_params as $preset_param_name => $preset_param_value) {
                if (array_key_exists($preset_param_name, $this->_working_params) && $this->_working_params[$preset_param_name]['type'] == 'preset') {
                    $return_params = $this->_getPresetParams($preset_param_name,$preset_param_value);
                }
            }
            foreach ($preset_params as $preset_param_name => $preset_param_value) {
                if (array_key_exists($preset_param_name, $this->_working_params) && $this->_working_params[$preset_param_name]['type'] != 'preset') {
                    $return_params[$preset_param_name] = $preset_param_value;
                }
            }
        }
		return $return_params;
	}

    /**
     * @return void
     */
	function _populateParams(){
        gantry_import('core.params.overrides.gantryurlparamoverride');
        gantry_import('core.params.overrides.gantrysessionparamoverride');
        gantry_import('core.params.overrides.gantrycookieparamoverride');
        gantry_import('core.params.overrides.gantrymenuitemparamoverride');

        // get a copy of the params for working with on this call
		$this->_working_params = $this->_template->getParams();

        if (!isset($_REQUEST['reset-settings'])){
            GantrySessionParamOverride::populate();
            GantryCookieParamOverride::populate();
        }

        GantryMenuItemParamOverride::populate();

        if (!isset($_REQUEST['reset-settings'])){
            GantryUrlParamOverride::populate();
        }
		
		$this->_params_hash = md5(serialize($this->_working_params));
	}

	/**
     * @param  $position
     * @return array
     */
    function _getFeaturesForPosition($position) {
	
		if (isset($this->_featuresPosition[$this->cacheKey($position, true)])) {
			return $this->_featuresPosition[$this->cacheKey($position, true)];
		}

   		$return = array();
   		// Init all features
		foreach($this->getFeatures() as $feature){
            $feature_instance = $this->_getFeature($feature);
			if ($feature_instance->isEnabled() && $feature_instance->isInPosition($position) && method_exists( $feature_instance , 'render')) {
				$return[] = $feature;
			}
		}		
		return $this->_featuresPosition[$this->cacheKey($position, true)] = $return;
    }

    /**
     * internal util to get short name from long name
     * @param  $longname
     * @return string
     */
    function _getShortName($longname) {
        $shortname = $longname;
        if (strlen($longname)>2) {
            $shortname = substr($longname,0,1) . substr($longname,-1);
        }
        return $shortname;
    }

    /**
     * internal util to get long name from short name
     * @param  $shortname
     * @return string
     */
    function _getLongName($shortname) {
        $longname = $shortname;
        switch (substr($shortname,0,1)) {
            case "s":
            default:
                $longname = "sidebar";
                break;
        }
        $longname .= "-".substr($shortname,-1);
        return $longname;
    }


    /**
     * internal util to retrieve the prefix of a position
     * @param  $position
     * @return #Fsubstr|?
     */
	function _getPositionPrefix($position) {
		return substr($position, 0, strrpos($position, "-"));
	}

	/**
     * internal util to retrieve the stored position schema
     * @param  $position
     * @param  $gridsize
     * @param  $count
     * @param  $index
     * @return #P#CGantry.layoutSchemas|boolean|?
     */
	function _getPositionSchema($position, $gridsize, $count, $index) {
		$param = $this->_getPositionPrefix($position) . '-layout';
        $defaultSchema = false;

		$storedParam = $this->get($param);
		if (!preg_match("/{/", $storedParam)) $storedParam = '';
		$setting = unserialize($storedParam);

 		$schema =& $setting[$gridsize][$count][$index];
		if ($this->document->direction == 'rtl' && $this->get('rtl-enabled')) {
			$layout = array_reverse($setting[$gridsize][$count]);
			$schema =& $layout[$index];
		}
 		if (isset($schema))
            return $schema;
		else {
            if (count($this->layoutSchemas[$gridsize]) < $count){
                $count = count($this->layoutSchemas[$gridsize]);
            }
            for ($i=$count;$i>0;$i--) {
				$layout = $this->layoutSchemas[$gridsize][$i];
				if ($this->document->direction == 'rtl' && $this->get('rtl-enabled')) {
					$layout = array_reverse($layout);
				}
                if (isset($layout[$index])) {
                    $defaultSchema = $layout[$index];
                    break;
                }
            }
            return $defaultSchema;
        }
	}


    /**
     * @param  $file
     * @return
     */
    function _getBrowserBasedChecks($file, $keep_path=false) {
        $ext = substr($file, strrpos($file, '.'));
        $path = ($keep_path)?dirname($file).DS:'';
        $filename = basename($file, $ext);

        $checks = $this->browser->getChecks($file, $keep_path);

        // check if RTL version needed
        $document =& $this->document;
        if ($document->direction == 'rtl' && $this->get('rtl-enabled')) {
            $checks[] = $path.$filename . '-rtl'.$ext;
        }
        return $checks;
    }

    /**
     * @return
     */
    function _getCurrentTemplate() {
        $session =& JFactory::getSession();
       	if (!$this->isAdmin()) {
            $app = &JApplication::getInstance('site', array(), 'J');
			$template = $app->getTemplate();
		}
		else {
            if (array_key_exists('cid',$_REQUEST)){
			    $template = $_REQUEST['cid'][0];
            }
            else {
                $template = $session->get('gantry-current-template');
                }
            }
        $session->set('gantry-current-template', $template);
        return $template;
    }

    /**
     * @param  $condition
     * @return
     */
	function _adminCountModules($condition)
	{
		$result = '';

		$words = explode(' ', $condition);
		for($i = 0; $i < count($words); $i+=2)
		{
			// odd parts (modules)
			$name		= strtolower($words[$i]);
			$words[$i]	= ((isset($this->_buffer['modules'][$name])) && ($this->_buffer['modules'][$name] === false)) ? 0 : count($this->_getModulesFromAdmin($name));
		}
		$str = 'return '.implode(' ', $words).';';
		return eval($str);
	}

	/**
	 * Get modules by position
	 *
	 * @param string 	$position	The position of the module
	 * @return array	An array of module objects
	 */
	function &_getModulesFromAdmin($position)
	{
		$position	= strtolower( $position );
		$result		= array();

		$modules = $this->_loadModulesFromAdmin();

		$total = count($modules);
		for($i = 0; $i < $total; $i++) {
			if($modules[$i]->position == $position) {
				$result[] =& $modules[$i];
			}
		}
		return $result;
	}

    /**
	 * Load published modules
	 *
	 * @return	array
	 */
	function &_loadModulesFromAdmin()
	{
		static $clean;

		if (isset($clean)) {
			return $clean;
		}

        $db	= JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('a.id');
		$query->from('#__menu AS a');
        $query->where('a.home = 1');
        $query->where('a.client_id = 0');
        $db->setQuery($query);

		$Itemid 	= (int)$db->loadResult();
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser(0);
		$groups		= implode(',', $user->getAuthorisedViewLevels());
		$lang 		= JFactory::getLanguage()->getTag();
		$clientId 	= 0;

		$cache 		= JFactory::getCache ('com_modules', '');
		$cacheid 	= md5(serialize(array($Itemid, $groups, $clientId, $lang)));

		if (!($clean = $cache->get($cacheid))) {


			$query = $db->getQuery(true);
			$query->select('id, title, module, position, content, showtitle, params, mm.menuid');
			$query->from('#__modules AS m');
			$query->join('LEFT','#__modules_menu AS mm ON mm.moduleid = m.id');
			$query->where('m.published = 1');

			$date = JFactory::getDate();
			$now = $date->toMySQL();
			$nullDate = $db->getNullDate();
			$query->where('(m.publish_up = '.$db->Quote($nullDate).' OR m.publish_up <= '.$db->Quote($now).')');
			$query->where('(m.publish_down = '.$db->Quote($nullDate).' OR m.publish_down >= '.$db->Quote($now).')');

			$query->where('m.access IN ('.$groups.')');
			$query->where('m.client_id = 0');
			$query->where('(mm.menuid = '. (int) $Itemid . ' OR mm.menuid <=0)');

			// Filter by language
			if ($app->isSite() && $app->getLanguageFilter()) {
				$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
			}

			$query->order('position, ordering');

			// Set the query
			$db->setQuery($query);
			if (!($modules = $db->loadObjectList())) {
				JError::raiseWarning(500, JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
				return false;
			}

			// Apply negative selections and eliminate duplicates
			$negId	= $Itemid ? -(int)$Itemid : false;
			$dupes	= array();
			$clean	= array();
			for ($i = 0, $n = count($modules); $i < $n; $i++)
			{
				$module = &$modules[$i];

				// The module is excluded if there is an explicit prohibition, or if
				// the Itemid is missing or zero and the module is in exclude mode.
				$negHit	= ($negId === (int) $module->menuid)
						|| (!$negId && (int)$module->menuid < 0);

				if (isset($dupes[$module->id]))
				{
					// If this item has been excluded, keep the duplicate flag set,
					// but remove any item from the cleaned array.
					if ($negHit) {
						unset($clean[$module->id]);
					}
					continue;
				}
				$dupes[$module->id] = true;

				// Only accept modules without explicit exclusions.
				if (!$negHit)
				{
					//determine if this is a custom module
					$file				= $module->module;
					$custom				= substr($file, 0, 4) == 'mod_' ?  0 : 1;
					$module->user		= $custom;
					// Custom module name is given by the title field, otherwise strip off "com_"
					$module->name		= $custom ? $module->title : substr($file, 4);
					$module->style		= null;
					$module->position	= strtolower($module->position);
					$clean[$module->id]	= $module;
				}
			}
			unset($dupes);
			// Return to simple indexing that matches the query order.
			$clean = array_values($clean);

			$cache->store($clean, $cacheid);
		}

		return $clean;
	}
	
	function _loadStyles() {
	
		$type = 'css';
		$template_path = $this->templatePath.DS .$type.DS;
		$gantry_path = $this->gantryPath.DS.$type.DS;

		$gantry_first_paths = array(
			$gantry_path,
			$template_path
		);
		
		if (empty($this->_styles_available)) {
			$raw_styles = array();
			foreach($gantry_first_paths as  $style_path){
				if (file_exists($style_path) && is_dir($style_path)){
					$d = dir($style_path);
					while (false !== ($entry = $d->read())) {
						if($entry != '.' && $entry != '..'){
						
							if (!isset($raw_styles[$style_path])) {
								$raw_styles[$style_path.$entry] = $style_path.$entry;
							}
						}
					}
					$d->close();
				}
			}
			
			$this->_styles_available = $raw_styles;
		}
	}


    protected function _loadFeatures(){
         $features_paths = array(
            $this->templatePath.DS.'features',
            $this->gantryPath.DS.'features'
         );

        $raw_features = array();
        foreach($features_paths as  $feature_path){
            if (file_exists($feature_path) && is_dir($feature_path)){
                $d = dir($feature_path);
                while (false !== ($entry = $d->read())) {
                    if($entry != '.' && $entry != '..'){
                        $feature_name = basename($entry, ".php");
                        $path	= $feature_path.DS.$feature_name.'.php';
                        $className = 'GantryFeature'.ucfirst($feature_name);
                        if (!class_exists($className)) {
                            if (file_exists( $path ))
                            {
                                require_once( $path );
                                if(class_exists($className))
                                {
                                    $raw_features[$this->get($feature_name."-priority",10)][] = $feature_name;
                                }
                            }

                        }
                    }
                }
                $d->close();
            }
        }

        ksort($raw_features);
        foreach($raw_features as $features){
            foreach ($features as $feature){
                if (!in_array($feature,  $this->_features)){
                    $this->_features[$feature] = $feature;
                }
            }
        }
    }

    /**
     * @return void
     */
    function _loadAjaxModels(){
         $models_paths = array(
            $this->templatePath.DS.'ajax-models',
            $this->gantryPath.DS.'ajax-models'
         );
        $this->_loadModels($models_paths, $this->_ajaxmodels);
        return;
    }

    function _loadAdminAjaxModels(){
         $models_paths = array(
            $this->templatePath.DS.'admin'.DS.'ajax-models',
            $this->gantryPath.DS.'admin'.DS.'ajax-models'
         );
        $this->_loadModels($models_paths, $this->_adminajaxmodels);
        return;
    }

    function _loadModels($paths, &$results){
        $raw_models = array();
        foreach($paths as  $model_path){
            if (file_exists($model_path) && is_dir($model_path)){
                $d = dir($model_path);
                while (false !== ($entry = $d->read())) {
                    if($entry != '.' && $entry != '..'){
                        $model_name = basename($entry, ".php");
                        $path	= $model_path.DS.$model_name.'.php';
                        if (file_exists( $path ) && !array_key_exists($model_name, $results))
                        {
                            $results[$model_name] = $path;
                        }
                    }
                }
                $d->close();
            }
        }
    }

    /**
     * @param  $feature_name
     * @return boolean
     */
    function _getFeature($feature_name){
	
		if (isset($this->_featuresInstances[$feature_name]))
			return $this->_featuresInstances[$feature_name];
			
        $className = 'GantryFeature'.ucfirst($feature_name);

        if (!class_exists($className, false)){
            $this->_loadFeatures();
        }

        if (class_exists($className, false))
        {
            return $this->_featuresInstances[$feature_name] = new $className();
        }
		
        return $this->_featuresInstances[$feature_name] = false;
    }

    function _loadLayouts(){

		if (empty($this->_layouts))
		{
			$layout_paths = array(
				$this->templatePath.DS.'html'.DS.'layouts',
				$this->gantryPath.DS.'html'.DS.'layouts'
			 );

			$raw_layouts = array();
			foreach($layout_paths as  $layout_path){
				if (file_exists($layout_path) && is_dir($layout_path)){
					$d = dir($layout_path);
					while (false !== ($entry = $d->read())) {
						if($entry != '.' && $entry != '..'){
							$layout_name = basename($entry, ".php");
							
							if (!isset($raw_layouts[$layout_name])) {
								$raw_layouts[$layout_name] = $layout_path.DS.$layout_name.'.php';
							}
						}
					}
					$d->close();
				}
			}
			foreach ($raw_layouts as $layout => $path){
				if (!in_array($layout,  $this->_layouts)){
					$this->_layouts[$layout] = $path;
				}
			}
		}
		
		foreach ($this->_layouts as $layout => $path) {
			$className = 'GantryLayout'.ucfirst($layout);
			if (!class_exists($className, false)) {
				if (file_exists( $path )) {
					require_once( $path );
					if(!class_exists($className, false)) {
						unset($this->_layouts[$layout]);
					}
				} else {
					unset($this->_layouts[$layout]);
				}
			}
		}
    }

    function _getLayout($layout_name){
        $className = 'GantryLayout'.ucfirst($layout_name);
        if (!class_exists($className, false)){
            $this->_loadLayouts();
        }

        if (class_exists($className, false))
        {
            return new $className();
        }
        return false;
    }

    /**
     * @param  $schema
     * @return array
     */
    function _flipBodyPosition($schema) {

    	$backup = array_keys($schema);
    	$backup_reverse = array_reverse($schema);
    	$reverse = array_reverse($backup);

    	$pos = array_search('mb',$backup);

    	unset($backup[$pos]);

  		$new_keys = array();
  		$new_schema = array();

		reset($backup);
  		foreach($reverse as $value) {
  			if ($value != 'mb')	{
  				$value = current($backup);
  				next($backup);
  			}
  			$new_keys[] = $value;
  		}

  		reset($backup_reverse);
  		foreach ($new_keys as $key) {
  			$new_schema[$key] = current($backup_reverse);
  			next($backup_reverse);
  		}
    	return $new_schema;
    }

    /**
     * @return void
     */
	function _checkAjaxTool() {
        $ajax_tool = "gantry-ajax.php";
        $path = $this->templatePath . '/';
        $origin = $this->gantryPath . "/".$ajax_tool;


        if ((!file_exists($path . $ajax_tool) || (filesize($path . $ajax_tool) != filesize($origin))) && file_exists($path) && is_dir($path) && is_writable($path)) {
            jimport('joomla.filesystem.file');

            if (file_exists($path . $ajax_tool)) JFile::delete($path . $ajax_tool);
            JFile::copy($origin, $path . $ajax_tool);
        }
	}

    /**
     * @return void
     */
	function _checkLanguageFiles() {
        jimport('joomla.filesystem.file');
        $language_dir = $this->basePath.'/language/en-GB';
        $admin_language_dir = $this->basePath.'/administrator/language/en-GB';
        $template_lang_file = 'en-GB.tpl_'.$this->templateName.'.ini';

        if (file_exists($this->templatePath.DS.$template_lang_file)  &&
                (
                    (
                        !file_exists($language_dir.DS.$template_lang_file) &&
                        is_writable($language_dir)
                    )
                    ||
                    (
                        $this->get('copy_lang_files_if_diff',0)==1 &&
                        file_exists($language_dir.DS.$template_lang_file) &&
                        filesize($language_dir.DS.$template_lang_file) != filesize($this->templatePath.DS.$template_lang_file)
                    )
                )
            )
        {
            JFile::copy($this->templatePath.DS.$template_lang_file, $language_dir.DS.$template_lang_file);
        }

        if (file_exists($this->templatePath.DS.'admin'.DS.$template_lang_file) &&
                (
                    (
                        !file_exists($admin_language_dir.DS.$template_lang_file) &&
                        is_writable($admin_language_dir)
                    )
                    ||
                    (
                        $this->get('copy_lang_files_if_diff',0)==1 &&
                        file_exists($admin_language_dir.DS.$template_lang_file) &&
                        filesize($admin_language_dir.DS.$template_lang_file) != filesize($this->templatePath.DS.'admin'.DS.$template_lang_file)
                    )
                )
            )
        {
            JFile::copy($this->templatePath.DS.'admin'.DS.$template_lang_file, $admin_language_dir.DS.$template_lang_file);
        }
	}

    /**
     * @param  $array1
     * @param  $array2
     * @return
     */
	function _array_merge_replace_recursive( &$array1,  &$array2) {
		$merged = $array1;

		foreach($array2 as $key => $value) {
			if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
				$merged[$key] = $this->_array_merge_replace_recursive($merged[$key], $value);
			}
			else {
				$merged[$key] = $value;
			}
		}

		return $merged;
	}
		
	function cacheKey($key, $browser = false)
	{
		return $this->templateName . '-' . $this->_params_hash . ($browser ? ('-' . $this->_browser_hash) : '') .  "-" . $key;
	}
	
    /**#@-*/

    function addAdminElement($className){
        if (class_exists($className) && !in_array($className, $this->adminElements)){
            $this->adminElements[] = $className;
        }
    }

    function getCookiePath()
    {
        $cookieUrl = '';
        if (!empty($this->baseUrl))
        {
            if (substr($this->baseUrl, -1, 1) == '/')
            {
                $cookieUrl = substr($this->baseUrl, 0, -1);
            }
            else
            {
                $cookieUrl = $this->baseUrl;
            }
        }
        return $cookieUrl;
    }
}
