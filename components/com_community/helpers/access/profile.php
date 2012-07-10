<?php
/**
 * @package		JomSocial
 * @subpackage	Library
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

Class CProfileAccess implements CAccessInterface
{
    /**
	 * Method to check if a user is authorised to perform an action in this class
	 *
	 * @param	integer	$userId	Id of the user for which to check authorisation.
	 * @param	string	$action	The name of the action to authorise.
	 * @param	mixed	$asset	Name of the asset as a string.
	 *
	 * @return	boolean	True if authorised.
	 * @since	Jomsocial 2.4
	 */
	static public function authorise()
	{
		$args      = func_get_args();
		$assetName = array_shift ( $args );

		if (method_exists(__CLASS__,$assetName)) {
			return call_user_func_array(array(__CLASS__, $assetName), $args);
		} else {
			return null;
		}
	}

	/**
	 * Return true if the user can view the given profile
	 * @param type $asset
	 * @param type $assetObject
	 *
	 */
	static public function profileView($userid, $asset, $user)
	{
		$viewer = CFactory::getUser($userid);

		// @rule: Global admin can view all
		if( COwnerHelper::isCommunityAdmin() || $viewer->id == $user->id ){
			return true;
		}

		// @rule: if the user is blocked, you can't see it either
		if( $user->isBlocked() ){
			return false;
		}

		// Check based on privacy settin
		$param = $user->getParams();
		$access = $param->get('privacyProfileView');
		
		// @rule, User with public access, show
		// In old profile, 0 also means public
		if( $access == PRIVACY_PUBLIC || $access == 0){
			return true;
		}
               
		// @rule: at this stage, non registered member can't view it anyway
		if( $viewer->id == 0){
			return false;
		}

		// @rule: User that limit to friend only, check for friend
		if( $access == PRIVACY_FRIENDS ){
			$friends = explode( ',', $viewer->_friends );
			if(in_array( $user->id, $friends )){
				return true;
			}
		}

                if( $access == PRIVACY_MEMBERS && $viewer->id !==0){
                    return true;
                }

		// @rule: for private profile, only owner can view
		// No checking needed, already allow user to see themselves at the top line

		return false;
	}
	
	/**
	 *
	 * @param type $userid
	 * @param type $asset
	 * @param type $user 
	 */
	static public function profileDelete($userid, $asset, $user)
	{
		$config	= CFactory::getConfig();
		$viewer = CFactory::getUser($userid);
		
		// Check if profile deletion is disabled
		if( !$config->get('profile_deletion') )
		{
			return false;
		}
		
		// Guest obviously can't delete a profile
		if( $userid == 0){
			return false;
		}
		
		// Community admin cannot be deleted from the front-end
		if( COwnerHelper::isCommunityAdmin($user->id) ){
			return false;
		}
		
		// You can only delete your own profile
		if( $userid == $user->id ){
			return true;
		}
		
		return false;
	}
	
	static public function profileBan($userid, $asset, $user)
	{
		// Only community admin can ban a user
		return COwnerHelper::isCommunityAdmin( $userid );
	}
	
}
