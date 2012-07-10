<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CUserHelper
{
	static public function getUserId( $username )
	{
		$db		=& JFactory::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'username' ) . '=' . $db->Quote( $username );
	
		$db->setQuery( $query );
		
		$id		= $db->loadResult();
	
		return $id;
	}

	static function getThumb( $userId , $imageClass = '' , $anchorClass = '' )
	{
		CFactory::load( 'helpers' , 'string' );
		$user	= CFactory::getUser( $userId );
		
		$imageClass		= (!empty( $imageClass ) ) ? ' class="' . $imageClass . '"' : '';
		$anchorClass	= ( !empty( $anchorClass ) ) ? ' class="' . $anchorClass . '"' : '';
		
		$data	= '<a href="' . CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->id ) . '"' . $anchorClass . '>';
		$data	.= '<img src="'.$user->getThumbAvatar().'" alt="' . CStringHelper::escape( $user->getDisplayName() ) . '"' . $imageClass . ' />';
		$data	.= '</a>';
		
		return $data;
	}

	/**
	 * Get the html code to be added to the page
	 * 
	 * return	$html	String
	 */	 	
	static public function getBlockUserHTML( $userId, $isBlocked )
	{
		$my    = CFactory::getUser();
		$html = '';
		
		if(!empty($my->id)) {
		
		    $tmpl  = new Ctemplate();
	  
		    $tmpl->set( 'userId'   , $userId );
		    $tmpl->set( 'isBlocked', $isBlocked);
		    $html = $tmpl->fetch( 'block.user' );
		    
	  	}
	  	
		return $html;
	}
	
	static public function isUseFirstLastName()
	{
		$isUseFirstLastName	= false;
		
		// Firstname, Lastname for base on field code FIELD_GIVENNAME, FIELD_FAMILYNAME
		$modelProfile	= CFactory::getModel('profile');
		
		$firstName		= $modelProfile->getFieldId('FIELD_GIVENNAME');
		$lastName		= $modelProfile->getFieldId('FIELD_FAMILYNAME');
		$isUseFirstLastName	= ($firstName && $lastName);
		
		if ($isUseFirstLastName)
		{
			$table		= JTable::getInstance('ProfileField', 'CTable');
			$table->load($firstName);
			$isFirstNamePublished	= $table->published;
			$table->load($lastName);
			$isLastNamePublished	= $table->published;
			$isUseFirstLastName		= ($isFirstNamePublished && $isLastNamePublished);
			
			// we don't use this html because the generated class name doesn't match in this case
			//$firstNameHTML	= CProfile::getFieldHTML($firstName);
			//$lastNameHTML	= CProfile::getFieldHTML($lastName);
		}
		
		return $isUseFirstLastName;
	}
	
	/**
	 * Add default items for status box
	 */
	static function addDefaultStatusCreator(&$status)
	{
		
		$my 		= CFactory::getUser();
		$userid		=  JRequest::getVar('userid', $my->id);
		$user		= CFactory::getUser($userid);
		$config 	= CFactory::getConfig();
		$template	= new CTemplate();
		
		$isMine = COwnerHelper::isMine($my->id, $user->id);
		
		/* Message creator */
		$creator        = new CUserStatusCreator('message');
		$creator->title = ($isMine) ? JText::_('COM_COMMUNITY_STATUS') : JText::_('COM_COMMUNITY_MESSAGE');
		$creator->html  = $template->fetch('status.message');
		$status->addCreator($creator);

		if($isMine){
		if( $config->get( 'enablephotos') )
		{
			/* Photo creator */
			$creator        = new CUserStatusCreator('photo');
			$creator->title = JText::_('COM_COMMUNITY_PHOTOS');
			$creator->html  = $template->fetch('status.photo');

			$status->addCreator($creator);
		}

		if( $config->get( 'enablevideos') )
		{
			/* Video creator */
			$creator        = new CUserStatusCreator('video');
			$creator->title = JText::_('COM_COMMUNITY_VIDEOS');
			$creator->html  = $template->fetch('status.video');

			$status->addCreator($creator);
		}

		if( $config->get( 'enableevents') && ($config->get('createevents') || COwnerHelper::isCommunityAdmin() )  )
		{
			/* Event creator */
			CFactory::load( 'helpers' , 'event' );
			$dateSelection = CEventHelper::getDateSelection();

			$model		= CFactory::getModel( 'events' );
			$categories	= $model->getCategories();

			// Load category tree
			CFactory::load('helpers','category');
			$cTree	= CCategoryHelper::getCategories($categories);
			$lists['categoryid']	=   CCategoryHelper::getSelectList( 'events', $cTree );

			$template->set( 'startDate'       , $dateSelection->startDate );
			$template->set( 'endDate'         , $dateSelection->endDate );
			$template->set( 'startHourSelect' , $dateSelection->startHour );
			$template->set( 'endHourSelect'   , $dateSelection->endHour );
			$template->set( 'startMinSelect'  , $dateSelection->startMin );
			$template->set( 'endMinSelect'    , $dateSelection->endMin );
			$template->set( 'startAmPmSelect' , $dateSelection->startAmPm );
			$template->set( 'endAmPmSelect'   , $dateSelection->endAmPm );
			$template->set( 'lists'           , $lists );

			$creator  = new CUserStatusCreator('event');
			$creator->title = JText::_('COM_COMMUNITY_EVENTS');
			$creator->html  = $template->fetch('status.event');

			$status->addCreator($creator);
		}
		}
	}
}

/**
 * Deprecated since 1.8
 * Use CUserHelper::getUserId instead. 
 */
function cGetUserId( $username )
{
	return CUserHelper::getUserId( $username );
}

/**
 * Deprecated since 1.8
 * Use CUserHelper::getThumb instead. 
 */
function cGetUserThumb( $userId , $imageClass = '' , $anchorClass = '' )
{
	return CUserHelper::getThumb( $userId , $imageClass , $anchorClass );
}

/**
 * Deprecated since 1.8
 * Use CValidateHelper::username instead. 
 */
function cValidUsername( $username )
{
	CFactory::load( 'helpers' , 'validate' );
	
	return CValidateHelper::username( $username );
}

function getBlockUserHTML( $userId, $isBlocked )
{
	return CUserHelper::getBlockUserHTML( $userId , $isBlocked );
}