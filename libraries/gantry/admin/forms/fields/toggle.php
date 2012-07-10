<?php
/**
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();


/**
 * Renders a toggle element
 *
 * @package     gantry
 * @subpackage  admin.elements
 */
gantry_import('core.config.gantryformfield');

class GantryFormFieldToggle extends GantryFormField {

    protected $type = 'toggle';
    protected $basetype = 'checkbox';

	public function getInput(){
		global $gantry;
		$hidden = '<input type="hidden" name="'.$this->name.'" value="_" />';
		
		$options = array ();
        $options[] = array('value'=>1,'text'=>'On/Off','id'=>$this->element->name);


		if (!defined('GANTRY_TOGGLE')) {
			$this->template = end(explode(DS, $gantry->templatePath));
			
            $gantry->addScript($gantry->gantryUrl.'/admin/widgets/toggle/js/touch.js');
            $gantry->addScript($gantry->gantryUrl.'/admin/widgets/toggle/js/toggle.js');
            define('GANTRY_TOGGLE',1);
        }

		
		//$gantry->addDomReadyScript($this->toggleInit($this->id));
		
		$checked = ($this->value == 0) ? '' : 'checked="checked"';
		if ($this->value == 0) $toggleStatus = 'unchecked';
		else $toggleStatus = 'checked';
		
		if ($this->detached) $disabledField = ' disabled';
		else $disabledField = '';
		
		return '
		<div class="wrapper">'."\n".'
			<div class="toggle">'."\n".'
				<div class="toggle-container toggle-'.$toggleStatus.$disabledField.'">'."\n".'
					<div class="toggle-sides">'."\n".'
						<div class="toggle-wrapper">'."\n".'
							<div class="toggle-switch"></div>'."\n".'
							<input type="hidden" name="'.$this->name.'" value="'.$this->value.'" />'."\n".'
							<input type="checkbox" class="toggle-input" id="'.$this->id.'" value="'.$this->value.'" '.$checked.' />'."\n".'
						</div>'."\n".'
						<div class="toggle-button"></div>'."\n".'
					</div>'."\n".'
				</div>'."\n".'
			</div>'."\n".'
		</div>'."\n".'
		';
    }

    public static function initialize(){

    }

    public static function finalize(){
		global $gantry;
		$gantry->addDomReadyScript("window.gantryToggles = new Toggle();");
    }

}
