<?php

(defined('_VALID_MOS') or defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

// Include our custom cmslib
if(!defined('AZ_CACHE_PATH'))
	define('AZ_CACHE_PATH',(dirname(dirname(dirname(dirname(__FILE__))))). '/components/libraries/cmslib/cache');

/**
 * Original code by Brian Lozier
 * http://www.massassi.com/php/articles/template_engines/ 
 * Modified by Azrul (www.azrul.com) to run on Joomla 
 */ 
 
class AzrulJXTemplate {
    var $vars; /// Holds all the template variables

    /**
     * Constructor
     *
     * @param $file string the file name you want to load
     */
    function AzrulJXTemplate($file = null) {
        $this->file = $file;
        @ini_set('short_open_tag', 'On');
    }

    /**
     * Set a template variable.
     */
    function set($name, $value) {
        $this->vars[$name] = is_object($value) ? $value->fetch() : $value;
    }

    /**
     * Open, parse, and return the template file.
     *
     * @param $file string the template file name
     */
    function fetch($file = null) {
        if(!$file) $file = $this->file;

		if($this->vars)
        	extract($this->vars);          // Extract the vars to local namespace

        ob_start();                    // Start output buffering
        include($file);                // Include the file
        $contents = ob_get_contents(); // Get the contents of the buffer
        ob_end_clean();                // End buffering and discard
        return $contents;              // Return the contents
    }
    
    function object_to_array($obj) {
       $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
       $arr = array();
       foreach ($_arr as $key => $val) {
               $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
               $arr[$key] = $val;
       }
       return $arr;
	}
}

class AzrulJXCachedTemplate extends AzrulJXTemplate {
    var $cache_id;
    var $expire;
    var $cached;
    var $file;

    /**
     * Constructor.
     *
     * @param $cache_id string unique cache identifier
     * @param $expire int number of seconds the cache will live
     */
    function AzrulJXCachedTemplate($cache_id = "", $cache_timeout = 10000) {
        $this->AzrulJXTemplate();
        $this->cache_id = AZ_CACHE_PATH . "/cache__". md5($cache_id);
        $this->cached = false;
        $this->expire = $cache_timeout;
    }

    /**
     * Test to see whether the currently loaded cache_id has a valid
     * corrosponding cache file.
     */
    function is_cached() {
    	//return false;
        if($this->cached) return true;

        // Passed a cache_id?
        if(!$this->cache_id) return false;

        // Cache file exists?
        if(!file_exists($this->cache_id)) return false;

        // Can get the time of the file?
        if(!($mtime = filemtime($this->cache_id))) return false;

        // Cache expired?
        // Implemented as 'never-expires' cache, so, the data need to change
        // for the cache to be modified
        if(($mtime + $this->expire) < time()) {
            @unlink($this->cache_id);
            return false;
        }

        else {
            /**
             * Cache the results of this is_cached() call.  Why?  So
             * we don't have to double the overhead for each template.
             * If we didn't cache, it would be hitting the file system
             * twice as much (file_exists() & filemtime() [twice each]).
             */
            $this->cached = true; 
            return true;
        }
    }

    /**
     * This function returns a cached copy of a template (if it exists),
     * otherwise, it parses it as normal and caches the content.
     *
     * @param $file string the template file
     */
    function fetch_cache($file, $processFunc = null) {
    	$contents	= "";

        if($this->is_cached()) {
            $fp = @fopen($this->cache_id, 'r');
            if($fp){
            	$filesize = filesize($this->cache_id);
            	if($filesize > 0){
            		$contents = fread($fp, $filesize);
            	}
            	fclose($fp);
            } else {
            	$contents = $this->fetch($file);
			}
        }
        else {
            $contents = $this->fetch($file);
            
            // Check if caller wants to process contents with another function
			if($processFunc)
                $contents = $processFunc($contents);

			if(!empty($contents)){
			
	            // Write the cache, only if there is some data
	            if($fp = @fopen($this->cache_id, 'w')) {
	                fwrite($fp, $contents);
	                fclose($fp);
	            }
	            else {
	                //die('Unable to write cache.');
	            }
            }

           
        }
        
         return $contents;
    }
}

