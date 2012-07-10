<?php
/**
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$template_path = dirname(__FILE__);
require_once($template_path.'/lib/stainless.php');

$mainframe = &JFactory::getApplication();
$stainless = &AdminPraise3Tools::getInstance();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo  $this->language; ?>" lang="<?php echo  $this->language; ?>" dir="<?php echo  $this->direction; ?>" >
<head>
<jdoc:include type="head" />

<link href="templates/<?php echo  $this->template ?>/css/template.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="templates/<?php echo  $this->template; ?>/js/ap-component.js"></script>
</head>
<body class="contentpane ap-modal ap-modal-<?php echo $stainless->get('option'). " ap-modal-view-" . $stainless->get('view') . " " .$stainless->get('templateTheme');?>">
	<jdoc:include type="message" />
	<jdoc:include type="modules" name="toolbar" />
	<?php if(($stainless->get('option') == "com_admin") || ($stainless->get('option') == "com_config")) { ?>
	<jdoc:include type="modules" name="submenu" id="submenu-box" />
	<?php } ?>
	<div id="ap-modal-content">
		<jdoc:include type="component" />
	</div>
</body>
</html>
