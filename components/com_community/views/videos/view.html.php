<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CommunityViewVideos extends CommunityView
{
	var $_videoLib	= null;
	var $model		= '';
	
	public function CommunityViewVideos()
	{
		CFactory::load( 'helpers', 'videos' );
		CFactory::load( 'libraries' , 'videos' );
		$this->model	= CFactory::getModel('videos');
		$this->videoLib	= new CVideoLibrary();
	}

	private function _getVideosHTML( $videos, $pagination = NULL)
	{
		//$videos	= $videos ? CVideosHelper::prepareVideos($videos) : array();
		
		$videoEntries	= array();
		
		if ($videos)
		{
			foreach($videos as $videoEntry)
			{
				$video	=& JTable::getInstance('Video','CTable');
				$video->bind( $videoEntry );
				$videoEntries[]	= $video;
			}
		}
		
		$my		= CFactory::getUser();
		$user	= CFactory::getUser(JRequest::getInt('userid' , $my->id ));

		// for featured/unfeatured link
		CFactory::load( 'helpers', 'owner' );
		
		CFactory::load( 'libraries' , 'featured' );
		$featured	= new CFeatured( FEATURED_VIDEOS );
		$featuredVideos	= $featured->getItemIds();
		$featuredList	= array();
		
		foreach($featuredVideos as $videoId )
		{
			$featuredList[]	= $videoId;
		}
		
		$allowManageVideos 	= true;
		$groupVideo 		= false;
		$groupId			= JRequest::getVar('groupid' , '' , 'GET');		
		
		$task			= JRequest::getVar( 'task' , '' , 'GET' );
		$redirectUrl	= CRoute::getURI( false );

		if( !empty($groupId) )
		{
			CFactory::load( 'helpers' , 'group' );			
			$allowManageVideos	= CGroupHelper::allowManageVideo($groupId);
			$groupVideo			= true;
		}

		CFactory::load( 'libraries', 'videos' );
		CFactory::load( 'helpers', 'string' );
		
		$tmpl	= new CTemplate();
		return $tmpl->set( 'sort'				, JRequest::getVar('sort', 'latest') )
					->set( 'currentTask'		, JRequest::getCmd( 'task' , '') )
					->set( 'videos'			, $videoEntries )
					->set( 'videoThumbWidth'	, CVideoLibrary::thumbSize('width') )
					->set( 'videoThumbHeight'	, CVideoLibrary::thumbSize('height') )
					->set( 'redirectUrl'		, $redirectUrl )
					->set( 'my'				, $my )
					->set( 'user'				, $user )
					->set( 'featuredList'		, $featuredList )
					->set( 'isCommunityAdmin' 	, COwnerHelper::isCommunityAdmin() )
					->set( 'allowManageVideos' , $allowManageVideos )
					->set( 'groupVideo' 		, $groupVideo)
					->set( 'pagination' 		, $pagination)
					->fetch( 'videos.list' );
	}

	/**
	 *	Get Featured Videos
	 *	
	 *	@return		array	Objects of random featured videos
	 *	@since		1.5
	 */
	private function _getFeatVideos()
	{
		CFactory::load( 'libraries' , 'featured' );
		CFactory::load('helpers', 'videos');
		
		$featured		= new CFeatured( FEATURED_VIDEOS );
		$featuredVideos	= $featured->getItemIds();
		$featuredList	= array();
		
		foreach($featuredVideos as $videoId )
		{
			$table		=& JTable::getInstance( 'Video' , 'CTable' );
			$table->load($videoId);
			
			$featuredList[]	= $table;
		}
		
		return $featuredList;
	}
	
	/**
	 * Return sort options for videos
	 */
	private function _getSortOptions()
	{
		$sortItems	= array
		(
			'latest' 		=> JText::_('COM_COMMUNITY_VIDEOS_SORT_LATEST'),
			'mostwalls'		=> JText::_('COM_COMMUNITY_VIDEOS_SORT_MOST_WALL_POST'),
			'mostviews'		=> JText::_('COM_COMMUNITY_VIDEOS_SORT_POPULAR'),
			'title'			=> JText::_('COM_COMMUNITY_VIDEOS_SORT_TITLE')
		);
		
		return $sortItems;
	}

	/**
	 *	Generate Featured Videos HTML
	 *	
	 *	@param		array	Array of video objects
	 *	@return		string	HTML
	 *	@since		1.2
	 */
	private function _getFeatHTML($videos)
	{
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'libraries', 'videos' );

		$tmpl	= new CTemplate();
		return $tmpl->set( 'videos'		, $videos )
					->set( 'isCommunityAdmin' , COwnerHelper::isCommunityAdmin() )
					->set( 'videoThumbWidth'	, CVideoLibrary::thumbSize('width') )
					->set( 'videoThumbHeight'	, CVideoLibrary::thumbSize('height') )
					->fetch( 'videos.featured' );
	}

	/**
	 * Display all videos in the whole system
	 **/
	public function display($id= null)
	{
		// Load required filterbar library that will be used to display the filtering and sorting.
		CFactory::load( 'libraries' , 'filterbar' );
		
		$mainframe  =	JFactory::getApplication();
		$document   =	JFactory::getDocument();
		$my	    =	CFactory::getUser();
		$model	    =	CFactory::getModel('videos');

		

		// Get category id from the query string if there are any.
		$categoryId	= JRequest::getInt( 'catid' , null );
		$category	= JTable::getInstance( 'VideosCategory' , 'CTable' );
		$category->load( $categoryId );

		$groupId    = JRequest::getVar('groupid','', 'GET');
		if (!empty($groupId))
		{
			$group =& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );
			
			// @rule: Test if the group is unpublished, don't display it at all.
			if( !$group->published )
			{
				$this->_redirectUnpublishGroup();
				return;
			}
		
			// Set pathway for group videos
			// Community > Groups > Group Name > Videos
			$this->addPathway( JText::_('COM_COMMUNITY_GROUPS'), CRoute::_('index.php?option=com_community&view=groups') );
			$this->addPathway( $group->name, CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId));
		}
		
		$this->_addSubmenu();
		$this->showSubmenu(); 
	
		// If we are browing by category, add additional breadcrumb and add
		// category name in the page title
		if($categoryId != null)
		{
			if (!empty($groupId))
			{
				$this->addPathway( JText::_('COM_COMMUNITY_VIDEOS'), CRoute::_('index.php?option=com_community&view=videos&groupid=' . $groupId));
			} else {
				$this->addPathway( JText::_('COM_COMMUNITY_VIDEOS'), CRoute::_('index.php?option=com_community&view=videos') );	
			}

			$this->addPathway( JText::_( $this->escape( $category->name ) ), '' );
			$document->setTitle( JText::_( 'COM_COMMUNITY_VIDEOS_CATEGORIES' ) . ' : ' . JText::_( $this->escape( $category->name ) ) );
		}
		else
		{
			$this->addPathway( JText::_( 'COM_COMMUNITY_VIDEOS' ) );
			$document->setTitle(JText::_( 'COM_COMMUNITY_VIDEOS_BROWSE_ALL_VIDEOS' ));
		}

		$groupId = JRequest::getVar('groupid' , '' , 'GET');  
		  
		$groupLink = !empty($groupId) ? '&groupid=' . $groupId : '';
		$feedLink  = CRoute::_('index.php?option=com_community&view=videos' . $groupLink . '&format=feed');
		$feed      = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_SUBSCRIBE_ALL_VIDEOS_FEED') . '" href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );

		// Featured Videos
		$featVideos	=   '';
		$featuredHTML	=   '';
		$sorted 	=   JRequest::getVar('sort', 'latest');
		$limitstart	=   JRequest::getVar( 'limitstart' , 0 );
		$permissions	=   ($my->id==0) ? 0 : 20;
		$permissions  = COwnerHelper::isCommunityAdmin() ? 40 : $permissions;
		
		$cat_id = isset($category->id)?$category->id:"";
		
		if( !empty($groupId) )
		{
			// $group object loaded above
			CFactory::load( 'helpers' , 'owner' );
			$isMember	=   $group->isMember( $my->id );
			$isMine		=   ($my->id == $group->ownerid);
			$isBanned	=   $group->isBanned( $my->id );
			
			if( !$isMember && !$isMine && !COwnerHelper::isCommunityAdmin() && $group->approvals == COMMUNITY_PRIVATE_GROUP )
			{
				$this->noAccess( JText::_('COM_COMMUNITY_GROUPS_PRIVATE_NOTICE') );
				return;
			}
			
			// cache video list.
			$videosData  =	$this->getVideosShowAllVideos( $cat_id, $permissions,$sorted,$groupId );
			$videosHTML  =	$videosData['HTML'];
			
			$allVideosUrl 	= 'index.php?option=com_community&view=videos&groupid='.$groupId;
			$catVideoUrl	= 'index.php?option=com_community&view=videos&groupid='.$groupId.'&catid=';
		}
		else
		{
			// cache video list.
			$videosData  =	$this->getVideosShowAllVideos( $cat_id, $permissions,$sorted  );
			$videosHTML  =	$videosData['HTML'];
			
			$allVideosUrl 	= 'index.php?option=com_community&view=videos';
			$catVideoUrl	= 'index.php?option=com_community&view=videos&task=display&catid=';
		
			// Featured Videos
			// Cache featured video.
			$featuredData  =	$this->_cachedCall('getVideosFeaturedList', array(), '', array( COMMUNITY_CACHE_TAG_FEATURED ) );
			$featuredHTML	= $featuredData['HTML'];
		}

		//Cache for category
		//This is local file
		$categories  =	$this->getVideosCategories($categoryId);
		$sortItems	= $this->_getSortOptions();

		CFactory::load( 'libraries' , 'privacy' );
		
		$tmpl	= new CTemplate();
		echo $tmpl	->set( 'sort'			, JRequest::getVar('sort', 'latest') )
					->set( 'currentTask'	, JRequest::getCmd( 'task' , '') )
					->set( 'featuredHTML'	, $featuredHTML )
					->set( 'videosHTML'	, $videosHTML )
					->set( 'categories' 	, $categories )
					->set( 'category' 		, $category )
					->set( 'sortings'		, CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'latest') )
					->set( 'allVideosUrl'	, $allVideosUrl )
					->set( 'catVideoUrl'	, $catVideoUrl )
					->fetch( 'videos.index' );	
	}
	/**
	* List All FEATURED VIDEO
	**/
	public function getVideosFeaturedList(){
	    $featVideos		= $this->_getFeatVideos();
	    $featuredHTML['HTML']	= $this->_getFeatHTML( $featVideos );
	    return $featuredHTML;
	}
	/**
	* List All category
	**/
	public function getVideosCategories($categoryId){
	    $model	    =	CFactory::getModel('videos');
	    $categories	= $model->getCategories($categoryId);
	    return $categories;
	 }
	 /**
	 * List All Videos
	 **/
	public function getVideosShowAllVideos($category,$permissions,$sorted,$groupId=null){
	    $model	    =	CFactory::getModel('videos');
	    if(is_null($groupId)){
		$filters	  = array
				(
					'status'		=> 'ready',
					'category_id'	=> $category,
					'permissions'	=> $permissions,
					'or_group_privacy'	=> 0,
					'sorting'		=> $sorted
				);
		$videos		= $model->getVideos($filters);
	    } else{
		$videos		= $model->getGroupVideos($groupId, $category);
	    }
	    $pagination	= $model->getPagination();
	    $videosHTML['HTML']	= $this->_getVideosHTML( $videos, $pagination );

	    return $videosHTML;
	}
	/**
	 * Application full view
	 **/
	public function appFullView()
	{
		$document		= JFactory::getDocument();
		$document->setTitle( JText::_('COM_COMMUNITY_VIDEOS_WALL_TITLE') );
		
		$applicationName	= JString::strtolower( JRequest::getVar( 'app' , '' , 'GET' ) );

		if(empty($applicationName))
		{
			JError::raiseError( 500, JText::_('COM_COMMUNITY_APP_ID_REQUIRED'));
		}

		$output	= '';
		
		if( $applicationName == 'walls' )
		{
			CFactory::load( 'libraries' , 'wall' );
			$limit		= JRequest::getVar( 'limit' , 5 , 'REQUEST' );
			$limitstart = JRequest::getVar( 'limitstart', 0, 'REQUEST' );
			$videoId	= JRequest::getInt( 'videoid' , '' , 'GET' );
			$my			= CFactory::getUser();
			$config		= CFactory::getConfig();
			
			$video		=& JTable::getInstance( 'Video' , 'CTable' );
			$video->load( $videoId );
		
			CFactory::load( 'helpers' , 'owner' );
			CFactory::load( 'helpers' , 'friends' );
					
			if( !$config->get('lockvideoswalls') || ($config->get('lockvideoswalls') && CFriendsHelper::isConnected($my->id, $video->creator) ) || COwnerHelper::isCommunityAdmin() )
			{
				$output	.= CWallLibrary::getWallInputForm( $video->id , 'videos,ajaxSaveWall', 'videos,ajaxRemoveWall' );
			}

			// Get the walls content
			$viewAllLink = false;
			$wallCount	= false;
			if(JRequest::getVar('task', '', 'REQUEST') != 'app')
			{
				$viewAllLink	= CRoute::_('index.php?option=com_community&view=videos&task=app&videoid=' . $video->id . '&app=walls');
				$wallCount		= CWallLibrary::getWallCount('videos', $video->id);
			}
			$output 		.='<div id="wallContent">';
			$output			.= CWallLibrary::getWallContents( 'videos' , $video->id , ( COwnerHelper::isCommunityAdmin() || COwnerHelper::isMine($my->id, $video->creator) ) , $limit , $limitstart );
			$output			.= CWallLibrary::getViewAllLinkHTML($viewAllLink, $wallCount);
			$output 		.= '</div>';
			
			jimport('joomla.html.pagination');
			$wallModel 		= CFactory::getModel('wall');
			$pagination		= new JPagination( $wallModel->getCount( $video->id , 'videos' ) , $limitstart , $limit );

			$output		.= '<div class="pagination-container">' . $pagination->getPagesLinks() . '</div>';
		}
		else
		{
			$model				= CFactory::getModel('apps');
			$applications		=& CAppPlugins::getInstance();
			$applicationId		= $model->getUserApplicationId( $applicationName );

			$application		= $applications->get( $applicationName , $applicationId );
				
			if (is_callable(array($application, 'onAppDisplay'), true))
			{
				// Get the parameters
				$manifest			= CPluginHelper::getPluginPath('community',$applicationName) . DS . $applicationName . DS . $applicationName . '.xml';

				$params			= new CParameter( $model->getUserAppParams( $applicationId ) , $manifest );

				$application->params	=& $params;
				$application->id		= $applicationId;

				$output	= $application->onAppDisplay( $params );
			}
			else
			{
				JError::raiseError( 500, JText::_('COM_COMMUNITY_APPS_NOT_FOUND'));
			}
		}
		
		echo $output;
	}

	/**
	 * View to display the search form
	 **/
	public function search()
	{
		$document	= JFactory::getDocument();
		
		$this->addPathway( JText::_( 'COM_COMMUNITY_VIDEOS' ) , CRoute::_('index.php?option=com_community&view=videos' ) );
		$this->addPathway( JText::_('COM_COMMUNITY_VIDEOS_SEARCH_VIDEOS') , '' );
		$document->setTitle(JText::_( 'COM_COMMUNITY_VIDEOS_SEARCH_VIDEOS' ));

		$this->_addSubmenu();
		$this->showSubmenu();

		$search		= JRequest::getVar( 'search-text' , '' , 'REQUEST' );
		$result		= array();
		$pagination	= null;
		$total		= 0;

		if( !empty( $search ) )
		{
			$searchModel = CFactory::getModel( 'Search' );
			$result		 = $searchModel->searchVideo( $search );
			$pagination	 = $searchModel->getPagination();
			$total		 = $searchModel->getTotal();
		}

		$searchLinks	=   parent::getAppSearchLinks('videos');
		
		$pagination	= is_null($pagination) ? '' : $pagination->getPagesLinks();
		
		$videosHTML	= $this->_getVideosHTML($result);
		
		$tmpl		= new CTemplate();
		echo $tmpl	->set( 'videosHTML'	, $videosHTML )
					->set( 'pagination'	, $pagination )
					->set( 'videosCount'	, $total )
					->set( 'search'		, $search)
					->set( 'searchLinks'	, $searchLinks )
					->fetch( 'videos.search' );
	}
		 
	public function myvideos($id = null)
	{
		$document 	= JFactory::getDocument();
		$my			= CFactory::getUser();
		$userid		= JRequest::getInt( 'userid' , $my->id );
		$user		= CFactory::getUser($userid);
		
		// Set document title
                CFactory::load('helpers' , 'owner' );
                $blocked	= $user->isBlocked();

		if( $blocked && !COwnerHelper::isCommunityAdmin() )
		{
			$tmpl	= new CTemplate();
			echo $tmpl->fetch('profile.blocked');
			return;
		}
		
		if($my->id == $user->id)
			$title	= JText::_('COM_COMMUNITY_VIDEOS_MY');
		else
			$title	= JText::sprintf('COM_COMMUNITY_VIDEOS_USERS_VIDEO_TITLE', $user->getDisplayName());

		$document->setTitle($title);

		// Set pathway
		$mainframe	=& JFactory::getApplication();
		$this->addPathway( JText::_( 'COM_COMMUNITY_VIDEOS' ) , CRoute::_('index.php?option=com_community&view=videos' ) );
		$this->addPathway( $title );  

		$feedLink = CRoute::_('index.php?option=com_community&view=videos&userid=' . $user->id . '&format=feed');
		$feed = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_SUBSCRIBE_MY_VIDEOS_FEED') . '" href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );

		// Show the mini header when viewing other's photos
		if($my->id != $user->id)
			$this->attachMiniHeaderUser($user->id);

		// Display submenu
		$this->_addSubmenu();
		$this->showSubmenu();

		// Get data from DB
		$model			= CFactory::getModel('videos');

		$filters		= array
		(
			'creator'	=> $user->id,
			'status'	=> 'ready',
			'sorting'	=> JRequest::getVar('sort', 'latest')
		);
		$videos			= $model->getVideos($filters);

		// Load required filterbar library that will be used to display the filtering and sorting.
		CFactory::load( 'libraries' , 'filterbar' );

		$sortItems	= $this->_getSortOptions();

		//pagination
		$pagination		= $model->getPagination();

		$videosHTML		= $this->_getVideosHTML( $videos, $pagination);
		
		$tmpl		= new CTemplate();
		echo $tmpl	->set( 'user'			, $user )
					->set( 'sort'			, JRequest::getVar('sort', 'latest') )
					->set( 'currentTask'	, JRequest::getCmd( 'task' , '') )
					->set( 'videosHTML'	, $videosHTML )
					->set( 'sortings'		, CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'latest') )
					->set( 'pagination'	, $pagination )
					->fetch( 'videos.myvideos' );	
	}

	public function mypendingvideos($id= null)
	{
		$document 	= JFactory::getDocument();
		$my			= CFactory::getUser();
		
		$userid		= JRequest::getInt( 'userid' , '' );
		$user		= CFactory::getUser($userid);

		$this->_addSubmenu();
		$this->showSubmenu();

		// Set pathway
		$mainframe	=& JFactory::getApplication();
		$pathway 	=& $mainframe->getPathway();
		$pathway->addItem( 'My Pending Videos', '' );

		// Get data from DB
		$model			= CFactory::getModel('videos');
		
		// Group video pending
		$groupid = JRequest::getVar('groupid', '0');		
		if(!empty($groupid))
		{
			$filters		= array
			(
				'groupid'	=> $groupid,
				'status'	=> 'pending'
			);
		}
		else
		{
			$filters		= array
			(
				'creator'	=> $user->id,
				'groupid'	=> 0,
				'status'	=> 'pending'
			);
		}
		
		$pendingVideos	= $model->getVideos($filters);

		CFactory::load( 'helpers' , 'owner' );

		// Substitute permission in text form
		foreach ($pendingVideos as $video) {
			//$video		=& $this->_getExtra($video);
			$video->isOwner = COwnerHelper::isMine($my->id, $video->creator);
		}
		
		$videosHTML		= $this->_getVideosHTML( $pendingVideos );
		
		$pagination		= $model->getPagination();		
		

		$tmpl	= new CTemplate();

		echo $tmpl	->set( 'videosHTML'	, $videosHTML )
					->set( 'sort'			, JRequest::getVar('sort', 'latest') )
					->set( 'currentTask'	, JRequest::getCmd( 'task' , '') )
					->set( 'pendingVideos'	, $pendingVideos )
					->set( 'pagination'	, $pagination )
					->set( 'params'		, $this->videoLib )
					->fetch( 'videos.pending' );	
	}

	/**
	 * Method to display video
	 *
	 **/
	public function video()
	{
		$mainframe	=& JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= CFactory::getConfig();
		$my		= CFactory::getUser();
		$videoId	= JRequest::getVar('videoid' , '' , 'GET');
		$groupId	= JRequest::getVar('groupid' , '' , 'GET');	

		// Get show video location map by default
		$videoMapsDefault	= $config->get('videosmapdefault');

		// Load window library
		CFactory::load( 'libraries' , 'window' );
		// Load necessary window css / javascript headers.
		CWindow::load();
		// Load the video table

		// Load string helper
		CFactory::load( 'helpers' , 'string' );

		$video		=& JTable::getInstance( 'Video' , 'CTable' );
		if (empty($videoId))
 		{
 			$url	= CRoute::_('index.php?option=com_community&view=videos', false);
 			$mainframe->redirect($url, JText::_('COM_COMMUNITY_VIDEOS_ID_ERROR'), 'warning');
		}
		if (!$video->load($videoId))
		{
			$url	= CRoute::_('index.php?option=com_community&view=videos', false);
			$mainframe->redirect($url, JText::_('COM_COMMUNITY_VIDEOS_NOT_AVAILABLE'), 'warning');
		}
		
		// Setting up the sub menu
		CFactory::load( 'helpers' , 'owner' );
		if( COwnerHelper::isCommunityAdmin() || ($my->id == $video->creator && ($my->id != 0)) )
		{
			$this->addSubmenuItem('' , JText::_('COM_COMMUNITY_VIDEOS_FETCH_THUMBNAIL'), 'joms.videos.fetchThumbnail(\'' . $video->id . '\')', true);

			// Only add the set as profile video for video owner
			if( $my->id == $video->creator )
			{
				$this->addSubmenuItem('' , JText::_('COM_COMMUNITY_VIDEOS_SET_AS_PROFILE'), 'joms.videos.linkConfirmProfileVideo(\'' . $video->id . '\')', true);
			}
			$redirectUrl	= CRoute::getURI( false );
			$this->addSubmenuItem( '' , JText::_('COM_COMMUNITY_EDIT') , 'joms.videos.showEditWindow(\'' . $video->id . '\',\'' . $redirectUrl . '\');' , true );
			$this->addSubmenuItem( '' , JText::_('COM_COMMUNITY_DELETE'), 'joms.videos.deleteVideo(\'' . $video->id . '\')', true);
		}
		$this->_addSubmenu();
		$this->showSubmenu();
        
		// Show the mini header when viewing other's photos
		if( ($video->creator_type == VIDEO_USER_TYPE) && ($my->id != $video->creator))
		{
			$this->attachMiniHeaderUser($video->creator);
		}

		// Check permission
		$user		= CFactory::getUser( $video->creator );
		$blocked	= $user->isBlocked();
		if( $blocked && !COwnerHelper::isCommunityAdmin() )
		{
			$tmpl	= new CTemplate();
			echo $tmpl->fetch('profile.blocked');
			return;
		}
		
		
		$sorted 	=   JRequest::getVar('sort', 'latest');
		$limit	=   JRequest::getVar( 'limitstart' , 6 );
		$permissions=   ($my->id==0) ? 0 : 20;
		$cat_id		=	JRequest::getVar('cat_id', '');
		$model		=	CFactory::getModel('videos');
		
		if( $video->creator_type == VIDEO_GROUP_TYPE )
		{
			CFactory::load( 'helpers' , 'group' );
			
			if(!CGroupHelper::allowViewMedia($groupId))
			{
				$document->setTitle( JText::_('COM_COMMUNITY_RESTRICTED_ACCESS') );
				$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS', 'notice'));			
				echo JText::_('COM_COMMUNITY_GROUPS_VIDEO_MEMBER_PERMISSION');
				return;
			}

			$group =& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );
						
			// Set pathway
			$pathway 		=& $mainframe->getPathway();
			$pathway->addItem( JText::_('COM_COMMUNITY_GROUPS'), CRoute::_('index.php?option=com_community&view=groups') );
			$pathway->addItem($group->name, CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId));
			$pathway->addItem( JText::_('COM_COMMUNITY_VIDEOS'), CRoute::_('index.php?option=com_community&view=videos&groupid=' . $groupId));
			$pathway->addItem( $video->getTitle(), '' );
			
			
			$otherVideos		= $model->getGroupVideos($groupId, $cat_id, $limit);
		}
		else 
		{
			if (!$this->isPermitted($my->id, $video->creator, $video->permissions))
			{
				$document->setTitle( JText::_('COM_COMMUNITY_RESTRICTED_ACCESS') );
				$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS', 'notice'));			
				
				switch ($video->permissions)
				{
					case '40':
						$this->noAccess(JText::_('COM_COMMUNITY_VIDEOS_OWNER_ONLY', 'notice'));
						break;
					case '30':
						$owner	= CFactory::getUser($video->creator);
						$this->noAccess(JText::sprintf('COM_COMMUNITY_VIDEOS_FRIEND_PERMISSION_MESSAGE', $owner->getDisplayName()));
						break;
					default:
						$this->noAccess();
						break;
				}
				return;
			}
			// Set pathway
			$pathway 		=& $mainframe->getPathway();
			$pathway->addItem( 'Video', CRoute::_('index.php?option=com_community&view=videos') );
			$pathway->addItem( $video->getTitle(), '' );
						
			$filters	  = array
						(
							'status'		=> 'ready',
							'category_id'	=> $cat_id,
							'creator'	=> $user->id,
							'permissions'	=> $permissions,
							'or_group_privacy'	=> 0,
							'sorting'		=> $sorted,
							'limit'			=> $limit
						);
			$otherVideos		= $model->getVideos($filters);
		}
		//var_dump($otherVideos);
		// Set the current user's active profile
		CFactory::setActiveProfile( $video->creator );
		
		// Hit counter + 1
		$video->hit();

		// Get reporting html
		$reportHTML		= '';
		CFactory::load('libraries', 'reporting');
		$report			= new CReportingLibrary();
		$reportHTML		= $report->getReportingHTML( JText::_('COM_COMMUNITY_VIDEOS_REPORT_VIDEOS') , 'videos,reportVideo' , array( $video->id ) );
		
		// Get bookmark html
		CFactory::load( 'libraries' , 'bookmarks' );
		$bookmarks		= new CBookmarks($video->getPermalink());
		$bookmarksHTML	= $bookmarks->getHTML();
	
		// Get the walls
		CFactory::load( 'libraries' , 'wall' );
		$wallContent	= CWallLibrary::getWallContents( 'videos' , $video->id , ( COwnerHelper::isCommunityAdmin() || ($my->id == $video->creator && ($my->id != 0)) ) , 10 ,0);
		$wallCount		= CWallLibrary::getWallCount('videos', $video->id);
		$viewAllLink = false;
		if(JRequest::getVar('task', '', 'REQUEST') != 'app')
		{
			$viewAllLink	= CRoute::_('index.php?option=com_community&view=videos&task=app&videoid=' . $video->id . '&app=walls');
		}
		$wallContent	.= CWallLibrary::getViewAllLinkHTML($viewAllLink, $wallCount);
		
		$wallForm		= '';
		
		if( $this->isPermitted( $my->id , $video->creator , PRIVACY_FRIENDS ) || !$config->get('lockvideoswalls') )
		{
			$wallForm		= CWallLibrary::getWallInputForm( $video->id , 'videos,ajaxSaveWall', 'videos,ajaxRemoveWall' , $viewAllLink );
		}
		
		$redirectUrl	= CRoute::getURI( false );

		// Get like
		CFactory::load( 'libraries' , 'like' );
		$likes	    =	new CLike();
		$likesHTML  =	$likes->getHTML( 'videos', $video->id, $my->id );
		
		$tmpl	= new CTemplate();

		if( $video->creator_type == VIDEO_GROUP_TYPE )
		{
			$group =& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );

			$document   = JFactory::getDocument();
			$document->addHeadLink( $group->getThumbAvatar(), 'image_src', 'rel' );
		}
		
		if ($video->location !== '' && $videoMapsDefault == 1)
		{
			CFactory::load('libraries', 'mapping');
		    $zoomableMap = CMapping::drawZoomableMap($video->location, 220, 150);
		} else {
		    $zoomableMap = "";
		}
		
		echo $tmpl	->setMetaTags( 'video'	, $video )
					->set('user'		, $user )
					->set('zoomableMap'		, $zoomableMap )
					->set('likesHTML'		, $likesHTML )
					->set('redirectUrl'	, $redirectUrl )
					->set('wallForm' 		, $wallForm)
					->set('wallContent' 	, $wallContent)
					->set('bookmarksHTML'	, $bookmarksHTML)
					->set('reportHTML' 	, $reportHTML)
					->set('video' 		, $video)		
					->set('otherVideos' 		, $otherVideos)		
					->fetch('videos.video');
	}

	/**
	 *	Check if permitted to play the video
	 *	
	 *	@param	int		$myid		The current user's id
	 *	@param	int		$userid		The active profile user's id
	 *	@param	int		$permission	The video's permission
	 *	@return	bool	True if it's permitted
	 *	@since	1.2
	 */
	public function isPermitted($myid=0, $userid=0, $permissions=0)
	{
		CFactory::load('libraries', 'privacy');
		return CPrivacy::isAccessAllowed($myid, $userid, 'custom', $permissions);
	}

	public function _addSubmenu()
	{
		$my		=   CFactory::getUser();
		$config		=&  CFactory::getConfig();
		$task		=   JRequest::getVar( 'task' , '' , 'REQUEST' );
		$groupId	=   JRequest::getVar('groupid' , '' , 'GET');
		
		$group		=   JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $groupId );
		$isBanned	=   $group->isBanned( $my->id );
		
		if( !empty($groupId) )
		{
			$this->addSubmenuItem( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId, JText::_('COM_COMMUNITY_GROUPS_BACK_TO_GROUP'));

			$videos			= $this->model->hasPendingVideos( $groupId , VIDEO_GROUP_TYPE );
			
			if ($videos)
			{
				$this->addSubmenuItem('index.php?option=com_community&view=videos&task=mypendingvideos&groupid=' . $groupId, JText::_('COM_COMMUNITY_VIDEOS_GROUP_PENDING') , '' , SUBMENU_LEFT );
			}
			
			CFactory::load( 'helpers' , 'group' );			
			$allowManageVideos = CGroupHelper::allowManageVideo($groupId);				
						
			if( $allowManageVideos && !$isBanned )
			{
				$this->addSubmenuItem( '', JText::_('COM_COMMUNITY_ADD'), 'joms.videos.addVideo(\''.VIDEO_GROUP_TYPE.'\', \''.$groupId.'\')', SUBMENU_RIGHT);
			}
		}
		else
		{
			$this->addSubmenuItem('index.php?option=com_community&view=videos&task=display', JText::_('COM_COMMUNITY_VIDEOS_ALL_DESC'), '', SUBMENU_LEFT);
				
			if(!empty($my->id))
			{
				$this->addSubmenuItem('index.php?option=com_community&view=videos&task=myvideos&userid=' . $my->id, JText::_('COM_COMMUNITY_VIDEOS_MY'), '', SUBMENU_LEFT);
				$this->addSubmenuItem( '' , JText::_('COM_COMMUNITY_ADD'), 'joms.videos.addVideo()', SUBMENU_RIGHT);
			}

			if( (!$config->get('enableguestsearchvideos') && COwnerHelper::isRegisteredUser() ) || $config->get('enableguestsearchvideos') )
			{
				$tmpl = new CTemplate();
				$tmpl->set( 'url', CRoute::_('index.php?option=com_community&view=videos&task=search') );
				$html = $tmpl->fetch( 'videos.search.submenu' );
				$this->addSubmenuItem('index.php?option=com_community&view=videos&task=search', JText::_('COM_COMMUNITY_SEARCH'), 'joms.videos.toggleSearchSubmenu(this)', SUBMENU_LEFT, $html);
			}

			$videos			= $this->model->hasPendingVideos( $my->id , VIDEO_USER_TYPE );			

			if (!empty($my->id) && $videos )
			{
				$this->addSubmenuItem('index.php?option=com_community&view=videos&task=mypendingvideos&userid=' . $my->id, JText::_('COM_COMMUNITY_VIDEOS_PENDING') , '' , SUBMENU_LEFT );
			}
		}
	}
}
