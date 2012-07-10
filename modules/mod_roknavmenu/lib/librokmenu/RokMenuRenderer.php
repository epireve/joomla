<?php
/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!interface_exists('RokMenuRenderer')) {

    /**
     * The base class for all data providers for menus
     */
    interface RokMenuRenderer {
        /**
         * @abstract
         * @return void
         */
        public function __construct();

        /**
         * @abstract
         * @param  $args
         * @return void
         */
        public function setArgs(array &$args);

        /**
         * @abstract
         * @return void
         */
        public function initialize(RokMenuProvider $provider);

        /**
         * @abstract
         * @return string
         */
        public function renderHeader();

        /**
         * @abstract
         * @return string
         */
        public function renderMenu();

        /**
         * @abstract
         * @return string
         */
        public function renderFooter();

        /**
         * @abstract
         * @return array
         */
        public function getDefaults();

    }
}