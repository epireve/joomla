<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Disallow direct access to this file
if(!defined('_JEXEC')) die('Restricted access');

class XiptTableUsers extends XiptTable
{

	function __construct( )
	{
		//userid is ker of xipt_users table
		parent::__construct( '#__xipt_users', 'userid');
	}
	
	/** 
	 * over ride the store function as it needs some modification 
     * in storing data for xipt_users table
	 */
	function store( $updateNulls=false )
	{
		$k = $this->_tbl_key;

		if( $this->isRowExists($this->$k) == true)
		{
			$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		}
		else
		{
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}

		if( !$ret )
			XipteError::raiseError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());

		return $ret;
	}
	
	/** 
	 * check for the row if already exists, according to promary key
	 * 
	 * @access public
	 * @param int primary key
	 * @return null|string null if successful otherwise returns and error message
	 */
	function isRowExists($pk)
	{
		$query = new XiptQuery();

		return $query->select('*')
			  ->from($this->getTableName())
 			  ->where(" `{$this->getKeyName()}` = $pk ")
			  ->dbLoadQuery("", "")
			  ->loadObject();
	}
	
}