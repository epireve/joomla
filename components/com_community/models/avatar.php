<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Avatar 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once ( JPATH_ROOT .DS.'components'.DS.'com_community'.DS.'models'.DS.'models.php');

/**
 *
 */ 
class CommunityModelAvatar extends JCCModel
{
	/**
	 * Return live path to large avatar
	 */	 	
	public function getLargeImg($id , $appType = 'profile'){
		return $this->_getImage($id, 2, $appType , 'components/com_community/assets/default.jpg');
	}
	
	/**
	 * Return live path to large avatar
	 */
	public function getMediumImg($userid){
		//return 
	}
	
	
	/**
	 * Return live path to large avatar
	 * $addType	The type of the calling app be it group, profile etc.	 
	 */
	public function getSmallImg($id , $appType = 'profile'){
		return $this->_getImage($id, 0, $appType , 'components/com_community/assets/default_thumb.jpg');
	}
	
	/**
	 *
	 */	 	
	public function _getImage($id, $type, $appType , $default){
		$db =& $this->getDBO();
		
		$strSQL	= 'SELECT ' . $db->nameQuote('path') .' FROM ' . $db->nameQuote('#__community_avatar') . ' '
				. 'WHERE ' . $db->nameQuote('id') .'=' . $db->Quote($id) . ' '
				. 'AND ' . $db->nameQuote('apptype') .'=' . $db->Quote($appType) . ' '
				. 'AND ' . $db->nameQuote('type') .'=' . $db->Quote($type);

  		$db->setQuery($strSQL);
  		$path = $db->loadResult();
  		
  		if(!$path){
  			// Display default image
  			$path = $default; 
		}
  		
  		return JURI::base() . $path;
	}
	
	/**
	 * Set small thumbnail avatar
	 * @param	int		userid
	 * @param	string	relative path to avatar image			 
	 */
	public function setLargeImg($id, $path , $appType){
		$this->_setImage($id, $path, 2 , $appType);
		return $this;
	}
	
	/**
	 * Set small thumbnail avatar
	 * @param	int		userid
	 * @param	string	relative path to avatar image			 
	 */
	public function setMediumImg($id, $path , $appType ){
		$obj = new stdClass();
		
		$obj->userid = $id;
  		$obj->path = $path;
  		$obj->type = 1;
  		$obj->appType	= $appType;
		return $this;
	}
	
	/**
	 * Set small thumbnail avatar
	 * @param	int		userid
	 * @param	string	relative path to avatar image			 
	 */	 	
	public function setSmallImg($id, $path , $appType){
		$this->_setImage($id, $path, 0 , $appType);
		return $this;
	}



	/**
	 *
	 */	 	
	public function _setImage($id, $path, $type , $appType){
		$db =& $this->getDBO();
		
		$obj = new stdClass();
		
		$obj->id	 	= $id;
		
		// Fix back quotes
  		$obj->path		= CString::str_ireplace( '\\' , '/' , $path );
  		$obj->type 		= $type;
  		$obj->appType	= $appType;
  		
  		$sql = 'SELECT COUNT(*) FROM ' . $db->nameQuote('#__community_avatar')
			.  ' WHERE ' . $db->nameQuote('id') .'=' . $db->Quote($id)
			.  ' AND ' . $db->nameQuote('apptype') .'=' . $db->Quote($appType)
			.  ' AND ' . $db->nameQuote('type') .'=' . $db->Quote($type);
  		$db->setQuery($sql);
  		$exist = $db->loadResult();
  		
  		if(!$exist){
  			$db->insertObject('#__community_avatar', $obj);
  			
  			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
		    }
		    
  		}else{
  			// Need to delete old image
  			$sql = 'SELECT ' . $db->nameQuote('path') .' FROM ' . $db->nameQuote('#__community_avatar')
				.  ' WHERE ' . $db->nameQuote('id') .'=' . $db->Quote($id)
				.  ' AND ' . $db->nameQuote('apptype') .'=' . $db->Quote($appType) 
				.  ' AND ' . $db->nameQuote('type') .'=' . $db->Quote($type);
			$db->setQuery($sql);
			
			$oldfile = $db->loadResult();
			$oldfile = CString::str_ireplace('/', DS, $oldfile);
			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
		    }
			
			JFile::delete($oldfile);
  			
  			$sql = 'UPDATE ' . $db->nameQuote('#__community_avatar') .' SET ' . $db->nameQuote('path') .'=' . $db->Quote($obj->path)
			  	 . ' WHERE ' . $db->nameQuote('id') .'=' . $db->Quote($id)
			  	 . ' AND ' . $db->nameQuote('apptype') .'=' . $db->Quote($appType)
				 . ' AND ' . $db->nameQuote('type').'=' . $db->Quote($type);
  			
			$db->setQuery($sql);
  			$db->query();
  			
  			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
		    }
  		}
	}
}
