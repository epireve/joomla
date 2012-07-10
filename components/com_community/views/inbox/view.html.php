<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');
jimport( 'joomla.utilities.arrayhelper');

class CommunityViewInbox extends CommunityView
{

	public function _addSubmenu()
	{
		$this->addSubmenuItem('index.php?option=com_community&view=inbox', JText::_('COM_COMMUNITY_INBOX') );
		$this->addSubmenuItem('index.php?option=com_community&view=inbox&task=sent', JText::_('COM_COMMUNITY_INBOX_SENT'));
		$this->addSubmenuItem('index.php?option=com_community&view=inbox&task=write', JText::_('COM_COMMUNITY_INBOX_WRITE') );

		$task		= JRequest::getVar( 'task' , '' , 'REQUEST' );
		
		if(! empty($task) && $task == 'read')
		{
			$msgid		= JRequest::getVar('msgid' , '' , 'REQUEST');
			$this->addSubmenuItem('index.php?option=com_community&view=inbox&task=markUnread&msgid='.$msgid, JText::_('COM_COMMUNITY_INBOX_MARK_UNREAD'), '', true );		
		}		
	}
	
	public function showSubmenu(){
		$this->_addSubmenu();
		parent::showSubmenu();
	}
	
	public function display($tpl = null)
	{
		$this->inbox();
	}		
	
	public function inbox($data)
	{
		if(!$this->accessAllowed('registered'))	return;

		$mainframe =& JFactory::getApplication();
		$my	=& JFactory::getUser();	
		$config		= CFactory::getConfig();
		if( !$config->get('enablepm') )
		{
			echo JText::_('COM_COMMUNITY_PRIVATE_MESSAGING_DISABLED');
			return;
		}

		//page title
		$this->addPathway( JText::_('COM_COMMUNITY_INBOX_TITLE') );
		
		$inboxModel 	= CFactory::getModel( 'inbox' );
		
		$document = JFactory::getDocument();
		$document->setTitle( JText::_('COM_COMMUNITY_INBOX_TITLE') );
		$this->showSubMenu();

		if(empty($data->msg))
		{
		?>
				<div class="community-empty-list"><?php echo JText::_('COM_COMMUNITY_INBOX_MESSAGE_EMPTY'); ?></div>	   
		<?php
		}
		else
		{
			CFactory::load( 'libraries' , 'tooltip' );

			for( $i = 0; $i < count( $data->msg ) ; $i++ )
			{
				$row		=& $data->msg[$i];
				$user			= CFactory::getUser( ($row->to==$my->id)?$row->from:$row->to );
				
				$row->avatar	= $user->getThumbAvatar();
				$row->isUnread     	= ( $row->unRead > 0 ) ? true : false;
				
				$row->from_name	= $user->getDisplayName();
			}
			$tmpl = new CTemplate();
			
			echo $tmpl	->set('totalMessages'	, $inboxModel->getUserInboxCount() )
						->set('messages'	, $data->msg )
						->set('pagination'	, $data->pagination->getPagesLinks())
						->fetch('inbox.list');
		}
	}
	
	
	public function sent($data)
	{
	    if(!$this->accessAllowed('registered'))	return;
		
		$mainframe =& JFactory::getApplication();
		$my	=& JFactory::getUser();
		$config		= CFactory::getConfig();
		
		if( !$config->get('enablepm') )
		{
			echo JText::_('COM_COMMUNITY_PRIVATE_MESSAGING_DISABLED');
			return;
		}
		
		$this->showSubMenu();
		//page title
		$pathway 	=& $mainframe->getPathway();

		$pathway->addItem( JText::_('COM_COMMUNITY_INBOX_TITLE'), CRoute::_('index.php?option=com_community&view=inbox'));
		$pathway->addItem( JText::_('COM_COMMUNITY_INBOX_SENT_MESSAGES_TITLE') , '');
		
		$document = JFactory::getDocument();
		$document->setTitle( JText::_('COM_COMMUNITY_INBOX_SENT_MESSAGES_TITLE') );		
		
		if(empty($data->msg))
		{
		?>		
			
				<div class="community-empty-list"><?php echo JText::_('COM_COMMUNITY_INBOX_MESSAGE_EMPTY'); ?></div>

		<?php
		}
		else
		{	
			$appsLib	=& CAppPlugins::getInstance();
			$appsLib->loadApplications();

			for( $i = 0; $i < count( $data->msg ) ; $i++ )
			{
				$row		=& $data->msg[$i];
				
				// onMessageDisplay Event trigger
				$args = array();
				$args[]	=& $row;
				$appsLib->triggerEvent( 'onMessageDisplay' , $args );				
				
				$user			= CFactory::getUser( $row->from );
				$row->from_name	= $user->getDisplayName();
				$row->avatar	= $user->getThumbAvatar();
				$row->isUnread	= false; // for sent item, always set to false.
			}

			$tmpl = new CTemplate();
			echo $tmpl->set('messages', $data->msg )
				->set('pagination', $data->pagination->getPagesLinks())
				->fetch('inbox.list');
		}
	}
	

	
	public function write($data)
	{
		if(!$this->accessAllowed('registered'))return;
		
		$mainframe 	=& JFactory::getApplication();
		$my			=& JFactory::getUser();
		$config		= CFactory::getConfig();
		
		if( !$config->get('enablepm') )
		{
			echo JText::_('COM_COMMUNITY_PRIVATE_MESSAGING_DISABLED');
			return;
		}
		//page title
		$pathway 	=& $mainframe->getPathway();

		$pathway->addItem(JText::_('COM_COMMUNITY_INBOX_TITLE'), CRoute::_('index.php?option=com_community&view=inbox'));
		$pathway->addItem(JText::_('COM_COMMUNITY_INBOX_TITLE_WRITE'), '');
		
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_INBOX_TITLE_WRITE'));		
										
		$this->showSubMenu();

 		$autoCLink  = CRoute::_(JURI::base().('index.php?option=com_community&view=inbox&task=ajaxAutoName&no_html=1&tmpl=component'));
 		
		$js = 'assets/validate-1.5';
		$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
		CAssets::attach($js, 'js');
 		
		$js = 'assets/autocomplete-1.0.js';
		CAssets::attach($js, 'js');
 		
		$js =<<<SHOWJS
		  var yPos;

			joms.jQuery().ready(function(){
				joms.jQuery("#toDisplay").autocomplete("$autoCLink", {
					minChars:1, 
					cacheLength:10, 
					selectOnly:1,
					matchSubset:true, 
					matchContains:true, 
					multiple:false,
					scrollHeight: 200,
					formatItem: function(data, i, n, value) {
						var formatHTML = '<div class="cInbox-ACResult"><img src="'+data[2]+'" />'+data[0]+'</div><div class="clr"></div>';
						return formatHTML;
					},
					formatResult: function(data, value) {
						return data[0];
	 				}
	 			});
			});
SHOWJS;

		$document->addScriptDeclaration($js);
		
		if($data->sent)
		{
			return;
		}
				
		$inboxModel	= CFactory::getModel( 'inbox' );
		$totalSent	= $inboxModel->getTotalMessageSent( $my->id );
		


		CFactory::load( 'libraries' , 'apps' );
		$app 		=& CAppPlugins::getInstance();
		$appFields	= $app->triggerEvent('onFormDisplay' , array('jsform-inbox-write'));
		$beforeFormDisplay	= CFormElement::renderElements( $appFields , 'before' );
		$afterFormDisplay	= CFormElement::renderElements( $appFields , 'after' );

		$tmpl	= new CTemplate();
		echo $tmpl	->set( 'beforeFormDisplay', $beforeFormDisplay )
					->set( 'afterFormDisplay'	, $afterFormDisplay )
					->set( 'autoCLink'		, $autoCLink )
					->set( 'data' 			, $data)
					->set( 'totalSent'		, $totalSent )
					->set( 'maxSent'		, $config->get('pmperday') )
					->set( 'useRealName'	, ($config->get('displayname') == 'name') ? '1' : '0' )
					->fetch('inbox.write');
	}
	
	public function reply($data)
	{
		$mainframe =& JFactory::getApplication();
				
		//page title
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_INBOX_TITLE_REPLY'));		
		
		?>
		<fieldset>
		<form name="writeMessageForm" id="writeMessageForm" action="" method="POST">
			<input type="hidden" name="subject" value="RE :">
			<p>
			Reply to: <?php echo $data['reply_to']; ?>
			</p>
			<div>
			<label style="text-align:top;"><?php echo JText::_('COM_COMMUNITY_MESSAGE'); ?> :</label>
			<textarea name="body"></textarea>
			</div>
			
			<div>
			<?php if($data['allow_reply']){ ?>					
			  <input type="hidden" name="action" value="doSubmit"/>			  
			  <input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SUBMIT_BUTTON');?>"/>
			<?php }//end if ?> 
			<button name="cancel" onclick="javascript: history.go(-1); return false;"><?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON'); ?></button>
			</div>
		</form>
		</fieldset>
		<?php
	}	
	
	/**
	 * Show the message reading window
	 */	 		
	public function read($data)
	{
		$mainframe =& JFactory::getApplication();
		if(!$this->accessAllowed('registered'))
		{
			return;
		}

		//page title
		$document = JFactory::getDocument();
		
		$this->showSubMenu();		
		
		$inboxModel = CFactory::getModel('inbox');
		$my			=& JFactory::getUser();
		$msgid		= JRequest::getVar('msgid', 0, 'REQUEST');
		
		if(!$inboxModel->canRead($my->id, $msgid))
		{
			$mainframe =& JFactory::getApplication();
			$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_PERMISSION_DENIED_WARNING'), 'error');
			return;
		}

		$pathway 	=& $mainframe->getPathway();
		$pathway->addItem( $this->escape( JText::_('COM_COMMUNITY_INBOX_TITLE') ), CRoute::_('index.php?option=com_community&view=inbox') );

		$parentData	= '';
		$html		= '';
		$messageHeading	= '';

		$parentData = $inboxModel->getMessage($msgid);
		
		if(! empty($data->messages))
		{
			$document	= JFactory::getDocument();
			
			$pathway->addItem( $this->escape( $parentData->subject ) );
			$document->setTitle($this->escape( $parentData->subject ) );
			
			require_once( COMMUNITY_COM_PATH.DS.'libraries' . DS . 'apps.php' );			
			$appsLib	=& CAppPlugins::getInstance();
			$appsLib->loadApplications();
				
			foreach ($data->messages as $row)
			{
				// onMessageDisplay Event trigger
				$args = array();
				$args[]	=& $row;
				$appsLib->triggerEvent( 'onMessageDisplay' , $args );
				$user	= CFactory::getUser($row->from);

				//construct the delete link
		        $deleteLink = CRoute::_('index.php?option=com_community&view=inbox&task=remove&msgid='.$row->id);
				$authorLink	= CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->id );
							
				$tmpl = new CTemplate();
				$html .= $tmpl	->set( 'user',  $user )
								->set( 'msg', $row )
								->set( 'isMine' 	, COwnerHelper::isMine($my->id, $user->id))
								->set( 'removeLink', $deleteLink)
								->set( 'authorLink'	, $authorLink )
								->fetch( 'inbox.message' );
			}
			
			$myLink		= CRoute::_('index.php?option=com_community&view=profile&userid=' . $my->id );

			$recipient		= $inboxModel->getRecepientMessage($msgid);
			$recepientCount	= count($recipient);
			$textOther		= $recepientCount > 1 ? 'COM_COMMUNITY_MSG_OTHER' : 'COM_COMMUNITY_MSG_OTHER_SINGULAR';
			
			$messageHeading	= JText::sprintf('COM_COMMUNITY_MSG_BETWEEN_YOU_AND_USER' , $myLink , '#' , JText::sprintf($textOther, $recepientCount));
		} 
		else 
		{
			$html	= '<div class="text">' . JText::_('COM_COMMUNITY_INBOX_MESSAGE_EMPTY') . '</div>';
					
		}//end if

		$tmplMain	= new CTemplate();
		echo $tmplMain	->set( 'messageHeading'	, $messageHeading )
						->set( 'recipient',  $recipient )
						->set( 'messages',  $data->messages )
						->set( 'parentData',  $parentData )
						->set( 'htmlContent',  $html )
						->set( 'my',  $my )
						->fetch( 'inbox.read' );		
	
	}//end messages
	
	public function successPage(){
	
        //page title
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_INBOX_TITLE_WRITE'));		
		
		
		$msg = JText::_('COM_COMMUNITY_INBOX_MESSAGE_SENT');
		
		?>	
		<div>
			<div class="text"><?php echo $msg ?></div>
		</div>
		<form>
		    <input type="hidden" name="option" value="com_community">
		    <input type="hidden" name="view" value="inbox">
		    <input type="hidden" name="task" value="write">
			<div>
			    <input type="submit" value="<?php echo JText::_('COM_COMMUNITY_DONE_BUTTON');?>"/>
			</div>
		</form>	
	    <?php
	}
}
