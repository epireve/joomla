<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');
jimport( 'joomla.application.component.table' );

abstract class XiptTable extends JTable
{
	protected	$_name;

	//apply caching
    public function getName()
	{
		if(isset($this->_name))
			return $this->_name;
			
		$r = null;
		if (!preg_match('/Table(.*)/i', get_class($this), $r)) {
			XiptError::raiseError (__CLASS__.'.'.__LINE__, "XiTable : Can't get or parse class name.");
		}
		$this->_name = strtolower( $r[1] );
		
		return $this->_name;
	}

	/*
	 * Collect prefix auto-magically
	 */
	public function getPrefix()
	{
		if(isset($this->_prefix))
			return $this->_prefix;

		$r = null;
		if (!preg_match('/(.*)Table/i', get_class($this), $r)) {
			XiError::raiseError (__CLASS__.'.'.__LINE__, "XiModel::getName() : Can't get or parse class name.");
		}

		$this->_prefix  =  JString::strtolower($r[1]);
		return $this->_prefix;
	}

	function __construct($tblFullName=null, $tblPrimaryKey=null, $db=null)
	{
		if($db===null)
			$db	=&	JFactory::getDBO();

		//call parent to build the table object
		parent::__construct( $tblFullName, $tblPrimaryKey, $db);

		//now automatically load the table fields
		//this way we do not need to do things statically
		$this->_loadTableProps();
	}

	public function reset()
	{
		$k = $this->_tbl_key;
		foreach ($this->getProperties() as $name => $value)
			$this->$name	= null;
		
		return true;
	}
	
	/**
     * Load properties of object based on table fields
     * It will be done via reading table from DB
     */
    private function _loadTableProps()
    {
   		$fields = $this->getColumns();

    	//still not found, the table
    	if(empty($fields))
    		return false;

    	foreach ($fields as $name=>$type){
    		$this->set($name,NULL);
    	}

        return true;
    }

	/**
	 * Get structure of table from db table
	 */
	public function getColumns()
	{
		if(isset($this->_columns))
			return $this->_columns;
			
		$tableName 	= $this->getTableName();
		if(XiptHelperTable::isTableExist($tableName)===FALSE)
			return XiptError::raiseError(__CLASS__.'.'.__LINE__, XiptText::_("Table $this->_tbl DOES_NOT_EXIST"));
			

		$fields 		= $this->_db->getTableFields($tableName);
		$this->_columns = $fields[$tableName];

		return $this->_columns;
	}
	
	function bind($data =array())
	{
		
		$prop = $this->getProperties();
		
		foreach($data as $key => $value){
			// set those properties which exists in filed list of table
			// otherwise do not update
			if(array_key_exists($key, $prop))
				$this->$key = $value;
		}
			
		return true;
	}
	
	function delete($oid,$glue='AND')
	{
		//if its a pk, then simple call parent
		if(is_array($oid)===false)
			return parent::delete($oid);

		// if an array/ means not a primiary key
		//Support multiple key-value pair in $oid
		//rather then deleting on behalf of key only
		if(empty($oid) || count($oid)<=0 )
			return false;

		$query = new XiptQuery(); 
		$query->delete()
			  ->from($this->getTableName());
		
		foreach($oid as $key=> $value){
			$query->where(" `$key`  = '$value' ",$glue);
		}
		// XITODO : generate warning if record does not exists
		return $query->dbLoadQuery("", "")
					 ->query();		
		
	}
}

