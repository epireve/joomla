<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class CommunityViewEvents extends CommunityView
{

	public function _addSubmenu()
	{
		CFactory::load( 'helpers' , 'event' );
		$id		= JRequest::getVar( 'eventid' , '' , 'REQUEST' );
		$event	=& JTable::getInstance( 'Event' , 'CTable' );
		$event->load( $id );
		
		CEventHelper::getHandler( $event )->addSubmenus( $this );
	}
	
	public function showSubmenu()
	{
		$this->_addSubmenu();
		parent::showSubmenu();
	}

	/**
	 * Application full view
	 **/
	public function appFullView()
	{
		$document = JFactory::getDocument();
		
		$this->showSubmenu();
		
		$applicationName = JString::strtolower( JRequest::getVar( 'app' , '' , 'GET' ) );

		if(empty($applicationName))
		{
			JError::raiseError( 500, 'COM_COMMUNITY_APP_ID_REQUIRED');
		}
		
		if(!$this->accessAllowed('registered'))
		{
			return;
		}
		
		$output	= '';
		
		//@todo: Since group walls doesn't use application yet, we process it manually now.
		if( $applicationName == 'walls' )
		{
			CFactory::load( 'libraries' , 'wall' );
			$jConfig	= JFactory::getConfig();
			$limit		= JRequest::getInt( 'limit' , 5 , 'REQUEST' );
                       	$limitstart = JRequest::getInt( 'limitstart', 0, 'REQUEST' );
			$eventId	= JRequest::getInt( 'eventid' , '' , 'GET' );
			$my			= CFactory::getUser();
			$config		= CFactory::getConfig();

			$eventsModel	= CFactory::getModel( 'Events' );
			$event			=& JTable::getInstance( 'Event' , 'CTable' );
			$event->load( $eventId );
			$config			= CFactory::getConfig();
			$document->setTitle( JText::sprintf('COM_COMMUNITY_EVENTS_WALL_TITLE' , $event->title ) );
			CFactory::load( 'helpers' , 'owner' );

			$guest				= $event->isMember( $my->id );
			$waitingApproval	= $event->isPendingApproval( $my->id );
			$status				= $event->getUserStatus($my->id, 'events');
			$responded			= (($status == COMMUNITY_EVENT_STATUS_ATTEND)
									|| ($status == COMMUNITY_EVENT_STATUS_WONTATTEND)
									|| ($status == COMMUNITY_EVENT_STATUS_MAYBE));

			if( !$config->get('lockeventwalls') || ($config->get('lockeventwalls') && ($guest) && !($waitingApproval) && $responded) || COwnerHelper::isCommunityAdmin() )
			{
				$output	.= CWallLibrary::getWallInputForm( $event->id , 'events,ajaxSaveWall', 'events,ajaxRemoveWall' );

				// Get the walls content
				$output 		.='<div id="wallContent">';
				$output			.= CWallLibrary::getWallContents( 'events' , $event->id , $event->isAdmin( $my->id ) , $limit , $limitstart , 'wall.content' ,'events,events');
				$output 		.= '</div>';
				
				jimport('joomla.html.pagination');
				$wallModel 		= CFactory::getModel('wall');
				$pagination		= new JPagination( $wallModel->getCount( $event->id , 'events' ) , $limitstart , $limit );
	
				$output		.= '<div class="pagination-container">' . $pagination->getPagesLinks() . '</div>';
			}
		}
		else
		{
			CFactory::load( 'libraries' , 'apps' );
			$model				= CFactory::getModel('apps');
			$applications		=& CAppPlugins::getInstance();
			$applicationId		= $model->getUserApplicationId( $applicationName );
			
			$application		= $applications->get( $applicationName , $applicationId );

			if( !$application )
			{
				JError::raiseError( 500 , 'COM_COMMUNITY_APPS_NOT_FOUND' );
			}
			
			// Get the parameters
			$manifest			= CPluginHelper::getPluginPath('community',$applicationName) . DS . $applicationName . DS . $applicationName . '.xml';
			
			$params			= new CParameter( $model->getUserAppParams( $applicationId ) , $manifest );
	
			$application->params	=& $params;
			$application->id		= $applicationId;
			
			$output	= $application->onAppDisplay( $params );
		}
		
		echo $output;
	}

	public function display($tpl = null)
	{

		$mainframe	=& JFactory::getApplication();
		$document 	= JFactory::getDocument();
		$config		= CFactory::getConfig();
		$my		= CFactory::getUser();

		$script = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
		$document->addCustomTag( $script );

		$groupId    = JRequest::getVar('groupid','', 'GET');
		if (!empty($groupId))
		{
			$group =& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );
			
			// @rule: Test if the group is unpublished, don't display it at all.
			if( !$group->published )
			{
				echo JText::_('COM_COMMUNITY_GROUPS_UNPUBLISH_WARNING');
				return;
			}
		
			// Set pathway for group videos
			// Community > Groups > Group Name > Events
			$this->addPathway( JText::_('COM_COMMUNITY_GROUPS'), CRoute::_('index.php?option=com_community&view=groups') );
			$this->addPathway( $group->name, CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId));
		}

		//page title
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS') , CRoute::_('index.php?option=com_community&view=events') );

		// Get category id from the query string if there are any.
		$categoryId	= JRequest::getInt( 'categoryid' , 0 );
		$limitstart	= JRequest::getVar( 'limitstart' , 0 );
		$category	=& JTable::getInstance( 'EventCategory' , 'CTable' );
		$category->load( $categoryId );
		
		if( isset( $category ) && $category->id != 0 )
		{
			$document->setTitle( JText::sprintf('COM_COMMUNITY_GROUPS_CATEGORY_NAME' , JText::_( $this->escape($category->name) ) ) );
		}
		else
		{
			$document->setTitle(JText::_('COM_COMMUNITY_EVENTS'));
		} 
		

		$this->showSubmenu();   
		
		$feedLink = CRoute::_('index.php?option=com_community&view=events&format=feed');
		$feed = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_SUBSCRIBE_ALL_EVENTS_FEED') . '" href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );
		
		// loading neccessary files here.
		CFactory::load( 'libraries' , 'filterbar' );
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'helpers' , 'event' );
		CFactory::load( 'models' , 'events');

 		$data		= new stdClass();
		$sorted		= JRequest::getVar( 'sort' , 'startdate' , 'GET' );

		/* begin: UNLIMITED LEVEL BREADCRUMBS PROCESSING */
		if( $category->parent == COMMUNITY_NO_PARENT )
		{
			$this->addPathway( JText::_( $this->escape( $category->name ) ), CRoute::_('index.php?option=com_community&view=events&task=display&categoryid=' . $category->id ) );
		}
		else
		{
			// Parent Category
			$parentsInArray	=   array();
			$n		=   0;
			$parentId	=   $category->id;

			$parent	=&  JTable::getInstance( 'EventCategory' , 'CTable' );

			do
			{
				$parent->load( $parentId );
				$parentId	=   $parent->parent;

				$parentsInArray[$n]['id']	=   $parent->id;
				$parentsInArray[$n]['parent']	=   $parent->parent;
				$parentsInArray[$n]['name']	=   $parent->name;
				
				$n++;				
			}
			while ( $parent->parent > COMMUNITY_NO_PARENT );

			for( $i=count($parentsInArray)-1; $i>=0; $i-- )
			{
				$this->addPathway( $parentsInArray[$i]['name'], CRoute::_('index.php?option=com_community&view=events&task=display&categoryid=' . $parentsInArray[$i]['id'] ) );
			}
		}
		/* end: UNLIMITED LEVEL BREADCRUMBS PROCESSING */

		$data->categories   =	$this->_cachedCall('_getEventsCategories', array( $category->id ), '', array( COMMUNITY_CACHE_TAG_EVENTS_CAT ) );

		$model		= CFactory::getModel( 'events' );
		CFactory::load( 'helpers' , 'event' );
		$event		=&JTable::getInstance( 'Event' , 'CTable' );
		$handler	= CEventHelper::getHandler( $event );

		 // It is safe to pass 0 as the category id as the model itself checks for this value.
		$data->events      = $model->getEvents( $category->id , null, $sorted , null , true , false , null , null , $handler->getContentTypes() , $handler->getContentId() );

		// Get pagination object
		$data->pagination	= $model->getPagination();

		$eventsHTML	=	$this->_cachedCall('_getEventsHTML', array( $data->events, false, $data->pagination ), '', array( COMMUNITY_CACHE_TAG_EVENTS ) );
		//Cache Group Featured List
		$featuredList	=	$this->_cachedCall('_getEventsFeaturedList', array(), '', array( COMMUNITY_CACHE_TAG_FEATURED ) );

		$sortItems =  array(
				'latest' 	=> JText::_('COM_COMMUNITY_EVENTS_SORT_CREATED'),
				'startdate'	=> JText::_('COM_COMMUNITY_EVENTS_SORT_COMING'));
		
		CFactory::load( 'helpers' , 'owner' );
		
		$tmpl	=   new CTemplate();		
		echo $tmpl  ->set( 'handler'		, $handler )
			    ->set( 'featuredList'	, $featuredList )
			    ->set( 'index'		, true )
			    ->set( 'categories'		, $data->categories )
			    ->set( 'eventsHTML'		, $eventsHTML )
			    ->set( 'config'		, $config )
			    ->set( 'category'		, $category )
			    ->set( 'isCommunityAdmin'	, COwnerHelper::isCommunityAdmin() )
			    ->set( 'sortings'		, CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'startdate') )
			    ->set( 'my' 		, $my )
			    ->fetch( 'events.index' );
		
	}

	/**
	 * Display invite form
	 **/
	public function invitefriends()
	{
		$document	= JFactory::getDocument();
		$document->setTitle( JText::_('COM_COMMUNITY_EVENTS_INVITE_FRIENDS_TO_EVENT_TITLE') );

		if( !$this->accessAllowed( 'registered' ) )
		{
			return;
		}

		$this->showSubmenu();

		$my				= CFactory::getUser();
		$eventId		= JRequest::getVar( 'eventid' , '' , 'GET' );
		$this->_addEventInPathway( $eventId );
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS_INVITE_FRIENDS_TO_EVENT_TITLE') );

		$friendsModel	= CFactory::getModel( 'Friends' );
		$model	        = CFactory::getModel( 'Events' );
		$event          =& JTable::getInstance('Event' , 'CTable');
		$event->load($eventId);

		$tmpFriends		= $friendsModel->getFriends( $my->id , 'name' , false);

		$friends		= array();

		for( $i = 0; $i < count( $tmpFriends ); $i++ )
		{
			$friend			=& $tmpFriends[ $i ];
			$eventMember	=& JTable::getInstance( 'EventMembers' , 'CTable' );
			$eventMember->load( $eventId , $friend->id );


			if( !$event->isMember( $friend->id ) && !$eventMember->exists())
			{
				$friends[]	= $friend;
			}
		}
		unset( $tmpFriends );

		$tmpl   = new CTemplate();
		echo $tmpl  ->set( 'friends'	, $friends )
			    ->set( 'event'	, $event )
			    ->fetch( 'events.invitefriends' );
	}
	
	public function pastevents()
	{
		$mainframe	=&	JFactory::getApplication();
		$document 	=&	JFactory::getDocument();
		$config		=&	CFactory::getConfig();
		$my		=	CFactory::getUser();

		$groupId    = JRequest::getVar('groupid','', 'GET');
		if (!empty($groupId))
		{
			$group =& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );

			// Set pathway for group videos
			// Community > Groups > Group Name > Events
			$this->addPathway( JText::_('COM_COMMUNITY_GROUPS'), CRoute::_('index.php?option=com_community&view=groups') );
			$this->addPathway( $group->name, CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId));
		}
		else
		{
			$this->addPathway( JText::_('COM_COMMUNITY_EVENTS') , CRoute::_('index.php?option=com_community&view=events') );
			$this->addPathway( JText::_('COM_COMMUNITY_EVENTS_PAST_TITLE') , '' );
		}

		$document->setTitle(JText::_('COM_COMMUNITY_EVENTS_PAST_TITLE'));

		$this->showSubmenu();

		$feedLink = CRoute::_('index.php?option=com_community&view=events&task=pastevents&format=feed');
		$feed = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_SUBSCRIBE_EXPIRED_EVENTS_FEED') . '"  href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );

		// loading neccessary files here.
		CFactory::load( 'libraries' , 'filterbar' );
		CFactory::load( 'helpers' , 'event' );
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'models' , 'events');
		//$event		= JTable::getInstance( 'Event' , 'CTable' );

 		$data		= new stdClass();
		$sorted		= JRequest::getVar( 'sort' , 'latest' , 'GET' );
		$model		= CFactory::getModel( 'events' );

		CFactory::load( 'helpers' , 'event' );
		$event		=&JTable::getInstance( 'Event' , 'CTable' );
		$handler	= CEventHelper::getHandler( $event );

 		// It is safe to pass 0 as the category id as the model itself checks for this value.
 		$data->events	= $model->getEvents( null, null , $sorted, null, false, true, null , null , $handler->getContentTypes() , $handler->getContentId() );

		// Get pagination object
		$data->pagination	= $model->getPagination();

		// Get the template for the group lists
		$eventsHTML  =	$this->_cachedCall('_getEventsHTML', array( $data->events, true, $data->pagination ), '', array( COMMUNITY_CACHE_TAG_EVENTS ) );

		$sortItems =  array(
				    'latest' 	=> JText::_('COM_COMMUNITY_EVENTS_SORT_CREATED') ,
				    'startdate'	=> JText::_('COM_COMMUNITY_EVENTS_SORT_START_DATE'));

		$tmpl	=   new CTemplate();
		echo $tmpl  ->set( 'eventsHTML'		, $eventsHTML )
			    ->set( 'config'		, $config )
			    ->set( 'isCommunityAdmin'	, COwnerHelper::isCommunityAdmin() )
			    ->set( 'sortings'		, CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'startdate') )
			    ->set( 'my' 		, $my )
			    ->fetch( 'events.pastevents' );
	}
	
	/*
	 * @since 2.4
	 * To retrieve nearby events
	 */
	public function modEventNearby(){
		return $this->_getNearbyEvent();
	}
	
	/*
	 * @since 2.4
	 */
	public function _getNearbyEvent(){
		$tmpl	=   new CTemplate();
		echo $tmpl -> fetch( 'events.nearbysearch' );
	}
	
	/*
	 * @since 2.4
	 * To retrieve events on calendar
	 */
	public function modEventCalendar(){
		return $this->_getEventCalendar();
	}
	
	/*
	 * @since 2.4
	 */
	public function _getEventCalendar(){
		$tmpl	=   new CTemplate();
		echo $tmpl -> fetch( 'events.eventcalendar' );
	}
	
	/*
	 * @since 2.4
	 * To retrieve event pending list
	 */
	public function modEventPendingList(){
		$my	= CFactory::getUser();
		return $this->_getPendingListHTML($my);
	}

	public function myevents()
	{
		if(!$this->accessAllowed('registered'))
		{
			return;
		}

		$mainframe	=&	JFactory::getApplication();
		$document 	=&	JFactory::getDocument();
		$config		=&	CFactory::getConfig();
		$my		=	CFactory::getUser();
		$userid		=	JRequest::getCmd('userid', null );

		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS') , CRoute::_('index.php?option=com_community&view=events') );
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS_MINE') , '' );

		$document->setTitle(JText::_('COM_COMMUNITY_EVENTS_MINE'));
		
		$this->showSubmenu(); 
		
		$feedLink = CRoute::_('index.php?option=com_community&view=events&userid=' . $userid . '&format=feed');
		$feed = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_SUBSCRIBE_MY_EVENTS_FEED') . '" href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );
		
		// loading neccessary files here.
		CFactory::load( 'libraries' , 'filterbar' );
		CFactory::load( 'helpers' , 'event' );
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'models' , 'events');
		//$event		= JTable::getInstance( 'Event' , 'CTable' );

 		$data		= new stdClass();
		$sorted		= JRequest::getVar( 'sort' , 'startdate' , 'GET' );
		$model		= CFactory::getModel( 'events' );

 		// It is safe to pass 0 as the category id as the model itself checks for this value.
 		$data->events		= $model->getEvents( null, $my->id , $sorted );

		// Get pagination object
		$data->pagination	= $model->getPagination();

		// Get the template for the group lists
		$eventsHTML  =	$this->_cachedCall('_getEventsHTML', array( $data->events, false, $data->pagination ), '', array( COMMUNITY_CACHE_TAG_EVENTS ) );
		
		$tmpl		= new CTemplate();

		$sortItems =  array(
				'latest'	=>  JText::_('COM_COMMUNITY_EVENTS_SORT_CREATED'),
				'startdate'	=>  JText::_('COM_COMMUNITY_EVENTS_SORT_COMING'));

		echo $tmpl  ->set( 'eventsHTML'		, $eventsHTML )
			    ->set( 'config'		, $config )
			    ->set( 'isCommunityAdmin'	, COwnerHelper::isCommunityAdmin() )
			    ->set( 'sortings'		, CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'startdate') )
			    ->set( 'my' 		, $my )
			    ->fetch( 'events.myevents' );
	}

	public function myinvites()
	{
		if(!$this->accessAllowed('registered'))
		{
			return;
		}

		$mainframe	=&	JFactory::getApplication();
		$document 	=&	JFactory::getDocument();
		$config		=&	CFactory::getConfig();
		$my			=	CFactory::getUser();
		$userid     =	JRequest::getCmd('userid', null );

		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS') , CRoute::_('index.php?option=com_community&view=events') );
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS_PENDING_INVITATIONS') , '' );

		$document->setTitle(JText::_('COM_COMMUNITY_EVENTS_PENDING_INVITATIONS'));

		$this->showSubmenu();

		$feedLink = CRoute::_('index.php?option=com_community&view=events&userid=' . $userid . '&format=feed');
		$feed = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_SUBSCRIBE_TO_PENDING_INVITATIONS_FEED') . '"  href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );


		CFactory::load( 'libraries' , 'filterbar' );
		CFactory::load( 'helpers' , 'event' );
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'models' , 'events');

		$sorted		= JRequest::getVar( 'sort' , 'startdate' , 'GET' );
		$model		= CFactory::getModel( 'events' );
		$pending	= COMMUNITY_EVENT_STATUS_INVITED;

		// It is safe to pass 0 as the category id as the model itself checks for this value.
 		$rows		= $model->getEvents( null, $my->id , $sorted, null, true, false, $pending );
		$pagination	= $model->getPagination();
		$count		= count( $rows );
		$sortItems	= array( 'latest'	=> JText::_('COM_COMMUNITY_EVENTS_SORT_CREATED') ,
					 'startdate'	=> JText::_('COM_COMMUNITY_EVENTS_SORT_COMING'));
		
		$events		= array();
		
		if( $rows )
		{
			foreach( $rows as $row )
			{
				$event	=& JTable::getInstance( 'Event' , 'CTable' );
				$event->bind( $row );
				$events[]	= $event;
			}
			unset($eventObjs);
		}
		
		$tmpl	= new CTemplate();
		echo $tmpl  ->set( 'events'		, $events )
			    ->set( 'pagination' 	, $pagination )
			    ->set( 'config'		, $config )
			    ->set( 'isCommunityAdmin'	, COwnerHelper::isCommunityAdmin() )
			    ->set( 'sortings'		, CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'startdate') )
			    ->set( 'my' 		, $my )
			    ->set( 'count' 		, $count )
			    ->fetch( 'events.myinvites' );
	}

	/**
	 * Method to display the create / edit event's form.
	 * Both views share the same template file.	 
	 **/	 		
	public function _displayForm($event)
	{
		$mainframe	= JFactory::getApplication();
		$my		= CFactory::getUser();
		$config		= CFactory::getConfig();
		$model		= CFactory::getModel( 'events' );
		$categories	= $model->getCategories();		
		$now 		= JFactory::getDate();

		//J1.6 returns timezone as string, not integer offset.
		if(method_exists('JDate','getOffsetFromGMT')){
			$systemOffset = new CDate('now',$mainframe->getCfg('offset'));
			$systemOffset = $systemOffset->getOffsetFromGMT(true);
		} else {
			$systemOffset = $mainframe->getCfg('offset');
		}
		$now->setOffset($systemOffset);

		$editorType	= ($config->get('allowhtml') )? $config->get('htmleditor' , 'none') : 'none' ;

		CFactory::load( 'libraries' , 'editor' );
		$editor	    =	new CEditor( $editorType );		
		$totalEventCount	= $model->getEventsCreationCount( $my->id );
                
		if($event->catid == NULL)
                    $event->catid		= JRequest::getInt( 'categoryid', 0, 'GET');
		
                $event->startdatetime	= JRequest::getVar('startdatetime', '00:01', 'POST');
		$event->enddatetime	= JRequest::getVar('enddatetime', '23:59', 'POST');

		CFactory::load( 'helpers' , 'time' );
		$timezones	= CTimeHelper::getTimezoneList();
		
		CFactory::load( 'helpers' , 'event' );
		$helper	= CEventHelper::getHandler( $event );

		$startDate	= $event->getStartDate( false );
		$endDate	= $event->getEndDate( false );
		
		$allday	=   false;
		
		if(($startDate->toFormat( '%Y-%m-%d' ) == $endDate->toFormat( '%Y-%m-%d' )) && $startDate->toFormat( '%H:%M:%S' )=='00:00:00' && $endDate->toFormat( '%H:%M:%S' )=='23:59:59'){
			$allday =   true;
                }

		$dateSelection = CEventHelper::getDateSelection($startDate, $endDate);

		// Load category tree
		CFactory::load('helpers','category');
		$cTree	= CCategoryHelper::getCategories($categories);

		$lists['categoryid']	=   CCategoryHelper::getSelectList( 'events', $cTree, $event->catid );

		CFactory::load( 'libraries' , 'apps' );
		$app		    =& CAppPlugins::getInstance();
		$appFields	    = $app->triggerEvent('onFormDisplay' , array('createEvent'));
		$beforeFormDisplay  = CFormElement::renderElements( $appFields , 'before' );
		$afterFormDisplay   = CFormElement::renderElements( $appFields , 'after' );
		
		$tmpl	= new CTemplate();
		echo $tmpl  ->set( 'startDate'		, $startDate )
			    ->set( 'endDate'		, $endDate )
			    ->set( 'startHourSelect'	, $dateSelection->startHour )
			    ->set( 'endHourSelect'	, $dateSelection->endHour )
			    ->set( 'startMinSelect'	, $dateSelection->startMin )
			    ->set( 'endMinSelect'	, $dateSelection->endMin )
			    ->set( 'startAmPmSelect'	, $dateSelection->startAmPm )
			    ->set( 'endAmPmSelect'	, $dateSelection->endAmPm )
			    ->set( 'timezones'		, $timezones )
			    ->set( 'config'		, $config )
			    ->set( 'systemOffset'	, $systemOffset)
			    ->set( 'lists'		, $lists )
			    ->set( 'categories'		, $categories )
			    ->set( 'event'		, $event )
			    ->set( 'editor'		, $editor )
			    ->set( 'helper'		, $helper )
			    ->set( 'now'		, $now->toFormat('%Y-%m-%d') )
			    ->set( 'eventCreated'	, $totalEventCount )
			    ->set( 'eventcreatelimit'	, $config->get('eventcreatelimit') )
			    ->set( 'allday'		, $allday )
			    ->set( 'beforeFormDisplay', $beforeFormDisplay )
			    ->set( 'afterFormDisplay' , $afterFormDisplay )
			    ->fetch( 'events.forms' );
	}
	
	/**
	 * Display the form of the event import and the listing of events users can import
	 * from the calendar file.	 
	 **/	 	
	public function import( $events )
	{
		if(!$this->accessAllowed('registered'))
		{
			return;
		}
	
		$document 	= JFactory::getDocument();
		$config		= CFactory::getConfig();
		$document->setTitle( JText::_('COM_COMMUNITY_EVENTS_IMPORT_ICAL') );
		
		$this->showSubmenu();
		$model		= CFactory::getModel( 'events' );
		$categories	= $model->getCategories();
		
		CFactory::load( 'helpers' , 'time' );
		$timezones	= CTimeHelper::getTimezoneList();

		$tmpl	= new CTemplate();
		echo $tmpl  ->set( 'events'	, $events )
			    ->set( 'categories' , $categories )
			    ->set( 'timezones'	, $timezones )
			    ->fetch( 'events.import' );
	}
	
	/**
	 * Displays the create event form
	 **/	 	
	public function create($event)
	{
		if(!$this->accessAllowed('registered'))
		{
			return;
		}

		$document 	= JFactory::getDocument();
		$config		= CFactory::getConfig();
		$mainframe	= JFactory::getApplication();
		CFactory::load( 'helpers' , 'owner' );		
		CFactory::load( 'helpers' , 'event' );
		$handler	= CEventHelper::getHandler( $event );

		if( !$handler->creatable() )
		{
			$document->setTitle( '' );
			$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_EVENTS_DISABLE_CREATE'), 'error');
			return;
		}		
		
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS') , CRoute::_('index.php?option=com_community&view=events') );
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS_CREATE_TITLE') , '' );
		$document->setTitle(JText::_('COM_COMMUNITY_EVENTS_CREATE_TITLE'));
		
		$js	= 'assets/validate-1.5'.(( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js');
		CAssets::attach($js, 'js');
		
		$this->showSubmenu();
		$this->_displayForm($event);
		return;		
	}
	
	public function edit($event)
	{		
		if(!$this->accessAllowed('registered'))
			return;
		$document 	= JFactory::getDocument();
		$config		= CFactory::getConfig();
		$document->setTitle(JText::_('COM_COMMUNITY_EVENTS_EDIT_TITLE'));

		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS') , CRoute::_('index.php?option=com_community&view=events') );
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS_EDIT_TITLE') , '' );
        
		$file	= 'assets/validate-1.5';
		$file	.= $config->getBool('usepackedjavascript') ? '.pack.js' : '.js';

		CAssets::attach( $file , 'js' );

		
		if(!$this->accessAllowed('registered') )
		{
			echo JText::_( 'COM_COMMUNITY_ACCESS_FORBIDDEN' );
			return;
		}

		$this->showSubmenu();
		$this->_displayForm($event);
		return;
	}
	
	public function printpopup($event)
	{
		$config = CFactory::getConfig();
		$my 	= CFactory::getUser();
		// We need to attach the javascirpt manually
		
		$js = JURI::root().'components/com_community/assets/joms.jquery';
		$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
		$script  = '<script type="text/javascript" src="'.$js.'"></script>';
		
		$js	= JURI::root().'components/com_community/assets/script-1.2';
		$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
		
		$script .= '<script type="text/javascript" src="'.$js.'"></script>'; 
		
		$creator = CFactory::getUser($event->creator);
		$creatorUtcOffset = $creator->getUtcOffset();
		$creatorUtcOffsetStr = CTimeHelper::getTimezone( $event->offset );
		
		// Get the formated date & time
		$format	=   ($config->get('eventshowampm')) ?  JText::_('COM_COMMUNITY_DATE_FORMAT_LC2_12H') : JText::_('COM_COMMUNITY_DATE_FORMAT_LC2_24H');
		$event->startdateHTML   = CTimeHelper::getFormattedTime($event->startdate, $format);
		$event->enddateHTML     = CTimeHelper::getFormattedTime($event->enddate, $format);

		// Output to template
		$tmpl	=   new CTemplate();
		echo $tmpl  ->set( 'my'			    , $my)
			    ->set( 'event'		    , $event )
			    ->set( 'script'		    , $script)
			    ->set( 'creatorUtcOffsetStr'    , $creatorUtcOffsetStr )
			    ->fetch( 'events.print' );
	}
	
	/**
	 * Responsible for displaying the event page.
	 **/	 	
	public function viewevent()
	{
		$mainframe	= JFactory::getApplication();
		$document 	= JFactory::getDocument();
		$config		= CFactory::getConfig();
		$my		= CFactory::getUser();
		
		CFactory::load( 'libraries' , 'tooltip' );
		CFactory::load( 'libraries' , 'wall' );
		CFactory::load( 'libraries' , 'window' );
		CFactory::load( 'libraries' , 'activities' );
		CFactory::load( 'libraries' , 'events' );
		CWindow::load();
		
		$eventLib	= new CEvents();
		$eventid	=	JRequest::getInt( 'eventid' , 0 );
		$eventModel	=&	CFactory::getModel( 'events' );		
		$event		=&	JTable::getInstance( 'Event' , 'CTable' );

                CFactory::load( 'helpers' , 'event' );
		$handler	= CEventHelper::getHandler( $event );
		$event->load($eventid);
		
		if( !$handler->exists() )
		{
			$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_EVENTS_NOT_AVAILABLE_ERROR'), 'error');
			return;
		}
		
		if( !$handler->browsable() )
		{
			echo JText::_('COM_COMMUNITY_NOT_ALLOWED_TO_ACCESS_SECTION' );
			return;
		}
		
		// @rule: Test if the group is unpublished, don't display it at all.
		if( !$event->published )
		{
			echo JText::_('COM_COMMUNITY_EVENTS_UNDER_MODERATION' );
			return;
		}
		$this->showSubmenu();
		$event->hit();

		// Basic page presentation
		if ($event->type=='group')
		{
			$groupId = $event->contentid;
			$group =& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );
		
			// Set pathway for group videos
			// Community > Groups > Group Name > Events
			$this->addPathway( JText::_('COM_COMMUNITY_GROUPS'), CRoute::_('index.php?option=com_community&view=groups') );
			$this->addPathway( $group->name, CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId));
		}

		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS'), CRoute::_('index.php?option=com_community&view=events') );
		$this->addPathway( $event->title );
		
		// Permissions and privacies
		CFactory::load('helpers' , 'owner');
		$isEventGuest	= $event->isMember( $my->id );
		$isMine			= ($my->id == $event->creator);
		$isAdmin		= $event->isAdmin( $my->id );
		$isCommunityAdmin	= COwnerHelper::isCommunityAdmin();

		// Get Event Admins
		$eventAdmins		= $event->getAdmins( 12 , CC_RANDOMIZE );		
		$adminsInArray = array();

		// Attach avatar of the admin
		for( $i = 0; ($i < count($eventAdmins)); $i++)
		{
			$row	=&  $eventAdmins[$i];
			$admin	=   CFactory::getUser( $row->id );
			array_push( $adminsInArray, '<a href="' . CUrlHelper::userLink($admin->id) . '">' . $admin->getDisplayName() . '</a>' );
		}
		
		$adminsList	=   ltrim( implode( ', ', $adminsInArray ), ',' );
		
		// Get Attending Event Guests
		$eventMembers			= $event->getMembers( COMMUNITY_EVENT_STATUS_ATTEND, 12 , CC_RANDOMIZE );
		$eventMembersCount		= $event->getMembersCount( COMMUNITY_EVENT_STATUS_ATTEND );

		// Attach avatar of the admin
		// Pre-load multiple users at once
		$userids = array();
		foreach($eventMembers as $uid)
		{
			$userids[] = $uid->id;
		}
		CFactory::loadUsers($userids);

		for( $i = 0; ($i < count($eventMembers)); $i++)
		{
			$row	=& $eventMembers[$i];
			$eventMembers[$i]	= CFactory::getUser( $row->id );
		}


		// Pre-load multiple users at once

		$waitingApproval	    = $event->isPendingApproval( $my->id );
		$waitingRespond	        = false;

		$myStatus = $event->getUserStatus($my->id);
		
		$hasResponded = (($myStatus == COMMUNITY_EVENT_STATUS_ATTEND)
						|| ($myStatus == COMMUNITY_EVENT_STATUS_WONTATTEND)
						|| ($myStatus == COMMUNITY_EVENT_STATUS_MAYBE));
			
		// Get Bookmark HTML
		CFactory::load('libraries' , 'bookmarks');
		$bookmarks	= new CBookmarks(CRoute::getExternalURL( 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id ));
		$bookmarksHTML	= $bookmarks->getHTML();
		
		// Get Reporting HTML
		CFactory::load('libraries', 'reporting');
		$report		= new CReportingLibrary();
		$reportHTML	= $report->getReportingHTML( JText::_('COM_COMMUNITY_EVENTS_REPORT') , 'events,reportEvent' , array( $event->id ) );
		
		// Get the Wall
		CFactory::load( 'libraries' , 'wall' );
		$wallContent	= CWallLibrary::getWallContents( 'events' , $event->id , $isAdmin , 10 ,0 , 'wall.content' , 'events,events');
		$wallCount		= CWallLibrary::getWallCount('events', $event->id);
		$viewAllLink	= false;

		if(JRequest::getVar('task', '', 'REQUEST') != 'app')
		{
			$viewAllLink	= CRoute::_('index.php?option=com_community&view=events&task=app&eventid=' . $event->id . '&app=walls');
		}
		$wallContent	.= CWallLibrary::getViewAllLinkHTML($viewAllLink, $wallCount);
		
		$wallForm		= '';
		/*if( !$config->get('lockeventwalls')
			|| ($config->get('lockeventwalls') && ($isEventGuest) && !($waitingApproval) && $hasResponded) 
			|| $isCommunityAdmin )
		{
			$wallForm	= CWallLibrary::getWallInputForm( $event->id , 'events,ajaxSaveWall', 'events,ajaxRemoveWall' );
		}*/
		
		// Construct the RVSP radio list
		$arr = array(
			JHTML::_('select.option',  COMMUNITY_EVENT_STATUS_ATTEND, JText::_( 'COM_COMMUNITY_EVENTS_YES' ) ),
			JHTML::_('select.option',  COMMUNITY_EVENT_STATUS_WONTATTEND, JText::_( 'COM_COMMUNITY_EVENTS_NO' ) ),
			JHTML::_('select.option',  COMMUNITY_EVENT_STATUS_MAYBE, JText::_( 'COM_COMMUNITY_EVENTS_MAYBE' ) )
		);
		$status		= $event->getMemberStatus($my->id);
		$radioList	= JHTML::_('select.radiolist',  $arr, 'status', '', 'value', 'text', $status, false );
		
		$unapprovedCount = $event->inviteRequestCount();
		//...
		$editEvent		= JRequest::getVar( 'edit' , false , 'GET' );
		$editEvent		= ( $editEvent == 1 ) ? true : false;

		// Am I invited in this event?
		$isInvited  	= false;
		$join	    	= '';
		$friendsCount	= 0;
		$isInvited  	= $eventModel->isInvitedMe(0, $my->id, $event->id);

		// If I was invited, I want to know my invitation informations
		if( $isInvited )
		{
		     $invitor	=   CFactory::getUser( $isInvited[0]->invited_by );
		     $join	=   '<a href="' . CUrlHelper::userLink( $invitor->id ) . '">' . $invitor->getDisplayName() . '</a>';

		     // Get users friends in this group
		     $friendsCount  =	$eventModel->getFriendsCount( $my->id, $event->id );
		}

		// Get like
		CFactory::load( 'libraries' , 'like' );
		$likes	    =	new CLike();
		$likesHTML  =	$likes->getHTML( 'events', $event->id, $my->id );

		// Is this event is a past event?
		$now		=   new JDate();
		$isPastEvent	=   ( $event->getEndDate( false )->toMySQL() < $now->toMySQL(true) ) ? true : false;
		
		// Get the formated date & time
		$format	=   ($config->get('eventshowampm')) ?  JText::_('COM_COMMUNITY_EVENTS_TIME_FORMAT_12HR') : JText::_('COM_COMMUNITY_EVENTS_TIME_FORMAT_24HR');

                $startDate	= $event->getStartDate( false );
		$endDate	= $event->getEndDate( false );
                $allday = false;

                if(($startDate->toFormat( '%Y-%m-%d' ) == $endDate->toFormat( '%Y-%m-%d' )) && $startDate->toFormat( '%H:%M:%S' )=='00:00:00' && $endDate->toFormat( '%H:%M:%S' )=='23:59:59'){
                    $format = JText::_('COM_COMMUNITY_EVENT_TIME_FORMAT_LC1');
                    $allday =   true;
                }
                
		$event->startdateHTML   = CTimeHelper::getFormattedTime($event->startdate, $format);
		$event->enddateHTML     = CTimeHelper::getFormattedTime($event->enddate, $format);

		CFactory::load( 'libraries' , 'invitation' );
		$inviteHTML =	CInvitation::getHTML( null , 'events,inviteUsers' , $event->id , CInvitation::SHOW_FRIENDS , CInvitation::SHOW_EMAIL );

		CFactory::load( 'libraries', 'userstatus' );
		$status = new CUserStatus($event->id, 'events');

		$tmpl	=   new CTemplate();
		$creator        =  new CUserStatusCreator('message');
		$creator->title =  ($isMine) ? JText::_('COM_COMMUNITY_STATUS') : JText::_('COM_COMMUNITY_MESSAGE');
		$creator->html  =  $tmpl->fetch('status.message');
		$status->addCreator($creator);
		
		// Upgrade wall to stream @since 2.5
		$event->upgradeWallToStream();
				
		// Add custom stream
		$streamHTML = $eventLib->getStreamHTML($event);

                if($event->getMemberStatus($my->id) == COMMUNITY_EVENT_STATUS_ATTEND)
                        $RSVPmessage = JText::_('COM_COMMUNITY_EVENTS_ATTENDING_EVENT_MESSAGE');
                else if($event->getMemberStatus($my->id) == COMMUNITY_EVENT_STATUS_WONTATTEND)
                        $RSVPmessage = JText::_('COM_COMMUNITY_EVENTS_NOT_ATTENDING_EVENT_MESSAGE');
                else
			$RSVPmessage = JText::_('COM_COMMUNITY_EVENTS_NOT_RESPOND_RSVP_MESSAGE');

		// Output to template
		echo	$tmpl	->setMetaTags('event'		, $event )
				->set( 'status'			, $status )
				->set( 'streamHTML'		, $streamHTML )
				->set( 'timezone'		, CTimeHelper::getTimezone( $event->offset ) )
				->set( 'handler'		, $handler )
				->set( 'likesHTML'		, $likesHTML )
				->set( 'inviteHTML'		, $inviteHTML )
				->set( 'guestStatus'		, $event->getUserStatus($my->id) )
				->set( 'event'			, $event )
				->set( 'radioList'		, $radioList )
				->set( 'bookmarksHTML'		, $bookmarksHTML )
				->set( 'reportHTML'		, $reportHTML )
				->set( 'isEventGuest'		, $isEventGuest )
				->set( 'isMine'			, $isMine )
				->set( 'isAdmin'		, $isAdmin )
				->set( 'isCommunityAdmin'	, $isCommunityAdmin )
				->set( 'unapproved'		, $unapprovedCount )
				->set( 'waitingApproval'	, $waitingApproval )
				->set( 'wallContent'		, $wallContent )
				->set( 'eventMembers'		, $eventMembers )
				->set( 'eventMembersCount'	, $eventMembersCount )
				->set( 'editEvent'		, $editEvent )
				->set( 'my'			, $my )
				->set( 'memberStatus'		, $myStatus )
				->set( 'waitingRespond'		, $waitingRespond )
				->set( 'isInvited'		, $isInvited )
				->set( 'join'			, $join )
				->set( 'friendsCount'		, $friendsCount )
				->set( 'isPastEvent'		, $isPastEvent )
				->set( 'adminsList'		, $adminsList )
                                ->set( 'RSVPmessage'            , $RSVPmessage )
                                ->set( 'allday'                 , $allday)
				->fetch( 'events.viewevent' );
	}
	
	/**
	 * Responsible to output the html codes for the task viewguest.
	 * Outputs html codes for the viewguest page.
	 * 	 
	 * @return	none.	 
	 **/	 	 	
	public function viewguest()
	{
		if(!$this->accessAllowed('registered'))
		{
			return;
		}
		
		$mainframe	= JFactory::getApplication();
		$document 	= JFactory::getDocument();
		$config		= CFactory::getConfig();
		$my		= CFactory::getUser();
		$id		= JRequest::getInt( 'eventid' , 0 );
		$type		= JRequest::getCmd('type');
		$approval	= JRequest::getCmd('approve');
		$event		= JTable::getInstance( 'Event' , 'CTable' );
		$event->load( $id );
		
		CFactory::load( 'helpers' , 'event' );
		$handler	= CEventHelper::getHandler( $event );
		$types		= array( COMMUNITY_EVENT_ADMINISTRATOR , COMMUNITY_EVENT_STATUS_INVITED , COMMUNITY_EVENT_STATUS_ATTEND , COMMUNITY_EVENT_STATUS_BLOCKED , COMMUNITY_EVENT_STATUS_REQUESTINVITE );

		if( !in_array( $type , $types ) )
		{
			JError::raiseError( '500' , JText::_( 'Invalid status type' ) );
		}
		
		// Set the guest type for the title purpose
		switch ( $type )
		{
			case COMMUNITY_EVENT_ADMINISTRATOR:
				$guestType = JText::_('COM_COMMUNITY_ADMINS');
			break;
			case COMMUNITY_EVENT_STATUS_INVITED:
				$guestType = JText::_('COM_COMMUNITY_EVENTS_PENDING_MEMBER');
			break;
			case COMMUNITY_EVENT_STATUS_ATTEND:
				$guestType = JText::_('COM_COMMUNITY_EVENTS_CONFIRMED_GUESTS');
			break;
			case COMMUNITY_EVENT_STATUS_BLOCKED:
				$guestType = JText::_('COM_COMMUNITY_EVENTS_BLOCKED');
			break;
			case COMMUNITY_EVENT_STATUS_REQUESTINVITE:
				$guestType = JText::_('COM_COMMUNITY_REQUESTED_INVITATION');
			break;
		}
		
		// Then we load basic page presentation
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS') , CRoute::_('index.php?option=com_community&view=events') );
		$this->addPathway( JText::sprintf('COM_COMMUNITY_EVENTS_TITLE_LABEL', $event->title) , '' );

		// Set the specific title
		$document->setTitle(JText::sprintf('COM_COMMUNTIY_EVENTS_GUESTLIST' , $event->title, $guestType ));
        

		CFactory::load( 'helpers' , 'owner' );
		$status			= $event->getUserStatus($my->id);
		$allowed		= array( COMMUNITY_EVENT_STATUS_INVITED , COMMUNITY_EVENT_STATUS_ATTEND , COMMUNITY_EVENT_STATUS_WONTATTEND , COMMUNITY_EVENT_STATUS_MAYBE );
		$accessAllowed	= ( ( in_array( $status , $allowed ) ) && $status != COMMUNITY_EVENT_STATUS_BLOCKED ) ? true : false;

		if( $handler->hasInvitation() && ( ( $accessAllowed && $event->allowinvite ) || $event->isAdmin( $my->id ) || COwnerHelper::isCommunityAdmin() ) )
		{
			$this->addSubmenuItem('index.php?option=com_community&view=events&task=invitefriends&eventid=' . $event->id, JText::_('COM_COMMUNITY_TAB_INVITE') , '' , SUBMENU_RIGHT );        
		}
		$this->showSubmenu();

		$isSuperAdmin	= COwnerHelper::isCommunityAdmin();    
        
		// status = unsure | noreply | accepted | declined | blocked
		// permission = admin | guest |

		if( $type == COMMUNITY_EVENT_ADMINISTRATOR)
		{
			$guestsIds		= $event->getAdmins( 0 );
			}
			else
			{
				$guestsIds		= $event->getMembers( $type , 0 , false, $approval);
			}

		$guests         = array();

			// Pre-load multiple users at once
			$userids = array();
			foreach($guestsIds as $uid){ $userids[] = $uid->id; }
			CFactory::loadUsers($userids);

		for ($i=0; $i < count($guestsIds); $i++)
		{
				$guests[$i]	= CFactory::getUser($guestsIds[$i]->id);
				$guests[$i]->friendsCount	= $guests[$i]->getFriendCount();
				$guests[$i]->isMe			= ( $my->id == $guests[$i]->id ) ? true : false;
				$guests[$i]->isAdmin		= $event->isAdmin($guests[$i]->id);
				$guests[$i]->statusType		= $guestsIds[$i]->statusCode;
			}

			$pagination		= $event->getPagination();

		// Output to template
		$tmpl	=   new CTemplate();
		echo $tmpl  ->set( 'event'	    , $event)
			    ->set( 'handler'	    , $handler )
			    ->set( 'guests'	    , $guests )
			    ->set( 'eventid'	    , $event->id )
			    ->set( 'isMine'	    , $event->isCreator($my->id) )
			    ->set( 'isSuperAdmin'   , $isSuperAdmin )
			    ->set( 'pagination'	    , $pagination )
			    ->set( 'my'		    , $my )
			    ->set( 'config'	    , $config )
			    ->fetch( 'events.viewguest' );
	}
	
	public function search()
	{
		CFactory::load( 'helpers' , 'event' );
		
		// Get the document object and set the necessary properties of the document
		$document	= JFactory::getDocument();
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS') , CRoute::_('index.php?option=com_community&view=events') );
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS_SEARCH') , '' );
		$document->setTitle(JText::_('COM_COMMUNITY_SEARCH_EVENTS_TITLE'));

		$mainframe	=& JFactory::getApplication();
		$script = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
		$document->addCustomTag( $script );
		
		// Display the submenu
		$this->showSubmenu();

		//New search features
		$model		=   CFactory::getModel( 'events' );
		$categories	=   $model->getCategories();

		// input filtered to remove tags
		$search		=   JRequest::getVar( 'search' , '' );

		// Input for advance search
		$catId		=   JRequest::getInt( 'catid', '' );
		$unit		=   JRequest::getVar( 'unit', '' );

		$category	=& JTable::getInstance( 'EventCategory' , 'CTable' );
		$category->load( $catId );
		
		$advance		    =   array();
		$advance['startdate']	    =   JRequest::getVar( 'startdate', '' );
		$advance['enddate']	    =   JRequest::getVar( 'enddate', '' );
		$advance['radius']	    =   JRequest::getVar( 'radius', '' );
		$advance['fromlocation']    =   JRequest::getVar( 'location', '' );

		if( $unit === COMMUNITY_EVENT_UNIT_KM )
		{
			// Since our searching need a value in Miles unit, we need to convert the KM value to Miles
			// 1 kilometre	=   0.621371192 miles
			$advance['radius']  =	$advance['radius'] * 0.621371192;
		}
		
		$events		= '';
		$pagination	= null;
		$posted		= JRequest::getInt( 'posted', '' );
		$count		= 0;
		$eventsHTML	= '';

		// Test if there are any post requests made
		if( !empty($search) || (!empty($advance['startdate']) || !empty($advance['enddate']) || !empty($advance['radius']) || !empty($advance['fromlocation'])) )
		{
			// Check for request forgeries
			JRequest::checkToken('get') or jexit( JText::_( 'COM_COMMUNITY_INVALID_TOKEN' ) );

			CFactory::load( 'libraries' , 'apps' );
			$appsLib	=&  CAppPlugins::getInstance();
			$saveSuccess	=   $appsLib->triggerEvent( 'onFormSave' , array('jsform-events-search' ));
			
			if( empty($saveSuccess) || !in_array( false , $saveSuccess ) )
			{
				$events	    = $model->getEvents( $category->id, null , null , $search, null, null, null, $advance );
				$pagination = $model->getPagination();
				$count	    = $model->getEventsSearchTotal();
			}
		}
		
		// Get the template for the events lists
		$eventsHTML	= $this->_getEventsHTML( $events, false, $pagination );

		CFactory::load( 'libraries' , 'apps' );
		$app			=&  CAppPlugins::getInstance();
		$appFields		=   $app->triggerEvent('onFormDisplay' , array( 'jsform-events-search') );
		$beforeFormDisplay	=   CFormElement::renderElements( $appFields , 'before' );
		$afterFormDisplay	=   CFormElement::renderElements( $appFields , 'after' );

		$searchLinks	=   parent::getAppSearchLinks('events');

		// Revert back the radius value
		$advance['radius']	=   JRequest::getVar( 'radius', '' );

		$tmpl	=   new CTemplate();
		echo $tmpl  ->set( 'beforeFormDisplay'  , $beforeFormDisplay )
			    ->set( 'afterFormDisplay'   , $afterFormDisplay )
			    ->set( 'posted'		, $posted )
			    ->set( 'eventsCount'	, $count )
			    ->set( 'eventsHTML'		, $eventsHTML )
			    ->set( 'search'		, $search )
			    ->set( 'catId'		, $category->id )
			    ->set( 'categories'		, $categories )
			    ->set( 'advance'		, $advance )
			    ->set( 'unit'		, $unit )
			    ->set( 'searchLinks'	, $searchLinks )
			    ->fetch( 'events.search' );
	}
	
	/**
	 * An event has just been created, should we just show the album ?
	 */
	public function created()
	{

		$eventid 	=  JRequest::getInt( 'eventid', 0 );

		CFactory::load( 'models' , 'events');
		$event		= JTable::getInstance( 'Event' , 'CTable' );

		$event->load($eventid);
		$document = JFactory::getDocument();
		$document->setTitle( $event->title );

		$uri	= JURI::base();
		$this->showSubmenu();

		$tmpl	= new CTemplate();
		echo $tmpl  ->set( 'link'	, CRoute::_('index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id ) )
			    ->set( 'linkUpload'	, CRoute::_('index.php?option=com_community&view=events&task=uploadavatar&eventid=' . $event->id ) )
			    ->set( 'linkEdit'	, CRoute::_('index.php?option=com_community&view=events&task=edit&eventid=' . $event->id ) )
			    ->set( 'linkInvite'	, CRoute::_('index.php?option=com_community&view=events&task=invitefriends&eventid=' . $event->id ) )
			    ->fetch( 'events.created' );
	}
	
	public function sendmail()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_EVENTS_EMAIL_SEND'));
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS') , CRoute::_('index.php?option=com_community&view=events') );
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS_EMAIL_SEND') );
		
		if(!$this->accessAllowed('registered'))
		{
			return;
		}
		
		// Display the submenu
		$this->showSubmenu();
		$eventId	= JRequest::getInt('eventid' , '' );
		
		CFactory::load( 'helpers', 'owner' );		
		CFactory::load( 'models' , 'events' );
		$event		=& JTable::getInstance( 'Event' , 'CTable' );
		$event->load( $eventId );

		if( empty($eventId ) || empty( $event->title) )
		{
			echo JText::_('COM_COMMUNITY_INVALID_ID_PROVIDED');
			return;
		}
		
		$my			= CFactory::getUser();
		$config		= CFactory::getConfig();

		CFactory::load( 'libraries' , 'editor' );
		$editor	    =	new CEditor( $config->get('htmleditor') );
		
		CFactory::load( 'helpers' , 'event' );
		$handler	= CEventHelper::getHandler( $event );
		if( !$handler->manageable() )
		{
			$this->noAccess();
			return;
		}

		$message    =	JRequest::getVar( 'message' , '' , 'post' , 'string' , JREQUEST_ALLOWRAW );
		$title	    =	JRequest::getVar( 'title'	, '' );
		
		$tmpl	=   new CTemplate();
		echo $tmpl  ->set( 'editor'	, $editor )
			    ->set( 'event'	, $event )
			    ->set( 'message'	, $message )
			    ->set( 'title'	, $title )
			    ->fetch( 'events.sendmail' );
	}
	
	public function uploadAvatar()
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_EVENTS_AVATAR'));
        
		$eventid    = JRequest::getVar('eventid', '0');
		$this->_addEventInPathway( $eventid );
		$this->addPathway( JText::_('COM_COMMUNITY_EVENTS_AVATAR') );

		$this->showSubmenu();
		$event		=& JTable::getInstance( 'Event' , 'CTable' );
		$event->load( $eventid );
		
		CFactory::load( 'helpers' , 'event' );
		$handler	= CEventHelper::getHandler( $event );
		if( !$handler->manageable() )
		{
			$this->noAccess();
			return;
		}
		
		$config			= CFactory::getConfig();
		$uploadLimit	= (double) $config->get('maxuploadsize');
		$uploadLimit	.= 'MB';

		CFactory::load( 'models' , 'events' );
		$event	=& JTable::getInstance( 'Event' , 'CTable' );
		$event->load( $eventid );

		CFactory::load( 'libraries' , 'apps' );
		$app 		=& CAppPlugins::getInstance();
		$appFields	= $app->triggerEvent('onFormDisplay' , array( 'jsform-events-uploadavatar') );
		$beforeFormDisplay	= CFormElement::renderElements( $appFields , 'before' );
		$afterFormDisplay	= CFormElement::renderElements( $appFields , 'after' );

		$tmpl	= new CTemplate();
		echo $tmpl  ->set( 'beforeFormDisplay'	, $beforeFormDisplay )
			    ->set( 'afterFormDisplay'	, $afterFormDisplay )
			    ->set( 'eventId'		, $eventid )
			    ->set( 'avatar'		, $event->getAvatar('avatar') )
			    ->set( 'thumbnail'		, $event->getThumbAvatar() )
			    ->set( 'uploadLimit'	, $uploadLimit )
			    ->fetch( 'events.uploadavatar' );
	}
	
	public function _addEventInPathway( $eventId )
	{
		CFactory::load( 'models' , 'events' );
		$event			=& JTable::getInstance( 'Event' , 'CTable' );
		$event->load( $eventId );

		$this->addPathway( $event->title , CRoute::_('index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id) );
	}
	
	public function _getEventsHTML( $eventObjs, $isExpired = false, $pagination = NULL)
	{
		CFactory::load( 'helpers' , 'owner' );

		$events	= array();
		CFactory::load( 'models' , 'events' );
		
		$config	=   CFactory::getConfig();
		$format	=   ($config->get('eventshowampm')) ?  JText::_('COM_COMMUNITY_DATE_FORMAT_LC2_12H') : JText::_('COM_COMMUNITY_DATE_FORMAT_LC2_24H');

		if( $eventObjs )
		{
			foreach( $eventObjs as $row )
			{
				$event	    =&	JTable::getInstance( 'Event' , 'CTable' );
				$event->bind( $row );
				$events[]   =	$event;
			}
			unset($eventObjs);
		}	
		
		CFactory::load( 'libraries' , 'featured' );
		$featured	= new CFeatured( FEATURED_EVENTS );
		$featuredList	= $featured->getItemIds();

		$tmpl	=   new CTemplate();
		return $tmpl	->set( 'showFeatured'	    , $config->get('show_featured') )
				->set( 'featuredList'	    , $featuredList )
				->set( 'isCommunityAdmin'   , COwnerHelper::isCommunityAdmin() )
				->set( 'events'		    , $events )
				->set( 'isExpired'	    , $isExpired )
				->set( 'pagination'	    , $pagination )
				->set( 'timeFormat'	    , $format)
				->fetch( 'events.list' );
	}

	public function _getEventsCategories( $categoryId )
	{
		$model		= CFactory::getModel( 'events' );

		$categories = $model->getCategoriesCount();

		CFactory::load('helpers','category');

		$categories = CCategoryHelper::getParentCount($categories, $categoryId);

		return $categories;

	}
	
	public function _getPendingListHTML($user)
	{
		CFactory::load( 'models', 'events' );
		$model	    =   CFactory::getModel( 'events' );
		$sorted	    =	JRequest::getVar( 'sort' , 'startdate' , 'GET' );
		$pending    =	COMMUNITY_EVENT_STATUS_INVITED;
		$rows	    =   $model->getEvents( null, $user->id , $sorted, null, true, false, $pending );		
		$events	    =   array();
		
		if( $rows )
		{
			foreach( $rows as $row )
			{
				$event	    =&  JTable::getInstance( 'Event' , 'CTable' );
				$event->bind( $row );
				$events[]   =	$event;
			}
		}
		
		$tmpl	=   new CTemplate();
		return $tmpl	->set( 'events',	$events )
				->fetch( 'events.pendinginvitelist' );
	}
	
	public function _getEventsFeaturedList(){
		CFactory::load( 'libraries' , 'featured' );
		$featured		= new CFeatured( FEATURED_EVENTS );
		$featuredEvents	= $featured->getItemIds();
		$featuredList	= array();

		foreach($featuredEvents as $event )
		{
			$table	=&  JTable::getInstance( 'Event' , 'CTable' );
			$table->load($event);
			$featuredList[]	= $table;
		}
		return $featuredList;
	}
}
