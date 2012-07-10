<?php
/**
 * @category	Events
 * @package		JomSocial
 * @copyright (C) 2010 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CEventsTrigger
{
	public function onEventCreate( $event )
	{
		$config		= CFactory::getConfig();
				
		// Send an email notification to the site admin's when there is a new group created
		if( $config->get( 'event_moderation' ) )
		{
			$userModel	= CFactory::getModel( 'User' );
			$my			= CFactory::getUser();
			$admins		= $userModel->getSuperAdmins();

			// Add notification
			CFactory::load( 'libraries' , 'notification' );

			//Send notification email to administrators
			foreach( $admins as $row )
			{
				if( $row->sendEmail )
				{
					$params	= new CParameter( '' );
					$params->set('url' , JURI::root() . 'administrator/index.php?option=com_community&view=events' );
					$params->set('title' , $event->title );

					CNotificationLibrary::add( 'etype_events_notify_admin' , $my->id , $row->id , JText::sprintf( 'COM_COMMUNITY_EVENTS_MODERATION_NOTICE' , $event->title ) , '' , 'events.notifyadmin' , $params );
				}
			}
		}
	}
}