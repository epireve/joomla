<?php
/**
 * @version   3.2.11 September 8, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die;

gantry_import('core.config.gantryformgroup');


class GantryFormGroupGrouped extends GantryFormGroup
{
    protected $type = 'grouped';
    protected $baseetype = 'group';

    public function getInput(){
        $buffer = '';

		$buffer .= "<div class='wrapper'>";
        foreach ($this->fields as $field) {
            $buffer .= '<div class="gantry-field">';
            if ($field->show_label) $buffer .= $this->preLabel($field).$field->getLabel().$this->postLabel($field)."\n";
            $buffer .= $field->getInput();
            $buffer .= "<div class='clr'></div>\n";
            $buffer .= "</div>";

        }
		$buffer .= "</div>";
        return $buffer;
    }
}