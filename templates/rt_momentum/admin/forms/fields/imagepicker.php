<?php
/**
 * @package     gantry
 * @subpackage  admin.elements
 * @version        1.3 October 12, 2011
 * @author        RocketTheme http://www.rockettheme.com
 * @copyright     Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();
/**
 * @package     gantry
 * @subpackage  admin.elements
 */

gantry_import('core.config.gantryformfield');
class GantryFormFieldImagePicker extends GantryFormField {

    protected $type = 'imagepicker';
    protected $basetype = 'imagepicker';

	function getInput(){
		JHTML::_('behavior.modal');
		global $gantry;

		$com_rokgallery = JComponentHelper::getComponent('com_rokgallery');
		$layout = $link = $dropdown = "";
		$options = $choices = array();
		$nomargin = false;
		$rokgallery = !isset($com_rokgallery->option) ? false : ($com_rokgallery->option !== null) ? true : false;
		//$rokgallery = false; // debug

		$value = str_replace("'", '"', $this->value);
		$data = json_decode($value);
		if (!$data && strlen($value)){
			$nomargin = true;
			$data = json_decode('{"path":"'.$value.'"}');
		}
		$preview = "";
		$preview_width = 'width="50"';
		$preview_height = 'height="50"';

		if (!$data && (!isset($data->preview) || !isset($data->path))) $preview = $gantry->templateUrl . '/admin/forms/fields/imagepicker/images/no-image.jpg';
		else if (isset($data->preview)) $preview = $data->preview;
		else {
			$preview = JURI::root(true) . '/' . $data->path;
			$preview_height = "";
		}


		if (!defined('ELEMENT_RTIMAGEPICKER')){
			gantry_addStyle($gantry->templateUrl . '/admin/forms/fields/imagepicker/css/imagepicker.css');

			gantry_addInlineScript("
			if (typeof jInsertEditorText == 'undefined'){
				function jInsertEditorText(text, editor) {
					var source = text.match(/(src)=(\"[^\"]*\")/i), img;
					text = source[2].replace(/\\\"/g, '');
					img = '".JURI::root(true)."/' + text;

					document.getElementById(editor + '-img').src = img;
					document.getElementById(editor + '-img').removeProperty('height');
					document.getElementById(editor).value = JSON.encode({path: text});
				};
			};
			");

			gantry_addInlineScript("
				var AdminURI = '".JURI::base(true)."/';
				var GalleryPickerInsertText = function(input, string, size, minithumb){
					var data = {
						path: string,
						width: size.width,
						height: size.height,
						preview: minithumb
					};

					document.getElementById(input + '-img').src = minithumb;
					document.getElementById(input + '-infos').innerHTML = data.width + ' x ' + data.height;
					document.getElementById(input).value = JSON.encode(data);

				};

				var empty_background_img = '".$gantry->templateUrl."/admin/forms/fields/imagepicker/images/no-image.jpg';
				window.addEvent('domready', function(){
					document.id('".$this->id."').addEvent('keyup', function(value){
						document.id('".$this->id."-infos').innerHTML = '';
						if (!value || !value.length) document.id('".$this->id."-img').set('src', empty_background_img);
						else {
							var data = JSON.decode(value);
							document.id('".$this->id."-img').set('src', (data.preview ? data.preview : '".JURI::root(true)."/' + data.path));
							if (!data.preview){
								document.id('".$this->id."-img').removeProperty('height');
							} else {
								document.id('".$this->id."-img').set('height', '50');
								if (data.width && data.height) document.id('".$this->id."-infos').innerHTML = data.width + ' x ' + data.height;
							}
						}

						this.setProperty('value', value);
					});

					document.id('".$this->id."-clear').addEvent('click', function(e){
						e.stop();
						document.id('".$this->id."').set('value', '').fireEvent('set', '');
					});

					var dropdown = document.id('".$this->id."mediatype');
					if (dropdown){
						dropdown.addEvent('change', function(){
							document.id('".$this->id."-link').set('href', this.value);
						});
					}
				});
			");
            
            define('ELEMENT_RTIMAGEPICKER', true);
        }

        if ($rokgallery) $link = 'index.php?option=com_rokgallery&view=gallerypicker&tmpl=component&show_menuitems=0&inputfield=' . $this->id;
        else $link = "index.php?option=com_media&view=images&layout=default&tmpl=component&e_name=" . $this->id;

        if ($rokgallery){
			$choices = array(
				array('RokGallery', 'index.php?option=com_rokgallery&view=gallerypicker&tmpl=component&show_menuitems=0&inputfield=' . $this->id),
		    	array('MediaManager', 'index.php?option=com_media&view=images&layout=default&tmpl=component&e_name=' . $this->id)
		    );

			foreach ($choices as $option){
				$options[] = GantryHtmlSelect::option($option[1], $option[0], 'value', 'text');
			}

			include_once($gantry->gantryPath.DS.'admin'.DS.'forms'.DS.'fields'.DS.'selectbox.php');
			$selectbox = new GantryFormFieldSelectBox;
			$selectbox->id = $this->id . 'mediatype';
			$selectbox->value = $link;
			$selectbox->addOptions($options);
			$dropdown = '<div id="'.$this->id.'-mediadropdown" class="mediadropdown">'.$selectbox->getInput() ."</div>";
        }

        $value = str_replace('"', "'", $value);
		$layout .= '
			<div class="wrapper">'."\n".'
				<div id="' . $this->id . '-wrapper" class="backgroundpicker">'."\n".'
					<img id="'.$this->id.'-img" class="backgroundpicker-img" '.$preview_width.' '.$preview_height.' alt="" src="'.$preview.'" />
					
					<div id="'.$this->id.'-infos" class="backgroundpicker-infos" '.($rokgallery && !$nomargin ? 'style="display:block;"' : 'style="display:none;"').' >'
						.((isset($data->width) && (isset($data->height))) ? $data->width.' x '.$data->height : '').
					'</div>


					<a id="'.$this->id.'-link" href="'.$link.'" rel="{handler: \'iframe\', size: {x: 675, y: 450}}" class="bg-button modal">'."\n".'
						<span class="bg-button-right">'."\n".'
							Select
						</span>'."\n".'
					</a>'."\n".'
					<a id="'.$this->id.'-clear" href="#" class="bg-button bg-button-clear">'."\n".'
					<span class="bg-button-right">'."\n".'
							Reset
						</span>'."\n".'
					</a>'."\n".'

					'.$dropdown.'

					<input class="background-picker" type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="' . $value . '" />'."\n".'
					<div class="clr"></div>
				</div>'."\n".'
			</div>'."\n".'
		';

		return $layout;
	}

}

?>