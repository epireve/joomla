<?php
/**
 * @package   Gantry Template Framework - RocketTheme
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.gantryflatfile');

if (!defined('POSITIONS_MD5'))
{
    define('POSITIONS_MD5', 0);
    define('POSITIONS_LAYPUT', 1);
}

class GantryPositions
{

    private static $instances = array();

    public static function getInstance($grid)
    {
        if (!array_key_exists($grid, self::$instances))
        {
            $instances[$grid] = new GantryPositions($grid);
        }
        return $instances[$grid];
    }


    private $_db = null;
    private $_db_file = null;
    private $_cache = array();

    private $_gridSystem;

    protected function __construct($grid)
    {
        $this->_gridSystem = $grid;
    }

    public function __sleep()
    {
        return array(
            '_cache',
            '_gridSystem'
        );
    }

    private function _init()
    {
        global $gantry;

        if (null == $this->_db)
        {
            $this->_db = new Flatfile();
            $this->_db->datadir = $gantry->gantryPath . DS . 'admin' . DS . 'cache' . DS;
        }

        $this->_db_file = $this->_gridSystem . '.cache.txt';
    }

    public function get($md5)
    {
        $this->_init();
        $ret = null;

        if (array_key_exists($md5, $this->_cache))
        {
            return $this->_cache[$md5];
        }
        $retarray = $this->_db->selectUnique($this->_db_file, POSITIONS_MD5, $md5);
        if (null != $retarray && is_array($retarray) && count($retarray) > 0)
        {
            $ret = $retarray[POSITIONS_LAYPUT];
        }
        $this->_cache[$md5] = $ret;
        return $ret;
    }

    public function set($md5, $permutation)
    {
        $this->_init();
        $this->_db->insert($this->_db_file, array($md5, $permutation));
    }
}