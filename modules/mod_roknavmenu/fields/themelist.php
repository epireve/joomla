<?php
/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of folder
 *
 * @package        Joomla.Framework
 * @subpackage    Form
 * @since        1.6
 */
class JFormFieldThemeList extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    public $type = 'ThemeList';

    protected $_front_side_template;

    /**
     * Method to get the field options.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions()
    {
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        $options = array();


        // Load 2x Catalog Themes
        require_once(JPATH_ROOT . "/modules/mod_roknavmenu/lib/RokNavMenu.php");
        RokNavMenu::loadCatalogs();

        foreach (RokNavMenu::$themes as $theme_name => $theme_info)
        {
            $options[] = JHTML::_('select.option', $theme_name, $theme_info['fullname']);
        }

        return $options;
    }
}
