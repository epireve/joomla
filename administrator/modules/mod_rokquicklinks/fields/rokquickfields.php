<?php
/**
 * @package RokQuickLinks - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );

class JFormFieldRokQuickFields extends JFormField {
	
	var	$_name = 'rokquickfields';
	var $directory = null;
	
	public function getInput(){
		
		$document 	=& JFactory::getDocument();
		$path = JURI::Root(true)."/administrator/modules/mod_rokquicklinks/";
		$document->addStyleSheet($path.'admin/css/quickfields.css');
		$document->addScript($path.'admin/js/quickfields'.$this->_getJSVersion().'.js');
		
		$value = str_replace("'", '"', $this->value);
		$this->directory = (string) $this->element->attributes()->directory;

		$output = "";
		
		// hackish way to close tables that we don't want to use
		//$output .= '</td></tr><tr><td colspan="2">';
		
		// real layout
		$output .= '<div id="quicklinks-admin">'."\n";
		$output .=  $this->populate($value);
		$output .= '</div>'."\n";
		
		$output .= "<input id='quicklinks-dir' value='".JURI::Root(true).$this->directory ."' type='hidden' />";
		$output .= "<input id='".$this->id."' name='".$this->name."' type='hidden' value='".($value)."' />";
		
		echo $output;
	}
	
	function populate($value){
		$blocks = json_decode($value, true);
		$output = '';
		
		for($i = 1; $i <= count($blocks); $i++){
			$output .= $this->layout($blocks[$i - 1], $i);
		}
		
		return $output;
	}
	
	function populateIcons($selectedIcon = false){
		$path = JPATH_ROOT . str_replace('/', DS, $this->directory);
		$icons = scandir($path);
		$output = '';
		
		foreach($icons as $icon){
			$pathinfo = pathinfo($icon);
			$ext = $pathinfo['extension'];
			
			if ($ext == 'png' || $ext == 'jpg' || $ext == 'bmp' || $ext == 'gif'){
				if (basename($selectedIcon) == $pathinfo['filename'] . "." . $ext) $selected = ' selected="selected"';
				else $selected = '';
				
				$output .= '<option value="'.$pathinfo['basename'].'"'.$selected.'>'.$pathinfo['filename'].'</option>'."\n";
			}
		}
		
		return $output;
	}
	
	function layout($block, $index){
		$icon = JUri::root(true) . $this->directory . $block['icon'];
		$title = $block['title'];
		$link = $block['link'];
		
		return '
			<div class="quicklinks-block">
				<div class="quicklinks-icon"><img src="'.$icon.'" /></div>
				<div class="quicklinks-title">
					<span>'.JTEXT::_('MC_RQL_TITLE').'</span>
					<input class="text_area quick-input" id="jform_params_title-'.$index.'" name="jform[params][title-'.$index.']" value="'.$title.'" type="text" />
				</div>
				<div class="quicklinks-link">
					<span>'.JTEXT::_('MC_RQL_LINK').'</span>
					<input class="text_area quick-input" id="jform_params_link-'.$index.'" name="jform[params][link-'.$index.']" value="'.$link.'" type="text" />
				</div>
				<div class="quicklinks-iconslist">
					<span>'.JTEXT::_('MC_RQL_ICON').'</span>
					<select class="inputbox quicklinks-select" id="jform_params_icon-'.$index.'" name="jform[params][icon-'.$index.']">
						'.$this->populateIcons($icon).'
					</select>
				</div>
				
				<div class="quicklinks-controls">
					<div class="quicklinks-add"></div>
					<div class="quicklinks-remove"></div>
				</div>
				<div class="quicklinks-move"></div>
			</div>
		';
	}
	
	function _getJSVersion() {
		if (version_compare(JVERSION, '1.5', '>=') && version_compare(JVERSION, '1.6', '<')){
			if (JPluginHelper::isEnabled('system', 'mtupgrade')){
				return "-mt1.2";
			} else {
				return "";
			}
		} else {
			return "";
		}
	}
	
}