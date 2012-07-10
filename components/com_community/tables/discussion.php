<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

// Include interface definition
CFactory::load( 'models' , 'tags' );

class CTableDiscussion extends JTable
	implements CTaggable_Item
{

	var $id			= null;
	var $groupid	= null;
	var $creator 	= null;
	var $created 	= null;
	var $title 		= null;
	var $message 	= null;
	var $lock	 	= null;
  
	/**
	 * Constructor
	 */	 	
	public function __construct( &$db )
	{
		parent::__construct( '#__community_groups_discuss', 'id', $db );
	}
	
	public function check()
	{
		// Filter the discussion
		$config = CFactory::getConfig();
		//$clean = ('none' != $config->get('htmleditor'));
		
		$safeHtmlFilter	= CFactory::getInputFilter();
		$this->title	= $safeHtmlFilter->clean($this->title);
		
		$safeHtmlFilter	= CFactory::getInputFilter($config->getBool('allowhtml'));
                $this->message 	= $safeHtmlFilter->clean($this->message);
		
		return true;
	}
	
	public function store()
	{
		if (!$this->check()) {
			return false;
		}
		
		$result = parent::store();
		
		if($result)
		{
			$this->_updateGroupStats();
		}
		return $result;
	}
	
	public function delete($oid=null)
	{
		$result = parent::delete($oid);
		
		if($result)
		{
			$this->_updateGroupStats();
		}
		return $result;
	}
	
	private function _updateGroupStats()
	{
		CFactory::load( 'models' , 'groups' );
		$group	=& JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $this->groupid );
		$group->updateStats();
		$group->store();
	}
	
	public function lock( $id=null, $lockStatus=false )
	{
		$db		= JFactory::getDBO();
		
		$obj		= new stdClass();
		$obj->id	= $id;
		$obj->lock	= $lockStatus;
		
		return $db->updateObject('#__community_groups_discuss',$obj,'id',false);
		
	}

	/**
	 * Return the title of the object
	 */
	public function tagGetTitle()
	{
		return $this->title;
	}

	/**
	 * Return the HTML summary of the object
	 */
    public function tagGetHtml()
	{
		return '';
	}

	/**
	 * Return the internal link of the object
	 *
	 */
	public function tagGetLink()
	{
		return $this->getViewURI();
	}
	
	/**
	 * Return true if the user is allow to modify the tag
	 *
	 */
	public function tagAllow($userid)
	{
		// @todo: neec to check with group admin
		return true;
	}
}