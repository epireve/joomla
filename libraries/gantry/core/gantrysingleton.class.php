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
defined('GANTRY_VERSION') or die();

/**
 * Implement the singleton pattern for the Gantry framework
 *
 * @package gantry
 * @subpackage core
 */
class GantrySingleton {

    /**
     * Gets the singleton instance of the class name passed in.
     *
     * @param  string $class The name of the class to get a singleton for
     * @return The singleton instance of the class name passed in.
     */
    function getInstance($class)
    {
        static $instances = array ();
            // array of instance names
        if (!array_key_exists($class, $instances)) {
            // instance does not exist, so create it
            $instances[$class] = new $class;
        }
            // if
        $instance =& $instances[$class];
        return $instance;
    }
}