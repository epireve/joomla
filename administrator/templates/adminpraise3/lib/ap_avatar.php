<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class APAvatar {
	
	public static function find($showAvatar) {
		$user	= JFactory::getUser();
	
		switch($showAvatar) :
			
			case 'gravatar':
			$info = self::getGravatar($user);
			break;
			
			case 'jomsocial':
			$info =  self::getJomSocialAvatar($user);
			break;
			
			case 'k2':
			$info =  self::getK2Avatar($user);
			break;
			
			case 'cb':
			$info =  self::getCBAvatar($user);
			break;
			
			case 0:
			default:
			return false;
			break;
			
		endswitch;
	
		if ($info['img_url']) :
			$link = "<a href=\"".$info['link']."\" class=\"modal\" rel=\"{handler: 'iframe', size: {x: 900, y: 550}}\"><img src=\"". $info['img_url'] ."\" title=\"". JText::_( 'PROFILE' ) . " " . $user->get('username') ."\"/></a>";
		else :
			$link = "<a href=\"".$info['link']."\" class=\"modal\" rel=\"{handler: 'iframe', size: {x: 900, y: 550}}\">".$user->username."</a>";
		endif;
		
		return $link;
	}
	
	private static function getGravatar($user) {
		$email	= $user->email;
		$hash	= md5(strtolower(trim($email)));
		$info['img_url']	= 'http://www.gravatar.com/avatar/'.$hash.'.jpg';
		$info['link']		= JURI::root() . "administrator/index.php?option=com_users&view=user&task=edit&tmpl=component&cid[]=".$user->id;
		return $info;
	}
	
	private static function getJomSocialAvatar($user) {
		$info['link'] = JURI::root().'administrator/index.php?option=com_community&view=users&layout=edit&id='.$user->id;
		
		$jomsocial_core = JPATH_ADMINISTRATOR.'/components/com_community/libraries/core.php';
		if (!class_exists('CFactory') && file_exists($jomsocial_core)) :
			include_once($jomsocial_core);
		endif;
		
		$info['img_url']	= CFactory::getUser($user->id)->getThumbAvatar();
		
		return $info;
	}
	
	private static function getK2Avatar($user) {
		$k2_media_url	= JURI::root().'media/k2/users/';
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id, image FROM #__k2_users WHERE userID = ".$user->id);
		$k2user = $db->loadObject();
		
		$info['img_url']	= $k2user->image ? $k2_media_url.$k2user->image : null;
		$info['link'] 		= JURI::root().'administrator/index.php?option=com_k2&view=user&cid='.$k2user->id;
		return $info;
	}
	
	private static function getCBAvatar($user) {
		$cb_media_url	= JURI::root().'images/comprofiler/';
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id = ".$user->id);
		$cbuser = $db->loadObject();
		
		$info['link']		= JURI::root() . "administrator/index.php?option=com_users&view=user&task=edit&tmpl=component&cid[]=".$user->id;
		$info['img_url']	= $cbuser->avatar ? $cb_media_url.$cbuser->avatar : null;
		return $info;
	}
	
}