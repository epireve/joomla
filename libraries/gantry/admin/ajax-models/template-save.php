<?php
/**
 * @package gantry
 * @subpackage admin.ajax-models
 * @version        3.2.11 September 8, 2011
 * @author        RocketTheme http://www.rockettheme.com
 * @copyright     Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();

global $gantry;

$action = JRequest::getString('action');
gantry_import('core.gantryjson');


switch ($action){
    case 'save':
    case 'apply':
        echo gantryAjaxSaveTemplate();
        break;
    default:
        echo "error";
}

	function gantryAjaxSaveTemplate()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

        JModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_gantry/models');
        $model = JModel::getInstance("Template", 'GantryModel');
        $data = JRequest::getVar('jform', array(), 'post', 'array');
        if (!$model->save($data)) {
            return 'error';
        }

		$task = JRequest::getCmd('task');
		if($task == 'apply') {
			return 'success: ' . JText::_('Template settings applied.');
		} else {
			return 'success: ' . JText::_('Template settings saved.');
		}
	}