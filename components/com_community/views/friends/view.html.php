<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');
jimport( 'joomla.utilities.arrayhelper');
jimport( 'joomla.html.html');

class CommunityViewFriends extends CommunityView
{
	public function _addSubmenu()
	{
		$mySQLVer	= 0;
	
		if(JFile::exists(JPATH_COMPONENT.DS.'libraries'.DS.'advancesearch.php'))
		{	
			require_once (JPATH_COMPONENT.DS.'libraries'.DS.'advancesearch.php');
			$mySQLVer	= CAdvanceSearch::getMySQLVersion();
		}
	
		$this->addSubmenuItem('index.php?option=com_community&view=friends', JText::_('COM_COMMUNITY_FRIENDS_VIEW_ALL'));

		$tmpl = new CTemplate();
		$tmpl->set( 'url', CRoute::_('index.php?option=com_community&view=search') );
		$html = $tmpl->fetch( 'search.submenu' );		
		$this->addSubmenuItem('index.php?option=com_community&view=search', JText::_('COM_COMMUNITY_SEARCH_FRIENDS'), 'joms.videos.toggleSearchSubmenu(this)', SUBMENU_LEFT, $html);

		if($mySQLVer >= 4.1 )
		{
			$this->addSubmenuItem('index.php?option=com_community&view=search&task=advancesearch', JText::_('COM_COMMUNITY_CUSTOM_SEARCH'));
		}
		$this->addSubmenuItem('index.php?option=com_community&view=friends&task=invite', JText::_('COM_COMMUNITY_INVITE_FRIENDS'));
		$this->addSubmenuItem('index.php?option=com_community&view=friends&task=sent', JText::_('COM_COMMUNITY_FRIENDS_REQUEST_SENT'));
		$this->addSubmenuItem('index.php?option=com_community&view=friends&task=pending', JText::_('COM_COMMUNITY_FRIENDS_PENDING_APPROVAL'));
	}

	public function showSubmenu()
	{
		$this->_addSubmenu();
		parent::showSubmenu();
	}
	
	/**
	 * DIsplay list of friends
	 * 
	 * if no $_GET['id'] is set, we're viewing our own friends	 	 
	 */	 	
	public function friends($data = null)
	{
		// Load window library
		CFactory::load( 'libraries' , 'window' );
		
		// Load required filterbar library that will be used to display the filtering and sorting.
		CFactory::load( 'libraries' , 'filterbar' );

		// Load necessary window css / javascript headers.
		CWindow::load();
		
		$mainframe =& JFactory::getApplication();
		$document = JFactory::getDocument();
		$my	= CFactory::getUser();
		$id = JRequest::getInt('userid', null );
		
		if( $id == null )
		{
			$id	= $my->id;
		}
		// Display mini header if user is viewing other user's friend
		if( $id != $my->id )
		{
			$this->attachMiniHeaderUser( $id );
		}

		$feedLink = CRoute::_('index.php?option=com_community&view=friends&format=feed');
		$feed = '<link rel="alternate" type="application/rss+xml" title="' . JText::_('COM_COMMUNITY_SUBSCRIBE_TO_FRIENDS_FEEDS') . '" href="'.$feedLink.'"/>';
		$document->addCustomTag( $feed );

		$friendsModel	= CFactory::getModel('friends');
		$user			= CFactory::getUser($id);
		$params 		= $user->getParams();
		CFactory::load( 'helpers' , 'friends' );
			
		$people  	= CFactory::getModel( 'search' );
		$userModel 	= CFactory::getModel( 'user' );
		$avatar	 	= CFactory::getModel( 'avatar' );
		$friends 	= CFactory::getModel( 'friends' );
		$sorted		= JRequest::getVar( 'sort' , 'latest' , 'GET' );
		$filter		= JRequest::getWord( 'filter' , 'all' , 'GET' );
		
		CFactory::load('helpers', 'friends');
		$rows 		= $friends->getFriends( $id , $sorted , true , $filter );
		$isMine		= ( ($id == $my->id) && ($my->id != 0) );
		$document	= JFactory::getDocument();
		
		$this->addPathway(JText::_('COM_COMMUNITY_FRIENDS'), CRoute::_('index.php?option=com_community&view=friends'));
		
		if( $my->id == $id )
		{
			$this->addPathway(JText::_('COM_COMMUNITY_FRIENDS_MY_FRIENDS') );
		}
		else
		{
			$this->addPathway(JText::sprintf('COM_COMMUNITY_FRIENDS_ALL_FRIENDS', $user->getDisplayName()));
		}
		
		// Hide submenu if we are viewing other's friends
		if( $isMine )
		{
			$this->showSubmenu();
			$document->setTitle(JText::_('COM_COMMUNITY_FRIENDS_MY_FRIENDS'));
		}
		else
		{
			$this->addSubmenuItem('index.php?option=com_community&view=profile&userid=' . $user->id , JText::_('COM_COMMUNITY_PROFILE_BACK_TO_PROFILE'));
			$this->addSubmenuItem('index.php?option=com_community&view=friends&userid=' . $user->id , JText::_('COM_COMMUNITY_FRIENDS_VIEW_ALL'));
			$this->addSubmenuItem('index.php?option=com_community&view=friends&task=mutualFriends&userid=' . $user->id . '&filter=mutual', JText::_('COM_COMMUNITY_MUTUAL_FRIENDS'));

			$tmpl = new CTemplate();
			$tmpl->set('view',"friends");
			$tmpl->set( 'url', CRoute::_('index.php?option=com_community&view=friends&task=viewfriends') );
			$html = $tmpl->fetch( 'friendsearch.submenu' );
			$this->addSubmenuItem('index.php?option=com_community&view=friends&task=viewfriends', JText::_('COM_COMMUNITY_SEARCH_FRIENDS'), 'joms.videos.toggleSearchSubmenu(this)', SUBMENU_LEFT, $html);
			
			parent::showSubmenu ();
			
			$document->setTitle(JText::sprintf('COM_COMMUNITY_FRIENDS_ALL_FRIENDS', $user->getDisplayName()));
		}

		$sortItems =  array(
							'latest' 		=> JText::_('COM_COMMUNITY_SORT_RECENT_FRIENDS') , 
 							'online'		=> JText::_('COM_COMMUNITY_ONLINE'),
							'name'			=> JText::_('COM_COMMUNITY_SORT_ALPHABETICAL')
						);
		
		$config			= CFactory::getConfig();
		$filterItems	= array();
		
		if( $config->get('alphabetfiltering') )
		{
			$filterItems	=  array(
								'all' 	=> JText::_('COM_COMMUNITY_JUMP_ALL') ,
								'abc' 	=> JText::_('COM_COMMUNITY_JUMP_ABC') , 
								'def'	=> JText::_('COM_COMMUNITY_JUMP_DEF') ,
								'ghi'	=> JText::_('COM_COMMUNITY_JUMP_GHI') ,
								'jkl'	=> JText::_('COM_COMMUNITY_JUMP_JKL') ,
								'mno'	=> JText::_('COM_COMMUNITY_JUMP_MNO') ,
								'pqr'	=> JText::_('COM_COMMUNITY_JUMP_PQR') ,
								'stu'	=> JText::_('COM_COMMUNITY_JUMP_STU') ,
								'vwx'	=> JText::_('COM_COMMUNITY_JUMP_VWX') ,
								'yz'	=> JText::_('COM_COMMUNITY_JUMP_YZ') , 
								'others'=> JText::_('COM_COMMUNITY_JUMP_OTHERS')
								);
		}
		
		// Check if friend is banned
		$blockModel	= CFactory::getModel('block');

		$resultRows = array();

		// @todo: preload all friends
		foreach($rows as $row)
		{
			$user = CFactory::getUser($row->id);

			$obj = clone($row);
			$obj->friendsCount  = $user->getFriendCount(); 
			$obj->profileLink	= CUrlHelper::userLink($row->id);
			$obj->isFriend		= true;
			$obj->isBlocked		= $blockModel->getBlockStatus($user->id,$my->id);
			$resultRows[] = $obj;
		}
		unset($rows);
		
		// Should not show recently added filter to otehr people
 		$sortingHTML	= '';

		if($isMine)
		{
			$sortingHTML	= CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'latest' , $filterItems , 'all' );
		}
		
		$tmpl	=   new CTemplate();
		$html	=   $tmpl   ->set( 'isMine'	, $isMine )
				    ->setRef( 'my'	, $my )
				    ->setRef( 'friends'	, $resultRows )
				    ->set('sortings'	, $sortingHTML )
				    ->set( 'config'	, CFactory::getConfig() )
				    ->fetch('friends.list');

		$html .= '<div class="pagination-container">';
		$pagination	= $friends->getPagination();
		$html .= $pagination->getPagesLinks();
		$html .= '</div>';

		echo $html;
	}

	/**
	 * Search list of friends
	 *
	 * if no $_GET['id'] is set, we're viewing our own friends
	 */

	public function friendsearch($data)
	{
		//return $this->search($data);

		require_once (JPATH_COMPONENT.DS.'libraries'.DS.'profile.php');
		require_once (JPATH_COMPONENT.DS.'helpers'.DS.'friends.php');

		$document	= JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_SEARCH_FRIENDS_TITLE'));
		$avatarOnly		= JRequest::getVar( 'avatar' , '' );
		$this->addPathway( JText::_('COM_COMMUNITY_SEARCH_FRIENDS_TITLE') );
		$my				= CFactory::getUser();
		$friendsModel	= CFactory::getModel('friends');
		$resultRows 	= array();
		$id = JRequest::getInt('userid', null );
		if( $id == null )
		{
			$id	= $my->id;
		}
		$user			= CFactory::getUser($id);
		$isMine		= ( ($id == $my->id) && ($my->id != 0) );

		$pagination = (!empty($data)) ? $data->pagination : '';

		$tmpl		= new CTemplate();
		for($i = 0; $i < count( $data->result ); $i++)
		{
			$row 				=& $data->result[$i];
			$user				= CFactory::getUser( $row->id );
			$row->profileLink	= CRoute::_('index.php?option=com_community&view=profile&userid=' . $row->id );
			$row->friendsCount	= $user->getFriendCount();
			$isFriend 			=  CFriendsHelper::isConnected ( $row->id, $my->id );

			$row->user      	= $user;
			$row->addFriend 	= ((! $isFriend) && ($my->id != 0) && $my->id != $row->id) ? true : false;

			$resultRows[] = $row;
		}
		$tmpl->set('data'		, $resultRows);
		$tmpl->set('sortings'	, '');
		$tmpl->set('pagination' , $pagination );

		CFactory::load( 'libraries' , 'tooltip' );
		//JHTML::_('behavior.tooltip');

		CFactory::load( 'libraries' , 'featured' );
		$featured		= new CFeatured( FEATURED_USERS );
		$featuredList	= $featured->getItemIds();

		$tmpl->set('featuredList' , $featuredList);

		CFactory::load( 'helpers' , 'owner' );
		$tmpl->set('isCommunityAdmin', COwnerHelper::isCommunityAdmin() );
		$tmpl->set('showFeaturedList' , false );
		$tmpl->set('my' , $my );
		$resultHTML 	= $tmpl->fetch('people.browse');
		unset( $tmpl );

		$searchLinks	=   parent::getAppSearchLinks('people');

		if( $isMine )
		{
			$this->showSubmenu();
			$document->setTitle(JText::_('COM_COMMUNITY_FRIENDS_MY_FRIENDS'));
		}
		else
		{
			$this->addSubmenuItem('index.php?option=com_community&view=profile&userid=' . $user->id , JText::_('COM_COMMUNITY_PROFILE_BACK_TO_PROFILE'));
			$this->addSubmenuItem('index.php?option=com_community&view=friends&userid=' . $user->id , JText::_('COM_COMMUNITY_FRIENDS_VIEW_ALL'));
			$this->addSubmenuItem('index.php?option=com_community&view=friends&task=mutualFriends&userid=' . $user->id . '&filter=mutual', JText::_('COM_COMMUNITY_MUTUAL_FRIENDS'));

			$tmpl = new CTemplate();
			$tmpl->set('view',"friends");
			$tmpl->set( 'url', CRoute::_('index.php?option=com_community&view=friends&task=viewfriends') );
			$html = $tmpl->fetch( 'friendsearch.submenu' );
			$this->addSubmenuItem('index.php?option=com_community&view=friends&task=viewfriends', JText::_('COM_COMMUNITY_SEARCH_FRIENDS'), 'joms.videos.toggleSearchSubmenu(this)', SUBMENU_LEFT, $html);

			parent::showSubmenu ();

			$document->setTitle(JText::sprintf('COM_COMMUNITY_FRIENDS_ALL_FRIENDS', $user->getDisplayName()));
		}

		$tmpl 		= new CTemplate();
		$tmpl->set( 'avatarOnly'	, $avatarOnly );
		$tmpl->set( 'results'		, $data->result );
		$tmpl->set( 'resultHTML'	, $resultHTML );
		$tmpl->set( 'query'		, $data->query );
		$tmpl->set( 'searchLinks'	, $searchLinks );
		echo $tmpl->fetch( 'friendsearch' );
	}

	public function add($data = null){
		
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_FRIENDS_ADD_NEW_FRIEND'));
		?>
		<div class="app-box">
			<p><?php echo JText::sprintf('COM_COMMUNITY_ADD_USER_AS_FRIEND', $data->name );?></p>
			<form name="addfriend" method="post" action="">
				<div>
					<label><?php echo JText::sprintf('COM_COMMUNITY_INVITE_PERSONAL_MESSAGE_TO' , $data->name ); ?></label>
				</div>
				
				<div>
					<textarea name="msg"></textarea>
				</div>
				
				<div>
					<input type="submit" class="button" name="submit" value="<?php echo JText::_('COM_COMMUNITY_FRIENDS_ADD_BUTTON');?>"/>
					<input type="submit" class="button" name="cancel" value="<?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON');?>"/>
				</div>
				<input type="hidden" class="button" name="id" value="<?php echo $data->id; ?>"/>
			</form>
		</div>
		<?php
	}
	
	public function online($data = null)
	{
		// Load the toolbar
		$this->showHeader(JText::_('COM_COMMUNITY_FRIENDS_ONLINE_FRIENDS'), 'generic');
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_ONLINE_FRIENDS_TITLE'));
	}
	
	public function sent($data = null)
	{
		$mainframe =& JFactory::getApplication();

		// Load window library
		CFactory::load( 'libraries' , 'window' );
		
		// Load necessary window css / javascript headers.
		CWindow::load();
		
		$config	= CFactory::getConfig();
		$my	=& JFactory::getUser();
		if($my->id == 0)
		{
        	$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_PLEASE_LOGIN_WARNING'), 'error');
        	return; 
		}
		
		$this->addPathway(JText::_('COM_COMMUNITY_FRIENDS'), CRoute::_('index.php?option=com_community&view=friends'));
		$this->addPathway(JText::_("COM_COMMUNITY_FRIENDS_WAITING_AUTHORIZATION"), '');
		
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_FRIENDS_WAITING_AUTHORIZATION'));
		$this->showSubMenu();

		$friends 	= CFactory::getModel( 'friends' );
		
		$rows = !empty($data->sent) ? $data->sent : array();

		for( $i = 0; $i < count( $rows ); $i++ )
		{
			$row	=& $rows[$i];
			$row->user	= CFactory::getUser($row->id );
			$row->user->friendsCount  = $row->user->getFriendCount();
			$row->user->profileLink	= CUrlHelper::userLink($row->id);
		}

		$tmpl	=   new CTemplate();
		echo $tmpl  ->set( 'my'	    , $my )
			    ->set( 'config' , $config )
			    ->set( 'rows'   , $rows )
			    ->fetch( 'friends.request' );
	}
	
	public function deleteLink($controller,$method,$id){
		$deleteLink = '<a class="remove" onClick="if(!confirm(\'' . JText::_('COM_COMMUNITY_CONFIRM_DELETE_FRIEND') . '\'))return false;" href="'.CUrl::build($controller,$method).'&fid='.$id.'">&nbsp;</a>';
		return $deleteLink;
	}
	
	/**
	 * Display a list of pending friend requests
	 **/	 	
	public function pending($data = null)
	{
		if(!$this->accessAllowed('registered'))	return;	
	
		$mainframe =& JFactory::getApplication();
		$config		= CFactory::getConfig();
		
		// Load window library
		CFactory::load( 'libraries' , 'window' );
		
		// Load necessary window css / javascript headers.
		CWindow::load();
		
		$my		= CFactory::getUser();
		
		if($my->id == 0)
		{
        	$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_PLEASE_LOGIN_WARNING'), 'error');
        	return; 
		}
		
		// Set pathway
		$this->addPathway(JText::_('COM_COMMUNITY_FRIENDS'), CRoute::_('index.php?option=com_community&view=friends'));
		$this->addPathway(JText::_('COM_COMMUNITY_FRIENDS_AWAITING_AUTHORIZATION'), '');
		
		// Set document title
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_FRIENDS_AWAITING_AUTHORIZATION'));
        
		// Load submenu
		$this->showSubMenu();
		
		$friends 	= CFactory::getModel( 'friends' );
		
		$rows = !empty($data->pending) ? $data->pending : array();

		for( $i = 0; $i < count( $rows ); $i++ )
		{
			$row	=& $rows[$i];
			$row->user	= CFactory::getUser($row->id );
			$row->user->friendsCount  = $row->user->getFriendCount();
			$row->user->profileLink	= CUrlHelper::userLink($row->id);
			$row->msg = $this->escape($row->msg);
		}
		
		$tmpl	=   new CTemplate();
		echo $tmpl  ->set( 'rows'	, $rows )
			    ->setRef( 'my'	, $my )
			    ->set( 'config'	, $config )
			    ->set( 'pagination' , $data->pagination )
			    ->fetch( 'friends.pending' );
	}
	
	public function addSuccess($data = null)
	{
		$this->addInfo( JText::sprintf( 'COM_COMMUNITY_FRIENDS_WILL_RECEIVE_REQUEST', $data->name ) );
	
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_FRIEND_ADDED_SUCCESSFULLY_TITLE'));
	}
	
	/**
	 * Show the invite window
	 */	 	
	public function invite()
	{
		$mainframe =& JFactory::getApplication();
		
		$document	= JFactory::getDocument();
		$config		= CFactory::getConfig();
		$document->setTitle(JText::sprintf('COM_COMMUNITY_FRIENDS_INVITE_FRIENDS_TITLE', $config->get('sitename') ));
        
		$my	  = CFactory::getUser();

		$this->showSubmenu();

		$post = (JRequest::getVar('action', '', 'POST') == 'invite') ? JRequest::get('POST') : array('message'=>'','emails'=>'');
		
		$pathway 	=& $mainframe->getPathway();
		$this->addPathway(JText::_('COM_COMMUNITY_FRIENDS'), CRoute::_('index.php?option=com_community&view=friends'));
		$this->addPathway(JText::_('COM_COMMUNITY_INVITE_FRIENDS') , '');

		// Process the Suggest Friends
		// Load required filterbar library that will be used to display the filtering and sorting.
		CFactory::load( 'libraries' , 'filterbar' );
		$id			= JRequest::getCmd('userid', $my->id);
		$user		= CFactory::getUser($id);
		$sorted		= JRequest::getVar( 'sort' , 'suggestion' , 'GET' );
		$filter		= JRequest::getVar( 'filter' , 'suggestion' , 'GET' );
		$friends 	= CFactory::getModel( 'friends' );
		
		$rows 		= $friends->getFriends( $id , $sorted , true , $filter );
		$resultRows = array();
		
		foreach($rows as $row)
		{
			$user = CFactory::getUser($row->id);
			
			$obj = clone($row);
			$obj->friendsCount  = $user->getFriendCount(); 
			$obj->profileLink	= CUrlHelper::userLink($row->id);
			$obj->isFriend		= true;
			$resultRows[] = $obj;
		}
		unset($rows);

		CFactory::load( 'libraries' , 'apps' );
		$app 		=& CAppPlugins::getInstance();
		$appFields	= $app->triggerEvent('onFormDisplay' , array('jsform-friends-invite'));
		$beforeFormDisplay	= CFormElement::renderElements( $appFields , 'before' );
		$afterFormDisplay	= CFormElement::renderElements( $appFields , 'after' );
		
		$tmpl	= new CTemplate();
		echo $tmpl  ->set( 'facebookInvite'	, $my->authorise('community.view', 'facebook.friend.invite') )
					->set( 'beforeFormDisplay'	, $beforeFormDisplay )
					->set( 'afterFormDisplay'	, $afterFormDisplay )
					->set( 'my'			, $my )
					->set( 'post'		, $post )
					->setRef( 'friends'		, $resultRows )
					->set( 'config'		, CFactory::getConfig() )
					->fetch( 'friends.invite' );
	}
	
	public function news()
	{
		// Load the toolbar
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_FRIENDS_FRIENDS_NEWS'));
	}
}