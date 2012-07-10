<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ();

class CommunityInboxController extends CommunityBaseController
{
	var $_icon = 'inbox';

	private function _isSpam( $user , $data )
	{
		$config	= CFactory::getConfig();
		
		// @rule: Spam checks
		if( $config->get( 'antispam_akismet_messages') )
		{
			CFactory::load( 'libraries' , 'spamfilter' );

			$filter				= CSpamFilter::getFilter();
			$filter->setAuthor( $user->getDisplayName() );
			$filter->setMessage( $data );
			$filter->setEmail( $user->email );
			$filter->setURL( JURI::root() );
			$filter->setType( 'message' );
			$filter->setIP( $_SERVER['REMOTE_ADDR'] );

			if( $filter->isSpam() )
			{
				return true;
			}
		}
		return false;
	}
	
	public function ajaxIphoneInbox()
	{
		$objResponse	= new JAXResponse();
		$document		= JFactory::getDocument();

		$viewType	= $document->getType();
		$view		=& $this->getView( 'inbox', '', $viewType );


		$html = '';

		ob_start();
		$this->display();
		$content = ob_get_contents();
		ob_end_clean();

		$tmpl			= new CTemplate();
		$tmpl->set('toolbar_active', 'inbox');
		$simpleToolbar	= $tmpl->fetch('toolbar.simple');

		$objResponse->addAssign('social-content', 'innerHTML', $simpleToolbar . $content);
		return $objResponse->sendResponse();
	}

	public function display() {
		$model	=& $this->getModel ( 'inbox' );
		$msg	=& $model->getInbox ();
		$modMsg	= array ();

		$view	=& $this->getView ( 'inbox' );
		$my		=& JFactory::getUser ();
		
		if($my->id == 0)
		{
			return $this->blockUnregister();
		}
		
		// Add small avatar to each image
		if (! empty ( $msg ))
		{
			foreach ( $msg as $key => $val )
			{
				// based on the grouped message parent. check the unread message
				// count for this user.
				$filter ['parent'] = $val->parent;
				$filter ['user_id'] = $my->id;
				$unRead = $model->countUnRead ( $filter );
				$msg [$key]->unRead = $unRead;
			}
		}
		$data = new stdClass ( );
		$data->msg = $msg;

		$newFilter ['user_id'] = $my->id;
		$data->inbox = $model->countUnRead ( $newFilter );
		$data->pagination = & $model->getPagination ();
		echo $view->get ( 'inbox', $data );
	}

	/**
	 * @todo: user should be loaded from library or other model
	 */
	public function write()
	{
		CFactory::setActiveProfile ();
		$mainframe =& JFactory::getApplication();
		$my		= CFactory::getUser ();
		$view	= & $this->getView ( 'inbox' );
		$data	= new stdClass ( );
		
		if($my->id == 0)
		{
			return $this->blockUnregister();
		}

		$data->to			= JRequest::getVar ( 'friends', array(), 'POST','array' );
//		$data->to			= JRequest::getVar ( 'to', '', 'POST' );
		$data->subject		= JRequest::getVar ( 'subject', '', 'POST' );		
		$data->body			= JRequest::getVar ( 'body', '', 'POST' );
		$data->sent			= 0;
		$model				= & $this->getModel ( 'user' );
		$actualTo			= array ();
		
		// are we saving ??
		if ($saving = JRequest::getVar ( 'action', '', 'POST' ))
		{
			CFactory::load( 'libraries' , 'apps' );
			$appsLib		=& CAppPlugins::getInstance();
			$saveSuccess	= $appsLib->triggerEvent( 'onFormSave' , array('jsform-inbox-write' ));
			
			if( empty($saveSuccess) || !in_array( false , $saveSuccess ) )
			{				
				// @rule: Check if user exceeded limit
				$inboxModel		=& $this->getModel ( 'inbox' );
				$config			= CFactory::getConfig();
				$useRealName	= ($config->get('displayname') == 'name') ? true : false;
				
				$maxSent		= $config->get('pmperday');
				$totalSent		= $inboxModel->getTotalMessageSent( $my->id );
				
				if( $totalSent >=  $maxSent && $maxSent != 0 )
				{
					$mainframe->redirect(CRoute::_('index.php?option=com_community&view=inbox' , false) , JText::_('COM_COMMUNITY_PM_LIMIT_REACHED'));
				}
	
				$validated = true;

				// @rule: Spam checks
				if( $this->_isSpam( $my , $data->subject . ' ' . $data->body ) )
				{
					$view->addWarning( JText::_('COM_COMMUNITY_INBOX_MESSAGE_MARKED_SPAM') );
					$validated	= false;
				}
						     
				// Block users
				CFactory::load( 'helpers' , 'owner' );
				CFactory::load( 'libraries' , 'block' );
		        $getBlockStatus		= new blockUser();

				// Enable multiple recipients
				// @since 2.4
				//$actualTo = $data->to != '' ? explode(',', $data->to) : array();
				$actualTo = $data->to;
				$actualTo = array_unique($actualTo);

				if ( !( count($actualTo) > 0 ) )
				{
					$view->addWarning ( JText::_('COM_COMMUNITY_INBOX_RECEIVER_MISSING') );
					$validated = false;
				}
				
				$tempUser = array();
				foreach ( $actualTo as $recepientId ) {
					// Get name for error message show
					$user = CFactory::getUser($recepientId);
					$name = $user->getDisplayName();
					$thumb = $user->getThumbAvatar();

					if( $getBlockStatus->isUserBlocked($recepientId,'inbox') && !COwnerHelper::isCommunityAdmin() ){
						$view->addWarning ( JText::_('COM_COMMUNITY_YOU_ARE_BLOCKED_BY_USER') . ' - ' . $name);
						$validated = false;
					}

					// restrict user to send message to themselve
					if( $my->id == $recepientId )
					{
						$mainframe->redirect(CRoute::_('index.php?option=com_community&view=inbox&task=write' , false) , JText::_('COM_COMMUNITY_INBOX_MESSAGE_CANNOT_SEND_TO_SELF') , 'error' );
						return;
					}
					
					$tempUser[] = array('rid'=>$recepientId, 'avatar' => $thumb , 'name' => $name); //since 2.4, to keep track previous 'to' info
				}
				
				$data->toUsersInfo = $tempUser;
		        
				if (empty ( $data->subject ))
				{
					$view->addWarning ( JText::_('COM_COMMUNITY_INBOX_SUBJECT_MISSING') );
					$validated = false;
				}
	
				if (empty ( $data->body ))
				{
					$view->addWarning ( JText::_('COM_COMMUNITY_INBOX_MESSAGE_EMPTY') );
					$validated = false;
				}

				// store message
				if ($validated)
				{
					$model = & $this->getModel ( 'inbox' );
	
					$msgData		= JRequest::get( 'POST' );
					$msgData ['to'] = $actualTo;

					$msgid = $model->send ( $msgData );
					$data->sent = 1;
			
					//add user points
					CFactory::load( 'libraries' , 'userpoints' );		
					CUserPoints::assignPoint('inbox.message.send');				
	
					// Add notification
					CFactory::load( 'libraries' , 'notification' );
					
					$params			= new CParameter( '' );
					$params->set('url' , 'index.php?option=com_community&view=inbox&task=read&msgid='. $msgid );
					$params->set( 'message' , $data->body );
					$params->set( 'title'	, $data->subject );
					
					foreach ( $actualTo as $recepientId ) {
						CNotificationLibrary::add( 'etype_inbox_create_message' , $my->id , $recepientId , JText::sprintf('COM_COMMUNITY_SENT_YOU_MESSAGE', $my->getDisplayName()) , '' , 'inbox.sent' , $params );
					}
					
					$mainframe->redirect(CRoute::_('index.php?option=com_community&view=inbox&task=read&msgid=' . $msgid , false) , JText::_('COM_COMMUNITY_INBOX_MESSAGE_SENT'));
					return;
				}
			}
		}
		$inModel	=& $this->getModel ( 'inbox' );

		$newFilter ['user_id'] = $my->id;
		$data->inbox = $inModel->countUnRead ( $newFilter );
		$this->_icon = 'compose';
		echo $view->get ( 'write', $data );
	}
	
	/**
	 * Remove the selected message
	 */
	public function remove() {
		$msgId = JRequest::getVar ( 'msgid', '', 'GET' );
		$my = & JFactory::getUser ();
		$view = & $this->getView ( 'inbox' );
		$model = & $this->getModel ( 'inbox' );
		
		if($my->id == 0)
		{
		return $this->blockUnregister();
		}

		if ($model->removeReceivedMsg ( $msgId, $my->id )) {
			$view->addInfo ( JText::_('COM_COMMUNITY_INBOX_MESSAGE_REMOVED' ) );
		} else {
			$view->addInfo ( JText::_('COM_COMMUNITY_INBOX_MESSAGE_FAILED_REMOVE' ));
		}
		$this->display ();
	}

	/**
	 * View all sent emails
	 */
	public function sent() {
		CFactory::setActiveProfile ();
		$model = & $this->getModel ( 'inbox' );
		$msg = & $model->getSent ();
		$modMsg = array ();
		


		$view = & $this->getView ( 'inbox' );

		// Add small avatar to each image
		$avatarModel = & $this->getModel ( 'avatar' );
		if (! empty ( $msg )) {
			foreach ( $msg as $key => $val ) {			
			
				if (is_array ( $val->to )) { // multiuser


					$tmpNameArr = array ();
					$tmpAvatar = array ();

					//avatar
					foreach ( $val->to as $toId ) {
						$user			= CFactory::getUser( $toId );
						$tmpAvatar []	= $user->getThumbAvatar();
						$tmpNameArr [] 	= $user->getDisplayName();
					}

					$msg [$key]->smallAvatar	= $tmpAvatar;
					$msg [$key]->to_name 		= $tmpNameArr;
				}
			}
		}
		
		$data = new stdClass ( );
		$data->msg = $msg;

		$my = & JFactory::getUser ();
		$newFilter ['user_id'] = $my->id;
		
		if($my->id == 0)
		{
			return $this->blockUnregister();
		}

		$data->inbox = $model->countUnRead ( $newFilter );
		$data->pagination = & $model->getPagination ();
		$this->_icon = 'sent';
		echo $view->get ( 'sent', $data );
	}

	/**
	 * Open the message thread for reading
	 */
	public function read() {
                //Load Link Generator Helpers
               

                $msgId = JRequest::getVar ( 'msgid', '', 'REQUEST' );
		$my = & JFactory::getUser ();
		
		if($my->id == 0)
		{
		return $this->blockUnregister();
		}

		$filter = array ();

		$filter ['msgId'] = $msgId;
		$filter ['to'] = $my->id;

		$model = & $this->getModel ( 'inbox' );
		$view = & $this->getView ( 'inbox' );
		$data	= new stdClass();
		$data->messages = $model->getMessages ( $filter );

		// mark as "read"
		$filter ['parent'] = $msgId;
		$filter ['user_id'] = $my->id;
		$model->markMessageAsRead ( $filter );			
		// ok done. display the messages.
		echo $view->get ( 'read', $data );

	}

	/**
	 * Reply a message
	 */
	public function reply() {
		$msgId = JRequest::getVar ( 'msgid', '', 'REQUEST' );

		$my = CFactory::getUser ();
		$model = & $this->getModel ( 'inbox' );
		$view = & $this->getView ( 'inbox' );
		$allowReply = 1;
		
		if($my->id == 0)
		{
		return $this->blockUnregister();
		}

		$message = $model->getMessage ( $msgId );
		$messageRecepient = $model->getUserMessage ( $msgId );

		// make sure we can only reply to message that belogn to current user
		$myMsg = true;
		if (! empty ( $message )) {
			$myMsg = ($my->id == $message->from);
		}

		if (! empty ( $messageRecepient )) {
			$myMsg = ($my->id == $messageRecepient->to);
		}

		if (! $myMsg) {
			//show warning
			$view->addWarning ( 'COM_COMMUNITY_INBOX_NOT_ALLOWED_TO_REPLY_MESSAGE' );
			$allowReply = 0;
		}

		$cDate = & JFactory::getDate (); //get the current date from system.
		$obj = new stdClass ( );
		$obj->id        = null;
		$obj->from      = $my->id;
		$obj->posted_on = $cDate->toMySQL ();
		$obj->from_name = $my->name;
		$obj->subject   = 'RE:' . $message->subject;
		$obj->body      = JRequest::getVar ( 'body', '', 'POST' );

		if ('doSubmit' == JRequest::getVar ( 'action', '', 'POST' )) {
			$model->sendReply ( $obj, $msgId );
			$view->addInfo ( JText::_('COM_COMMUNITY_INBOX_MESSAGE_SENT'));
			
			//add user points
			CFactory::load( 'libraries' , 'userpoints' );		
			CUserPoints::assignPoint('inbox.message.reply');			
		}

		$data = array ();
		$data ['reply_to'] = $message->from_name;
		$data ['allow_reply'] = $allowReply;

		echo $view->get ( 'reply', $data );
	}
	
	/**
	 * Remove a message via ajax
	 * A user can only remove a message that he can read/reply to.
	 */
	public function ajaxRemoveFullMessages($msgId){
                $filter = JFilterInput::getInstance();
                $msgId = $filter->clean($msgId, 'int');

		$objResponse = new JAXResponse ( );
		$my 	= CFactory::getUser ();
		$view 	= & $this->getView ( 'inbox' );
		$model 	= & $this->getModel ( 'inbox' );
			
		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}
		
		$conv	= $model->getFullMessages($msgId);						
		$delCnt = 0;


                $filter = array ();
                $parentId = $model->getParent ( $msgId );

		$filter ['msgId'] = $parentId;
		$filter ['to'] = $my->id;


		$data	= new stdClass();
		$data->messages = $model->getMessages ( $filter , true);

               
                $childCount = count($data->messages);
                  
		if(! empty($conv))
		{
			foreach($conv as $msg)
			{
				if($model->canReply($my->id, $msg->id)) {
					if ($model->removeReceivedMsg ( $msg->id, $my->id )) {						
						$delCnt++;
					}//end if
				}//end if
			}//end foreach
		}//end if

                if($delCnt > 0) {
                    $objResponse->addScriptCall ( 'totalmessage = '.$childCount.';' );
                    $objResponse->addScriptCall ( 'if(joms.jQuery(\'#message-'. $msgId .'\').attr("class") == "inbox-unread"){joms.jQuery(".notifcount").html((joms.jQuery(".notifcount").html() - totalmessage));}' );
                    $objResponse->addScriptCall ( 'joms.jQuery(\'#message-'. $msgId .'\').remove();' );
                }
		
		$header	    =	JText::_('COM_COMMUNITY_INBOX_TITLE');		
		$message    =	JText::_('COM_COMMUNITY_INBOX_MESSAGES_REMOVED');
		
		$actions    =	'<button class="button" onclick="cWindowHide();">' . JText::_('COM_COMMUNITY_CLOSE_BUTTON') . '</button>';
		
		$objResponse->addAssign('cwin_logo', 'innerHTML', $header);
		$objResponse->addScriptCall('cWindowAddContent', $message, $actions);

		$objResponse->sendResponse ();
	}
	
	/**
	 * Remove a sent message via ajax
	 * A user can only remove a sent message that he can read/reply to.
	 */
	public function ajaxRemoveSentMessages($msgId){
                $filter = JFilterInput::getInstance();
                $msgId = $filter->clean($msgId, 'int');
                
		$objResponse = new JAXResponse ( );
		$my 	= CFactory::getUser ();
		$view 	= & $this->getView ( 'inbox' );
		$model 	= & $this->getModel ( 'inbox' );
			
		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}
		
		$conv	= $model->getSentMessages($msgId);						
		$delCnt = 0;
		
		if(! empty($conv))
		{
			foreach($conv as $msg)
			{
				if($model->canReply($my->id, $msg->id)) {
					if ($model->removeReceivedMsg ( $msg->id, $my->id )) {						
						$delCnt++;
					}//end if
				}//end if
			}//end foreach
		}//end if

		if($delCnt > 0) {
			$objResponse->addScriptCall ( 'joms.jQuery(\'#message-'. $msgId .'\').remove' );
		}
		
		$header	    =	JText::_('COM_COMMUNITY_INBOX_SENT_MESSAGES_TITLE');		
		$message    =	JText::_('COM_COMMUNITY_INBOX_MESSAGES_REMOVED');
		
		$actions    =	'<button class="button" onclick="cWindowHide();">' . JText::_('COM_COMMUNITY_CLOSE_BUTTON') . '</button>';
		
		$objResponse->addAssign('cwin_logo', 'innerHTML', $header);
		$objResponse->addScriptCall('cWindowAddContent', $message, $actions);

		$objResponse->sendResponse ();
	}		

	/**
	 * Remove a message via ajax
	 * A user can only remove a message that he can read/reply to.
	 */
	public function ajaxRemoveMessage($msgId){
                $filter = JFilterInput::getInstance();
                $msgId = $filter->clean($msgId, 'int');
                
		$objResponse = new JAXResponse ( );
		$my 	= CFactory::getUser ();
		$view 	= & $this->getView ( 'inbox' );
		$model 	= & $this->getModel ( 'inbox' );
		
		if($my->id == 0)
		{
		return $this->ajaxBlockUnregister();
		}

		if($model->canReply($my->id, $msgId)) {
			if ($model->removeReceivedMsg ( $msgId, $my->id )) {
				$objResponse->addScriptCall ( 'joms.jQuery(\'#message-'. $msgId .'\').remove' );
			}
		} else {
			$objResponse->addScriptCall('alert', JText::_('COM_COMMUNITY_PERMISSION_DENIED_WARNING'));
		}

		$objResponse->sendResponse ();
	}

	/**
	 * Add reply via ajax
	 * @todo: check permission and message ownership
	 */
	public function ajaxAddReply($msgId, $reply)
	{
                $filter = JFilterInput::getInstance();
                $msgId = $filter->clean($msgId, 'int');
                $reply = $filter->clean($reply, 'string');
                
		$objResponse = new JAXResponse();

		$my = CFactory::getUser ();
		$model = & $this->getModel ( 'inbox' );
		$message = $model->getMessage ( $msgId );
		$messageRecepient = $model->getParticipantsID ( $msgId , $my->id);
		
		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}
         
		// Block users
		CFactory::load( 'helpers' ,'owner' );
		CFactory::load( 'libraries' , 'block' );
		$getBlockStatus		= new blockUser();
        
		if( $getBlockStatus->isUserBlocked($messageRecepient[0],'inbox') && !COwnerHelper::isCommunityAdmin() ){
			$objResponse->addScriptCall( 'alert' , JText::_( 'COM_COMMUNITY_YOU_ARE_BLOCKED_BY_USER' ) );
			$objResponse->sendResponse ();
			return;
		}

		$objResponse->addScriptCall ( 'joms.jQuery(\'textarea.replybox\').css(\'disabled\', false);' );
		$objResponse->addScriptCall ( 'joms.jQuery(\'div.ajax-wait\').hide();' );

		// @rule: Spam checks
		if( $this->_isSpam( $my , $reply ) )
		{
			$objResponse->addScriptCall( 'alert' , JText::_('COM_COMMUNITY_INBOX_MESSAGE_MARKED_SPAM') );
			$objResponse->sendResponse ();
			return;
		}
		
		if ( empty ( $reply ))
		{
			$objResponse->addScriptCall( 'alert' , JText::_( 'COM_COMMUNITY_INBOX_MESSAGE_CANNOT_BE_EMPTY' ) );
			$objResponse->sendResponse ();
			return;
		}

		if ( empty ( $messageRecepient ))
		{
			$objResponse->addScriptCall( 'alert' , JText::_( 'COM_COMMUNITY_INBOX_MESSAGE_CANNOT_FIND_RECIPIENT' ) );
			$objResponse->sendResponse ();
			return;
		}

		// make sure we can only reply to message that belogn to current user
		if ( !$model->canReply($my->id, $msgId) )
		{
			$objResponse->addScriptCall( 'alert' , JText::_( 'COM_COMMUNITY_PERMISSION_DENIED_WARNING' ) );
			$objResponse->sendResponse ();
			return;
		}


		//$cDate =& JFactory::getDate();//get the current date from system.
		//$cDate = & gmdate ( 'Y-m-d H:i:s' ); //get the current date from system. use gmd
		//$date = cGetDate();
		$date	=& JFactory::getDate(); //get the time without any offset!
		

		$obj 			= new stdClass ( );
		$obj->id		= null;
		$obj->from 		= $my->id;
		$obj->posted_on = $date->toMySQL();
		$obj->from_name = $my->name;
		$obj->subject 	= 'RE:' . $message->subject;
		$obj->body 		= $reply;

		$model->sendReply ( $obj, $msgId );
		$deleteLink = CRoute::_('index.php?option=com_community&view=inbox&task=remove&msgid='.$obj->id);
		$authorLink	= CRoute::_('index.php?option=com_community&view=profile&userid=' . $my->id );
		
		//add user points
		CFactory::load( 'libraries' , 'userpoints' );		
		CUserPoints::assignPoint('inbox.message.reply');

		// Add notification
		CFactory::load( 'libraries' , 'notification' );
				
		foreach($messageRecepient as $row)
		{
			$params			= new CParameter( '' );
			
			$params->set( 'message' , $reply );
			$params->set( 'title'	, '' );
			$params->set('url' , 'index.php?option=com_community&view=inbox&task=read&msgid='. $msgId );
	
			CNotificationLibrary::add( 'etype_inbox_create_message' , $my->id , $row , JText::sprintf('COM_COMMUNITY_SENT_YOU_MESSAGE', $my->getDisplayName()) , '' , 'inbox.sent' , $params );
		}
		
		// onMessageDisplay Event trigger
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();		
		$args = array();
		$args[]	=& $obj;
		$appsLib->triggerEvent( 'onMessageDisplay' , $args );		

		$tmpl = new CTemplate();
		$tmpl->set( 'user', CFactory::getUser($obj->from) );
		$tmpl->set( 'msg', $obj );
		$tmpl->set( 'removeLink', $deleteLink);
		$tmpl->set( 'authorLink', $authorLink );
		$html = $tmpl->fetch( 'inbox.message' );

		$objResponse->addScriptCall ( 'cAppendReply', $html );

		return $objResponse->sendResponse ();
	}

	/**
	 * @todo: check permission and message ownership
	 */
	public function ajaxCompose($id) {
                $filter = JFilterInput::getInstance();
                $id = $filter->clean($id, 'int');

		if (!COwnerHelper::isRegisteredUser()) {
			return $this->ajaxBlockUnregister();
		}

		$objResponse = new JAXResponse ( );
		$config = CFactory::getConfig();
		$user 	= CFactory::getUser($id);
		$my 	= CFactory::getUser();
		
		if($my->id == 0)
		{
		return $this->ajaxBlockUnregister();
		}
		
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'libraries' , 'block' );
		$getBlockStatus		= new blockUser();
				
		// Block banned users
		if( $getBlockStatus->isUserBlocked($id,'inbox') && !COwnerHelper::isCommunityAdmin() ){
			$this->ajaxblock();
		}
		
		CFactory::load( 'helpers', 'time' );
		
		$inboxModel =& $this->getModel( 'inbox' );
		$lastSent	= $inboxModel->getLastSentTime($my->id);
		$dateNow = new JDate();
		
		
		CFactory::load( 'helpers' , 'owner' );		

		// We need to make sure that this guy are not spamming other people inbox 
		// by checking against his last message time. Make sure it doesn't exceed
		// pmFloodLimit config (in seconds)
		if( ($dateNow->toUnix() - $lastSent->toUnix()) < $config->get( 'floodLimit' ) && !COwnerHelper::isCommunityAdmin() ){
			$html = '<dl id="system-message"><dt class="notice">'. JText::_('COM_COMMUNITY_NOTICE') .'</dt><dd class="notice message"><ul><li>';
			$html .= JText::sprintf('COM_COMMUNITY_PLEASE_WAIT_BEFORE_SENDING_MESSAGE', $config->get( 'floodLimit' )); 
			$html .= '</li></ul></dd></dl>';
						
			$actions = '<button class="button" onclick="cWindowHide();" name="cancel">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';

			$objResponse->addScriptCall('cWindowAddContent', $html, $actions);
			return $objResponse->sendResponse();
		}
	
		// @rule: Check if user exceeded daily limit		
		$maxSent		= $config->get('pmperday');
		$totalSent		= $inboxModel->getTotalMessageSent( $my->id );
		
		if( $totalSent >=  $maxSent && $maxSent != 0 )
		{
			$html = '<dl id="system-message"><dt class="notice">'. JText::_('COM_COMMUNITY_NOTICE') .'</dt><dd class="notice message"><ul><li>';
			$html .= JText::_('COM_COMMUNITY_PM_LIMIT_REACHED'); 
			$html .= '</li></ul></dd></dl>';
			$actions = '<button class="button" onclick="cWindowHide();" name="cancel">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';
			
			$objResponse->addScriptCall('cWindowAddContent', $html, $actions);
			return $objResponse->sendResponse();
		}
		//====================================
	

		$tmpl = new CTemplate();
		$tmpl->set('user', $user);
		$html = $tmpl->fetch('inbox.ajaxcompose');

		$actions = '<button class="button" onclick="javascript:return joms.messaging.send();" name="send">'. JText::_('COM_COMMUNITY_SEND_BUTTON') .'</button>&nbsp;';
		$actions .= '<button class="button" onclick="javascript:cWindowHide();" name="cancel">'. JText::_('COM_COMMUNITY_CANCEL_BUTTON') .'</button>';
		
		// Change cWindow title
		$objResponse->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_INBOX_TITLE_WRITE'));
		$objResponse->addScriptCall ('cWindowAddContent', $html, $actions);

		return $objResponse->sendResponse();
	}

	/**
	 * A new message submitted via ajax
	 */
	public function ajaxSend($postVars)
	{
                //$postVars pending filtering
		$objResponse	= new JAXResponse ( );
		$config			= CFactory::getConfig();
		$my				= CFactory::getUser ();
		
		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}
		
		CFactory::load( 'helpers', 'time' );
		
		$inboxModel =& $this->getModel( 'inbox' );
		$lastSent	= $inboxModel->getLastSentTime($my->id);
		$dateNow = new JDate();
		
		CFactory::load( 'helpers' , 'owner' );

		// We need to make sure that this guy are not spamming other people inbox 
		// by checking against his last message time. Make sure it doesn't exceed
		// pmFloodLimit config (in seconds)
		if( ($dateNow->toUnix() - $lastSent->toUnix()) < $config->get( 'floodLimit' ) && !COwnerHelper::isCommunityAdmin() ){
			$html = '<dl id="system-message"><dt class="notice">'. JText::_('COM_COMMUNITY_NOTICE') .'</dt><dd class="notice message"><ul><li>';
			$html .= JText::sprintf('COM_COMMUNITY_PLEASE_WAIT_BEFORE_SENDING_MESSAGE', $config->get( 'floodLimit' )); 
			$html .= '</li></ul></dd></dl>';
					
			$actions = '<button class="button" onclick="cWindowHide();" name="cancel">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';

			$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

			return $objResponse->sendResponse();			
		}

		// Prevent user to send message to themselve
		if($postVars['to']==$my->id)
		{ 
			$html = '<dl id="system-message"><dt class="notice">'. JText::_('COM_COMMUNITY_NOTICE') .'</dt><dd class="notice message"><ul><li>';
			$html .= JText::_('COM_COMMUNITY_INBOX_MESSAGE_CANNOT_SEND_TO_SELF'); 
			$html .= '</li></ul></dd></dl>';

			$actions = '<button class="button" onclick="cWindowHide();" name="cancel">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';

			$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

			return $objResponse->sendResponse();
		}

		$postVars = CAjaxHelper::toArray ( $postVars );
		$doCont   = true;
		$errMsg   = "";
		$resizeH  = 0;
		
		if( $this->_isSpam( $my , $postVars[ 'subject' ]  . ' ' . $postVars[ 'body' ] ) )
		{
			$html = '<dl id="system-message"><dt class="notice">' . JText::_('COM_COMMUNITY_NOTICE') . '</dt><dd class="notice message"><ul><li>';
			$html .= JText::_('COM_COMMUNITY_INBOX_MESSAGE_MARKED_SPAM'); 
			$html .= '</li></ul></dd></dl>';

			$actions = '<button class="button" onclick="cWindowHide();" name="cancel">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';

			$objResponse->addScriptCall('cWindowAddContent', $html, $actions);

			return $objResponse->sendResponse();
		}
		
		if( empty($postVars['subject']) || JString::trim($postVars['subject']) == '' )
		{
			$errMsg = '<div class="message">'.JText::_('COM_COMMUNITY_INBOX_SUBJECT_MISSING').'</div>';
			$doCont = false;
			$resizeH += 35;
		}


		if( empty($postVars['body']) || JString::trim($postVars['body']) == '' )
		{
			$errMsg .= '<div class="message">'.JText::_('COM_COMMUNITY_INBOX_MESSAGE_MISSING').'</div>';
			$doCont = false;
			$resizeH += 35;
		}

		if($doCont)
		{
			$data = $postVars;
			$model = & $this->getModel ( 'inbox' );

			$pattern 	 = "/<br \/>/i";
			$replacement = "\r\n";
 			$data['body'] = preg_replace($pattern, $replacement, $data['body'] );

			$msgid = $model->send ($data);

			//add user points
			CFactory::load( 'libraries' , 'userpoints' );
			CFactory::load( 'libraries' , 'notification' );	
			CUserPoints::assignPoint('inbox.message.send');

			// Add notification
			$params			= new CParameter( '' );
			$params->set('url' , 'index.php?option=com_community&view=inbox&task=read&msgid='. $msgid );

			$params->set( 'message' , $data['body'] );
			$params->set( 'title'	, $data['subject'] );	

			CNotificationLibrary::add( 'etype_inbox_create_message' , $my->id , $data[ 'to' ] , JText::sprintf('COM_COMMUNITY_SENT_YOU_MESSAGE', $my->getDisplayName()) , '' , 'inbox.sent' , $params );
		
			$html = JText::_('COM_COMMUNITY_INBOX_MESSAGE_SENT');
			$actions = '<button class="button" onclick="cWindowHide();" name="close">' . JText::_('COM_COMMUNITY_BUTTON_CLOSE_BUTTON') . '</button>';
		} else {
		    //validation failed. display error message.
			$user = CFactory::getUser($postVars['to']);

			$tmpl = new CTemplate();
			$tmpl->set('user', $user);
			$tmpl->set('subject',JString::trim($postVars['subject']));
			$tmpl->set('body',JString::trim($postVars['body']));
			$html = $tmpl->fetch('inbox.ajaxcompose');

			$html = $errMsg . $html;

			$actions = '<button class="button" onclick="javascript:return joms.messaging.send();" name="send">'. JText::_('COM_COMMUNITY_SEND_BUTTON') .'</button>&nbsp;';
			$actions .= '<button class="button" onclick="javascript:cWindowHide();" name="cancel">'. JText::_('COM_COMMUNITY_CANCEL_BUTTON') .'</button>';

			// Change cWindow title
			$objResponse->addAssign('cwin_logo', 'innerHTML', JText::_('COM_COMMUNITY_INBOX_TITLE_WRITE'));
		}

		$objResponse->addScriptCall('cWindowAddContent', $html, $actions);
		$objResponse->addScriptCall('joms.messaging.sendCompleted');
	
		return $objResponse->sendResponse ();
	}

	/**
	 * @todo: need to filter this down. loading too many user at once
	 */
	public function ajaxAutoName() {
		$my 			= CFactory::getUser();
		$config			= CFactory::getConfig();
		$displayName	= $config->get('displayname');	

		$search_term = JRequest::getVar ( 'q', '', 'GET' );		
				
		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}

		$model = & $this->getModel ( 'user' );
		$friendsModel = & $this->getModel ( 'friends' );

		$friends =& $friendsModel->getFriends($my->id,'',false);

		$names = "";

		foreach( $friends as $row ){
			$cur_name = '';
			if($config->get('displayname') == 'name'){
				$cur_name = $row->name;
			}else{
				$cur_name = $row->username;
			}
			
			$filter = strrpos(strtolower($cur_name), strtolower($search_term));
			
			if ($filter !== false) {
				$user 	= CFactory::getUser( $row->id );
				$avatar = $user->getThumbAvatar();
				$names .= $cur_name."|".$row->id."|".$avatar."\n";
			}		
		}

		echo $names;
		exit ();
	}


	/**
	 * Set message as Read
	 */
	public function ajaxMarkMessageAsRead($msgId){
                $filter = JFilterInput::getInstance();
                $msgId = $filter->clean($msgId, 'int');
                
		$objResponse = new JAXResponse ( );
		$my 	= CFactory::getUser ();
		$view 	= & $this->getView ( 'inbox' );
		$model 	= & $this->getModel ( 'inbox' );
				
		if($my->id == 0)
		{
		return $this->ajaxBlockUnregister();
		}

		$filter = array(
			'parent'    => $msgId,
			'user_id'   => $my->id
		);

		$model->markAsRead( $filter );
		$objResponse->addScriptCall ( 'markAsRead', $msgId );
		$objResponse->sendResponse ();
	}



	/**
	 * Set message as Read
	 */
	public function ajaxMarkMessageAsUnread($msgId){
                $filter = JFilterInput::getInstance();
                $msgId = $filter->clean($msgId, 'int');
                
		$objResponse = new JAXResponse ( );
		$my 	= CFactory::getUser ();
		$view 	= & $this->getView ( 'inbox' );
		$model 	= & $this->getModel ( 'inbox' );
		
		if($my->id == 0)
		{
		return $this->ajaxBlockUnregister();
		}
		
		$filter = array(
			'parent'    => $msgId,
			'user_id'   => $my->id
		);

		$model->markAsUnread( $filter );
		$objResponse->addScriptCall ( 'markAsUnread', $msgId );
		$objResponse->sendResponse ();
	}
	
	
	public function markUnread()
	{
		$mainframe	=& JFactory::getApplication();		
		$my 		=& JFactory::getUser ();		
		$model		=& $this->getModel ( 'inbox' );
		
		if($my->id == 0)
		{
		return $this->blockUnregister();
		}
				
		$msgId 	= JRequest::getVar ( 'msgid', '', 'REQUEST' );
		
		if(empty($msgId))
		{
			$mainframe->redirect(CRoute::_('index.php?option=com_community&view=inbox', false), JText::_('COM_COMMUNITY_INBOX_MARK_UNREAD_FAILED'), 'error');
		}
		else
		{			
			$filter = array(
				'parent'    => $msgId,
				'user_id'   => $my->id
			);
				
			$model->markMessageAsUnread( $filter );
			$mainframe->redirect(CRoute::_('index.php?option=com_community&view=inbox', false), JText::_('COM_COMMUNITY_INBOX_MARK_UNREAD_SUCCESS'));
		}
	}
	
	public function ajaxDeleteMessages($task)
	{
		$my =	CFactory::getUser();
		$objResponse	=   new JAXResponse ( );
		
		if($my->id == 0)
		{
			return $this->ajaxBlockUnregister();
		}
		
		if($task=='inbox')
		{
			$header	    =	JText::_('COM_COMMUNITY_INBOX_TITLE');
		}
		elseif($task=='sent')	
		{
			$header	    =	JText::_('COM_COMMUNITY_INBOX_SENT_MESSAGES_TITLE');
		}
		
		$message    =	JText::_('COM_COMMUNITY_INBOX_REMOVE_CONFIRM');
		$actions    =	'<button class="button" onclick="joms.messaging.deleteMarked(\'' . $task . '\');">' . JText::_('COM_COMMUNITY_YES') . '</button>';
		$actions   .=	'&nbsp;<button class="button" onclick="cWindowHide();">' . JText::_('COM_COMMUNITY_NO') . '</button>';
		
		$objResponse->addAssign('cwin_logo', 'innerHTML', $header);
		$objResponse->addScriptCall('cWindowAddContent', $message, $actions);
		
		return $objResponse->sendResponse();
	}
	
}
