<?php
/**
 * @package		JomSocial
 * @subpackage	Library
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

Class CFacebookAccess implements CAccessInterface
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

	/*
	 * This function will get the permission to facebook friend invite.
	 *
	 * @return : bool
	 */
    static public function facebookFriendInviteView($userId)
    {
        $config = CFactory::getConfig();

        if (self::facebookView($userId) && $config->get( 'facebook_invite_friends' )) {
            return true;
        } else {
          return false;
        }
    }

	/*
	 * This function will get the permission to facebook view.
	 *
	 * @return : bool
	 */
    static public function facebookView($userId)
    {
        $config         = CFactory::getConfig();

        $connectModel	= CFactory::getModel( 'Connect' );
        $isFacebookUser = $connectModel->isAssociated( $userId );

        if( $isFacebookUser && $config->get('fbconnectkey') && $config->get( 'fbconnectsecret' ) ) {
            return true;
        } else {
            return false;
        }

    }
	
}