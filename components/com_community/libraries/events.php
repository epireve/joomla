<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	Photos 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
CFactory::load( 'libraries' , 'comment' );

class CEvents implements 
	CCommentInterface, CStreamable
{

	static public function sendCommentNotification( CTableWall $wall , $message )
	{
		CFactory::load( 'libraries' , 'notification' );

		$my			= CFactory::getUser();
		$targetUser	= CFactory::getUser( $wall->post_by );
		$url		= 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $wall->contentid;
		$params 	= $targetUser->getParams();

		$params		= new CParameter( '' );
		$params->set( 'url' , $url );
		$params->set( 'message' , $message );

		CNotificationLibrary::add( 'etype_events_submit_wall_comment' , $my->id , $targetUser->id , JText::sprintf('PLG_WALLS_WALL_COMMENT_EMAIL_SUBJECT' , $my->getDisplayName() ) , '' , 'events.wallcomment' , $params );
		return true;
	}
	
	/**
	 * Return an array of valid 'app' code to fetch from the stream
	 * @return array
	 */
	static public function getStreamAppCode(){
		return array('events.wall', 'event.attend');
	}


	static public function getActivityContentHTML($act)
	{
		// Ok, the activity could be an upload OR a wall comment. In the future, the content should
		// indicate which is which
		$html 	 = '';
		$param 	 = new CParameter( $act->params );
		$action  = $param->get('action' , false);

		CFactory::load('models', 'events');
		
		if( $action == 'events.create'  )
		{
			return CEvents::getEventSummary($act->cid, $param);
		}
		else if( $action == 'event.join' || $action ==  'event.attendence.attend' )
		{	
			return CEvents::getEventSummary($act->cid, $param);
		}
		else if( $action == 'event.wall.create' || $action == 'events.wall.create')
		{
			CFactory::load('libraries', 'wall');
			
			$wallid = $param->get('wallid' , 0);
			$html = CWallLibrary::getWallContentSummary($wallid);
			return $html;
		}
	
		return $html;
	}
	
	static public function getEventSummary($eventid, $param)
	{
		$config = CFactory::getConfig();
		$model  =CFactory::getModel( 'events' );
		$event	=& JTable::getInstance( 'Event' , 'CTable' );
		$event->load( $eventid );
		
		// Add tagging code
		/*
		$tagsHTML = '';
		if($config->get('tags_events') && $config->get('tags_show_in_stream')){
			CFactory::load('libraries', 'tags');
			$tags = new CTags();
			$tagsHTML = $tags->getHTML('events', $eventid, false);
		}*/
		
		$tmpl	= new CTemplate();
		$tmpl->set( 'event'		, $event );
		$tmpl->set( 'param'		, $param );
		
		return $tmpl->fetch( 'activity.events.update' );
	} 
	
	/**
	 * Return array of rss-feed compatible data
	 */	 	
	public function getFEED($maxEntry=20, $userid=null)
	{
                              
		$events   = array();
		
        CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'models' , 'events' );
		
		$model    = new CommunityModelEvents();
        $eventObjs= $model->getEvents( null, $userid );

		if( $eventObjs )
		{
			foreach( $eventObjs as $row )
			{
				$event	=& JTable::getInstance( 'Event' , 'CTable' );
				$event->load( $row->id );
				$events[]	= $event;
			}
			unset($eventObjs);
		}		
		
		return $events;
	}
	
	/**
	 * Return HTML formatted stream for events
	 * @param type $eventid 
	 */
	public function getStreamHTML( $event )
	{
		CFactory::load('libraries', 'activities');
		$activities = new CActivities();
		$streamHTML = $activities->getAppHTML( 
					array(
						'app' => CActivities::APP_EVENTS,
						'eventid' => $event->id,
						'apptype' => 'event'
					)
				);
		
		return $streamHTML;
	}
	
	/** 
	 * Return true is the user can post to the stream 
	 **/
	public function isAllowStreamPost( $userid, $options )
	{
		// Guest cannot post.
		if( $userid == 0){
			return false;
		}
		
		// Admin can comment on any post
		if(COwnerHelper::isCommunityAdmin()){
			return true;
		}
		
		$event	=& JTable::getInstance( 'Event' , 'CTable' );
-		$event->load( $options['eventid'] );
		return $event->isMember($userid);
	}

        public function getEventMemberHTML( $eventId )
        {
            CFactory::load( 'libraries' , 'tooltip' );
            CFactory::load( 'helpers' , 'event' );
            
            $event                              =&	JTable::getInstance( 'Event' , 'CTable' );
            $event->load($eventId);
            $eventMembers			= $event->getMembers( COMMUNITY_EVENT_STATUS_ATTEND, 12 , CC_RANDOMIZE );
            $eventMembersCount		= $event->getMembersCount( COMMUNITY_EVENT_STATUS_ATTEND );

            for( $i = 0; ($i < count($eventMembers)); $i++)
            {
			$row	=& $eventMembers[$i];
			$eventMembers[$i]	= CFactory::getUser( $row->id );
            }
            $handler	= CEventHelper::getHandler( $event );
            
            $tmpl	= new CTemplate();
            $tmpl->set( 'eventMembers',    $eventMembers );
            $tmpl->set( 'eventMembersCount',    $eventMembersCount );
            $tmpl->set( 'handler',    $handler );
            $tmpl->set( 'eventId',  $eventId);

            return $tmpl->fetch( 'events.members.html' );
        }
	
}