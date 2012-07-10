<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined('_JEXEC') or die();
?>
<script type="text/javascript" src="<?php echo JURI::root();?>components/com_xipt/elements/colorbox/jscolor.js"></script>

<?php 
class JElementColorbox extends JElement
{
	var	$_name = 'Colorbox';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$html = '<input class="color" type="text" id="'.$control_name.'['.$name.']" name="'.$control_name.'['.$name.']" value="'.$value.'" />';

		return $html;
	}
	
}