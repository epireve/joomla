<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$template_path = dirname(__FILE__);
require_once($template_path.'/lib/stainless.php');
$stainless = AdminPraise3Tools::getInstance();

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

require_once(dirname(__FILE__).DS.'index.php');
?>