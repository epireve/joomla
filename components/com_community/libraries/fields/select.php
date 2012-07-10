<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'fields'.DS.'profilefield.php');
class CFieldsSelect extends CProfileField
{
	public function getFieldHTML( $field , $required, $isDropDown = true)
	{		
		$class		= ($field->required == 1) ? ' required' : '';
		$class	.= !empty( $field->tips ) ? ' jomNameTips tipRight' : '';		
		$optionSize	= 1; // the default 'select below'
		
		if( !empty( $field->options ) )
		{
			$optionSize	+= count($field->options);
		}
		
		$dropDown	= ($isDropDown) ? '' : ' size="'.$optionSize.'"';
		CFactory::load( 'helpers' , 'string' );
		$html		= '<select id="field'.$field->id.'" name="field' . $field->id . '"' . $dropDown . ' class="select'.$class.'" title="' . CStringHelper::escape( JText::_( $field->tips ) ). '" style="'.$this->getStyle().'" size="'.$this->params->get('size').'">';
		
		$defaultSelected	= '';
		
		//@rule: If there is no value, we need to default to a default value
		if(empty( $field->value ) )
		{
			$defaultSelected	.= ' selected="selected"';
		}
		
		if($isDropDown)
		{
			$html	.= '<option value="" ' . $defaultSelected . '>' . JText::_('COM_COMMUNITY_SELECT_BELOW') . '</option>';
		}	
		
		if( !empty( $field->options ) )
		{
			$selectedElement	= 0;
			CFactory::load( 'libraries' , 'template' );
			foreach( $field->options as $option )
			{
				$selected	= ( $option == $field->value ) ? ' selected="selected"' : '';
				
				if( !empty( $selected ) )
				{
					$selectedElement++;
				}
				
				$html	.= '<option value="' . CTemplate::escape( $option ) . '"' . $selected . '>' . JText::_( $option ) . '</option>';
			}
			
			if($selectedElement == 0)
			{
				//if nothing is selected, we default the 1st option to be selected.
				$eleName	= 'field'.$field->id;
				$html			.=<<< HTML
					   <script type='text/javascript'>
						   var slt = document.getElementById('$eleName');
						   if(slt != null)
						   {
						       slt.options[0].selected = true;
						   }
					   </script>
HTML;
			}
		}
		$html	.= '</select>';
		$html   .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		
		return $html;
	}
}
