<?php
/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 */
class RokNavMenuEvents extends JPlugin
{

    public function onContentPrepareForm($form, $data)
    {


        $app = JFactory::getApplication();
        if (!$app->isAdmin()) return;

        $option = JRequest::getWord('option');
        $layout = JRequest::getWord('layout');
        $task = JRequest::getWord('task');

        $module = $this->getModuleType($data);


        if ($option == 'com_modules' && $layout == 'edit' && $module == 'mod_roknavmenu')
        {

            require_once(JPATH_ROOT . '/modules/mod_roknavmenu/lib/RokNavMenu.php');

            require_once(JPATH_ROOT . '/modules/mod_roknavmenu/lib/RokSubfieldForm.php');

            JForm::addFieldPath(JPATH_ROOT . '/modules/mod_roknavmenu/fields');


            RokNavMenu::loadCatalogs();
            foreach (RokNavMenu::$themes as $theme_name => $theme_info)
            {
                $params_file = $theme_info['path'] . "/parameters.xml";
                if (JFile::exists($params_file))
                {
                    $form->loadFile($params_file, false);
                }

                $fields_folder = $theme_info['path'] . "/fields";
                if (JFolder::exists($fields_folder))
                {
                    JForm::addFieldPath($fields_folder);
                }

                $language_path = $theme_info['path'] . "/language";
                if (JFolder::exists($language_path)){
                    $language =& JFactory::getLanguage();
                    $language->load($theme_name ,$theme_info['path'], $language->getTag(), true);
                }

            }

            $subfieldform = RokSubfieldForm::getInstance($form);

            if (!empty($data) && isset($data->params)) $subfieldform->setOriginalParams($data->params);

            if ($task == 'save' || $task == 'apply')
            {
                $subfieldform->makeSubfieldsVisable();
            }
        }
        else if ($option == 'com_menus' && $layout == 'edit'){
            JForm::addFieldPath(JPATH_ROOT . '/modules/mod_roknavmenu/fields');
            // Load 2x Catalog Themes
            require_once(JPATH_ROOT . "/modules/mod_roknavmenu/lib/RokNavMenu.php");
            RokNavMenu::loadCatalogs();
            foreach (RokNavMenu::$themes as $theme_name => $theme_info)
            {
                $item_file = $theme_info['path'] . "/item.xml";
                if (JFile::exists($item_file))
                {
                    $form->loadFile($item_file, true);
                }

                $fields_folder = $theme_info['path'] . "/fields";
                if (JFolder::exists($fields_folder))
                {
                    JForm::addFieldPath($fields_folder);
                }
            }
        }

    }

    protected function getModuleType(&$data)
    {
        if (is_array($data) && isset($data['module']))
        {
            return $data['module'];
        }
        elseif (is_array($data) && empty($data))
        {
            $form = JRequest::getVar('jform');
            if (is_array($form) && array_key_exists('module',$form))
            {
                return $form['module'];
            }
        }
        if (is_object($data) && method_exists( $data , 'get'))
        {
            return $data->get('module');
        }
        return '';
    }
}

