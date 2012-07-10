<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

jimport( 'joomla.application.component.model' );

abstract class XiptModel extends JModel
{
	protected 	$_pagination	 = '';
	protected	$_query		= null;
	protected 	$_total 	= array();

	public function __construct($options = array())
	{
		//name can be collected by parent class
		if(array_key_exists('name',$options)==false)
			$options['name']	= $this->getName();

		if(array_key_exists('prefix',$options)==false)
			$options['prefix']	= $this->getPrefix();

		//now construct the parent
		parent::__construct($options);
		// IMP : This is running an extra query, eah time when an object is created
		//$this->getPagination();
	}

	/*
	 * We need to override joomla behaviour as they differ in
	 * Model and Controller Naming
	 */
	function getName()
	{
		if (!isset($this->_name))
		{
			$r = null;
			if (!preg_match('/Model(.*)/i', get_class($this), $r)) {
				JError::raiseError (__CLASS__.'.'.__LINE__, "XiptModel::getName() : Can't get or parse class name.");
			}
			$this->_name = strtolower( $r[1] );
		}

		return $this->_name;
	}

	/*
	 * Collect prefix auto-magically
	 */
	public function getPrefix()
	{
		if(isset($this->_prefix) && empty($this->_prefix)===false)
			return $this->_prefix;

		$r = null;
		if (!preg_match('/(.*)Model/i', get_class($this), $r)) {
			XiptError::raiseError (__CLASS__.'.'.__LINE__, "XiptModel::getPrefix() : Can't get or parse class name.");
		}

		$this->_prefix  =  JString::strtolower($r[1]);
		return $this->_prefix;
	}
	
	/*
	 * Count number of total records as per current query
	 */
	public function getTotal()
	{
		if($this->_total)
			return $this->_total;

		$query 			= $this->getQuery();
        $this->_total 	= $this->_getListCount((string) $query);

		return $this->_total;
	}

	
	public function getEmptyRecord()
	{
		$vars = $this->getTable()->getProperties();
		$retObj = new stdClass();

		$table = $this->getTable();
		$table->load(null);
	
		foreach($vars as $key => $value)		
			$retObj->$key = $table->$key;
		
		return array(0 => $retObj);
	}
	
	/*
	 * Returns Records from Model Tables
	 * as per Model STATE
	 */
	public function loadRecords($limit=null, $limitstart=null)
	{
		if($limit===null)
			$limit = $this->getState('limit',null);

		if($limitstart ===null)
			$limitstart = $this->getState('limitstart',0);

		$query = $this->getQuery();

		//there might be no table and no query at all
		if($query === null )
			return null;
			
		//we want returned record indexed by columns
		$pKey = $this->getTable()->getKeyName();
		$query->limit($limit,$limitstart);

		//for unique indexing using md5
		$index  = md5($query->__toString());
		
		$reset = XiptLibJomsocial::cleanStaticCache();
		if(isset($this->_recordlist[$index])&& $reset ==false)
			return $this->_recordlist[$index];
		
		$this->_recordlist[$index] = $query->dbLoadQuery("", "")
										->loadObjectList($pKey);
			
		return $this->_recordlist[$index];
	}


	/**
	 * Get an object of model-corresponding table.
	 * @return XiptTable
	 */
	public function getTable($tableName=null)
	{
		// support for parameter
		if($tableName===null)
			$tableName = $this->getName();

		return XiptFactory::getInstance($tableName,'Table');
	}

	function save($data, $pk=null)
	{
		if(isset($data)===false || count($data)<=0)
		{			
			XiptError::raiseError(__CLASS__.'.'.__LINE__,XiptText::_("NO_DATA_TO_SAVE"));
			return false;
		}

		//load the table row
		$table = $this->getTable();
		if(!$table){
			XiptError::raiseError(__CLASS__.'.'.__LINE__,sprintf(XiptText::_("TABLE_DOES_NOT_EXIST"),$table));
			return false;
		}
	
		// If table object was loaded by some code previously
		// then it can overwrite the previous record
		// So we must ensure that either PK is set to given value
		// Else it should be set to 0
		$table->reset();
		
		//if we have itemid then we MUST load the record
		// else this is a new record
		if($pk && $table->load($pk)===false){
			XiptError::raiseError(XiptText::_("NOT_ABLE_TO_LOAD_DATA"));
			return false;
		}

		//records should be clean after saving data in table
		//if you will not clean records, then caching will load old data
		$this->_recordlist = array();
		
		//bind, and then save, we should return the record_id updated/inserted
	    if($table->save($data))
			return $table->{$table->getKeyName()};

		//some error occured
		XiptError::raiseError(__CLASS__.'.'.__LINE__, XiptText::_("NOT_ABLE_TO_SAVE_DATA"));
		return false;
	}

	/**
	 * Method to delete rows.
	 */
	public function delete($filters,$glue='AND')
	{
		//XITODO assert for $pk
		//load the table row
		$table = $this->getTable();

		if(!$table)
			return false;

		//try to load and delete 
	    if($table->delete($filters,$glue))
	    	return true;

		XiptError::raiseError(__CLASS__.'.'.__LINE__,XiptText::_('NOT_ABLE_TO_DELETE_DATA'));		
		return false;
	}

	/**
	 * XITODO Method to order rows.
	 */
	public function order($pk, $change)
	{
		XiptError::assert($pk, sprintf(XiptText::_("PRIMARY_KEY_DOES_NOT_EXIST"),$pk), XiptError::ERROR);

		//load the table row
		$table = $this->getTable();

		if(!$table)
			return false;

		//try to move
	    if($table->load($pk) && $table->move($change))
			return true;

		//some error occured
		XiptError::raiseError(__CLASS__.'.'.__LINE__,XiptText::_("NOT_ABLE_TO_LOAD_DATA"));
		return false;
	}
	
	/**
	 * Returns the Query Object if exist
	 * else It builds the object
	 * @return XiQuery
	 */
	public function getQuery()
	{
		//query already exist
		if($this->_query)
			return $this->_query;

		//create a new query
		$this->_query = new XiptQuery();
		
		$this->_query->select('*'); 
		$this->_query->from($this->getTable()->getTableName());	
		return $this->_query;
	}

	/**
	 * @return XiPagination
	 */
	function &getPagination()
	{
	 	if($this->_pagination)
	 		return $this->_pagination;

		$this->_pagination = new XiptPagination($this);
		return $this->_pagination;
	}	
	
	function saveParams($data, $id, $what = '')
	{

	    XiptError::assert($id, sprintf(XiptText::_("ID_DOES_NOT_EXIST"),$id), XiptError::ERROR);
		
		XiptError::assert($what, sprintf(XiptText::_("PARAM_DOES_NOT_EXIST"),$what), XiptError::ERROR);
		
		if(empty($data) || !is_array($data))
			return false;
			
		//$xmlPath = XIPT_FRONT_PATH_ASSETS.DS.'xml'.DS. JString::strtolower($this->getName().".$what.xml");
		$iniPath = XIPT_FRONT_PATH_ASSETS.DS.'ini'.DS. JString::strtolower($this->getName().".$what.ini");
		$iniData = JFile::read($iniPath);
		
		$param	= new XiptParameter();
		$param->loadINI($iniData);
		$param->loadArray($data);
		$iniData	= $param->toString('XiptINI');
		return $this->save(array($what => $iniData), $id);
	}
	
	function loadParams($id, $what='')
	{		
		$record = $this->loadRecords(0);
		
		$xmlPath 	= XIPT_FRONT_PATH_ASSETS.DS.'xml'.DS.JString::strtolower($this->getName().".$what.xml");
		$iniPath	= XIPT_FRONT_PATH_ASSETS.DS.'ini'.DS.JString::strtolower($this->getName().".$what.ini");
		$iniData	= JFile::read($iniPath);

		XiptError::assert(JFile::exists($xmlPath), sprintf(XiptText::_("FILE_DOES_NOT_EXIST"),$xmlPath), XiptError::ERROR);
		
		$config = new XiptParameter($iniData,$xmlPath);
		if(isset($record[$id])) $config->bind($record[$id]->$what);	
		
		return $config;
	}
	
	public function boolean($pk, $column, $value)
	{
		return $this->save(array($column => $value), $pk);
	}
}
