<?php
/**
 * @package		JomSocial
 * @subpackage	Core
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');


class CTags{
	const MIN_LENGTH = 3;
	private $lastInsert = 0;
	
	static function sortTag($tag1, $tag2)
	{
		if($tag1->rank == $tag2->rank)
			return 0;
		
		if( $tag1->rank > $tag2->rank)
			return -1;
		else
			return 1;
	}
	
	/**
	 * Return the HTML code to display the tagged users
	 *
	 */
	public function getHTML($element, $cid, $edit)
	{
		
		$tagModel 	= CFactory::getModel('tags');
		$tags 		= $this->getTags($element, $cid);
		$recentTags = $this->getRecentTags(10);
		$tagCount 	= $tagModel->getTagsCount();
		
		// @todo: limit number of tags shows in the first show, show
		// the rest via javascript
		
		
		$html = '';
		if(empty($tags) && !$edit)
		{
			// If user cannot edit the tag, and there is actually no tag at all, 
			// skip the whole tag area	
		} else {
			foreach($tags as &$row)
			{
				$tagCount = $tagCount == 0 ? $row->rank: $tagCount;
				$row->highlight =  ( ($row->rank / $tagCount) > 0.02 );
			}
			
			usort($tags, array("CTags", "sortTag")); 
			
			$tmpl = new CTemplate();
			$html = $tmpl	->set('recentTags'	, $recentTags)
							->set('tags'		, $tags)
							->set('element'	, $element)
							->set('cid'		, $cid)
							->set('edit'		, $edit)
							->fetch('tag.html');
		}
		return $html;
	}

	/**
	 *
	 * @param string $tag the tag to seach for
	 * @return string HTML code of item listing
	 */
	public function getItemsHTML($tag){
		$items = $this->getItems($tag);

		$tmpl = new CTemplate();

		$tmpl->set('items'	, $items);

		$html = $tmpl->fetch('tag.list');
		return $html;
	}

	/**
	 * Return a list of CTaggable_Item
	 *
	 * @param string $tag
	 */
	public function getItems($tag)
	{
		$tagModel = CFactory::getModel('tags');
		$items = $tagModel->getItems($tag);
		
		$taggableItem = array();

		$elementTableMap = array(
			'videos' => 'Video',
			'events' => 'Event',
			'groups' => 'Group',
			'discussion' =>'Discussion');
		
		foreach($items as $row)
		{
			// Get the taggable object item

			// @todo: ideally, we should be able to use the code below :(
			// $table	=&  JTable::getInstance( ucfirst($row->element) , 'CTable' );
// 			$table =&  JTable::getInstance( $elementTableMap[$row->element] , 'CTable' );
// 
// 			$table->load($row->cid);
			$table = $this->getItemTable($row);
			
			$taggableItem[] = $table;
		}
		return $taggableItem;
	}

	public function getItemTable($row){
		$elementTableMap = array(
			'videos' => 'Video',
			'events' => 'Event',
			'groups' => 'Group',
			'discussion' =>'Discussion',
			'albums' => 'Album');
		
		
		$table =&  JTable::getInstance( $elementTableMap[$row->element] , 'CTable' );
		$table->load($row->cid);
		
		return $table;
	}

	/**
	 *
	 * @param string $element
	 * @param int $id
	 * @param string $tagString
	 */
	public function add($element, $id, $tagString){
		
		// @todo: make sure current user has privilage to
		// add tag to the content 
		
		$my = CFactory::getUser();

		$now = new JDate();

		CFactory::load( 'tables' , 'tag' );

		$tag	=&  JTable::getInstance( 'Tag' , 'CTable' );

		$tag->element	= $element;
		$tag->userid	= $my->id;
		$tag->cid		= $id;
		$tag->created	= $now->toMySQL();
		$tag->tag		= $tagString;
		
		$success = $tag->store();
		$this->lastInsert = $tag->id;
		
		return $success;
	}

	public function delete($id){
	
		// @todo: make sure current user has the privilage to
		// remove tags
		
		CFactory::load( 'tables' , 'tag' );

		$tag	=&  JTable::getInstance( 'Tag' , 'CTable' );
		$tag->load($id);
		return $tag->delete();

	}
	
	public function lastInsertId(){
		return $this->lastInsert;
	}

	/**
	 * Return list of tags
	 * @param string $element
	 * @param int $id
	 */
	public function getTags($element, $cid){
		$tagModel = CFactory::getModel('tags');
		$tags = $tagModel->getTags($element, $cid);
		return $tags;
	}
	
	
	public function getRecentTags($limit){
		$tagModel = CFactory::getModel('tags');
		$tags = $tagModel->getRecentTags($limit);
		return $tags;
	}
}


