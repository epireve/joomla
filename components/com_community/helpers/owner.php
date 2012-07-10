<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class COwnerHelper
{
	// Check if the given id is the same and not a guest
	static public function isMine($id1, $id2)
	{
		return ($id1 == $id2) && (($id1 != 0) || ($id2 != 0) );
	}
	
	static public function isRegisteredUser()
	{
		$my		=& JFactory::getUser();
		return (($my->id != 0) && ($my->block !=1));
	}
	
	/**
	 *  Determines if the currently logged in user is a super administrator
	 **/
	static public function isSuperAdministrator()
	{
		return COwnerHelper::isCommunityAdmin();
	}
	
	/**
	 * Check if a user can administer the community
	 */ 
	static public function isCommunityAdmin($userid = null)
	{
		$my	= CFactory::getUser($userid);
		$cacl = CACL::getInstance();
		$usergroup = $cacl->getGroupsByUserId($my->id);		
		$admingroups = array (	0 => 'Super Administrator',
								1 => 'Administrator',	
								2 => 'Manager',
								3 => 'Super Users'
								);
		return (in_array($usergroup, $admingroups));
		//return ( $my->usertype == 'Super Administrator' || $my->usertype == 'Administrator' || $my->usertype == 'Manager' );
	}
	
	/**
	 * Sends an email to site administrators
	 * 
	 * @param	String	$subject	A string representation of the email subject.
	 * @param	String	$message	A string representation of the email message.	 	 	 
	 **/	 	
	static public function emailCommunityAdmins( $subject , $message )
	{
		$mainframe		=& JFactory::getApplication();
		$model			= CFactory::getModel( 'Register' );
		$recipients		= $model->getSuperAdministratorEmail();
		
		$sitename 		= $mainframe->getCfg( 'sitename' );		
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$subject 		= html_entity_decode( $subject , ENT_QUOTES );

		foreach ( $recipients as $recipient )
		{
			if ($recipient->sendEmail)
			{
				$message	= html_entity_decode( $message , ENT_QUOTES);
				JUtility::sendMail($mailfrom, $fromname, $recipient->email, $subject, $message );
			}
		}
		
		return true;
	}
}

/**
 * Deprecated since 1.8
 */
function isMine($id1, $id2)
{
	return COwnerHelper::isMine( $id1, $id2 );
}

/**
 * Deprecated since 1.8
 */
function isRegisteredUser()
{
	return COwnerHelper::isRegisteredUser();
}

/**
 * Deprecated since 1.8
 */
function isSuperAdministrator()
{
	return COwnerHelper::isCommunityAdmin();
}

/**
 * Deprecated since 1.8
 */
function isCommunityAdmin($userid = null)
{
	return COwnerHelper::isCommunityAdmin();
}