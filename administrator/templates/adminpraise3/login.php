<?php
/**
 * @copyright	Copyright (C) 2009 JoomlaPraise. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(dirname(__FILE__).'/lib/stainless.php');
$stainless = AdminPraise3Tools::getInstance();

$templateTheme    = $stainless->get('templateTheme');
$mainframe = &JFactory::getApplication();

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<jdoc:include type="head" />

<link href="templates/<?php echo  $this->template ?>/css/template.css" rel="stylesheet" type="text/css" />

<meta name="viewport" content="width=device-width, minimum-scale=0.2, maximum-scale=1.0" />
<link rel="apple-touch-icon" href="templates/<?php echo $this->template ?>/images/apple-touch-icon.png" />

<script type="text/javascript" src="templates/<?php echo $this->template ?>/js/stainless.js"></script>
<script language="javascript" type="text/javascript">
	function setFocus() {
		document.login.username.select();
		document.login.username.focus();
	}
    apSetLoginCookie();
</script>

<?php echo $stainless->generateLoginStyles(); ?>
<?php
// Fade out system messages
if ($this->getBuffer('message'))
	echo $stainless->fadeOutJS();
?>
</head>
<body id="login" onload="javascript:setFocus()" class="<?php echo $stainless->get('templateColor'). " " .$stainless->get('templateTheme');?>">
<div id="ap-login">
	<div id="content-box">
		<div class="padding">
			<div id="element-box" class="login">
				<div>
					<jdoc:include type="component" />
					<p class="home-page">
						<a href="<?php echo JURI::root(); ?>"><?php echo JText::_('Return to site Home Page') ?></a>
					</p>
					<div class="clr"></div>
				</div>
				<div id="ap-login-logo">
					<span id="ap-login-icon"></span>
				</div>
			</div>
			<noscript>
				<?php echo JText::_('WARNJAVASCRIPT') ?>
			</noscript>
			<div class="clr"></div>
		</div>
	</div>
</div>
	<div id="ap-footer" class="ap-padding">
		<!--begin-->
		<p class="copyright">
		<a target="_blank" href="http://www.adminpraise.com/joomla/admin-templates.php">Joomla! Admin Templates</a>
		&amp; <a target="_blank" href="http://www.adminpraise.com/joomla/admin-extensions.php">Extensions</a>
		by <a target="_blank" href="http://www.adminpraise.com/" class="ap-footlogo">AdminPraise</a>.
		<br />
		<a target="_blank" href="http://www.joomla.org">Joomla!</a> 
		<?php echo  JText::_('ISFREESOFTWARE') ?>	</p>
		<!--end-->
		<div class="clear">&nbsp;</div>
	</div>
<div id="hiddenDiv"><jdoc:include type="message" />
</div>
</body>
</html>
