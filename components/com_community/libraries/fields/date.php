<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.utilities.date');
require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'fields'.DS.'profilefield.php');
class CFieldsDate extends CProfileField
{
	/**
	 * Method to format the specified value for text type
	 **/	 	
	public function getFieldData( $field )
	{
		$value = $field['value'];
		if( empty( $value ) )
			return $value;
		
		if(! class_exists('CFactory'))
		{
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
		}
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'profile.php' );
				
		$model	= CFactory::getModel( 'profile' );				
		$myDate = $model->formatDate($value); 
		
		return $myDate;
	}
	
	public function getFieldHTML( $field , $required )
	{		
		$html	= '';
				
		$day	= '';
		$month	= 0;
		$year	= '';		

		if(! empty($field->value))
		{
		    if(! is_array($field->value))
		    {
				$myDateArr	= explode(' ', $field->value);
			}
			else
			{
			    $myDateArr[0]  = $field->value[2] . '-' . $field->value[1] . '-' . $field->value[0];
			}
			
			if(is_array($myDateArr) && count($myDateArr) > 0)
			{
				$myDate	= explode('-', $myDateArr[0]);
				
				$day	= !empty($myDate[2]) ? $myDate[2] : '';
				$month	= !empty($myDate[1]) ? $myDate[1] : 0;
				$year	= !empty($myDate[0]) ? $myDate[0] : '';								
			}
		}		
				
		$months	= Array(
						JText::_('COM_COMMUNITY_MONTH_JANUARY'),
						JText::_('COM_COMMUNITY_MONTH_FEBRUARY'),
						JText::_('COM_COMMUNITY_MONTH_MATCH'),
						JText::_('COM_COMMUNITY_MONTH_APRIL'),
						JText::_('COM_COMMUNITY_MONTH_MAY'),
						JText::_('COM_COMMUNITY_MONTH_JUNE'),
						JText::_('COM_COMMUNITY_MONTH_JULY'),
						JText::_('COM_COMMUNITY_MONTH_AUGUST'),
						JText::_('COM_COMMUNITY_MONTH_SEPTEMBER'),
						JText::_('COM_COMMUNITY_MONTH_OCTOBER'),
						JText::_('COM_COMMUNITY_MONTH_NOVEMBER'),
						JText::_('COM_COMMUNITY_MONTH_DECEMBER')
						);

        $class	= ($field->required == 1) ? ' required' : '';
        CFactory::load( 'helpers' , 'string' );
        
        //$class	= !empty( $field->tips ) ? ' jomNameTips tipRight' : '';
		$html .= '<div class="' . $class . '" style="display: inline-block; " title="' . CStringHelper::escape( JText::_( $field->tips ) ). '">';
		
		// Individual field should not have a tooltip
		//$class	= '';
		
		$html .= '<input type="text" size="3" maxlength="2" name="field' . $field->id . '[]" value="' . $day . '" class="inputbox validate-custom-date '.  $class .'" /> ' . JText::_('COM_COMMUNITY_DAY_FORMAT');
		$html .= '&nbsp;/&nbsp;<select name="field' . $field->id . '[]" class="select validate-custom-date' . $class . '">';

		$defaultSelected	= '';
		
		//@rule: If there is no value, we need to default to a default value
		if( $month == 0 )
		{
			$defaultSelected	.= ' selected="selected"';
		}
		$html	.= '<option value=""' . $defaultSelected . '>' . JText::_('COM_COMMUNITY_SELECT_BELOW') . '</option>';

		for( $i = 0; $i < count($months); $i++)
		{
			if(($i + 1)== $month)
			{
				$html .= '<option value="' . ($i + 1) . '" selected="selected">' . $months[$i] . '</option>';
			}
			else
			{
				$html .= '<option value="' . ($i + 1) . '">' . $months[$i] . '</option>';
			}
		}
		$html .= '</select>&nbsp;/&nbsp;';
		$html .= '<input type="text" size="5" maxlength="4" name="field' . $field->id . '[]" value="' . $year . '" class="inputbox validate-custom-date' . $class . '" /> ' . JText::_('COM_COMMUNITY_YEAR_FORMAT');
		$html .= '<span id="errfield'.$field->id.'msg" style="display:none;">&nbsp;</span>';
		$html .= '</div>';
		
		return $html;
	}
	
	public function isValid( $value , $required )
	{
		if( ($required && empty($value)) || !isset($this->fieldId))
		{
			return false;
		}
		
		$db		=& JFactory::getDBO();
		$query	= 'SELECT * FROM '.$db->nameQuote('#__community_fields')
				. ' WHERE '.$db->nameQuote('id').'='.$db->quote($this->fieldId);
		$db->setQuery($query);
		$field	= $db->loadAssoc();
		
		$params	= new CParameter($field['params']);
		$max_range = $params->get('maxrange');
		$min_range = $params->get('minrange');
		$value = JFactory::getDate(strtotime($value))->toUnix();
		$max_ok = true;
		$min_ok = true;

		//$ret = true;
		
		if ($max_range)
		{
			$max_range = JFactory::getDate(strtotime($max_range))->toUnix();
			$max_ok = ($value < $max_range);
		}
		if ($min_range)
		{
			$min_range = JFactory::getDate(strtotime($min_range))->toUnix();
			$min_ok = ($value > $min_range);
		}
		
		return ($max_ok && $min_ok) ? true : false;
		//return $ret;
	}
	
	public function formatdata( $value )
	{	
		$finalvalue = '';
		
		if(is_array($value))
		{
			if( empty( $value[0] ) || empty( $value[1] ) || empty( $value[2] ) )
			{
				$finalvalue = '';
			}
			else
			{
				$day	= intval($value[0]);
				$month	= intval($value[1]);
				$year	= intval($value[2]);
				
				$day 	= !empty($day) 		? $day 		: 1;
				$month 	= !empty($month) 	? $month 	: 1;
				$year 	= !empty($year) 	? $year 	: 1970;

				if( !checkdate($month, $day, $year) )
				{
					return $finalvalue;
				}
				
				$finalvalue	= $year . '-' . $month . '-' . $day . ' 23:59:59';
			}
		}
			
		return $finalvalue;	
	}
	
	public function getType()
	{
		return 'date';
	}
}
