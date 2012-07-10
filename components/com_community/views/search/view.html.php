<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');
jimport( 'joomla.utilities.arrayhelper');
jimport( 'joomla.html.html');

class CommunityViewSearch extends CommunityView
{
	public function _addSubmenu()
	{
		$mySQLVer	= 0;
		if(JFile::exists(JPATH_COMPONENT.DS.'libraries'.DS.'advancesearch.php'))
		{
			require_once (JPATH_COMPONENT.DS.'libraries'.DS.'advancesearch.php');
			$mySQLVer	= CAdvanceSearch::getMySQLVersion();
		}	
	
		// Only display related links for guests
		$my 		= CFactory::getUser();
		$config		= CFactory::getConfig();
		
		if( $my->id == 0)
		{

			$tmpl = new CTemplate();
			$tmpl->set( 'url', CRoute::_('index.php?option=com_community&view=search') );
			$html = $tmpl->fetch( 'search.submenu' );		
			$this->addSubmenuItem('index.php?option=com_community&view=search', JText::_('COM_COMMUNITY_SEARCH_FRIENDS'), 'joms.videos.toggleSearchSubmenu(this)', SUBMENU_LEFT, $html);			

			if($mySQLVer >= 4.1 && $config->get('guestsearch'))
				$this->addSubmenuItem('index.php?option=com_community&view=search&task=advancesearch', JText::_('COM_COMMUNITY_CUSTOM_SEARCH'));
		}
		else
		{
			$this->addSubmenuItem('index.php?option=com_community&view=friends', JText::_('COM_COMMUNITY_FRIENDS_VIEW_ALL'));

			$tmpl = new CTemplate();
			$tmpl->set( 'url', CRoute::_('index.php?option=com_community&view=search') );
			$html = $tmpl->fetch( 'search.submenu' );		
			$this->addSubmenuItem('index.php?option=com_community&view=search', JText::_('COM_COMMUNITY_SEARCH_FRIENDS'), 'joms.videos.toggleSearchSubmenu(this)', SUBMENU_LEFT, $html);

			if($mySQLVer >= 4.1 )
				$this->addSubmenuItem('index.php?option=com_community&view=search&task=advancesearch', JText::_('COM_COMMUNITY_CUSTOM_SEARCH'));
			$this->addSubmenuItem('index.php?option=com_community&view=friends&task=invite', JText::_('COM_COMMUNITY_INVITE_FRIENDS'));
			$this->addSubmenuItem('index.php?option=com_community&view=friends&task=sent', JText::_('COM_COMMUNITY_FRIENDS_REQUEST_SENT'));
			$this->addSubmenuItem('index.php?option=com_community&view=friends&task=pending', JText::_('COM_COMMUNITY_FRIENDS_PENDING_APPROVAL'));
		}
	}

	public function showSubmenu(){
		$this->_addSubmenu();
		parent::showSubmenu();
	}
	
	public function search($data)
	{
		//return $this->search($data);
		
		require_once (JPATH_COMPONENT.DS.'libraries'.DS.'profile.php');
		require_once (JPATH_COMPONENT.DS.'helpers'.DS.'friends.php');
		
		$document	= JFactory::getDocument();		
		$document->setTitle(JText::_('COM_COMMUNITY_SEARCH_FRIENDS_TITLE'));
		$this->showSubMenu();
		$avatarOnly		= JRequest::getVar( 'avatar' , '' );
		$this->addPathway( JText::_('COM_COMMUNITY_SEARCH_FRIENDS_TITLE') );
		$my				= CFactory::getUser();
		$friendsModel	= CFactory::getModel('friends');
		$resultRows 	= array();
		
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
		$tmpl	->set('data'		, $resultRows)
				->set('sortings'	, '')
				->set('pagination' , $pagination );

		CFactory::load( 'libraries' , 'tooltip' );
		//JHTML::_('behavior.tooltip');
		
		CFactory::load( 'libraries' , 'featured' );
		$featured		= new CFeatured( FEATURED_USERS );
		$featuredList	= $featured->getItemIds();
		
		$tmpl->set('featuredList' , $featuredList);
		
		CFactory::load( 'helpers' , 'owner' );
		$resultHTML  = $tmpl->set('isCommunityAdmin', COwnerHelper::isCommunityAdmin() )
							->set('showFeaturedList' , false )
							->set('my' , $my )
							->fetch('people.browse');
		unset( $tmpl );

		$searchLinks	=   parent::getAppSearchLinks('people');
		
		$tmpl 		= new CTemplate();	
		echo $tmpl	->set( 'avatarOnly'	, $avatarOnly )
					->set( 'results'		, $data->result )
					->set( 'resultHTML'	, $resultHTML )
					->set( 'query'		, $data->query )
					->set( 'searchLinks'	, $searchLinks )
					->fetch( 'search' );
	}

	public function browse($data=null)
	{
		require_once (JPATH_COMPONENT.DS.'libraries'.DS.'template.php');
		
		$mainframe	=& JFactory::getApplication();
		$document	= JFactory::getDocument();	
		
		
		// Load required filterbar library that will be used to display the filtering and sorting.
		CFactory::load( 'libraries' , 'filterbar' );
		
		$this->addPathway( JText::_( 'COM_COMMUNITY_GROUPS_MEMBERS' ) , '' );
		
		
		$document->setTitle( JText::_( 'COM_COMMUNITY_GROUPS_MEMBERS' ) );
		
		CFactory::load( 'helpers' , 'friends' );
		CFactory::load( 'libraries' , 'template');
		CFactory::load( 'libraries' , 'tooltip' );
		CFactory::load( 'helpers' , 'owner' );		
		CFactory::load( 'libraries' , 'featured' );
		
		$my				= CFactory::getUser();
		$view			= CFactory::getView('search');
		$searchModel  	= CFactory::getModel('search');
		$userModel		= CFactory::getModel('user');
		$avatar			= CFactory::getModel('avatar');
		$friends		= CFactory::getModel('friends');
		
		
		$tmpl		= new CTemplate();
		$sorted		= JRequest::getVar( 'sort' , 'latest' , 'GET' );
		$filter		= JRequest::getWord( 'filter' , 'all' , 'GET' );
		$rows		= $searchModel->getPeople( $sorted , $filter );
		
		$sortItems	=  array(
							'latest' 	=> JText::_('COM_COMMUNITY_SORT_LATEST') , 
							'online'	=> JText::_('COM_COMMUNITY_SORT_ONLINE') ,
							'alphabetical'	=> JText::_('COM_COMMUNITY_SORT_ALPHABETICAL')
							);

		$filterItems	= array();
		$config			= CFactory::getConfig();
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
		$html		= '';
		$totalUser	= $userModel->getMembersCount();
		$resultRows	= array();

		// No need to pre-load multiple users at once since $searchModel->getPeople
		// already did
		for($i = 0; $i < count($rows); $i++)
		{
			$row =& $rows[$i];
			
			$obj = clone($row);
			$user				= CFactory::getUser( $row->id );
			$obj->friendsCount  = $user->getFriendCount();
			$obj->user			= $user;
			$obj->profileLink	= CUrl::build( 'profile' , '' , array( 'userid' => $row->id ) );
			$isFriend =  CFriendsHelper::isConnected( $row->id, $my->id );

			$connection		= $friends->getFriendConnection( $my->id , $row->id );
			$obj->isMyFriend = false;
			if(!empty($connection)){
			    if($connection[0]->connect_from == $my->id){
				$obj->isMyFriend = true;
			    }
			}
			
			$obj->addFriend 	= ((! $isFriend) && $my->id != $row->id) ? true : false;
		
			$resultRows[] = $obj;
		}
		$featuredList = $this->_cachedCall('getFeaturedMember',array(),'',array( COMMUNITY_CACHE_TAG_FEATURED ));

		$config			= CFactory::getConfig();
		
		if( $config->get('alphabetfiltering') )
		{
			$sortingsHTML	= CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'latest' , $filterItems , 'all' );
		}
		else
		{
			$sortingsHTML	= CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'latest' );
		}
		
		echo $tmpl	->set('featuredList'		, $featuredList)
					->set( 'isCommunityAdmin'	, COwnerHelper::isCommunityAdmin() )
					->set( 'featuredList'		,  $featuredList )
					->set('data'				, $resultRows)
					->set('sortings'			, $sortingsHTML )
					->set( 'my'				, $my )
					->set( 'totalUser'			, $totalUser )
					->set( 'showFeaturedList' 	, true )
					->set( 'pagination'		, $searchModel->getPagination() )
					->fetch('people.browse');
	}

	public function getFeaturedMember(){
	    $featured	= new CFeatured( FEATURED_USERS );
	    $featuredList	= $featured->getItemIds();
	    $filterblocked = array();	
	    foreach($featuredList as $id){
		$user	= CFactory::getUser( $id );
		if($user->block == 0) $filterblocked[] = $id;
	    }
	    return $filterblocked;
	    //return $featuredList;
	}

	public function field($data)
	{
		$mainframe =& JFactory::getApplication();
		require_once (JPATH_COMPONENT.DS.'libraries'.DS.'template.php');		
		
		$searchFields = JRequest::get('get');
		
		// Remove non-search field
		if(isset($searchFields['option'])) 	unset($searchFields['option']);
		if(isset($searchFields['view'])) 	unset($searchFields['view']); 
		if(isset($searchFields['task'])) 	unset($searchFields['task']);
		if(isset($searchFields['Itemid'])) 	unset($searchFields['Itemid']);
		if(isset($searchFields['format'])) 	unset($searchFields['format']);
		
		$keys = array_keys($searchFields);
		$vals = array_values($searchFields);
		
		CFactory::load( 'helpers' , 'friends' );
		
		$document = JFactory::getDocument();	
		
		$searchModel	= CFactory::getModel('search');
		$profileModel	= CFactory::getModel( 'profile' );
		$profileName	= $profileModel->getProfileName( $keys[0] );
		$profileName	= JText::_( $profileName );
		$document->setTitle( JText::sprintf( 'COM_COMMUNITY_MEMBERS_WITH_FIELD', JText::_( $profileName ) , $vals[0] ) );
		
		$rows = $data->result;
		
		
		$my		= CFactory::getUser();
		
		$resultRows = array();
		$friendsModel = CFactory::getModel('friends');
		
		$tmpl = new CTemplate();
		for($i = 0; $i < count($rows); $i++){
		
			$row =& $rows[$i];
			
			$userObj			= CFactory::getUser( $row->id );
			$obj				= new stdClass();
			$obj->user			= $userObj;
			$obj->friendsCount  = $userObj->getFriendCount();
			$obj->profileLink	= CRoute::_('index.php?option=com_community&view=profile&userid=' . $row->id );
			$isFriend =  CFriendsHelper::isConnected( $row->id, $my->id );
			
			$obj->addFriend 	= ((! $isFriend) && ($my->id != 0) && $my->id != $row->id) ? true : false;
			
			$resultRows[] = $obj;
		}
		
		$pagination   = $searchModel->getPagination();
		
		echo $tmpl	->set('data'		, $resultRows)
					->set('sortings'	, '')
					->set('pagination'	, $pagination)
					->set('featuredList' , '')
					->set('isCommunityAdmin','')
					->set('my' , $my)	
					->fetch('people.browse');
		}
	
	public function advanceSearch()
	{
		CFactory::load('libraries', 'advancesearch');
		CFactory::load('libraries', 'messaging');
		CFactory::load('helpers', 'friends');
		CFactory::load('helpers' , 'owner' );
		$document	= JFactory::getDocument();

		//load calendar behavior
		JHtml::_('behavior.calendar');
		JHtml::_('behavior.tooltip');
		
		$document->setTitle(JText::_('COM_COMMUNITY_TITLE_CUSTOM_SEARCH'));
		$this->showSubMenu();
		
		$this->addPathway( JText::_('COM_COMMUNITY_TITLE_CUSTOM_SEARCH') );
		
		
		$my 		= CFactory::getUser();		
		$config		= CFactory::getConfig();
		
		$result	= null;
		$fields = CAdvanceSearch::getFields();
		$data 	= new stdClass();
		
		$post 		= JRequest::get('GET');
		$keyList	= isset($post['key-list']) ? $post['key-list'] : '';
		$avatarOnly	= JRequest::getVar( 'avatar' , '' );
		
		if( JString::strlen($keyList) > 0)
		{
		
			//formatting the assoc array
			$filter			= array();
			$key			= explode(',', $keyList);
			$joinOperator	= $post['operator'];
			
			foreach($key as $idx)
			{
				$obj	= new stdClass();
				$obj->field		= $post['field'.$idx];
				$obj->condition	= $post['condition'.$idx];
				$obj->fieldType	= $post['fieldType'.$idx];
				
				if( $obj->fieldType == 'email')
				{
					$obj->condition	= 'equal';
				}
				
				// we need to check whether the value contain start and end kind of values.
				// if yes, make them an array.
				if(isset($post['value'.$idx.'_2']))
				{
					if($obj->fieldType == 'date')
					{
						$startDate	= (empty($post['value'.$idx])) ? '01/01/1970' : $post['value'.$idx];
						$endDate	= (empty($post['value'.$idx.'_2'])) ? '01/01/1970' : $post['value'.$idx.'_2'];
						
						// Joomla 1.5 uses "/"
						// Joomla 1.6 uses "-"
						$delimeter	= '-';
						if (strpos($startDate, '/'))
						{
							$delimeter	= '/';
						}
						
						$sdate		= explode($delimeter, $startDate);
						$edate		= explode($delimeter, $endDate);
						if(isset($sdate[2]) && isset($edate[2])){
							$obj->value		= array($sdate[0] . '-' . $sdate[1] . '-' . $sdate[2] . ' 00:00:00',
													$edate[0] . '-' . $edate[1] . '-' . $edate[2] . ' 23:59:59');
						} else {
							$obj->value		= array(0,0);
						}
					} 
					else
					{
						$obj->value		= array($post['value'.$idx], $post['value'.$idx.'_2']);	
					}
				}
				else
				{
					if($obj->fieldType == 'date')
					{						
						$startDate	= (empty($post['value'.$idx])) ? '01/01/1970' : $post['value'.$idx];
						$delimeter	= '-';
						if (strpos($startDate, '/'))
						{
							$delimeter	= '/';
						}
						$sdate		= explode($delimeter, $startDate);
						if(isset($sdate[2])){
							$obj->value	= $sdate[2] . '-' . $sdate[1] . '-' . $sdate[0] . ' 00:00:00';
						} else {
							$obj->value = 0;
						}
					}
					else if($obj->fieldType == 'checkbox')
					{
						if(empty($post['value'.$idx]))
						{
							//this mean user didnot check any of the option.
							$obj->value		= '';
						}
						else
						{
							$obj->value		= isset($post['value'.$idx]) ? implode(',', $post['value'.$idx]) : '';
						}
					}	
					else
					{
						$obj->value		= isset($post['value'.$idx]) ? $post['value'.$idx] : '';
					}
				}
				
				$filter[]	= $obj;
			}
			$data->search	= CAdvanceSearch::getResult($filter, $joinOperator, $avatarOnly );
			$data->filter	= $post;
		}
		
		$rows 		= (! empty($data->search)) ? $data->search->result : array();
		$pagination = (! empty($data->search)) ? $data->search->pagination : '';
		$filter 	= (! empty($data->filter)) ? $data->filter : array();
		
		$resultRows = array();
		$friendsModel = CFactory::getModel('friends');		
		
		for($i = 0; $i < count($rows); $i++)
		{
			$row =& $rows[$i];
						
			$obj				= new stdClass();
			$obj->user			=& $row;
			$obj->friendsCount  = $row->getFriendCount();
			$obj->profileLink	= CRoute::_('index.php?option=com_community&view=profile&userid=' . $row->id );
			$isFriend =  CFriendsHelper::isConnected( $row->id, $my->id );
			
			$obj->addFriend 	= ((! $isFriend) && ($my->id != 0) && $my->id != $row->id) ? true : false;
			
			$resultRows[] = $obj;
		}
		


		if (class_exists('Services_JSON')) 
		{
			$json = new Services_JSON();
		}
		else
		{
			require_once (AZRUL_SYSTEM_PATH.DS.'pc_includes'.DS.'JSON.php');
			$json = new Services_JSON();
		}

		$tmpl 		= new CTemplate();
		$searchForm = $tmpl	->set( 'fields', $fields)
							->set( 'keyList' , $keyList )
							->set( 'avatarOnly' , $avatarOnly )
							->set( 'filterJson', $json->encode($filter) )
							->set( 'postresult' , isset( $post['key-list']) )
							->fetch( 'search.advancesearch' );

		if( isset( $post['key-list'] ) )
		{
			//result template
			$tmplResult 		= new CTemplate();
			CFactory::load( 'libraries' , 'tooltip' );
			CFactory::load( 'helpers' , 'owner' );
			//JHTML::_('behavior.tooltip');
			
			CFactory::load( 'libraries' , 'featured' );
			$featured		= new CFeatured( FEATURED_USERS );
			$featuredList	= $featured->getItemIds();
			
			$tmpl->set('featuredList' , $featuredList);
			
			$searchForm	.= $tmplResult	->set( 'my', $my )
										->set( 'showFeaturedList' , false )
										->set( 'featuredList' , $featuredList )
										->set( 'data'		, $resultRows)
										->set( 'sortings'	, '')
										->set( 'pagination', $pagination )
										->set( 'filter', $filter )
										->set( 'featuredList', $featuredList )
										->set( 'isCommunityAdmin', COwnerHelper::isCommunityAdmin() )
										->fetch('people.browse');
		}
		
		echo $searchForm; 
	}
}
