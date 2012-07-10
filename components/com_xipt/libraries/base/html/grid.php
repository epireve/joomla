<?php

/**
* @copyright	Copyright (C) 2009 - 2009 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @contact		shyam@joomlaxi.com
*/

defined('JPATH_BASE') or die;

require_once JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'html' . DS . 'grid.php';

class XiHtmlGrid extends JHTMLGrid
{
	function switchBool( &$row,$what , $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= $row->$what ? $imgY : $imgX;
		$task 	= $row->$what ? 'switchOff'.$what : 'switchOn'.$what;
		$alt 	= $row->$what ? XiText::_( 'Switchon'.$what ) : XiptText::_( 'Switchoff'.$what);
		$action = $row->$what ? XiText::_( 'Switch off '.$what.' Item' ) : XiText::_( 'Switch on '.$what.' Item' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}
	
	
	function checkedOut( &$row, $i, $identifier = 'id' )
	{
		$user   =& JFactory::getUser();
		$userid = $user->get('id');

		$result = false;
		if(is_a($row, 'XiTable')) {
			$result = $row->isCheckedOut($userid);
		} else {
			$result = XiTable::isCheckedOut($userid, $row->checked_out);
		}

		$checked = '';
		if ( $result ) {
			$checked = self::_checkedOut( $row );
		} else {
			$checked = XiHTML::_('grid.id', $i, $row->$identifier );
		}

		return $checked;
	}
	
	
	function _checkedOut( &$row, $overlib = 1 )
	{
		$hover = '';
		if ( $overlib )
		{
			$text = addslashes(htmlspecialchars($row->checked_out));

			$date 	= XiHTML::_('date',  $row->checked_out_time, XiptText::_('DATE_FORMAT_LC1') );
			$time	= XiHTML::_('date',  $row->checked_out_time, '%H:%M' );

			$hover = '<span class="editlinktip hasTip" title="'. XiText::_( 'CHECKED_OUT' ) .'::'. $text .'<br />'. $date .'<br />'. $time .'">';
		}
		$checked = $hover .'<img src="images/checked_out.png"/></span>';

		return $checked;
	}
}
