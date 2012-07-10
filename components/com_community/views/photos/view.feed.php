<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CommunityViewPhotos extends CommunityView
{

	/**
	 * Default view method
	 * Display all available album
	 **/
	public function display()
	{            
		$this->_getAllAlbums();
		
// 		// Now that we have the content, we need to insert the media link 
// 		$pattern    = "'</item>'s";
// 		for($i =0; $i< count($postvars); $i++)
// 		{
// 			if(!empty($postvars[$i]) && is_array($postvars[$i])){
// 				$key = $postvars[$i][0];
// 				// Blogger view
// 				
// 				preg_match($pattern, $key, $matches);
// 				if($matches){
// 					$key = $matches[1];
// 				}
// 				$post[$key] = $postvars[$i][1];
// 			}
// 		}
// 		echo $content;
// 		exit;
	}

	public function myphotos()
	{
		$my			= CFactory::getUser();		
		$document	= JFactory::getDocument();

		CFactory::load('helpers' , 'owner' );
		$userid		= JRequest::getInt( 'userid' , '' );
		$user		= CFactory::getUser($userid);
		
		$blocked	= $user->isBlocked();

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

 		$model	= CFactory::getModel( 'photos' );

 		$albums		= $model->getAlbums( $user->id , true , true );
		
		foreach($albums as $album )
		{
			$table			=& JTable::getInstance( 'Album' , 'CTable' );
			$table->bind($album);
			$table->thumbnail	= $table->getCoverThumbPath();

			$albumAuthor = CFactory::getUser($table->creator);
			
			$description  = '<img src="' . $table->thumbnail . '" alt="" />&nbsp;';
			$description .= $albumAuthor->getDisplayName(). ' posted '. $album->count . ' photos ';
            		
			//print_r($albumAuthor); exit;
			$item = new JFeedItem();
			$item->title 		= $table->name;
			$item->link 		= CRoute::_('index.php?option=com_community&view=photos&task=album&albumid='.$album->id.'&userid='.$albumAuthor->id);
			$item->description 	= $description . $table->description;
			$item->date			= $table->created;
			$item->author		= $albumAuthor->getDisplayName();
			
			$item->description = CString::str_ireplace('_QQQ_', '"', $item->description);
			// Make sure url is absolute
			$item->description  = CString::str_ireplace('href="/', 'href="'. JURI::base(), $item->description);

			$document->addItem( $item );
			
		}
	}

	public function _getAlbumsHTML( $albums , $type = PHOTOS_USER_TYPE )
	{
		$my		= CFactory::getUser();
		$config		= CFactory::getConfig();
		$groupId	= JRequest::getInt( 'groupid' , '' ,'REQUEST');

		$tmpl		= new CTemplate();

		CFactory::load( 'libraries' , 'activities' );
		CFactory::load( 'models' , 'groups' );
		CFactory::load( 'helpers' , 'owner' );
		
		for($i = 0; $i < count($albums); $i++)
		{
			$albums[$i]->user		= CFactory::getUser( $albums[$i]->creator );
			$albums[$i]->link 		= CRoute::_("index.php?option=com_community&view=photos&task=album&albumid={$albums[$i]->id}&userid={$albums[$i]->creator}");
			$albums[$i]->editLink 	= CRoute::_("index.php?option=com_community&view=photos&task=editAlbum&albumid={$albums[$i]->id}&userid={$albums[$i]->creator}");
			$albums[$i]->uploadLink = CRoute::_("index.php?option=com_community&view=photos&task=uploader&albumid={$albums[$i]->id}&userid={$albums[$i]->creator}");
			$albums[$i]->isOwner	= ($my->id == $albums[$i]->creator);

			if( $type == PHOTOS_GROUP_TYPE)
			{
				$group	=& JTable::getInstance( 'Group' , 'CTable' );
				$group->load($groupId);
				
				$albums[$i]->link 		= CRoute::_("index.php?option=com_community&view=photos&task=album&albumid={$albums[$i]->id}&groupid={$albums[$i]->groupid}");
				$albums[$i]->editLink	= CRoute::_("index.php?option=com_community&view=photos&task=editAlbum&albumid={$albums[$i]->id}&groupid={$albums[$i]->groupid}");
				$albums[$i]->uploadLink = CRoute::_("index.php?option=com_community&view=photos&task=uploader&albumid={$albums[$i]->id}&groupid={$albums[$i]->groupid}");

				
				$params				= $group->getParams();
				$photopermission	= $params->get('photopermission', GROUP_PHOTO_PERMISSION_ADMINS);
			
				if( $photopermission == GROUP_PHOTO_PERMISSION_MEMBERS && $group->isMember($my->id) )
				{
					$albums[$i]->isOwner	= ($my->id == $albums[$i]->creator);
				}
				else if( ($photopermission == GROUP_PHOTO_PERMISSION_ADMINS && $group->isAdmin($my->id ) ) || COwnerHelper::isCommunityAdmin() )
				{
					$albums[$i]->isOwner	= true;
				}
				else
				{
					$albums[$i]->isOwner	= false;
				}
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
					$albums[$i]->lastupdated	= CActivityStream::_createdLapse( $lastUpdated );
				}
			}
			else
			{
				$lastUpdated	= new JDate( $albums[$i]->lastupdated );
				$albums[$i]->lastupdated	= CActivityStream::_createdLapse( $lastUpdated );
			}

		}
		CFactory::load( 'helpers' , 'owner' );

		CFactory::load( 'libraries' , 'featured' );
		$featured	= new CFeatured( FEATURED_ALBUMS );
		$featuredList	= $featured->getItemIds();

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
		$task	= JRequest::getCmd( 'task' , '');
		$tmpl->set( 'isOwner'		, $isOwner );
		$tmpl->set( 'type'			, $type );
		$tmpl->set( 'createLink'	, $createLink );
		$tmpl->set( 'currentTask'	, $task );
		$tmpl->set( 'showFeatured'		, $config->get('show_featured') );
		$tmpl->set( 'featuredList'		, $featuredList );
		$tmpl->set( 'isCommunityAdmin'	, COwnerHelper::isCommunityAdmin() );
		$tmpl->set( 'my'		, $my );
		$tmpl->set( 'albums' 	, $albums );
		$tmpl->set( 'isSuperAdmin'	, COwnerHelper::isCommunityAdmin());

		return $tmpl->fetch( 'albums.list' );
	}


	public function _groupAlbum()
	{
		CFactory::load( 'models' , 'photos' );
		CFactory::load('helpers', 'friends');
		CFactory::load( 'models' , 'groups' );
		
		$mainframe	=& JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= CFactory::getConfig();
		$my			= CFactory::getUser();
 		$model		= CFactory::getModel('photos');
 		$groupModel	= CFactory::getModel( 'groups' );
		$albumId	= JRequest::getInt('albumid' , '' , 'GET');
 		$defaultId	= JRequest::getVar('photo' , '' , 'GET');
		$groupId	= JRequest::getInt( 'groupid' , '' , 'REQUEST' );
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $groupId );
		
 		if( empty( $albumId ) )
 		{
 			echo JText::_('COM_COMMUNITY_PHOTOS_NO_ALBUMID_ERROR');
 			return;
		}

		CFactory::load( 'helpers' , 'owner' );
		//@rule: Do not allow non members to view albums for private group
		if( $group->approvals == COMMUNITY_PRIVATE_GROUP && !$group->isMember( $my->id ) && !$group->isAdmin( $my->id ) && !COwnerHelper::isCommunityAdmin() )
		{			
			// Set document title
			$document->setTitle( JText::_('COM_COMMUNITY_RESTRICTED_ACCESS') );
			$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS', 'notice'));
			
			echo JText::_('COM_COMMUNITY_GROUPS_ALBUM_MEMBER_PERMISSION');
			return;
		}

		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );

		// Set document title
		CFactory::load( 'helpers' , 'string' );
		$document->setTitle( JText::sprintf( 'COM_COMMUNITY_PHOTOS_USER_PHOTOS_TITLE' , $group->name ) .' - '. $album->name);
		$this->setTitle($album->name);

		$pathway 	=& $mainframe->getPathway();
		$pathway->addItem( $album->name, '' );
		
		CError::assert( $group , 'object' , 'istype' , __FILE__ , __LINE__ );
	
		// Get list of photos and set some limit to be displayed.
		// @todo: make limit configurable?
		$photos		= $model->getAllPhotos( $albumId, PHOTOS_GROUP_TYPE , null , null , COMMUNITY_ORDER_BY_DESC , COMMUNITY_ORDERING_BY_CREATED );
	
		// Need to append the absolute path for the captions
		for( $i = 0; $i < count( $photos ); $i++ )
		{
			$item =& JTable::getInstance( 'Photo' , 'CTable' );
			$item->bind($photos[$i]);
			$photos[$i] = $item;
			
			$photo	=& $photos[ $i ];
			$photo->link		= CRoute::_('index.php?option=com_community&view=photos&task=photo&groupid=' . $groupId . '&albumid=' . $photo->albumid) . '#photoid=' . $photo->id;
		}

		CFactory::load( 'helpers' , 'group' );					
		$allowManagePhotos = CGroupHelper::allowManagePhoto($groupId);
				
		if( ($group->isAdmin($my->id) || $allowManagePhotos) && $my->id != 0 )
		{
			$this->addSubmenuItem('index.php?option=com_community&view=photos&task=uploader&albumid=' . $album->id . '&groupid=' . $group->id , JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_PHOTOS'), '', true);
			$this->addSubmenuItem('index.php?option=com_community&view=photos&task=newalbum&groupid=' . $group->id, JText::_('COM_COMMUNITY_PHOTOS_CREATE_PHOTO_ALBUM') , '' , true );
		}
		$tmpl	= new CTemplate();
		CFactory::load( 'libraries' , 'bookmarks' );
		$bookmarks		= new CBookmarks(CRoute::getExternalURL( 'index.php?option=com_community&view=photos&task=album&albumid=' . $album->id . '&groupid=' . $group->id ));
		$bookmarksHTML	= $bookmarks->getHTML();

		$tmpl->set( 'bookmarksHTML'	, $bookmarksHTML );
		$tmpl->set( 'isOwner' 		, $group->isAdmin( $my->id ) );
		$tmpl->set( 'photos' 		, $photos );
		$tmpl->set( 'album'			, $album );

		echo $tmpl->fetch('photos.album');
	}
	
	public function _userAlbum()
	{
		$mainframe =& JFactory::getApplication();
		$document = JFactory::getDocument();

		// Get the configuration object.
		$config	= CFactory::getConfig();

		// Get necessary properties and load the libraries
		CFactory::load( 'models' , 'photos' );
		CFactory::load('helpers', 'friends');

		$my			= CFactory::getUser();
		$userid		= JRequest::getInt('userid', null );
		$user		= CFactory::getUser($userid);

		if(!$user->block || COwnerHelper::isCommunityAdmin($my->id))
		{
	 		$model		= CFactory::getModel('photos');
	 		$albumId	= JRequest::getVar('albumid' , '' , 'GET');
	 		$defaultId	= JRequest::getVar('photo' , '' , 'GET');

			// Show the mini header when viewing other's photos
			if( $my->id != $user->id )
			{
				$this->attachMiniHeaderUser($user->id);
			}

			// Load the album table
			$album		=& JTable::getInstance( 'Album' , 'CTable' );
			$album->load( $albumId );
			$albumCreator = CFactory::getUser($album->creator);
			
			CFactory::load('helpers', 'owner');
			CFactory::load('libraries', 'privacy');
			
	 		if( empty( $albumId ) || ( $album->creator != $user->id && !COwnerHelper::isCommunityAdmin() ) )
	 		{
	 			echo JText::_('COM_COMMUNITY_PHOTOS_NO_ALBUMID_ERROR');
	 			return;
			}
			

			// Get list of photos and set some limit to be displayed.
			// @todo: make limit configurable?
			$photos		= $model->getAllPhotos( $albumId, PHOTOS_USER_TYPE , null , null , COMMUNITY_ORDER_BY_DESC , COMMUNITY_ORDERING_BY_CREATED );
	
			// Need to append the absolute path for the captions
			for( $i = 0; $i < count( $photos ); $i++ )
			{
				$item =& JTable::getInstance( 'Photo' , 'CTable' );
				$item->bind($photos[$i]);
				$photos[$i] = $item;
				
				$photo	= $photos[ $i ];
				$photo->link		= CRoute::_('index.php?option=com_community&view=photos&task=photo&userid=' . $user->id . '&albumid=' . $photo->albumid) . '#photoid=' . $photo->id;
			}
			
			// Set document title
			CFactory::load( 'helpers' , 'string' );
			$document->setTitle( JText::sprintf( 'COM_COMMUNITY_PHOTOS_USER_PHOTOS_TITLE' ,  $user->getDisplayName() ) .' - '. $album->name);
			$this->setTitle($album->name);
			
			if( !CPrivacy::isAccessAllowed($my->id, $user->id, 'user', 'privacyPhotoView') ){
				echo JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN');
				return;
			}
			
			$guidData = array();
			for($i = 0; $i < 20 && $i < count($photos); $i++)
			{
				$photo = $photos[$i];
				
				$item = new JFeedItem();
				$item->title 		= $photo->caption;
				$item->link 		= CRoute::_('index.php?option=com_community&view=photos&task=photo&userid='.$album->creator.'&albumid='.$album->id).'#photoid='.$photo->id;
				$item->description 	= '<img src="'.$photo->getImageURI().'" width="320"/>';
				$item->date			= $photo->created;
				$item->source		= $photo->getImageURI();
				//$item->author		= $albumAuthor->getDisplayName();
				
				// inject guid with unique data to be replaced later
				$item->guid			= 'photo-'.rand();
			
				$media  = '<media:content expression="full" type="image/jpg" url="'.htmlspecialchars($photo->getImageURI(), ENT_COMPAT, 'UTF-8').'">';
		        $media .= '<media:description />';
		        $media .= '<media:rating scheme="urn:simple">nonadult</media:rating>';
		        $media .= '<media:adult>false</media:adult>';
		        $media .= '<media:thumbnail url="'.htmlspecialchars($photo->getThumbURI(), ENT_COMPAT, 'UTF-8').'" width="64" height="64" />';
		        $media .= '<media:title>ki1.jpg</media:title>';
		      	$media .= '</media:content>';
		      	
		      	$guidData['<guid>'.$item->guid.'</guid>'] = $media; 
		      	
				$document->addItem( $item );
			}
			
			$content = $document->render();
			
			foreach($guidData as $key => $val){
				$content = str_replace($key, $val, $content);
			}
			
			// Add media namespace declaration
			$content = str_replace('<rss ', '<rss xmlns:media="http://search.yahoo.com/mrss/" ', $content);
			
			echo $content;
			exit;
			
		}
		else
		{
			$mainframe->redirect( 'index.php?option=com_community&view=photos', JText::_('COM_COMMUNITY_PHOTOS_USER_ACCOUNT_IS_BANNED') );
		}
	}
	
	/**
	 * Display the photo thumbnails from an album
	 **/
	public function album()
	{
		$document	= JFactory::getDocument();
		$css		= rtrim( JURI::root() , '/' ) . '/components/com_community/assets/album.css';
		$document->addStyleSheet($css);	
		$groupId	= JRequest::getInt( 'groupid' , '' , 'REQUEST' );
		
		if(!empty($groupId) )
		{
			$this->_groupAlbum();
		}
		else
		{
			$this->_userAlbum();
		}
	}

	public function _groupPhoto()
	{
		$mainframe =& JFactory::getApplication();
		$document = JFactory::getDocument();


		// Get necessary properties and load the libraries
		CFactory::load( 'models' , 'photos' );
		CFactory::load('helpers', 'friends');

		$my			= CFactory::getUser();
 		$model		= CFactory::getModel('photos');
 		$groupId	= JRequest::getVar('groupid' , '' , 'GET');
 		$albumId	= JRequest::getVar('albumid' , '' , 'GET');
 		$defaultId	= JRequest::getVar('photoid' , '' , 'GET');

 		if( empty( $albumId ) )
 		{
 			echo JText::_('COM_COMMUNITY_PHOTOS_NO_ALBUMID_ERROR');
 			return;
		}

		// Load the album table
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );
		
		// Set pathway
        $pathway 	=& $mainframe->getPathway();
		$pathway->addItem( $album->name, '' );
		
		CFactory::load( 'models' , 'groups' );
		$group		=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $groupId );
		
		CFactory::load( 'helpers' , 'group' );
		
		//@rule: Do not allow non members to view albums for private group
		if(!CGroupHelper::allowViewMedia($group->id))
		{
			// Set document title
			$document->setTitle( JText::_('COM_COMMUNITY_RESTRICTED_ACCESS') );
			$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_RESTRICTED_ACCESS', 'notice'));
			
			echo JText::_('COM_COMMUNITY_GROUPS_PHOTO_MEMBER_PERMISSION');
			return;			
		}
		
		// Get list of photos and set some limit to be displayed.
		// @todo: make limit configurable? set to 1000, unlimited?
		$photos		= $model->getPhotos( $albumId, 1000);

		// Set document title
		CFactory::load( 'helpers' , 'string' );
		$document->setTitle($album->name);

		// @checks: Test if album doesnt have any default photo id. We need to get the first row
		// of the photos to be the default
		if($album->photoid == '0')
		{
			$album->photoid	= ( count( $photos ) >= 1 ) ? $photos[0]->id : '0';
		}

		// Try to see if there is any photo id in the query
		$defaultId		= ( !empty($defaultId) ) ? $defaultId : $album->photoid;

		// Load the default photo
		$photo			=& JTable::getInstance( 'Photo' , 'CTable' );
		$photo->load( $defaultId );

		// If default has an id of 0, we need to tell the template to dont process anything
		$default		= ($photo->id == 0 ) ? false : $photo;

		CFactory::load( 'helpers' , 'owner' );	
		
		//friend list for photo tag
		CFactory::load( 'libraries' , 'phototagging' );
		$tagging	= new CPhotoTagging();
								
		
		for($i=0; $i < count($photos); $i++)
		{
			// Convert the generic object row into CTablePhoto object 
			$item =& JTable::getInstance( 'Photo' , 'CTable' );
			$item->bind($photos[$i]);
			$photos[$i] = $item;
			
			$row			=& $photos[$i];
			$taggedList		= $tagging->getTaggedList($row->id);
							
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
				
				$tagItem->user			= $tagUser;
				$tagItem->canRemoveTag	= $canRemoveTag;
			}
			$row->tagged	= $taggedList;
		}
					
		// for photo tagging. only allow to tag members
		$groupModel 		= CFactory::getModel( 'groups' );
		$groupMemberIds		= $groupModel->getMembersId($groupId, true);
		$friends			= array();
		
		foreach($groupMemberIds as $grpMemberId)
		{
			if($my->id != $grpMemberId)
			{
				$memberUser		= CFactory::getUser($grpMemberId);			
				$friends[]		= $memberUser;
			}
		}
		if(COwnerHelper::isCommunityAdmin() || $group->isAdmin( $my->id ) || $group->isMember( $my->id )) array_unshift($friends, $my);
		
		CFactory::load( 'helpers' , 'group' );			
		$allowManagePhotos = CGroupHelper::allowManagePhoto($groupId);	
		
		// Only add these links when there are photos in the album
		if( $allowManagePhotos && $default )
		{
			$this->addSubmenuItem('' , JText::_('COM_COMMUNITY_PHOTOS_SET_AS_ALBUM_COVER'), "setPhotoAsDefault();" , true);
			$this->addSubmenuItem('' , JText::_('COM_COMMUNITY_PHOTOS_DELETE'), "removePhoto();", true);
		}

		// Show wall contents
		CFactory::load( 'helpers' , 'friends' );
		
		// Load up required objects.
		$friendModel 	= CFactory::getModel( 'friends' );
		$config			= CFactory::getConfig();
		$showWall		= false;
		$allowTag		= false;

		//check if we can allow the current viewing user to tag the photos
		if($group->isMember( $my->id ) || $group->isAdmin( $my->id ) || COwnerHelper::isCommunityAdmin())
		{
			$showWall	= true;
			$allowTag = true;
		}

		$tmpl			= new CTemplate();
		CFactory::load( 'libraries' , 'bookmarks' );
		$bookmarks		= new CBookmarks(CRoute::getExternalURL( 'index.php?option=com_community&view=photos&task=album&albumid=' . $album->id . '&groupid=' . $group->id ));
		$bookmarksHTML	= $bookmarks->getHTML();
		
		$photoCreator = CFactory::getUser($photo->creator);
		
		$tmpl->set( 'bookmarksHTML'	, $bookmarksHTML );
		$tmpl->set( 'showWall'		, $showWall );
		$tmpl->set( 'allowTag'		, $allowTag );
		$tmpl->set( 'isOwner' 		, $group->isAdmin($my->id) );
		$tmpl->set( 'photos' 		, $photos );
		$tmpl->set( 'default'		, $default );
		$tmpl->set( 'album'			, $album);
		$tmpl->set( 'friends'		, $friends);
		$tmpl->set( 'config'		, $config);
		$tmpl->set( 'photoCreator'	, $photoCreator);
		
		echo $tmpl->fetch('photos.photo');
	}
	
	public function _userPhoto()
	{
		$mainframe =& JFactory::getApplication();
		$document = JFactory::getDocument();


		// Get necessary properties and load the libraries
		CFactory::load( 'models' , 'photos' );
		CFactory::load('helpers', 'friends');

		$my			= CFactory::getUser();
 		$model		= CFactory::getModel('photos');
 		$albumId	= JRequest::getVar('albumid' , '' , 'GET');
 		$defaultId	= JRequest::getVar('photoid' , '' , 'GET');

 		if( empty( $albumId ) )
 		{
 			echo JText::_('COM_COMMUNITY_PHOTOS_NO_ALBUMID_ERROR');
 			return;
		}

		// Load the album table
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );
		

		// Since the URL might not contain userid, we need to get the user object from the creator
		$user		= CFactory::getUser( $album->creator );
		
		if(!$user->block || COwnerHelper::isCommunityAdmin($my->id))
		{
			// Set the current user's active profile
			CFactory::setActiveProfile( $album->creator );
			
			// Get list of photos and set some limit to be displayed.
			// @todo: make limit configurable? set to 1000, unlimited?
			$photos		= $model->getPhotos( $albumId, 1000);
			$pagination	= $model->getPagination();
	
			CFactory::load( 'helpers' , 'pagination' );
			// @todo: make limit configurable?
			$paging		= CPaginationLibrary::getLinks( $pagination , 'photos,ajaxPagination' , $albumId , 10 );
	
			// Set document title
			CFactory::load( 'helpers' , 'string' );
			$document->setTitle($album->name);
	
			// @checks: Test if album doesnt have any default photo id. We need to get the first row
			// of the photos to be the default
			if($album->photoid == '0')
			{
				$album->photoid	= ( count( $photos ) >= 1 ) ? $photos[0]->id : '0';
			}
	
			// Try to see if there is any photo id in the query
			$defaultId		= ( !empty($defaultId) ) ? $defaultId : $album->photoid;
	
			// Load the default photo
			$photo			=& JTable::getInstance( 'Photo' , 'CTable' );
			$photo->load( $defaultId );
	
			// If default has an id of 0, we need to tell the template to dont process anything
			$default		= ($photo->id == 0 ) ? false : $photo;
	
			// Load User params
			$params			=& $user->getParams();
	
			// site visitor
			$relation = 10;
	
			// site members
			if( $my->id != 0 )
				$relation = 20;
	
			// friends
			if( CFriendsHelper::isConnected($my->id, $user->id) )
				 $relation = 30;
	
			// mine
			if( COwnerHelper::isMine($my->id, $user->id) ){
				 $relation = 40;
			}
	
			if( $my->id != $user->id )
			{
				$this->attachMiniHeaderUser( $user->id );
			}

			CFactory::load( 'helpers' , 'owner' );
			
			// @todo: respect privacy settings
			if( $relation < $params->get('privacyPhotoView') && !COwnerHelper::isCommunityAdmin() )
			{
				echo JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN');
				return;
			}
	
			CFactory::load( 'helpers' , 'owner' );	
			
			//friend list for photo tag
			CFactory::load( 'libraries' , 'phototagging' );
			$tagging	= new CPhotoTagging();
	
			for($i=0; $i < count($photos); $i++)
			{
				$item = JTable::getInstance( 'Photo' , 'CTable' );
				$item->bind($photos[$i]);
				$photos[$i] = $item;
				$row			=& $photos[$i];
				$taggedList		= $tagging->getTaggedList($row->id);				
								
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
					
					$tagItem->user			= $tagUser;
					$tagItem->canRemoveTag	= $canRemoveTag;									
					
				}
				$row->tagged	= $taggedList;			
			}
						
			$friendModel 		= CFactory::getModel( 'friends' );
			$friends			= $friendModel->getFriends( $my->id , '' , false );
			array_unshift($friends, $my);

			// Show wall contents
			CFactory::load( 'helpers' , 'friends' );
			
			// Load up required objects.
			$user			= CFactory::getUser( $photo->creator );
			$config			= CFactory::getConfig();
			
			$isConnected	= CFriendsHelper::isConnected( $my->id , $user->id );
			$isMe			= COwnerHelper::isMine( $my->id , $user->id );
			$showWall		= false;
			$allowTag		= false;
			
			// Check if user is really allowed to post walls on this photo.
			if( ($isMe) || (!$config->get('lockprofilewalls')) || ( $config->get('lockprofilewalls') && $isConnected ) )
			{
				$showWall	= true;
			}
			
			//check if we can allow the current viewing user to tag the photos
			if(($isMe) || $isConnected)
			{
				$allowTag = true;
			}

			
			$tmpl	= new CTemplate();
			CFactory::load( 'libraries' , 'bookmarks' );
			$bookmarks		=new CBookmarks(CRoute::getExternalURL( 'index.php?option=com_community&view=photos&task=album&albumid=' . $album->id . '&userid=' . $user->id ));
			$bookmarksHTML	= $bookmarks->getHTML();
			
			$tmpl->set( 'showWall'		, $showWall );
			$tmpl->set( 'allowTag'		, $allowTag );
			$tmpl->set( 'isOwner' 		, COwnerHelper::isMine($my->id, $user->id));
			$tmpl->set( 'photos' 		, $photos );
			$tmpl->set( 'pagination'	, $paging );
			$tmpl->set( 'default'		, $default );
			$tmpl->set( 'album'			, $album);
			$tmpl->set( 'config'		, $config);
	
			//echo $tmpl->fetch('photos.photo');
		}
		else
		{
	        CFactory::load('helpers' , 'owner' );
			$tmpl	= new CTemplate();
			echo $tmpl->fetch('profile.blocked');
			return;
		}	
	}
	
	/**
	 * Displays single photo view
	 *
	 **/
	public function photo()
	{
		$mainframe	=& JFactory::getApplication();
		$document	= JFactory::getDocument();

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

 		if( empty( $albumId ) )
 		{
 			echo JText::_('COM_COMMUNITY_PHOTOS_NO_ALBUMID_ERROR');
 			return;
		}
		
		CFactory::load( 'models' , 'photos' );
		// Load the album table
		$album		=& JTable::getInstance( 'Album' , 'CTable' );
		$album->load( $albumId );
		
		if($album->type == PHOTOS_GROUP_TYPE)
		{
			$this->_groupPhoto();
		}
		else
		{			
			$this->_userPhoto();
		}
	}
	
	public function _getAllAlbums()
	{
		$mainframe  = JFactory::getApplication();
		$document	= JFactory::getDocument();
		
		$my			= CFactory::getUser();
		$userId		= JRequest::getInt( 'userid' , '' );

		CFactory::load( 'models', 'groups' );
		
 		$model		= CFactory::getModel( 'photos' );
 		$groupId	= JRequest::getInt( 'groupid' , '' , 'REQUEST' );
		$type		= PHOTOS_USER_TYPE;
		
		if( !empty($userId) ){  
		$user		= CFactory::getUser($userId);
            			
    		// Set document title
		CFactory::load( 'helpers' , 'owner' );
		$blocked	= $user->isBlocked();
    
    		if( $blocked && !COwnerHelper::isCommunityAdmin() )
    		{
    			$tmpl	= new CTemplate();
    			echo $tmpl->fetch('profile.blocked');
    			return;
    		}  
		
    		if($my->id == $user->id){
    			$title	= JText::_('COM_COMMUNITY_PHOTOS_MY_PHOTOS');
    		}else{
    			$title	= JText::sprintf('COM_COMMUNITY_PHOTOS_USER_PHOTOS_TITLE', $user->getDisplayName());
            }
            
        }else{
                $title  = JText::_('COM_COMMUNITY_PHOTOS_ALL_PHOTOS_TITLE');
        }

 		if( !empty($groupId) )
 		{ 
			$title  = JText::_('COM_COMMUNITY_SUBSCRIBE_TO_GROUP_PHOTOS_FEEDS');
 			$group	=& JTable::getInstance( 'Group' , 'CTable' );
 			$group->load( $groupId );  
			//@rule: Do not allow non members to view albums for private group
			if( $group->approvals == COMMUNITY_PRIVATE_GROUP && !$group->isMember( $my->id ) && !$group->isAdmin( $my->id ) )
			{
				echo JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN');
				return;			
			} 
 			$type		= PHOTOS_GROUP_TYPE; 
 			$albumsData	= $model->getGroupAlbums( $groupId, false, false, $mainframe->getCfg('feed_limit') );
		}
		else
		{
		    // Get ALL albums or USER albums
			if( !empty($userId) )
			    $albumsData	= $model->_getAlbums( $userId, $type, false, false, $mainframe->getCfg('feed_limit') );
			else    
			    $albumsData	= $model->getAllAlbums( $userId, $mainframe->getCfg('feed_limit') );
		}

		CFactory::load( 'libraries' , 'featured' );
		$featured		= new CFeatured( FEATURED_ALBUMS );
		$featuredAlbums	= $featured->getItemIds();
		$featuredList	= array();
		
		// Prepare feeds
		$document->setTitle( $title );
		
		foreach($albumsData as $album )
		{
			$table			=& JTable::getInstance( 'Album' , 'CTable' );
			$table->bind($album);
			$table->thumbnail	= $table->getCoverThumbPath();

			$albumAuthor = CFactory::getUser($table->creator);
			
			$description  = '<img src="' . $table->thumbnail . '" alt="" />&nbsp;';
			$description .= $albumAuthor->getDisplayName(). ' posted '. $album->count . ' photos ';
            		
			//print_r($albumAuthor); exit;
			$item = new JFeedItem();
			$item->title 		= $table->name;
			$item->link 		= CRoute::_('index.php?option=com_community&view=photos&task=album&albumid='.$album->id.'&userid='.$albumAuthor->id);
			$item->description 	= $description . $table->description;
			$item->date			= $table->created;
			$item->author		= $albumAuthor->getDisplayName();
			
			// Make sure url is absolute
			$item->description  = CString::str_ireplace('href="/', 'href="'. JURI::base(), $item->description);

			$document->addItem( $item );
			
		}
		
		$content = $document->render();
    }
}