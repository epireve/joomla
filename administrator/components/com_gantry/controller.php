<?php
/**
 * @package   gantry
 * @subpackage core
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

jimport('joomla.application.component.controller');

// Import file dependencies
//require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_templates'.DS.'helpers'.DS.'template.php');

/**
 * @package        Joomla
 * @subpackage    RokGantry
 */
class GantryController extends JController
{
    /**
     * @var        string    The default view.
     * @since    1.6
     */
    protected $default_view = 'template';

    public function ajax()
    {
        global $gantry;

        // comment out the following 2 lines for debugging
        //$request = @$_SERVER['HTTP_X_REQUESTED_WITH'];
        $modelname = JRequest::getString('model');
        //if ((!isset($request) || strtolower($request) != 'xmlhttprequest') && (isset($modelname) && $modelname != "diagnostics")) die("Direct access not allowed.");

        // load and inititialize gantry class
        $gantry_path = JPATH_SITE . '/libraries/gantry/gantry.php';
        if (file_exists($gantry_path))
        {
            require_once($gantry_path);
        }
        else
        {
            echo "error " . JText::_('Unable to find Gantry library.  Please make sure you have it installed.');
            die;
        }

        $model = $gantry->getAjaxModel(JRequest::getString('model'), true);
        if ($model === false) die();
        include_once($model);

        /*
            - USAGE EXAMPLE -

            new Ajax({
				url: 'http://url/template/administrator/index.php?option=com_admin&tmpl=gantry-ajax-admin',
                onSuccess: function(response) {console.log(response);}
            }).request({
                'model': 'example', // <- mandatory, see "ajax-models" folder
                'template': 'template_folder', // <- mandatory, the name of the gantry template folder (rt_dominion_j15)
                'example': 'example1', // <-- from here are all custom query posts you can use
                'name': 'w00fz',
                'message': 'Hello World!'
            });
        */

        // Clear the cache gantry cache after each call
        $cache = GantryCache::getInstance();
        $cache->clearGroupCache();
    }
}
