<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version		3.2.11 September 8, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureMoreArticles extends GantryFeature {
    var $_feature_name = 'morearticles';
	
	function init() {
		global $gantry;
		
		if ($this->get('enabled')) {
			
			$gantry->addScript('gantry-morearticles.js');
            $queryUrl =  JROUTE::_($gantry->addQueryStringParams($gantry->getCurrentUrl($gantry->_setbyurl),array('tmpl'=>'component', 'type'=>'raw')));
			$gantry->addInlineScript("window.addEvent('domready', function() { new GantryMoreArticles({'leadings': ".$this->_getCurrentLeadingArticles().", 'moreText': '".addslashes($this->get('text'))."', 'url': '".$queryUrl."'}); })");
			
			if ($gantry->get('morearticles-pagination')) {
				$gantry->addInlineStyle('.rt-pagination {display: none;}');
			}
		}
	}
	
	function isOrderable() {
		return false;
	}
	
	function _getCurrentLeadingArticles(){
		$num_leading_articles = false;
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		if (null != $menu){
			$params = new JParameter($menu->params);
			$num_leading_articles = $params->get('num_leading_articles',0) + $params->get('num_intro_articles',0);
		}
		return ($num_leading_articles !== false ? $num_leading_articles : 0);
	}
}