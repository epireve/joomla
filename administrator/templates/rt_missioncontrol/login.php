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

global $mctrl;
$mctrl =& MissionControl::getInstance();
//$mctrl->storeRedirect();

$mctrl->addStyle("core.css");
$mctrl->addStyle("colors.css.php");
$mctrl->addStyle("http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light&subset=latin");
$mctrl->addScript('MC.js');
$mctrl->addScript('MC.Notice.js');
$mctrl->addScript('MC.Lang.js');
//var_dump ($mctrl);

// old doctype:
// 	<!DOCTYPE PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $mctrl->language; ?>" lang="<?php echo $mctrl->language; ?>" dir="<?php echo $mctrl->direction; ?>">
	<head>
		<jdoc:include type="head" />
	</head>
	<body id="mc-login" class="<?php $mctrl->displayBodyTags(); ?>">
		<div id="mc-frame">
			<div id="mc-header">
				<div class="mc-wrapper">
					<div id="mc-status">
						<?php $mctrl->displayLoginStatus(); ?>
					</div>	
				</div>
				<div id="mc-logo">
					<?php $mctrl->displayLogo(); ?>
					<h1><?php echo JText::_("MC_ADMINISTRATOR_LOGIN"); ?></h1>
				</div>
			</div>
			<div id="mc-body">
				<div class="mc-wrapper">
					<jdoc:include type="component" />
				</div>
			</div>	
			<div id="mc-footer">
				<div class="mc-wrapper">
					<p class="copyright">
						<span class="mc-footer-logo"></span>
						<a href="http://www.joomla.org" target="_blank">Joomla!</a>
						 <?php $joomla= '<a href="http://www.joomla.org">Joomla!&#174;</a>';
	 echo JText::sprintf('JGLOBAL_ISFREESOFTWARE', $joomla) ?> - Joomla! <?php echo  JText::_('Version') ?> <?php echo  JVERSION; ?><br />
						<?php echo JText::_('MC_FOOTER') ?>
					</p>
				</div>
			</div>
			<div id="mc-message">
				<jdoc:include type="message" />
			</div>
		</div>
	</body>
</html>
