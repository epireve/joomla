<?php
/**
 * @category	Events
 * @package		JomSocial
 * @copyright (C) 2010 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CProfileTrigger
{
	public function onAfterProfileUpdate($userid, $save)
	{
		if($save)
		{
			// Update user's IP location
			$usermodel		= CFactory::getModel('user');
			$profileModel	= CFactory::getModel('profile');
			$user  = CFactory::getUser($userid);
			$juser =& JFactory::getUser($userid);

			// Update location data
			$profileModel->updateLocationData($user->id);
			
			// Update user's firstname and lastname. Only update if both is not 
			// empty and is actually specifies
			$givenName  = $user->getInfo('FIELD_GIVENNAME');
			$familyName = $user->getInfo('FIELD_FAMILYNAME');
			
			if(!empty($givenName) && !empty($familyName))
			{
				
				$juser->name = $givenName . ' ' .$familyName;
				
				// We need to update the cuser object too since it is static,
				// it might still be used
				$user->name = $juser->name;
				
				if(!$juser->save()){
					// save failed ?
				}
			}
			
			
			// Update all user counts
			$friendModel = CFactory::getModel('friends');
			$friendModel->updateFriendCount($userid);
			//$user->_friendcount = $numFriend;
			//echo $user->save();
			//echo $user->_friendcount;
		} 
	}
	
	/**
	 * Method is called during the status update triggers. 
	 **/
	public function onProfileStatusUpdate( $userid , $oldMessage , $newMessage )
	{
		$config	= CFactory::getConfig();
		$my = CFactory::getUser();
		
		if( $config->get('fbconnectpoststatus') )
		{
			CFactory::load( 'libraries' , 'facebook' );
			$facebook	= new CFacebook();
						
			if( $facebook ){
				$fbuserid = $facebook->getUser();
				
				$connectModel	= CFactory::getModel( 'Connect' );
				$connectTable	=& JTable::getInstance( 'Connect' , 'CTable' );
				$connectTable->load($fbuserid);
				
				// Make sure the FB session match the user session
				if( $connectTable->userid == $my->id )
				{
					/**
					 * Check post status to facebook settings
					 */
					//echo "posting to facebook"; exit;
					$targetUser		= CFactory::getUser( $my->id );
					$userParams 	= $targetUser->getParams();

					if($userParams->get('postFacebookStatus'))
					{
					    $result = $facebook->postStatus( $newMessage );
						//print_r($result); exit; 
					}
				}
			}
		}
	}
}