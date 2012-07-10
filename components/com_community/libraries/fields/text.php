<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'fields'.DS.'profilefield.php');
class CFieldsText extends CProfileField
{
	public function getFieldHTML( $field , $required )
	{
		$params	= new CParameter($field->params);
		
		$readonly	= $params->get('readonly') ? ' readonly=""' : '';
		$style 				= $this->getStyle()?'':' style="' .$this->getStyle() . '" ';
		
		// If maximum is not set, we define it to a default
		$field->max	= empty( $field->max ) ? 200 : $field->max;
		CFactory::load( 'helpers' , 'string' );
		$class	= ($field->required == 1) ? ' required' : '';
		$class	.= !empty( $field->tips ) ? ' jomNameTips tipRight' : '';
		$tooltipcss = "";
		if(!empty($field->tips)){
		    $tooltipcss = "jomNameTips";
		}
		
		$html	= '<input title="' . CStringHelper::escape( JText::_( $field->tips ) ).'" type="text" value="' . $field->value . '" id="field' . $field->id . '" name="field' . $field->id . '" maxlength="' . $field->max . '" size="40" class="'.$tooltipcss.' tipRight inputbox' . $class . '" '.$style.$readonly.' />';
		$html   .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		
		return $html;
	}
	
	public function isValid( $value , $required )
	{
		if( $required && empty($value))
		{
			return false;
		}	
		//validate string length
		if(!$this->validLength($value)){
			return false;
		}		
		return true;
	}
}