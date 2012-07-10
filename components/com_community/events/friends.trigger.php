<?php
/**
 * @category	Events
 * @package		JomSocial
 * @copyright (C) 2010 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CFriendsTrigger
{
	public function onFriendApprove($obj)
	{
		// Update friends count
		$friendsModel	= CFactory::getModel( 'friends' );
		
		$friendsModel->updateFriendCount( $obj->profileOwnerId );
		$friendsModel->updateFriendCount( $obj->friendId );
	}
}