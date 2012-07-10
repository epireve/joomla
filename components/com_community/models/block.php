<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Ban User
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

class CommunityModelBlock extends JCCModel
{
	/**
	 * check if the user is ban
	 */	 	
	public function getBlockStatus($myId,$userId)
	{
		// A Guest obviously has not blocked anyone or
		// have anyone else blocked hi
		if($userId == 0 || $myId == 0){
			return false;
		}
		
		$db	=& $this->getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote('id') 
					.' FROM ' . $db->nameQuote('#__community_blocklist')
					.' WHERE ' . $db->nameQuote('blocked_userid') .'=' . $db->Quote($myId)
					.' AND ' . $db->nameQuote('userid') .'=' . $db->Quote($userId);	

		$db->setQuery( $query );
		$result	= $db->loadObject() ? true : false;

		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}
		
		return $result;
	}
	
	/**
	 * ban a user
	 */	 	
	public function blockUser($myId,$userId)
	{
		$db	=& $this->getDBO();
		
		// check if user is banned
		if( !$this->getBlockStatus($userId,$myId) && $myId!=$userId ){
		
			$query	= 'INSERT INTO ' . $db->nameQuote('#__community_blocklist')
					. ' SET ' . $db->nameQuote('blocked_userid').'=' . $db->Quote($userId)
					. ' , ' . $db->nameQuote('userid') .'=' . $db->Quote($myId);	
	
			$db->setQuery( $query );
			$db->query();
	
			if($db->getErrorNum())
			{
				JError::raiseError(500, $db->stderr());
			}
			
			return true;
						
		}
		
	}
	
	/**
	 * remove ban a user (unban)
	 */	 	
	public function removeBannedUser($myId,$userId)
	{
		$db	=& $this->getDBO();
		
		// check if user is banned
		if( $this->getBlockStatus($userId,$myId) ){
		
			$query	= 'DELETE FROM ' . $db->nameQuote('#__community_blocklist')
					. ' WHERE ' . $db->nameQuote('blocked_userid') .'=' . $db->Quote($userId)
					. ' AND ' . $db->nameQuote('userid') .'=' . $db->Quote($myId);	
	
			$db->setQuery( $query );
				$db->query();
	
			if($db->getErrorNum())
			{
				JError::raiseError(500, $db->stderr());
			}   
			
			return true;
			
		}
		
	}
	
	/**
	 * get list of ban user
	 */
	public function getBanList($myId)
	{
		$db	=& $this->getDBO();
		
		$query	= "SELECT m.*,n.`name` FROM `#__community_blocklist` m "
				. "LEFT JOIN `#__users` n ON m.`blocked_userid`=n.`id` "
				. "WHERE m.`userid`=" . $db->Quote($myId) . " "
                . "AND m.`blocked_userid`!=0";
		$db->setQuery( $query );
		
		$result	= $db->loadObjectList();
	
		return $result;
	}	 	 	
					
}
