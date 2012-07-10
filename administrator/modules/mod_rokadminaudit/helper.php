<?php
/**
 * @package RokAdminAudit - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );

class rokAdminAuditHelper
{
	function getRows(&$params, $start = 0, $limit, $detail_filter) {
	
		$db = JFactory::getDBO();
		$session =& JFactory::getSession();
		
		if (!isset($limit)) $limit = intval($params->get('limit', 5));
		if (!isset($detail_filter)) $detail_filter = $params->get('detail_filter', 'low');
		$where = '';
		
		if ($detail_filter == 'low') {
			$where = 'and (r.task = "apply" or r.task = "save")';
		} elseif ($detail_filter == 'medium') {
			$where = 'and (r.task != "cancel" and r.task != "preview" and r.option != "com_cpanel")';
		}
		
		// get admin activity
		$query = 'select r.*, u.name, u.username, u.email,e.name as extension from #__rokadminaudit as r, #__users as u, #__extensions as e where r.user_id = u.id and e.element = r.option '.$where.' order by id desc limit '. intval($limit);
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		// rows count
		$query = 'select count(*), r.*, u.name, u.username, u.email,e.name as extension from #__rokadminaudit as r, #__users as u, #__extensions as e where r.user_id = u.id and e.element = r.option '.$where.' order by id desc';
		$db->setQuery($query);
		$count = $db->loadResult();
		
		return array('rows' => $rows, 'count' => $count);

	}
	
	function getDescription(&$row) {

	
		$title = '';
		$task = 'Undefined Task';
		$tasks = array('' => 'Default View',
					   'cancel' => 'Canceled',
					   'preview' => 'Previewed',
					   'edit' => 'Edited',
					   'save' => 'Saved',
					   'apply' => 'Saved');
					   
		if ($row->task == 'save' or $row->task == 'apply') {
			$link = $row->referrer;
			
			$matches = parse_url($link);

			if (array_key_exists('query',$matches)) {
				
				$query = $matches['query'];
				
				//var_dump ($query);
				
				$qbits = rokAdminAuditHelper::parseQueryString($query);
				if (isset($qbits['layout']) && $qbits['layout']=='edit' && isset($qbits['view'])) {
					$qbits['task'] = $qbits['view'].".".$qbits['layout'];
					unset($qbits['layout']);
					unset($qbits['view']);
					
					$query = rokAdminAuditHelper::rebuildQueryString($qbits);
					$link = $matches['path']."?".$query;
					
				}

			}

			
		} else {
			$link = $row->page;
		}
		
		$extension = $row->extension;
		
		if (strpos($extension,'com_') === 0) {
			$extension = rokAdminAuditHelper::camelCase(substr($extension,4));
		}
		
		
		if (isset($row->title) && $row->title != '') $title = ': <em>'.$row->title.'</em>';
		if (isset($tasks[$row->task])) $task = $tasks[$row->task];
		if ($row->option == 'com_cpanel') $extension = JText::_('Site Dashboard');
		
		return $extension.': <a href="'.JRoute::_($link).'">'.$task.$title.'</a>';
	
	
	}
	
	function parseQueryString($str) { 
	    $op = array(); 
	    $pairs = explode("&", $str); 
	    foreach ($pairs as $pair) { 
	        list($k, $v) = array_map("urldecode", explode("=", $pair)); 
	        $op[$k] = $v; 
	    } 
	    return $op; 
	}
	
	function rebuildQueryString($ar) {
		$tmp = array();
		foreach ($ar as $key => $val) {
			$temp[] = (urlencode($key)."=".urlencode($val));
		}
		return implode("&",$temp);
	
	}
	
	function camelCase($str, $capitalise_first_char = true) {
	    if($capitalise_first_char) {
	      $str[0] = strtoupper($str[0]);
	    }
	    $func = create_function('$c', 'return strtoupper($c[1]);');
	    return preg_replace_callback('/_([a-z])/', $func, $str);
	}
	
	function isDetail($type, &$params){
		if ($params->get('detail_filter', 'low') == $type) return 'selected="selected"';
		else return "";
	}
	
	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 */
	function getGravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
		$mode = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
		$url = ($mode == 'https') ? $mode.'://secure.gravatar.com/avatar/' : $mode.'://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
}
