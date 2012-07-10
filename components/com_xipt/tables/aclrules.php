<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Disallow direct access to this file
if(!defined('_JEXEC')) die('Restricted access');

class XiptTableAclrules extends XiptTable
{	
	function __construct()
	{
		parent::__construct('#__xipt_aclrules','id');
	}
	
	/**
	 * Overrides Joomla's load method so that we can define proper values
	 * upon loading a new entry
	 * 
	 * @param	int	id	The id of the field
	 * @param	boolean isGroup	Whether the field is a group
	 * 	 
	 * @return boolean true on success
	 **/
	function load( $id )
	{
		// ID exist 
		if($id){
			return parent::load( $id );
		}
		
		// load the default value for new object 
		$this->id			= 0;
		$this->rulename		= '';
		$this->aclname		= '';
		$this->aclparams	= '';
		$this->coreparams	= '';
		$this->published	= true;
		return true;
	}

	/**
	 * Overrides Joomla's JTable store method so that we can define proper values
	 * upon saving a new entry
	 * 
	 * @return boolean true on success
	 **/
	function store( )
	{
 		parent::store();
 		return $this->id;
	}

}