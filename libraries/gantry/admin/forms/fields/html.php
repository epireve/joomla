<?php
/**
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();
/**
 * @package     gantry
 * @subpackage  admin.elements
 */
gantry_import('core.config.gantryformfield');

class GantryFormFieldHTML extends GantryFormField {
	

    protected $type = 'html';
    protected $basetype = 'none';

	public function getInput(){
		global $gantry;
		
		$html = (string)$this->element->html;
		
		// version
		$html = str_replace("{template_version}", $gantry->_template->getVersion(), $html);
		
		// template name
		$html = str_replace("{template_name}", $gantry->get('template_full_name'), $html);
				
		// preview
		$html = str_replace("{template_preview}", $gantry->templateUrl . '/screenshot.png', $html);
		
		return "<div class='html'>".$html."</div>";
	}
	
	public function getLabel(){
        return "";
    }
	
}