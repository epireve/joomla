<?php
/**
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.utilities.date');

class CDatetime extends JDate
{
	private $_datetime = false;
	private $_current = false;
	
	public function __construct()
	{
		parent::__construct();
		$this->setDate('now');
		$this->_current = isset($this->_date)?$this->_date:$this->format('U');
	}
	
	public function setDate($time, $tzOffset=0)
	{
		$this->_datetime = getdate(JFactory::getDate($time, $tzOffset)->toUnix());
	}
	
	public function reset()
	{
		$this->_date = $this->_current;
		$this->_datetime = getdate($this->_current);
	}
	
	public function manipulate($interval, $amount)
	{
		$amount = intval($amount);
		
		switch ($interval)
		{
			case 'year':
				$this->_datetime['year'] += $amount;
				break;
			case 'month':
				$this->_datetime['mon'] += $amount;
				break;
			case 'day':
				$this->_datetime['mday'] += $amount;
				break;
			case 'hour':
				$this->_datetime['hours'] += $amount;
				break;
			case 'minute':
				$this->_datetime['minutes'] += $amount;
				break;
			case 'second':
				$this->_datetime['seconds'] += $amount;
				break;
			default:
				break;
		}
		
		$this->_date = mktime($this->_datetime['hours'],$this->_datetime['minutes'],$this->_datetime['seconds'],$this->_datetime['mon'],$this->_datetime['mday'],$this->_datetime['year']);
	}
	
}
