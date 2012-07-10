<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'fields'.DS.'profilefield.php');
class CFieldsRadio extends CProfileField
{
	public function getFieldHTML( $field , $required )
	{
		$html				= '';
		$selectedElement	= 0;		
		$class				= ($field->required == 1) ? ' required validate-custom-radio ' : '';
		$elementSelected	= 0;
		$elementCnt	        = 0;		
		$style 				= ' style="margin: 0 5px 0 0;' .$this->getStyle() . '" ';
		for( $i = 0; $i < count( $field->options ); $i++ )
		{
		    $option		= $field->options[ $i ];
			$selected	= ( $option == $field->value ) ? ' checked="checked"' : '';
			
			if( empty( $selected ) )
			{
				$elementSelected++;
			}			
			$elementCnt++;				
		}
		
		
		$cnt = 0;
		CFactory::load( 'helpers' , 'string' );
		$class	.= !empty( $field->tips ) ? 'jomNameTips tipRight' : '';
		$html	.= '<div class="' . $class . '" style="display: inline-block;" title="' . CStringHelper::escape( JText::_( $field->tips ) ). '">';
		for( $i = 0; $i < count( $field->options ); $i++ )
		{
		    $option		= $field->options[ $i ];
			
			$selected	= ( $option == $field->value ) ? ' checked="checked"' : '';		    		    
			
			$html 	.= '<label class="lblradio-block">';
			$html	.= '<input type="radio" name="field' . $field->id . '" value="' . $option . '"' . $selected . '  class="radio '.$class.'" ' . $style . ' />';
			$html	.= JText::_( $option ) . '</label>';
		}
		$html   .= '<span id="errfield'.$field->id.'msg" style="display: none;">&nbsp;</span>';
		$html	.= '</div>';				
		
		return $html;
	}
	
	public function isValid( $value , $required )
	{
		if( $required && empty($value))
		{
			return false;
		}		
		return true;
	}
}