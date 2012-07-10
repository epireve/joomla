<?php
/**
 * @package: JomSocial
 * ===================================================
 * @author
 * Name: Sigrid Suski & Radek Suski, Sigsiu.NET
 * Email: sobi@sigsiu.net
 * Url: http://www.Sigsiu.NET
 * ===================================================
 * @copyright Copyright (C) 2006 - 2010 Sigsiu.NET (http://www.sigsiu.net). All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 2.0
 * ===================================================
 * $Date: 2010-02-16 08:10:06 +0000 (Tue, 16 Feb 2010) $
 * $Author: Radek Suski $
 */

defined('_JEXEC') or die();

require_once ( COMMUNITY_COM_PATH . DS . 'libraries' . DS . 'twitter'. DS . 'twitteroauth.php' );
require_once ( COMMUNITY_COM_PATH . DS . 'libraries' . DS . 'twitter'. DS . 'config.php' );

class CommunityTwitterController extends CommunityBaseController
{
	public function execute( $task = null )
	{
		switch ( $task ) {
			default:
				$this->connect();
				break;
			case 'callback':
				$this->callback();
				break;
		}
	}
	private function callback()
	{
		$m = JFactory::getApplication( 'site' );
		$oauthSecret = $m->getUserState( 'com_community.twitter_oauth_secret' );		
		$oauthToken = $m->getUserState( 'com_community.twitter_oauth_token' );
		$oauthVerifier = JRequest::getVar( 'oauth_verifier' );		
		$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET, $oauthToken, $oauthSecret );
		$accessToken = $connection->getAccessToken( $oauthVerifier );		
		$my =& JFactory::getUser();
		$user = CFactory::getUser( $my->id );
		$userparams = $user->getAppParams( 'twitter' );
		$userparams->set( 'token', $accessToken[ 'oauth_token' ] );
		$userparams->set( 'secret', $accessToken[ 'oauth_token_secret' ] );
		$userparams->bind( $userparams->toArray() );			
		$app = CFactory::getModel( 'apps');
		$app->addApp( $my->id, 'twitter' );
		$id = $app->getUserApplicationId( 'twitter', $my->id );
		$app->storeParams( $id, $userparams->toString() );	
		$m->redirect( $url = JURI::root().'index.php?option=com_community&view=profile&Itemid='.JRequest::getInt( 'Itemid' ), JText::_( 'Connection has been established.' ) );
	}
	private function connect()
	{
		$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET );
		$requestToken = $connection->getRequestToken( JURI::root().'index.php?option=com_community&view=twitter&task=callback&Itemid='.JRequest::getInt( 'Itemid' ) );		
		$m = JFactory::getApplication( 'site' );		
		/* If last connection failed don't display authorization link. */
		switch ( $connection->http_code ) {
			case 200:
				$m->setUserState( 'com_community.twitter_oauth_token', $requestToken[ 'oauth_token' ] );
				$m->setUserState( 'com_community.twitter_oauth_secret', $requestToken[ 'oauth_token_secret' ] );
				/* Build authorize URL and redirect user to Twitter. */
				$url = $connection->getAuthorizeURL( $requestToken[ 'oauth_token' ] );
				break;
			default:
				$url = JURI::root().'index.php?option=com_community&view=profile&Itemid='.JRequest::getInt( 'Itemid' );
				$msg = JText::_( 'Could not connect to Twitter. Try again later. Message: '.$connection->http_header[ 'status' ] );
				$mtype = 'error';
		}		
		$m->redirect( $url, $msg, $mtype );
	}
}

