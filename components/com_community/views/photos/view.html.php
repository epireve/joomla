<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CommunityViewPhotos extends CommunityView
{
        public function regen()
        {
            $doucment = JFactory::getDocument();
            $doucment->setTitle(JText::_('Running the Utility'));

            $tmpl = new CTemplate();

            echo $tmpl->fetch( 'photos.regen' );
        }
	public function _addSubmenu()
	{
		$handler	= $this->_getHandler();
		$handler->setSubmenus();
	}

	public function _flashuploader()
	{
		$groupId	= JRequest::getInt( 'groupid' , '' , 'REQUEST' );
		$model		= CFactory::getModel( 'photos' );

		// Since upload will always be the browser's photos, use the browsers id
		$my			= CFactory::getUser();

		// Maintenance mode, clear out tokens that are older than 1 hours
		$model->cleanUpTokens();
		$token		= $model->getUserUploadToken( $my->id );

		// We need to generate our own session management since there
		// are some bridges causes the flash browser to not really work.
		if( !$token && $my->id != 0 )
		{
			// Get the current browsers session object.
			$mySession	=& JFactory::getSession();

			// Generate a session handler for this user.
			$myToken	= $mySession->getToken( true );
			
			$date		=& JFactory::getDate();
			$token				= new stdClass();
			$token->userid		= $my->id;
			$token->datetime	= $date->toMySQL();
			$token->token		= $myToken;
			
			$model->addUserUploadSession( $token );
		}
		
		$config			= CFactory::getConfig();
		$albumId		= JRequest::getVar( 'albumid' , '' , 'REQUEST' );
		$handler		= $this->_getHandler();
		$uploadURI		= $handler->getFlashUploadURI( $token , $albumId );

		$albums				= '';
		$createAlbumLink	= '';
		$photoUploaded		= '';
		$photoUploadLimit	= '';
		$viewAlbumLink		= '';

		if(!empty($groupId) )
		{
			CFactory::load( 'models' , 'groups' );
			$group				=& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );
			$albums				= $model->getGroupAlbums( $groupId , false , false , '', ( $group->isAdmin( $my->id )  || COwnerHelper::isCommunityAdmin() )  );
			$createAlbumLink	= CRoute::_('index.php?option=com_community&view=photos&task=newalbum&groupid=' . $groupId );
			$photoUploaded		= $model->getPhotosCount( $groupId , PHOTOS_GROUP_TYPE );
			$photoUploadLimit	= $config->get('groupphotouploadlimit');
			$viewAlbumLink		= CRoute::_('index.php?option=com_community&view=photos&task=album&groupid=' . $groupId . '&albumid=' . $albumId);
		}
		else
		{
			$albums				= $model->getAlbums( $my->id );
			$createAlbumLink	= CRoute::_('index.php?option=com_community&view=photos&task=newalbum&userid=' . $my->id );
			$photoUploaded		= $model->getPhotosCount( $my->id , PHOTOS_USER_TYPE );
			$photoUploadLimit	= $config->get('photouploadlimit');
			$viewAlbumLink		= CRoute::_('index.php?option=com_community&view=photos&task=album&userid=' . $my->id . '&albumid=' . $albumId);
		}

		$tmpl				= new CTemplate();

		echo $tmpl	->set('createAlbumLink', $createAlbumLink )
					->set('albums'			, $albums )
					->set( 'uploadURI'		, $uploadURI )
					->set('albumId' 		, $albumId)
					->set('uploadLimit'	, $config->get('maxuploadsize') )
					->set('photoUploaded'	, $photoUploaded )
					->set('viewAlbumLink'	, $viewAlbumLink )
					->set('photoUploadLimit' , $photoUploadLimit )
					->fetch( 'photos.flashuploader' );
	}
	
	/**
	 * Display the multi upload form
	 **/
	public function _htmluploader()
	{	
		$groupId	= JRequest::getInt( 'groupid' , '' , 'REQUEST' );
		$model		= CFactory::getModel( 'photos' );
		$my			= CFactory::getUser();
		$config		= CFactory::getConfig();
		$albumId	= JRequest::getInt( 'albumid' , '' , 'REQUEST' );
		
		if(!empty($groupId) )
		{
			CFactory::load( 'models' , 'groups' );
			
			$group				=& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );
			$albums				= $model->getGroupAlbums( $groupId , false , false , '', ( $group->isAdmin( $my->id )  || COwnerHelper::isCommunityAdmin() ) );
			$createAlbumLink	= CRoute::_('index.php?option=com_community&view=photos&task=newalbum&groupid=' . $groupId );
			$photoUploaded		= $model->getPhotosCount( $groupId , PHOTOS_GROUP_TYPE );
			$photoUploadLimit	= $config->get('groupphotouploadlimit');
			$viewAlbumLink		= CRoute::_('index.php?option=com_community&view=photos&task=album&groupid=' . $groupId . '&albumid=' . $albumId);
		}
		else
		{
			$albums				= $model->getAlbums( $my->id );
			if (empty($albumId) && !empty($albums) && !empty($albums[0]->id))
			{
				$albumId		= $albums[0]->id;
			}
			$createAlbumLink	= CRoute::_('index.php?option=com_community&view=photos&task=newalbum&userid=' . $my->id );
			$photoUploaded		= $model->getPhotosCount( $my->id , PHOTOS_USER_TYPE );
			$photoUploadLimit	= $config->get('photouploadlimit');
			$viewAlbumLink		= CRoute::_('index.php?option=com_community&view=photos&task=album&userid=' . $my->id . '&albumid=' . $albumId);
		}

		// Attach the photo upload css.
		// CTemplate::addStylesheet( 'photouploader' );
		CFactory::load('helpers' , 'string' );
		
		$tmpl			= new CTemplate();
		echo $tmpl	->set('createAlbumLink', $createAlbumLink )
					->set('albums'			, $albums )
					->set( 'my'			, CFactory::getUser() )
					->set('albumId' 		, $albumId)
					->set('photoUploaded'	, $photoUploaded )
					->set('viewAlbumLink'	, $viewAlbumLink )
					->set('photoUploadLimit' , $photoUploadLimit )
					->set('uploadLimit'	, $config->get('maxuploadsize') )
					->fetch( 'photos.htmluploader' );
	}
	
	public function showSubmenu()
	{
		$this->_addSubmenu();
		parent::showSubmenu();
	}

	/**
	 * Default view method
	 * Display all photos in the whole system
	 **/
	public function display()
	{
		$document	= JFactory::getDocument();
		$my			= CFactory::getUser();
		$document->setTitle( JText::_('COM_COMMUNITY_PHOTOS_ALL_PHOTOS_TITLE') );
		$mainframe	=& JFactory::getApplication();

		// Set pathway for group photos
		// Community > Groups > Group Name > Photos
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

			$pathway =& $mainframe->getPathway();
			$pathway->addItem(JText::_('COM_COMMUNITY_GROUPS'), CRoute::_('index.php?option=com_community&view=groups'));
			$pathway->addItem($group->name, CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId));
		}
		$this->addPathway( JText::_('COM_COMMUNITY_PHOTOS_ALL_PHOTOS_TITLE') );

		// Load tooltips lib
		CFactory::load( 'libraries' , 'tooltip' );
		CFactory::load( 'models', 'groups' );
		
 		$model		= CFactory::getModel( 'photos' );
 		$groupId	= JRequest::getInt( 'groupid' , '' , 'REQUEST' );
 		$limitstart	= JRequest::getInt( 'limitstart' , 0 );
		$type		= PHOTOS_USER_TYPE;
		
		$handler	= $this->_getHandler();

		$this->showSubmenu();

		$groupLink = !empty($groupId) ? '&groupid=' . $groupId : '';
		$feedLink = CRoute::_('index.php?option=com_community&view=photos' . $groupLink . '&format=feed');
		$feed = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_PHOTOS_ALL_PHOTOS_FEED') . '" href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );

		$albumsData = $handler->getAllAlbumData();
		if($albumsData === FALSE)
		{
			return;
		}
		
		//$albumHTML = $this->_getAllAlbumsHTML($albumsData , $type, $model->getPagination() );
		$albumHTML = $this->_getAllAlbumsHTML($albumsData['data'] , $type, $albumsData['pagination'] );
		
		$featuredList	= array();
		if(empty($groupId))
		{
				$cache = CFactory::getCache('Core');
				
				if (!($featuredList = $cache->load('_photos_featuredList'))){
					CFactory::load( 'libraries' , 'featured' );
					$featured		= new CFeatured( FEATURED_ALBUMS );
					$featuredAlbums	= $featured->getItemIds();
					foreach($featuredAlbums as $album )
					{
						$table			=& JTable::getInstance( 'Album' , 'CTable' );
						$table->load($album);
			
						$table->thumbnail	= $table->getCoverThumbPath();
						$table->thumbnail	= ($table->thumbnail) ? JURI::root() . $table->thumbnail : JURI::root() . 'components/com_community/assets/album_thumb.jpg';
						$featuredList[]	= $table;
					}
					
					$cache->save($featuredList, NULL, array(COMMUNITY_CACHE_TAG_PHOTOS, COMMUNITY_CACHE_TAG_ALBUMS));
				}
		}
		
		$tmpl	= new CTemplate();
		CFactory::load( 'helpers' , 'owner' );

		echo $tmpl	->set( 'isCommunityAdmin' , COwnerHelper::isCommunityAdmin() )
					->set( 'albumsHTML'	, $albumHTML)
					->set( 'groupId'	, $groupId)
					->fetch( 'photos.index' );
	}
	
	/*
	 * @since 2.4
	 */
	public function modFeaturedAlbum(){
		return $this->_getFeaturedAlbum();
	}
	
	/*
	 * @since 2.4
	 */
	private function _getFeaturedAlbum(){
		$featuredList	= array();
		$cache = CFactory::getCache('Core');
		$config		= CFactory::getConfig();

		if (!($featuredList = $cache->load('_photos_featuredList'))){
			CFactory::load( 'libraries' , 'featured' );
			$featured		= new CFeatured( FEATURED_ALBUMS );
			$featuredAlbums	= $featured->getItemIds();
			foreach($featuredAlbums as $album ){
				$table			=& JTable::getInstance( 'Album' , 'CTable' );
				$table->load($album);

				$table->thumbnail	= $table->getCoverThumbPath();
				$table->thumbnail	= ($table->thumbnail) ? JURI::root() . $table->thumbnail : JURI::root() . 'components/com_community/assets/album_thumb.jpg';
					
				if ($table->location != '')
				{
					CFactory::load('libraries', 'mapping');
					$zoomableMap = CMapping::drawZoomableMap($table->location, 220, 150);
				} 
				else 
				{
					$zoomableMap = "";
				}
				
				$table->zoomableMap = $zoomableMap;
				
				$featuredList[]	= $table;
			}

			$cache->save($featuredList, NULL, array(COMMUNITY_CACHE_TAG_PHOTOS, COMMUNITY_CACHE_TAG_ALBUMS));
		}
		
		$tmpl	= new CTemplate();
		CFactory::load( 'helpers' , 'owner' );
		
		$photoTag = CFactory::getModel('phototagging');
		
		//add photos info in featured list
		//$handler	= $this->_getHandler();
		$photoModel	= CFactory::getModel( 'photos' );
		if (is_array($featuredList))
		{
			foreach($featuredList as &$fl){
				// bind photo links
				$fl->photos = $photoModel->getPhotos( $fl->id, 5); //set the photo limit to be configurable by admin
				$photos = &$fl->photos;
				$maxTime = '';
				
				$tagRecords = array();
				// Get all photos from album
				
				for( $i = 0; $i < count( $photos ); $i++ )
				{
					$item =& JTable::getInstance( 'Photo' , 'CTable' );
					$item->bind($photos[$i]);
					$photos[$i] = $item;

					$photo		 =& $photos[$i];
					$photo->link = CRoute::_('index.php?option=com_community&view=photos&task=photo&userid=' . $fl->creator . '&albumid=' . $photo->albumid ) . '#photoid=' . $photo->id;
					//Cannot use handler as Handler needs userid obtained from Get. In here, userid might not exists in $_GET
					//$handler->getPhotoURI( $photo->id , $photo->albumid );
					$tagRecords[] = $photoTag->getTaggedList( $photo->id );
					

					//Get last update			
					$maxTime = ($photo->created > $maxTime) ? $photo->created : $maxTime;
				}
				
				$people = array();
				// Get the people in the tags
				foreach($tagRecords as $tag)
				{
					foreach($tag as $t){
						$people[] = $t->userid;
					}
				}
		
				$people = array_unique($people);
				foreach($people as &$person)
				{
					$person = CFactory::getUser($person);
				}
				
				//bind tagged person in the album
				$fl -> tagged = $people;

				//bind album desc
				$maxTime = new JDate($maxTime);
				$fl->lastUpdated = CActivityStream::_createdLapse($maxTime, false);
			}
		}
		
		//try to get the photos within this album
		
		// Get show photo location map by default
		$photoMapsDefault	= $config->get('photosmapdefault');	

		return $tmpl -> set( 'isCommunityAdmin' , COwnerHelper::isCommunityAdmin() )
					 -> set( 'featuredList'	, $featuredList )
					 -> set( 'photoModel', $photoModel )
					 -> set( 'photoMapsDefault', $photoMapsDefault )
					 -> fetch( 'photos.album.featured' );
	}

	public function myphotos()
	{
		$my			= CFactory::getUser();
		$mainframe  = JFactory::getApplication();
		$document	= JFactory::getDocument();
		
        CFactory::load('helpers' , 'owner' );
        $userid		= JRequest::getInt( 'userid' , $my->id );
        
        if ($userid)
        {
        	$user		= CFactory::getUser($userid);
		} else {
			$user		= CFactory::getUser();
		}

		// set bread crumbs
		if($userid == $my->id){
		    $this->addPathway( JText::_( 'COM_COMMUNITY_PHOTOS' ) , CRoute::_('index.php?option=com_community&view=photos' ) );
		    $this->addPathway( JText::_('COM_COMMUNITY_PHOTOS_MY_PHOTOS_TITLE') );
		} else {
		    $this->addPathway( JText::_('COM_COMMUNITY_PHOTOS'), CRoute::_('index.php?option=com_community&view=photos'));
		    $this->addPathway( JText::sprintf('COM_COMMUNITY_PHOTOS_USER_PHOTOS_TITLE', $user->getDisplayName()), CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid=' . $userid ));
		}

		$blocked	= $user->isBlocked();
		CFactory::load('libraries','privacy');
		
		/*
		// privacyPhotoView is deprecated
		// we no longer check this value
		
		if( !CPrivacy::isAccessAllowed($my->id, $user->id, 'user', 'privacyPhotoView') )
		{
			//echo JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN');
			$this->noAccess();
			return;
		}
		*/
		
		if( $blocked && !COwnerHelper::isCommunityAdmin() )
		{
			$tmpl	= new CTemplate();
			echo $tmpl->fetch('profile.blocked');
			return;
		}
				
		if($my->id == $user->id)
			$document->setTitle( JText::_('COM_COMMUNITY_PHOTOS_MY_PHOTOS_TITLE') );
		else
			$document->setTitle( JText::sprintf('COM_COMMUNITY_PHOTOS_USER_PHOTOS_TITLE', $user->getDisplayName()) );
		
		
		// Show the mini header when viewing other's photos
		if($my->id != $user->id)
			$this->attachMiniHeaderUser($user->id);

		$this->showSubmenu();

		$feedLink = CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid=' . $user->id . '&format=feed');
		$feed = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_SUBSCRIBE_MY_PHOTOS_FEED') . '" href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );

 		$model	= CFactory::getModel( 'photos' );

 		$albums		= $model->getAlbums( $user->id , true , true );
		
		// Load tooltips lib
		CFactory::load( 'libraries' , 'tooltip' );

		$tmpl	= new CTemplate();
		
		echo $tmpl	->set( 'albumsHTML'	, $this->_getAlbumsHTML($albums, PHOTOS_USER_TYPE, $model->getPagination()) )
					->fetch( 'photos.myphotos' );
	}

	public function _getAllAlbumsHTML( $albums , $type = PHOTOS_USER_TYPE, $pagination = NULL )
	{
		$my	    = CFactory::getUser();
		$config	    = CFactory::getConfig();
		$groupId    = JRequest::getInt( 'groupid' , '' ,'REQUEST');
		$handler    = $this->_getHandler();
		
		$tmpl		= new CTemplate();

		CFactory::load( 'libraries' , 'activities' );
		CFactory::load( 'models' , 'groups' );
		CFactory::load( 'helpers' , 'owner' );
		
		// Use for redirect after editAlbum
		$displaygrp = ($groupId == 0) ? 'display' : 'displaygrp';
		
		for($i = 0; $i < count($albums); $i++)
		{
			$type	= $albums[$i]->groupid > 0 ? PHOTOS_GROUP_TYPE : PHOTOS_USER_TYPE;
			
			$albums[$i]->user		= CFactory::getUser( $albums[$i]->creator );
			$albums[$i]->link 		= CRoute::_("index.php?option=com_community&view=photos&task=album&albumid={$albums[$i]->id}&userid={$albums[$i]->creator}");
			$albums[$i]->editLink 	= CRoute::_("index.php?option=com_community&view=photos&task=editAlbum&albumid={$albums[$i]->id}&userid={$albums[$i]->creator}&referrer=display");
			$albums[$i]->uploadLink = "javascript:joms.notifications.showUploadPhoto({$albums[$i]->id});"; //CRoute::_("index.php?option=com_community&view=photos&task=uploader&albumid={$albums[$i]->id}&userid={$albums[$i]->creator}");
			$albums[$i]->isOwner	= ($my->id == $albums[$i]->creator);

			if( $type == PHOTOS_GROUP_TYPE)
			{
				$albums[$i]->link 		= CRoute::_("index.php?option=com_community&view=photos&task=album&albumid={$albums[$i]->id}&groupid={$albums[$i]->groupid}");
				$albums[$i]->editLink	= CRoute::_("index.php?option=com_community&view=photos&task=editAlbum&albumid={$albums[$i]->id}&groupid={$albums[$i]->groupid}&referrer={$displaygrp}");
				$albums[$i]->uploadLink = "javascript:joms.notifications.showUploadPhoto({$albums[$i]->id},{$albums[$i]->groupid});"; //CRoute::_("index.php?option=com_community&view=photos&task=uploader&albumid={$albums[$i]->id}&groupid={$albums[$i]->groupid}");
				$albums[$i]->isOwner = $my->authorise('community.view','photos.group.album.'.$groupId, $albums[$i]);
			}

			// If new albums that has just been created and
			// does not contain any images, the lastupdated will always be 0000-00-00 00:00:00:00
			// Try to use the albums creation date instead.
			if( $albums[$i]->lastupdated == '0000-00-00 00:00:00' || $albums[$i]->lastupdated == '')
			{
				$albums[$i]->lastupdated	= $albums[$i]->created;

				if( $albums[$i]->lastupdated == '' || $albums[$i]->lastupdated == '0000-00-00 00:00:00')
				{
					$albums[$i]->lastupdated	= JText::_( 'COM_COMMUNITY_PHOTOS_NO_ACTIVITY' );
				}
				else
				{
					$lastUpdated	= new JDate( $albums[$i]->lastupdated );
					$albums[$i]->lastupdated	= CActivityStream::_createdLapse( $lastUpdated, false );
				}
			}
			else
			{
				$lastUpdated	= new JDate( $albums[$i]->lastupdated );
				$albums[$i]->lastupdated	= CActivityStream::_createdLapse( $lastUpdated, false );
			}

		}

		CFactory::load( 'helpers' , 'owner' );

		CFactory::load( 'libraries' , 'featured' );
		$featured	= new CFeatured( FEATURED_ALBUMS );
		$featuredList	= $featured->getItemIds();

		$createLink		= $handler->getAlbumCreateLink();

		if( $type == PHOTOS_GROUP_TYPE )
		{
			CFactory::load( 'helpers' , 'group' );
			
			$isOwner	= CGroupHelper::allowManagePhoto( $groupId );
		}
		else
		{
			$userId		= JRequest::getInt( 'userid' , '' , 'REQUEST' );
			$user		= CFactory::getUser( $userId );
			
			$isOwner		= ($my->id == $user->id) ? true : false;
		}
		
		$task	= JRequest::getCmd( 'task' , '');
		return $tmpl	->set( 'isMember'		, $my->id != 0 )
						->set( 'isOwner'		, $isOwner )
						->set( 'type'			, $type )
						->set( 'createLink'	, $createLink )
						->set( 'currentTask'	, $task )
						->set( 'showFeatured'		, $config->get('show_featured') )
						->set( 'featuredList'		, $featuredList )
						->set( 'isCommunityAdmin'	, COwnerHelper::isCommunityAdmin() )
						->set( 'my'		, $my )
						->set( 'albums' 	, $albums )
						->set( 'pagination', $pagination )
						->set( 'isSuperAdmin'	, COwnerHelper::isCommunityAdmin())
						->fetch( 'albums.list' );
	}

	public function _getAlbumsHTML( $albums , $type = PHOTOS_USER_TYPE, $pagination = NULL )
	{
		$my			= CFactory::getUser();
		$groupId	= JRequest::getVar( 'groupid' , '' ,'REQUEST');

		$tmpl		= new CTemplate();

		CFactory::load( 'libraries' , 'activities' );
		CFactory::load( 'models' , 'groups' );
		CFactory::load( 'helpers' , 'owner' );
		
		$handler	= $this->_getHandler();
		$photoTag = CFactory::getModel('phototagging');
		$photos		= array();
		
		foreach( $albums as &$album )
		{
			$album->user		= CFactory::getUser( $album->creator );
			$album->link 		= CRoute::_("index.php?option=com_community&view=photos&task=album&albumid={$album->id}&userid={$album->creator}");
			$album->editLink 	= CRoute::_("index.php?option=com_community&view=photos&task=editAlbum&albumid={$album->id}&userid={$album->creator}&referrer=myphotos");
			$album->uploadLink	= "javascript:joms.notifications.showUploadPhoto({$album->id});"; //CRoute::_("index.php?option=com_community&view=photos&task=uploader&albumid={$album->id}&userid={$album->creator}");
			$album->isOwner		= ($my->id == $album->creator);
			
			// Get all photos from album
			$photos[$album->id] = $handler->getAlbumPhotos($album->id);
			$photoCount			= count($photos[$album->id]);
			$tagRecords[$album->id] = array();
			
			// get tag users from photos
			if ($photoCount > 0)
			{
				for ($i = 0; $i < $photoCount; $i++)
				{
					$tempTagRecords = $photoTag->getTaggedList( $photos[$album->id][$i]->id );
					
					if (count($tempTagRecords) > 0)
					{
						$tagRecords[$album->id][] = $tempTagRecords;
					}
				}
			}
			
			if( $type == PHOTOS_GROUP_TYPE)
			{
				$album->link 		= CRoute::_("index.php?option=com_community&view=photos&task=album&albumid={$album->id}&groupid={$album->groupid}");
				$album->editLink	= CRoute::_("index.php?option=com_community&view=photos&task=editAlbum&albumid={$album->id}&groupid={$album->groupid}&referrer=myphotos");
				$album->uploadLink = CRoute::_("index.php?option=com_community&view=photos&task=uploader&albumid={$album->id}&groupid={$album->groupid}");
				$albums[$i]->isOwner = $my->authorise('community.view','photos.group.album.'.$groupId, $albums[$i]);
			}

			// If new albums that has just been created and
			// does not contain any images, the lastupdated will always be 0000-00-00 00:00:00:00
			// Try to use the albums creation date instead.
			if( $album->lastupdated == '0000-00-00 00:00:00' || $album->lastupdated == '')
			{
				$album->lastupdated	= $album->created;

				if( $album->lastupdated == '' || $album->lastupdated == '0000-00-00 00:00:00')
				{
					$album->lastupdated	= JText::_( 'COM_COMMUNITY_PHOTOS_NO_ACTIVITY' );
				}
				else
				{
					$lastUpdated	= new JDate( $album->lastupdated );
					$album->lastupdated	= CActivityStream::_createdLapse( $lastUpdated, false );
				}
			}
			else
			{
				$lastUpdated	= new JDate( $album->lastupdated );
				$album->lastupdated	= CActivityStream::_createdLapse( $lastUpdated, false );
			}

		}
		CFactory::load( 'helpers' , 'owner' );

		$createLink		= CRoute::_('index.php?option=com_community&view=photos&task=newalbum&userid=' . $my->id );

		if( $type == PHOTOS_GROUP_TYPE )
		{
			$createLink	= CRoute::_('index.php?option=com_community&view=photos&task=newalbum&groupid=' . $groupId );
			
			CFactory::load( 'helpers' , 'group' );
			
			$isOwner	= CGroupHelper::allowManagePhoto( $groupId );
		}
		else
		{
			$userId		= JRequest::getInt( 'userid' , '' , 'REQUEST' );
			$user		= CFactory::getUser( $userId );
			
			$isOwner		= ($my->id == $user->id) ? true : false;
		}

		CFactory::load( 'libraries' , 'featured' );
		$featured	= new CFeatured( FEATURED_ALBUMS );
		$featuredList	= $featured->getItemIds();
		
		$task	= JRequest::getCmd( 'task' , '');
		return $tmpl->set( 'isMember'		, $my->id != 0 )
					->set( 'isOwner'		, $isOwner )
					->set( 'type'			, $type )
					->set( 'createLink'	, $createLink )
					->set( 'currentTask'	, $task )
					->set( 'isCommunityAdmin'	, COwnerHelper::isCommunityAdmin() )
					->set( 'my'		, $my )
					->set( 'albums' 	, $albums )
					->set( 'pagination', $pagination )
					->set( 'isSuperAdmin'	, COwnerHelper::isCommunityAdmin())
					->set( 'featuredList'		, $featuredList )
					->fetch( 'albums.list' );
	}
	
	/**
	 * Displays edit album form
	 **/
	public function editAlbum( $bolSaveSuccess = true )
	{
		$document	= JFactory::getDocument();
		$config		= CFactory::getConfig();

		
		// Load necessary libraries, models
		CFactory::load( 'models' , 'photos' );
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$albumId	= JRequest::getInt( 'albumid' , '' , 'GET' );
		$referrer	= JRequest::getVar( 'referrer' , '' , 'GET' );
		$type		= JRequest::getInt( 'groupid' , '' , 'REQUEST' );
		$type		= !empty($type) ? PHOTOS_GROUP_TYPE : PHOTOS_USER_TYPE;
		$album->load( $albumId );
		$permissions = $album->permissions ? $album->permissions : JRequest::getVar('permissions', '', 'POST');
		
		// Added to maintain user input value if there is save error 
		if ($bolSaveSuccess === false )
		{
			$album->name 		= JRequest::getVar('name', '', 'POST');
			$album->location	= JRequest::getVar('location', '', 'POST');
			$album->description	= JRequest::getVar('description', '', 'POST');
			$album->permissions	= JRequest::getVar('permissions', '', 'POST');
			$album->type		= JRequest::getVar('type', '', 'POST');
		}
		
		$this->addPathway( JText::sprintf('COM_COMMUNITY_PHOTOS_EDIT_ALBUM_TITLE', $album->name ) );
		$this->showSubmenu();
		
		if( $album->id == 0 )
		{
			echo JText::_('COM_COMMUNITY_PHOTOS_INVALID_ALBUM');
			return;
		}

		$document->setTitle( JText::sprintf('COM_COMMUNITY_PHOTOS_EDIT_ALBUM_TITLE', $album->name ) );
        
		$js	= 'assets/validate-1.5'.(( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js');
		CAssets::attach($js, 'js');

		$app 		=& CAppPlugins::getInstance();
		$appFields	= $app->triggerEvent('onFormDisplay' , array('jsform-photos-newalbum'));
		$beforeFormDisplay	= CFormElement::renderElements( $appFields , 'before' );
		$afterFormDisplay	= CFormElement::renderElements( $appFields , 'after' );

		CFactory::load( 'libraries' , 'privacy' );
		
		$tmpl	= new CTemplate();
		echo $tmpl 	->set( 'album'	, $album )
					->set(	'type' , $type )
					->set(	'referrer' , $referrer )
					->set(	'permissions', $permissions)
					->set( 'beforeFormDisplay', $beforeFormDisplay )
					->set( 'afterFormDisplay'	, $afterFormDisplay )
					->set( 'enableLocation',	$config->get('enable_photos_location') )
					->fetch( 'photos.editalbum' );
	}

	/**
	 * Display the new album form
	 **/
	public function newalbum()
	{
		$config		= CFactory::getConfig();	
		$document 	= JFactory::getDocument();

		$document->setTitle( JText::_('COM_COMMUNITY_PHOTOS_CREATE_NEW_ALBUM_TITLE') );
		$this->addPathway( JText::_( 'COM_COMMUNITY_PHOTOS' ) , CRoute::_('index.php?option=com_community&view=photos' ) );
		$this->addPathway( JText::_('COM_COMMUNITY_PHOTOS_CREATE_NEW_ALBUM_TITLE') );
		
		$js	= 'assets/validate-1.5'.(( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js');
		CAssets::attach($js, 'js');
        
		$handler	= $this->_getHandler();
		$type	= $handler->getType();
		
		$user 	 = CFactory::getRequestUser();
		$params  = $user->getParams();

		$this->showSubmenu();
		
		$album	=& JTable::getInstance( 'Album' , 'CTable' );		
		
		// Added to maintain user input value if there is save error 
		$album->name 		= JRequest::getVar('name', '', 'POST');
		$album->location	= JRequest::getVar('location', '', 'POST');
		$album->description	= JRequest::getVar('description', '', 'POST');
		$album->permissions	= JRequest::getVar('permissions', $params->get( 'privacyPhotoView' ), 'POST');
		$album->type		= JRequest::getVar('type', '', 'POST');
		
		CFactory::load( 'libraries' , 'apps' );
		$app 		=& CAppPlugins::getInstance();
		$appFields	= $app->triggerEvent('onFormDisplay' , array('jsform-photos-newalbum'));
		$beforeFormDisplay	= CFormElement::renderElements( $appFields , 'before' );
		$afterFormDisplay	= CFormElement::renderElements( $appFields , 'after' );

		CFactory::load('libraries', 'privacy');

		$tmpl	= new CTemplate();
		echo $tmpl	->set( 'beforeFormDisplay', $beforeFormDisplay )
					->set( 'afterFormDisplay'	, $afterFormDisplay )
					->set( 'permissions' , $album->permissions )
					->set( 'type' , $type )
					->set( 'album'	, $album )
					->set( 'referrer'	, '' )
					->set( 'enableLocation',	$config->get('enable_photos_location') )	
					->fetch( 'photos.editalbum' );
	}

	public function uploader()
	{
		$document = JFactory::getDocument();		
		$handler	= $this->_getHandler();
		$albumId	= JRequest::getInt( 'albumid' , -1 );
		$my			= CFactory::getUser();

		$document->setTitle(JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_MULTIPLE_PHOTOS_TITLE'));
		$this->addPathway( JText::_( 'COM_COMMUNITY_PHOTOS' ) , CRoute::_('index.php?option=com_community&view=photos' ) );
		
		if( $albumId != -1 )
		{
			$album	=& JTable::getInstance( 'Album' , 'CTable' );
			$album->load( $albumId );

			$this->addPathway( $album->name , $handler->getAlbumURI( $album->id ) );
		}
		$this->addPathway( JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_MULTIPLE_PHOTOS_TITLE') );

		$css		= rtrim( JURI::root() , '/' ) . '/components/com_community/assets/uploader/style.css';
		$document->addStyleSheet($css);
		
		// Display submenu on the page.
		$this->showSubmenu();
		
		// Add create album link
		$groupId	= JRequest::getVar( 'groupid' , '' , 'REQUEST' );
		$type		= PHOTOS_USER_TYPE;	

		// Get the configuration for uploader tool
		$config		= CFactory::getConfig();
		$groupId	= JRequest::getVar( 'groupid' , '' , 'REQUEST' );

		CFactory::load( 'helpers' , 'limits' );
		
		CFactory::load( 'helpers' , 'owner' );
		
		if($handler->isExceedUploadLimit() && !CownerHelper::isCommunityAdmin() ) 
		{
			return;
		}

		/*$useFlash	= $config->get( 'flashuploader' );

		if( $useFlash )
		{
			echo $this->_flashuploader();
		}
		else
		{*/
			echo $this->_htmluploader();
		//}
		
	}

	/**
	 * Display the photo thumbnails from an album
	 **/
	public function album()
	{
		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();
		$config		= CFactory::getConfig();
		$handler	= $this->_getHandler();
		$my			= CFactory::getUser();
		CFactory::load( 'libraries' , 'activities' );

		// Get show photo location map by default
		$photoMapsDefault	= $config->get('photosmapdefault');

		$albumId	= JRequest::getVar('albumid' , '' , 'GET');
 		$defaultId	= JRequest::getVar('photo' , '' , 'GET');
		$userId	= JRequest::getVar('userid' , '' , 'GET');
		$user = CFactory::getUser($userId);
		// Set pathway for group photos
		// Community > Groups > Group Name > Photos > Album Name
		$pathway =& $mainframe->getPathway();
		$groupId    = JRequest::getVar('groupid','', 'GET');
		if (!empty($groupId))
		{
			$group =& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );

			$pathway->addItem(JText::_('COM_COMMUNITY_GROUPS'), CRoute::_('index.php?option=com_community&view=groups'));
			$pathway->addItem($group->name, CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId));
			$pathway->addItem(JText::_('COM_COMMUNITY_PHOTOS'), CRoute::_('index.php?option=com_community&view=photos&groupid=' . $groupId));
		} else {
			$pathway->addItem( JText::_( 'COM_COMMUNITY_PHOTOS' ) , CRoute::_('index.php?option=com_community&view=photos' ) );
			$pathway->addItem( JText::sprintf('COM_COMMUNITY_PHOTOS_USER_PHOTOS_TITLE', $user->getDisplayName()), CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid=' . $userId ));
		}
		
		$handler->setMiniHeader();
		
 		if( empty( $albumId ) )
 		{
 			echo JText::_('COM_COMMUNITY_PHOTOS_NO_ALBUMID_ERROR');
 			return;
		}
		
		if( !$handler->isAlbumBrowsable( $albumId ) )
		{
			return;
		}
		
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );	
		
		//$photos		= $handler->getAlbumPhotos( $album->id );
		$photoPaginationLimit	= intval($config->get('photopaginationlimit'));
		$photoThumbLimit = $photoPaginationLimit;
		$model	= CFactory::getModel( 'photos' );
		$photos		= $model->getPhotos( $album->id, $photoThumbLimit, JRequest::getVar('limitstart' , '0' , 'GET') );

		$pagination = $model->getPagination();
		
		$photo	=& JTable::getInstance( 'Photo' , 'CTable' );
		$photo->load($album->photoid);
		if ($album->photoid == '0')
		{
			$album->thumbnail = $photo->getThumbURI();
		}
		else
		{			
			$album->thumbnail = $photo->getImageURI();
		}

		// Increment album's hit each time this page is loaded.
		$album->hit();
		
		
		$otherAlbums	= $model->_getOnlyAlbums( $user->id , PHOTOS_USER_TYPE, 0, 20 );
		$totalAlbums	= count($otherAlbums);
		$showOtherAlbum = 6;
		$randomAlbum = array();
		if (count($otherAlbums) > 0)
		{
			$randomId = ($totalAlbums < $showOtherAlbum) ? array_rand( $otherAlbums, $totalAlbums ) : array_rand( $otherAlbums, $showOtherAlbum );
		
			$count = 0;
			for ($i = 0; $i < $totalAlbums; $i++) {
				$num = (is_array($randomId)) ? $randomId[$i] : $randomId;
				if ($otherAlbums[$num]->id != $album->id) 
				{ 
					$count++;
					$randomAlbum[] = $otherAlbums[$num];
				} 
				if (count($randomAlbum) == ($showOtherAlbum - 1))
				{
					break;
				}
			}
		}

		$js = 'assets/gallery';
		$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
		CAssets::attach($js, 'js');
		
		CFactory::load( 'helpers' , 'string' );
		$document->setTitle( JText::sprintf( 'COM_COMMUNITY_PHOTOS_USER_PHOTOS_TITLE' ,  $handler->getCreatorName() ) .' - '. $album->name );
		$this->setTitle( $album->name );
		$handler->setAlbumPathway( CStringHelper::escape($album->name) );
		$handler->setRSSHeader( $albumId );
		
		// Set album thumbnail and description for social bookmarking sites linking
		$document->addHeadLink($album->getCoverThumbURI(), 'image_src', 'rel');
		$document->setDescription( CStringHelper::escape($album->getDescription()) );
		
		CFactory::load( 'libraries' , 'phototagging' );
		$tagging = new CPhotoTagging();
		$people = array();

		$maxTime = $album->created;
		// Need to append the absolute path for the captions
		for( $i = 0; $i < count( $photos ); $i++ )
		{
			$item =& JTable::getInstance( 'Photo' , 'CTable' );
			$item->bind($photos[$i]);
			$photos[$i] = $item;
			
			$photo		 =& $photos[$i];
			$photo->link = $handler->getPhotoURI( $photo->id , $photo->albumid );

			$tags = $tagging->getTaggedList($photo->id);
						
			//Get last update			
			$maxTime = ($photo->created > $maxTime) ? $photo->created : $maxTime;

			// Get the people in the tags
			foreach($tags as $tag)
			{
				$people[] = $tag->userid;
			}
		}
				
		//Update lastUpdated
		$maxTime = new JDate($maxTime);
		$album->lastUpdated = CActivityStream::_createdLapse($maxTime, false);

		$people = array_unique($people);
		foreach($people as &$person)
		{
			$person = CFactory::getUser($person);
		}

		CFactory::load( 'libraries' , 'bookmarks' );
		$bookmarks	= new CBookmarks( $handler->getAlbumExternalURI( $album->id ) );

		// Get the walls
		CFactory::load( 'libraries' , 'wall' );
		$wallContent	= CWallLibrary::getWallContents( 'albums' , $album->id , ( COwnerHelper::isCommunityAdmin() || ($my->id == $album->creator && ($my->id != 0)) ) , 10 ,0);
		$wallCount		= CWallLibrary::getWallCount('albums', $album->id);
		$viewAllLink = false;
		if(JRequest::getVar('task', '', 'REQUEST') != 'app')
		{
			$viewAllLink	= CRoute::_('index.php?option=com_community&view=photos&task=app&albumid=' . $album->id . '&app=walls');
		}
		$wallContent	.= CWallLibrary::getViewAllLinkHTML($viewAllLink, $wallCount);

		$wallForm		= CWallLibrary::getWallInputForm( $album->id , 'photos,ajaxAlbumSaveWall', 'photos,ajaxAlbumRemoveWall' , $viewAllLink );
		$redirectUrl	= CRoute::getURI( false );
		// Add tagging code
//		$tagsHTML = '';
//		if($config->get('tags_photos')){
//			CFactory::load('libraries', 'tags');
//			$tags = new CTags();
//			$tagsHTML = $tags->getHTML('albums', $album->id, $handler->isAlbumOwner( $album->id ) );
//		}

		$this->showSubmenu();
		$tmpl	= new CTemplate();
		if($album->location!=""){
		CFactory::load('libraries', 'mapping');
		    $zoomableMap = CMapping::drawZoomableMap($album->location, 220, 150);
		} else {
		    $zoomableMap = "";
		}
		// Get the likes / dislikes item
		CFactory::load( 'libraries' , 'like' );
		$like 		= new CLike();
		$likesHTML	= $like->getHTML( 'album', $album->id, $my->id );
		
		$owner = CFactory::getUser($album->creator);
		
		echo $tmpl		->set( 'likesHTML'	, $likesHTML )
					->set( 'photosmapdefault'   , $photoMapsDefault )
					->set( 'my'				, $my )
					->set( 'bookmarksHTML'	, $bookmarks->getHTML() )
					->set( 'isOwner' 		, $handler->isAlbumOwner( $album->id ) )
					->set( 'isAdmin'		, COwnerHelper::isCommunityAdmin() )
					->set( 'owner'			, $owner )
					->set( 'photos' 		, $photos )
					->set( 'people'			, $people )
					->set( 'album'			, $album)
					->set( 'otherAlbums'	, $randomAlbum)
					->set( 'likesHTML'		, $likesHTML)
					->set('wallForm' 		, $wallForm)
					->set('wallContent' 	, $wallContent)
					->set('zoomableMap' 	, $zoomableMap)
					->set('pagination' 	, $pagination)
					->fetch('photos.album');
	}
	
	/**
	 * Displays single photo view
	 *
	 **/
	public function photo()
	{
		$mainframe	=& JFactory::getApplication();
		$document	= JFactory::getDocument();
		$my		= CFactory::getUser();
		$config		= CFactory::getConfig();
		
		// Load window library
		CFactory::load( 'libraries' , 'window' );		
		CWindow::load();
		
		// Get the configuration object.
		$config	= CFactory::getConfig();

		$css	= JURI::root() . 'components/com_community/assets/album.css';
		$document->addStyleSheet($css);
		$css	= JURI::root() . 'components/com_community/assets/photos.css';
		$document->addStyleSheet($css);
		
		$js = 'assets/gallery';
		$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
		CAssets::attach($js, 'js');

 		$albumId	= JRequest::getVar('albumid' , '' , 'GET');
		$defaultId	= JRequest::getVar('photoid' , '' , 'GET');
		$userId	= JRequest::getVar('userid' , '' , 'GET');
		$user = CFactory::getUser($userId);
		
 		$handler	= $this->_getHandler();
 		$handler->setMiniHeader();
 		
 		if( empty( $albumId ) )
 		{
 			echo JText::_('COM_COMMUNITY_PHOTOS_NO_ALBUMID_ERROR');
 			return;
		}
		
		CFactory::load( 'models' , 'photos' );
		CFactory::load('helpers', 'friends');

		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );

		// Set pathway
		$pathway 	=& $mainframe->getPathway();

		// Set pathway for group photos
		// Community > Groups > Group Name > Photos > Album Name
		$groupId    = JRequest::getVar('groupid','', 'GET');
		if (!empty($groupId))
		{
			$group =& JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $groupId );

			$pathway->addItem(JText::_('COM_COMMUNITY_GROUPS'), CRoute::_('index.php?option=com_community&view=groups'));
			$pathway->addItem($group->name, CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $groupId));
			$pathway->addItem(JText::_('COM_COMMUNITY_PHOTOS'), CRoute::_('index.php?option=com_community&view=photos&groupid=' . $groupId));
		} else {
			$pathway->addItem(JText::_('COM_COMMUNITY_PHOTOS'), CRoute::_('index.php?option=com_community&view=photos'));
			$pathway->addItem( JText::sprintf('COM_COMMUNITY_PHOTOS_USER_PHOTOS_TITLE', $user->getDisplayName()), CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid=' . $userId ));
		}
		$pathway->addItem( $album->name , '' );
		
		// Set document title
		CFactory::load( 'helpers' , 'string' );
		$document->setTitle( $album->name );
		
		if( !$handler->isAlbumBrowsable( $albumId ) )
		{
			return;
		}
		
		$model	=&  CFactory::getModel('photos');
		$photos	=   $model->getPhotos( $albumId, 1000);

		// @checks: Test if album doesnt have any default photo id. We need to get the first row
		// of the photos to be the default
		if($album->photoid == '0')
		{
			$album->photoid	= ( count( $photos ) >= 1 ) ? $photos[0]->id : '0';
		}

		// Try to see if there is any photo id in the query
		$defaultId  =	( !empty($defaultId) ) ? $defaultId : $album->photoid;

		// Load the default photo
		$photo	    =&	JTable::getInstance( 'Photo' , 'CTable' );
		$photo->load( $defaultId );
		
		$document->addHeadLink($photo->getThumbURI(), 'image_src', 'rel');

		// If default has an id of 0, we need to tell the template to dont process anything
		$default    =	($photo->id == 0 ) ? false : $photo;

		//friend list for photo tag
		CFactory::load( 'libraries' , 'phototagging' );
		$tagging	=   new CPhotoTagging();

		for($i=0; $i < count($photos); $i++)
		{
			$item =& JTable::getInstance( 'Photo' , 'CTable' );
			$item->bind($photos[$i]); 
			$photos[$i]	=   $item;
			$row		=&  $photos[$i];
			$taggedList	=   $tagging->getTaggedList($row->id);
							
			for($t=0;$t < count($taggedList);$t++)
			{
				$tagItem	=& $taggedList[$t];
				$tagUser	= CFactory::getUser($tagItem->userid);
				
				$canRemoveTag	= 0;
				// 1st we check the tagged user is the photo owner.
				//	If yes, canRemoveTag == true.
				//	If no, then check on user is the tag creator or not.
				//		If yes, canRemoveTag == true
				//		If no, then check on user whether user is being tagged
				if(COwnerHelper::isMine($my->id, $row->creator) || COwnerHelper::isMine($my->id, $tagItem->created_by) || COwnerHelper::isMine($my->id, $tagItem->userid))
				{
					$canRemoveTag = 1;
				}
				
				$tagItem->user		=   $tagUser;
				$tagItem->canRemoveTag	=   $canRemoveTag;
				
			}
			$row->tagged	= $taggedList;			
		}

		//$friends	= $handler->getTaggingUsers();   

		// Show wall contents
		CFactory::load( 'helpers' , 'friends' );
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'libraries' , 'bookmarks' );		
			
		// Load up required objects.		
		$isMine			= $handler->isAlbumOwner( $album->id );
		$bookmarks		= new CBookmarks( $handler->getPhotoExternalURI( $photo->id , $album->id ) );

		//Performance issues: blocked users should be handle in sql statment
		/**
		 * Get ban list
		 */
		//$block          = CFactory::getModel( 'block' );
		//$friendlist     = array();

		//foreach($friends as $friend){
		    // Exclude blocked user
		//    if( !$block->getBlockStatus($my->id,$friend->id) ){
		//	$friendlist[]   = $friend;
		//    }
		//}

		$this->showSubmenu();
		$tmpl	= new CTemplate();
		echo $tmpl	->set( 'bookmarksHTML'	, $bookmarks->getHTML() )
					->set( 'showWall'		, $handler->isWallAllowed() )
					->set( 'allowTag'		, $handler->isTaggable() )
					->set( 'isOwner' 		, $isMine )
					->set( 'isAdmin'		, COwnerHelper::isCommunityAdmin() )
					->set( 'photos' 		, $photos )
					->set( 'default'		, $default )
					->set( 'album'			, $album)
//					->set( 'friends'		, $friends)
					->set( 'config'		, $config)
					->set( 'photoCreator'	, CFactory::getUser( $photo->creator ) )
					->fetch('photos.photo');
	}
	
	/**
	 * return the resized images
	 */	 	
	public function showimage()
	{
	}
	
	
	/**
	 * Return photos handlers
	 */	 	
	private function _getHandler()
	{
		$handler = null;
		
		$groupId	= JRequest::getInt( 'groupid' , '' , 'REQUEST' );
		$type		= PHOTOS_USER_TYPE;

		if(!empty($groupId) )
		{
			// group photo
			$handler = new CommunityViewPhotosGroupHandler( $this );
		}
		else
		{
			// user photo
			$handler = new CommunityViewPhotosUserHandler( $this );
		}
		
		return $handler;
	} 
	
	/**
	 * Application full view
	 **/
	public function appFullView()
	{
		$document		= JFactory::getDocument();
		$document->setTitle( JText::_('COM_COMMUNITY_PHOTOS_WALL_TITLE') );
		
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
			$albumId	= JRequest::getInt( 'albumid' , '' , 'GET' );
			$my			= CFactory::getUser();
			$config		= CFactory::getConfig();
			
			$album		=& JTable::getInstance( 'Album' , 'CTable' );
			$album->load( $albumId );
		
			CFactory::load( 'helpers' , 'owner' );
			CFactory::load( 'helpers' , 'friends' );
					
			if( CFriendsHelper::isConnected($my->id, $album->creator) || COwnerHelper::isCommunityAdmin() )
			{
				$output	.= CWallLibrary::getWallInputForm( $album->id , 'photos,ajaxAlbumSaveWall', 'photos,ajaxAlbumRemoveWall' );
			}

			// Get the walls content
			$viewAllLink = false;
			$wallCount	= false;
			if(JRequest::getVar('task', '', 'REQUEST') != 'app')
			{
				$viewAllLink	= CRoute::_('index.php?option=com_community&view=photos&task=app&albumid=' . $album->id . '&app=walls');
				$wallCount		= CWallLibrary::getWallCount('album', $album->id);
			}
			$output 	.='<div id="wallContent">';
			$output		.= CWallLibrary::getWallContents( 'albums' , $album->id , ( COwnerHelper::isCommunityAdmin() || COwnerHelper::isMine($my->id, $album->creator) ) , $limit , $limitstart );
			$output		.= CWallLibrary::getViewAllLinkHTML($viewAllLink, $wallCount);
			$output 	.= '</div>';
			
			jimport('joomla.html.pagination');
			$wallModel 	= CFactory::getModel('wall');
			$pagination	= new JPagination( $wallModel->getCount( $album->id , 'albums' ) , $limitstart , $limit );

			$output		.= '<div class="pagination-container">' . $pagination->getPagesLinks() . '</div>';
		}
		else
		{
			$model			= CFactory::getModel('apps');
			$applications	=& CAppPlugins::getInstance();
			$applicationId	= $model->getUserApplicationId( $applicationName );

			$application	= $applications->get( $applicationName , $applicationId );
			
			if (is_callable(array($application, 'onAppDisplay'), true))
			{
				// Get the parameters
				$manifest		= CPluginHelper::getPluginPath('community',$applicationName) . DS . $applicationName . DS . $applicationName . '.xml';

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
}

abstract class CommunityViewPhotoHandler extends CommunityView
{
	protected $type 	= '';
	protected $model 	= '';
	protected $view		= '';
	protected $my		= '';
		
	abstract public function getType();
	abstract public function getFlashUploadURI( $token , $albumId );
	abstract public function getAllAlbumData();
	abstract public function getAlbumURI( $albumId );
	abstract public function getAlbumExternalURI( $albumId );
	abstract public function getPhotoURI( $photoId , $albumId );
	abstract public function getPhotoExternalURI( $photoId , $albumId );
	abstract public function getCreatorName();
	abstract public function getAlbumPhotos( $albumId );
	abstract public function getTaggingUsers();
	abstract public function getAlbumCreateLink();
	
	abstract public function setAlbumPathway( $albumName );
	abstract public function setMiniHeader();
	abstract public function setSubmenus();
	abstract public function setRSSHeader( $albumId );
	
	abstract public function isExceedUploadLimit();
	abstract public function isPhotoBrowsable( $photoId );
	abstract public function isAlbumBrowsable( $albumId );
	abstract public function isAlbumOwner( $albumId );
	abstract public function isTaggable();
	abstract public function isWallAllowed();
	
	public function __construct( CommunityViewPhotos $viewObj )
	{
		$this->view		= $viewObj;
		$this->my		= CFactory::getUser();
		$this->model	= CFactory::getModel( 'photos' );
	}
}
class CommunityViewPhotosUserHandler extends CommunityViewPhotoHandler
{
	var $user	= null;
	
	public function __construct( $viewObj )
	{
		parent::__construct( $viewObj );
		$userid			= JRequest::getVar('userid' , '' , 'GET' );
		$this->user		= CFactory::getUser( $userid );
	}

	public function getAlbumCreateLink()
	{
		return CRoute::_('index.php?option=com_community&view=photos&task=newalbum&userid=' . $this->my->id );
	}
	
	public function getFlashUploadURI( $token , $albumId )
	{
		$session	= JFactory::getSession();
		$url	= 'index.php?option=com_community&view=photos&task=upload&no_html=1&albumid=' . $albumId . '&tmpl=component';
		$url	.= '&' . $session->getName() . '=' . $session->getId() .'&token=' . $token->token .'&uploaderid=' . $this->my->id . '&userid=' . $this->my->id;
		$url	= rtrim( JURI::root() , '/' ) . '/' . $url;
		return $url;
//		$url = CRoute::_($url);
//		$uri = JURI::getInstance();
//		$uri = new JURI($uri->toString());
//		$uri->setPath($url);
//		$uri->setQuery('');
//		return $uri->toString();
	}
	
	public function isWallAllowed()
	{
		CFactory::load( 'helpers' , 'friends' );
		CFactory::load( 'helpers' , 'owner' );
		
		$config		= CFactory::getConfig();
		
		// Check if user is really allowed to post walls on this photo.
		if( COwnerHelper::isMine( $this->my->id , $this->user->id ) || (!$config->get('lockprofilewalls')) || ( $config->get('lockprofilewalls') && CFriendsHelper::isConnected( $this->my->id , $this->user->id ) ) )
		{
			return true;
		}
		return false;
	}

	public function isTaggable()
	{
		CFactory::load( 'helpers' , 'friends' );
		CFactory::load( 'helpers' , 'owner' );
		
		if( COwnerHelper::isMine( $this->my->id , $this->user->id ) || CFriendsHelper::isConnected( $this->my->id , $this->user->id ) )
		{
			return true;
		}
		return false;
	}
	
	public function getTaggingUsers()
	{
		$model		= CFactory::getModel( 'friends' );
//		$friends	= $model->getFriends( $this->my->id , '' , false );
		$friends	= $model->getFriendRecords( $this->my->id , '' , false );
		array_unshift($friends, $this->my);
		
		return $friends;
	}
	
	public function setRSSHeader( $albumId )
	{
		$document	= JFactory::getDocument();
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );
		$mainframe	=& JFActory::getApplication();

		// Set feed url
		$link	= CRoute::_('index.php?option=com_community&view=photos&task=album&albumid='.$album->id.'&userid='.$album->creator.'&format=feed');
		$feed	= '<link rel="alternate" type="application/rss+xml" href="'.$link.'"/>';
		
		$document->addCustomTag( $feed );
	}
	
	public function getAlbumPhotos( $albumId )
	{
		$config	= CFactory::getConfig();
		$model	= CFactory::getModel('Photos');
		
		// @todo: make limit configurable?
		return $model->getAllPhotos( $albumId, PHOTOS_USER_TYPE , null , null , $config->get('photosordering') );
	}
	
	public function setAlbumPathway( $albumName )
	{
		$mainframe	= JFactory::getApplication();
		$pathway 	=& $mainframe->getPathway();
		$pathway->addItem( $albumName );
	}

	public function setSubmenus()
	{
		$my			= CFactory::getUser();
		$config		= CFactory::getConfig();
		$task		= JRequest::getCmd( 'task' , '' , 'GET' );
		$albumId	= JRequest::getInt('albumid' , '' , 'GET');
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );
		
		if($albumId != 0 )
		{
			$albumId	= '&albumid=' . $albumId;
		}
		else
		{
			$albumId	= '';
		}

		switch( $task )
		{
			case 'photo':
				if ($albumId) $this->view->addSubmenuItem('index.php?option=com_community&view=photos&userid=' . $this->user->id . '&task=album' . $albumId , JText::_('COM_COMMUNITY_PHOTOS_BACK_TO_ALBUM'));
				$this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=display', JText::_('COM_COMMUNITY_PHOTOS_ALL_PHOTOS'));
				if( COwnerHelper::isCommunityAdmin() || ($this->my->id == $album->creator && ($this->my->id != 0) ) ) 
				{
					$this->view->addSubmenuItem('' , JText::_('COM_COMMUNITY_DELETE'), "joms.gallery.confirmRemovePhoto();", true);
					
					if( $this->my->id == $album->creator )
					{
						$this->view->addSubmenuItem('' , JText::_('COM_COMMUNITY_PHOTOS_SET_AVATAR'), "joms.gallery.setProfilePicture();" , true);
					}
					$this->view->addSubmenuItem('' , JText::_('COM_COMMUNITY_PHOTOS_SET_AS_ALBUM_COVER'), "joms.gallery.setPhotoAsDefault();" , true);
				}
				if( !$config->get('deleteoriginalphotos') ) {
					$this->view->addSubmenuItem('' , JText::_('COM_COMMUNITY_DOWNLOAD_IMAGE'), "joms.gallery.downloadPhoto();", true);
				}
				break;
			case 'singleupload':
			case 'uploader':
				if ($albumId) $this->view->addSubmenuItem('index.php?option=com_community&view=photos&userid=' . $this->user->id . '&task=album' . $albumId , JText::_('COM_COMMUNITY_PHOTOS_BACK_TO_ALBUM'));
				$this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=display', JText::_('COM_COMMUNITY_PHOTOS_ALL_PHOTOS'));
				$this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=newalbum&userid=' . $my->id, JText::_('COM_COMMUNITY_PHOTOS_CREATE_PHOTO_ALBUM'),'',true);
			break;
			case 'myphotos':
			default:
				$this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=display', JText::_('COM_COMMUNITY_PHOTOS_ALL_PHOTOS'));
				
				if( $this->my->id != 0 || COwnerHelper::isCommunityAdmin() )
				{
					$albumid = JRequest::getCmd('albumid', '', 'GET');
					$groupid = JRequest::getCmd('groupid', '', 'GET');
					if($task != 'newalbum'  && $task != 'editAlbum'){
					    $this->view->addSubmenuItem('javascript:void(0);', JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_PHOTOS'), "joms.notifications.showUploadPhoto('".$albumid."','".$groupid."'); return false;", true);
					}
				}

				if( $task == 'album' && $my->id == $album->creator )
				{
					$this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=editAlbum' . $albumId . '&userid=' . $my->id . '&referrer=album' , JText::_('COM_COMMUNITY_EDIT_ALBUM') , '' , true );
				}

				if( $this->my->id != 0 || COwnerHelper::isCommunityAdmin() )
				{
					$this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=newalbum&userid=' . $my->id, JText::_('COM_COMMUNITY_PHOTOS_CREATE_PHOTO_ALBUM') , '' , true );
				}
			break;
		}		

		if( $my->id != 0 )
		{
			$this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=myphotos&userid=' . $my->id, JText::_('COM_COMMUNITY_PHOTOS_MY_PHOTOS'));
		}
	}
	
	public function getType()
	{
		return PHOTOS_USER_TYPE;
	}

	/**
	 * Deprecated since 1.8.9
	 **/	 
	public function isPhotoBrowsable( $photoId )
	{
		return $this->isAlbumBrowsable( $photoId );
	}
	
	public function isAlbumBrowsable( $albumId )
	{
		CFactory::load('libraries', 'privacy' );
		$mainframe	=& JFactory::getApplication();
		
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );
		
		if($this->user->block && !COwnerHelper::isCommunityAdmin( $this->my->id ) )
		{
			$mainframe->redirect( 'index.php?option=com_community&view=photos', JText::_('COM_COMMUNITY_PHOTOS_USER_ACCOUNT_IS_BANNED') );
			return false;
		}
		
		//if( !CPrivacy::isAccessAllowed($this->my->id, $this->user->id, 'user', 'privacyPhotoView') || $album->creator != $this->user->id )
		if( !CPrivacy::isAccessAllowed($this->my->id, $this->user->id, 'custom', $album->permissions) || $album->creator != $this->user->id )
		{
			$this->noAccess();
			return false;
		}
		return true;
	}
	
	public function isAlbumOwner( $albumId )
	{
		CFactory::load('models' , 'photos' );
		
		if( $this->my->id == 0 )
			return false;
			
		$album	=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );
		
		return COwnerHelper::isMine($this->my->id, $album->creator );
	}
	
	/**
	 * Return the uri to the album view, given the album id
	 */
	public function getAlbumURI( $albumId )
	{
		return CRoute::_( 'index.php?option=com_community&view=photos&task=album&albumid=' . $albumId . '&userid=' . $this->user->id );
	}

	public function getAlbumExternalURI( $albumId )
	{
		return CRoute::getExternalURL( 'index.php?option=com_community&view=photos&task=album&albumid=' . $albumId . '&userid=' . $this->user->id );
	}
	
	/**
	 * Return the uri to the photo view, given the album id and photo id
	 */	 	
	public function getPhotoURI( $photoId , $albumId )
	{
		return CRoute::_('index.php?option=com_community&view=photos&task=photo&userid=' . $this->user->id . '&albumid=' . $albumId ) . '#photoid=' . $photoId;
	}

	public function getPhotoExternalURI( $photoId, $albumId )
	{
		return CRoute::getExternalURL( 'index.php?option=com_community&view=photos&task=album&albumid=' . $albumId . '&userid=' . $this->user->id ) . '#photoid=' . $photoId;
	}
	
	public function isExceedUploadLimit()
	{
		$my	= CFactory::getUser();
		
		if( CLimitsHelper::exceededPhotoUpload($my->id , PHOTOS_USER_TYPE ) )
		{
			$config			= CFactory::getConfig();
			$photoLimit		= $config->get( 'photouploadlimit' );
			
			echo JText::sprintf('COM_COMMUNITY_PHOTOS_UPLOAD_LIMIT_REACHED' , $photoLimit );
			return true;
		}
		return false;
	}
	
	/**
	 * Return data for the 'all album' view
	 */
	public function getAllAlbumData()
	{
		$albumsData['data']			= $this->model->getAllAlbums( $this->my->id );
		$albumsData['pagination']	= $this->model->getPagination();
		return $albumsData;
	}
	
	public function setMiniHeader()
	{
		if( $this->my->id != $this->user->id )
		{
			$this->view->attachMiniHeaderUser($this->user->id);
		}
	}
	
	public function getCreatorName()
	{
		return $this->user->getDisplayName();
	}
}

class CommunityViewPhotosGroupHandler extends CommunityViewPhotoHandler
{
	private $groupid = null;
	
	/**
	 * Constructor
	 */
	public function __construct( $viewObj )
	{
		parent::__construct( $viewObj );
		$this->groupid = JRequest::getInt( 'groupid' , '' , 'REQUEST' );
	}

	public function getFlashUploadURI( $token , $albumId )
	{
		$session	= JFactory::getSession();
		$url	= 'index.php?option=com_community&view=photos&task=upload&no_html=1&albumid=' . $albumId . '&tmpl=component';
		$url	.= '&' . $session->getName() . '=' . $session->getId() .'&token=' . $token->token .'&uploaderid=' . $this->my->id . '&groupid=' . $this->groupid;
		$url	= rtrim( JURI::root() , '/' ) . '/' . $url;
		return $url;
		
//		$url = CRoute::_($url);
//		$uri = JURI::getInstance();
//		$uri = new JURI($uri->toString());
//		$uri->setPath($url);
//		$uri->setQuery('');
//		return $uri->toString();
	}
	
	public function getAlbumCreateLink()
	{
		return CRoute::_('index.php?option=com_community&view=photos&task=newalbum&groupid=' . $this->groupid );
	}
	
	public function isWallAllowed()
	{
		return $this->isTaggable();
	}
	
	public function isTaggable()
	{
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'models' , 'groups' );
		$group	=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $this->groupid );
		
		//check if we can allow the current viewing user to tag the photos
		if($group->isMember( $this->my->id ) || $group->isAdmin( $this->my->id ) || COwnerHelper::isCommunityAdmin() )
		{
			return true;
		}
		return false;
	}
	
	public function getTaggingUsers()
	{
		// for photo tagging. only allow to tag members
		$model	= CFactory::getModel( 'groups' );
		$ids	= $model->getMembersId( $this->groupid , true);
		$users	= array();
		
		$group	=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $this->groupid );
		
		foreach($ids as $id )
		{
			if( $this->my->id != $id )
			{
				$user		= CFactory::getUser( $id );			
				$users[]	= $user;
			}
		}

		CFactory::load( 'helpers' , 'owner' );
		
		if(COwnerHelper::isCommunityAdmin() || $group->isAdmin( $this->my->id ) || $group->isMember( $this->my->id ))
			array_unshift($users, $this->my);
		
		return $users;
	}
	
	public function setRSSHeader( $albumId )
	{
		return;
	}
	
	public function getAlbumPhotos( $albumId )
	{
		$config	= CFactory::getConfig();
		$model	= CFactory::getModel('Photos');
		
		// @todo: make limit configurable?
		return $model->getAllPhotos( $albumId , PHOTOS_GROUP_TYPE  , null , null , $config->get('photosordering') );
	}

	public function setSubmenus()
	{
		CFactory::load( 'helpers' , 'group' );
		CFactory::load( 'helpers' , 'owner' );
		
		$task		=   JRequest::getVar( 'task', '', 'GET' );
		$albumId	=   JRequest::getInt( 'albumid', 0 , 'REQUEST');
		$groupId	=   JRequest::getInt( 'groupid', '', 'REQUEST' );

		if(!empty($albumId))
		{
		    $album	=   JTable::getInstance( 'Album' ,'CTable' );
		    $album->load( $albumId );
		    $groupId	=   $album->groupid;
		}
		
		CFactory::load( 'models' , 'groups' );
		$config		=   CFactory::getConfig();
		$group		=   JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $groupId );

		$my		=   CFactory::getUser();
		$albumId	=   $albumId != 0 ? '&albumid=' . $albumId : '';

		// Check if the current user is banned from this group
		$isBanned	=   $group->isBanned( $my->id );

		CFactory::load( 'helpers' , 'group' );
		
		$allowManagePhotos = CGroupHelper::allowManagePhoto($this->groupid);
		
		if( ($task == 'uploader' || $task == 'photo') && !empty($albumId) )
		{
			$this->view->addSubmenuItem('index.php?option=com_community&view=photos&groupid=' . $this->groupid . '&task=album' . $albumId , JText::_('COM_COMMUNITY_PHOTOS_BACK_TO_ALBUM'));
		}

		if( $allowManagePhotos && $task != 'photo' && !$isBanned )
		{
			$rawAlbumId = "";
			if(!empty($albumId)){
			    $rawAlbumId = explode('=',$albumId);
			    $rawAlbumId = $rawAlbumId[1];

			}
			
			if($task != 'newalbum' && $task != 'editAlbum'){
			$this->view->addSubmenuItem('javascript:void(0);' , JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_PHOTOS'), 'joms.notifications.showUploadPhoto(\''.$rawAlbumId.'\','.$this->groupid.'); return false;', true);
			}

			if( $task == 'album' && ( ($my->id == $album->creator && $allowManagePhotos ) || $group->isAdmin($my->id) || COwnerHelper::isCommunityAdmin() ) )
			{
				$this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=editAlbum&albumid=' . $album->id . '&groupid=' . $group->id . '&referrer=albumgrp' , JText::_('COM_COMMUNITY_EDIT_ALBUM') , '' , true );
			}
			
			$this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=newalbum&groupid=' . $this->groupid , JText::_('COM_COMMUNITY_PHOTOS_CREATE_PHOTO_ALBUM') , '' , true );
		}

		if( $task == 'photo' )
		{
			if( $album->hasAccess( $my->id , 'deletephotos' ) )
			{
				$this->view->addSubmenuItem('' , JText::_('COM_COMMUNITY_PHOTOS_DELETE'), "joms.gallery.confirmRemovePhoto();", true);
			}
			
			if( $my->id == $album->creator )
			{
				$this->view->addSubmenuItem('' , JText::_('COM_COMMUNITY_PHOTOS_SET_AVATAR'), "joms.gallery.setProfilePicture();" , true);
			}

			if( ($my->id == $album->creator && $allowManagePhotos ) || $group->isAdmin($my->id) || COwnerHelper::isCommunityAdmin() ) 
			{
				$this->view->addSubmenuItem('' , JText::_('COM_COMMUNITY_PHOTOS_SET_AS_ALBUM_COVER'), "joms.gallery.setPhotoAsDefault();" , true);	
			}

			if( !$config->get('deleteoriginalphotos') ) {
				$this->view->addSubmenuItem('' , JText::_('COM_COMMUNITY_DOWNLOAD_IMAGE'), "joms.gallery.downloadPhoto();", true);
			}
			
                        if($groupId!='')
                        {
                            $this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=newalbum&groupid=' . $groupId, JText::_('COM_COMMUNITY_PHOTOS_CREATE_PHOTO_ALBUM'),'',true);
                        }
                        else
                        {
                            $this->view->addSubmenuItem('index.php?option=com_community&view=photos&task=newalbum&userid=' . $my->id, JText::_('COM_COMMUNITY_PHOTOS_CREATE_PHOTO_ALBUM'),'',true);
                        }
		}

		$this->view->addSubmenuItem('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $this->groupid , JText::_('COM_COMMUNITY_GROUPS_BACK_TO_GROUP'));

	}
	
	/**
	 * Deprecated since 1.8.9
	 **/	 
	public function isPhotoBrowsable( $photoId )
	{
		return $this->isAlbumBrowsable( $photoId );
	}

	public function isAlbumOwner( $albumId )
	{
		CFactory::load('models','groups');

		if( $this->my->id == 0 )
			return false;
			
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $this->groupid );
		
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );
		
		if($album->creator == $this->my->id || COwnerHelper::isCommunityAdmin())
		{
			return true;
		}
		
		return false;
	}
	
	public function isAlbumBrowsable( $albumId )
	{
		CFactory::load('models','groups');
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );

		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $album->groupid );

		$document	= JFactory::getDocument();
		$mainframe	=& JFactory::getApplication();

		//@rule: Do not allow non members to view albums for private group
		if( $group->approvals == COMMUNITY_PRIVATE_GROUP && !$group->isMember( $this->my->id ) && !$group->isAdmin( $this->my->id ) && !COwnerHelper::isCommunityAdmin() )
		{			
			// Set document title
			$document->setTitle( JText::_('COM_COMMUNITY_RESTRICTED_ACCESS') );
			$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS', 'notice'));
			
			echo JText::_('COM_COMMUNITY_GROUPS_ALBUM_MEMBER_PERMISSION');
			return false;
		}

		return true;
	}
	
	public function getType()
	{
		return PHOTOS_GROUP_TYPE;
	}
	
	/**
	 * Return the uri to the album view, given the album id
	 */	 	
	public function getAlbumURI( $albumId )
	{
		return CRoute::_( 'index.php?option=com_community&view=photos&task=album&albumid=' . $albumId . '&groupid=' . $this->groupid );
	}

	public function getAlbumExternalURI( $albumId )
	{
		return CRoute::getExternalURL( 'index.php?option=com_community&view=photos&task=album&albumid=' . $albumId . '&groupid=' . $this->groupid );
	}
	
	public function getPhotoURI( $photoId , $albumId )
	{
		return CRoute::_('index.php?option=com_community&view=photos&task=photo&groupid=' . $this->groupid . '&albumid=' . $albumId ) . '#photoid=' . $photoId;
	}
	
	public function getPhotoExternalURI( $photoId, $albumId )
	{
		return CRoute::getExternalURL( 'index.php?option=com_community&view=photos&task=photo&albumid=' . $albumId . '&groupid=' . $this->groupid ) . '#photoid=' . $photoId;
	}
	
	public function isExceedUploadLimit()
	{
		if( CLimitsHelper::exceededPhotoUpload($this->groupid , PHOTOS_GROUP_TYPE ) )
		{
			$config			= CFactory::getConfig();
			$photoLimit		= $config->get( 'groupphotouploadlimit' );
			
			echo JText::sprintf('COM_COMMUNITY_GROUPS_PHOTO_LIMIT' , $photoLimit );
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Return data for the 'all album' view
	 */	 	
	public function getAllAlbumData()
	{
		$my	= CFactory::getUser();
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $this->groupid );
		
		//@rule: Do not allow non members to view albums for private group
		if( $group->approvals == COMMUNITY_PRIVATE_GROUP && !$group->isMember( $my->id ) && !$group->isAdmin( $my->id ) )
		{
			$this->noAccess();
			return FALSE;			
		}
		$type		= PHOTOS_GROUP_TYPE;
		$albumsData['data']	= $this->model->getGroupAlbums( $this->groupid, true );
		$albumsData['pagination']	= $this->model->getPagination();
		
		return $albumsData;
	}
	
	public function setMiniHeader()
	{
		// Do nothing because the mini header for groups are done on the view itself. Function is to satisfy the abstract.
	}

	public function setAlbumPathway( $albumName )
	{
		$mainframe	=& JFactory::getApplication();
        $pathway 	=& $mainframe->getPathway();
		$pathway->addItem( $albumName , '' );
	}

	public function getCreatorName()
	{
		CFactory::load('models','groups');
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $this->groupid );
		
		return $group->name;
	}
}