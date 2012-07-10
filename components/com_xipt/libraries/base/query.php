<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
/**
 * @version		$Id: databasequery.php 14571 2010-02-04 07:07:47Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

if(!defined('_JEXEC')) die('Restricted access');


/**
 * Query Building Class.
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.6
 */
class XiptQuery
{
	/** @var string The query type */
	protected $_type = '';

	/** @var object The select element */
	protected $_select = null;

	/** @var object The delete element */
	protected $_delete = null;

	/** @var object The update element */
	protected $_update = null;

	/** @var object The insert element */
	protected $_insert = null;

	/** @var object The from element */
	protected $_from = null;

	/** @var object The join element */
	protected $_join = null;

	/** @var object The set element */
	protected $_set = null;

	/** @var object The where element */
	protected $_where = null;

	/** @var object The where element */
	protected $_group = null;

	/** @var object The where element */
	protected $_having = null;

	/** @var object The where element */
	protected $_order = null;
	
	/** @var object The where element */
	protected $_limit = 0;
	protected $_offset = 0;

	/** @var object The where element */
	protected $_ignore = null;
	
	
	/**
	 * Clear data from the query or a specific clause of the query.
	 *
	 * @param	string	Optionally, the name of the clause to clear, or nothing to clear the whole query.
	 */
	public function clear($clause = null)
	{
		switch ($clause) {
			case 'select':
				$this->_select = null;
				$this->_type = null;
				break;
			case 'delete':
				$this->_delete = null;
				$this->_type = null;
				break;
			case 'update':
				$this->_update = null;
				$this->_type = null;
				break;
			case 'insert':
				$this->_insert = null;
				$this->_type = null;
				break;
			case 'from':
				$this->_from = null;
				break;
			case 'join':
				$this->_join = null;
				break;
			case 'set':
				$this->_set = null;
				break;
			case 'where':
				$this->_where = null;
				break;
			case 'group':
				$this->_group = null;
				break;
			case 'having':
				$this->_having = null;
				break;
			case 'order':
				$this->_order = null;
				break;
			case 'limit':
				$this->_limit = null;
				break;
			case 'ignore':
				$this->_ignore = null;
				break;
				
			default:
				$this->_type = null;
				$this->_select = null;
				$this->_delete = null;
				$this->_udpate = null;
				$this->_insert = null;
				$this->_from = null;
				$this->_join = null;
				$this->_set = null;
				$this->_where = null;
				$this->_group = null;
				$this->_having = null;
				$this->_order = null;
				$this->_limit = null;
				$this->_ignore = null;
				break;
		}

		return $this;
	}


	/**
	 * @param	mixed	A string or an array of field names
	 */
	public function select($columns)
	{
		$this->_type = 'select';
		if (is_null($this->_select)) {
			$this->_select = new XiptQueryElement('SELECT', $columns);
		} else {
			$this->_select->append($columns);
		}

		return $this;
	}


	/**
	 * @param	mixed	A string or an array of field names
	 */
	public function delete()
	{
		$this->_type = 'delete';
		$this->_delete = new XiptQueryElement('DELETE', array(), '');
		return $this;
	}

	/**
	 * @param	mixed	A string or array of table names
	 */
	public function insert($tables)
	{
		$this->_type = 'insert';
		$this->_insert = new XiptQueryElement('INSERT INTO', $tables);
		return $this;
	}

	/**
	 * @param	mixed	A string or array of table names
	 */
	public function update($tables)
	{
		$this->_type = 'update';
		$this->_update = new XiptQueryelement('UPDATE', $tables);
		return $this;
	}

	/**
	 * @param	mixed	A string or array of table names
	 */
	public function from($tables)
	{
		if (is_null($this->_from)) {
			$this->_from = new XiptQueryelement('FROM', $tables);
		} else {
			$this->_from->append($tables);
		}

		return $this;
	}

	/**
	 * @param	string
	 * @param	string
	 */
	public function join($type, $conditions)
	{
		if (is_null($this->_join)) {
			$this->_join = array();
		}
		$this->_join[] = new XiptQueryelement(strtoupper($type) . ' JOIN', $conditions);

		return $this;
	}

	/**
	 * @param	string
	 */
	public function innerJoin($conditions)
	{
		$this->join('INNER', $conditions);

		return $this;
	}

	/**
	 * @param	string
	 */
	public function outerJoin($conditions)
	{
		$this->join('OUTER', $conditions);

		return $this;
	}

	/**
	 * @param	string
	 */
	public function leftJoin($conditions)
	{
		$this->join('LEFT', $conditions);

		return $this;
	}

	/**
	 * @param	string
	 */
	public function rightJoin($conditions)
	{
		$this->join('RIGHT', $conditions);

		return $this;
	}

	/**
	 * @param	mixed	A string or array of conditions
	 * @param	string
	 */
	public function set($conditions, $glue=',')
	{
		if (is_null($this->_set)) {
			$glue = strtoupper($glue);
			$this->_set = new XiptQueryelement('SET', $conditions, "\n\t$glue ");
		} else {
			$this->_set->append($conditions);
		}

		return $this;
	}

	/**
	 * @param	mixed	A string or array of where conditions
	 * @param	string
	 */
	public function where($conditions, $glue='AND')
	{
		if (is_null($this->_where)) {
			$glue = strtoupper($glue);
			$this->_where = new XiptQueryelement('WHERE', $conditions, " $glue ");
		} else {
			$this->_where->append($conditions);
		}

		return $this;
	}

	/**
	 * @param	mixed	A string or array of ordering columns
	 */
	public function group($columns)
	{
		if (is_null($this->_group)) {
			$this->_group = new XiptQueryelement('GROUP BY', $columns);
		} else {
			$this->_group->append($columns);
		}

		return $this;
	}

	/**
	 * @param	mixed	A string or array of columns
	 * @param	string
	 */
	public function having($conditions, $glue='AND')
	{
		if (is_null($this->_having)) {
			$glue = strtoupper($glue);
			$this->_having = new XiptQueryelement('HAVING', $conditions, " $glue ");
		} else {
			$this->_having->append($conditions);
		}

		return $this;
	}

	/**
	 * @param	mixed	A string or array of ordering columns
	 */
	public function order($columns)
	{
		if (is_null($this->_order)) {
			$this->_order = new XiptQueryElement('ORDER BY', $columns);
		} else {
			$this->_order->append($columns);
		}

		return $this;
	}
	
	public function limit($limit=0, $offset=0)
	{
		//IMP : Do not apply limit if it is Zero
		if($limit !=0 ){
			$this->_limit 	= $limit;
			$this->_offset 	= $offset;
		}
		return $this;
	}
	
	public function ignore()
	{
		$this->_type = 'ignore';
		$this->_ignore = new XiptQueryElement('IGNORE', array(), '');
		return $this;
	}
	

	/**
	 * @return	string	The completed query
	 */
	public function __toString()
	{
		$query = '';

		switch ($this->_type) {
			case 'select':
				$query .= (string) $this->_select;
				$query .= (string) $this->_from;
				if ($this->_join) {
					// special case for joins
					foreach ($this->_join as $join) {
						$query .= (string) $join;
					}
				}
				if ($this->_where) {
					$query .= (string) $this->_where;
				}
				if ($this->_group) {
					$query .= (string) $this->_group;
				}
				if ($this->_having) {
					$query .= (string) $this->_having;
				}
				if ($this->_order) {
					$query .= (string) $this->_order;
				}
							
				break;

			case 'delete':
				$query .= (string) $this->_delete;
				$query .= (string) $this->_ignore;
				$query .= (string) $this->_from;
				if ($this->_where) {
					$query .= (string) $this->_where;
				}
				
				break;

			case 'update':
				$query .= (string) $this->_update;
				$query .= (string) $this->_ignore;
				$query .= (string) $this->_set;
				if ($this->_where) {
					$query .= (string) $this->_where;
				}
				break;

			case 'insert':
				$query .= (string) $this->_insert;
				$query .= (string) $this->_ignore;
				$query .= (string) $this->_set;
				if ($this->_where) {
					$query .= (string) $this->_where;
				}
				break;
		}

		return $query;
	}

	function convertWhereIntoString()
	{
		if ($this->_where) {
			return $this->_where->convertWhereIntoString();
		}
		return true;
	}
	
	function dbLoadQuery($queryPrefix="", $querySuffix="")
	{
		//XITODO : Add limit and limitstart support in query class
		$db = JFactory::getDBO();
		$db->setQuery($queryPrefix.(string)$this.$querySuffix, $this->_offset,$this->_limit);
		return $db;
	}
}