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
 * @package   gantry
 * @subpackage core
 */
abstract class GantryElement extends JElement {

    public function __construct($parent = null){
        global $gantry;
        parent::__construct($parent);
        $gantry->addAdminElement(get_class($this));
    }
    
    public static function runFinalize($className){
        $class = new ReflectionClass($className);
        if ($class->hasMethod('finalize')){
            $finalize_method = $class->getMethod('finalize');
            if ($finalize_method->isStatic()){
                call_user_func(array($className,'finalize'));
            }
        }
    }
}