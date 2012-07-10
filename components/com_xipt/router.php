<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

function _GetXiptMenus()
{
	static $menus = null;

	if($menus !==null)
		return $menus;

	$dbo = JFactory::getDBO();
	//use #__extensions for joomla 1.6
	if (XIPT_JOOMLA_15){ 				
		$dbo->setQuery(  " SELECT `id` "
					." FROM `#__components` "
					." WHERE `option`='com_xipt' AND `iscore`=0"
				);
	}
	else{ 				
		$dbo->setQuery(  " SELECT `extension_id` "
					." FROM `#__extensions` "
					." WHERE `element`='com_xipt' AND `client_id`=1"
				);
	}
	$XIPTCmpId 	= $dbo->loadResult();
	$menu 		= JSite::getMenu();
	//$menus 		= $menu->getItems('extension_id',$XIPTCmpId);
      //check query is empty or not fro joomla 1.6
    if(empty($XIPTCmpId[XIPT_JOOMLA_EXT_ID])){
		$menus = $menu->getActive();
	}
	else {
    	//Pass Atribute and value in getItems() for joomla 1.6
    	$menus = $menu->getItems(XIPT_JOOMLA_MENU_COMP_ID,array($XIPTCmpId[XIPT_JOOMLA_EXT_ID]));
	}

	return $menus;
}

function _getXiptUrlVars()
{
	return array('view','task','ptypeid');
}

function _findXiptMatchCount($menu, $query)
{
	$vars = _getXiptUrlVars();
	$count = 0;
	foreach($vars as $var)
	{
		//variable not requested
		if(!isset($query[$var]))
			continue;

		//variable not exist in menu
		if(!isset($menu[$var]))
			continue;

		//exist but do not match
		if($menu[$var] !== $query[$var])
			return 0;

		$count++;
	}
	return $count;
}

function XiptBuildRoute( &$query )
{
	$segments = array();
	$menus = _GetXiptMenus();

	//If item id is not set then we need to extract those
	$selMenu = null;
	if (!isset($query['Itemid']) && $menus)
	{
		$count 		= 0;
		$selMenu 	= $menus[0];

		foreach($menus as $menu){
			//count matching
			$matching = _findXiptMatchCount($menu->query,$query);

			if($count >= $matching)
				continue;

			//current menu matches more
			$count		= $matching;
			$selMenu 	= $menu;
		}

		//assig ItemID of selected menu
		$query['Itemid'] = $selMenu->id;
	}

	//thhere is no menu item for xipt
	if (!isset($query['Itemid']))
		return $segments;
	
	//finally selected menu is
	$selMenu = JSite::getMenu()->getItem($query['Itemid']);

	//remove not-required variables, which can be calculated from URL itself
	$vars = _getXiptUrlVars();
	foreach($vars as $var)
	{
		//variable not requested
		if(!isset($query[$var]))
			continue;

		//variable not exist in menu
		if(!isset($selMenu->query[$var]))
			continue;

		//exist but do not match
		if($selMenu->query[$var] === $query[$var])
			unset($query[$var]);
	}

	return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 */
function XiptParseRoute( $segments )
{
	$myVars = _getXiptUrlVars();
	$vars = array();

	foreach($myVars as $v)
	{
		$reqVar = JRequest::getVar($v, null);
		if($reqVar===null)
			continue;

		$vars[$v] = $reqVar;
	}

	return $vars;
}