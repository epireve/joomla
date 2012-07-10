<?php
/**
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

require_once(dirname(__FILE__) . '/cache/cache.class.php');
require_once(dirname(__FILE__) . '/cache/joomlaCacheDriver.class.php');
require_once(dirname(__FILE__) . '/cache/fileCacheDriver.class.php');

gantry_import('core.gantrysingleton');
/**
 *
 */
class GantryCache
{
    static $instance;

    const GROUP_NAME = 'Gantry';

    /**
     * Files to watch for changes.  Invalidate cache
     * @var array
     */
    protected $watch_files = array();

    /**
     * The cache object.
     *
     * @var Cache
     */
    protected $cache = null;

    /**
     * Lifetime of the cache
     * @access private
     * @var int
     */
    protected $lifetime = 900;

    /**
     * @var string
     */
    protected $base_cache_dir;

    /**
     * @static
     * @param bool $admin
     * @return GantryCache
     */
    public static function getInstance($admin = false)
    {
        if (!self::$instance) {
            self::$instance = new GantryCache($admin);
        }
        return self::$instance;
    }

    public function __construct($admin = false)
    {
        $this->base_cache_dir = JPATH_BASE . '/cache/_gantry-3.2.11/';
        $this->cache = new GantryCacheLib();
        $this->init($admin);
    }

    public function init($admin)
    {
        $conf = & JFactory::getConfig();

        if (!$admin && $conf->getValue('config.caching')) {
            // Use Joomla Cache for the frontend
            $this->cache->addDriver('frontend', new JoomlaCacheDriver(self::GROUP_NAME, $this->lifetime));
        }
        elseif ($admin) {
            // TODO get lifetime for backend cache
            $cache_dir = JPATH_BASE . '/cache/_gantry-3.2.11/';
            $this->cache->addDriver('admin', new FileCacheDriver($this->lifetime, $cache_dir));                 
        }
    }

    protected function checkForClear(){
        if ($this->checkWatchedFiles()){
            $this->clearGroupCache();
        }
    }

    public function call($identifier, $function = null, $arguments = array())
    {
        $this->checkForClear();
        $ret = $this->cache->get(self::GROUP_NAME, $identifier);
        if ($ret == false && $function != null) {
            $ret = call_user_func_array($function, $arguments);
            $this->cache->set(self::GROUP_NAME, $identifier, $ret);
        }
        return $ret;
    }

    public function get($identifier){
        $this->checkForClear();
        return $this->cache->get(self::GROUP_NAME, $identifier);
    }

    public function set($identifier, $data){
        return $this->cache->set(self::GROUP_NAME, $identifier, $data);
    }

    public function clearAllCache()
    {
        return $this->cache->clearAllCache();
    }

    public function clearGroupCache()
    {
        return $this->cache->clearGroupCache(self::GROUP_NAME);
    }

    public function clear($identifier)
    {
        return $this->cache->clearCache(self::GROUP_NAME, $identifier);
    }

    /**
     * Gets the lifetime for gantry
     * @access public
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Sets the lifetime for gantry
     * @access public
     * @param int $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
        $this->cache->setLifeTime($lifetime);
    }

    public function addWatchFile($filepath){
        if (file_exists($filepath) && !in_array($filepath, $this->watch_files)){
            $key = md5($filepath);
            $this->watch_files[$key] = $filepath;
            if ($this->cache->get(self::GROUP_NAME,$key) === false){
                $this->cache->set(self::GROUP_NAME,$key, filemtime($filepath));
            }
        }
    }

    /**
     * @return bool true if the cache needs to be clean
     */
    protected function checkWatchedFiles(){
        // check the watch files
        foreach($this->watch_files as $key => $watchfile){
            if (file_exists($watchfile)){
                $file_mtime = filemtime($watchfile);
                $cached_filemtime = $this->cache->get(self::GROUP_NAME,$key);
                if ($cached_filemtime != false && $file_mtime != $cached_filemtime){
                    return true;
                }
            }
        }
        return false;
    }
}