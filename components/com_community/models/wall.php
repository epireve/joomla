<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Walls
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

// Deprecated since 1.8.x to support older modules / plugins
CFactory::load( 'tables' , 'wall' );

class CommunityModelWall extends JCCModel
{
	var $_pagination	= '';
	
	/**
	 * Return 1 wall object
	 */	 	
	public function get($id){
		$db= JFactory::getDBO();

		$strSQL	= 'SELECT a.* , b.' . $db->nameQuote('name').' FROM ' . $db->nameQuote('#__community_wall').' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users').' AS b '
				. ' WHERE b.' . $db->nameQuote('id').'=a.' . $db->nameQuote('post_by')
				. ' AND a.' . $db->nameQuote('id').'=' . $db->Quote( $id ) ;
 
		$db->setQuery( $strSQL );
		
		if($db->getErrorNum()){
			JError::raiseError(500, $db->stderr());
		}
		
		$result = $db->loadObjectList();
		if(empty($result))
			JError::raiseError(500, 'Invalid id given');
			
		return $result[0];
	}
	
	/**
	 * Return an array of wall post
	 */	 	
	public function getPost($type, $cid, $limit, $limitstart, $order = 'DESC'){
		$db= JFactory::getDBO();

		$strSQL	= 'SELECT a.* , b.' . $db->nameQuote('name').' FROM ' . $db->nameQuote('#__community_wall').' AS a '
				. ' INNER JOIN ' . $db->nameQuote('#__users').' AS b '
				. ' WHERE b.' . $db->nameQuote('id').'=a.' . $db->nameQuote('post_by')
				. ' AND a.' . $db->nameQuote('type').'=' . $db->Quote( $type ) . ' '
				. ' AND a.' . $db->nameQuote('contentid').'=' . $db->Quote( $cid )
				. ' ORDER BY a.' . $db->nameQuote('date').' '. $order;
 
		$strSQL.= " LIMIT $limitstart , $limit ";

		$db->setQuery( $strSQL );
		if($db->getErrorNum()){
			JError::raiseError(500, $db->stderr());
		}
		
		$result=$db->loadObjectList();
		return $result;
	}
	
	
	/**
	 * Store wall post
	 */	 	
	public function addPost($type, $cid, $post_by, $message){
		$table = JTable::getInstance('Wall', 'CTable');
		$table->type = $type;
		$table->contentid = $cid;
		$table->post_by = $post_by;
		$table->message = $message;
		$table->store();
		
		return $table->id;
	}
	
	/**
	 * Return all the CTableWall object with the given type/cid
	 * 
	 */
	public function getAllPost($type, $cid)
	{
		/**
		 * Modified by Adam Lim on 14 July 2011
		 * Added ORDER BY date ASC to avoid messed up message display possibility
		 */
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_wall' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'contentid' ) . '=' . $db->Quote( $cid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
				. 'ORDER BY date ASC';
		
		$db->setQuery( $query );
		$results = $db->loadObjectList();

		 if($db->getErrorNum())
		 {
		 	JError::raiseError(500, $db->stderr());
		 }
		 
		 $posts = array();
		 foreach($results as $row)
		 {
		 	$table = JTable::getInstance('Wall', 'CTable');
		 	$table->bind($row);
		 	$posts[] = $table;
		 }
		 
		 return $posts;
	}
	
	/**
	 * This function removes all wall post from specific contentid
	 **/	 	
	public function deleteAllChildPosts( $uniqueId , $type )
	{
		CError::assert( $uniqueId , '' , '!empty' , __FILE__ , __LINE__ );
		CError::assert( $type , '' , '!empty' , __FILE__ , __LINE__ );

		$db	=   JFactory::getDBO();
		
		$query	=   'DELETE FROM ' . $db->nameQuote( '#__community_wall' ) . ' '
			    . 'WHERE ' . $db->nameQuote( 'contentid' ) . '=' . $db->Quote( $uniqueId ) . ' '
			    . 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );
		
		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}
		
		return true;
	}

	/**
	 *	Deletes a wall entry
	 *	
	 * @param	id int Specific id for the wall
	 * 	 
	 */
	 public function deletePost($id)
	 {
	 	CError::assert( $id , '' , '!empty' , __FILE__ , __LINE__ );
	 	
		$db = JFactory::getDBO();

		//@todo check content id belong valid user b4 delete
		$query	= 'DELETE FROM ' . $db->nameQuote('#__community_wall') . ' '
				. 'WHERE ' . $db->nameQuote('id') . '=' . $db->Quote( $id );
				
		 $db->setQuery($query);
		 $db->query();

		 if($db->getErrorNum())
		 {
		 	JError::raiseError(500, $db->stderr());
		 }
		 
		// Post an event trigger
		$args 	= array();
		$args[]	= $id;
		
		CFactory::load( 'libraries' , 'apps' );
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();
		$appsLib->triggerEvent( 'onAfterWallDelete' , $args );
		
		return true;	 
	}
	 
	 /**
	  *	Gets the count of wall entries for specific item
	  *	
	  * @params uniqueId	The unique id for the speicific item
	  * @params	type		The unique type for the specific item
	  **/
	 public function getCount( $uniqueId , $type )
	 {
	 	$cache = CFactory::getFastCache();
		$cacheid = __FILE__ . __LINE__ . serialize(func_get_args()) . serialize(JRequest::get());
		
		if( $data = $cache->get( $cacheid ) )
		{
			return $data;
		}
		
	 	CError::assert( $uniqueId , '' , '!empty' , __FILE__ , __LINE__ );
	 	$db		=& $this->getDBO();
	 	
	 	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_wall' )
	 			. 'WHERE ' . $db->nameQuote('contentid') . '=' . $db->Quote( $uniqueId )
	 			. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );
	 	
	 	$db->setQuery( $query );
	 	$count	= $db->loadResult();
	 	
		$cache->store($count, $cacheid);
	 	return $count;
	 }	  	  	  	  	 
	
	
	public function getPagination() {
		return $this->_pagination;
	}
}