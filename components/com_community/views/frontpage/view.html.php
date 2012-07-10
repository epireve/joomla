<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
jimport( 'joomla.utilities.arrayhelper');

class CommunityViewFrontpage extends CommunityView
{
	public function display()
	{
		$mainframe 	= JFactory::getApplication();		
		$config 	= CFactory::getConfig();
		$document 	= JFactory::getDocument();
		
		$usersConfig	=& JComponentHelper::getParams( 'com_users' );
                $useractivation = $usersConfig->get( 'useractivation' );
                
 		$document->setTitle( JText::sprintf('COM_COMMUNITY_FRONTPAGE_TITLE', $config->get('sitename')));

		$my 			 = CFactory::getUser();
		$model 			 = CFactory::getModel('user');
		$avatarModel 	 = CFactory::getModel('avatar');
		$status 		 = CFactory::getModel('status');	
		
		$frontpageUsers	 = intval( $config->get('frontpageusers') );
		$document->addScriptDeclaration("var frontpageUsers	= ".$frontpageUsers.";");
		
		$frontpageVideos = intval( $config->get('frontpagevideos') );
		$document->addScriptDeclaration("var frontpageVideos	= ".$frontpageVideos.";");
		
		$status			 = $status->get( $my->id );
		

		$feedLink = CRoute::_('index.php?option=com_community&view=frontpage&format=feed');
		$feed = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_SUBSCRIBE_RECENT_ACTIVITIES_FEED') . '" href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );

		CFactory::load( 'libraries' , 'tooltip' );
		CFactory::load( 'libraries' , 'activities' );

		// Process headers HTML output
		$headerHTML	= '';
		$tmpl		= new CTemplate();
		$alreadyLogin = 0;
		
		if( $my->id != 0 )
		{
			$headerHTML	  = $tmpl->fetch( 'frontpage.members');
			$alreadyLogin = 1;
		}
		else
		{
			$uri	= CRoute::_('index.php?option=com_community&view=' . $config->get('redirect_login') , false );
			$uri	= base64_encode($uri);
			
			$fbHtml	= '';

			if( $config->get('fbconnectkey') && $config->get('fbconnectsecret') )
			{
				CFactory::load( 'libraries' , 'facebook' );
				$facebook	= new CFacebook();
				$fbHtml		= $facebook->getLoginHTML();
			}

			$usersConfig =& JComponentHelper::getParams('com_users');
                        $tmpl->set('useractivation' , $useractivation);
			$headerHTML =	$tmpl	->set( 'fbHtml'		, $fbHtml )
						->set( 'return'		, $uri )			
						->set( 'config'		, $config )
						->set( 'usersConfig'	, $usersConfig )
						->fetch( 'frontpage.guests' );
		}
		
		$my		=   CFactory::getUser();
		$totalMembers	=   $model->getMembersCount();
		
		unset( $tmpl );

		$latestMembersData  =	$this->_cachedCall('showLatestMembers', array( $config->get('frontpageusers') ), '', array( COMMUNITY_CACHE_TAG_FRONTPAGE ) );
		$latestMembersHTML  =	$latestMembersData['HTML'];

		$latestGroupsData   =	$this->_cachedCall('showLatestGroups', array( $config->get('frontpagegroups') ), '', array( COMMUNITY_CACHE_TAG_FRONTPAGE ) );
		$latestGroupsHTML   =	$latestGroupsData['HTML'];

		$latestVideoData    =	$this->showLatestVideos( $config->get('frontpagevideos'));
		$latestVideoHTML    =	$latestVideoData['HTML'];

		$latestPhotosData   =	$this->_cachedCall('showLatestPhotos', array(false), '', array( COMMUNITY_CACHE_TAG_FRONTPAGE ) );
		$latestPhotosHTML   =	$latestPhotosData['HTML'];

		$latestEventsData   =	$this->_cachedCall('showLatestEvents', array($config->get('frontpage_events_limit')), '', array( COMMUNITY_CACHE_TAG_FRONTPAGE ) );
		$latestEventsHTML   =	$latestEventsData['HTML'];

		$latestActivitiesData   =   $this->showLatestActivities();
		$latestActivitiesHTML   =   $latestActivitiesData['HTML'];
		
		$tmpl	=   new CTemplate();
		$tmpl	->set( 'totalMembers'	    , $totalMembers)
			->set( 'my'		    , $my )
			->set( 'alreadyLogin'	    , $alreadyLogin )
			->set( 'header'		    , $headerHTML )
			->set( 'onlineMembers'	    , $this->getOnlineMembers() )
			->set( 'userActivities'	    , $latestActivitiesHTML) 
			->set( 'config'		    , $config)
			->set( 'latestMembers'	    , $latestMembersHTML)
			->set( 'latestGroups'	    , $latestGroupsHTML)
		
		/** Compatibility fix . Deprecated in 2.2**/
		/*Deprecated in 2.2*/
		// $tmpl->set( 'latestPhotos'	    , $this->showLatestPhotos( true ) ); 
			->set( 'latestPhotosHTML'   , $latestPhotosHTML )

		/* Deprecated in 2.2 */
		//$tmpl->set( 'latestVideos'			, $this->showLatestVideos( $config->get('frontpagevideos') , true ) );
			->set( 'latestVideosHTML'   , $latestVideoHTML )
			->set( 'latestEvents'	    , $latestEventsHTML )
		
		/** As of 2.2 **/
			->set( 'customActivityHTML' , $this->getCustomActivityHTML() );
		/** Compatibility fix **/

		CFactory::load( 'helpers', 'string' );

		/* User status */
		CFactory::load( 'libraries', 'userstatus' );

		$status = new CUserStatus();
		
		if($my->authorise('community.view','frontpage.statusbox')){	
			// Add default status box
			CFactory::load('helpers', 'user');
			CUserHelper::addDefaultStatusCreator($status);

			if (COwnerHelper::isCommunityAdmin() && $config->get('custom_activity'))
			{
				$template	= new CTemplate();
				$template->set( 'customActivities', CActivityStream::getCustomActivities());

				$creator	=   new CUserStatusCreator('custom');
				$creator->title =   JText::_('COM_COMMUNITY_CUSTOM');
				$creator->html	=   $template->fetch('status.custom');

				$status->addCreator($creator);
			}

		}

		echo $tmpl  ->set('userstatus'	, $status)
			    ->fetch('frontpage.index');
	}
	
	public function getCustomActivityHTML()
	{
		$tmpl	= new CTemplate();
		return $tmpl	->set( 'isCommunityAdmin'   , COwnerHelper::isCommunityAdmin() )	
				->set( 'customActivities'   , CActivityStream::getCustomActivities() )
				->fetch( 'custom.activity' );
	}
	
	public function showLatestActivities()
	{
		$act		=   new CActivityStream();
		$config		=   CFactory::getConfig();
		$my		=   CFactory::getUser();
		$userActivities	=   '';
		
		if( $config->get('frontpageactivitydefault')=='friends' && $my->id != 0 )
		{
			$userActivities = CActivities::getActivitiesByFilter('active-user-and-friends', $my->id, 'frontpage', true);
		}
		else
		{
			$userActivities = CActivities::getActivitiesByFilter('all', $my->id, 'frontpage', true);
		}

		$activities = array();
		$activities['HTML'] = $userActivities;

		return $activities;
	}
	
	public function showMostActive($data = null){
	}
	
	/**
	 * Show listing of group with the most recent activities
	 */	 	
	public function showActiveGroup()
	{
		$groupModel 	= CFactory::getModel('groups');
		$activityModel	= CFactory::getModel('activities');
		$act	= new CActivityStream();
		
		$html = $act->getHTML( '', '', null, 10 , 'groups');
		
		return $html;
	}

	/**
	 * Retrieve the latest events
	 *
	 * @param	int	$total	The total number of events to retrieve
	 * @return	string	The html codes.	 
	 **/	 	 	 	
	public function showLatestEvents( $total = 5 )
	{
		$session = JFactory::getSession();
		$html = '';//$session->get('frontpage_events');
		if( !$html)
		{
			
		
		$tmpl		    =	new CTemplate();
		$frontpage_latest_events	= intval( $tmpl->params->get('frontpage_latest_events') );
		$html = '';
		$data = array();

		if( $frontpage_latest_events != 0 )
		{
			$model	= CFactory::getModel('Events');
			$result	= $model->getEvents( null , null , null , null , true , false , null , null , CEventHelper::ALL_TYPES , 0 , $total );
			$events	= array();

			foreach( $result as $row )
			{
				$event	=& JTable::getInstance( 'Event' , 'CTable' );
				$event->bind( $row );
				$events[]	= $event;
			}
			$tmpl = new CTemplate();
			$tmpl->set( 'events' , $events );

			$html = $tmpl->fetch('frontpage.latestevents');
		}
		}
		$session->set('frontpage_events', $html);
		$data['HTML'] = $html;
		return $data;
	}
		
	public function showLatestGroups( $total = 5 )
	{
		$tmpl			=   new CTemplate();
		$config			=   CFactory::getConfig();
		$showlatestgroups	=   intval(  $tmpl->params->get('showlatestgroups') );
		$html = '';
		$data = array();
		
		if( $showlatestgroups != 0 )
		{
			$groupModel	= CFactory::getModel('groups');
			$tmpGroups	= $groupModel->getAllGroups( null , null , null , $total );
			$groups		= array();

			$data = array();

			foreach($tmpGroups as $row)
			{
				$group	=& JTable::getInstance('Group','CTable');
				$group->bind( $row );
				$group->description = CStringHelper::truncate( $group->description, $config->get('tips_desc_length') );
				$groups[]	= $group;
			}

			$tmpl	=   new CTemplate();
			$html	=   $tmpl   ->setRef( 'groups',	$groups )
					    ->fetch('frontpage.latestgroup');
		}

		$data['HTML'] = $html;

		return $data;
	}
	
	public function showLatestVideos( $total = 5 , $raw = false )
	{
		$tmpl		    =	new CTemplate();
		$config				= CFactory::getConfig();
		$showlatestvideos	= intval( $tmpl->params->get('showlatestvideos') );
		$html = '';
		$data = array();
		
		if( $showlatestvideos != 0 )
		{
			$my		= CFactory::getUser();

			// Oversample the total so that we get a randomized value
			$oversampledTotal	= $total * COMMUNITY_OVERSAMPLING_FACTOR;

			CFactory::load( 'libraries', 'videos' );

			$videoModel 	= CFactory::getModel('videos');

			$videosfilter	= array(
				'published'	    =>	1,
				'status'	    =>	'ready',
				'permissions'	    =>	($my->id==0) ? 0 : 20,
				'or_group_privacy'  =>	0,
				'limit'		    =>	$oversampledTotal
			);

			$result			= $videoModel->getVideos($videosfilter, true);

			$videos	= array();
			// Bind with video table to inherit its method
			foreach($result as $videoEntry)
			{
					$video	=& JTable::getInstance('Video','CTable');
					$video->bind( $videoEntry );
					$videos[]   = $video;
			}
			if ($videos)
			{
				shuffle( $videos );

				// Test the number of result so the loop will not fail with incorrect index.
				$total		= count( $videos ) < $total ? count($videos) : $total;
				$videos		= array_slice($videos, 0, $total);
			}

			if( $raw )
			{
				return $videos;
			}

			$tmpl	=   new CTemplate();
			$html	=   $tmpl   ->setRef( 'data', $videos )
					    ->set( 'thumbWidth' , CVideoLibrary::thumbSize('width') )
					    ->set( 'thumbHeight' , CVideoLibrary::thumbSize('height') )
					    ->fetch('frontpage.latestvideos');
		}
		
		$data['HTML'] = $html;
		
		return $data;
	}
	  
	public function showLatestMembers($limit)
	{
		$session = JFactory::getSession();
		$html = ''; //$session->get('frontpage_members');
		if( !$html)
		{
			
			$tmpl	=   new CTemplate();
			$showlatestmembers  = intval( $tmpl->params->get('showlatestmembers') );
			$html = '';
			$data = array();

			if( $showlatestmembers != 0 )
			{
				$model = CFactory::getModel('user');
				$latestMembers = $model->getLatestMember( $limit );
				$totalMembers  = $model->getMembersCount();

				$data = array();

				if( !empty( $latestMembers ) )
				{
					shuffle( $latestMembers );
					$data['members'] = $latestMembers;
					$data['limit'] = ( count( $latestMembers ) > $limit ) ? $limit : count( $latestMembers );
				}

				$tmpl	=   new CTemplate();
				$html	=   $tmpl   ->set('memberList'	    , $this->get('getMembersHTML', $data))
							->set('totalMembers'    , $totalMembers)
							->fetch('frontpage.latestmember');
			}

		}
		
		//$html = $session->set('frontpage_members', $html);
		$data['HTML'] = $html;
		
		return $data;
	}
	
	/**
	 * Show listing of most recent photos.
	 * @param	$rawData	Retrieves the raw data of recent photos	 
	 */
	public function showLatestPhotos( $rawData = false )
	{
		$tmpl		    =	new CTemplate();
		$config		    =	CFactory::getConfig();
		$photoModel	    =	CFactory::getModel('photos');
		$showlatestphotos   =	intval( $tmpl->params->get('showlatestphotos') );
		$frontpagePhotos    = intval( $config->get('frontpagephotos') );
		$latestPhotos	    = $photoModel->getAllPhotos( null , PHOTOS_USER_TYPE, $frontpagePhotos, 0 , COMMUNITY_ORDER_BY_DESC , COMMUNITY_ORDERING_BY_CREATED );
		$data = array();

		if( $showlatestphotos != 0)
		{
			if( $latestPhotos )
			{
				shuffle( $latestPhotos );
				// Make sure it is all photo object
				foreach( $latestPhotos as &$row )
				{
					$photo	=& JTable::getInstance( 'Photo' , 'CTable' );
					$photo->bind($row);
					$row = $photo;
				}
			}

			if( !empty($latestPhotos) )
			{
				for( $i = 0; $i < count( $latestPhotos ); $i++ )
				{
					$row	    =&	$latestPhotos[$i];
					$row->user  =	CFactory::getUser( $row->creator );
				}
			}

			if( $rawData )
			{
				return $latestPhotos;
			}

			$tmpl	=   new CTemplate();
			$data['HTML']	=   $tmpl   ->setRef( 'latestPhotos'	, $latestPhotos )
						    ->fetch('frontpage.latestphoto');
		}
		else
		{
			$data['HTML'] = '';
		}

		

		return $data;
	}

	public function getMembersHTML($data)
	{
		if (empty($data)) return '';
		
		$members	= $data['members'];
		$limit		= $data['limit'];

		$tmpl	=   new CTemplate();
		echo $tmpl  ->set('members' , $members)
			    ->fetch('frontpage.latestmember.list');
	}
	
	public function getOnlineMembers()
	{
		$model 		   = CFactory::getModel('user');
	 	$onlineMembers = $model->getOnlineUsers( 20 , false );
	    
		if( $onlineMembers )
		{
			shuffle( $onlineMembers );
		}
		
		if( !empty( $onlineMembers ) )
		{
			for( $i = 0; $i < count( $onlineMembers ); $i++ )
			{
				$row		=& $onlineMembers[$i];
				$row->user	=  CFactory::getUser( $row->id );
			}
		}
		
		return $onlineMembers;
	}
	
	public function getVideosHTML($rows){
		$tmpl	=   new CTemplate();
		$tmpl	->set('videos'	, $rows)
			->fetch('frontpage.videos.list');
	}
}

