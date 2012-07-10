<?php
/**
 * @version � 2.2 December 20, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and init the MissioControl Class
require_once('lib/missioncontrol.class.php');
global $option;

global $mctrl;
$mctrl =& MissionControl::getInstance();
$mctrl->processAjax();
$mctrl->initRenderer();
$mctrl->addStyle("core.css","1004112135");
$mctrl->addStyle("menu.css","1004112135");
$mctrl->addStyle("colors.css.php");
$mctrl->addScript('MC.js');
$mctrl->addOverrideStyles();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $mctrl->language; ?>" lang="<?php echo $mctrl->language; ?>" dir="<?php echo $mctrl->direction; ?>">
	<head>
		<jdoc:include type="head" />
	</head>
	<body id="mc-standard" class="<?php $mctrl->displayBodyTags(); ?>">
		<div id="mc-frame">
			<div id="mc-header">
				<div class="mc-wrapper">
					<div id="mc-status">
						<?php $mctrl->displayStatus(); ?>
					</div>
					<div id="mc-logo">
						<?php $mctrl->displayLogo(); ?>
						<h1><?php echo $mctrl->params->get('adminTitle') ? $mctrl->params->get('adminTitle') : JText::_('Administration'); ?></h1>
					</div>
					<div id="mc-nav">
						<?php $mctrl->displayMenu(); ?>
					</div>
					<div id="mc-userinfo">
						<div class="mc-userinfo2">
						<?php $mctrl->displayUserInfo(); ?>
						</div>
					</div>
					<div class="mc-clr"></div>
				</div>
			</div>
			<div id="mc-body">
				<div class="mc-wrapper">
					<jdoc:include type="message" />
					<div id="mc-title">
						<?php $mctrl->displayTitle(); ?>
						<?php $mctrl->displayHelpButton(); ?>
						<?php $mctrl->displayToolbar(); ?>
						<div class="mc-clr"></div>
					</div>
					<div id="mc-submenu">
						<?php $mctrl->displaySubMenu(); ?>
					</div>
					
				
					<?php if ($option == 'com_cpanel') : ?>
					<div id="mc-sidebar">
						<jdoc:include type="modules" name="sidebar" style="sidebar"  />
					</div>
					<div id="mc-cpanel">
						<?php $mctrl->displayDashText(); ?>
						<jdoc:include type="modules" name="dashboard" style="standard"  />
					<?php endif; ?>
					
					<div id="mc-component">
						<jdoc:include type="component" />
					</div>
					<?php if ($option == 'com_cpanel') : ?>
					</div>					
					<?php endif; ?>
					<div class="mc-clr"></div>
				</div>
			</div>	
			<div id="mc-footer">
				<div class="mc-wrapper">
					<p class="copyright">
						<span class="mc-footer-logo"></span>
						<a href="http://www.joomla.org" target="_blank">Joomla!</a>
						 <?php $joomla= '<a href="http://www.joomla.org">Joomla!&#174;</a>';
	 echo JText::sprintf('JGLOBAL_ISFREESOFTWARE', $joomla) ?> - Joomla! <?php echo  JText::_('Version') ?> <?php echo  JVERSION; ?><br />
						<?php echo JText::_('MC_FOOTER') ?> (MC Version <?php echo CURRENT_VERSION; ?>)
					</p>
				</div>
			</div>
			<div id="mc-message">
				
			</div>
		</div>
	</body>
</html>
