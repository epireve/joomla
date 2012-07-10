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

class GantryFormFieldColorChooser extends GantryFormField {

    protected $type = 'colorchooser';
    protected $basetype = 'text';

	public function getInput(){
        //($name, $value, &$node, $control_name)
		//global $stylesList;
        /**
         * @global Gantry $gantry
         */
		global $gantry;
		$output = '';

		$this->template = end(explode(DS, $gantry->templatePath));
		$transparent = 1;
		
		if ($this->element->attributes('transparent') == 'false') $transparent = 0;
            if (!defined('GANTRY_CSS')) {
			$gantry->addStyle($gantry->gantryUrl.'/admin/widgets/gantry.css');
			define('GANTRY_CSS', 1);
		}
		
		if (!defined('GANTRY_MOORAINBOW')) {
			
			$gantry->addStyle($gantry->gantryUrl.'/admin/widgets/colorchooser/css/mooRainbow.css');
			$gantry->addScript($gantry->gantryUrl.'/admin/widgets/colorchooser/js/mooRainbow.js');
			$gantry->addScript($gantry->gantryUrl.'/admin/widgets/colorchooser/js/colorchooser.js');
			
			define('GANTRY_MOORAINBOW',1);
		}
				
		$gantry->addDomReadyScript("GantryColorChooser.add('".$this->id."', ".$transparent.");");

		$output .= "<div class='wrapper'>";
		$output .= "<input class=\"picker-input text-color\" id=\"".$this->id."\" name=\"".$this->name."\" type=\"text\" size=\"7\" maxlength=\"11\" value=\"".$this->value."\" />";
		$output .= "<div class=\"picker\" id=\"myRainbow_".$this->id."_input\"><div class=\"overlay".(($this->value == 'transparent') ? ' overlay-transparent' : '')."\" style=\"background-color: ".$this->value."\"><div></div></div></div>\n";
		$output .= "</div>";
		
		return $output;
	}
}