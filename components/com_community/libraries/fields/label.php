<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'fields'.DS.'profilefield.php');
class CFieldsLabel extends CProfileField
{
	public function getFieldHTML( $field , $required )
	{
		CFactory::load( 'helpers' , 'string' );
		$class	= !empty( $field->tips ) ? ' jomNameTips tipRight' : '';
		
		$html	= '<textarea title="' . CStringHelper::escape( JText::_( $field->tips ) ) .'" id="field' . $field->id . '" name="field' . $field->id . '"  class="textarea inputbox' . $class . '" cols="20" rows="5" readonly="readonly">' . CStringHelper::escape( $field->tips ) . '</textarea>';
		$html   .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';

		return $html;
	}
}
