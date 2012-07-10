<?php
/**
 * @copyright (C) 2009 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once ( JPATH_ROOT .DS.'components'.DS.'com_community'.DS.'models'.DS.'models.php');

// Deprecated since 1.8.x to support older modules / plugins
CFactory::load( 'tables' , 'videos' );
CFactory::load( 'tables' , 'videoscategory' );

class CommunityModelVideos extends JCCModel
implements CLimitsInterface
{
	var $_pagination 	= '';
	var $total			= '';

	public function CommunityModelVideos()
	{
		parent::JCCModel();
		
		$id = JRequest::getVar('videoid', 0, '', 'int');
		$this->setId((int)$id);

 	 	$mainframe = JFactory::getApplication();

 	 	// Get the pagination request variables		
 	 	$limit	    =	($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
		$limitstart =	JRequest::getVar('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart =	($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		// Get cache object.
 	 	$oCache = CCache::inject($this);
 	 	$oCache->addMethod('updatePermission', CCache::ACTION_REMOVE, array(COMMUNITY_CACHE_TAG_VIDEOS));
	}

	public function setId($id)
	{
		// Set new video ID and wipe data
		$this->_id		= $id;
		return $this;
	}

	/**
	 *	Checks whether specific user or group has pending videos
	 *	
	 *	@params	$id	int	The unique id of the creator or groupid
	 *	@params	$type	string	The video type whether user or group	 	 	 
	 **/
	public function hasPendingVideos( $id , $type = VIDEO_USER_TYPE )
	{
		if($type == VIDEO_USER_TYPE && $id == 0)
		{
			return 0;
		}
		
		$db		= $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__community_videos' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'creator_type') . '=' . $db->Quote( $type ) . ' ';
				
		if( $type == VIDEO_USER_TYPE )
		{
			$query	.= 'AND ' . $db->nameQuote( 'creator' ) . '=' . $db->Quote( $id );
		}
		else
		{
			$query	.= 'AND ' . $db->nameQuote( 'groupid' ) . '=' . $db->Quote( $id );
		}
		
		$query	.= ' AND ' . $db->nameQuote( 'status' ) . '=' . $db->Quote( 'pending' );
		$query	.= ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		
		$db->setQuery($query);
		$result	= $db->loadResult() >= 1 ? true : false;
		
		return $result;
	}
	
	/**
	 * Loads the videos
	 * 
	 * @public
	 * @param	array	$filters	The video's filter
	 * @return	array	An array of videos object
	 * @since	1.2
	 */
	public function getVideos($filters = array(), $tableBind=false)
	{
		$cache = CFactory::getFastCache();
		$cacheid = serialize(func_get_args()) . serialize(JRequest::get());
		if($data = $cache->get( $cacheid ) )
		{
		    jimport('joomla.html.pagination');
	 	    $this->_pagination	= new JPagination($data['total'], $data['limitstart'], $data['limit']);
			return $data['entries'];
		}
		$db		= $this->getDBO();
		
		$where	= array();
		foreach ($filters as $field => $value)
		{
			if ($value || $value === 0)
			{
				switch (strtolower($field))
				{
					case 'id':
						if (is_array($value)) {
							JArrayHelper::toInteger($value);
							$value	= implode( ',', $value );
						}
						$where[]	= 'v.'.$db->nameQuote('id').' IN (' . $value . ')';
						break;
					case 'title':
						$where[]	= 'v.'.$db->nameQuote('title').'  LIKE ' . $db->quote('%' . $value . '%');
						break;
					case 'type':
						$where[]	= 'v.'.$db->nameQuote('type').' = ' . $db->quote($value);
						break;
					case 'description':
						$where[]	= 'v.'.$db->nameQuote('description').' LIKE ' . $db->quote('%' . $value . '%');
						break;
					case 'creator':
						$where[]	= 'v.'.$db->nameQuote('creator').' = ' . $db->quote((int)$value);
						break;
					case 'creator_type':
						$where[]	= 'v.'.$db->nameQuote('creator_type').' = ' . $db->quote($value);
						break;
					case 'created':
						$value		= JFactory::getDate($value)->toMySQL();
						$where[]	= 'v.'.$db->nameQuote('created').' BETWEEN ' . $db->quote('1970-01-01 00:00:01') . ' AND ' . $db->quote($value);
						break;
					case 'permissions':
						$where[]	= 'v.'.$db->nameQuote('permissions').' <= ' . $db->quote((int)$value);
						break;
					case 'category_id':
						if (is_array($value)) {
							JArrayHelper::toInteger($value);
							$value	= implode( ',', $value );
						}
						$where[]	= 'v.'.$db->nameQuote('category_id').' IN (' . $value . ')';
						break;
					case 'hits':
						$where[]	= 'v.'.$db->nameQuote('hits').' >= ' . $db->quote((int)$value);
						break;
					case 'published':
						$where[]	= 'v.'.$db->nameQuote('published').' = ' . $db->quote((bool)$value);
						break;
					case 'featured':
						$where[]	= 'v.'.$db->nameQuote('featured').' = ' . $db->quote((bool)$value);
						break;
					case 'duration':
						$where[]	= 'v.'.$db->nameQuote('duration').' >= ' . $db->quote((int)$value);
						break;
					case 'status':
						$where[]	= 'v.'.$db->nameQuote('status').' = ' . $db->quote($value);
						break;
					case 'groupid':
						$where[]	= 'v.'.$db->nameQuote('groupid').' = ' . $db->quote($value);
						break;
					case 'limitstart':
						$limitstart	= (int) $value;
						break;
					case 'limit':
						$limit		= (int) $value;
						break;					
				}
			}
		}

		$where		= count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
		
		// Joint with group table
		$join	= '';
		if (isset($filters['or_group_privacy']))
		{
			$approvals	= (int) $filters['or_group_privacy'];
			$join		=  ' LEFT JOIN ' . $db->nameQuote('#__community_groups') . ' AS g';
			$join 		.= ' ON g.'.$db->nameQuote('id').' = v.'.$db->nameQuote('groupid');
			$where		.= ' AND (g.'.$db->nameQuote('approvals').' = '.$db->Quote('0').' OR g.'.$db->nameQuote('approvals').' IS NULL)';
		}

		$order		= '';
		$sorting	= isset($filters['sorting']) ? $filters['sorting'] : 'latest';

		switch ($sorting)
		{
			case 'mostwalls':
				// mostwalls is sorted below using JArrayHelper::sortObjects
				// since in db vidoes doesn't has wallcount field
			case 'mostviews':
				$order	= ' ORDER BY v.'.$db->nameQuote('hits').' DESC';
				break;
			case 'title':
				$order	= ' ORDER BY v.'.$db->nameQuote('title').' ASC';
				break;
			case 'latest':
			default :
				$order	= ' ORDER BY v.'.$db->nameQuote('created').' DESC';
				break;
		}

		$limit		= (isset($limit)) ? $limit : $this->getState('limit');
		$limit		= ($limit < 0) ? 0 : $limit;
		$limitstart = (isset($limitstart)) ? $limitstart : $this->getState('limitstart');

		$limiter	= ' LIMIT '	. $limitstart . ', ' . $limit;

		$query		= ' SELECT v.*, v.'.$db->nameQuote('created').' AS lastupdated'
					. ' FROM ' . $db->nameQuote('#__community_videos') . ' AS v'
					. $join
					. $where
					. $order
					. $limiter;
		$db->setQuery($query);
		$result		= $db->loadObjectList();

		if ($db->getErrorNum())
			JError::raiseError(500, $db->stderr());

		// Get total of records to be used in the pagination
		$query		= ' SELECT COUNT(*)'
					. ' FROM ' . $db->nameQuote('#__community_videos') . ' AS v'
					. $join
					. $where
					;
		$db->setQuery($query);
		$total		= $db->loadResult();
		$this->total	= $total;

		if($db->getErrorNum())
			JError::raiseError( 500, $db->stderr());

		// Apply pagination
		if (empty($this->_pagination)) {
	 	    jimport('joomla.html.pagination');
	 	    $this->_pagination	= new JPagination($total, $limitstart, $limit);
	 	}


		// Add the wallcount property for sorting purpose
		foreach ($result as $video) {
			// Wall post count
			$query	= ' SELECT COUNT(*)'
					. ' FROM ' . $db->nameQuote('#__community_wall')
					. ' WHERE ' . $db->nameQuote('type') . ' = ' . $db->quote('videos')
					. ' AND ' . $db->nameQuote('published') . ' = ' . $db->quote(1)
					. ' AND ' . $db->nameQuote('contentid') . ' = ' . $db->quote($video->id)
					;
			$db->setQuery($query);
			$video->wallcount	= $db->loadResult();
		}

		// Sort videos according to wall post count
		if ($sorting == 'mostwalls')
			JArrayHelper::sortObjects( $result, 'wallcount', -1);

		$resultentriesdata = array('entries'=>$result,'total'=>$total,'limitstart'=>$limitstart,'limit'=>$limit);
		$cache->store($resultentriesdata, $cacheid,array(COMMUNITY_CACHE_TAG_VIDEOS));
		return $result;
	}

	/**
	 * Loads the categories
	 * 
	 * @access	public
	 * @return	array	An array of categories object
	 * @since	1.2
	 */
	public function getCategories($categoryId = null)
	{
		
		$cache = CFactory::getFastCache();
		$cacheid = serialize(func_get_args()) . serialize(JRequest::get());
		if($data = $cache->get( $cacheid ) )
		{
			return $data;
		}
		
		$my			= CFactory::getUser();
		$permissions= ($my->id==0) ? 0 : 20;
		$groupId	= JRequest::getVar('groupid' , '' , 'GET');
		$conditions = '';
		$db			= $this->getDBO();
		
		if( !empty($groupId) )
		{
			$conditions	= ' AND v.'.$db->nameQuote('creator_type').' = ' . $db->quote(VIDEO_GROUP_TYPE);
			//$conditions	.= ' AND b.groupid = ' . $groupId;
			$conditions	.= ' AND g.'.$db->nameQuote('id').' = ' .$db->Quote($groupId);
		}
		else
		{
			$conditions	.= ' AND (g.'.$db->nameQuote('approvals').' = '.$db->Quote('0').' OR g.'.$db->nameQuote('approvals').' IS NULL)';
		}
		
		$allcats	= $this->getAllCategories($categoryId);
		$result		= array();	
		foreach ($allcats as $cat)
		{
			$query	= ' SELECT COUNT(v.'.$db->nameQuote('id').') AS count'
					. ' FROM ' . $db->nameQuote('#__community_videos') . ' AS v'
					. ' LEFT JOIN ' . $db->nameQuote('#__community_groups') . ' AS g ON g.' . $db->nameQuote('id').' = v.' . $db->nameQuote('groupid')
					. ' WHERE v.' . $db->nameQuote('category_id').' = ' . $db->Quote($cat->id)
					. ' AND v.' . $db->nameQuote('status').' = ' . $db->Quote('ready')
					. ' AND v.' . $db->nameQuote('published').' = ' . $db->Quote(1)
					. ' AND v.' . $db->nameQuote('permissions').' <= ' . $db->Quote($permissions)
					. $conditions;
			$db->setQuery($query);
			$cat->count	= $db->loadResult(); 
			$result[] = $cat;
		}
		
		if($db->getErrorNum())
			JError::raiseError( 500, $db->stderr());

		$cache->store($result, $cacheid,array(COMMUNITY_CACHE_TAG_VIDEOS_CAT));
		return $result;
	}
	
	public function getAllCategories($catId = COMMUNITY_ALL_CATEGORIES)
	{
		$db		= $this->getDBO();

		$where	=   '';
		if( $catId !== COMMUNITY_ALL_CATEGORIES )
		{
			if( $catId === COMMUNITY_NO_PARENT )
			{
				$where	=   'WHERE ' . $db->nameQuote('parent').'=' . $db->Quote( COMMUNITY_NO_PARENT ) . ' ';
			}
			else
			{
				$where	=   'WHERE ' . $db->nameQuote('parent').'=' . $db->Quote( $catId ) . ' ';
			}
		}

		$query	= ' SELECT * '
				. ' FROM ' . $db->nameQuote('#__community_videos_category')
				. ' ' . $where.' ORDER BY '.$db->nameQuote('name').' ASC';
		$db->setQuery($query);
		$result	= $db->loadObjectList();
		return $result;
	}

	public function getPagination()
	{
		return $this->_pagination;
	}
	
	public function getTotal()
	{
		return $this->total;
	}
	
	public function deleteVideoWalls($id)
	{
		if (!$id) return;
		$db		= $this->getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote('#__community_wall')
				. ' WHERE ' . $db->nameQuote('contentid') . ' = ' . $db->quote($id)
				. ' AND ' . $db->nameQuote('type') . ' = ' . $db->quote('videos');
		$db->setQuery($query);
		$db->query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		return true;
	}
	
	public function deleteVideoActivities($id = 0)
	{
		if (!$id) return;
		$db		= $this->getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote('#__community_activities')
				. ' WHERE ' . $db->nameQuote('app') . ' = ' . $db->quote('videos')
				. ' AND ' . $db->nameQuote('cid') . ' = ' . $db->quote($id);
		$db->setQuery($query);
		$db->query();
		if($db->getErrorNum()){
			JError::raiseError( 500, $db->stderr());
		}
		return true;
	}
	
	/**
	 * Returns Group's videos
	 *
	 * @access public
	 * @param integer the id of the group
	 */	 
	public function getGroupVideos( $groupid, $categoryid="", $limit="" )
	{
	    
		$filter	= array(
			'groupid'		=> $groupid,
			'published'		=> 1,
			'status'		=> 'ready',
			'category_id'	=> $categoryid,
			'creator_type' 	=> VIDEO_GROUP_TYPE,
			'sorting'		=> JRequest::getVar('sort', 'latest'),
			'limit'			=> $limit
		);
		
		$videos 		= $this->getVideos( $filter );
		
		return $videos;
	}
	
	public function getPendingVideos()
	{
		$filter		= array('status' => 'pending');
		return $this->getVideos($filter);
	}
	
	/**
	 * Get the count of the videos from specific user
	 **/
	public function getVideosCount( $userId = 0, $videoType = VIDEO_USER_TYPE )
	{
		if ($userId==0) return 0;
		
		$db		= $this->getDBO();
		
		$query	= 'SELECT COUNT(1) FROM ' 
				. $db->nameQuote( '#__community_videos' ) . ' AS a '
				. 'WHERE ' . $db->nameQuote('creator').'=' . $db->Quote( $userId ) . ' '
				. 'AND ' . $db->nameQuote('creator_type').'=' . $db->Quote( $videoType );
		
		$db->setQuery( $query );
		$count	= $db->loadResult();
		
		return $count;
	}
	
	/**
	 * Retrieve a list of popular videos from the site.
	 *
	 * @param   int $limit  The total number of records to return.
	 **/
	public function getPopularVideos( $limit = 0 )
	{
		$filter = array(
							'published' => 1,
							'status'    => 'ready',
							'sorting'   => 'mostviews',
							'limit'     => $limit
						);

		$result 	= $this->getVideos( $filter );
		$videos		= array();
		foreach( $result as $row )
		{
			$video	=& JTable::getInstance( 'Video' , 'CTable' );
			$video->load( $row->id );
			$videos[]	= $video;
		}
		return $videos;

	}
	
	// A user updated his view permission, change the permission level for
	// all videos
	public function updatePermission($userid, $permission){
		$db	=&  $this->getDBO();
		$query	=   'UPDATE ' . $db->nameQuote('#__community_videos')
			    . ' SET ' . $db->nameQuote('permissions'). ' = '. $db->Quote( $permission ) 
			    . ' WHERE ' . $db->nameQuote('creator') . ' = ' . $db->Quote( $userid )
			    . ' AND ' . $db->nameQuote('groupid') . ' = ' . $db->quote(0);
		
		$db->setQuery( $query );
		$db->query();
		
		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
		}
		
		return $this;
	}

	/**
	 * Return total videos for the day for the specific user.
	 * 
	 * @param	string	$userId	The specific userid.	 
	 **/	 	 	
	public function getTotalToday( $userId )
	{
		$db		= JFactory::getDBO();
		$date	=& JFactory::getDate();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote('#__community_videos').' AS a WHERE '
				. $db->nameQuote( 'creator' ) . '=' . $db->Quote( $userId ) . ' '
				. 'AND TO_DAYS(' . $db->Quote( $date->toMySQL( true ) ) . ') - TO_DAYS( DATE_ADD( a.' . $db->nameQuote('created').' , INTERVAL ' . $date->getOffset() . ' HOUR ) ) = 0 ';
		
		$db->setQuery( $query );
		return $db->loadResult();
	}
}

abstract class CVideoProvider extends JObject
{
	abstract function getThumbnail();
	abstract function getTitle();
	abstract function getDuration();
	abstract function getType();
	abstract function getViewHTML($videoId, $videoWidth, $videoHeight);
	public function __construct($db=null) {
		parent::__construct();
	}
	
	/**
	 * Initialize the provider with video url resource
	 */	 	
	public function init($url)
	{
		$this->url 		= $url;
		$this->videoId 	= $this->getId();
	}
        
        /**
         * Return embedded code
         * 
         * @param type $videoId
         * @param type $videoWidth
         * @param type $videoHeight
         * @return type 
         * 
         */
        public function getEmbedCode($videoId, $videoWidth, $videoHeight)
	{
		return $this->getViewHTML($videoId, $videoWidth, $videoHeight);
	}
        
        /**
         * Return true if the video is valid.
         * This function uses a typical video privider method where they normally provide
         * a XML feed file to extract all the video info
         * @return type Boolean
         */
        public function isValid()
	{
		// Connect and get the remote video
		CFactory::load('helpers', 'remote');
		
                // Simple check, make sure video id exist
		if (empty($this->videoId))
		{
			$this->setError	( JText::_('COM_COMMUNITY_VIDEOS_INVALID_VIDEO_ID_ERROR') );
			return false;
		}
                
                // Youtube might return 'Video not found' in the content file
                $this->xmlContent = CRemoteHelper::getContent($this->getFeedUrl());
		if( $this->xmlContent == false )
		{
			$this->setError	( JText::_('COM_COMMUNITY_VIDEOS_FETCHING_VIDEO_ERROR') );
			return false;
		}


		return true;
	}
}