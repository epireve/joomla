<?php
/**
 * RocketTheme Module
 *
 * @package RocketTheme
 * @subpackage rokmodule
 * @version   2.2 December 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
defined('_JEXEC') or die();

$module_id = JRequest::getInt('moduleid',null);

$db		=& JFactory::getDBO();
$query = "SELECT DISTINCT * from #__modules where id=".$module_id;

$db->setQuery( $query );
$result = $db->loadObject();


if ($result) {

    $document	= &JFactory::getDocument();
    $renderer	= $document->loadRenderer( 'module' );
    $options	 = array( 'style' => "raw" );
    $module	 = JModuleHelper::getModule( $result->module );
    $module->params = $result->params;

    $output = $renderer->render( $module, $options );

    echo $output;
    
}


?>