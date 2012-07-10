<?php
/**
 * @package   gantry
 * @subpackage core
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();

/**
 * @package   gantry
 * @subpackage core
 */
class GantryStyleLink {
    /**
     * type
     * @access private
     * @var string (url or local)
     */
    var $type;

    /**
     * Gets the type for gantry
     * @access public
     * @return type
     */
    function getType() {
        return $this->type;
    }

    /**
     * Sets the type for gantry
     * @access public
     * @param type $type
     */
    function setType($type) {
        $this->type = $type;
    }

    /**
     * The local filesystem path for the style link
     * @access private
     * @var string
     */
    var $path;

    /**
     * Gets the path for gantry
     * @access public
     * @return string
     */
    function getPath() {
        return $this->path;
    }

    /**
     * Sets the path for gantry
     * @access public
     * @param string $path
     */
    function setPath($path) {
        $this->path = $path;
    }

    /**
     * The url for the style link, local or full
     * @access private
     * @var string
     */
    var $url;

    /**
     * Gets the url for gantry
     * @access public
     * @return string
     */
    function getUrl() {
        return $this->url;
    }

    /**
     * Sets the url for gantry
     * @access public
     * @param string $url
     */
    function setUrl($url) {
        $this->url = $url;
    }

    function GantryStyleLink($type, $path, $url){
        $this->type = $type;
        $this->path = $path;
        $this->url = $url;
    }
}