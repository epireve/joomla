<?php
/**
 * @package		JomSocial
 * @subpackage	Library 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
class CActivities
{
	
	const COMMENT_SELF 	= -1;
	const LIKE_SELF 	= -1;
	
	/* apps activity code */
	const APP_EVENTS	= 'events';
	const APP_GROUPS	= 'groups';
	
	/**
	 * Removes an existing activity from the system
	 * @access	static
	 **/	 	 	
	public function remove( $appType , $uniqueId )
	{
		$activitiesModel	= CFactory::getModel( 'activities' );
		
		return $activitiesModel->removeActivity( $appType , $uniqueId );
	}
	public function removeGroup($ids)
	{
	    $activitiesModel	= CFactory::getModel( 'activities' );

	    return $activitiesModel->removeGroupActivity( $ids );
	}
	/**
	 * Add new activity,
	 * @access 	static
	 * 	 
	 */	 	
	public function add($activity, $params='', $points = 1){
		
		CError::assert($activity , '', '!empty', __FILE__ , __LINE__ );

		// If params is an object, instead of a string, we convert it to string
		
		$cmd 	= !empty($activity->cmd) 		? $activity->cmd : '';
				
		if( !empty($cmd) )
		{
			$userPointModel	= CFactory::getModel( 'Userpoints' );
	
			// Test command, with userpoint command. If is unpublished do not proceed into adding to activity stream.
			$point			= $userPointModel->getPointData( $cmd );
			
			if( $point && !$point->published )
			{
				return;
			}			
		}
		
		$actor		= !empty($activity->actor) 		? $activity->actor 	: '';
		$target 	= !empty($activity->target) 	? $activity->target : 0;
		$title		= !empty($activity->title) 		? $activity->title 	: '';
		$content	= !empty($activity->content) 	? $activity->content : '';
		$appname	= !empty($activity->app) 		? $activity->app : '';
		$cid		= !empty($activity->cid) 		? $activity->cid : 0;
		$groupid	= !empty($activity->groupid) 	? $activity->groupid : 0;
		$group_access	= !empty($activity->group_access) 	? $activity->group_access : 0;
		$event_access	= !empty($activity->event_access) 	? $activity->event_access : 0;
		$eventid	= !empty($activity->eventid) 	? $activity->eventid : 0;
		$points		= !empty($activity->points) 	? $activity->points : $points;
		$access		= !empty($activity->access) 	? $activity->access : 0;
		$location	= !empty($activity->location) 	? $activity->location : '';
		
		$comment_id		= !empty($activity->comment_id) 	? $activity->comment_id : 0;
		$comment_type	= !empty($activity->comment_type) 	? $activity->comment_type : '';
		
		$like_id		= !empty($activity->like_id) 	? $activity->like_id : 0;
		$like_type		= !empty($activity->like_type) 	? $activity->like_type : '';
		
		// If the params in embedded within the activity object, use it
		// if it is not explicitly overriden
		if (empty($params) && !empty($activity->params))
		{
			$params = $activity->params;
		}

		
		$activities = CFactory::getModel('activities');
		
		// Update access for activity based on the user's profile privacy
		if( !empty($actor) && $actor != 0)
		{
			$user			= CFactory::getUser( $actor );
			$userParams		= $user->getParams();
			$profileAccess	= $userParams->get('privacyProfileView');
			
			// Only overwrite access if the user global profile privacy is higher
			// BUT, if access is defined as PRIVACY_FORCE_PUBLIC, do not modify it
			if( ( $access != PRIVACY_FORCE_PUBLIC ) && ( $profileAccess > $access ) )
			{
				$access	= $profileAccess;
				
			}
		}
		
		$table =& JTable::getInstance( 'Activity' , 'CTable' );
		$table->actor		= $actor;
		$table->target 		= $target;
		$table->title		= $title;
		$table->content		= $content;
		$table->app			= $appname;
		$table->cid			= $cid;
		$table->groupid		= $groupid;
		$table->group_access	= $group_access;
		$table->eventid		= $eventid;
		$table->event_access= $event_access;
		$table->points		= $points;
		$table->access		= $access;
		$table->location	= $location;
		$table->params		= $params;
		$table->comment_id		= $comment_id;
		$table->comment_type	= $comment_type;
		$table->like_id 	= $like_id;
		$table->like_type	= $like_type;	
		
		$table->store();
		
		// Update comment id, if we comment on the stream itself
		if($comment_id == CActivities::COMMENT_SELF)
		{
			$table->comment_id		= $table->id;
			
		}
		
		// Update comment id, if we like on the stream itself
		if($comment_id == CActivities::LIKE_SELF)
		{
			$table->like_id			= $table->id;
			
		}
		if($comment_id == CActivities::COMMENT_SELF || $comment_id == CActivities::LIKE_SELF)
		{
			$table->store();
		}
		
	}
	
	
	
	/**
	 * Return the HTML formatted activity content
	 */
	static function getActivityContent($act)
	{
		
		$cache = CFactory::getFastCache();
		$cacheid = __FILE__ . __LINE__ . serialize(func_get_args());
		if( $data = $cache->get( $cacheid ) )
		{
			return $data;
		}
		
		// Return empty content or content with old, invalid data
		// In some old version, some content might have 'This is the body'
		if( $act->content == 'This is the body' ){
			return '';
		}
		
		$html = $act->content;
		
		// For known core, apps, we can simply call the content command
		switch($act->app)
		{
			case 'videos':
				//if($act->content == '{getActivityContentHTML}')
				{
					CFactory::load('libraries' , 'videos');
					$html = CVideos::getActivityContentHTML($act);
				}
				break;
				
			case 'photos':
				//if($act->content == '{getActivityContentHTML}')
				{
					CFactory::load('libraries' , 'photos');
					$html = CPhotos::getActivityContentHTML($act);
				}
				break;
				
			case 'events':
				{
					CFactory::load('libraries' , 'events');
					$html = CEvents::getActivityContentHTML($act);
				}
				break;

			case 'groups.wall':
			case 'groups':
				{
					CFactory::load('libraries' , 'groups');
					$html = CGroups::getActivityContentHTML($act);
				}
				break;
			case 'groups.discussion.reply':
			case 'groups.discussion':
				{
					CFactory::load('libraries' , 'groups');
					$html = CGroups::getActivityContentHTML($act);
				}
				break;
			case 'groups.bulletin':
				{
					CFactory::load('libraries' , 'groups');
					$html = CGroups::getActivityContentHTML($act);
				}
			case 'system':
				{
					CFactory::load('libraries','adminstreams');
					$html = CAdminstreams::getActivityContentHTML($act);
					
				}
				break;
			case 'walls':
				// If a wall does not have any content, do not
				// display the summary
				if($act->app == 'walls' && $act->cid == 0){
					$html = '';
					return $html;
				}

				if($act->cid != 0){
					CFactory::load('libraries','wall');
					$html = CWall::getActivityContentHTML($act);
				}
				break;
			default:
				// for other unknown apps, we include the plugin see if it is is callable
				// we call the onActivityContentDisplay();
				CFactory::load( 'libraries', 'apps' );

				$apps		=& CAppPlugins::getInstance();
				$plugin  	=& $apps->get($act->app);
				$method		= 'onActivityContentDisplay';
				
				if( is_callable( array($plugin, $method) ) )
				{
					$args = array();
					$args[] = $act;

					$html	= call_user_func_array( array($plugin, $method) , $args);
					
				} 
				else
				{
					
						$html = $act->content;
				}
				
		}
		$cache->store($html, $cacheid,array('activities'));
		return $html;
	}
		 	
	/**
	 * Return an array of activity data
	 * 
	 * @param mixed $type string or arrayn or string
	 */	 	
	private function _getData( $options )
	{		
		$dispatcher =& CDispatcher::getInstanceStatic();
		$observers =& $dispatcher->getObservers();
		$plgObj = false;
		for ($i = 0; $i < count($observers); $i++)
		{
			if ($observers[$i] instanceof plgCommunityWordfilter)
			{
				$plgObj = $observers[$i];
			}
		}
		
		// Default params
		$default = array(
			'actor' =>0, 
			'target' => 0,
			'date' => null, 
			'app' => null,
			'cid' => null, // don't filter with cid
			'groupid' => null,
			'eventid' => null,
			'maxList' => 20 ,
			'type' => '' , 
			'exclusions' => null , 
			'displayArchived' => false 
		);
		$options = array_merge($default, $options);
		extract($options);
		
		CFactory::load('libraries', 'mapping');
		CFactory::load('libraries', 'wall');
		CFactory::load('libraries', 'groups');
		CFactory::load('libraries', 'events');
		CFactory::load('helpers', 'friends');
		
		$activities = CFactory::getModel('activities');
		$appModel	= CFactory::getModel('apps');
		$html 		= '';
		$numLines 	= 0;
		$my			= CFactory::getUser();
		$actorId	= $actor;
		$htmlData 	= array();
		$config		= CFactory::getConfig();
        		
		//Get blocked list
		$model		   = CFactory::getModel('block');
		$blockLists    = $model->getBanList($my->id);
		$blockedUserId = array();
		
		foreach($blockLists as $blocklist)
		{
		    $blockedUserId[] = $blocklist->blocked_userid; 
        }

        // Exclude banned userid
        if( !empty($target) && !empty($blockedUserId) )
        {
            $target = array_diff($target,$blockedUserId);	
		}
		
		if( !empty($app))
		{
			$rows = $activities->getAppActivities( $options );
		}
		else
		{
			$rows = $activities->getActivities( $actor, $target, $date, $maxList , $config->get('respectactivityprivacy') , $exclusions , $displayArchived );
		}
		$day = -1;
		

		// If exclusion is set, we need to remove activities that arrives
		// after the exclusion list is set.
		// Inject additional properties for processing
		for($i = 0; $i < count($rows); $i++) 
		{
			$row			=& $rows[$i];
			
			// A 'used' activities = activities that has been aggregated
			$row->used 		= false;

			// If the id is larger than any of the exclusion list,
			// we simply hide it
			if(isset($exclusion) && $exclusion > 0 && $row->id > $exclusions){
				$row->used 		= true;
			}
		}
		
		unset($row);
		
		
		$dayinterval 	= ACTIVITY_INTERVAL_DAY;
		$lastTitle 		= '';

		// Experimental Viewer Sensitive Profile Status
		$viewer	= CFactory::getUser()->id;
		$view	= JRequest::getCmd('view');
		foreach($rows as $row)
		{
			/*
			if ($row->app=='profile')
			{
				// strip off {actor} and {target} from the previous format
				$row->title		= CString::str_ireplace('{actor} to {target}', '', $row->title);
				$row->title		= CString::str_ireplace('{actor}', '', $row->title);
				$row->title		= CString::str_ireplace('{target}', '', $row->title);
				// self-post status and status from other on viewer's profile - don't display target
				// @todo: this really need to go to the template instead
				$titleString	= ($row->actor == $row->target || $row->target == 0 ) ? '{actor}' : '{actor} <span class="com_icons com_icons12 com_icons-inline com_icons-rarr">»</span> {target}';
				$titleString	= '<div class="newsfeed-content-actor">'.$titleString. '</div>%1$s';
				$row->title		= JText::sprintf($titleString,$row->title);
			}
			*/

			if ($row->app=='events.wall' || $row->app=='groups.wall')
			{
                                //add actor
				//$row->title		= JText::sprintf('COM_COMMUNITY_ACTIVITIES_STATUS_MESSAGE',$row->title);
			}
		}

		for($i = 0; $i < count($rows) && (count($htmlData) <= $maxList ); $i++) 
		{
			$row		= $rows[$i];
			$oRow		=& $rows[$i];	// The original object
			
			// store aggregated activities
			$oRow->activities = array();

			if(!$row->used && count($htmlData) <= $maxList )
			{
				$oRow	=& $rows[$i];
				
				if(!isset($row->used))
				{
					$row->used = false;
				}
				
				if($day != $row->getDayDiff() )
				{
					$act		= new stdClass();
					$act->type	= 'content';
					$day		= $row->getDayDiff();
					
					if($day == 0)
					{
						$act->title = JText::_('TODAY');
					}
					else if($day == 1)	
					{
						$act->title = JText::_('COM_COMMUNITY_ACTIVITIES_YESTERDAY');
					}
					else if($day < 7)
					{
						$act->title = JText::sprintf('COM_COMMUNITY_ACTIVITIES_DAYS_AGO', $day);
					}
					else if(($day >= 7) && ($day < 30))
					{
						$dayinterval = ACTIVITY_INTERVAL_WEEK;						
						$act->title = (intval($day/$dayinterval) == 1 ? JText::_('COM_COMMUNITY_ACTIVITIES_WEEK_AGO') : JText::sprintf('COM_COMMUNITY_ACTIVITIES_WEEK_AGO_MANY', intval($day/$dayinterval)));
					}	
					else if(($day >= 30))
					{
						$dayinterval = ACTIVITY_INTERVAL_MONTH;
						$act->title = (intval($day/$dayinterval) == 1 ? JText::_('COM_COMMUNITY_ACTIVITIES_MONTH_AGO') : JText::sprintf('COM_COMMUNITY_ACTIVITIES_MONTH_AGO_MANY', intval($day/$dayinterval)));
					}
					
					// set to a new 'title' type if this new one has a new title
					// only add if this is a new title
					if($act->title != $lastTitle)
					{
						$lastTitle 	= $act->title;
						$act->type 	= 'title'; 
						$htmlData[] = $act;
					}
				}
				
				$act = new stdClass();
				$act->type = 'content';
				
				// Set to compact view if necessary
				// This method is a bit crude, but we have no other reliable data
				// to choose which will go to compact view
				
				// Attend an event
				$act->compactView		= !( strpos( $oRow->params , 'action=events.attendence.attend') === FALSE );
				$act->compactView		=  $act->compactView || !( strpos( $oRow->params , '"action":"events.attendence.attend"') === FALSE );
				
				// Create an event
				$act->compactView		= $act->compactView || !(strpos( $oRow->params , 'action=events.create') === FALSE);				
				$act->compactView		= $act->compactView || !(strpos( $oRow->params , '"action":"events.create"') === FALSE);				

				// Update/join group
				$act->compactView		= $act->compactView || ($oRow->app == 'groups' && empty($oRow->content));
				
				// Add as friend
				$act->compactView		= $act->compactView || ($oRow->app == 'friends');
				
				// Add/Remove app. This is tricky since string is hard-coded
				// and no other info is available
				$act->compactView		= $act->compactView || ($oRow->title == JText::_('COM_COMMUNITY_ACTIVITIES_APPLICATIONS_ADDED') );
				
				// Feature a user
				$act->compactView		= $act->compactView || ($oRow->app == 'users');
				
				$title 	= $row->title;
				$app 	= $row->app;
				$cid 	= $row->cid;
				$actor 	= $row->actor;
				
				//Check for event or group title if exists
				if($row->eventid){
					$eventModel	= CFactory::getModel('events');
					$act->appTitle  = $eventModel->getTitle($row->eventid);
				}
				
				else if($row->groupid){
					$groupModel	= CFactory::getModel('groups');
					$act->appTitle  = $groupModel->getGroupName($row->groupid);
				}
				
				for($j = $i; ($j < count($rows)) && ($row->getDayDiff() == $day); $j++)
				{
					$row = $rows[$j];			
					// we aggregate stream that has the same content on the same day.
					// we should not however aggregate content that does not support
					// multiple content. How do we detect? easy, they don't have
					// {multiple} in the title string
					
					// However, if the activity is from the same user, we only want 
					// to show the laste acitivity
					if( ($row->getDayDiff() == $day) 
						&& ($row->title  == $title) 
						&& ($app == $row->app) 
						&& ($cid == $row->cid )
						
						&& ( 
							( JString::strpos($row->title, '{/multiple}') !== FALSE )
							||
							($row->actor == $actor )
							)
						 
						)
					{
						// @rule: If an exclusion is added, we need to fetch activities without these items.
						// Aggregated activities should also be excluded.
						$row->used 			= true;
						$oRow->activities[] = $row;
					}
				}
				
				$app	= !empty($oRow->app) ? $this->_appLink($oRow->app, $oRow->actor, $oRow->target,$oRow->title) : '';
				
				$oRow->title	= CString::str_ireplace('{app}', $app, $oRow->title);    
				
				$favicon = '';
				
				
				// this should not really be empty
				if(!empty($oRow->app))
				{
					// Favicon override with group image for known group stream data
					//if(in_array($oRow->app, CGroups::getStreamAppCode())){
					if( $oRow->groupid ){
						// check if the image icon exist in template folder
						$favicon = JURI::root(). 'components/com_community/assets/favicon/groups.png';
						if ( JFile::exists(JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'templates' . DS . $config->get('template') . DS . 'images' . DS . 'favicon' . DS .'groups.png') )
						{
							$favicon = JURI::root(). 'components/com_community/templates/'.$config->get('template').'/images/favicon/groups.png';
						}
					
					}
					
					// Favicon override with event image for known event stream data
					// This would override group favicon
					if( $oRow->eventid ){
						// check if the image icon exist in template folder
						$favicon = JURI::root(). 'components/com_community/assets/favicon/events.png';
						if ( JFile::exists(JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'templates' . DS . $config->get('template') . DS . 'images' . DS . 'favicon' . DS .'groups.png') )
						{
							$favicon = JURI::root(). 'components/com_community/templates/'.$config->get('template').'/images/favicon/events.png';
						}
					}
					
					// If it is not group or event stream, use normal favicon search
					if( !($oRow->groupid || $oRow->eventid) )
					{

						
						// check if the image icon exist in template folder
						if ( JFile::exists(JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'templates' . DS . $config->get('template') . DS . 'images' . DS . 'favicon' . DS . $oRow->app.'.png') )
						{
							$favicon = JURI::root(). 'components/com_community/templates/'.$config->get('template').'/images/favicon/'.$oRow->app.'.png';
						}
						else
						{
							// check if the image icon exist in asset folder
							if ( JFile::exists(JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'assets' . DS . 'favicon' . DS . $oRow->app.'.png') )
							{
								$favicon = JURI::root(). 'components/com_community/assets/favicon/'.$oRow->app.'.png';
							}
							elseif ( JFile::exists(CPluginHelper::getPluginPath('community',$oRow->app) . DS . $oRow->app . DS . 'favicon.png') )
							{
								$favicon = JURI::root(). CPluginHelper::getPluginURI('community',$oRow->app) . '/' .$oRow->app.'/favicon.png';
							}
							else
							{
								$favicon = JURI::root(). 'components/com_community/assets/favicon/default.png';
							}
						}

					}
				}
				else
				{
				    $favicon = JURI::root(). 'components/com_community/assets/favicon/default.png';
				}

				$act->favicon = $favicon;
				
				$target = $this->_targetLink($oRow->target, true );
				$oRow->title	= CString::str_ireplace('{target}', $target, $oRow->title);
	
				if(count($oRow->activities) > 1)
				{
					
					// multiple
					$actorsLink = '';					
					foreach( $oRow->activities as $actor )
					{
						if(empty($actorsLink))
							$actorsLink = $this->_actorLink(intval($actor->actor));
						else {
							// only add if this actor is NOT already linked
							$alink = $this->_actorLink(intval($actor->actor));
							$pos = strpos($actorsLink, $alink);
							if ($pos === false) {
								$actorsLink .= ', '.$alink;
							}
						}
					}
					$actorLink = $this->_actorLink(intval($oRow->actor));
					
					$count = count($oRow->activities);
					
					$oRow->title 	= preg_replace('/\{single\}(.*?)\{\/single\}/i', '', $oRow->title);
					$search  		= array('{multiple}','{/multiple}');
					
					$oRow->title	= CString::str_ireplace($search, '', $oRow->title);
					
					//Joomla 1.6 CString::str_ireplace issue of not replacing correctly strings with backslashes 	
					$oRow->title = str_ireplace($search, '', $oRow->title);
					
					$oRow->title	= CString::str_ireplace('{actors}'	, $actorsLink, $oRow->title);
					$oRow->title	= CString::str_ireplace('{actor}'	, $actorLink, $oRow->title);
					$oRow->title	= CString::str_ireplace('{count}'	, $count, $oRow->title);
				} else {
					// single
					$actorLink = $this->_actorLink(intval($oRow->actor));
					
					$oRow->title = preg_replace('/\{multiple\}(.*)\{\/multiple\}/i', '', $oRow->title);
					$search  = array('{single}','{/single}');
					$oRow->title	= CString::str_ireplace($search, '', $oRow->title);
					$oRow->title	= CString::str_ireplace('{actor}', $actorLink, $oRow->title);
				}
				
				// If the param contains any data, replace it with the content
				preg_match_all("/{(.*?)}/", $oRow->title, $matches, PREG_SET_ORDER);
				if(!empty( $matches )) 
				{
					$params = new CParameter( $oRow->params );
					foreach ($matches as $val) 
					{	
						
						$replaceWith = $params->get($val[1], null);
						
						//if the replacement start with 'index.php', we can CRoute it
						if( strpos($replaceWith, 'index.php') === 0){
							$replaceWith = CRoute::_($replaceWith);
						}
						
						if( !is_null( $replaceWith ) ) 
						{
							$oRow->title	= CString::str_ireplace($val[0], $replaceWith, $oRow->title);
						}
					}
				}
				
				
 
				// Format the title 
				$oRow->title = ($plgObj) ? $plgObj->_censor($oRow->title) : $oRow->title;
				$oRow->title = $this->_formatTitle($oRow);
				$act->id 		= $oRow->id;
				$act->title 	= $oRow->title;
				$act->actor 	= $oRow->actor;
				$act->target 	= $oRow->target;
				$act->content	= $this->getActivityContent( $oRow );
				
				
				$timeFormat		= $config->get( 'activitiestimeformat' );
				$dayFormat		= $config->get( 'activitiesdayformat' );
				$date			= CTimeHelper::getDate($oRow->created);
                                 
				$createdTime = '';
				if($config->get('activitydateformat') == COMMUNITY_DATE_FIXED)
				{
					$createdTime 	= $date->toFormat($dayinterval == ACTIVITY_INTERVAL_DAY ? $timeFormat : $dayFormat ,true);    
				}
				else
				{
					$createdTime	= CTimeHelper::timeLapse($date);
				}
				$act->created 			= $createdTime;
				$act->createdDate 		= (C_JOOMLA_15==1)?$date->toFormat(JText::_('DATE_FORMAT_LC2')):$date->Format(JText::_('DATE_FORMAT_LC2'));
				$act->createdDateRaw	= $oRow->created;
				$act->app 				= $oRow->app;
				$act->eventid			= $oRow->eventid;
				$act->groupid			= $oRow->groupid;
				$act->group_access		= $oRow->group_access;
				$act->event_access		= $oRow->event_access;
				$act->location			= $oRow->getLocation();
				$act->commentCount		= $oRow->getCommentCount();
				$act->commentAllowed	= $oRow->allowComment();
				$act->commentLast		= $oRow->getLastComment();
				$act->likeCount			= $oRow->getLikeCount();
				$act->likeAllowed		= $oRow->allowLike();
				$act->isFriend			= $my->isFriendWith( $act->actor );
				$act->isMyGroup			= $my->isInGroup($oRow->groupid);
				$act->isMyEvent			= $my->isInEvent($oRow->eventid);
				$act->userLiked			= $oRow->userLiked($my->id);
				
				
				$htmlData[] = $act;
			}
		}
		
		$objActivity				= new stdClass();
		$objActivity->data			= $htmlData;
		
		return $objActivity;
	}
	
	/**
	 * Return html formatted activity stream for apps stream
	 * 
	 */
	public function getAppHTML( $options )
	{
		// Default options
		$default = array(
			'actor'	=> 0,
			'target' => 0,
			'app' => null,
			'cid' => null,
			'groupid' => null,
			'eventid' => null,
			'date' => null,
			'filter' => null,
			'latestId' => 0,			
			'maxEntry' => 20,
			'idprefix' => null,
			'showActivityContent' => true,
			'showMoreActivity' => true,
			'exclusions' =>null,
			'displayArchived' => true	
		);
		$options = array_merge($default, $options);
		extract($options);
		
		jimport('joomla.utilities.date');
		$mainframe =& JFactory::getApplication();
		
		CFactory::load('helpers', 'url');
		CFactory::load('helpers', 'owner');
		CFactory::load('libraries', 'template');
		
		// Load the library needed
		CFactory::load('libraries', $app);
		
		$activities = CFactory::getModel('activities');
		$appModel	= CFactory::getModel('apps');
		$config 	= CFactory::getConfig();
		$html		= '';
		$numLines 	= 0;
		$my			= CFactory::getUser();
		$actorId	= $actor;
		$htmlData	= array();
		$tmpl 		= new CTemplate();
		
		// get the social object classname
		// It has to implement CStreamable
		$className = 'C'.ucfirst ($app);
		$appLib = new $className();
		
		// Make sure the lib implement CStreamable
		if( !( $appLib instanceof CStreamable ) ){
			JError::raiseError( 500, $className );
		}
		
		// Check if the current post is belong to the current user
		$isMine	 = false;
		$appCode = $appLib->getStreamAppCode();
		
		$maxList		= ($maxEntry == 0) ? $config->get('maxactivities') : $maxEntry;
		
		$config			= CFactory::getConfig();
		$isSuperAdmin	= COwnerHelper::isCommunityAdmin();
		$data			= $this->_getData( 
				array( 
					'actor' => $actor, 
					'target' => $target, 
					'date' => $date, 
					'maxList' => $maxList, 
					'app' => $appCode , 
					'cid' => $cid,
					'groupid' => $groupid,
					'eventid' => $eventid,
					'exclusions' => $exclusions , 
					'displayArchived' =>$displayArchived ) );
					
		// We should also exclude any data that earlier (hence larger id) than any
		// of the current exclusion list
		$exclusions		= isset($data->exclusions) ? $data->exclusions: null;
		$htmlData		= $data->data;
		
		// Show welcome message on activity stream if this is a fresh installation
		if( $activities->getTotalActivities() == 0 && $my->id < 1){
			$tmpl->set( 'freshInstallMsg'	, JText::_('COM_COMMUNITY_ACTIVITIES_FRESH_INSTALL_MESSAGE') );
		}
		
		//hide show more if there is no more results
		if(count($htmlData) <= $config->get('maxactivities') ){
			$showMoreActivity = false;
		}
		
		$tmpl->set( 'showMoreActivity'   , $showMoreActivity )
			->set( 'exclusions'	    , $exclusions )
			->set( 'isMine'		    , $isMine)
			->set( 'activities'		, $htmlData)
			->set( 'idprefix'	    , $idprefix)
			->set( 'my'				, $my)
			->set( 'apptype'		, $options['apptype'])
			->set( 'isSuperAdmin'	, $isSuperAdmin)
			->set( 'config'		    , $config )
			->set( 'showMore'	    , $showActivityContent )
			->set( 'filter'		    , $filter )
			->set( 'latestId'	    , $latestId)
			->set( 'groupId'	    , $groupid)
			->set( 'eventId'	    , $eventid)
			->set( 'isMember'	    , $appLib->isAllowStreamPost($my->id, $options));
		
		//if in module, disable Comment, Like
		if($idprefix == ''){
			$showActivityComment = 1;
			$showActivityLike = 1;
		} else {
			$showActivityComment = 0;
			$showActivityLike = 0;
		}
		
		$data	=   $tmpl   ->set('showComment'	, $showActivityComment)
				    ->set('showLike'	, $showActivityLike)
				    ->fetch('activities.apps'); 
		
		return $data;
	}
	
	/**
	 * Return html formatted activity stream
	 * @access 	public
	 * @todo	Add caching	- Improve performance via caching 	 
	 * 
	 * @param type : can be a single string or array or string
	 */	 	
	public function getHTML( $actor, $target, $date = null, $maxEntry=0 , $type = '', $idprefix = '', $showActivityContent = true , $showMoreActivity = false , $exclusions = null , $displayArchived = false, $filter='all', $latestId = 0)
	{
		
		jimport('joomla.utilities.date');
		$mainframe =& JFactory::getApplication();
		
		CFactory::load('helpers', 'url');
		CFactory::load('helpers', 'owner');
		CFactory::load('libraries', 'template');
		$activities = CFactory::getModel('activities');
		$appModel	= CFactory::getModel('apps');
		$config 	= CFactory::getConfig();
		$html		= '';
		$numLines 	= 0;
		$my			= CFactory::getUser();
		$htmlData	= array();
		$tmpl 		= new CTemplate();

		//maxlist from profile will respect the max activities on the profile settings
		$targetUser			= CFactory::getUser($actor);
		$params		=& $targetUser->getParams();
		
		if($maxEntry == 0){
			$maxList		= ($type == 'profile') ? $params->get( 'activityLimit' , $config->get('maxactivities') ) : $config->get('maxactivities');
		}else{
			$maxList = $maxEntry;
		}
		
		$config			= CFactory::getConfig();
		$isSuperAdmin	= COwnerHelper::isCommunityAdmin();
		$data			= $this->_getData(
							array(
							'actor'  => $actor,
							'target' => $target, 
							'date' => $date, 
							'maxList' => $maxList, 
							'type' => $type , 
							'exclusions' => $exclusions , 
							'displayArchived' =>$displayArchived )
						);

		$htmlData		= $data->data;
		
		// Show welcome message on activity stream if this is a fresh installation
		if( $activities->getTotalActivities() == 0 && $my->id < 1){
			$tmpl->set( 'freshInstallMsg'	, JText::_('COM_COMMUNITY_ACTIVITIES_FRESH_INSTALL_MESSAGE') );
		}
		
		//hide show more if there is no more results
		if(count($htmlData) <= $maxList){
			$showMoreActivity = false;
		}
		
		$tmpl	->set( 'showMoreActivity'   , $showMoreActivity )
				->set( 'activities'	, $htmlData)
				->set( 'idprefix'	    , $idprefix)
				->set( 'my'		    , $my)
				->set( 'isSuperAdmin'	, $isSuperAdmin)
				->set( 'config'		    , $config )
				->set( 'showMore'	    , $showActivityContent )
				->set( 'filter'		    , $filter )
				->set( 'latestId'	    , $latestId);
		
		//if in module, disable Comment, Like
		if($idprefix == ''){
			$showActivityComment = 1;
			$showActivityLike = 1;
		} else {
			$showActivityComment = 0;
			$showActivityLike = 0;
		}
		
		$data	=   $tmpl->set('showComment'	, $showActivityComment)
						 ->set('showLike'	, $showActivityLike)
						 ->set('actorId'	, $actor)
						 ->fetch('activities.index'); 
		
		return $data;
	}
	
	/**
	 * Return array of rss-feed compatible data
	 */	 	
	public function getFEED($actor, $target, $date = null, $maxEntry=20,  $type='')
	{
		jimport('joomla.utilities.date');
		$mainframe =& JFactory::getApplication();
		
		$activities = CFactory::getModel('activities');
		$appModel	= CFactory::getModel('apps');
		$html = '';
		$numLines = 0;
		$my			= CFactory::getUser();
		$actorId	= $actor;
		$feedData 	= array();

		$htmlData = $this->_getData(
				array( 'actor' => $actor, 
					'target' => $target, 
					'date' => $date, 
					'maxList' => $maxEntry, 
					'type' => $type ));
		return $htmlData;
	}
	
	/**
	 * Return how many days has lapse since
	 * @param	JDate date The date you want to compare	 	
	 * @access 	private
	 */	 	
	private function _daysLapse($date){
		require_once (JPATH_COMPONENT.DS.'helpers'.DS.'time.php');
		$now =& JFactory::getDate();
		
		$html ='';
		$diff = CTimeHelper::timeDifference($date->toUnix(), $now->toUnix());
		return $diff['days'];
	}
	
	
	/**
	 * Return html formatted lapse time
	 * @param	JDate date The date you want to compare	 	
	 * @param boolean $showFull default to be true to show xx Hours xx Minutes, if false will only show Hours or Minutes
	 * @access 	private	 
	 */	 	
	public function _createdLapse(&$date, $showFull = true)
	{
		CFactory::load( 'helpers' , 'time' );
		
		$now	=& JFactory::getDate();
		$html	= '';
		$diff	= CTimeHelper::timeDifference($date->toUnix(), $now->toUnix());
		

		if( !empty($diff['days']) )
		{
			$days		= $diff['days'];
			$months		= ceil( $days / 30 );
			
			switch( $days )
			{
				case ($days == 1):
				
					// @rule: Something that happened yesterday
					$html	.= JText::_( 'COM_COMMUNITY_LAPSED_YESTERDAY' );

				break;
				case ( $days > 1 && $days < 7 && $days < 30 ):
				
					// @rule: Something that happened within the past 7 days
					$html	.= JText::sprintf( 'COM_COMMUNITY_LAPSED_DAYS' , $days ) . ' ';

				break;
				case ( $days > 1 && $days > 7 && $days < 30 ):
				
					// @rule: Something that happened within the month but after a week
					$weeks	= round( $days / 7 );
					$html	.= JText::sprintf( CStringHelper::isPlural( $weeks ) ? 'COM_COMMUNITY_LAPSED_WEEK_MANY' : 'COM_COMMUNITY_LAPSED_WEEK' , $weeks ) . ' ';	

				break;
				case ( $days > 30 && $days < 365 ):
				
					// @rule: Something that happened months ago
					$months	= round( $days / 30 );
					$html	.= JText::sprintf( CStringHelper::isPlural( $months ) ? 'COM_COMMUNITY_LAPSED_MONTH_MANY' : 'COM_COMMUNITY_LAPSED_MONTH' , $months ) . ' ';

				break;
				case ( $days > 365 ):
				
					// @rule: Something that happened years ago
					$years	= round( $days / 365 );
					$html	.= JText::sprintf( CStringHelper::isPlural( $years ) ? 'COM_COMMUNITY_LAPSED_YEAR_MANY' : 'COM_COMMUNITY_LAPSED_YEAR' , $years ) . ' ';

				break;
			}
		}
		else
		{
			// We only show he hours if it is less than 1 day
			if(!empty($diff['hours']))				
				$html .= JText::sprintf('COM_COMMUNITY_LAPSED_HOURS', $diff['hours']) . ' ';
			
			if (($showFull && !empty($diff['hours'])) || (empty($diff['hours'])))
			{
				if(!empty($diff['minutes']))
					$html .= JText::sprintf('COM_COMMUNITY_LAPSED_MINUTES', $diff['minutes']) . ' ';
			}
		}
		
		if(empty($html)){
			$html .= JText::_('COM_COMMUNITY_LAPSED_LESS_THAN_A_MINUTE');
		}
		
		if($html != JText::_('COM_COMMUNITY_LAPSED_YESTERDAY'))
			$html .= JText::_('COM_COMMUNITY_LAPSED_AGO');

		return $html;
	}
	
	/**
	 * Return html formatted link to actor
	 * @param	integer id Actor/user id	 	
	 * @access 	private	 
	 */	
	private function _actorLink($id)
	{
		static $instances = array();
		
		if( empty($instances[$id]))
		{
			$my			=& JFactory::getUser();
			$view 		= JRequest::getVar('view', 'frontpage', 'REQUEST');
			$format		= JRequest::getVar('format', 'html', 'REQUEST');
			$linkName	= ($id==0)? false : true;
			$user		= CFactory::getUser($id);
			$name = $user->getDisplayName();
			
			// Wrap the name with link to his/her profile
			$html		= $name;
			
			if($linkName)
			{
				$html = '<a class="actor-link" href="'.CUrlHelper::userLink($id).'">'.$name.'</a>';
			}
			
			$instances[$id] = $html;
		}
		
		return $instances[$id];
	}
	
	/**
	 * Return html formatted link to target
	 * @param	integer id Target/user id	 	
	 * @access 	private	 
	 */	
	private function _targetLink( $id, $onApp=false )
	{
		static $instances = array();
		
		if( empty($instances[$id]) ){
		
		$my			=& JFactory::getUser();
		$linkName	= ($id==0)? false : true;
		
// 		if(($id == $my->id) && ($id == $user->id)){
// 			$name = $onApp ? 'your' : 'you';
// 			$linkName = false;
// 		} else 
		//{
			$user 	= CFactory::getUser($id);
			$name = $user->getDisplayName();
		//}
		
		// Wrap the name with link to his/her profile
		$html = $name;
		if($linkName)
			$html = '<a href="'.CUrlHelper::userLink($id).'">'.$name.'</a>';
			
		$instances[$id] = $html;
		}
		return $instances[$id];
	}
	
	/**
	 * Perform necessary formatting on the title for display in the stream
	 * @param type $row 
	 */
	private function _formatTitle($row)
	{		
		// We will need to replace _QQQ_ here since
		// CKses will reformat the link
		$row->title = CTemplate::quote($row->title);
		
		// If the title start with actor's name, and it is a normal status, remove them!
		// Otherwise, leave it as the old style
		if(strpos( $row->title, '<a class="actor-link"' ) !== false && isset($row->actor) && ($row->app == 'profile')){
			$pattern = '/(<a class="actor-link".*?' . '>.*?<\/a>)/';
			$row->title = preg_replace($pattern, '', $row->title);
		}
		
		return CKses::kses($row->title, CKses::allowed() );
	}
	
	/**
	 * Return html formatted link to application
	 * @param	integer id Actor/user id	 	
	 * @access 	private	
	 * @todo	Add link to known application/views 	 
	 */	
	private function _appLink($name, $actor = 0, $userid = 0, $title = ''){
		
		if(empty($name))
			return '';
		
// 		if( empty($instances[$id.$actor.$userid]) )
// 		{
		$appModel	= CFactory::getModel('apps');
		$url = '';
		
		// @todo: check if this app exist
		if(true) {
			// if no target specified, we use actor
			if($userid == 0) 
				$userid= $actor;
				
			if( $userid != 0 
				&& $name != 'profile'
				&& $name != 'news_feed'
				&& $name != 'photos'
				&& $name != 'friends')
				{
					
				$url = CUrlHelper::userLink($userid) . '#app-' . $name;
				if($title == JText::_('COM_COMMUNITY_ACTIVITIES_APPLICATIONS_REMOVED')){
					$url = $appModel->getAppTitle($name);
				}else{
					$url = '<a href="' . $url .'" >'. $appModel->getAppTitle($name) . '</a>';
				}
			}else{
				$url = $appModel->getAppTitle($name);
			}
			
		}
		return $url;
	}
	
	/**
	 * Retrieve a list of custom activities which the admin can push
	 *
	 * @return  Array   An array of custom activities
	 **/
	public function getCustomActivities()
	{
		// These are default activities pre-defined by the system
		$messages	= array();
		$messages['system.registered']			= JText::sprintf( 'COM_COMMUNITY_TOTAL_USERS_REGISTERED_THIS_MONTH' );
		$messages['system.populargroup']		= JText::sprintf( 'COM_COMMUNITY_ACTIVITIES_POPULAR_GROUP' );
		$messages['system.totalphotos']			= JText::sprintf( 'COM_COMMUNITY_ACTIVITIES_TOTAL_PHOTOS' );
		$messages['system.popularprofiles']		= JText::sprintf( 'COM_COMMUNITY_ACTIVITIES_TOP_PROFILES', 5 );
		$messages['system.popularphotos']		= JText::sprintf( 'COM_COMMUNITY_ACTIVITIES_TOP_PHOTOS', 5 );
		$messages['system.popularvideos']		= JText::sprintf( 'COM_COMMUNITY_ACTIVITIES_TOP_VIDEOS', 5 );
		
		// Triggers to allow 3rd party to push their custom messages as well.
		$apps	=& CAppPlugins::getInstance();
		$apps->loadApplications();
		
		$args	= array();
		$args[]	=& $messages;

		$apps->triggerEvent( 'onCustomActivityDisplay' , $args );
		
		return $messages;
	}
        
        /**
         *
         * @param type $filter
         * @param type $userId
         * @param type $view
         * @param type $showMore
         * @return type 
         */
	public function getActivitiesByFilter($filter='all', $userId=0, $view='', $showMore=true)
	{
		$config = CFactory::getConfig();
		$act = new CActivityStream();
		
		if($userId==0){
			// Legacy code, some module might still use the old code
			$user = CFactory::getRequestUser();
		} else {
			$user = CFactory::getUser($userId);
		}

		jimport('joomla.utilities.date');
		$friendsModel	= CFactory::getModel('friends');

		$memberSince = CTimeHelper::getDate($user->registerDate);
		//$friendIds = $friendsModel->getFriendIds($user->id);
		$friendIds = $user->getFriendIds();
		
		switch($filter)
		{				
			case "active-profile" :
				$target = array($user->id);
				$params		=& $user->getParams();
				$actLimit	= ($view == 'profile') ? $params->get( 'activityLimit' , $config->get('maxactivities') ) : $config->get('maxactivities');
				$html = $act->getHTML($user->id, $target, '', $actLimit, $view , '', true, $showMore, null, false, 'active-profile');
				break;

			case "me-and-friends" : 
				$user	=& JFactory::getUser();
				$html = $act->getHTML($user->id, $friendIds, $memberSince , 0 , $view , '' , true , $showMore, null, false, 'me-and-friends' );
				break;

			case "active-user-and-friends" :
			case "active-profile-and-friends" :
				$params		=& $user->getParams();
				$actLimit	= ($view == 'profile') ? $params->get( 'activityLimit' , $config->get('maxactivities') ) : $config->get('maxactivities');
				$html = $act->getHTML($user->id, $friendIds, $memberSince, $actLimit, $view , '', true, $showMore, null, false, 'active-profile-and-friends' );
				break;

			case "all":
			default :
				$html = $act->getHTML('', '', null , 0 , $view , '' , true , $showMore );
				break;
		}

		return $html;
	}

	/**
	 * Remove activities by the given apps and wall id
	 *
	 * @param type $option array search criteria.
     * @param type $wallId int Wall post id.
	 * @since 2.4
	 *
	 */
	public function removeWallActivities($option, $wallId)
	{
		// Return all activities by the given apps and specific criteria.
		$activitiesModel	= CFactory::getModel( 'activities' );
		$activities         = $activitiesModel->getAppActivities($option);

		// Generate target activity id from param's wall id
		$activityID = 0;
		$params	    = new CParameter();

		foreach ($activities as $objAct) {
			$params->bind($objAct->params);
			if ($params->get('wallid') == $wallId) {
				$activityID = $objAct->id;
				break;
			}
		}

		// Remove activity.
		if ( $activityID > 0 ) {
			$activity	= JTable::getInstance( 'Activity' , 'CTable' );
			$activity->load( $activityID );
			$activity->delete($option['app']);
		}
	}
}

/**
 * Maintain classname compatibility with JomSocial 1.6 below
 */
class CActivityStream extends CActivities
{}
