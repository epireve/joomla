<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
/**
 */

// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptLibAvatar
{
	/**
	 * (on Ajaxcall)Chek if default avatar remove then it say "can not remove default avatar".
	 * @param unknown_type $response
	 * @return string|string
	 */
	static function removeAvatar($args, &$response)
	{		
		$userId = null;
		if(is_array($args) && isset($args[0])){
			$userId = $args[0];
		}			
		if(empty($userId)){
			$userId = JFactory::getUser()->id;
		}
		// get user Avatar
		$myCurrnetAvatar= self::getUserInfo($userId);		
	
		//get User Profile Type and  ProfileType Avatar
		$myPType  		= XiptLibProfiletypes::getUserData($userId, 'PROFILETYPE');
		$myPType_avatar = XiptLibProfiletypes::getProfiletypeData($myPType, 'avatar');

		//Compare User Avatar and Profile-type Avatar
		if(JString::stristr($myCurrnetAvatar,$myPType_avatar)){
			self::setResponse($response);
			return false;
		}
		return true;
	
	}
	
	/**
	 * return user field ($what) from community user table
	 * @param $what
	 */
	static function getUserInfo($userId=null, $what='_avatar')
	{
		return CFactory::getUser($userId)->$what;
	}
	/**
	 * if default avatar remove then set response message.
	 * @param unknown_type $response
	 */
	static function setResponse(&$response) {	
		$tmpl		= new CTemplate();
		$content	= XiptText::_('YOU_CANNOT_REMOVE_DEFAULT_AVATAR');
		//Do not required any action.
		$formAction	= '';//		CRoute::_('index.php?option=com_community&view=profile&task=removeAvatar' );
		$actions	= '<form action="' . $formAction . '" method="POST">';
		//$actions	.=	'<input class="button" type="submit" value="' . JText::_('CC_BUTTON_YES') . '" />';
		$actions	.=	'&nbsp;<button class="button" onclick="cWindowHide();return false;">' . XiptText::_('OK') . '</button>';
		$actions	.= '</form>';

		$response->addAssign('cwin_logo', 'innerHTML', XiptText::_('REMOVE_PROFILE_PICTURE') );
		$response->addScriptCall('cWindowAddContent', $content, $actions);
	}
	
	/**
	 * When user remove Avatar then set to default avatar as profile pix
	 */
	static function removeProfilePicture()
	{
		//when admin remove any avatar of user by admin panel then get userid
		// at default value,if user remove self avatar.(when fron end user login) 
		$userId = JRequest::getVar('userid',JFactory::getUser()->id,'POST');
		
		$pType  = XiptLibProfiletypes::getUserData($userId, 'PROFILETYPE');
		$newPath = XiptLibProfiletypes::getProfiletypeData($pType, 'avatar');
			
		self::_removeProfilePicture($userId,$pType, $newPath);
		$view = JRequest::getVar('view','profile','GET');
		//$task =	JRequest::getVar('task','profile','GET');
		JFactory::getApplication()->redirect( CRoute::_( "index.php?option=com_community&view=$view&userid=$userId" , false ) , JText::_('CC_PROFILE_PICTURE_REMOVED') );
		
	}
	
	function _removeProfilePicture( $id ,$pType, $newPath,$type = 'avatar')
	{
		$db = JFactory::getDBO();
		// Test if the record exists.
		$oldAvatar	= self::getUserInfo($id);
		
		//If avatar is default then not remove it
		if(JString::stristr( $oldAvatar , $newPath ))
		{
			JFactory::getApplication()->enqueueMessage(XiptText::_("YOU_CANNOT_REMOVE_DEFAULT_AVATAR"));
			return;
		}
		//get avatar_PROFILE-TYPE_thumb.jpg path
		if(JString::substr($newPath,-4) == ".png")
			$thumbPath = JString::str_ireplace(".png","_thumb.png",$newPath);
		else
			$thumbPath = JString::str_ireplace(".jpg","_thumb.jpg",$newPath);	
		
		//if (Applied)Avatar is default user.png (JomSocial default Profile pix) then not insert our default avatar (user.png) path in database.	
		if(JString::stristr($newPath, DEFAULT_AVATAR) && JString::stristr($thumbPath, DEFAULT_AVATAR_THUMB)){
			$newPath   = '';
            $thumbPath = '';
		}		
		
		// create query for update Avatar and thumb
		$query	=   'UPDATE ' . $db->nameQuote( '#__community_users' ) . ' '
			    	.'SET ' . $db->nameQuote( $type ) . '=' . $db->Quote( $newPath ) . ', '
			    			. '`thumb` = '. $db->Quote( $thumbPath ) . ' '
			    	.'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $id );
		
		$db->setQuery( $query );
		$db->query( $query );
		    	
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
	    
		//get thumb Path
		$oldAvatarThumb	= self::getUserInfo($id,'_thumb');
		// If old file is default avatar thumb or default avatar , we should not remove it.
		// if old file is not default avatar then remove it.
		// Need proper way to test it
		if(!JString::stristr( $oldAvatar , $newPath ) && !JString::stristr( $oldAvatarThumb , $thumbPath ) )
		{
			// File exists, try to remove old files first.
			$oldAvatar	= XiptHelperUtils::getRealPath( $oldAvatar );	
			$oldAvatarThumb=XiptHelperUtils::getRealPath( $oldAvatarThumb );
			if( JFile::exists( $oldAvatar ) )
			{	
				JFile::delete($oldAvatar);
				JFile::delete($oldAvatarThumb);
			}
		}
		
		return true;
	}
}