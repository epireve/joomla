<?php
/**
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

/**
 * Jom Social Model file for photo tagging
 */

class CommunityModelPhotoTagging extends JCCModel
{

// 	var $id 			= null;
// 	var $photoid		= null;
// 	var $userid 		= null;
// 	var $position		= null;
// 	var $created_by 	= null;  
// 	var $created 		= null;
	var	$_error	= null;
	
	/* public array to retrieve return value */
	public $return_value = array();
	
	public function getError()
	{
		return $this->_error;
	}

	public function isTagExists($photoId, $userId)
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(1) AS CNT FROM '.$db->nameQuote('#__community_photos_tag');
		$query .= ' WHERE '.$db->nameQuote('photoid').' = ' . $db->Quote($photoId);
		$query .= ' AND '.$db->nameQuote('userid').' = ' . $db->Quote($userId);
		
		$db->setQuery($query);
		
		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}
		
		$result = $db->loadResult();
		return (empty($result)) ? false : true;
	}
	
	
	public function addTag( $photoId, $userId, $position)
	{
		$db		=& $this->getDBO();
		$my		= CFactory::getUser();
		$date	=& JFactory::getDate(); //get the time without any offset!
		
		$data				= new stdClass();
		$data->photoid		= $photoId;
		$data->userid		= $userId;
		$data->position		= $position;
		$data->created_by	= $my->id;
		$data->created		= $date->toMySQL();
		
		if($db->insertObject( '#__community_photos_tag' , $data))
		{
			//reset error msg.
			$this->_error	= null;
			$this->return_value[__FUNCTION__] = true;
		}
		else
		{
			$this->_error	= $db->stderr();
			$this->return_value[__FUNCTION__] = false;	
		}
		
		return $this;
	}
	
	public function removeTag( $photoId, $userId )
	{
		$db		=& $this->getDBO();			
		
		$query = 'DELETE FROM '.$db->nameQuote('#__community_photos_tag');
		$query .= ' WHERE '.$db->nameQuote('photoid').' = ' . $db->Quote($photoId);
		$query .= ' AND '.$db->nameQuote('userid').' = ' . $db->Quote($userId);		
		
		$db->setQuery($query);
		$db->query();
		
		if($db->getErrorNum())
		{
			$this->_error	= $db->stderr();
			return false;
		}		

		return true;
	}
	
	public function removeTagByPhoto($photoId)
	{
		$db		=& $this->getDBO();			
		
		$query = 'DELETE FROM '.$db->nameQuote('#__community_photos_tag');
		$query .= ' WHERE '.$db->nameQuote('photoid').' = ' . $db->Quote($photoId);
		
		$db->setQuery($query);
		$db->query();
		
		if($db->getErrorNum())
		{
			$this->_error	= $db->stderr();
			return false;
		}		

		return true;
	}
	
	public function getTagId( $photoId, $userId )
	{
		$db		=& $this->getDBO();			
		
		$query = 'SELECT '.$db->nameQuote('id').' FROM '.$db->nameQuote('#__community_photos_tag');
		$query .= ' WHERE '.$db->nameQuote('photoid').' = ' . $db->Quote($photoId);
		$query .= ' AND '.$db->nameQuote('userid').' = ' . $db->Quote($userId);		
		
		$db->setQuery($query);				
		
		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}
		
		$result = $db->loadResult();
				
		return $result;
	}	
	
	
	public function getTaggedList( $photoId )
	{
		$db =& $this->getDBO();	
		
		$query = 'SELECT a.* FROM '.$db->nameQuote('#__community_photos_tag').' as a';
		$query .= ' JOIN '.$db->nameQuote('#__users').'as b ON a.'.$db->nameQuote('userid').'=b.'.$db->nameQuote('id').' AND b.'.$db->nameQuote('block').'=0';
		$query .= ' WHERE a.'.$db->nameQuote('photoid').' = ' . $db->Quote($photoId);
		$query .= ' ORDER BY a.'.$db->nameQuote('id');

		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}		
		
		return $result;
	}
	
	public function getFriendList( $photoId )
	{
		$db =& $this->getDBO();
		$my	= CFactory::getUser();		
				
		$query	= 'SELECT DISTINCT(a.'.$db->nameQuote('connect_to').') AS id ';
		$query .= ' FROM '.$db->nameQuote('#__community_connection').' AS a';
		$query .= ' INNER JOIN '.$db->nameQuote('#__users').' AS b';
		$query .= ' ON a.'.$db->nameQuote('connect_from').' = ' . $db->Quote( $my->id ) ;
		$query .= ' AND a.'.$db->nameQuote('connect_to').' = b.'.$db->nameQuote('id');
		$query .= ' AND a.'.$db->nameQuote('status').' = '.$db->Quote('1');
		$query .= ' AND NOT EXISTS (';
		$query .= ' SELECT '.$db->nameQuote('userid').' FROM '.$db->nameQuote('#__community_photos_tag').' AS c'
					.' WHERE c.'.$db->nameQuote('userid').' = a.`'.$db->nameQuote('connect_to')
					.' AND c.'.$db->nameQuote('photoid').' = ' . $db->Quote( $photoId );
		$query .= ')';
						
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}		
		
		return $result;
	}
	
	
	

}

?>
