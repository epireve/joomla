<?php
/**
 * @package		JomSocial
 * @subpackage	Library
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

Class CActivitiesAccess implements CAccessInterface{

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

	/*
	 * @param : int(activity_id)
	 * This function will get the permission to add for profile/mainstream activity
	 *
	 * @return : bool
	 */
	static public function activitiesCommentAdd($userId, $assetId){
		$obj = func_get_arg(0);
		$model		=& CFactory::getModel('activities');
		$result		= false;
		$config		= CFactory::getConfig();
		
		// Guest can never leave a comment
		if( $userId == 0){
			return false;
		}
		
		// If global config allow all members to comment, allow it
		if( $config->get( 'allmemberactivitycomment' ) == '1')
		{
			return true;
		}

		$allow_comment = false;
		
		// if all activity comment is allowed, return true
		$config			= CFactory::getConfig();
		if($config->get( 'allmemberactivitycomment' ) == '1' && COwnerHelper::isRegisteredUser()){
			$allow_comment = true;
		}

		if($obj instanceof CTableEvent || $obj instanceof CTableGroup){
			//event or group activities only
			if($obj -> isMember($userId)){
				$allow_comment = true;
			}
		}else if($config->get( 'allmemberactivitycomment' ) == '1' && COwnerHelper::isRegisteredUser()){
			// if all activity comment is allowed, return true
			$allow_comment = true;
		}

		if($allow_comment || CFriendsHelper::isConnected($assetId, $userId) || COwnerHelper::isCommunityAdmin()){
			$result = true;
		}

		return $result;
	}

	/*
	 * @param : int(activity_id)
	 * This function will get the permission to delete for profile/mainstream activity
	 *
	 * @return : bool
	 */
	static public function activitiesDelete($userId, $assetId){
		$obj = func_get_arg(0);
		$model		  =& CFactory::getModel('activities');
		$result = false;

		if($obj instanceof CTableEvent || $obj instanceof CTableGroup){
			//event or group activities only
			$isAppOwner = ($userId == $obj->creator);
			if($isAppOwner || COwnerHelper::isCommunityAdmin() || $model->getActivityOwner($assetId) == $userId){
				$result = true;
			}
		}else{
			if($model->getActivityOwner($assetId) == $userId){
				$result = true;
			}
		}

		return $result;
	}
}