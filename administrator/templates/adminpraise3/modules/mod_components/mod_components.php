<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

$menu = AdminPraise3Menu::getInstance();
$components = AdminPraise3MenuHelper::getComponents();

require($tmpl_path.'/default.php');