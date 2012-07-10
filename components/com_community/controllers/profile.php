<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class CommunityProfileController extends CommunityBaseController
{
	/**
	 * Edit a user's profile	
	 * 	 	
	 * @access	public
	 * @param	none 
	 */
	private $_icon = '';

	public function editProfileWall( $wallId )
	{
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'helpers' , 'time');

		$my		= CFactory::getUser();
		$wall	=& JTable::getInstance( 'Wall' , 'CTable' );
		$wall->load( $wallId );

		// @rule: We only allow editing of wall in 15 minutes
		$now		= JFactory::getDate();
		$interval	= CTimeHelper::timeIntervalDifference( $wall->date , $now->toMySQL() );
		$interval	= abs( $interval );
		
		if( ( COwnerHelper::isCommunityAdmin() || $my->id == $wall->post_by ) && ( COMMUNITY_WALLS_EDIT_INTERVAL > $interval ) )
		{
			return true;
		}
		return false;
	}
	
	public function ajaxConfirmRemoveAvatar()
	{
		$response	= new JAXResponse();
		$my			= CFactory::getUser();
		
		$tmpl		= new CTemplate();
		$content	= JText::_('COM_COMMUNITY_CONFIRM_REMOVE_PROFILE_PICTURE');
		
		$formAction	= CRoute::_('index.php?option=com_community&view=profile&task=removeAvatar' );
		$actions	= '<form action="' . $formAction . '" method="POST">';
		$actions	.=	'<input class="button" type="submit" value="' . JText::_('COM_COMMUNITY_YES_BUTTON') . '" />';
		$actions	.=	'&nbsp;<button class="button" onclick="cWindowHide();return false;">' . JText::_('COM_COMMUNITY_NO_BUTTON') . '</button>';
		$actions	.= '</form>';

		$response->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_REMOVE_PROFILE_PICTURE') );
		$response->addScriptCall('cWindowAddContent', $content, $actions);

		return $response->sendResponse();
	}
	
	public function removeAvatar()
	{
		$my				= CFactory::getUser();
		$mainframe		=& JFactory::getApplication();
		
		if( $my->id == 0 )
		{
			echo JText::_('COM_COMMUNITY_ACCESS_FORBIDDEN');
			return;
		}
		
		$model		= CFactory::getModel( 'user' );
		$model->removeProfilePicture( $my->id , 'avatar' );
		$model->removeProfilePicture( $my->id , 'thumb' );
		
		$mainframe->redirect( CRoute::_( 'index.php?option=com_community&view=profile' , false ) , JText::_('COM_COMMUNITY_PROFILE_PICTURE_REMOVED') );
	}
	
	public function ajaxPlayProfileVideo($videoid=null, $userid=0)
	{
                $filter = JFilterInput::getInstance();
                $videoid = $filter->clean($videoid, 'int');
                $userid = $filter->clean($userid, 'int');

		$objResponse	= new JAXResponse();

		// Get necessary properties and load the libraries
		$my		=   CFactory::getUser();
                
		$video		=   JTable::getInstance( 'Video' , 'CTable' );
		$video->load($videoid);

		if(!empty($video->id))
		{
			// Check video permission
			if (!$this->isPermitted($my->id, $video->creator, $video->permissions))
			{
				switch ($video->permissions)
				{
					case PRIVACY_PRIVATE :
						$content = JText::_('COM_COMMUNITY_VIDEOS_OWNER_ONLY');
						break;
					case PRIVACY_FRIENDS :
						$owner	= CFactory::getUser($video->creator);
						$content = JText::sprintf('COM_COMMUNITY_VIDEOS_FRIEND_PERMISSION_MESSAGE', $owner->getDisplayName());
						break;
					default:
						$content = JText::_('COM_COMMUNITY_VIDEOS_LOGIN_MESSAGE');
						break;
				}
				
				$objResponse->addScriptCall('cWindowShow', '', $title, 430, 80);
			}
			else
			{
				$title		=	$video->getTitle();
				$content	=	$video->getPlayerHTML(null,null,true);

				$objResponse->addScriptCall('cWindowShow', '', $content, $video->getWidth()+30, $video->getHeight()+30);
				//$objResponse->addScriptCall('cWindowResize', $video->getHeight()+30);
			}
		}
		else
		{
			$content	= JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_NOT_EXIST');

                        if( COwnerHelper::isMine( $my->id, $userid ) ){
                                    $redirectURL	= CRoute::_('index.php?option=com_community&view=profile&task=linkVideo' , false );
                                    $action         = '<input type="button" class="button" onclick="cWindowHide(); window.location=\''.$redirectURL.'\';" value="' . JText::_('COM_COMMUNITY_VIDEOS_ADD_PROFILE_VIDEO') . '"/>';

                                    $objResponse->addScriptCall('cWindowActions', $action);
                        }
                        
                        $objResponse->addScriptCall('cWindowShow', '', $title, 430, 80);
		}

		$action = '<button  class="button" onclick="javascript:cWindowHide();">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';
		$objResponse->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO'));
		$objResponse->addScriptCall('cWindowAddContent',$content, $action);
		
		return $objResponse->sendResponse();
	}

	// Confirm before change video
	public function ajaxConfirmLinkProfileVideo($id)
	{
                $filter = JFilterInput::getInstance();
                $id = $filter->clean($id, 'int');

		$objResponse	= new JAXResponse();

		$header		= JText::_('COM_COMMUNITY_VIDEOS_EDIT_PROFILE_VIDEO');
		$message    = JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_CONFIRM_LINK');
		$actions	= '<button  class="button" onclick="joms.videos.linkProfileVideo(' . $id . ');">' . JText::_('COM_COMMUNITY_YES_BUTTON') . '</button>';
		$actions   .= '&nbsp;<button class="button" onclick="cWindowHide();">' . JText::_('COM_COMMUNITY_NO_BUTTON') . '</button>';

		$objResponse->addAssign('cwin_logo', 'innerHTML', $header);
		$objResponse->addScriptCall('cWindowAddContent', $message, $actions);

		return $objResponse->sendResponse();
    }

	// Store to database and reload page
	public function ajaxLinkProfileVideo($videoid)
	{
		$filter = JFilterInput::getInstance();
		$videoid = $filter->clean($videoid, 'int');

		$objResponse	= new JAXResponse();

		$my				= CFactory::getUser();

		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}

		$params			=	$my->getParams();
		$params->set('profileVideo', $videoid);
		$my->save('params');

		$header  = JText::_('COM_COMMUNITY_VIDEOS_EDIT_PROFILE_VIDEO');
		$message = JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_LINKED');
		$actions = '<button  class="button" onclick="window.location.reload()">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';
	
		$objResponse->addAssign('cwin_logo', 'innerHTML', $header);
		$objResponse->addScriptCall('cWindowAddContent', $message, $actions);

		return $objResponse->sendResponse();
    }

	// Need confirmation before remove link
	public function ajaxRemoveConfirmLinkProfileVideo($userid, $videoid)
	{
                $filter = JFilterInput::getInstance();
                $videoid = $filter->clean($videoid, 'int');
                $userid = $filter->clean($userid, 'int');

		$objResponse	= new JAXResponse();

		$my				= CFactory::getUser();

		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}

		$header		= JText::_('COM_COMMUNITY_VIDEOS_REMOVE_PROFILE_VIDEO');
		$message    = JText::_('COM_COMMUNITY_VIDEOS_REMOVE_PROFILE_VIDEO_CONFIRM_LINK');
		$action		= '<button  class="button" onclick="joms.videos.removeLinkProfileVideo(' . $userid . ', ' . $videoid . ');">' . JText::_('COM_COMMUNITY_YES_BUTTON') . '</button>';
		$action    .= '&nbsp;<button class="button" onclick="cWindowHide();">' . JText::_('COM_COMMUNITY_NO_BUTTON') . '</button>';

	
		$objResponse->addScriptCall('cWindowAddContent', $message, $action);

		return $objResponse->sendResponse();
	}

	// Remove link
	public function ajaxRemoveLinkProfileVideo($userid, $videoid)
	{
                $filter = JFilterInput::getInstance();
                $videoid = $filter->clean($videoid, 'int');
                $userid = $filter->clean($userid, 'int');
                
		$objResponse	= new JAXResponse();

		$my				= CFactory::getUser();

		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}

		$user	=	CFactory::getUser($userid);

		// Set params to default(0 for no profile video)
		$params			=	$user->getParams();
		$params->set('profileVideo', 0);
		$user->save('params');

		$header		= JText::_('COM_COMMUNITY_VIDEOS_EDIT_PROFILE_VIDEO');
		$message	= JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_REMOVED');
		$message   .= '<br />' . JText::_('COM_COMMUNITY_VIDEOS_DELETE_VIDEO_INSTRUCTION');

		$actions = '<button  class="button" onclick="joms.videos.deleteVideo(' . $videoid . ')">' . JText::_('COM_COMMUNITY_VIDEOS_DELETE_VIDEO') . '</button>';
		$actions .=	'&nbsp;<button  class="button" onclick="window.location.reload()">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';

		$objResponse->addAssign('cwin_logo', 'innerHTML', $header);
		$objResponse->addScriptCall('cWindowAddContent', $message, $actions);

		return $objResponse->sendResponse();
	}
    
	public function ajaxIphoneProfile()
    {		
		$document		= JFactory::getDocument();
		
		$viewType	= $document->getType(); 		 	
		$view		=& $this->getView( 'profile', '', $viewType);
						
		
		$html = '';
		
		ob_start();				
		$this->profile();				
		$content = ob_get_contents();
		ob_end_clean();
		
		$tmpl			= new CTemplate();
		$tmpl->set('toolbar_active', 'profile');
		$simpleToolbar	= $tmpl->fetch('toolbar.simple');		
		
		$objResponse->addAssign('social-content', 'innerHTML', $simpleToolbar . $content);
		return $objResponse->sendResponse();		    	
    }

	/**
	 *	Ajax method to block user from the site. This method is only used by site administrators
	 *	
	 *	@params	$userId	int	The user id that needs to be blocked
	 *	@params	$isBlocked	boolean	Whether the user is already blocked or not. If it is blocked, system should unblock it.	 	 	 
	 **/	 	
	public function ajaxBanUser( $userId , $isBlocked )
	{
		$filter = JFilterInput::getInstance();
		$userId = $filter->clean($userId, 'int');
		$isBlocked = $filter->clean($isBlocked, 'bool');

		$user	= CFactory::getUser( $userId );
		
		$objResponse	= new JAXResponse();
		$title			= '';
		$my				= CFactory::getUser();
		CFactory::load( 'helpers' , 'owner' );
		
		if($my->id == 0)
		{
		   	return $this->ajaxBlockUnregister();
		}
		
		// @rule: Only site admin can access this function.
		if( $my->authorise('community.ban', 'profile.'.$userId , $user) )
		{
			$isSuperAdmin	= COwnerHelper::isCommunityAdmin( $user->id );

			// @rule: Do not allow to block super administrators.
			if( $isSuperAdmin )
			{
				$content = '<div>' . JText::_('COM_COMMUNITY_NOT_ALLOWED_TO_BAN_SUPER_ADMIN') . '</div>';
				$actions = '<input type="button" class="button" onclick="cWindowHide();return false;" name="cancel" value="'.JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON').'" />';
			}
			else
			{
				ob_start();
				if( !$isBlocked )
				{
				?>
				<div><?php echo JText::sprintf( 'COM_COMMUNITY_BAN_USER_CONFIRMATION' , $user->getDisplayName() ); ?></div>
				<?php
					$title	= JText::_('COM_COMMUNITY_BAN_USER');
				}
				else
				{
				?>
				<div><?php echo JText::sprintf( 'COM_COMMUNITY_UNBAN_USER_CONFIRMATION' , $user->getDisplayName() ); ?></div>
				<?php
				$title	= JText::_('COM_COMMUNITY_UNBAN_USER');
				}
				$content		= ob_get_contents();
				ob_end_clean();
				
				$objResponse->addAssign('cwin_logo', 'innerHTML', $title );
	
				$formAction	= CRoute::_('index.php?option=com_community&view=profile&task=banuser' , false );
				$actions  = '<form name="cancelRequest" action="' . $formAction . '" method="POST">';
				$actions .= '<input type="hidden" name="userid" value="' . $userId . '" />';
				$actions .= ( $isBlocked ) ? '<input type="hidden" name="blocked" value="1" />' : '';
				$actions .= '<input type="submit" value="' . JText::_('COM_COMMUNITY_YES_BUTTON') . '" class="button" />&nbsp;';
				$actions .= '<input type="button" class="button" onclick="cWindowHide();return false;" name="cancel" value="'.JText::_('COM_COMMUNITY_NO_BUTTON').'" />';
				$actions .= '</form>';
			}
		}

		$objResponse->addScriptCall('cWindowAddContent', $content, $actions);
						
		return $objResponse->sendResponse();
	}

	/**
	 *	Ajax method to remove user's picture from the site. This method is only used by site administrators
	 *	
	 *	@params	$userId	int	The user id that needs to have their picture removed.	 	 	 
	 **/
	public function ajaxRemovePicture( $userId )
	{
                $filter = JFilterInput::getInstance();
                $userId = $filter->clean($userId, 'int');

		$objResponse	= new JAXResponse();

		$my				= CFactory::getUser();
		CFactory::load( 'helpers' , 'owner' );
		
		if($my->id == 0)
		{
		   	return $this->ajaxBlockUnregister();
		}		
		
		// @rule: Only site admin can access this function.
		if( COwnerHelper::isCommunityAdmin( $my->id ) )
		{
			ob_start();
			?>
				<div><?php echo JText::_( 'COM_COMMUNITY_REMOVE_AVATAR_CONFIRMATION'); ?></div>
			<?php
			$content		= ob_get_contents();
			ob_end_clean();
	
			$title	= JText::_('COM_COMMUNITY_REMOVE_PROFILE_PICTURE');
			
			$formAction	= CRoute::_('index.php?option=com_community&view=profile&task=removepicture' , false );
			$actions  = '<form name="cancelRequest" action="' . $formAction . '" method="POST">';
			$actions .= '<input type="hidden" name="userid" value="' . $userId . '" />';
			$actions .= '<input type="submit" value="' . JText::_('COM_COMMUNITY_YES_BUTTON') . '" class="button" />&nbsp;';
			$actions .= '<input type="button" class="button" onclick="cWindowHide();return false;" value="'.JText::_('COM_COMMUNITY_NO_BUTTON').'" />';
			$actions .= '</form>';

			$objResponse->addAssign('cwin_logo', 'innerHTML', $title);
			$objResponse->addScriptCall('cWindowAddContent', $content, $actions);
		}
		return $objResponse->sendResponse();
	}

	public function ajaxUploadNewPicture($userId)
	{
                $filter = JFilterInput::getInstance();
                $userId = $filter->clean($userId, 'int');
                
		$objResponse	= new JAXResponse();

		$my				= CFactory::getUser();
		CFactory::load( 'helpers' , 'owner' );
		$this->cacheClean( array(COMMUNITY_CACHE_TAG_ACTIVITIES , COMMUNITY_CACHE_TAG_FRONTPAGE) );
		if(!isCommunityAdmin())
		{
		   	return $this->ajaxBlockUnregister();
		}

		$title	= JText::_('COM_COMMUNITY_CHANGE_AVATAR');

		$formAction = CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar', false);

		$config			= CFactory::getConfig();
		$uploadLimit	= (double) $config->get('maxuploadsize');
		$uploadLimit	.= 'MB';

		$content	 =	'<form name="jsform-profile-ajaxuploadnewpicture" action="' . $formAction . '" id="uploadForm" method="post" enctype="multipart/form-data">';
		$content	.=	'<input class="inputbox button" type="file" id="file-upload" name="Filedata" />';
		$content	.=	'<input class="button" size="30" type="submit" id="file-upload-submit" value="' . JText::_('COM_COMMUNITY_BUTTON_UPLOAD_PICTURE') . '">';
		$content	.=	'<input type="hidden" name="action" value="doUpload" />';
		$content	.=	'<input type="hidden" name="userid" value="' . $userId . '" />';
		$content	.=	'</form>';
		
		if( $uploadLimit != 0 )
		{
			$content	.=	'<p class="info">' . JText::sprintf('COM_COMMUNITY_MAX_FILE_SIZE_FOR_UPLOAD' , $uploadLimit ) . '</p>';
		}
		
		$objResponse->addAssign('cwin_logo', 'innerHTML', $title);		
		$objResponse->addScriptCall('cWindowAddContent', $content);
		
		return $objResponse->sendResponse();
	}

	public function ajaxUpdateURL($userId)
	{
                $filter = JFilterInput::getInstance();
                $userId = $filter->clean($userId, 'int');

		$objResponse	= new JAXResponse();

		
		CFactory::load( 'helpers' , 'owner' );

		if( !COwnerHelper::isCommunityAdmin() )
		{
			echo JText::_('COM_COMMUNITY_RESTRICTED_ACCESS');
			return;
		}
		$tmpl		= new CTemplate();
		$user		= CFactory::getUser( $userId );
		
		$juriRoot = JURI::root(false);
		$juriPathOnly = JURI::root(true);
		$juriPathOnly = rtrim($juriPathOnly, '/');
		$profileURL	= rtrim( str_replace( $juriPathOnly , '', $juriRoot ) , '/' );

		$profileURL .= CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->id, false);
		$alias		= $user->getAlias();

		$inputHTML = '<input id="alias" name="alias" class="inputbox" type="alias" value="'. $alias.'" />';
		$prefixURL		= str_replace($alias, $inputHTML, $profileURL );
		
		// For backward compatibility issues, as we changed from ID-USER to ID:USER in 2.0,
		// we also need to test older urls.
		if( $prefixURL == $profileURL )
		{
			$prefixURL		= CString::str_ireplace( CString::str_ireplace( ':' , '-' , $alias ), $inputHTML, $profileURL );
		}

		$tmpl->set( 'prefixURL'	, $prefixURL );
		$tmpl->set( 'user'		, $user );
		$content	= $tmpl->fetch( 'ajax.updateurl' );

		$actions  = '<input type="button" value="' . JText::_('COM_COMMUNITY_UPDATE_BUTTON') . '" class="button" onclick="joms.jQuery(\'#jsform-profile-ajaxupdateurl\').submit();" />';
		$actions .= '<input type="button" class="button" onclick="cWindowHide();return false;" value="'.JText::_('COM_COMMUNITY_CANCEL_BUTTON').'" />';

		$objResponse->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_PROFILE_CHANGE_ALIAS'));
		$objResponse->addScriptCall('cWindowAddContent', $content, $actions);
		
		return $objResponse->sendResponse();
	}

	/**
	 * Resize user's thumbnail from the source image
	 * 
	 * @param Object $imgObj
	 * @param String $src
	 *
	 */
	public function ajaxUpdateThumbnail($sourceX, $sourceY, $width, $height, $hideSave = false )
	{
                $filter = JFilterInput::getInstance();
                $sourceX = $filter->clean($sourceX, 'float');
                $sourceY = $filter->clean($sourceY, 'float');
                $width = $filter->clean($width, 'float');
                $height = $filter->clean($height, 'float');
                $hideSave = $filter->clean($hideSave, 'bool');

		// Fetch the thumbnail remotely. This is necessary since the user
		// profile picture might not be stored locally
		$objResponse	= new JAXResponse();
		$my		= CFactory::getUser();
		$guest	= CFactory::getUser(0);

		if($my->id && $guest->_avatar != $my->_avatar)
		{
			// Resize it
			$srcPath = JPATH_ROOT . DS.$my->_avatar;
			$destPath = JPATH_ROOT .DS. $my->_thumb;

			$srcPath = str_replace('/', DS, $srcPath);
			$destPath = str_replace('/', DS, $destPath);

			$info = getimagesize($srcPath);

			$destType = $info['mime'];

			$destWidth = COMMUNITY_SMALL_AVATAR_WIDTH;
			$destHeight = COMMUNITY_SMALL_AVATAR_WIDTH;

			$currentWidth = $width;
			$currentHeight = $height;

			// @todo: we should just delete the old one and use a new path
			CFactory::load('helpers', 'image');
			CImageHelper::resize($srcPath, $destPath, $destType, $destWidth, $destHeight, $sourceX, $sourceY, $currentWidth, $currentHeight);
			
			$connectModel = CFactory::getModel('connect');
			
			// For facebook user, we need to add the watermark back on
			if($connectModel->isAssociated( $my->id ) && $config->get('fbwatermark'))
			{
				list( $watermarkWidth , $watermarkHeight ) = getimagesize( FACEBOOK_FAVICON );
				CImageHelper::addWatermark( $destPath , $destPath , $destType , FACEBOOK_FAVICON , ( $destWidth - $watermarkWidth ), ( $destHeight - $watermarkHeight) );
			}
			
			$objResponse->addScriptCall('refreshThumbnail');
			
		}
		else
		{
			return $this->ajaxBlockUnregister();
		}
		return $objResponse->sendResponse();
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
		if ( $permissions == 0) return true; // public

		// Load libraries
		CFactory::load('helpers', 'friends');
		CFactory::load('helpers', 'owner');

		if( COwnerHelper::isCommunityAdmin() ) {
			return true;
		}

		$relation	= 0;

		if( $myid != 0 )
			$relation = 20; // site members

		if( CFriendsHelper::isConnected($myid, $userid) )
			$relation	= 30; // friends

		if( COwnerHelper::isMine($myid, $userid) ){
			$relation	= 40; // mine
		}

		if( $relation >= $permissions ) {
			return true;
		}

		return false;
	}

	/**
	 * Ban user from the system
	 **/
	public function banuser()
	{
		CFactory::load( 'helpers' , 'owner' );
		
		$message	= '';
		$userId		= JRequest::getInt( 'userid' , '' , 'POST' );
		$blocked	= JRequest::getVar( 'blocked' , 0 , 'POST' );
		
		$my			= CFactory::getUser();
		$url		= CRoute::_('index.php?option=com_community&view=profile&userid=' . $userId , false );
		$mainframe	=& JFactory::getApplication();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}		
		
		if( COwnerHelper::isCommunityAdmin() )
		{
			$user	= CFactory::getUser( $userId );
			
			if( $user->id )
			{
				$user->block	= ( $blocked == 1 ) ? 0 : 1;
				$user->save();
				
				$message		= ( $blocked == 1 ) ? JText::_('COM_COMMUNITY_USER_UNBANNED') : JText::_('COM_COMMUNITY_USER_BANNED');
			}
			else
			{
				$message	= JText::_('COM_COMMUNITY_INVALID_PROFILE');
			}
		}
		else
		{
			$message	= JText::_('COM_COMMUNITY_ADMIN_ACCESS_ONLY');
		}
		
		$mainframe->redirect( $url , $message );
	}

	/**
	 * Reverts profile picture for specific user
	 **/	 	
	public function removepicture()
	{
		CFactory::load( 'helpers' , 'owner' );
		
		$message	= '';
		$userId		= JRequest::getInt( 'userid' , '' , 'POST' );
		$my			= CFactory::getUser();
		$url		= CRoute::_('index.php?option=com_community&view=profile&userid=' . $userId , false );
		$mainframe	=& JFactory::getApplication();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}		
		
		if( COwnerHelper::isCommunityAdmin() )
		{
			$user	= CFactory::getUser( $userId );
			
			// User id should be valid and admin should not be allowed to block themselves.
			if( $user->id )
			{
				$userModel		= CFactory::getModel( 'User' );
				$userModel->removeProfilePicture( $user->id , 'avatar' );
				$userModel->removeProfilePicture( $user->id , 'thumb' );
								
				$message		= JText::_('COM_COMMUNITY_PROFILE_PICTURE_REMOVED');
			}
			else
			{
				$message	= JText::_('COM_COMMUNITY_INVALID_PROFILE');
			}
		}
		else
		{
			$message	= JText::_('COM_COMMUNITY_ADMIN_ACCESS_ONLY');
		}
		
		$mainframe->redirect( $url , $message );
	}
	
	/**
	 * Method is called from the reporting library. Function calls should be
	 * registered here.
	 *
	 * return	String	Message that will be displayed to user upon submission.
	 **/	 	 	
	public function reportProfile( $link, $message , $id )
	{
		CFactory::load( 'libraries' , 'reporting' );
		$report = new CReportingLibrary();
		$config		=& CFactory::getConfig();
		$my			= CFactory::getUser();
		
		if( !$config->get('enablereporting') || ( ( $my->id == 0 ) && ( !$config->get('enableguestreporting') ) ) )
		{
			return '';
		}
		
		$report->createReport( JText::_('COM_COMMUNITY_REPORT_BAD_USER') , $link , $message );

		$action					= new stdClass();
		$action->label			= 'Block User';
		$action->method			= 'profile,blockProfile';
		$action->parameters		= $id;
		$action->defaultAction	= false;
		
		$report->addActions( array( $action ) );
		
		return JText::_('COM_COMMUNITY_REPORT_SUBMITTED');
	}
	
	/**
	 * Function that is called from the back end
	 **/	 	
	public function blockProfile( $userId )
	{
		$user		= CFactory::getUser( $userId );
		
		CFactory::load( 'helpers' , 'owner' );
		
		if( COwnerHelper::isCommunityAdmin() )
		{
			$user->set( 'block' , 1 );
			$user->save();
			return JText::_('COM_COMMUNITY_USER_ACCOUNT_BANNED');
		}
	}
	
	/**
	 * Responsible to display the edit profile form.	 
	 **/	 	
	public function edit()
	{
		CFactory::setActiveProfile();
		
		$user	= CFactory::getUser();
		
		if($user->id == 0)
		{
		   return $this->blockUnregister();
		}				
		
		if(JRequest::getVar('action', '', 'POST') != '')
		{
			$this->_saveProfile();
		}
		
		// Get/Create the model
		$model = & $this->getModel('profile');
		$model->setProfile('hello me');
		
		$document = JFactory::getDocument();

		$viewType	= $document->getType();	
 		$viewName	= JRequest::getCmd( 'view', $this->getName() );

		$data = new stdClass();
		$data->profile	= $model->getEditableProfile($user->id , $user->getProfileType() );
		
		$view 			= & $this->getView( $viewName, '', $viewType);

		$this->_icon = 'edit';

		if(!$data->profile)
		{
			echo $view->get('error', JText::_('COM_COMMUNITY_USER_NOT_FOUND') );
		}
		else
		{
			echo $view->get(__FUNCTION__, $data);
		}
	}

	public function editDetails()
	{		
		$user		=& JFactory::getUser();
		$mainframe	=& JFactory::getApplication();
		$view		=& $this->getView ( 'profile' );

		if($user->id == 0){
			return $this->blockUnregister();
		}
				
		$lang	=& JFactory::getLanguage();
		$lang->load(COM_USER_NAME);

		// Check if user is really allowed to edit.
		//$params =& $mainframe->getParams();
		$params = null;
		// check to see if Frontend User Params have been enabled
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$check = $usersConfig->get('frontend_userparams');

		if ($check == '1' || $check == 1 || $check == NULL)
		{
			if($user->authorize( COM_USER_NAME, 'edit' )) {
				$params		= $user->getParameters(true);
			
				//In Joomla 1.6, $params will be a JRegistry class, whereas it was JParameter in 1.5 
				//render() does not exist in JRegistry. Will need to translate the JForm XML in 1.6 to those acceptable for JParameter in 1.5.
				if((get_class($params) != 'JParameter' || get_class($params) != 'CParameter') && C_JOOMLA_15==0){
					
					CFactory::load( 'libraries' , 'jform' );
					
					$vals = $params->toArray();
					$params = &CJForm::getInstance('editDetails', JPATH_ADMINISTRATOR.'/components/com_users/models/forms/user.xml');
								
					//set data for the form				
					foreach($vals as $k => $v){
						$params->setValue($k , 'params' , $v);
					}
				}
			}
		}
		
		
		$my			= CFactory::getUser();
		$config		= CFactory::getConfig();
		
		$myParams	=& $my->getParams();
		$myDTS		= $myParams->get('daylightsavingoffset'); 		
		$cOffset	= ( $myDTS != '' ) ? $myDTS : $config->get('daylightsavingoffset');

		$dstOffset	= array();
		$counter = -4;
		for($i=0; $i <= 8; $i++ )
		{
			$dstOffset[] = 	JHTML::_('select.option', $counter, $counter);
			$counter++;
		}
		
		$offSetLists = JHTML::_('select.genericlist',  $dstOffset, 'daylightsavingoffset', 'class="inputbox" size="1"', 'value', 'text', $cOffset);		
		
		$data = new stdClass();		
		$data->params		= $params;
		$data->offsetList	= $offSetLists;
		
		
		$this->_icon = 'edit';				
		
		echo $view->get ( 'editDetails', $data);
	}
	
	
	public function save()
	{
		// Check for request forgeries
		$mainframe	=& JFactory::getApplication();
		JRequest::checkToken() or jexit( JText::_( 'COM_COMMUNITY_INVALID_TOKEN' ) );
		
		JFactory::getLanguage()->load(COM_USER_NAME);		
		
		$user 		=& JFactory::getUser();
		$userid		= JRequest::getVar( 'id', 0, 'post', 'int' );		

		// preform security checks
		if ($user->get('id') == 0 || $userid == 0 || $userid <> $user->get('id'))
		{
			echo $this->blockUnregister();
			return;
		}

		$username	= $user->get('username');
	
		//clean request
		$post = JRequest::get( 'post' );
		$post['username']	= $username;
		$post['password']	= JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['password2']	= JRequest::getVar('password2', '', 'post', 'string', JREQUEST_ALLOWRAW);
		
		
		//check email
	    $email		= $post['email'];
	    $emailPass	= $post['emailpass'];
	    $modelReg	=& $this->getModel('register');
	    
	    CFactory::load( 'helpers' , 'validate' );
	    if(!CValidateHelper::email($email))
	    {
	    	$msg = JText::sprintf('COM_COMMUNITY_INVITE_EMAIL_INVALID', $email);
			$mainframe->redirect(CRoute::_('index.php?option=com_community&view=profile&task=editDetails', false), $msg, 'error');
			return false;
	    }
	    
	    if(! empty($email) && ($email != $emailPass) && $modelReg->isEmailExists(array('email'=>$email)) )
		{
			$msg		= JText::sprintf('COM_COMMUNITY_EMAIL_EXIST', $email);
			$msg		= stripslashes($msg);
			$mainframe->redirect(CRoute::_('index.php?option=com_community&view=profile&task=editDetails', false), $msg, 'error');
			return false;			
	    }
	
		// get the redirect
		$return = CRoute::_('index.php?option=com_community&view=profile&task=editDetails', false);

		// do a password safety check
		if( JString::strlen($post['password']) || JString::strlen($post['password2'])) 
		{
			// so that "0" can be used as password e.g.
			if($post['password'] != $post['password2']) 
			{
				$msg = JText::_('PASSWORDS_DO_NOT_MATCH');
				$mainframe->redirect(CRoute::_('index.php?option=com_community&view=profile&task=editDetails', false), $msg, 'error');
				return false;
			}
		}
		
		// we don't want users to edit certain fields so we will unset them
		unset($post['gid']);
		unset($post['block']);
		unset($post['usertype']);
		unset($post['registerDate']);
		unset($post['activation']);

		//update CUser param 1st so that the new value will not be replace wif the old one.
		$my			= CFactory::getUser();
		$params		=& $my->getParams();
		$postvars	= $post['daylightsavingoffset'];
		$params->set('daylightsavingoffset', $postvars);
		
		
		// Store FB prefernce o ly FB connect data
		$connectModel	= CFactory::getModel( 'Connect' );
		if( $connectModel->isAssociated( $user->id ) )
		{
			$postvars	= !empty($post['postFacebookStatus']) ? 1 : 0;
			$my->_cparams->set('postFacebookStatus', $postvars );
		}
		
		$jConfig		=& JFactory::getConfig();
		$model			= CFactory::getModel( 'profile' );
		$editSuccess	= true;	
		$msg			= JText::_( 'COM_COMMUNITY_SETTINGS_SAVED' );	
		$jUser			=& JFactory::getUser();

		$my->save('params');
		//print_r($my);exit;
		// Bind the form fields to the user table
		if(!$jUser->bind($post))
		{
			$msg = $jUser->getError();
			$editSuccess = false;
		}

		//this is silly, in Joomla 1.6, in order to preserve the user group, we need to change the JUser's Groups array to contain group ID instead of name
		if(property_exists($jUser, 'groups')){
			foreach($jUser->groups as $groupid => $groupname){
				$jUser->groups[$groupid] = $groupid;
			}
		}

		// Store the web link table to the database
		if(!$jUser->save())
		{
			$msg	= $jUser->getError();
			$editSuccess = false;
		}

		if($editSuccess)
		{
			$session =& JFactory::getSession();
			$session->set('user', $jUser);
			
			// User with FB Connect, store post preference
			
			
			
			//execute the trigger
			$appsLib	=& CAppPlugins::getInstance();
			$appsLib->loadApplications();
			
			$userRow	= array();
			$userRow[]	= $jUser;
			 
			$appsLib->triggerEvent( 'onUserDetailsUpdate' , $userRow );
		}
		
		$mainframe->redirect(CRoute::_('index.php?option=com_community&view=profile&task=editDetails', false), $msg);
	}	
	
	/**
	 * Show rss feed for this user
	 */	 	
	public function feed(){
		$document	= JFactory::getDocument();
		
		$item = new JFeedItem();
		$item->author = '';
		$document->addItem($item);
	}
	
	/**
	 * Saves a user's profile	
	 * 	 	
	 * @access	private
	 * @param	none 
	 */
	private function _saveProfile()
	{
		$model		=& $this->getModel('profile');
		$usermodel	=& $this->getModel('user');
		$document	= JFactory::getDocument();
		$my			= CFactory::getUser();
		$mainframe	=& JFactory::getApplication();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}

		CFactory::load( 'libraries' , 'apps' );
		$appsLib		=& CAppPlugins::getInstance();
		$saveSuccess	= $appsLib->triggerEvent( 'onFormSave' , array('jsform-profile-edit' ));
		
		if( empty($saveSuccess) || !in_array( false , $saveSuccess ) )
		{
			$values		= array();
			$profiles	= $model->getEditableProfile( $my->id , $my->getProfileType() );
			
			CFactory::load( 'libraries' , 'profile' );
	
			foreach( $profiles['fields'] as $group => $fields )
			{
				foreach( $fields as $data )
				{
					$fieldValue	= new stdClass();
					
					// Get value from posted data and map it to the field.
					// Here we need to prepend the 'field' before the id because in the form, the 'field' is prepended to the id.
					$postData	= JRequest::getVar( 'field' . $data['id'] , '' , 'POST' );
					
					// Retrieve the privacy data for this particular field.
					$fieldValue->access	= JRequest::getInt( 'privacy' . $data['id'] , 0 , 'POST' );
					$fieldValue->value	= CProfileLibrary::formatData( $data['type']  , $postData );
					
					$values[ $data['id'] ]	= $fieldValue;
					
					// @rule: Validate custom profile if necessary
					if( !CProfileLibrary::validateField( $data['id'], $data['type'] , $values[ $data['id'] ]->value , $data['required']) )
					{
						// If there are errors on the form, display to the user.
						$message	= JText::sprintf('COM_COMMUNITY_FIELD_CONTAIN_IMPROPER_VALUES' ,  $data['name'] );
						$mainframe->enqueueMessage( CTemplate::quote($message) , 'error' );
						return;
					}
				}
			}

			// Rebuild new $values with field code
			$valuesCode = array();
			
			foreach( $values as $key => $val )
			{
				$fieldCode = $model->getFieldCode($key);

				if( $fieldCode )
				{
					// For backward compatibility, we can't pass in an object. We need it to behave
					// like 1.8.x where we only pass values.
					$valuesCode[$fieldCode] = $val->value;
				}
			}
			
			$saveSuccess = false;
			
			$appsLib	=& CAppPlugins::getInstance();
			$appsLib->loadApplications();
			
			// Trigger before onBeforeUserProfileUpdate
			$args		= array();
			$args[]		= $my->id;
			$args[]		= $valuesCode;
			$result 	= $appsLib->triggerEvent( 'onBeforeProfileUpdate' , $args );
			
			// make sure none of the $result is false
			if(!$result || ( !in_array(false, $result) ) )
			{
				$saveSuccess = true;
				$model->saveProfile($my->id, $values);
			}
		}

		// Trigger before onAfterUserProfileUpdate
		$args 	= array();
		$args[]	= $my->id;
		$args[]	= $saveSuccess; 
		$result = $appsLib->triggerEvent( 'onAfterProfileUpdate' , $args );
		
		if( $saveSuccess )
		{
			CFactory::load( 'libraries' , 'userpoints' );		
			CUserPoints::assignPoint('profile.save');		
	
			$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_PROFILE_SAVED') );
		}
		else
		{
			$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_PROFILE_NOT_SAVED'), 'error');	
		}
	}
	
	/**
	 * Displays front page profile of user
	 * 	 	
	 * @access	public
	 * @param	none
	 * @returns none	 
	 */
	public function display()
	{
		// By default, display the user profile page
		$this->profile();
	}
	
	private function _validVanityURL( $alias , $userId )
	{
        $model      = CFactory::getModel( 'Profile' );
		$user		= CFactory::getUser( $userId );
		
        $alias	= JFilterOutput::stringURLSafe( urlencode( $alias) );
        CFactory::load( 'helpers' , 'validate' );

        if( !$model->aliasExists( $alias , $userId ) && CValidateHelper::alias( $alias ) )
		{
			return true;
        }
        return false;
	}

	public function updateAlias()
	{
		CFactory::load( 'helpers' , 'owner' );
		
		if( !COwnerHelper::isCommunityAdmin() )
		{
			echo JText::_('COM_COMMUNITY_RESTRICTED_ACCESS');
			return;
		}
		$mainframe	=& JFactory::getApplication();
		$my		= CFactory::getUser();
		$userId	= JRequest::getInt( 'userid' , 0 , 'POST' );
		$alias	= JRequest::getCmd( 'alias' , 'POST' );
		$style	= 'message';

		if( $userId != 0 )
		{
			$user	= CFactory::getUser( $userId );

			if( $this->_validVanityURL( $alias , $user->id ) )
			{
				$user->set('_alias' , $alias );
				$user->save( 'params' );
				$message        = JText::_('COM_COMMUNITY_ALIAS_UPDATED' );
			}
			else
			{
				$message        = JText::_('COM_COMMUNITY_ALIAS_ALREADY_EXISTS' );
				$style			= 'error';
			}
			$mainframe->redirect( CRoute::_('index.php?option=com_community&view=profile&userid=' . $userId , false ) , $message , $style );
		}
		
	}
	
	public function preferences()
	{

		$view		=& $this->getView('profile');
		$my			= CFactory::getUser();
        $mainframe	=& JFactory::getApplication();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}		
		
		$method	= JRequest::getMethod();
		
		if($method == 'POST')
		{			
			CFactory::load( 'libraries' , 'apps' );
			$appsLib		=& CAppPlugins::getInstance();
			$saveSuccess	= $appsLib->triggerEvent( 'onFormSave' , array('jsform-profile-preferences' ));
			
			if( empty($saveSuccess) || !in_array( false , $saveSuccess ) )
			{
				$params			= $my->getParams();
				$postvars		= JRequest::get('POST');
				$activity       = JRequest::getInt('activityLimit');
				$profileLikes       = JRequest::getInt('profileLikes', 0);
				$editSuccess	= true;
				$message	= JText::_('COM_COMMUNITY_PREFERENCES_SETTINGS_SAVED');

				$mobileView = JRequest::getVar('mobileView');
				$params->set( 'mobileView', $mobileView );
				
                if($activity != 0)
                {
                    $params->set( 'activityLimit' , $activity );
                    $params->set( 'profileLikes' , $profileLikes );
                    $jConfig    =& JFactory::getConfig();
                    $model      = CFactory::getModel( 'Profile' );

                    if( $jConfig->getValue( 'sef' ) && isset( $postvars['alias'] ) && !empty( $postvars['alias'] ) )
                    {
						$alias	= JRequest::getCmd( 'alias' , 'POST' );
						
                    	if( $this->_validVanityURL( $alias , $my->id ) )
						{
							
							$my->set( '_alias' , $alias );
						}
                        else
                        {
                        	$message        = JText::_('COM_COMMUNITY_ALIAS_ALREADY_EXISTS' );
                            $editSuccess    = false;
                        }
                    }

                    $my->save( 'params' );

                    if( $editSuccess )
                    {
                            $mainframe->enqueueMessage( $message );
                    }
                    else
                    {
                            $mainframe->enqueueMessage( $message , 'error' );
                    }
                }
                else
                {
                    $mainframe->enqueueMessage( JText::_('COM_COMMUNITY_PREFERENCES_INVALID_VALUE' ) , 'error' );
                }
			}
		}
		
		echo $view->get(__FUNCTION__);
	}
	
	/**
	 * Allow user to set their privacy setting.
	 * User privacy setting is actually just part of their params	 
	 */	 	
	public function privacy()
	{
		CFactory::setActiveProfile();
		$my		= CFactory::getUser();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}		
		
		if(JRequest::getVar( 'action', '', 'POST') != '' )
		{
			CFactory::load( 'libraries' , 'apps' );
			$appsLib		=& CAppPlugins::getInstance();
			$saveSuccess	= $appsLib->triggerEvent( 'onFormSave' , array('jsform-profile-privacy' ));

			if( empty($saveSuccess) || !in_array( false , $saveSuccess ) )
			{
				$params		=& $my->getParams();
				$postvars	= JRequest::get('POST');
				$searchMail	= JRequest::getVar('search_email');
				$my->_search_email	= $searchMail;
				
				$previousProfilePermission	= $my->get('privacyProfileView');
				
				$activityModel = CFactory::getModel('activities');

				if (isset($postvars['resetPrivacyPhotoView']))
				{
					//Update all photos and album permission
					$photoPermission	= JRequest::getVar('privacyPhotoView', 0, 'POST');
					$photoModel			= CFactory::getModel('photos');
					$photoModel->updatePermission($my->id, $photoPermission);
					// Update all photos activity stream permission
					$activityModel->updatePermission($photoPermission, null , $my->id , 'photos');
					
					unset($postvars['resetPrivacyPhotoView']);
				}
				if (isset($postvars['resetPrivacyVideoView']))
				{
					//Update all videos permission
					$videoPermission	= JRequest::getVar('privacyVideoView', 0, 'POST');
					$videoModel			= CFactory::getModel('videos');
					$videoModel->updatePermission($my->id, $videoPermission);
					// Update all videos activity stream permission
					$activityModel->updatePermission($videoPermission, null , $my->id , 'videos');
					
					unset($postvars['resetPrivacyVideoView']);
				}
				
				foreach($postvars as $key => $val)
				{
					$params->set($key, $val);
				}
				
				$my->save('params');
		
				//add user points
				CFactory::load( 'libraries' , 'userpoints' );		
				CUserPoints::assignPoint('profile.privacy.update');
				
				//fix, we do not reset old privacy setting of old activities
				//update all profile related activity streams.
				//$profilePermission = JRequest::getVar('privacyProfileView', 0, 'POST');
				//$activityModel->updatePermission($profilePermission, $previousProfilePermission , $my->id, 'profile' ); 

				$post['sendEmail']	= JRequest::getVar('notifyEmailSystem');
				$jUser 		=& JFactory::getUser($my->id);

				$jUser->sendEmail = $post['sendEmail'];

				if(!$jUser->save())
				{
					$msg	= $jUser->getError();
					$msg		= stripslashes($msg);
					$mainframe->redirect(CRoute::_('index.php?option=com_community&view=profile&task=privacy', false), $msg, 'error');
				}

				$this->cacheClean( array(COMMUNITY_CACHE_TAG_FEATURED, COMMUNITY_CACHE_TAG_ACTIVITIES, COMMUNITY_CACHE_TAG_MEMBERS, COMMUNITY_CACHE_TAG_FRONTPAGE) );

				$mainframe =& JFactory::getApplication();
				$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_PRIVACY_SETTINGS_SAVED') );

			}
		}
		
		$view = CFactory::getView('profile');
		echo $view->get('privacy');
	}
	
	/**
	 * Viewing a user's profile
	 * 	 	
	 * @access	public
	 * @param	none
	 * @returns none	 
	 */
	public function profile()
	{
		// Set cookie
		$userid = JRequest::getVar('userid', 0, 'GET');		
		
		$data       = new stdClass();
		$model      =& $this->getModel('profile');
		$my         = CFactory::getUser();
		
		// Test if userid is 0, check if the user is viewing its own profile.
		if( $userid == 0 && $my->id != 0 )
		{
			$userid 	= $my->id;
			
			// We need to set the 'userid' var so that other code that uses
			// JRequest::getVar will work properly
			JRequest::setVar('userid', $userid);
		}
		
		$user			= CFactory::getUser( $userid );
		$config			= CFactory::getConfig();

		$data->profile	= $model->getViewableProfile( $userid , $user->getProfileType() );

		//show error if user id invalid / not found.
        if(empty($data->profile['id']) )
		{
			$this->blockUnregister();		
		}
		else
		{
				
			CFactory::setActiveProfile($userid);
			
			$my			= CFactory::getUser();
			$appsModel	= CFactory::getModel('apps');
					
			$avatar		=& $this->getModel('avatar');
			
			$document 	= JFactory::getDocument();
	
			$viewType	= $document->getType();	

			$view = & $this->getView( 'profile', '', $viewType);
			
			CFactory::load( 'helpers' , 'friends' );
			
			// Try initialize the user id. Maybe that user is logged in.
			$user	= CFactory::getUser( $userid );
			$id		= $user->id;

			$data->largeAvatar			= $my->getAvatar();
			
			// Assign the user object for the current viewer whether a guest or a member
			$data->user		= $user;			
			$data->apps		= array();
			
		
			if(!$id)
			{
				echo $view->get('error', JText::_('COM_COMMUNITY_USER_NOT_FOUND') );
			}
			else
			{
				echo $view->get(__FUNCTION__, $data, $id);
			}
		}//end if else
	}
	
	/**
	 * Links an existing photo in the system and use it as the profile picture
	 ***/	 	
	public function linkPhoto()
	{
		$id			= JRequest::getInt( 'id', 0, 'POST' );
		$photoModel	= CFactory::getModel( 'Photos' );
		$my			= CFactory::getUser();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}
		
		if( $id == 0 )
		{
			echo JText::_('COM_COMMUNITY_PHOTOS_INVALID_PHOTO_ID');
			return;
		}
		
		$photo		=& JTable::getInstance( 'Photo' , 'CTable' );
		$photo->load( $id );
		
		if( $my->id != $photo->creator )
		{
			echo JText::_('COM_COMMUNITY_ACCESS_DENIED');
			return;
		}
		
		
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.utility');

		$view 	= & $this->getView( 'profile');

		CFactory::load( 'helpers' , 'image' );
		
		$my			= CFactory::getUser();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}				

		$mainframe		=& JFactory::getApplication();

		// @todo: configurable width?
		$imageMaxWidth	= 160;

		// Get a hash for the file name.
		$fileName		= JUtility::getHash( $photo->id . time() );
		$hashFileName	= JString::substr( $fileName , 0 , 24 );
		$photoPath		= JPATH_ROOT . DS . $photo->image; //$photo->original;

		if( $photo->storage == 'file' )
		{
			// @rule: If photo original file still exists, we will use the original file.
			if( !JFile::exists( $photoPath ) )
			{
				$photoPath	= JPATH_ROOT . DS . $photo->image;
			}
			
			// @rule: If photo still doesn't exists, we should not allow the photo to be changed.
			if( !JFile::exists( $photoPath ) )
			{
				$mainframe->redirect( CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar' , false ) , JText::_('COM_COMMUNITY_PHOTOS_SET_AVATAR_ERROR') , 'error');
				return;
			}
		}
		else
		{
			CFactory::load( 'helpers' , 'remote' );
			$content	= cRemoteGetContent( $photo->getImageURI() );
			
			if( !$content )
			{
				$mainframe->redirect( CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar' , false ) , JText::_('COM_COMMUNITY_PHOTOS_SET_AVATAR_ERROR') , 'error');
				return;
			}
			$jConfig	=& JFactory::getConfig();
			$photoPath	= $jConfig->getValue('tmp_path'). DS . md5( $photo->image);	

			// Store image on temporary location
			JFile::write( $photoPath , $content );
		}

		$info			= getimagesize( $photoPath );
		$extension		= CImageHelper::getExtension( $info['mime'] );
		$config			= CFactory::getConfig();
		
		$storage			= JPATH_ROOT . DS . $config->getString('imagefolder') . DS . 'avatar';
		$storageImage		= $storage . DS . $hashFileName . $extension;
		$storageThumbnail	= $storage . DS . 'thumb_' . $hashFileName . $extension;
		$image				= $config->getString('imagefolder') . '/avatar/' . $hashFileName . $extension;
		$thumbnail			= $config->getString('imagefolder') . '/avatar/' . 'thumb_' . $hashFileName . $extension;
		$userModel			= CFactory::getModel( 'user' );

		// Only resize when the width exceeds the max.
		if( !CImageHelper::resizeProportional( $photoPath , $storageImage , $info['mime'] , $imageMaxWidth ) )
		{
			$mainframe->enqueueMessage(JText::sprintf('COM_COMMUNITY_ERROR_MOVING_UPLOADED_FILE' , $storageImage), 'error');
		}

		// Generate thumbnail
		if(!CImageHelper::createThumb( $photoPath , $storageThumbnail , $info['mime'] ))
		{
			$mainframe->enqueueMessage(JText::sprintf('COM_COMMUNITY_ERROR_MOVING_UPLOADED_FILE' , $storageThumbnail), 'error');
		}

		if( $photo->storage != 'file' )
		{
			//@rule: For non local storage, we need to remove the temporary photo
			JFile::delete( $photoPath );
		}
		
		$userModel->setImage( $my->id , $image , 'avatar' );
		$userModel->setImage( $my->id , $thumbnail , 'thumb' );

		// Update the user object so that the profile picture gets updated.
		$my->set( '_avatar' , $image );
		$my->set( '_thumb'	, $thumbnail );

		$mainframe->redirect( CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar' , false ) , JText::_('COM_COMMUNITY_PHOTOS_SET_AVATAR_SUCCESS') );
	}
	
	/**
	 * Upload a new user avatar
	 */	 	
	public function uploadAvatar()
	{
		CFactory::setActiveProfile();
		
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.utility');

		$view 	= & $this->getView( 'profile');

		CFactory::load( 'helpers' , 'image' );
		
		$my			= CFactory::getUser();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}				
		
		// If uplaod is detected, we process the uploaded avatar
		if( JRequest::getVar('action', '', 'POST') )
		{
			$mainframe =& JFactory::getApplication();
						
			$file		= JRequest::getVar( 'Filedata' , '' , 'FILES' , 'array' );

			$userid		= $my->id;

			if(JRequest::getVar('userid' , '' , 'POST') != ''){
				$userid		= JRequest::getInt( 'userid' , '' , 'POST' );
				$url		= CRoute::_('index.php?option=com_community&view=profile&userid=' . $userid );
			}

			if( !isset( $file['tmp_name'] ) || empty( $file['tmp_name'] ) )
			{	
				$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_NO_POST_DATA'), 'error');

				if(isset($url)){
					$mainframe->redirect($url);
				}
			}
			else
			{
				$config			= CFactory::getConfig();
				$uploadLimit	= (double) $config->get('maxuploadsize');
				$uploadLimit	= ( $uploadLimit * 1024 * 1024 );

				// @rule: Limit image size based on the maximum upload allowed.
				if( filesize( $file['tmp_name'] ) > $uploadLimit && $uploadLimit != 0 )
				{
					$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_VIDEOS_IMAGE_FILE_SIZE_EXCEEDED') , 'error' );

					if(isset($url)){
						$mainframe->redirect($url);
					}

					$mainframe->redirect( CRoute::_('index.php?option=com_community&view=profile&userid=' . $userid . '&task=uploadAvatar', false) );
				}

                if( !CImageHelper::isValidType( $file['type'] ) )
				{
					$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_IMAGE_FILE_NOT_SUPPORTED') , 'error' );

					if(isset($url))
					{
						$mainframe->redirect($url);
					}

					$mainframe->redirect( CRoute::_('index.php?option=com_community&view=profile&userid=' . $userid . '&task=uploadAvatar', false) );
            	}
				
				if( !CImageHelper::isValid($file['tmp_name'] ) )
				{
					$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_IMAGE_FILE_NOT_SUPPORTED'), 'error');

					if(isset($url)){
						$mainframe->redirect($url);
					}
				}
				else
				{
					// @todo: configurable width?
					$imageMaxWidth	= 160;

					// Get a hash for the file name.
					$profileType	= $my->getProfileType();
					$fileName		= JUtility::getHash( $file['tmp_name'] . time() );
					$hashFileName	= JString::substr( $fileName , 0 , 24 );
					$multiprofile	=& JTable::getInstance( 'MultiProfile' , 'CTable' );
					$multiprofile->load( $profileType );

					$useWatermark	= $profileType != COMMUNITY_DEFAULT_PROFILE && $config->get('profile_multiprofile') && !empty( $multiprofile->watermark ) ? true : false;
					//@todo: configurable path for avatar storage?

					$storage			= JPATH_ROOT . DS . $config->getString('imagefolder') . DS . 'avatar';
					$storageImage		= $storage . DS . $hashFileName . CImageHelper::getExtension( $file['type'] );
					$storageThumbnail	= $storage . DS . 'thumb_' . $hashFileName . CImageHelper::getExtension( $file['type'] );
					$image				= $config->getString('imagefolder') . '/avatar/' . $hashFileName . CImageHelper::getExtension( $file['type'] );
					$thumbnail			= $config->getString('imagefolder') . '/avatar/' . 'thumb_' . $hashFileName . CImageHelper::getExtension( $file['type'] );
						
					$userModel			= CFactory::getModel( 'user' );


					// Only resize when the width exceeds the max.
					if( !CImageHelper::resizeProportional( $file['tmp_name'] , $storageImage , $file['type'] , $imageMaxWidth ) )
					{
						$mainframe->enqueueMessage(JText::sprintf('COM_COMMUNITY_ERROR_MOVING_UPLOADED_FILE' , $storageImage), 'error');

						if(isset($url)){
							$mainframe->redirect($url);
						}
					}

					// Generate thumbnail
					if(!CImageHelper::createThumb( $file['tmp_name'] , $storageThumbnail , $file['type'] ))
					{
						$mainframe->enqueueMessage(JText::sprintf('COM_COMMUNITY_ERROR_MOVING_UPLOADED_FILE' , $storageThumbnail), 'error');

						if(isset($url)){
							$mainframe->redirect($url);
						}
					}			

					if( $useWatermark )
					{
						// @rule: Before adding the watermark, we should copy the user's original image so that when the admin tries to reset the avatar,
						// it will be able to grab the original picture.
						JFile::copy( $storageImage , JPATH_ROOT . DS . 'images' . DS . 'watermarks' . DS . 'original' . DS . md5( $my->id . '_avatar' ) . CImageHelper::getExtension( $file['type'] ) );
						JFile::copy( $storageThumbnail , JPATH_ROOT . DS . 'images' . DS . 'watermarks' . DS . 'original' . DS . md5( $my->id . '_thumb' ) . CImageHelper::getExtension( $file['type'] ) );
						
						$watermarkPath	= JPATH_ROOT . DS . CString::str_ireplace('/' , DS , $multiprofile->watermark);
						
						list( $watermarkWidth , $watermarkHeight )	= getimagesize( $watermarkPath );
						list( $avatarWidth , $avatarHeight ) 		= getimagesize( $storageImage );
						list( $thumbWidth , $thumbHeight ) 		= getimagesize( $storageThumbnail );

						$watermarkImage		= $storageImage;
						$watermarkThumbnail	= $storageThumbnail;						
						
						// Avatar Properties
						$avatarPosition	= CImageHelper::getPositions( $multiprofile->watermark_location , $avatarWidth , $avatarHeight , $watermarkWidth , $watermarkHeight );

						// The original image file will be removed from the system once it generates a new watermark image.
						CImageHelper::addWatermark( $storageImage , $watermarkImage , $file['type'] , $watermarkPath , $avatarPosition->x , $avatarPosition->y );

						//Thumbnail Properties
						$thumbPosition	= CImageHelper::getPositions( $multiprofile->watermark_location , $thumbWidth , $thumbHeight , $watermarkWidth , $watermarkHeight );
						
						// The original thumbnail file will be removed from the system once it generates a new watermark image.
						CImageHelper::addWatermark( $storageThumbnail , $watermarkThumbnail , $file['type'] , $watermarkPath , $thumbPosition->x , $thumbPosition->y );

						$my->set( '_watermark_hash' , $multiprofile->watermark_hash );
						$my->save();
					}
					
					$userModel->setImage( $userid , $image , 'avatar' );
					$userModel->setImage( $userid , $thumbnail , 'thumb' );
					
					// Update the user object so that the profile picture gets updated.
					$my->set( '_avatar' , $image );
					$my->set( '_thumb'	, $thumbnail );

					// @rule: once user changes their profile picture, storage method should always be file.
					$my->set( '_storage', 'file' );

					if(isset($url)){
						$mainframe->redirect($url);
					}
					
					//add user points
					CFactory::load( 'libraries' , 'userpoints' );
					CFactory::load( 'libraries' , 'activities');
							
					$act = new stdClass();
					$act->cmd 		= 'profile.avatar.upload';
					$act->actor   	= $userid;
					$act->target  	= 0;
					$act->title	  	= JText::_('COM_COMMUNITY_ACTIVITIES_NEW_AVATAR');
					$act->content	= '';
					$act->app		= 'profile';
					$act->cid		= 0;
					$act->comment_id	= $my->id;
					$act->comment_type	= 'profile.avatar.upload';
					
					$act->like_id	= $my->id;
					$act->like_type	= 'profile.avatar.upload';
							
					// Add activity logging
					CFactory::load ( 'libraries', 'activities' );
					CActivityStream::add( $act );
				
					CUserPoints::assignPoint('profile.avatar.upload');

					$this->cacheClean( array(COMMUNITY_CACHE_TAG_ACTIVITIES , COMMUNITY_CACHE_TAG_FRONTPAGE) );
				}
			}
		}
				
		echo $view->get( __FUNCTION__ );
	}

	/**
	 * Upload a new user video.
	 */
	public function linkVideo()
	{
		CFactory::setActiveProfile();
		$my		= CFactory::getUser();
                $config		= CFactory::getConfig();
	
		if($my->id == 0)
		{
			return $this->blockUnregister();
		}

                if( !$config->get('enableprofilevideo') )
                {
                    echo JText::_('COM_COMMUNITY_VIDEOS_PROFILE_VIDEO_DISABLE');
                    return;
                }
	
		$view 	= $this->getView( 'profile');
	
		echo $view->get( __FUNCTION__ );
	}

	public function editPage()
	{
		$my		= CFactory::getUser();
	
		if($my->id == 0)
		{
			return $this->blockUnregister();
		}
	
		$view 	= $this->getView( 'profile');
	
		echo $view->get( __FUNCTION__ );
		
	}
		
	/**
	 * Display drag&drop layout editing inetrface
	 */	 	
	public function editLayout()
	{
		$my		= CFactory::getUser();
	
		if($my->id == 0)
		{
			return $this->blockUnregister();
		}
	
		$view 	= $this->getView( 'profile');
	
		echo $view->get( __FUNCTION__ );
		
	}
	
	/**
	 * Full application view
	 */	 	
	public function app()
	{
		require_once (JPATH_COMPONENT.DS.'libraries'.DS.'apps.php');

		$view = & $this->getView('profile');
		echo $view->get( 'appFullView' );
	}
	
	/**
	 * Show pop up error message screen
	 * for invalid image file upload	 
	 */	 
	public function ajaxErrorFileUpload()
	{
		$objResponse	= new JAXResponse();
				
		$html    = '<div style="overflow:auto; height:200px; position: absolute-nothing;">' . JText::_('COM_COMMUNITY_PHOTOS_UPLOAD_DESC') . '</div>';
		$actions = '<button class="button" onclick="javascript:cWindowHide();" name="close">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';

		$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

		return $objResponse->sendResponse();
	}
	
	/*
	 * Allow users to delete their own profile
	 * 
	 */
	public function deleteProfile()
	{
		$view	=& $this->getView('profile');
		$method	= JRequest::getMethod();
		$my			= CFactory::getUser();
		$config		= CFactory::getConfig();
		CFactory::load ( 'helpers', 'owner' );
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}				
		//not allow to delete admin profile
		if(COwnerHelper::isCommunityAdmin($my->id)){
			echo JText::_('COM_COMMUNITY_CANNOT_DELETE_PROFILE_ADMIN');
			return;		
		}
		
		if( !$my->authorise('community.delete', 'profile.'.$my->id, $my) )
		{
			echo JText::_('COM_COMMUNITY_RESTRICTED_ACCESS');
			return;
		}
		
		
		if($method == 'POST')
		{
			// Instead of delete the user straight away, 
			// we'll block the user and notify the admin. 
			// Admin then would delete the user from backend
			JRequest::checkToken() or jexit( JText::_( 'COM_COMMUNITY_INVALID_TOKEN' ) );
			$my->set('block', 1);
			$my->save();
			
			// send notification email
			$model		= CFactory::getModel( 'profile' );
			$emails		= $model->getAdminEmails();
			$url		= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_community&view=users&layout=edit&id=' . $my->id;

			// Add notification
			CFactory::load( 'libraries' , 'notification' );
			
			$params			= new CParameter( '' );
			$params->set( 'userid' , $my->id );
			$params->set( 'username' , $my->getDisplayName() );
			$params->set( 'url' , $url );
			
			$subject		= JText::sprintf( 'COM_COMMUNITY_USER_ACCOUNT_DELETED_SUBJECT' , $my->getDisplayName() );
			CNotificationLibrary::add( 'etype_user_profile_delete' , $my->id , $emails , $subject , '' , 'user.deleted' , $params );

			//reduce counter for group member
			$groupTable		=& JTable::getInstance( 'Group' , 'CTable' );
			$groupsModel		= CFactory::getModel('groups');
			$groups			= $groupsModel->getGroups($my->id);
			
			//do processing
			foreach($groups as $group){
			    $group->membercount -=1;
			    $groupTable->bind( $group );
			    $groupTable->store();

			 //Delete Group Member
			  $groupTable->deleteMember($group->id,$my->id);
			}

			//reduce counter for event member count
			$eventTable	=& JTable::getInstance( 'Event' , 'CTable' );
			$eventModel	= CFactory::getModel( 'events' );
			$events		= $eventModel->getEvents( null, $my->id );

			foreach($events as $event){
			    $event->confirmedcount -=1;
			    $eventTable->bind($event);
			    $eventTable->store();

			    //remove guest
			    $eventTable->removeGuest($my->id, $event->id);
			}
			// logout and redirect the user
			$mainframe	=& JFactory::getApplication();
			$mainframe->logout($my->id);
			$mainframe->redirect(CRoute::_('index.php?option=com_community', false));
		}
		echo $view->get(__FUNCTION__);
	}

    /**
     * Block a user
     */         
	public function ajaxBlockUser( $userId )
	{
                $filter = JFilterInput::getInstance();
                $userId = $filter->clean($userId, 'int');

                $my             = CFactory::getUser();
		$response	= new JAXResponse();
		$config		= CFactory::getConfig();    
  
		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}   
		
		if( COwnerHelper::isCommunityAdmin($userId) )
		{
			return $this->ajaxRestrictBlockAdmin();
		}
		
		$content	= JText::_('COM_COMMUNITY_CONFIRM_BLOCK_USER'); 
		$redirect   = CRoute::_("index.php?option=com_community&view=profile&userid=" . $userId . "&task=blockUser" , false);

		$actions	= '<form name="jsform-profile-ajaxblockuser" method="post" action="' . $redirect . '" style="float:right;">';
		$actions	.= '<input type="submit" value="' . JText::_('COM_COMMUNITY_YES_BUTTON') . '" class="button" name="Submit"/>';
		$actions	.= '<input type="button" class="button" onclick="cWindowHide();return false;" name="cancel" value="'.JText::_('COM_COMMUNITY_NO_BUTTON').'" />'; 
		$actions	.= '</form>';   

		$response->addAssign('cwin_logo', 'innerHTML', $config->get('sitename'));
		$response->addScriptCall('cWindowAddContent', $content, $actions);

		$response->sendResponse();

	}

	public function blockUser()
	{  
        $my = CFactory::getUser();
	
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}
              
        $userId = JRequest::getVar('userid','','GET');
        
        CFactory::load ( 'libraries', 'block' );
        $blockUser  = new blockUser;
        $blockUser->block( $userId );
	}  
    
    /**
     * unBlock a user
     */  
	public function ajaxUnblockUser( $userId, $layout = null )
	{

                $filter = JFilterInput::getInstance();
                $userId = $filter->clean($userId, 'int');
                // $layout pending filter

                $my         = CFactory::getUser();
		$response	= new JAXResponse();
		$config		= CFactory::getConfig();

		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}		
		
		$content	= JText::_('COM_COMMUNITY_CONFIRM_UNBLOCK_USER'); 
		$layout     = !empty($layout) ? '&layout=' . $layout : '' ;
		$redirect   = CRoute::_("index.php?option=com_community&view=profile&userid=" . $userId . "&task=unBlockUser" . $layout , false);

		$actions	= '<form name="jsform-profile-ajaxunblockuser" method="post" action="' . $redirect . '" style="float:right;">';
		$actions	.= '<input type="submit" value="' . JText::_('COM_COMMUNITY_YES_BUTTON') . '" class="button" name="Submit"/>';    
		$actions	.= '<input type="button" class="button" onclick="cWindowHide();return false;" name="cancel" value="'.JText::_('COM_COMMUNITY_NO_BUTTON').'" />';
		$actions	.= '</form>';

		// Add invite button
		$response->addAssign('cwin_logo', 'innerHTML', $config->get('sitename'));
		$response->addScriptCall('cWindowAddContent', $content, $actions);

		$response->sendResponse();
	}  	 
	
	/**
	 * Un Ban member or friend (for ajax remove only)
	 */
	public function unBlockUser()
	{ 
        $my = CFactory::getUser();
	
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}
		
        $userId = JRequest::getVar('userid','','GET');
		$layout = JRequest::getVar('layout','','GET');
        
        CFactory::load ( 'libraries', 'block' );
        $blockUser  = new blockUser;
        $blockUser->unBlock( $userId , $layout ); 		
	}

	/**
	 * Method to view profile video
	 */
	public function video()
	{
        $view	=& $this->getView('profile');
        echo $view->get(__FUNCTION__);
	}
}
