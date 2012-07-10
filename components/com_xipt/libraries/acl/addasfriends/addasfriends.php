<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class addasfriends extends XiptAclBase
{
	function getResourceOwner($data)
	{
		return $data['args'][0];	
	}
	
	function getFeatureCounts($resourceAccesser,$resourceOwner,$otherptype,$aclSelfPtype)
	{
		// XITODO : change this query into object
		$db		= JFactory::getDBO();
		$query	= 'SELECT DISTINCT(a.connect_to) AS id  FROM ' . $db->nameQuote('#__community_connection') . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
				. 'ON a.connect_from=' . $db->Quote( $resourceAccesser ) . ' '
				. 'AND a.connect_to=b.id '
				. ' LEFT JOIN #__xipt_users as ptfrom ON a.`connect_to`=ptfrom.`userid`'
				. ' AND ptfrom .`profiletype`=' . $db->Quote($aclSelfPtype)
				. ' LEFT JOIN #__xipt_users as ptto ON a.`connect_to`=ptto.`userid`'
				. ' AND ptto .`profiletype`=' . $db->Quote($otherptype);
		$db->setQuery( $query );
		$count		= $db->loadResultArray();
		return count($count);
	}	
	
	function checkAclApplicable(&$data)
	{
		if('com_community' != $data['option'] && 'community' != $data['option'])
			return false;
			
		if('friends' != $data['view'])
			return false;
			
		if($data['args'][0] != 0 && $data['task'] === 'ajaxconnect')
				return true;
				
		return false;
	}
}
