<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.utilities.date');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'fields' . DS.'date.php');

class CFieldsBirthdate extends CFieldsDate
{
	public function getFieldData( $field )
	{
		$value = $field['value'];
		
		if( empty( $value ) )
			return $value;
		
		$params	= new CParameter($field['params']);
		$format = $params->get('display');
		
		if(! class_exists('CFactory'))
		{
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
		}
		
		$ret = '';
		
		if ($format == 'age')
		{
			// PHP version > 5.2
			$datetime	= new DateTime( $value );
			$now		= new DateTime( 'now' );
			
			// PHP version > 5.3
			if (method_exists($datetime, 'diff'))
			{
				$interval	= $datetime->diff($now);
				$ret		= $interval->format('%Y');
			} else {
				$mth		= $now->format( 'm' ) - $datetime->format( 'm');
				$day		= $now->format( 'd' ) - $datetime->format( 'd');
				$ret		= $now->format( 'Y' ) - $datetime->format( 'Y');
				
				if($mth >= 0){
					if($day < 0 && $mth == 0){
						$ret--;
					}
				}else{
					$ret--;
				}
			}
		}
		else
		{
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'profile.php' );
			$model	= CFactory::getModel( 'profile' );				
			
			//overwrite Profile date format in Configuration
			$format = $params->get('date_format');
			$ret = $model->formatDate($value,$format);
		}
		
		return $ret;
	}
	public function isValid( $value , $required )
	{
		if( ($required && empty($value)) || !isset($this->fieldId))
		{
			return false;
		}
				
		$max_range = $this->params->get('maxrange');
		$min_range = $this->params->get('minrange');
		$value = JFactory::getDate(strtotime($value))->toUnix();
		$max_ok = true;
		$min_ok = true;

		//$ret = true;
		
		if ($max_range)
		{
			if(strtotime($max_range)){
				$max_range = JFactory::getDate(strtotime($max_range))->toUnix();
				$max_ok = ($value < $max_range);
			} elseif (is_numeric($max_range) && intval($max_range) > 0){
				//consider as age format
				$datetime = new Datetime();
				$datetime->modify('-'.$max_range . ' year');
				$max_range = $datetime->format('U');
				//revert the age comparation
				$max_ok = ($value > $max_range);
			} else {
				$max_range = 0;
			}
			
		}
		if ($min_range)
		{
			if(strtotime($min_range)){
				$min_range = JFactory::getDate(strtotime($min_range))->toUnix();
				$min_ok = ($value > $min_range);
			} elseif (is_numeric($min_range) && intval($min_range) > 0){
				//consider as age format
				$datetime = new Datetime();
				$datetime->modify('-'.$min_range . ' year');
				$min_range = $datetime->format('U');
				//revert the age comparation
				$min_ok = ($value < $min_range);
			} else {
				$min_range = 0;
			}
		}
		
		return ($max_ok && $min_ok) ? true : false;
		//return $ret;
	}	
}