<?php
/**
 * @version        3.2.11 September 8, 2011
 * @author        RocketTheme http://www.rockettheme.com
 * @copyright     Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 */
class plgSystemGantry extends JPlugin
{


    protected $_cleanCacheAfterTasks =
    array(
        'com_modules' =>
        array(
            'module.apply',
            'module.save',
            'module.save2copy',
            'modules.unpublish',
            'modules.publish',
            'modules.saveorder',
            'modules.trash',
            'modules.duplicate'
        ),
        'com_templates' =>
        array(
            'publish',
            'save',
            'save_positions',
            'default',
            'apply',
            'save_source',
            'apply_source'
        ),
        'com_config' =>
        array(
            'save'
        )
    );

    /**
     * Catch the routed functions for
     */
    public function onAfterRoute()
    {


        $app = JFactory::getApplication();
        if (!$app->isAdmin()) return;

        $option = JRequest::getVar('option','');
        $task = JRequest::getVar('option','');

        //      $app = JFactory::getApplication();
        //
        //		$option = JRequest::getWord('option');
        //		$task = JRequest::getString('task');
        //
        //		if (!$app->isAdmin() || !array_key_exists($option, $this->_cleanCacheAfterTasks)) {
        //			return;
        //		}
        //
        //		//set if we need to export next render
        //		if (in_array($task, $this->_cleanCacheAfterTasks[$option])) {
        //            require_once(JPATH_LIBRARIES . "/gantry/gantry.php");
        //            gantry_import('core.utilities.gantrycache');
        //
        //            // back end gantry cache
        //            $cache = GantryCache::getInstance(true);
        //			$cache->clearGroupCache();
        //
        //            // Front end Cache
        //            $fecache = GantryCache::getInstance(false);
        //            $fecache->clearGroupCache();
        //		}
    }

    public function onAfterRender()
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin()) return;

        $option = JRequest::getString('option','');
        $view = JRequest::getString('view','');
        $task = JRequest::getString('task','');

        if ($option == 'com_templates' && (($view == 'styles') || (empty($view) && empty($task))))
        {
            $master_templates = $this->getMasters();
            $gantry_templates = $this->getGantryTemplates();
            if (!class_exists('phpQuery'))
            {
                require_once(JPATH_LIBRARIES . "/gantry/libs/phpQuery.php");
            }
            $document = & JFactory::getDocument();
            $doctype = $document->getType();
            if ($doctype == 'html')
            {
                $body =& JResponse::getBody();
                $pq = phpQuery::newDocument($body);

                foreach ($master_templates as $master)
                {
                    pq('td > input[value=' . $master . ']')->parent()->next()->append('<span style="margin:0 10px;background:#c00;color:#fff;padding:2px 4px;font-family:Georgia,serif;">Master</span>');
                }

                foreach ($gantry_templates as $gantry)
                {
                    $link = pq('td > input[value=' . $gantry . ']')->parent()->next()->find('a');
                    $value = str_replace('style.edit', 'template.edit', str_replace('com_templates', 'com_gantry', $link->attr('href')));
                    $link->attr('href', $value);
                }

                $body = $pq->getDocument()->htmlOuter();
                JResponse::setBody($body);
            }
        }

        if ($option == 'com_gantry')
        {

            if (!class_exists('phpQuery'))
            {
                require_once(JPATH_LIBRARIES . "/gantry/libs/phpQuery.php");
            }

            $body =& JResponse::getBody();
            $pq = phpQuery::newDocument($body);

            pq('div#toolbar-box')->after('<div class="clr"></div><dl id="system-message"><dt class="message"></dt><dd class="message message fade"><ul><li></li></ul></dd><span class="close"><span>x</span></span></dl>');
            pq('#mc-title')->before('<div class="clr"></div><dl id="system-message"><dt class="message"></dt><dd class="message message fade"><ul><li></li></ul></dd><span class="close"><span>x</span></span></dl>');
            pq('div#content > .pagetitle')->after('<div class="clr"></div><dl id="system-message"><dt class="message"></dt><dd class="message message fade"><ul><li></li></ul></dd><span class="close"><span>x</span></span></dl>');


            $body = $pq->getDocument()->htmlOuter();
            JResponse::setBody($body);
        }
    }

    public function onAfterDispatch()
    {

    }

    public function onSearch()
    {

    }

    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin()) return;
        if (array_key_exists('option', $_REQUEST) && array_key_exists('task', $_REQUEST))
        {
            $option = JRequest::getVar('option');
            $task = JRequest::getVar('task');

            // Redirect styles.duplicate to template.duplicate to handle gantry template styles
            if ($option == 'com_templates' && $task == 'styles.duplicate')
            {
                $this->setRequestOption('option', 'com_gantry');
                $this->setRequestOption('task', 'template.duplicate');
            }

            // Redirect styles.delete to not let a gantry master template style be deleted
            if ($option == 'com_templates' && $task == 'styles.delete')
            {
                $this->setRequestOption('option', 'com_gantry');
                $this->setRequestOption('task', 'template.delete');
            }

            // redirect styles.edit if the template style is a gantry one
            if ($option == 'com_templates' && $task == 'style.edit')
            {
                $id = JRequest::getInt('id', 0);
                if ($id == 0)
                {
                    // Initialise variables.
                    $pks = JRequest::getVar('cid', array(), 'post', 'array');
                    if (is_array($pks) && array_key_exists(0, $pks))
                    {
                        $id = $pks[0];
                    }
                }

                //redirect to gantry admin
                if ($this->isGantryTemplate($id))
                {
                    $this->setRequestOption('option', 'com_gantry');
                    $this->setRequestOption('task', 'template.edit');
                    $this->setRequestOption('id', $id);
                }
            }
        }
    }


    private function setRequestOption($key, $value)
    {
        JRequest::set(array($key => $value), 'GET');
        JRequest::set(array($key => $value), 'POST');
    }

    /**
     * Check if template is based on gantry
     *
     * @param string $id
     * @return boolean
     */
    private function isGantryTemplate($id)
    {
        // Get a row instance.
        $table = $this->getTable();

        // Attempt to load the row.
        $return = $table->load($id);

        // Check for a table object error.
        if ($return === false && $table->getError())
        {
            $this->setError($table->getError());
            return false;

        }
        $template = $table->template;

        return file_exists(JPATH_SITE . DS . 'templates' . DS . $template . DS . 'lib' . DS . 'gantry' . DS . 'gantry.php');

    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param    type    The table type to instantiate
     * @param    string    A prefix for the table class name. Optional.
     * @param    array    Configuration array for model. Optional.
     * @return    JTable    A database object
     */
    public function getTable($type = 'Style', $prefix = 'TemplatesTable', $config = array())
    {
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_templates/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    private function getTemplates()
    {
        $cache = JFactory::getCache('com_templates', '');
        $tag = JFactory::getLanguage()->getTag();

        $templates = $cache->get('templates0' . $tag);
        if ($templates === false)
        {
            // Load styles
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id, home, template, params');
            $query->from('#__template_styles');
            $query->where('client_id = 0');

            $db->setQuery($query);
            $templates = $db->loadObjectList('id');
            foreach ($templates as &$template)
            {
                $registry = new JRegistry;
                $registry->loadString($template->params);
                $template->params = $registry;

                // Create home element
                if ($template->home == '1' && !isset($templates[0]) && $template->home == $tag)
                {
                    $templates[0] = clone $template;
                }
            }
            $cache->store($templates, 'templates0' . $tag);
        }
        return $templates;
    }

    private function getMasters()
    {
        $templates = $this->getTemplates();
        $masters = array();
        foreach ($templates as $template)
        {
            if ($template->params->get('master') == 'true')
            {
                $masters[] = $template->id;
            }
        }
        return $masters;
    }

    private function getGantryTemplates()
    {
        $templates = $this->getTemplates();
        $gantry = array();
        foreach ($templates as $template)
        {
            if ($template->params->get('master') != null)
            {
                $gantry[] = $template->id;
            }
        }

        return $gantry;
    }
}