<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class CommunityOauthController extends CommunityBaseController
{
	public function callback()
	{
		$mainframe	=& JFactory::getApplication();
		$my			= CFactory::getUser();
		$denied     = JRequest::getVar( 'denied' , '' );
		$app        = JRequest::getVar( 'app' , '' );
		$url        = CRoute::_('index.php?option=com_community&view=profile&userid=' . $my->id , false );

		if( empty($app) )
		{
			echo JText::_('COM_COMMUNITY_INVALID_APPLICATION');
			return;
		}

		if( $my->id == 0 )
		{
			echo JText::_('COM_COMMUNITY_INVALID_ACCESS');
			return;
		}

		if( !empty( $denied ) )
		{
		    $mainframe->redirect( $url , JText::_( 'COM_COMMUNITY_OAUTH_APPLICATION_ACCESS_DENIED_WARNING' ) );
		}

		$oauth	=& JTable::getInstance( 'Oauth' , 'CTable' );
		if( $oauth->load( $my->id , $app ) )
		{
			$consumer       = plgCommunityTwitter::getConsumer();
			$oauth->userid	= $my->id;
			$oauth->app     = $app;
			$getData    	= JRequest::get('get');

			try
			{
			    $oauth->accesstoken 	= serialize( $consumer->getAccessToken( $getData , unserialize( $oauth->requesttoken ) ) );
			}
			catch( Exception $error )
			{
			    $mainframe->redirect( $url , $error->getMessage() );
			}

			if( !empty($oauth->accesstoken) )
			{
				$oauth->store();
			}
			$msg	= JText::_( 'COM_COMMUNITY_OAUTH_AUTHENTICATION_SUCCESS' );
			$mainframe->redirect( $url , $msg );
		}
	}

	public function remove()
	{
		$mainframe	=& JFactory::getApplication();
		$my			= CFactory::getUser();
		$app        = JRequest::getVar( 'app' , '' );

		if( empty($app) )
		{
			echo JText::_('COM_COMMUNITY_INVALID_APPLICATION');
			return;
		}

		if( $my->id == 0 )
		{
			echo JText::_('COM_COMMUNITY_INVALID_ACCESS');
			return;
		}
		$oauth	=& JTable::getInstance( 'Oauth' , 'CTable' );
		if( !$oauth->load( $my->id , $app ) )
		{
		    $url    = CRoute::_('index.php?option=com_community&view=profile&userid=' . $my->id , false );
		    $mainframe->redirect( $url , JText::_('COM_COMMUNITY_OAUTH_LOAD_APPLICATION_ERROR') );
		}

	    $oauth->delete();
	    $url    = CRoute::_('index.php?option=com_community&view=profile&userid=' . $my->id , false );
	    $mainframe->redirect( $url , JText::_('COM_COMMUNITY_OAUTH_DEAUTHORIZED_APPLICATION_SUCCESS') );

	}
}
