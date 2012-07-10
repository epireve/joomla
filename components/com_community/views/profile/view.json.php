<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class CommunityViewProfile extends CommunityView
{

	/**
	 * Displays the viewing profile page.
	 * 	 	
	 * @access	public
	 * @param	array  An associative array to display the fields
	 */	  	
	public function profile(& $data)
	{
        $mainframe      = JFactory::getApplication();
        $friendsModel	= CFactory::getModel('friends');
        
        $showfriends    = JRequest::getVar('showfriends', false);
        $userid         = JRequest::getVar('userid' , '');
        $user           = CFactory::getUser($userid);
		
		require_once (AZRUL_SYSTEM_PATH.DS.'pc_includes'.DS.'JSON.php');
		
		$json = new Services_JSON();
		$str = $json->encode($user);
		echo $str;
		exit;
		
	}
}
?>
