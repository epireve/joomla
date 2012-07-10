<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
jimport( 'joomla.utilities.arrayhelper');

class CommunityViewMemberList extends CommunityView
{
	public function display()
	{
		$id		= JRequest::getVar( 'listid' , '' );
		$list	=& JTable::getInstance( 'MemberList' , 'CTable' );
		$list->load( $id );
		
		if( empty( $list->id ) || is_null( $list->id ) )
		{
			echo JText::_('COM_COMMUNITY_INVALID_ID');
			return;
		}
		$document	= JFactory::getDocument();
		
		$document->setTitle( $list->getTitle() );
		$tmpCriterias	= $list->getCriterias();
		$criterias		= array();
		
		foreach( $list->getCriterias() as $criteria )
		{
			$obj				= new stdClass();
			$obj->field			= $criteria->field;
			$obj->condition		= $criteria->condition;
			$obj->fieldType		= $criteria->type;
			
			switch( $criteria->type )
			{
				case 'date':
				case 'birthdate':
					if( $criteria->condition == 'between' )
					{
						$date		= explode( ',' , $criteria->value );
						if(isset($date[1])){
							$delimeter	= '-';
							if (strpos($date[0], '/'))
							{
								$delimeter	= '/';
							}
							$startDate	= explode( $delimeter , $date[0] );
							$endDate	= explode( $delimeter , $date[1] );
							if(isset($startDate[2]) && isset($endDate[2])){
								//date format
								$obj->value	= array( $startDate[2] . '-' . intval($startDate[1]) . '-' . $startDate[0] . ' 00:00:00',
													 $endDate[2] . '-' . intval($endDate[1]) . '-' . $endDate[0] . ' 23:59:59');
							} else {
								//age format
								$obj->value	= array($date[0],$date[1]);
							}
						} else {
							//wrong data, set to default
							$obj->value	= array(0,0);
						}
					}
					else
					{
						$delimeter	= '-';
						if (strpos($criteria->value, '/'))
						{
							$delimeter	= '/';
						}
						$startDate	= explode($delimeter, $criteria->value );
						if(isset($startDate[2])){
							//date format
							$obj->value	= $startDate[2] . '-' . intval($startDate[1]) . '-' . $startDate[0] . ' 00:00:00';
						} else {
							//age format
							$obj->value=$criteria->value;
						}
					}
				break;
				case 'checkbox':
				default:
					$obj->value			= $criteria->value;
				break;
			}
			
			
			$criterias[]		= $obj;
		}
		CFactory::load( 'helpers' , 'time');
		$created	=  CTimeHelper::getDate($list->created);
		
		CFactory::load( 'libraries' , 'advancesearch' );
		CFactory::load( 'libraries' , 'filterbar' );
		
		$sortItems	=  array(
							'latest' 	=> JText::_('COM_COMMUNITY_SORT_LATEST') , 
							'online'	=> JText::_('COM_COMMUNITY_SORT_ONLINE') ,
							'alphabetical'	=> JText::_('COM_COMMUNITY_SORT_ALPHABETICAL')
							);
		$sorting	= JRequest::getVar( 'sort' , 'latest' , 'GET' );
		$data		= CAdvanceSearch::getResult( $criterias , $list->condition , $list->avataronly , $sorting );

		$tmpl		= new CTemplate();
		$html = $tmpl	->set( 'list' 		, $list )
						->set( 'created' 	, $created )
						->set( 'sorting'	, CFilterBar::getHTML( CRoute::getURI(), $sortItems, 'latest') )
						->fetch( 'memberlist.result' );
		unset( $tmpl );

		CFactory::load( 'libraries' , 'tooltip' );
		CFactory::load( 'helpers' , 'owner' );
		CFactory::load( 'libraries' , 'featured' );


		$featured		= new CFeatured( FEATURED_USERS );
		$featuredList	= $featured->getItemIds();
		$my				= CFactory::getUser();

		$resultRows = array();
		$friendsModel = CFactory::getModel('friends');
		
		CFactory::load( 'helpers' , 'friends' );
		foreach( $data->result as $user )
		{
			$obj				= new stdClass();
			$obj->user			= $user;
			$obj->friendsCount  = $user->getFriendCount();
			$obj->profileLink	= CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->id );
			$isFriend =  CFriendsHelper::isConnected( $user->id, $my->id );
			
			$obj->addFriend 	= ((! $isFriend) && ($my->id != 0) && $my->id != $user->id) ? true : false;						
			
			$resultRows[] = $obj;
		}
				
		$tmpl		= new CTemplate();

		echo $tmpl	->set( 'data' 		, $resultRows )
					->set( 'sortings'	, '' )
					->set( 'pagination', $data->pagination )
					->set( 'filter' , '' )
					->set( 'featuredList' , $featuredList)
					->set( 'my' , $my )
					->set( 'showFeaturedList' , false )
					->set( 'isCommunityAdmin' , COwnerHelper::isCommunityAdmin() )
					->fetch('people.browse');	
	}
}

