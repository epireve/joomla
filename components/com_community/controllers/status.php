<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.utilities.date');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . 'videos.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . 'photos.php' );
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'controllers' . DS . 'events.php' );

class CommunityStatusController extends CommunityBaseController
{
	private $_adapters = null;

	public function setAdapter($name, $adapter=null)
	{
		if (!is_object($adapter))
		{
			$class = 'CommunityStatus' . ucfirst($name) . 'Controller';

			if (!class_exists($class)) {
				return false;
			}
			$adapter = new $class();
			$adapter->parent =& $this;
		}

		$this->_adapters[$name] =& $adapter;

		return $adapter;
	}

	public function __call($func, $args)
	{
		    $name = $args[0];
		    $args = array_slice($args, 1);

		    $adapter = $this->setAdapter($name);

		    call_user_func_array(array($adapter, $func), $args);
	}

	var $_name = 'status';
		
	/**
	 * Update the status of current user
	 */	 	
	public function ajaxUpdate($message = '')
	{
                $filter = JFilterInput::getInstance();
                $message = $filter->clean($message, 'string');
		$cache = CFactory::getFastCache();
		$cache->clean(array('activities'));
		
		if (!COwnerHelper::isRegisteredUser())
		{
			return $this->ajaxBlockUnregister();
		}
		$mainframe 		=& JFactory::getApplication();
		$objResponse    = new JAXResponse();
		
		//@rule: In case someone bypasses the status in the html, we enforce the character limit.
		$config			= CFactory::getConfig();
		
		if( JString::strlen( $message ) > $config->get('statusmaxchar') )
		{
			$message	= JString::substr( $message , 0 , $config->get('statusmaxchar') );
		}
		
		//trim it here so that it wun go into activities stream.
		$message = JString::trim($message);		

		$my			= CFactory::getUser();    
		$status		=& $this->getModel('status');
		
		// @rule: Spam checks
		if( $config->get( 'antispam_akismet_status') )
		{
			CFactory::load( 'libraries' , 'spamfilter' );
			
			$filter				= CSpamFilter::getFilter();
			$filter->setAuthor( $my->getDisplayName() );
			$filter->setMessage( $message );
			$filter->setEmail( $my->email );
			$filter->setURL( CRoute::_('index.php?option=com_community&view=profile&userid=' . $my->id ) );
			$filter->setType( 'message' );
			$filter->setIP( $_SERVER['REMOTE_ADDR'] );

			if( $filter->isSpam() )
			{
				$objResponse->addAlert( JText::_('COM_COMMUNITY_STATUS_MARKED_SPAM') );
				return $objResponse->sendResponse();
			}
		}

		$status->update($my->id, $message);
		
		//set user status for current session.
		$today		=& JFactory::getDate();
		$message2	= (empty($message)) ? ' ' : $message;
		$my->set( '_status' , $message2 );
		$my->set( '_posted_on' , $today->toMySQL());
		
		$profileid = JRequest::getVar('userid' , 0 , 'GET');
		if(COwnerHelper::isMine($my->id, $profileid))
		{	
			$objResponse->addScriptCall("joms.jQuery('#profile-status span#profile-status-message').html('" . addslashes( $message ) . "');");
		}

		CFactory::load( 'helpers' , 'string' );
		$message		= CStringHelper::escape( $message );
		
		if(! empty($message))
		{		
			$act = new stdClass();
			$act->cmd 		= 'profile.status.update';
			$act->actor 	= $my->id;
			$act->target 	= $my->id;

			CFactory::load( 'helpers' , 'linkgenerator' );

			// @rule: Autolink hyperlinks
			$message		= CLinkGeneratorHelper::replaceURL( $message );
			
			// @rule: Autolink to users profile when message contains @username
			$message		= CLinkGeneratorHelper::replaceAliasURL( $message );
			
			CFactory::load('libraries', 'activities');
			$privacyParams	= $my->getParams();			
			
			$act->title		 = '{actor} '.$message;
			$act->content	 = '';
			$act->app		 = 'profile';
			$act->cid		 = $my->id;
			$act->access	 = $privacyParams->get('privacyProfileView');
			$act->comment_id 	= CActivities::COMMENT_SELF;
			$act->comment_type	= 'profile.status';
			$act->like_id 		= CActivities::LIKE_SELF;
			$act->like_type		= 'profile.status';

			
			CActivityStream::add($act);
			
			//add user points
			CFactory::load( 'libraries' , 'userpoints' );		
			CUserPoints::assignPoint('profile.status.update');
			
			//now we need to reload the activities streams
			$friendsModel	= CFactory::getModel('friends');
	
			$memberSince	= CTimeHelper::getDate($my->registerDate);
			$friendIds		= $friendsModel->getFriendIds($my->id);
	
			//include_once(JPATH_COMPONENT . DS.'libraries'.DS.'activities.php');
			$act 	= new CActivityStream();
			$params	=& $my->getParams();			
			$limit	= (! empty($params)) ? $params->get( 'activityLimit' , '' ) : ''; 
			$html	= $act->getHTML($my->id, $friendIds, $memberSince, $limit );			
			
			$status		= $my->getStatus();
			$status	= addslashes( $status );
 			$objResponse->addScriptCall( "joms.jQuery('#.-message').html('" . $status . "');");
			$objResponse->addScriptCall( "joms.jQuery('title').val('" . $status . "');");

			$objResponse->addAssign('activity-stream-container' , 'innerHTML' , $html );
		}

		return $objResponse->sendResponse();
	}
}