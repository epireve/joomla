<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CAjaxHelper
{
	static public function toArray($data)
	{
		$newData = array();
		foreach($data as $val){
			if(is_array($val))
				$newData[$val[0]] = isset($val[1]) ? $val[1] : ''; 
		}
		
		return $newData;
	}
}
