<?php
/**
 * @version   1.6.0-SNAPSHOT April 22, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die('Restricted index access');

jimport('joomla.plugin.plugin');

class plgSystemMissionControl extends JPlugin
{

    protected static $templates;

    public function plgSystemMissionControl(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    public function onAfterInitialise()
    {
        $mainframe =& JFactory::getApplication();
        $option = JRequest::getString('option');
        $extension = JRequest::getString('extension', null);


        if ($mainframe->isAdmin())
        {

            $admin_style = null;
            $session_user_style = JFactory::getUser()->getParam('admin_style', null);
            $session_default_user_style = JFactory::getUser()->getParam('mc_default_style', null);
            $base_user_style = $this->getBaseUserInfo()->getParam('admin_style', null);
            $default_style = $this->getDefaultTemplate()->id;
            $mc_template = $this->getTemplateByName('rt_missioncontrol');
            $mc_style = 0;
            if (!empty($mc_template)){
                $mc_style = $mc_template->id;
            }


            if (null == $base_user_style && null != $session_user_style && $session_user_style != $session_default_user_style)
            {
                $admin_style = $session_default_user_style;
            }
            else if (null != $base_user_style && null != $session_user_style && $base_user_style != $session_user_style)
            {
                $admin_style = $base_user_style;
            }
            else if (null == $base_user_style && null == $session_user_style)
            {
                $admin_style = $default_style;
            }
            else if (null != $base_user_style && null == $session_user_style)
            {
                $admin_style = $base_user_style;
            }
            else if (null != $base_user_style && null != $session_user_style)
            {
                $admin_style = $session_user_style;
            }


            if (null != $session_default_user_style)
            {
                $admin_style = $session_default_user_style;
            }
            JFactory::getUser()->setParam('mc_default_style', null);

            //Only run of the current template is missioncontrol
            if ($default_style == $mc_style && ($admin_style == null || ($this->getTemplateById($admin_style) !== false && $admin_style == $mc_style)))
            {

                $blacklist = $mc_template->params->get('blacklist',null);

                $fallback_style = $mc_template->params->get('blackliststyle',$this->getTemplateByName('bluestork')->id);
                if (!empty($blacklist))
                {
                    if (in_array(strtolower($option), $blacklist) || in_array($extension, $blacklist))
                    {
                        $admin_style = $fallback_style;
                        JFactory::getUser()->setParam('mc_default_style', $default_style);
                    }
                }
            }

            // set the admin style to the session user
            JFactory::getUser()->setParam('admin_style', $admin_style);

            // if the template is mission control set the toolbar to the missioncontrol one
            if ($mainframe->getTemplate() == "rt_missioncontrol" or
                $mainframe->getTemplate() == "rt_missioncontrol_j16")
            {
                JLoader::register('JButton', JPATH_ADMINISTRATOR.'/templates/rt_missioncontrol/lib/button.php');
                JLoader::load('JButton');
                JLoader::register('JToolBar', JPATH_ADMINISTRATOR . '/templates/rt_missioncontrol/lib/toolbar.php');
                JLoader::load('JToolBar');
            }
        }
    }


    public function onAfterRoute()
    {
        $mainframe =& JFactory::getApplication();
		global $mctrl;

        $option = JRequest::getString('option');

        $output = "<?php \n";

        $tid = JRequest::getString('id');

        $template = $mainframe->getTemplate();

	
        // is user in admin area?
        if ($mainframe->isAdmin() && $template == 'rt_missioncontrol')
        {
			// in admin area
            if (JRequest::getString('option') == 'com_templates'
                && (JRequest::getString('task') == 'style.apply' || JRequest::getString('task') == 'style.save')
            )
            {
	            $jform = JRequest::getVar('jform');
				$params = $jform['params'];

                foreach ($params as $key => $value)
                {
                    if (strpos($key, '_color') > 0)
                    {
                        $output .= '$' . $key . '="' . $value . '";';
                    }

                }
                $path = JPATH_ADMINISTRATOR . DS . 'templates' . DS . $template . DS . 'css' . DS . 'color-vars.php';

                jimport('joomla.filesystem.file');
                JFile::write($path, $output);

                return;
            }
        }


    }

    /**
     * @return array
     */
    protected function getTemplates()
    {
        if (!isset(self::$templates))
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id, home, template, params');
            $query->from('#__template_styles');
            $query->where('client_id = 1');
            $db->setQuery($query);
            $templates = $db->loadObjectList('id');
            foreach ($templates as &$template)
            {
                $template->template = JFilterInput::getInstance()->clean($template->template, 'cmd');
                $template->params = new JRegistry($template->params);
                if (!file_exists(JPATH_THEMES . DS . $template->template . DS . 'index.php'))
                {
                    $template->params = new JRegistry();
                    $template->template = 'bluestork';
                }
            }
            self::$templates = $templates;
        }
        return self::$templates;
    }

    /**
     * @param  $name
     * @return TemplatesTableStyle
     */
    protected function getTemplateByName($name)
    {
        $templates = $this->getTemplates();
        foreach ($templates as $id => $template)
        {
            if ($template->template == $name) return $template;
        }
        return null;
    }

    /**
     * @return TemplatesTableStyle
     */
    protected function getDefaultTemplate()
    {
        $templates = $this->getTemplates();
        $default = null;
        foreach ($templates as $id => $template)
        {
            if ($template->template == 'bluestork') $default = $template;
            if ($template->home == 1) return $template;
        }
        return $default;
    }

    /**
     * @param  $id
     * @return TemplatesTableStyle | bool
     */
    protected function getTemplateById($id)
    {
        $templates = $this->getTemplates();
        if (!array_key_exists($id, $templates))
        {
            return false;
        }
        return $templates[$id];
    }

    /**
     * @return JUser
     */
    protected function getBaseUserInfo()
    {
        $user = JUser::getInstance(JFactory::getUser()->id);
        $params = new JRegistry();
        $params->loadString($user->params);
        $user->params =& $params;
        return $user;
    }
}
