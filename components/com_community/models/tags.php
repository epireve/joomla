<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
require_once ( JPATH_ROOT .DS.'components'.DS.'com_community'.DS.'models'.DS.'models.php');

// Deprecated since 1.8.x to support older modules / plugins
CFactory::load( 'tables' , 'tag' );
CFactory::load( 'tables' , 'tagword' );

interface CTaggable_Item
{
	public function tagGetTitle();			// Return the title of the object
	public function tagGetHtml();			// Return the HTML summary of the object
	public function tagGetLink();			// return the internal link of the object
	public function	tagAllow($userid);		// return true/false if the user can add the tag
}

class CommunityModelTags extends JCCModel
{

	public function CommunityModelPhotos()
	{
		parent::JCCModel();
	}

	/**
	 *
	 * @param string $element
	 * @param int $cid
	 * @param int $uid
	 * @return array CTableTag
	 */
	public function getTags($element, $cid, $uid = 0){
		$db		= JFactory::getDBO();
		
		$query	= 'SELECT a.*,b.'.$db->nameQuote('count').' FROM '
				. $db->nameQuote( '#__community_tags' ) . ' as a '
				. 'LEFT JOIN ( '.$db->nameQuote( '#__community_tags_words' ) .' AS b ) '
				. ' ON ( a.'.$db->nameQuote('tag').' = b.'.$db->nameQuote('tag').' ) '
				. ' WHERE a.' . $db->nameQuote( 'element' ) . '=' . $db->Quote( $element )
				. ' AND a.' . $db->nameQuote( 'cid' ) . '=' . $db->Quote( $cid );
				
		$db->setQuery( $query );
		$result	= $db->loadObjectList();
		
		// Update their correct Thumbnails and check album permissions
		$tags = array();
		if( !empty($result) )
		{
			foreach( $result as &$row ) 
			{				
				$tag	=& JTable::getInstance( 'Tag' , 'CTable' );
				$tag->bind($row);
				$tag->rank = $row->count;
				$tags[] = $tag;
			}
		}
		
		return $tags;
	}
	
	/**
	 * Return total count of tags
	 */	 	
	public function getTagsCount(){
		$db		= JFactory::getDBO();
		$query	= 'SELECT SUM(count) FROM '.$db->nameQuote( '#__community_tags_words' );
		$db->setQuery( $query );
		$result = $db->loadResult();
		
		return $result;
	}
	
	
	public function getRecentTags($limit)
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT DISTINCT '.$db->nameQuote('tag').' FROM '
				. $db->nameQuote( '#__community_tags' ) . ' '
				. ' ORDER BY ' . $db->nameQuote( 'created' ) . ' DESC '
				. ' LIMIT ' . $limit;

		$db->setQuery( $query );
		$result	= $db->loadResultArray();
		return $result;
	}

	/**
	 *
	 * @param string $tag
	 */
	public function getItems($tag)
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT * FROM '
				. $db->nameQuote( '#__community_tags' ) . ' '
				. ' WHERE ' . $db->nameQuote( 'tag' ) . ' LIKE ' . $db->Quote( $tag )
				. ' ORDER BY ' . $db->nameQuote( 'created' ) . ' DESC '
				. ' LIMIT 10';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		// Update their correct Thumbnails and check album permissions
		$tags = array();
		if( !empty($result) )
		{
			foreach( $result as &$row )
			{
				$tag	=& JTable::getInstance( 'Tag' , 'CTable' );
				$tag->bind($row);
				$tags[] = $tag;
			}
		}

		return $tags;
	}
}