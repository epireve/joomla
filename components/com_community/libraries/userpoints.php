<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	user 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'karma.php' );

class CUserPoints {
	
	/**
	 * return the path to karma image
	 * @param	user	CUser object	 
	 */	 	
	public function getPointsImage( $user ) {
		return CKarma::getKarmaImage($user);
	}
	
	
	/**
	 * add points to user based on the action.
	 */	 	
	public function assignPoint( $action, $userId=null)
	{
		//get the rule points
		//must use the JFactory::getUser to get the aid
		$juser	=& JFactory::getUser($userId);		
		
		if( $juser->id != 0 )
		{
			if (!method_exists($juser,'authorisedLevels')) {
				$aid    = $juser->aid;
				// if the aid is null, means this is not the current logged-in user. 
				// so we need to manually get this aid for this user.
				if(is_null($aid))
				{
					$aid = 0; //defautl to 0
					// Get an ACL object
					$acl 	=& JFactory::getACL();
					$grp 	= $acl->getAroGroup($juser->id);
					$group	= 'USERS';
							
					if($acl->is_group_child_of( $grp->name, $group))
					{
						$aid	= 1;
						// Fudge Authors, Editors, Publishers and Super Administrators into the special access group
						if ($acl->is_group_child_of($grp->name, 'Registered') ||
							$acl->is_group_child_of($grp->name, 'Public Backend'))    {
							$aid	= 2;
						}
					}
				}
			} else {
				//joomla 1.6
				$aid    = $juser->authorisedLevels();
			}
		
			$points	= CUserPoints::_getActionPoint($action, $aid);						 
			
			$user	= CFactory::getUser($userId);
			$points	+= $user->getKarmaPoint();
			$user->_points = $points;
			$user->save();
		}
	}
	
	
	/**	 
	 * Private method. DO NOT call this method directly.
	 * Return points for various actions. Return value should be configurable from the backend.	 	 
	 */	 	
	public function _getActionPoint( $action, $aid = 0) {
	
		include_once(JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'models'.DS.'userpoints.php');
		
		$userPoint = '';
		if( class_exists('CFactory') ){
			$userPoint = CFactory::getModel('userpoints');
		} else {
			$userPoint = new CommunityModelUserPoints();
		}
		
		$point	= 0;
		$upObj	= $userPoint->getPointData( $action );
		
		if(! empty($upObj))
		{
			$published	= $upObj->published;						
			$access		= $upObj->access;
			
			if (is_array($aid)) {
				//joomla 1.6
				if(in_array($access,$aid)) {
					$point = $upObj->points;
				}			
			} else {
				//joomla 1.5 and older
				$userAccess	= (empty($aid)) ? 0 : $aid;
				if($access <= $userAccess) {
					$point = $upObj->points;
				}			
			}			
			if ($published == '0')
				$point = 0;
		}
		
		return $point;

	}//end _getActionPoint
	
	
}