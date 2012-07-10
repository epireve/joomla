<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class AdminPraise3MenuHelper {

	function getMainMenuLinks($key) {
		$links = array();
		
		$links['cpanel']['parent']		= array('url' => JURI::root().'administrator', 'text' => 'DASHBOARD', 'li-class' => 'home-item', 'a-class' => 'home-link');
		$links['cpanel']['children'] 	= array(
											array('url' => JURI::root().'administrator', 'text' => 'DASHBOARD'),
											array('url' => JURI::root(), 'text' => 'PREVIEW SITE', 'a-class' => 'modal'),
											array('url' => JURI::root().'?tp=1', 'text' => 'VIEW MODULE POSITIONS')
											);
											
		$links['sections']['parent']		= array('url' => 'index.php?option=com_sections&scope=content', 'text' => 'SECTIONS');
		$links['sections']['children'] 	= array(
											array('url' => 'index.php?option=com_sections&scope=content', 'text' => 'MANAGE SECTIONS'),
											array('url' => 'index.php?option=com_sections&scope=content&task=add', 'text' => 'NEW SECTION')
											);
		
		$links['categories']['parent']		= array('url' => 'index.php?option=com_categories&scope=content', 'text' => 'CATEGORIES');
		$links['categories']['children'] 	= array(
											array('url' => 'index.php?option=com_categories&scope=content', 'text' => 'MANAGE CATEGORIES'),
											array('url' => 'index.php?option=com_categories&scope=content&task=add', 'text' => 'NEW CATEGORY')
											);
		
		$links['articles']['parent']		= array('url' => 'index.php?option=com_content', 'text' => 'ARTICLES');
		$links['articles']['children'] 		= array(
												array('url' => 'index.php?option=com_content', 'text' => 'ARTICLES', 'children' => array(
													array('url' => 'index.php?option=com_content&task=add', 'text' => 'NEW ARTICLE')
												)),
												array('url' => 'index.php?option=com_sections&scope=content', 'text' => 'SECTIONS', 'children' => array(
													array('url' => 'index.php?option=com_sections&scope=content&task=add', 'text' => 'NEW SECTION')
												)),
												array('url' => 'index.php?option=com_categories&scope=content', 'text' => 'CATEGORIES', 'children' => array(
													array('url' => 'index.php?option=com_categories&scope=content&task=add', 'text' => 'NEW CATEGORY')
												)),
												array('url' => 'index.php?option=com_frontpage', 'text' => 'FRONTPAGE'),
												array('url' => 'index.php?option=com_content&filter_state=A', 'text' => 'ARCHIVED ARTICLES'),
												array('url' => 'index.php?option=com_trash&task=viewContent', 'text' => 'ARTICLE TRASH')
											);
		
		$links['plugins']['parent']		= array('url' => 'index.php?option=com_plugins', 'text' => 'PLUGINS');
		$links['plugins']['children'] 	= array(
											array('url' => 'index.php?option=com_plugins&filter_type=1', 'text' => 'ALL PLUGINS'),
											array('url' => 'index.php?option=com_plugins&filter_type=1', 'text' => 'PLUGIN FILTERS', 'children' => array(
												array('url' => 'index.php?option=com_plugins&filter_type=authentication', 'text' => 'AUTHENTICATION PLUGINS'),
												array('url' => 'index.php?option=com_plugins&filter_type=content', 'text' => 'CONTENT PLUGINS'),
												array('url' => 'index.php?option=com_plugins&filter_type=community', 'text' => 'COMMUNITY PLUGINS'),
												array('url' => 'index.php?option=com_plugins&filter_type=editors', 'text' => 'EDITORS PLUGINS'),
												array('url' => 'index.php?option=com_plugins&filter_type=editors-xtd', 'text' => 'EDITORS XTD PLUGINS'),
												array('url' => 'index.php?option=com_plugins&filter_type=payment', 'text' => 'PAYMENT PLUGINS'),
												array('url' => 'index.php?option=com_plugins&filter_type=search', 'text' => 'SEARCH PLUGINS'),
												array('url' => 'index.php?option=com_plugins&filter_type=system', 'text' => 'SYSTEM PLUGINS'),
												array('url' => 'index.php?option=com_plugins&filter_type=user', 'text' => 'USER PLUGINS'),
												array('url' => 'index.php?option=com_plugins&filter_type=xmlrpc', 'text' => 'XMLRPC PLUGINS')
											)),
											array('url' => 'index.php?option=com_installer', 'text' => 'INSTALL PLUGINS'),
											array('url' => 'index.php?option=com_installer&task=manage&type=plugins', 'text' => 'MANAGE PLUGINS')
											);
											
		$links['modules']['parent']		= array('url' => 'index.php?option=com_modules', 'text' => 'MODULES');
		$links['modules']['children'] 	= array(
											array('url' => 'index.php?option=com_modules', 'text' => 'SITE MODULES', 'children' => array(
												array('url' => 'index.php?option=com_modules&task=add', 'text' => 'NEW MODULE')
											)),
											array('url' => 'index.php?option=com_modules&client=1', 'text' => 'ADMIN MODULES', 'children' => array(
												array('url' => 'index.php?option=com_modules&client=1&task=add', 'text' => 'NEW ADMIN MODULE')
											)),
											array('url' => 'index.php?option=com_installer', 'text' => 'INSTALL MODULES'),
											array('url' => 'index.php?option=com_installer&task=manage&type=modules', 'text' => 'MANAGE MODULES')
											);
											

		$links['users']['parent']		= array('url' => 'index.php?option=com_users', 'text' => 'USERS', 'li-class' => 'users-item', 'a-class' => 'users-link');
		$links['users']['children'] 	= array(
											array('url' => 'index.php?option=com_users&filter_logged=1', 'text' => 'LOGGED IN USERS'),
											#array('url' => 'index.php?option=com_users&task=add', 'text' => 'NEW USER')
											);
											
											
		$links['templates']['parent']		= array('url' => 'index.php?option=com_templates', 'text' => 'APPEARANCE', 'li-class' => 'templates-item', 'a-class' => 'templates-link');
		$links['templates']['children'] 	= array(
											array('url' => 'index.php?option=com_templates', 'text' => 'SITE TEMPLATES'),
											array('url' => 'index.php?option=com_templates&client=1', 'text' => 'ADMIN TEMPLATES'),
											array('url' => 'index.php?option=com_installer', 'text' => 'INSTALL TEMPLATES'),
											array('url' => 'index.php?option=com_installer&task=manage&type=templates', 'text' => 'MANAGE TEMPLATES')
											);
											
		$links['installer']['parent']		= array('url' => 'index.php?option=com_installer', 'text' => 'INSTALLER', 'li-class' => 'installer-item', 'a-class' => 'installer-link');
		$links['installer']['children'] 	= array(
											array('url' => 'index.php?option=com_installer', 'text' => 'INSTALLER'),
											array('url' => 'index.php?option=com_installer&task=manage&type=components', 'text' => 'MANAGE COMPONENTS'),
											array('url' => 'index.php?option=com_installer&task=manage&type=modules', 'text' => 'MANAGE MODULES'),
											array('url' => 'index.php?option=com_installer&task=manage&type=plugins', 'text' => 'MANAGE PLUGINS'),
											array('url' => 'index.php?option=com_installer&task=manage&type=languages', 'text' => 'MANAGE LANGUAGES'),
											array('url' => 'index.php?option=com_installer&task=manage&type=templates', 'text' => 'MANAGE TEMPLATES')
											);				
																		
		$links['admin']['parent']		= array('url' => 'index.php?option=com_templates&task=edit&cid[]=adminpraise3&client=1', 'text' => 'SETTINGS', 'li-class' => 'admin-item', 'a-class' => 'admin-link');
		$links['admin']['children'] 	= array(
											array('url' => 'index.php?option=com_config&tmpl=component', 'text' => 'GLOBAL CONFIG', 'li-class' => 'modal-item', 'a-class' => 'modal'),
											array('url' => 'index.php?option=com_admin&task=sysinfo&tmpl=component', 'text' => 'SYSTEM INFO', 'li-class' => 'modal-item', 'a-class' => 'modal'),
											array('url' => 'index.php?option=com_templates&task=edit&cid[]=adminpraise3&client=1&tmpl=component', 'text' => 'ADMIN SETTINGS', 'li-class' => 'modal', 'a-class' => 'modal'),
											array('url' => 'index.php?option=com_modules&client=1', 'text' => 'ADMIN MODULES')
											);
											
		$links['tools']['parent']		= array('url' => 'index.php?option=com_templates&task=edit&cid[]=adminpraise3&client=1', 'text' => 'SETTINGS', 'li-class' => 'admin-item', 'a-class' => 'tools-link');
		$links['tools']['children'] 	= array(
											array('url' => 'index.php?ap_task=admin', 'text' => 'FULL ADMIN MENU'),
											array('url' => 'index.php?option=com_plugins', 'text' => 'PLUGINS'),
											array('url' => 'index.php?option=com_checkin', 'text' => 'CHECKIN'),
											array('url' => 'index.php?option=com_cache', 'text' => 'CACHE'),
											array('url' => 'index.php?option=com_media', 'text' => 'MEDIA MANAGER'),
											array('url' => 'index.php?option=com_massmail', 'text' => 'MASS MAIL')
											);
											
		if (isset($links[$key])) :
			return $links[$key];
		else :
			return array();
		endif;
	}

	function getSubMenuLinks($key) {
		$links = array();
		
		$links['content']	= array(
								array('url' => 'index.php?option=com_sections&scope=content', 'text' => 'SECTIONS'),
								array('url' => 'index.php?option=com_categories&scope=content', 'text' => 'CATEGORIES'),
								array('url' => 'index.php?option=com_frontpage', 'text' => 'FRONTPAGE')
								);
								
		$links['templates']	= array(
								array('url' => 'index.php?option=com_templates&task=edit&cid[]=adminpraise3&client=1', 'text' => 'ADMIN TEMPLATE PARAMS'),
								array('url' => 'index.php?option=com_installer', 'text' => 'INSTALL TEMPLATES'),
								array('url' => 'index.php?option=com_installer&task=manage&type=templates', 'text' => 'MANAGE TEMPLATES')
								);
								
		$links['modules']	= array(
								array('url' => 'index.php?option=com_installer', 'text' => 'INSTALL MODULES'),
								array('url' => 'index.php?option=com_installer&task=manage&type=modules', 'text' => 'MANAGE MODULES')
								);
								
		$links['plugins']	= array(
								array('url' => 'index.php?option=com_installer', 'text' => 'INSTALL PLUGINS'),
								array('url' => 'index.php?option=com_installer&task=manage&type=plugins', 'text' => 'MANAGE PLUGINS')
								);
								
		$links['cpanel']= array(
								array('url' => 'index.php?option=com_modules&client=1&task=add', 'text' => 'NEW DASHBOARD MODULE'),
								array('url' => 'index.php?option=com_modules&client=1', 'text' => 'MANAGE DASHBOARD MODULES')
								);
								
		$links['components']= array(
								array('url' => 'index.php?option=com_installer', 'text' => 'INSTALL COMPONENTS'),
								array('url' => 'index.php?option=com_installer&task=manage&type=components', 'text' => 'MANAGE COMPONENTS')
								);
								
		$links['users']= array(
								array('url' => 'index.php?option=com_users&filter_logged=1', 'text' => 'LOGGED IN USERS')
								);

		$adminLinks = AdminPraise3MenuHelper::getMainMenuLinks('admin');
		$links['admin'] = $adminLinks['children'];
		array_shift($links['admin']);
		
		$pfLinks = AdminPraise3MenuHelper::getCustomComponentLinks('projectfork');
		$pfChildren = $pfLinks['children'];
		$pfClasses = array('pf_button_controlpanel', 'pf_button_projects', 'pf_button_tasks', 'pf_button_time', 'pf_button_filemanager', 'pf_button_calendar', 'pf_button_board', 'pf_button_profile', 'pf_button_users', 'pf_button_groups', 'pf_button_config');
		
		for ($i=0; $i<count($pfChildren); $i++) :
			$pfChildren[$i]['li-class'] = $pfClasses[$i];
		endfor;

		$links['projectfork'] = $pfChildren;
		
		$vmLinks = AdminPraise3MenuHelper::getCustomComponentLinks('virtuemart');
				$vmChildren = $vmLinks['children'];
		
				$links['virtuemart'] = $vmChildren;
		
		if (isset($links[$key])) :
			return $links[$key];
		else :
			return array();
		endif;
	}
	
	function getCustomComponentLinks($key) {
		$links = array();
	
		$links['flexicontent']['parent']	= array('url' => 'index.php?option=com_flexicontent', 'text' => 'CONTENT');
		$links['flexicontent']['children'] 	= array(
												array('url' => 'index.php?option=com_flexicontent&view=items', 'text' => 'ITEMS'),
												array('url' => 'index.php?option=com_flexicontent&view=types', 'text' => 'TYPES'),
												array('url' => 'index.php?option=com_flexicontent&view=categories', 'text' => 'CATEGORIES'),
												array('url' => 'index.php?option=com_flexicontent&view=fields', 'text' => 'FIELDS'),
												array('url' => 'index.php?option=com_flexicontent&view=tags', 'text' => 'TAGS'),
												array('url' => 'index.php?option=com_flexicontent&view=archive', 'text' => 'ARCHIVE'),
												array('url' => 'index.php?option=com_flexicontent&view=filemanager', 'text' => 'FILES'), 
												array('url' => 'index.php?option=com_flexicontent&view=templates', 'text' => 'TEMPLATES'), 
												array('url' => 'index.php?option=com_flexicontent&view=stats', 'text' => 'STATISTICS')
											);
											
		$links['k2']['parent']	= array('url' => 'index.php?option=com_k2', 'text' => 'CONTENT');
		$links['k2']['children'] 	= array(
												array('url' => 'index.php?option=com_k2&view=item', 'text' => 'ADD NEW ITEM'),
												array('url' => 'index.php?option=com_k2&view=items&filter_trash=0', 'text' => 'ITEMS'),
												array('url' => 'index.php?option=com_k2&view=items&filter_featured=1', 'text' => 'FEATURED ITEMS'),
												array('url' => 'index.php?option=com_k2&view=items&filter_trash=1', 'text' => 'TRASHED ITEMS'),
												array('url' => 'index.php?option=com_k2&view=categories&filter_trash=0', 'text' => 'CATEGORIES'),
												array('url' => 'index.php?option=com_k2&view=categories&filter_trash=1', 'text' => 'TRASHED CATEGORIES'),
												array('url' => 'index.php?option=com_k2&view=tags', 'text' => 'TAGS'), 
												array('url' => 'index.php?option=com_k2&view=comments', 'text' => 'COMMENTS'), 
												array('url' => 'index.php?option=com_k2&view=extraFields', 'text' => 'EXTRA FIELDS'),
												array('url' => 'index.php?option=com_k2&view=extraFieldsGroups', 'text' => 'EXTRA FIELD GROUPS') 
											);
											
		$links['zoo']['parent']	= array('url' => 'index.php?option=com_zoo', 'text' => 'CONTENT');
		$links['zoo']['children'] 	= array(
												//array('url' => 'index.php?option=com_zoo&task=add', 'text' => 'ADD NEW ITEM'),
												array('url' => 'index.php?option=com_zoo', 'text' => 'ITEMS'),
												array('url' => 'index.php?option=com_zoo&controller=new', 'text' => 'NEW APP INSTANCE'),
												array('url' => 'index.php?option=com_zoo&controller=manager#filename', 'text' => 'INSTALL APP'),
												array('url' => 'index.php?option=com_zoo&controller=manager', 'text' => 'CONFIG') 
											);
											
		$links['jseblod']['parent']	= array('url' => 'index.php?option=com_cckjseblod', 'text' => 'CONTENT');
		$links['jseblod']['children'] 	= array(
												array('url' => 'index.php?option=com_cckjseblod&controller=interface&act=-1&cck=1', 'text' => 'ADD NEW CONTENT'),
												array('url' => 'index.php?option=com_content', 'text' => 'ITEMS'),
												array('url' => 'index.php?option=com_cckjseblod&controller=templates', 'text' => 'TEMPLATES'),
												array('url' => 'index.php?option=com_cckjseblod&controller=types', 'text' => 'CONTENT TYPES'),
												array('url' => 'index.php?option=com_cckjseblod&controller=items', 'text' => 'FIELDS'),
												array('url' => 'index.php?option=com_cckjseblod&controller=searchs', 'text' => 'SEARCH TYPES'),
												array('url' => 'index.php?option=com_cckjseblod&controller=packs', 'text' => 'PACK'),
												array('url' => 'index.php?option=com_cckjseblod&controller=configuration', 'text' => 'CONFIG') 
											);
											
		$links['sobi2']['parent']	= array('url' => 'index.php?option=com_sobi2', 'text' => 'DIRECTORY');
		$links['sobi2']['children'] 	= array(
												array('url' => 'index.php?option=com_sobi2&task=listing&catid=-1', 'text' => 'ALL ENTRIES'),
												array('url' => 'index.php?option=com_sobi2&task=getUnapproved', 'text' => 'ENTRIES AWAITING APPROVAL'),
												array('url' => 'index.php?option=com_sobi2&task=genConf', 'text' => 'GENERAL CONFIGURATION'),
												array('url' => 'index.php?option=com_sobi2&task=editFields', 'text' => 'CUSTOM FIELDS MANAGER'),
												array('url' => 'index2.php?option=com_sobi2&task=addItem&returnTask=', 'text' => 'ADD ENTRY'),
												array('url' => 'index2.php?option=com_sobi2&task=addCat&returnTask=', 'text' => 'ADD CATEGORY'),
												array('url' => 'index2.php?option=com_sobi2&task=templates', 'text' => 'TEMPLATE MANAGER'), 
												array('url' => 'index2.php?option=com_sobi2&task=pluginsManager', 'text' => 'PLUGIN MANAGER')
											);
											
		$links['sobipro']['parent']	= array('url' => 'index.php?option=com_sobipro', 'text' => 'DIRECTORY');
		$links['sobipro']['children'] 	= array(
												array('url' => 'index.php?option=com_sobipro&task=section.entries&pid=1', 'text' => 'ALL ENTRIES'),
												array('url' => 'index.php?option=com_sobipro&task=entry.add&pid=1', 'text' => 'ADD ENTRY'),
												array('url' => 'index.php?option=com_sobipro&sid=1', 'text' => 'CATEGORIES'),
												array('url' => 'index.php?option=com_sobipro&task=extensions', 'text' => 'APPLICATIONS'),
												array('url' => 'index.php?option=com_sobipro&task=acl', 'text' => 'ACL'),
												array('url' => 'index.php?option=com_sobipro&task=template.edit', 'text' => 'TEMPLATES'), 
												array('url' => 'index.php?option=com_sobipro&task=config.general', 'text' => 'CONFIGURATION')
											);
											
		$links['kunena']['parent']	= array('url' => 'index.php?option=com_kunena', 'text' => 'FORUM');
		$links['kunena']['children'] 	= array(
												array('url' => 'index.php?option=com_kunena&task=showAdministration', 'text' => 'CATEGORIES'),
												array('url' => 'index.php?option=com_kunena&task=new', 'text' => 'NEW CATEGORY'),
												array('url' => 'index.php?option=com_kunena&task=showprofiles', 'text' => 'USERS'),
												array('url' => 'index.php?option=com_kunena&task=showTemplates', 'text' => 'TEMPLATES'),
												array('url' => 'index.php?option=com_kunena&task=ranks', 'text' => 'RANKS'),
												array('url' => 'index.php?option=com_kunena&task=showtrashview', 'text' => 'TRASH'),
												array('url' => 'index.php?option=com_kunena&task=showconfig', 'text' => 'CONFIGURATION')
											);
											
		$links['ninjaboard']['parent']	= array('url' => 'index.php?option=com_ninjaboard&view=dashboard', 'text' => 'FORUM');
		$links['ninjaboard']['children'] 	= array(
												array('url' => 'index.php?option=com_ninjaboard&view=forums', 'text' => 'FORUMS'),
												array('url' => 'index.php?option=com_ninjaboard&view=forum', 'text' => 'NEW FORUM'),
												array('url' => 'index.php?option=com_ninjaboard&view=users', 'text' => 'USERS'),
												array('url' => 'index.php?option=com_ninjaboard&view=usergroups', 'text' => 'USERGROUPS'),
												array('url' => 'index.php?option=com_ninjaboard&view=ranks', 'text' => 'RANKS'), 
												array('url' => 'index.php?option=com_ninjaboard&view=tools', 'text' => 'TOOLS'),
												array('url' => 'index.php?option=com_ninjaboard&view=themes', 'text' => 'THEMES'),
												array('url' => 'index.php?option=com_ninjaboard&view=settings', 'text' => 'CONFIGURATION')
											);
											
		$links['joomailer']['parent']	= array('url' => 'index.php?option=com_joomailermailchimpintegration&view=main', 'text' => 'NEWSLETTER');
		$links['joomailer']['children'] 	= array(
												array('url' => 'index.php?option=com_joomailermailchimpintegration&view=joomailermailchimpintegrations', 'text' => 'LISTS'),
												array('url' => 'index.php?option=com_joomailermailchimpintegration&view=campaignlist', 'text' => 'CAMPAIGNS'),
												array('url' => 'index.php?option=com_joomailermailchimpintegration&view=create', 'text' => 'CREATE CAMPAIGN'),
												array('url' => 'index.php?option=com_joomailermailchimpintegration&view=campaigns', 'text' => 'REPORTS'),
												array('url' => 'index.php?option=com_joomailermailchimpintegration&view=templates', 'text' => 'TEMPLATES'),
												array('url' => 'index.php?option=com_joomailermailchimpintegration&view=extensions', 'text' => 'EXTENSIONS') 
											);
											
											
		$links['virtuemart']['parent']	= array('url' => 'index.php?option=com_virtuemart', 'text' => 'SHOP');
		$links['virtuemart']['children'] 	= array(
												array('url' => 'index.php?pshop_mode=admin&page=product.product_list&option=com_virtuemart', 'text' => 'PRODUCT LIST'),
												array('url' => 'index.php?pshop_mode=admin&page=product.product_category_list&option=com_virtuemart', 'text' => 'CATEGORY TREE'),
												array('url' => 'index.php?pshop_mode=admin&page=order.order_list&option=com_virtuemart', 'text' => 'ORDERS'),
												array('url' => 'index.php?pshop_mode=admin&page=store.payment_method_list&option=com_virtuemart', 'text' => 'PAYMENT METHODS'),
												array('url' => 'index.php?pshop_mode=admin&page=vendor.vendor_list&option=com_virtuemart', 'text' => 'VENDORS'),
												array('url' => 'index.php?pshop_mode=admin&page=admin.user_list&option=com_virtuemart', 'text' => 'USERS'),
												array('url' => 'index.php?pshop_mode=admin&page=admin.show_cfg&option=com_virtuemart', 'text' => 'CONFIGURATION'), 
												array('url' => 'index.php?pshop_mode=admin&page=store.store_form&option=com_virtuemart', 'text' => 'EDIT STORE')
											);
											
		$links['tienda']['parent']	= array('url' => 'index.php?option=com_tienda', 'text' => 'SHOP');
		$links['tienda']['children'] 	= array(
												array('url' => 'index.php?option=com_tienda&view=products', 'text' => 'PRODUCTS'),
												array('url' => 'index.php?option=com_tienda&view=categories', 'text' => 'CATEGORIES'),
												array('url' => 'index.php?option=com_tienda&view=orders', 'text' => 'ORDERS'),
												array('url' => 'index.php?option=com_tienda&view=users', 'text' => 'USERS'),
												array('url' => 'index.php?option=com_tienda&view=manufacturers', 'text' => 'MANUFACTURERS'),
												array('url' => 'index.php?option=com_tienda&view=localization', 'text' => 'LOCALIZATION'), 
												array('url' => 'index.php?option=com_tienda&view=reports', 'text' => 'REPORTS'),
												array('url' => 'index.php?option=com_tienda&view=tools', 'text' => 'TOOLS'),
												array('url' => 'index.php?option=com_tienda&view=config', 'text' => 'CONFIGURATION')
											);
		
											
		$links['projectfork']['parent']	= array('url' => 'index.php?option=com_projectfork', 'text' => 'PROJECTS');
		$links['projectfork']['children'] 	= array(
												array('url' => 'index.php?option=com_projectfork&amp;section=controlpanel', 'text' => 'CONTROL PANEL'),
												array('url' => 'index.php?option=com_projectfork&amp;section=projects', 'text' => 'Projects'),
												array('url' => 'index.php?option=com_projectfork&amp;section=tasks', 'text' => 'Tasks'),
												array('url' => 'index.php?option=com_projectfork&amp;section=time', 'text' => 'Time'),
												array('url' => 'index.php?option=com_projectfork&amp;section=filemanager', 'text' => 'Files'),
												array('url' => 'index.php?option=com_projectfork&amp;section=calendar', 'text' => 'Calendar'),
												array('url' => 'index.php?option=com_projectfork&amp;section=board', 'text' => 'Messages'), 
												array('url' => 'index.php?option=com_projectfork&amp;section=profile', 'text' => 'Profile'),
												array('url' => 'index.php?option=com_projectfork&amp;section=users', 'text' => 'Users'),
												array('url' => 'index.php?option=com_projectfork&amp;section=groups', 'text' => 'Groups'),
												array('url' => 'index.php?option=com_projectfork&amp;section=config', 'text' => 'Config')
											);
											
		$links['phocagallery']['parent']	= array('url' => 'index.php?option=com_phocagallery', 'text' => 'GALLERY');
		$links['phocagallery']['children'] 	= array(
												array('url' => 'index.php?option=com_phocagallery&view=phocagallerys', 'text' => 'IMAGES'),
												array('url' => 'index.php?option=com_phocagallery&view=phocagallerycs', 'text' => 'CATEGORIES'),
												array('url' => 'index.php?option=com_phocagallery&view=phocagalleryt', 'text' => 'THEMES'),
												array('url' => 'index.php?option=com_phocagallery&view=phocagalleryra', 'text' => 'CATEGORY RATING'),
												array('url' => 'index.php?option=com_phocagallery&view=phocagalleryraimg', 'text' => 'IMAGE RATING'),
												array('url' => 'index.php?option=com_phocagallery&view=phocagallerycos', 'text' => 'CATEGORY COMMENTS'),
												array('url' => 'index.php?option=com_phocagallery&view=phocagallerycoimgs', 'text' => 'IMAGE COMMENTS'), 
												array('url' => 'index.php?option=com_phocagallery&view=phocagalleryusers', 'text' => 'USERS'),
												array('url' => 'index.php?option=com_phocagallery&view=phocagalleryin', 'text' => 'INFO')
											);
	
		if (isset($links[$key])) :
			return $links[$key];
		else :
			return array();
		endif;
				
	}
	
	function getComponents()
	{
		$db   = &JFactory::getDBO();
		$user = &JFactory::getUser();
		$lang = &JFactory::getLanguage();
		
		$editAllComponents	= $user->authorize('com_components', 'manage');
		
		$query = 'SELECT *' .
		         ' FROM #__components' .
		         ' WHERE '.$db->NameQuote( 'option' ).' <> "com_frontpage"' .
		         ' AND '.$db->NameQuote( 'option' ).' <> "com_media"' .
		         ' AND enabled = 1' .
		         ' ORDER BY ordering, name';
		         $db->setQuery($query);
		       
		$comps = $db->loadObjectList(); // component list
		$subs  = array(); // sub menus
		$langs = array(); // additional language files to load
		$rows  = array();

		// first pass to collect sub-menu items
		foreach ($comps as $row)
		{
			if ($row->parent)
			{
				if (!array_key_exists($row->parent, $subs)) {
					$subs[$row->parent] = array ();
				}
				$subs[$row->parent][] = $row;
				$langs[$row->option.'.menu'] = true;
			} elseif (trim($row->admin_menu_link)) {
				$langs[$row->option.'.menu'] = true;
			}
		}

		// Load additional language files
		if (array_key_exists('.menu', $langs)) {
			unset($langs['.menu']);
		}
		foreach ($langs as $lang_name => $nothing) {
			$lang->load($lang_name);
		}

		foreach ($comps as $row)
		{
			if ($editAllComponents || $user->authorize('administration', 'edit', 'components', $row->option))
			{
				if ($row->parent == 0 && (trim($row->admin_menu_link) || array_key_exists($row->id, $subs)))
				{
					$row->name   = $lang->hasKey($row->option) ? JText::_($row->option) : $row->name;
					$link   = $row->admin_menu_link ? "index.php?$row->admin_menu_link" : "index.php?option=$row->option";
					
					$row->children = array();
					
					if (array_key_exists($row->id, $subs)) {
						foreach ($subs[$row->id] as $sub) {
							$key  = $row->option.'.'.$sub->name;
							$sub->name = $lang->hasKey($key) ? JText::_($key) : $sub->name;
							$link = $sub->admin_menu_link ? "index.php?$sub->admin_menu_link" : null;
							$row->children[] = $sub;
						}
					}
					if($row->parent == 0) {
						$rows[] = $row;
					}
				}
			}
		}
		return $rows;
	}

	function getMenus() {
		$db = &JFactory::getDBO();

		$sql = 
			"SELECT menutype, ".
			"	title ".
			"FROM #__menu_types ".
			"ORDER BY title";
		$db->setQuery($sql);

		$menus = $db->loadObjectList();
		return $menus;
	}
	
}