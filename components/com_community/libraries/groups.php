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

class CGroups implements 
	CCommentInterface, CStreamable
{
	
	static public function getActivityContentHTML($act)
	{
		// Ok, the activity could be an upload OR a wall comment. In the future, the content should
		// indicate which is which
		$html = '';
		$param = new CParameter( $act->params );
		$action = $param->get('action' , false);
		CFactory::load('models', 'groups');
		CFactory::load('models', 'discussions');
		$config = CFactory::getConfig();
		
		$groupModel		= CFactory::getModel( 'groups' );
		
		if( $action == CGroupsAction::DISCUSSION_CREATE )
		{
			// Old discussion might not have 'action', and we can't display their
			// discussion summary
			$topicId = $param->get('topic_id', false);
			if( $topicId ){
				                                         
				$group			= JTable::getInstance( 'Group' , 'CTable' );
				$discussion		= JTable::getInstance( 'Discussion' , 'CTable' );
			
				$group->load( $act->cid );
				$discussion->load( $topicId );

				// Add tagging code
				/*
				$tagsHTML = '';
				if($config->get('tags_groups') && $config->get('tags_show_in_stream')){
					CFactory::load('libraries', 'tags');
					$tags = new CTags();
					$tagsHTML = $tags->getHTML('discussion', $topicId, false);
				}
				*/

				CFactory::load( 'helpers' , 'string' );
				$discussion->message = strip_tags($discussion->message);
				$topic = CStringHelper::escape($discussion->message);
				$tmpl	= new CTemplate();
				$tmpl->set( 'comment' , JString::substr($topic, 0, $config->getInt('streamcontentlength')) );
				$html	= $tmpl->fetch( 'activity.groups.discussion.create' );
			} 
			return $html;
		} 
		else if ($action == CGroupsAction::WALLPOST_CREATE )
		{
			// a new wall post for group
			// @since 1.8
			$group	= JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $act->cid );
			
			$wallModel	= CFactory::getModel( 'Wall' );
			$wall		= JTable::getInstance( 'Wall' , 'CTable' );
			$my			= CFactory::getUser();
			
			// make sure the group is a public group or current use is
			// a member of the group
			if( ($group->approvals == 0) || $group->isMember($my->id))
			{
				CFactory::load( 'libraries' , 'comment' );
				$wall->load( $param->get('wallid' ));
				$comment	= strip_tags( $wall->comment , '<comment>');
				$comment	= CComment::stripCommentData( $comment );
				$tmpl	= new CTemplate();
				$tmpl->set( 'comment' , JString::substr($comment, 0, $config->getInt('streamcontentlength')) );
				$html	= $tmpl->fetch( 'activity.groups.wall.create' );
			}
			return $html;
		}
		else if($action == CGroupsAction::DISCUSSION_REPLY)
		{
			// @since 1.8
			$group	= JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $act->cid );
			
			$wallModel	= CFactory::getModel( 'Wall' );
			$wall		= JTable::getInstance( 'Wall' , 'CTable' );
			$my			= CFactory::getUser();
			
			// make sure the group is a public group or current use is
			// a member of the group
			if( ($group->approvals == 0) || $group->isMember($my->id))
			{
				$wallid = $param->get('wallid' );
				CFactory::load( 'libraries' , 'wall' );
				$html = CWallLibrary::getWallContentSummary($wallid);
			}
			return $html;
		}
		else if ($action == CGroupsAction::CREATE) 
		{
			$group	= JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $act->cid );
			
			$tmpl	= new CTemplate();
			$tmpl->set( 'group' , $group );
			$html	= $tmpl->fetch( 'activity.groups.create' );
		}
		
		
		return $html;
	}
	
	/**
	 * Return an array of valid 'app' code to fetch from the stream
	 * @return array
	 */
	static public function getStreamAppCode(){
		return array('groups.wall', 'groups.attend', 'events.wall', 'videos', 
			'groups.discussion', 'groups.discussion.reply', 'groups.bulletin',
				'photos', 'events');
	}


	static public function sendCommentNotification( CTableWall $wall , $message )
	{
		CFactory::load( 'libraries' , 'notification' );

		$my			= CFactory::getUser();
		$targetUser	= CFactory::getUser( $wall->post_by );
		$url		= 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $wall->contentid;
		$params 	= $targetUser->getParams();

		$params		= new CParameter( '' );
		$params->set( 'url' , $url );
		$params->set( 'message' , $message );

		CNotificationLibrary::add( 'etype_groups_submit_wall_comment' , $my->id , $targetUser->id , JText::sprintf('PLG_WALLS_WALL_COMMENT_EMAIL_SUBJECT' , $my->getDisplayName() ) , '' , 'groups.wallcomment' , $params );
		
		return true;
	}
	
	/**
	 *
	 */	 	
	static public function joinApproved($groupId, $userid)
	{
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$member		=& JTable::getInstance( 'GroupMembers' , 'CTable' );

		$group->load( $groupId );
		
		$act = new stdClass();
		$act->cmd 		= 'group.join';
		$act->actor   	= $userid;
		$act->target  	= 0;
		$act->title	  	= JText::sprintf('COM_COMMUNITY_GROUPS_GROUP_JOIN' , '{group_url}' , $group->name );
		$act->content	= '';
		$act->app		= 'groups';
		$act->cid		= $group->id;
		
		$params = new CParameter('');
		$params->set( 'group_url' , 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );
		
		// Add logging
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($act, $params->toString() );
		
		//add user points
		CFactory::load( 'libraries' , 'userpoints' );		
		CUserPoints::assignPoint('group.join');	
		
		// Store the group and update stats
		$group->updateStats();
		$group->store();
	}
	
	
	/**
	 * Return HTML formatted stream for groups
	 * @param object $group 
	 */
	public function getStreamHTML( $group)
	{
		CFactory::load('libraries', 'activities');
		$activities = new CActivities();
		$streamHTML = $activities->getAppHTML( 
					array(
						'app' => CActivities::APP_GROUPS,
						'groupid' => $group->id,
						'apptype' => 'group'
					)
				);
		
		return $streamHTML;
	}
	
	/** 
	 * Return true is the user can post to the stream 
	 **/
	public function isAllowStreamPost( $userid, $option )
	{	
		// Guest cannot post.
		if( $userid == 0){
			return false;
		}
		
		// Admin can comment on any post
		if(COwnerHelper::isCommunityAdmin()){
			return true;
		}
		
		// if the groupid not specified, obviously stream comment is not allowed
		if(empty($option['groupid'])){
			return false;
		}
		
		$group	=& JTable::getInstance( 'Group' , 'CTable' );
-		$group->load( $option['groupid'] );
		return $group->isMember($userid);
	}
}

class CGroupsAction
{
	const DISCUSSION_CREATE	= 'group.discussion.create';
	const DISCUSSION_REPLY	= 'group.discussion.reply';
	const WALLPOST_CREATE		= 'group.wall.create';
	const CREATE						= 'group.create';
}