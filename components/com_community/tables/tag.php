<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableTag extends JTable
{
	var $id 		= null;
	var $element 	= null;
	var $userid 		= null;
	var $cid 		= null;
	var $created 	= null;
	var $tag 		= null;

	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_tags', 'id', $db );
		
	}
	
	public function store(){
		// Make sure that there is no duplicate tag
		$tagModel = CFactory::getModel('tags');
		$tags = $tagModel->getTags($this->element, $this->cid);
		foreach($tags as $row){
			if($row->tag == $this->tag){
				return false;
			}
		}
		
		// Need to store first before updating the tag stats
		$result = parent::store();
		
		if($result){
			// Update tag words count
			$word =& JTable::getInstance('Tagword', 'CTable');
			$word->load($this->tag);
			$word->update();
		}
		
		return $result;
	}

}