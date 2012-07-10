<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableTagword extends JTable
{
	var $id 		= null;
	var $tag		= null;
	var $count 		= null;
	var $modified 	= null;

	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_tags_words', 'id', $db );
		
	}

	/**
	 *
	 * @param mixed $tag
	 * @return boolean
	 */
	public function load($tag){
		if(is_string($tag)){
			// Search via keyword
			$db		=& JFactory::getDBO();

			$query	= 'SELECT * FROM '
				. $db->nameQuote( '#__community_tags_words' ) . ' '
				. ' WHERE ' . $db->nameQuote( 'tag' ) . ' LIKE ' . $db->Quote( $tag );
			$db->setQuery( $query );
			$result = $db->loadObject();
			if(!empty($result)){
				$this->bind($result);

			} else {

				$this->tag  = $tag;
				$this->store();
			}
			
		} else {
			return parent::load($tag);
		}
	}

	/**
	 * Recalculate the count and last update time
	 */
	public function update($exclude = array()){
		// Search via keyword
		$db		=& JFactory::getDBO();

		$query	= 'SELECT count(*) FROM '
			. $db->nameQuote( '#__community_tags' ) . ' '
			. ' WHERE ' . $db->nameQuote( 'tag' ) . ' LIKE ' . $db->Quote( $this->tag );
		$db->setQuery( $query );
		$result = $db->loadResult();

		// Only update the stats if the count is not the same
		if($result != $this->count){
			$this->count = $result;

			$query	= 'SELECT * FROM '
			. $db->nameQuote( '#__community_tags' ) . ' '
			. ' WHERE ' . $db->nameQuote( 'tag' ) . ' LIKE ' . $db->Quote( $this->tag )
			. ' ORDER BY ' . $db->nameQuote( 'id' )
			. ' LIMIT 1 ';

			$db->setQuery( $query );
			$result = $db->loadObject();
			if(!empty($result)){
				$this->modified = $result->created;
			}
			

			$this->store();
		}

	}
}