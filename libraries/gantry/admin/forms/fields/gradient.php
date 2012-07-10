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

class GantryFormFieldGradient extends GantryFormField {

    protected $type = 'gradient';
    protected $basetype = 'none';

	public function getInput()
	{
		global $gantry;
		
		if (!defined('GANTRY_GRADIENT')) {
			
			$gantry->addScript($gantry->gantryUrl.'/admin/widgets/gradient/js/gradient.js');
				
			define('GANTRY_GRADIENT',1);
		}
		
		$gantry->addDomReadyScript($this->_jsInit());
		
		$output = "<div id=\"".$this->id."\" class=\"gradient-preview\"></div>\n";
		
		return $output;
	}
	
	function _jsInit() {
		$name2 = str_replace("_preview", "", $this->id);

		$js = "GantryGradient.add('".$this->id."', '".$name2."');";

		return $js;
	}
	
	public function getLabel(){
		return "";
    }
}

?>