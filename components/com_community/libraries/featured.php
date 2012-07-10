<?php
/**
 * @package		JomSocial
 * @subpackage	Library 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
class CFeatured
{
	var $stack	= null;
	var $type	= null;
	
	public function __construct( $type )
	{
		// Initialize stack type so that it can be used.
		$this->type				= $type;
		$this->_load();
	}
		
	public function _load()
	{
		$config	= CFactory::getConfig();
		$limit	= $config->get( 'featured' . $this->type . 'limit' , 10 );
		$db		=& JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote('#__community_featured')
				. ' WHERE ' . $db->nameQuote('type') .'=' . $db->Quote($this->type) 
				. ' ORDER BY ' . $db->nameQuote('id') .' DESC'
				. ' LIMIT 0,' . $limit;

		$db->setQuery($query);

		$this->stack	= $db->loadObjectList();
	}

	public function delete($cid)
	{
		$db		=& JFactory::getDBO();
		
		// Remove from Activity Stream
		// TODO: this is not be the best way
		$query	= 'SELECT ' . $db->nameQuote('created')
				. ' FROM ' . $db->nameQuote('#__community_featured')
				. ' WHERE ' . $db->nameQuote('cid') . '=' . $db->Quote($cid)
				. ' AND ' . $db->nameQuote('type') . '=' . $db->Quote($this->type);
		$db->setQuery($query);
		$actDate	= $db->loadResult();
		
		$query	= 'DELETE FROM ' . $db->nameQuote('#__community_activities')
				. ' WHERE ' . $db->nameQuote('created') . '=' . $db->Quote($actDate)
				. ' AND ' . $db->nameQuote('app') . '=' . $db->Quote($this->type);
		$db->setQuery($query);
		$db->query();
		
		// Remove from DB
		$query	= 'DELETE FROM ' . $db->nameQuote('#__community_featured')
				. ' WHERE ' . $db->nameQuote('cid') .'=' . $db->Quote( $cid )
				. ' AND ' . $db->nameQuote('type') .'=' . $db->Quote($this->type);
		$db->setQuery($query);
		
		// Remove cache.
		$this->removeCache();
		
		return $db->query();
	}
	
	public function add( $cid , $createdBy )
	{
		$config			= CFactory::getConfig();
		$limit			= $config->get( 'featured' . $this->type . 'limit' , 10 );
		$count			= count($this->stack);

		CFactory::load( 'models' , 'featured' );
		// Once limit is reached, shift first element off the stack.
		if( $count >= $limit )
		{
 			$removed	= array_pop( $this->stack );
			
			// We need to remove it from the database.
			$table		=& JTable::getInstance('Featured','CTable');
			$table->load( $removed->id );
			$table->delete();
		}

		// Add the latest featured into the stack.
		$table				=& JTable::getInstance( 'Featured' , 'CTable' );
		$table->cid			= $cid;
		$table->type		= $this->type;
		$table->created_by	= $createdBy;
		$table->created		= JFactory::getDate()->toMySQL();
		$table->store();
		
		$data				= new stdClass();
		$data->id			= $table->id;
		$data->cid			= $cid;
		$data->created_by	= $createdBy;
		$data->type			= $this->type;
		$data->created		= JFactory::getDate()->toMySQL();

		array_unshift($this->stack, $data );
		
		// Log into Activity Stream
		$this->_addToActivityStream($cid);
		
		// Remove cache.
		$this->removeCache();
		
		return true;
	}
	
	public function getItems()
	{
		return $this->stack;
	}
	
	/**
	 *	Check if an item is featured or not.
	 *
	 **/	 	 	
	public function isFeatured( $cid )
	{
		$ids	= $this->getItemIds();
		
		return in_array( $cid , $ids );
	}
	
	/**
	 *	Returns a list of unique ids from item
	 *
	 **/	 	 	
	public function getItemIds()
	{
		$id	= array();
		
		if($this->stack )
		{
			foreach( $this->stack as $item )
			{
				$id[]	= $item->cid;
			}
		}
		return $id;
	}
	
	/*
	 *	Add featured content into activity streams
	 */	 
	public function _addToActivityStream($cid = 0)
	{
		// $cid shouldn't be 0
		if ($cid == 0) return;
		
		// Construct activity stream
		$act			= new stdClass();
		$act->cid		= $cid;
		$act->target	= 0;
		$act->app		= $this->type;
		$act->cmd		= $this->type . '.featured';
		
		$params = new CParameter('');
		
		
		// Process each type of featured content
		switch ($this->type)
		{
			case FEATURED_GROUPS:
// 				CFactory::load('models', 'groups');
// 				$table			=& JTable::getInstance('Group', 'CTable');
// 				$table->load($cid);
// 				$act->actor		= 
// 				$groupUrl		= CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $table->id);
// 				$act->title		= JText::sprintf('COM_COMMUNITY_ACTIVITIES_FEATURED_GROUP', $groupUrl, $table->name);
// 				$act->content	= '<img src=\"' . $table->getAvatar() . '\" style=\"border: 1px solid #eee;margin-right: 3px;\" />';
// 				break;
				return;
			case FEATURED_USERS:
				$user			= CFactory::getUser($cid);
				$ownerUrl		= 'index.php?option=com_community&view=profile&userid=' . $user->id;
				$act->actor		= $user->id;
				$act->title		= JText::sprintf('COM_COMMUNITY_ACTIVITIES_FEATURED_USER', '{owner_url}', $user->getDisplayName());
				$act->content	= '';
				
				$params->set('owner_url'	, $ownerUrl );
				break;
			case FEATURED_VIDEOS:
				CFactory::load('models', 'videos');
				$table			=& JTable::getInstance('Video', 'CTable');
				$table->load($cid);
				$videoUrl		= $table->getViewURI();
				$ownerUrl		= 'index.php?option=com_community&view=profile&userid=' . $table->creator;
				$user			= CFactory::getUser($table->creator);
				$ownerName		= $user->getDisplayName();
				$act->actor		= $table->creator;
				$act->title		= JText::sprintf('COM_COMMUNITY_ACTIVITIES_FEATURED_VIDEO', '{owner_url}', $ownerName, '{video_url}');
				$config			= CFactory::getConfig();
				
				$params->set('owner_url'	, $ownerUrl );
				$params->set('video_url'	, $videoUrl );
				
				// embed the video when click show more
				// now only applies to external video provider

				break;
			case FEATURED_ALBUMS:
				CFactory::load('models', 'photos');
				$table			=& JTable::getInstance('Album', 'CTable');
				$table->load($cid);
				$albumUrl		= $table->getURI();
				$ownerUrl		= 'index.php?option=com_community&view=profile&userid=' . $table->creator;
				$user			= CFactory::getUser($table->creator);
				$ownerName		= $user->getDisplayName(); 
				$act->actor		= $table->creator;
				$act->title		= JText::sprintf('COM_COMMUNITY_ACTIVITIES_FEATURED_ALBUM', '{owner_url}', $ownerName, '{album_url}');
				$table->thumbnail= $table->getCoverThumbPath();
				$table->thumbnail= ($table->thumbnail) ? JURI::root() . $table->thumbnail : JURI::root() . 'components/com_community/assets/album_thumb.jpg';
				$act->content	= '<img src="' . $table->thumbnail . '" style="border: 1px solid #eee;margin-right: 3px;" />';
				// no such app as albums
				$act->app		= 'photos';
				
				$params->set('owner_url'	, $ownerUrl );
				$params->set('album_url'		, $albumUrl );
				
				break;
			default:
				// If featured type is unknown, we'll skip it
				return;
		}
		
		// Add activity logging with 0 points
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($act, $params->toString(),0);
	}
	
	/*
	 *	Remove featured cache.
	 */	 	
	public function removeCache(){
		switch ($this->type)
		{
			case FEATURED_GROUPS:
					CCache::remove(array(COMMUNITY_CACHE_TAG_GROUPS));
				break;
				
			case FEATURED_VIDEOS:
					CCache::remove(array(COMMUNITY_CACHE_TAG_VIDEOS));
				break;
				
			case FEATURED_ALBUMS:
					CCache::remove(array(COMMUNITY_CACHE_TAG_ALBUMS));
				break;
				
			case FEATURED_USERS:
					CCache::remove(array(COMMUNITY_CACHE_TAG_MEMBERS));
				break;
				
			default:
				break;
		}
	}
}