<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementWatermarkPreview extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Image';

	function fetchElement($name, $value, &$node, $control_name)
	{
		/*value contain profiletype id so get watermark from function */
		$watermark 		 = XiptHelperProfiletypes::getProfileTypeData($value,'watermark'); 
		$generatedImage  = '';
		/*generate image from watermark */
		$imagePath 	     = JPATH_ROOT.DS.DEFAULT_DEMOAVATAR;
		$watermarkPath   = JPATH_ROOT.DS.$watermark;
		
		$watermarkParams = XiptLibProfiletypes::getParams($value,'watermarkparams');
		
		if(JFile::exists($imagePath) && JFile::exists($watermarkPath))
			$generatedImage = XiptHelperImage::showWatermarkOverImage($imagePath,$watermarkPath,'ptype_'.$value,$watermarkParams->get('xiWatermarkPosition'));
		if(DS == '\\')
			$generatedImage = str_replace('\\','/',$generatedImage);
		
		$html = '';
		if($generatedImage == false || $generatedImage == '')
			$generatedImage = DEFAULT_DEMOAVATAR;

		$html .= '<img src="'.JURI::root().'/'.$generatedImage.'" width="64" height="64" alt="generatedimage" border=10 />';
		

		return $html;
	}
	
}
