<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'fields'.DS.'profilefield.php');
class CFieldsTextarea extends CProfileField
{
	public function getFieldHTML( $field , $required )
	{
		$params	= new CParameter($field->params);
		$readonly	= $params->get('readonly') ? ' readonly=""' : '';
		$style 				= $this->getStyle()?'':' style="' .$this->getStyle() . '" ';
		
		$config	= CFactory::getConfig();
		$js	= 'assets/validate-1.5';
		$js .= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
		CAssets::attach($js, 'js');
		
		// If maximum is not set, we define it to a default
		$field->max	= empty( $field->max ) ? 200 : $field->max;
	 
		$class	= ($field->required == 1) ? ' required' : '';
		$class	.= !empty( $field->tips ) ? ' jomNameTips tipRight' : '';
		CFactory::load( 'helpers' , 'string' );
		$html	= '<textarea id="field' . $field->id . '" name="field' . $field->id . '" class="inputbox textarea' . $class . '" title="' . CStringHelper::escape( JText::_( $field->tips ) ) . '"'.$style.$readonly.'>' . $field->value . '</textarea>';
		$html   .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		$html	.= '<script type="text/javascript">cvalidate.setMaxLength("#field' . $field->id . '", "' . $field->max . '");</script>';
		
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
