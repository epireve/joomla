<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );

/**
 * Jom Social Component Controller
 */
class CommunityControllerMessaging extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function ajaxSendMessage( $title , $message , $limit = 1 )
	{
		if(!$title || !$message){
		    $response	= new JAXResponse();
		    $response->addScriptCall("joms.jQuery('#error').remove();");
		    $response->addScriptCall( 'joms.jQuery("#messaging-form").prepend("<p id=error style=color:red>Error:Title or Message cannot be empty</p>");' );
		    return $response->sendResponse();
		}
		
		$limitstart		= $limit - 1;

		$model			=& $this->getModel( 'users' );
		$userId			= $model->getSiteUsers( $limitstart , 1 );

		$response	= new JAXResponse();
		
		$response->addScriptCall( 'joms.jQuery("#messaging-form").hide();' );
		$response->addScriptCall( 'joms.jQuery("#messaging-result").show();' );

		$user			= CFactory::getUser( $userId );
		$my				=& JFactory::getUser();
		
		if(!empty($userId))
		{
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'notification.php' );

			CNotificationLibrary::add( 'etype_system_messaging' , $my->id , $user->id , $title , $message );

			$response->addScriptCall( 'joms.jQuery("#no-progress").css("display","none");');
			$response->addScriptCall( 'joms.jQuery("#progress-status").append("<div>' . JText::sprintf('Sending message to <strong>%1$s</strong>',str_replace(array("\r","\n"), ' ', $user->getDisplayname()) ) . '<span style=\"color: green;margin-left: 5px;\">' . JText::_('COM_COMMUNITY_SUCCESS').'</span></div>");' );
			$response->addScriptCall( 'sendMessage' , $title , $message , ( $limit + 1 ) );
		}
		else
		{
			$response->addScriptCall( 'joms.jQuery("#progress-status").append("<div style=\"font-weight:700;\">' . JText::_('COM_COMMUNITY_UPDATED') . '</div>");');
		}
		return $response->sendResponse();
	}
}