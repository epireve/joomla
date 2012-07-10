<?php
/**
 * @package Gantry Template Framework - RocketTheme
 * @version 1.3 October 12, 2011
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and inititialize gantry class
require_once('lib/gantry/gantry.php');
$gantry->init();

?>
<?php if (JRequest::getString('type')=='raw'):?>
	<jdoc:include type="component" />
<?php else: ?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
		<head>
			<?php 
				$gantry->displayHead();
				$gantry->addStyles(array('template.css','joomla.css','typography.css'));
			?>
		</head>
		<body class="component-body">
			<div id="rt-main">
				<div class="rt-block">
					<div id="rt-mainbody">
						<div class="component-content">
					    	<jdoc:include type="component" />
					    </div>
					</div>
				</div>
			</div>
		</body>
	</html>
<?php endif; ?>
<?php
$gantry->finalize();
?>