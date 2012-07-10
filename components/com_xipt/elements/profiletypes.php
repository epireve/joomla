<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementProfiletypes extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Profiletypes';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$reqnone     = false;
		$reqall  	 = false;
		$multiselect = false;
		if(isset($node->_attributes->addnone) || isset($node->_attributes['addnone']))
			$reqnone = true;
			
		if(isset($node->_attributes->addall) || isset($node->_attributes['addall']))
			$reqall = true;
			
		if(isset($node->_attributes->multiselect) || isset($node->_attributes['multiselect']))
			$multiselect = true;
			
		$ptypeHtml = $this->getProfiletypeFieldHTML($name,$value,$control_name,$reqnone,$reqall,$multiselect);

		return $ptypeHtml;
	}
	
	
	function getProfiletypeFieldHTML($name,$value,$control_name='params',$reqnone=false,$reqall=false,$multiselect=false)
	{	
		$required			='1';
		$html				= '';
		$class				= ($required == 1) ? ' required' : '';
		$options			= XiptLibProfiletypes::getProfiletypeArray();
		
		if($multiselect)
			$html .= '<select id="'.$control_name.'['.$name.'][]" name="'.$control_name.'['.$name.'][]" value="" style="margin: 0 5px 5px 0;"  size="3" multiple/>';
		else
			$html	.= '<select id="'.$control_name.'['.$name.']" name="'.$control_name.'['.$name.']" title="' . "Select Account Type" . '::' . "Please Select your account type" . '">';
		
		if($reqall) {
			$selected	= ( JString::trim(0) == $value ) ? ' selected="true"' : '';
			$html	.= '<option value="' . 0 . '"' . $selected . '>' . XiptText::_("ALL") . '</option>';
		}
		
		if($reqnone) {
			$selected	= ( JString::trim(-1) == $value ) ? ' selected="true"' : '';
			$html	.= '<option value="' . -1 . '"' . $selected . '>' . XiptText::_("NONE") . '</option>';
		}
		
		foreach($options as $op)
		{
		    $option		= $op->name;
			$id			= $op->id;
		    
			if(!is_array($value))
				$value = array($value);
				
		    $selected	= (in_array($id, $value)) ? ' selected="true"' : '';
		    
		    if($multiselect)
		    {
		    	$html .= '<option name="'.$name.'_'.$id.'" "'.$selected.'" value="'.$id.'">' ;  
				$html .= $option.'</option>';
		    }
		    else
				$html	.= '<option value="' . $id . '"' . $selected . '>' . $option . '</option>';
		}
			
		$html	.= '</select>';	
		$html   .= '<span id="errprofiletypemsg" style="display: none;">&nbsp;</span>';
		
		return $html;
	}
}