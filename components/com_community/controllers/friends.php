<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CommunityFriendsController extends CommunityBaseController
{
	var $task;
	var $_icon = 'friends';
	var $_name = 'friends';

	public function ajaxIphoneFriends()
	{
		$objResponse	= new JAXResponse();
		$document		= JFactory::getDocument();

		$viewType	= $document->getType();
		$view		=& $this->getView( 'friends', '', $viewType );


		$html = '';

		ob_start();
		$this->display();
		$content = ob_get_contents();
		ob_end_clean();

		$tmpl			= new CTemplate();
		$tmpl->set('toolbar_active', 'friends');
		$simpleToolbar	= $tmpl->fetch('toolbar.simple');

		$objResponse->addAssign('social-content', 'innerHTML', $simpleToolbar . $content);
		return $objResponse->sendResponse();
	}

	public function edit()
	{
		// Get/Create the model
		$model = & $this->getModel('profile');
		$model->setProfile('hello me');

		$this->display(false, __FUNCTION__);
	}

	public function display()
	{
		// By default, display the user profile page
		$this->friends();
	}

	/**
	 * View all friends. Could be current user, if $_GET['id'] is not defined
	 * otherise, show your own friends
	 */
	public function friends()
	{
		CFactory::load('libraries', 'privacy');
		$document = JFactory::getDocument();
		$my =& CFactory::getUser();
		
		$viewType		= $document->getType();	
		$tagsFriends	= JRequest::getVar( 'tags','','GET');

		$view	=& $this->getView( 'friends','',  $viewType);
		$model	=& $this->getModel('friends');

		// Get the friend id to be displayed
		$id = JRequest::getCmd('userid', $my->id);
		
		// The friend count might be out of date. Lets fix it now
		$model->updateFriendCount( $id );

		// Check privacy setting
		if(!$my->authorise('community.view', 'friends.' . $id))
		{
			$this->blockUnregister();
			return;
		}
		
		$data	= new stdClass();
		echo $view->get('friends');
	}

	/**
	 * Search Within Friends
	 */
	public function friendsearch()
	{
		CFactory::load( 'libraries' , 'profile' );

		$mainframe =& JFactory::getApplication();

		$data			= new stdClass();
		$view			= $this->getView ('friends');
		$model			= $this->getModel('search');
		$profileModel		= $this->getModel('profile');

		$fields			= $profileModel->getAllFields();

		$search			= JRequest::get('REQUEST');
		$data->query		= JRequest::getVar( 'q', '', 'REQUEST' );
		$friendId		= JRequest::getVar( 'userid', '', 'REQUEST' );
		$avatarOnly		= JRequest::getVar( 'avatar' , '' );

		//prefill the search values.
		$fields = $this->_fillSearchValues($fields, $search);

		$data->fields		=& $fields;

		if(isset($search))
		{
			$model =& $this->getModel('search');
			$data->result	= $model->searchPeople( $search , $avatarOnly, $friendId );

			//pre-load cuser.
			$ids	= array();
			if(! empty($data->result))
			{
				foreach($data->result as $item)
				{
					$ids[]	= $item->id;
				}

				CFactory::loadUsers($ids);
			}
		}

		$data->pagination 	= $model->getPagination();

		echo $view->get('friendsearch',$data);
	}

	/**
	 * Show the user invite window
	 */
	public function invite()
	{
		$view = CFactory::getView('friends');
		$validated = false;
		
		$my = CFactory::getUser();
		
		if($my->id == 0)
		{
			return $this->blockUnregister();
		}
		
		if( JRequest::getVar('action', '', 'POST') == 'invite')
		{
			$mainframe =& JFactory::getApplication();


			CFactory::load( 'libraries' , 'apps' );
			$appsLib		=& CAppPlugins::getInstance();
			$saveSuccess	= $appsLib->triggerEvent( 'onFormSave' , array('jsform-friends-invite' ) );
			
			if( empty($saveSuccess) || !in_array( false , $saveSuccess ) )
			{
				$validated 			= true;
				$emailExistError	= array();
				$emailInvalidError	= array();
	
				$emails = JRequest::getVar('emails', '', 'POST');
	
				if( empty($emails) )
				{
					$validated = false;
					$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_FRIENDS_EMAIL_CANNOT_BE_EMPTY') , 'error');
				}
				else
				{
					$emails = explode(',', $emails);
					$userModel = CFactory::getModel('user');
	
					// Do simple email validation
					// make sure user is not a member yet
					// check for duplicate emails
					// make sure email is valid
					// make sure user is not already on the system
					CFactory::load('helpers', 'validate');
					$actualEmails = array();
					for( $i = 0; $i < count($emails); $i++ )
					{
						//trim the value
						$emails[$i] = JString::trim($emails[$i]);
	
						if(
							!empty($emails[$i])
							&& (boolean)CValidateHelper::email($emails[$i])
						)
						{
							//now if the email already exist in system, alert the user.
							if(!$userModel->userExistsbyEmail($emails[$i])){
								$actualEmails[$emails[$i]] = true;
							} else {
								$emailExistError[] = $emails[$i];
							}
						} else {
						    // log the error and display to user.
						    if(!empty($emails[$i]))
								$emailInvalidError[] = $emails[$i];
						}
					}
	
					$emails = array_keys($actualEmails);
					unset($actualEmails);
	
					if(count($emails) <= 0)
						$validated = false;
	
					if(count($emailInvalidError) > 0)
					{
						for($i = 0; $i < count($emailInvalidError); $i++)
						{
							$mainframe->enqueueMessage( JText::sprintf('COM_COMMUNITY_INVITE_EMAIL_INVALID', $emailInvalidError[$i]) , 'error');
						}
						$validated = false;
					}
	
	
					if(count($emailExistError) > 0)
					{
						for($i = 0; $i < count($emailExistError); $i++)
						{
							$mainframe->enqueueMessage( JText::sprintf('COM_COMMUNITY_INVITE_EMAIL_EXIST', $emailExistError[$i]) , 'error');
						}
						$validated = false;
					}
				}
	
				$message =  JRequest::getVar('message', '', 'POST');
				
				$config		= CFactory::getConfig();
	
				if( $validated )
				{
					CFactory::load( 'libraries' , 'notification' );
					
					for( $i = 0; $i < count($emails); $i++ ) 
					{
						$emails[$i] = JString::trim($emails[$i]);
						
						$params		= new CParameter( '' );
						$params->set( 'url' , 'index.php?option=com_community&view=profile&userid='.$my->id.'&invite='.$my->id );
						$params->set( 'message'	, $message );
						CNotificationLibrary::add( 'etype_friends_invite_users' , $my->id , $emails[ $i ] , JText::sprintf('COM_COMMUNITY_INVITE_EMAIL_SUBJECT', $my->getDisplayName(), $config->get('sitename') ) , '' , 'friends.invite' , $params );
					}
	
					$mainframe->enqueueMessage(JText::sprintf( (CStringHelper::isPlural(count($emails))) ? 'COM_COMMUNITY_INVITE_EMAIL_SENT_MANY' : 'COM_COMMUNITY_INVITE_EMAIL_SENT' , count($emails)));
					
					//add user points - friends.invite removed @ 20090313
					//clear the post value.
					JRequest::setVar('emails', '');
					JRequest::setVar('message', '');
	
				} else {
					// Display error message
				}
			}
		}

		echo $view->get('invite');
	}

	public function online()
	{
		$view = $this->getView('friends');
		echo $view->get(__FUNCTION__);

	}

	public function news()
	{
		$view = $this->getView('friends');
		echo $view->get(__FUNCTION__);
	}

	/**
	 * List down all request that you've sent but not approved by the other side yet
	 */
	public function sent(){
		$my =& JFactory::getUser();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}		
		
		$view = $this->getView('friends');
		$model =& $this->getModel('friends');

		$data	= new stdClass();
		$rsent = $model->getSentRequest($my->id);

		$data->sent = $rsent;
		$data->pagination =& $model->getPagination();

		echo $view->get('sent', $data);
	}

	/**
	 * Add new friend
	 */
	public function add(){
		$view = $this->getView('friends');
		
		$my =& JFactory::getUser();
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}		

		$model	=& $this->getModel('friends');
		$id		= JRequest::getCmd('userid', null);				
		$data 	=& JFactory::getUser($id);

		$task= JRequest::getVar('task','','GET');
		$task = $task.'()';
		$this->task=$task;

		// If a query is sent, seach for it
		if($query = JRequest::getVar('userid', '', 'POST'))
		{
			$model->addFriend($id, $my->id);
			
			//trigger for onFriendRequest
			$eventObject = new stdClass();
			$eventObject->profileOwnerId 	= $my->id;
			$eventObject->friendId 			= $id;
			$this->triggerFriendEvents( 'onFriendRequest' , $eventObject);
			unset($eventObject);
			
			echo $view->get('addSuccess', $data);
		}
		else
		{
			//disallow self add as a friend
			
			if($my->id==$id)
			{
				$view->addInfo( JText::_( 'COM_COMMUNITY_FRIENDS_CANNOT_ADD_SELF' ) );
				$this->display();
			}
			//disallow add existing friend
			elseif(count($model->getFriendConnection($my->id,$id))>0)
			{

				$view->addInfo(JText::_('COM_COMMUNITY_FRIENDS_IS_ALREADY_FRIEND'));
				$this->display();

			}
			else
			{
				echo $view->get('add', $data);
			}

		}

	}

	public function remove()
	{
		$my		= CFactory::getUser();
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}

		$view = $this->getView('friends');
		$friendId = JRequest::getVar('fid','','GET');
		$friend = CFactory::getUser($friendId);
		if ($this->delete($friendId))
		{
			$view->addInfo(JText::sprintf('COM_COMMUNITY_FRIENDS_REMOVED', $friend->getDisplayName()));
		} else {
			$view->addinfo(JText::_('COM_COMMUNITY_FRIENDS_REMOVING_FRIEND_ERROR'));
		}

		$this->display();
	}

	/**
	 * Method to cancel a friend request
	 */
	public function deleteSent()
	{
		$my		= CFactory::getUser();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}		
		
		$view	= $this->getView( 'friends' );
		$model	=& $this->getModel('friends');

		$friendId	= JRequest::getVar( 'fid' , '' , 'POST' );
		$message	= '';

		if($model->deleteSentRequest($my->id,$friendId))
		{
			$message	= JText::_('COM_COMMUNITY_FRIENDS_REQUEST_CANCELED');
			
			//add user points - friends.request.cancel removed @ 20090313
		}
		else
		{
			$message	= JText::_('COM_COMMUNITY_FRIENDS_REQUEST_CANCELLED_ERROR');
		}

		$view->addInfo( $message );
		$this->sent();
	}

	 /**
	  * functino tag() removed @ 02-oct-2009
	  * functino ajaxAssignTag() removed @ 02-oct-2009	  
	  */

	/**
	 * Ajax function to reject a friend request
	 **/
	public function ajaxRejectRequest( $requestId )
	{
		$objResponse	= new JAXResponse();
		$my				= CFactory::getUser();
		$friendsModel	= CFactory::getModel('friends');
		
		$filter	    =	JFilterInput::getInstance();
		$requestId = $filter->clean( $requestId, 'int' );
		
		if($my->id == 0)
		{
		   return $this->ajaxBlockUnregister();
		}		

		if( $friendsModel->isMyRequest( $requestId , $my->id) )
		{
			$pendingInfo = $friendsModel->getPendingUserId($requestId);
			
			if( $friendsModel->rejectRequest( $requestId ) )
			{
				//add user points - friends.request.reject removed @ 20090313

				$friendId  = $pendingInfo->connect_from;
				$friend	   = CFactory::getUser( $friendId );
				$friendUrl = CRoute::_('index.php?option=com_community&view=profile&userid='.$friendId);
				
				$objResponse->addScriptCall( 'joms.jQuery("#pending-' . $requestId . '").find(".jsNotificationActions").fadeOut();' );
				$objResponse->addScriptCall( 'joms.jQuery("#pending-' . $requestId . ' .jsNotificationContent").html(\'<div class="jsNotificationActor">' . JText::sprintf('COM_COMMUNITY_FRIEND_REQUEST_DECLINED', $friend->getDisplayName(), $friendUrl) . '</div>\').removeClass("jsNotificationHasActions");');

				$objResponse->addScriptCall('update_counter("#jsMenuNotif > .notifcount", -1);');
				$objResponse->addScriptCall('update_counter("#jsMenuFriend > .notifcount", -1);');

				//trigger for onFriendReject
				$eventObject = new stdClass();
				$eventObject->profileOwnerId 	= $my->id;
				$eventObject->friendId 			= $friendId;
				$this->triggerFriendEvents( 'onFriendReject' , $eventObject);
				unset($eventObject);
			}
			else
			{
				$objResponse->addScriptCall( 'joms.jQuery("#request-notice").html("' . JText::sprintf('COM_COMMUNITY_FRIEND_REQUEST_REJECT_FAILED', $requestId ) . '");' );
				$objResponse->addScriptCall( 'joms.jQuery("#request-notice").attr("class", "error");');
			}

		}
		else
		{
			$objResponse->addScriptCall( 'joms.jQuery("#request-notice").html("' . JText::_('COM_COMMUNITY_FRIENDS_NOT_YOUR_REQUEST') . '");' );
			$objResponse->addScriptCall( 'joms.jQuery("#request-notice").attr("class", "error");');
		}

		return $objResponse->sendResponse();

	}

	/**
	 * Ajax function to approve a friend request
	 **/
	public function ajaxApproveRequest( $requestId )
	{
		$objResponse	= new JAXResponse();
		$my				= CFactory::getUser();
		$friendsModel	= CFactory::getModel( 'friends' );
		
		$filter	    =	JFilterInput::getInstance();
		$requestId = $filter->clean( $requestId, 'int' );
		
		if($my->id == 0)
		{
		   return $this->ajaxBlockUnregister();
		}		

		if( $friendsModel->isMyRequest( $requestId , $my->id) )
		{
			$connected		= $friendsModel->approveRequest( $requestId );

			if( $connected )
			{
				$act			= new stdClass();
				$act->cmd 		= 'friends.request.approve';
				$act->actor   	= $connected[0];
				$act->target  	= $connected[1];
				$act->title	  	= JText::_('COM_COMMUNITY_ACTIVITY_FRIENDS_NOW');
				$act->content	= '';
				$act->app		= 'friends';
				$act->cid		= 0;

				CFactory::load ( 'libraries', 'activities' );
				CActivityStream::add($act);
				
				//add user points - give points to both parties
				CFactory::load( 'libraries' , 'userpoints' );		
				CUserPoints::assignPoint('friends.request.approve');				

				$friendId		= ( $connected[0] == $my->id ) ? $connected[1] : $connected[0];
				$friend			= CFactory::getUser( $friendId );
				$friendUrl      = CRoute::_('index.php?option=com_community&view=profile&userid='.$friendId);	
				CUserPoints::assignPoint('friends.request.approve', $friendId);

				// need to both user's friend list
				$friendsModel->updateFriendCount( $my->id );
				$friendsModel->updateFriendCount( $friendId );
			
				// Add the friend count for the current user and the connected user
				// @moved to internal trigger
				
				// Add notification
				CFactory::load( 'libraries' , 'notification' );
				
				$params			= new CParameter( '' );
				$params->set( 'url' , 'index.php?option=com_community&view=profile&userid='.$my->id );

				CNotificationLibrary::add( 'etype_friends_create_connection' , $my->id , $friend->id , JText::sprintf('COM_COMMUNITY_FRIEND_REQUEST_APPROVED', $my->getDisplayName() ) , '' , 'friends.approve' , $params );

				$objResponse->addScriptCall( 'joms.jQuery("#pending-' . $requestId . '").html(\'<div class="jsNotificationActor">' . JText::sprintf('COM_COMMUNITY_FRIEND_REQUEST_ACCEPTED', $friend->getDisplayName(), $friendUrl) . '</div>\').removeClass("jsNotificationHasActions");');
				
				$objResponse->addScriptCall('update_counter("#jsMenuNotif > .notifcount", -1);');
				$objResponse->addScriptCall('update_counter("#jsMenuFriend > .notifcount", -1);');
			
				//trigger for onFriendApprove
				$eventObject = new stdClass();
				$eventObject->profileOwnerId 	= $my->id;
				$eventObject->friendId 			= $friendId;
				$this->triggerFriendEvents( 'onFriendApprove' , $eventObject);
				unset($eventObject);
			}
		}
		else
		{
			$objResponse->addScriptCall( 'joms.jQuery("#request-notice").html("' . JText::_('COM_COMMUNITY_FRIENDS_NOT_YOUR_REQUEST') . '");' );
			$objResponse->addScriptCall( 'joms.jQuery("#request-notice").attr("class", "error");');
		}
		$this->cacheClean(array(COMMUNITY_CACHE_TAG_ACTIVITIES));
		return $objResponse->sendResponse();
	}
	
	public function ajaxSaveFriend($postVars)
	{
		$objResponse   = new JAXResponse();
		
		$filter	    =	JFilterInput::getInstance();
		$postVars = $filter->clean( $postVars, 'array' );

		//@todo filter paramater
		$model =& $this->getModel('friends');
		$my = CFactory::getUser();
		
		if($my->id == 0)
		{
		   return $this->ajaxBlockUnregister();
		}		

		$postVars = CAjaxHelper::toArray($postVars);
		$id = $postVars['userid']; //get it from post
		$msg = strip_tags($postVars['msg']);
		$data  = CFactory::getUser($id);

		// @rule: Do not allow users to request more friend requests as they are allowed to.
		CFactory::load( 'libraries' , 'limits' );

		if( CLimitsLibrary::exceedDaily( 'friends' ) )
		{
			$actions	 = '<form method="post" action="" style="float:right;">'; 
			$actions	.= '<input type="button" class="button" onclick="cWindowHide();return false;" name="cancel" value="'.JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON').'" />';
			$actions	.= '</form>';
			$html		= JText::_( 'COM_COMMUNITY_LIMIT_FRIEND_REQUEST_REACHED' );	
			$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

			return $objResponse->sendResponse();  
		}
		
		if(count($postVars)>0)
		{
			$model->addFriend($id, $my->id, $msg);

			$html 	 = JText::sprintf( 'COM_COMMUNITY_FRIENDS_WILL_RECEIVE_REQUEST' , $data->getDisplayName());
			$actions = '<button class="button" onclick="cWindowHide();" name="close">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';

			$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

			// Add notification
			CFactory::load( 'libraries' , 'notification' );
			
			$params			= new CParameter( '' );
			$params->set('url' , 'index.php?option=com_community&view=friends&task=pending' );
			$params->set('msg' , $msg );

			CNotificationLibrary::add( 'etype_friends_request_connection' , $my->id , $id , JText::sprintf('COM_COMMUNITY_FRIEND_ADD_REQUEST', $my->getDisplayName() ) , '' , 'friends.request' , $params );
			
			//add user points - friends.request.add removed @ 20090313
			//trigger for onFriendRequest
			$eventObject = new stdClass();
			$eventObject->profileOwnerId 	= $my->id;
			$eventObject->friendId 			= $id;
			$this->triggerFriendEvents( 'onFriendRequest' , $eventObject);
			unset($eventObject);
		 }


		return $objResponse->sendResponse();
	}

	/**
	 * Show internal invite
	 * Internal invite is more like an internal messaging system
	 */
	public function ajaxInvite() {
		return $objResponse->sendResponse();
	}

	/**
	 * Show import friends from other account
	 *
	 */
	public function ajaxFrindImport() {
	}


	/**
	 * Displays a dialog to the user if he / she really wants to
	 * cancel the friend request
	 **/
	public function ajaxCancelRequest( $friendsId )
	{
		$my = CFactory::getUser();
		
		$filter	    =	JFilterInput::getInstance();
		$friendsId = $filter->clean( $friendsId, 'int' );
		
		if($my->id == 0)
		{
		   return $this->ajaxBlockUnregister();
		}	
	
		$objResponse	= new JAXResponse();

		$html		= '<div>' . JText::_('COM_COMMUNITY_FRIENDS_CONFIRM_CANCEL_REQUEST') . '</div>';

		$formAction	= CRoute::_('index.php?option=com_community&view=friends&task=deleteSent' , false );
		$actions	= '<form name="cancelRequest" action="' . $formAction . '" method="POST">';
		$actions	.= '<input type="submit" class="button" name="save" value="' . JText::_('COM_COMMUNITY_YES_BUTTON') . '" />&nbsp;';
		$actions	.= '<input type="hidden" name="fid" value="' . $friendsId . '" />';
		$actions	.= '<input type="button" class="button" onclick="javascript:cWindowHide();return false;" name="cancel" value="'.JText::_('COM_COMMUNITY_NO_BUTTON').'" />';
		$actions	.= '</form>';

		$objResponse->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_CANCEL_FRIEND_REQUEST'));
		$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

		$objResponse->sendResponse();
	}
	
	/**
	 * Show the connection request box
	 */
	public function ajaxConnect( $friendId )
	{
		// Block unregistered users.
		if (!COwnerHelper::isRegisteredUser())
		{
			return $this->ajaxBlockUnregister();
		}
		
		$objResponse = new JAXResponse();
		
		$filter	    =	JFilterInput::getInstance();
		$friendId = $filter->clean( $friendId, 'int' );

		//@todo filter paramater
		$model	        =& $this->getModel('friends');
		$blockModel     =& $this->getModel('block'); 

        $my 			= CFactory::getUser();
		$view			= $this->getView('friends');
		$user  			= CFactory::getUser($friendId);
        
        CFactory::load('libraries','block');
        $blockUser   = new blockUser();  
		$config			= CFactory::getConfig();
		
		CFactory::load( 'helpers' , 'owner' );

		CFactory::load( 'libraries' , 'limits' );

		if( CLimitsLibrary::exceedDaily( 'friends' ) )
		{
			$actions	 = '<form method="post" action="" style="float:right;">'; 
			$actions	.= '<input type="button" class="button" onclick="cWindowHide();return false;" name="cancel" value="'.JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON').'" />';
			$actions	.= '</form>';
			$html		= JText::_( 'COM_COMMUNITY_LIMIT_FRIEND_REQUEST_REACHED' );	
			$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

			return $objResponse->sendResponse();  
		}
		
		// Block blocked users
		if( $blockModel->getBlockStatus($my->id,$friendId) && !COwnerHelper::isCommunityAdmin() ){    
		      $blockUser->ajaxBlockMessage();
        }
        
        // Warn owner that the user has been blocked, cannot add as friend
		if( $blockModel->getBlockStatus($friendId,$my->id) ){    
		      $blockUser->ajaxBlockWarn();
        }
        
        
		$connection		= $model->getFriendConnection( $my->id , $friendId );
		
		$html = '';
		$actions = '';
		CFactory::load( 'helpers' , 'string' );
		//@todo disallow self add as a friend
		//@todo disallow add existing friend
		if($my->id == $friendId)
		{
			$html = JText::_('COM_COMMUNITY_FRIENDS_CANNOT_ADD_SELF');
		}
		elseif($user->isBlocked()){
			$html = JText::_('COM_COMMUNITY_FRIENDS_CANNOT_ADD_INACTIVE_USER');
		}
		elseif(count( $connection ) > 0 )
		{
			if( $connection[0]->connect_from == $my->id )
			{
				$html = JText::sprintf('COM_COMMUNITY_FRIENDS_REQUEST_ALREADY_SENT', $user->getDisplayName());
			}
			else
			{
				$html = JText::sprintf('COM_COMMUNITY_FRIEND_REQUEST_ALREADY_RECEIVED', $user->getDisplayName());
			}
		}
		else
		{
			ob_start();
		?>
			<div id="addFriendContainer">
				<p><?php echo JText::sprintf('COM_COMMUNITY_CONFIRM_ADD_FRIEND' , $user->getDisplayName() );?></p>
				<form name="addfriend" id="addfriend" method="post" action="">				
			        <img class="cAvatar" src="<?php echo $user->getThumbAvatar(); ?>" alt="<?php echo CStringHelper::escape( $user->getDisplayName() );?>" alt=""/>
					<textarea class="inputbox" name="msg"><?php echo JText::_('COM_COMMUNITY_PROFILE_ADD_FRIEND_DEFAULT'); ?></textarea>
					<input type="hidden" class="button" name="userid" value="<?php echo $user->id; ?>"/>
				</form>
			</div>
		<?php
			$html	= ob_get_contents();
			ob_end_clean();

		    $actions  = '<button class="button" onclick="joms.friends.addNow();" name="save">' . JText::_('COM_COMMUNITY_FRIENDS_ADD_BUTTON') . '</button>';
		    $actions .= '<button class="button" onclick="javascript:cWindowHide();" name="cancel">' . JText::_('COM_COMMUNITY_CANCEL_BUTTON') . '</button>';
		}

		$objResponse->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_FRIENDS_ADD_NEW_FRIEND'));
		$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

		return $objResponse->sendResponse();
	}

	public function ajaxConfirmFriendRemoval($friendId)
	{
		$objResponse = new JAXResponse();
		
		$filter	    =	JFilterInput::getInstance();
		$friendId = $filter->clean( $friendId, 'int' );
		$my = CFactory::getUser();
		
		// Get html
		$user = CFactory::getUser($friendId);
		
		// Update friend list of both current user and friend
		$user->updateFriendList(true);
		$my->updateFriendList(true);

		ob_start();
		?>
			<p><?php echo JText::sprintf('COM_COMMUNITY_FRIEND_REMOVAL_WARNING', $user->getDisplayName()); ?></p>
			<br/>
			<input type="checkbox" name="block"/><?php echo JText::_('COM_COMMUNITY_ALSO_BLOCK_FRIEND'); ?>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
			
		// Get action
		ob_start();
		?>
			<button class="button" onclick="joms.friends.remove(<?php echo $user->id; ?>);" name="yes">
				<?php echo JText::_('COM_COMMUNITY_YES_BUTTON'); ?>
			</button>

			<button class="button" onclick="cWindowHide();" name="no">
				<?php echo JText::_('COM_COMMUNITY_NO_BUTTON'); ?>
			</button>
		<?php
		$actions = ob_get_contents();
		ob_end_clean();

		$objResponse->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_REMOVE_MEMBER'));
		$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

		return $objResponse->sendResponse();
	}

	public function ajaxBlockFriend($id)
	{
		$objResponse = new JAXResponse();
		
		$filter	    =	JFilterInput::getInstance();
		$id = $filter->clean( $id, 'int' );

		$user = CFactory::getUser($id);

		if ($this->block($id))
		{
			$html = JText::sprintf('COM_COMMUNITY_FRIEND_BLOCKED', $user->getDisplayName());
			$objResponse->addScriptCall('joms.jQuery(\'#friend-' . $user->id . '\').remove();');
		} else {
			$html = JText::_('COM_COMMUNITY_ERROR_BLOCK_USER');
		}

		// Get action
		ob_start();
		?>
			<button class="button" onclick="cWindowHide();" name="ok">
				<?php echo JText::_('COM_COMMUNITY_OK_BUTTON'); ?>
			</button>
		<?php
		$actions = ob_get_contents();
		ob_end_clean();

		$objResponse->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_BLOCK_FRIEND'));
		$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

		return $objResponse->sendResponse();
	}

	public function ajaxRemoveFriend($id)
	{
		$objResponse = new JAXResponse();
		
		$filter	    =	JFilterInput::getInstance();
		$id = $filter->clean( $id, 'int' );
		$my = CFactory::getUser();

		if ($this->delete($id))
		{
			$friend = CFactory::getUser($id);
			
			// Update friend list of both current user and friend
			$friend->updateFriendList(true);
			$my->updateFriendList(true);
			
			$html = JText::sprintf('COM_COMMUNITY_FRIENDS_REMOVED', $friend->getDisplayName());
			$objResponse->addScriptCall('joms.jQuery(\'#friend-' . $id . '\').remove();');
		} else {
			$html = JText::_('COM_COMMUNITY_FRIENDS_REMOVING_FRIEND_ERROR');
		}

		// Get action
		ob_start();
		?>
			<button class="button" onclick="cWindowHide();" name="ok">
				<?php echo JText::_('COM_COMMUNITY_OK_BUTTON'); ?>
			</button>		
		<?php
		$actions = ob_get_contents();
		ob_end_clean();

		$objResponse->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_REMOVE_FRIEND'));
		$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

		return $objResponse->sendResponse();
	}

	/**
	 * List down all connection request waiting for user to approve
	 */
	public function pending()
	{	

		$my		= CFactory::getUser();
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}

		$view		= $this->getView('friends');
		$model		=& $this->getModel('friends');
		$usermodel	=& $this->getModel('user');

		// @todo: make sure the rejectId and approveId is valid for this user
		if($id = JRequest::getVar('rejectId', 0, 'GET'))
		{
		    $mainframe =& JFactory::getApplication();

			if(! $model->rejectRequest($id)){
				$mainframe->enqueueMessage(JText::sprintf('COM_COMMUNITY_FRIENDS_REQUEST_REJECT_FAILED', $id));
			}
		}

		if($id = JRequest::getVar('approveId', 0, 'GET'))
		{
			$mainframe =& JFactory::getApplication();
			$connected = $model->approveRequest($id);

			// If approbe id is not valid or already approve, $connected will
			// be null.. yuck
			if($connected) {
				$act = new stdClass();
				$act->cmd 		= 'friends.request.approve';
				$act->actor   	= $connected[0];
				$act->target  	= $connected[1];
				$act->title	  	= JText::_('COM_COMMUNITY_ACTIVITY_FRIENDS_NOW');
				$act->content	= '';
				$act->app		= 'friends';
				$act->cid		= 0;

				CFactory::load ( 'libraries', 'activities' );
				CActivityStream::add($act);
				
				//add user points - give points to both parties
				CFactory::load( 'libraries' , 'userpoints' );		
				CUserPoints::assignPoint('friends.request.approve');				

				$friendId = ( $connected[0] == $my->id ) ? $connected[1] : $connected[0];
				$friend = CFactory::getUser($friendId);
				CUserPoints::assignPoint('friends.request.approve', $friendId);

				$mainframe->enqueueMessage(JText::sprintf('COM_COMMUNITY_FRIENDS_NOW', $friend->getDisplayName()));
			}
		}

		$data		= new stdClass();
		$rpending	= $model->getPending($my->id);

		$data->pending		= $rpending;
		$data->pagination	=& $model->getPagination();

		echo $view->get(__FUNCTION__, $data);
	}

	/**
	 * Browse the active user's friends
	 */
	public function browse(){
		$view =& $this->getView('friends');
		echo $view->get('browse');

	}

	public function search()
	{
		$view =& $this->getView('friends');
		$data = array();
		$data['query'] 	= '';
		$data['result']	= null;

		// If a query is sent, seach for it
		if($query = JRequest::getVar('q', '', 'POST')){
			$model =& $this->getModel('friends');
			$data['result'] = $model->searchPeople($query);
			$data['query'] 	= $query;
		}

		echo $view->get(__FUNCTION__, $data);
	}
	
	/*
	 * friends event name
	 * object	 	
     */	
	public function triggerFriendEvents( $eventName, &$args, $target = null)
	{
		CError::assert( $args , 'object', 'istype', __FILE__ , __LINE__ );
		
		require_once( COMMUNITY_COM_PATH.DS.'libraries' . DS . 'apps.php' );
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();		
		
		$params		= array();
		$params[]	= &$args;
		
		if(!is_null($target))
			$params[]	= $target;
				
		$appsLib->triggerEvent( $eventName , $params);
		return true;
	}
	
	/**
	 * Block user
	 */
	public function blockUser()
	{                          	
		$my		= CFactory::getUser();
		
		if($my->id == 0)
		{
		   return $this->blockUnregister();
		}
		
		$userId	= JRequest::getVar('fid','','GET');
        
        CFactory::load ( 'libraries', 'block' );
        $blockUser  = new blockUser;
        $blockUser->block( $userId );
	}

	public function delete($id)
	{
		$my		   = CFactory::getUser();
		$friend    = CFactory::getUser($id);

		if( empty($my->id) || empty($friend->id) ) 
			return false;

		CFactory::load( 'helpers' , 'friends' );
		$isFriend = $my->isFriendWith($friend->id);
		if (!$isFriend)
			return true;

		$model = CFactory::getModel('friends');

		if (!$model->deleteFriend($my->id, $friend->id))
			return false;

		// Substract the friend count
		$model->updateFriendCount( $my->id );
		$model->updateFriendCount( $friend->id );
		
		// Add user points
		// We deduct points to both parties
		CFactory::load( 'libraries' , 'userpoints' );
		CUserPoints::assignPoint('friends.remove');
		CUserPoints::assignPoint('friends.remove', $friend->id);

		// Trigger for onFriendRemove
		$eventObject = new stdClass();
		$eventObject->profileOwnerId = $my->id;
		$eventObject->friendId       = $friend->id;
		$this->triggerFriendEvents('onFriendRemove', $eventObject);
		unset($eventObject);

		return true;
	}

	public function block($id)
	{	
		$my			= CFactory::getUser();
		$friend     = CFactory::getUser($id);

		if( empty($my->id) || empty($friend->id) ) 
			return false;
		
		$model	= CFactory::getModel('block');

		if (!$model->blockUser($my->id, $friend->id))
			return false;
		
		$this->delete($friend->id);
			
		return true;
	}

	public function mutualFriends()
	{
		CFactory::load('libraries', 'privacy');
		$document =& JFactory::getDocument();
		$my =& JFactory::getUser();

		$viewType		= $document->getType();
		$tagsFriends	= JRequest::getVar( 'tags','','GET');

		$view	=& $this->getView( 'friends','',  $viewType);
		$model	=& $this->getModel('friends');

		// Get the friend id to be displayed
		$id = JRequest::getCmd('userid', $my->id);

		// Check privacy setting
		$accesAllowed = CPrivacy::isAccessAllowed($my->id, $id, 'user', 'privacyFriendsView');
		if(!$accesAllowed || ($my->id == 0 && $id == 0))
		{
			$this->blockUnregister();
			return;
		}

		$data	= new stdClass();
		echo $view->get('friends');
	}

	private function _fillSearchValues(&$fields, $search)
	{
		if(isset($search)){
			foreach($fields as $group)
			{
				$field = $group->fields;

				for($i = 0; $i <count($field); $i++){
					$fieldid	= $field[$i]->id;
					if(!empty($search['field'.$fieldid])){
						$tmpEle = $search['field'.$fieldid];
						if(is_array($tmpEle)){
							$tmpStr = "";
							foreach($tmpEle as $ele){
								$tmpStr .= $ele.',';
							}
							$field[$i]->value = $tmpStr;
						} else {
							$field[$i]->value = $search['field'.$fieldid];
						}
					}
				}//end for i
			}//end foreach
		}
		return $fields;
	}
}
