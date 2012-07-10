<?php
/**
 * @version   $Id$
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
class PlgSystemMissionControlInstallerScript
{

    public function install($parent)
    {
        $this->migrateFromOldTemplateName();
        return true;
    }

    public function update($parent)
    {
        return $this->migrateFromOldTemplateName();
    }

    protected function migrateFromOldTemplateName()
    {
        // See if the old named
        $old_template = $this->getTemplateInfo('rt_missioncontrol_j16');
        if ($old_template === false) {
            return true;
        }
        $new_template = $this->getTemplateInfo('rt_missioncontrol');
        if ($new_template === false) {
            return false;
        }

        $new_params = reset($new_template->styles);
        /** @var $new_params JRegistry */
        foreach ($old_template->styles as $style_id => &$style_params)
        {
            $working_params = clone $new_params;
            $working_params->merge($style_params);
            $old_template->styles[$style_id] = $working_params;
        }
        $this->mergeTemplateStyles($old_template, $new_template);
        $this->removeTemplateStyle(key($new_template->styles));
        $this->removeTemplate($old_template);
    }

    protected function removeTemplate($template)
    {
        $installer = new JInstaller();
        $installer->uninstall('template', $template->id);
    }


    protected function removeTemplateStyle($id)
    {
        /** @var $db JDatabase */
        $db = JFactory::getDbo();
        /** @var $query JDatabaseQuery */
        $query = $db->getQuery(true);
        $query->delete();
        $query->from('#__template_styles');
        $query->where('id = ' . (int)$id);
        $db->setQuery((string)$query);
        $db->query();
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
            return false;
        }
    }

    protected function mergeTemplateStyles($old_template, $new_template)
    {
        /** @var $db JDatabase */
        $db = JFactory::getDbo();
        foreach ($old_template->styles as $style_id => $style_params) {
            /** @var $style_params JRegistry*/
            /** @var $query JDatabaseQuery */
            $query = $db->getQuery(true);

            $query->update('#__template_styles');
            $query->set('template = ' . $db->quote($new_template->name));
            $query->set('title = REPLACE(title,' . $db->quote($old_template->name) . ',' . $db->quote($new_template->name) . ')');
            $query->set('params = ' . $db->quote($style_params->toString()));
            $query->where('id = ' . (int)$style_id);
            $db->setQuery((string)$query);
            $db->query();
            // Check for a database error.
            if ($db->getErrorNum()) {
                JError::raiseWarning(500, $db->getErrorMsg());
                return false;
            }
        }
    }

    protected function getTemplateInfo($template_name)
    {
        // Load the template name from the database
        /** @var $db JDatabase */
        $db = JFactory::getDbo();
        /** @var $query JDatabaseQuery */
        $query = $db->getQuery(true);
        $query->select('template, s.params, e.extension_id as id, s.id as style_id');
        $query->from('#__template_styles as s');
        $query->leftJoin('#__extensions as e ON e.type=' . $db->quote('template') . ' AND e.element=s.template AND e.client_id=s.client_id');
        $query->where('s.template =' . $db->quote($template_name), 'AND');
        $query->where('s.client_id = 1', 'AND');
        $db->setQuery($query);
        // Check for a database error.
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
            return false;
        }
        $objects = $db->loadObjectList();
        if ($objects == null) {
            return false;
        }
        $template = new stdClass();
        $template->styles = array();
        foreach ($objects as $template_style)
        {
            $template->name = $template_style->template;
            $template->id = $template_style->id;
            $template->styles[$template_style->style_id] = new JRegistry($template_style->params);
        }
        return $template;
    }
}
