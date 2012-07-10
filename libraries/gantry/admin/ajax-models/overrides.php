<?php
/**
 * @package gantry
 * @subpackage admin.ajax-models
 * @version        3.2.11 September 8, 2011
 * @author        RocketTheme http://www.rockettheme.com
 * @copyright     Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();
//gantry_import('core.gantryjson');
gantry_import('core.config.gantryformnaminghelper');

global $gantry;

$action = JRequest::getWord('action');
//if (!current_user_can('edit_theme_options')) die('-1');

if ($action == 'get_base_values')
{
    $passed_array = array();
    foreach ($gantry->_working_params as $param)
    {
        $param_name = GantryFormNamingHelper::get_field_id($param['name']);
        $passed_array[$param_name] = $param['value'];
    }
    $outdata = json_encode($passed_array);
    //$outdata = str_replace('\\\\\\' , '\\', $outdata);
    echo $outdata;
}
else if ($action == 'get_default_values')
{
    $passed_array = array();
    foreach ($gantry->_working_params as $param)
    {
        $param_name = GantryFormNamingHelper::get_field_id($param['name']);
        $passed_array[$param_name] = $param['default'];
    }
    $outdata =json_encode($passed_array);
    //$outdata = str_replace('\\\\\\' , '\\', $outdata);
    echo $outdata;
}
else
{
    return "error";
}
