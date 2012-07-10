<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

$menu	= AdminPraise3Menu::getInstance();

$html = "<ul>";
		
$html .= $menu->renderMainMenu('cpanel', ($this->get('option') == "com_cpanel" && $this->get('ap_task_set') != "list_components"));

if (($this->_user->get('gid') >= $this->get('menusAcl')) && $this->get('menusAcl') != 0)
	$html .= $menu->renderMenusMenu($this->get('option') =="com_menus");

if (($this->_user->get('gid') >= $this->get('sectionsAcl')) && $this->get('sectionsAcl') != 0)
	$html .= $menu->renderMainMenu('sections', $this->get('option') =="com_sections");
	
if (($this->_user->get('gid') >= $this->get('categoriesAcl')) && $this->get('categoriesAcl') != 0)
	$html .= $menu->renderMainMenu('categories', ($this->get('option') =="com_categories" && $this->get('scope') == "content"));
	
if (($this->_user->get('gid') >= $this->get('articlesAcl')) && $this->get('articlesAcl') != 0):
	$contentActive = $this->get('option') == "com_content" || $this->get('option') == "com_sections" && $this->get('sectionsAcl') == 0 || ($this->get('option') =="com_categories" && $this->get('scope') =="content") && $this->get('categoriesAcl') == 0 || $this->get('option') =="com_frontpage";
	$html .= $menu->renderMainMenu('articles', $contentActive);
endif;

if (($this->_user->get('gid') >= $this->get('flexicontentAcl')) && $this->get('flexicontentAcl') != 0)  
	$html .= $menu->renderCustomComponentMenu('flexicontent', $this->get('option') == 'com_flexicontent');

if (($this->_user->get('gid') >= $this->get('k2Acl')) && $this->get('k2Acl') != 0) 
	$html .= $menu->renderCustomComponentMenu('k2', $this->get('option') == 'com_k2');
	
if (($this->_user->get('gid') >= $this->get('kunenaAcl')) && $this->get('kunenaAcl') != 0) 
	$html .= $menu->renderCustomComponentMenu('kunena', $this->get('option') == 'com_kunena');
	
if (($this->_user->get('gid') >= $this->get('ninjaboardAcl')) && $this->get('ninjaboardAcl') != 0) 
	$html .= $menu->renderCustomComponentMenu('ninjaboard', $this->get('option') == 'com_ninjaboard');
	
if (($this->_user->get('gid') >= $this->get('zooAcl')) && $this->get('zooAcl') != 0) 
	$html .= $menu->renderCustomComponentMenu('zoo', $this->get('option') == 'com_zoo');
	
if (($this->_user->get('gid') >= $this->get('jseblodAcl')) && $this->get('jseblodAcl') != 0) 
	$html .= $menu->renderCustomComponentMenu('jseblod', $this->get('option') == 'com_cckjseblod');
	
if (($this->_user->get('gid') >= $this->get('joomailerAcl')) && $this->get('joomailerAcl') != 0) 
	$html .= $menu->renderCustomComponentMenu('joomailer', $this->get('option') == 'com_joomailermailchimpintegration');

if (($this->_user->get('gid') >= $this->get('sobi2Acl')) && $this->get('sobi2Acl') != 0)
	$html .= $menu->renderCustomComponentMenu('sobi2', $this->get('option') == 'com_sobi2');
	
if (($this->_user->get('gid') >= $this->get('sobiproAcl')) && $this->get('sobiproAcl') != 0) 
	$html .= $menu->renderCustomComponentMenu('sobipro', $this->get('option') == 'com_sobipro');

if (($this->_user->get('gid') >= $this->get('virtuemartAcl')) && $this->get('virtuemartAcl') != 0)
	$html .= $menu->renderCustomComponentMenu('virtuemart', $this->get('option') == 'com_virtuemart');

if (($this->_user->get('gid') >= $this->get('tiendaAcl')) && $this->get('tiendaAcl') != 0)
	$html .= $menu->renderCustomComponentMenu('tienda', $this->get('option') == 'com_tienda');

if (($this->_user->get('gid') >= $this->get('phocagalleryAcl')) && $this->get('phocagalleryAcl') != 0)
	$html .= $menu->renderCustomComponentMenu('phocagallery', $this->get('option') == 'com_phocagallery');
	
if (($this->_user->get('gid') >= $this->get('projectforkAcl')) && $this->get('projectforkAcl') != 0)
	$html .= $menu->renderCustomComponentMenu('projectfork', $this->get('option') == 'com_projectfork');

if (($this->_user->get('gid') >= $this->get('componentsAcl')) && $this->get('componentsAcl') != 0)  
	$html .= $menu->renderComponentMenu($this->get('ap_task') == 'list_components', $this->get('showChildren'));

if (($this->_user->get('gid') >= $this->get('modulesAcl')) && $this->get('modulesAcl') != 0)
	$html .= $menu->renderMainMenu('modules', $this->get('option') == 'com_modules');
	
if (($this->_user->get('gid') >= $this->get('pluginsAcl')) && $this->get('pluginsAcl') != 0)
	$html .= $menu->renderMainMenu('plugins', $this->get('option') == 'com_plugins');
	


	
for($x = 0; $x < 11; $x++) :
	$custom_main_acl  = $this->get('custom'.$x.'Acl', 0);
	$custom_main_name = $this->get('custom'.$x.'Name');
	$custom_main_link = $this->get('custom'.$x.'Link');
	if ($this->_user->get('gid') >= $custom_main_acl && $custom_main_acl != 0)
		$html .= '<li><a href="'.$custom_main_link.'">'.htmlspecialchars($custom_main_name).'</a></li>';
endfor;


        
if (($this->_user->get('gid') >= $this->get('templatesAcl')) && $this->get('templatesAcl') != 0)
	$html .= $menu->renderMainMenu('templates', $this->get('option') == 'com_templates');

if (($this->_user->get('gid') >= $this->get('usersAcl')) && $this->get('usersAcl') != 0)
	$html .= $menu->renderMainMenu('users', $this->get('option') == 'com_users');
	
if (($this->_user->get('gid') >= $this->get('installAcl')) && $this->get('installAcl') != 0)
	$html .= $menu->renderMainMenu('installer', $this->get('option') == 'com_installer');
        
if (($this->_user->get('gid') >= $this->get('adminAcl')) && $this->get('adminAcl') != 0)
	$html .= $menu->renderMainMenu('admin', $this->get('ap_task') =="admin");
	
if (($this->_user->get('gid') >= $this->get('adminAcl')) && $this->get('adminAcl') != 0){
	//$html .= $menu->renderMainMenu('tools', $this->get('ap_task') =="admin");
	$html .= "<li class=\"admin-item parent\">";
	$html .= "<a class=\"tools-link\"><span class=\"parent-name\">Tools</span></a>";
	$html .= "<ul class=\"status-tools submenu\">";
	$html .= "<li><a href=\"index.php?option=com_installer\"><span>". JText::_( 'INSTALLER' ) ."</span></a></li>";
	$html .= "<li><a href=\"index.php?option=com_plugins\"><span>". JText::_( 'PLUGINS' ) ."</span></a></li>";
	$html .= "<li><a href=\"index.php?option=com_cache\"><span>". JText::_( 'CACHE' ) ."</span></a></li>";
	$html .= "<li><a href=\"index.php?option=com_massmail\"><span>". JText::_( 'MASS MAIL' ) ."</span></a></li>";
	$html .= "<li><a href=\"index.php?option=com_media\"><span>". JText::_( 'MEDIA MANAGER' ) ."</span></a></li>";
	$html .= "<jdoc:include type=\"modules\" name=\"status\" style=\"statustools\" />";
	$html .= "<li><a href=\"index.php?ap_task=admin\"><span>". JText::_( 'FULL ADMIN MENU' ) ."</span></a></li>";
	$html .= "</ul>";
	$html .= "</li>";
}

$html .= "</ul>";


require($tmpl_path.'/default.php');