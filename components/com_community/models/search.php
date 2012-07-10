<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Search
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.utilities.date');
jimport('joomla.html.pagination');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

class CommunityModelSearch extends JCCModel
{
	var $_data = null;
	var $_profile;
	var $_pagination;
	var $_total;
 
	
	public function CommunityModelSearch(){
		parent::JCCModel();
 	 	$mainframe = JFactory::getApplication();
 	 	
 	 	// Get pagination request variables
 	 	$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : $mainframe->getCfg('list_limit');
	    $limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');
 	 	
 	 	// In case limit has been changed, adjust it
	    $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		 	 	
		$this->setState('limit',$limit);
 	 	$this->setState('limitstart',$limitstart);
	}	
	
	public function &getFiltered($wheres = array())
	{
		$db			= &$this->getDBO();
		
		$wheres[] = $db->nameQuote('block').' = '.$db->Quote('0');
		
		$query = "SELECT *"
			. ' FROM '.$db->nameQuote('#__users')
			. ' WHERE ' . implode( ' AND ', $wheres )
			. ' ORDER BY '.$db->nameQuote('id').' DESC ';
	
		$db->setQuery( $query );
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		$result = $db->loadObjectList();
		return $result;
	}
	
	
	/**
	 * get pagination data
	 */	 	
	public function getPagination()
	{
		return $this->_pagination;
	}
	
	/**
	 * get total data
	 */	 	
	public function getTotal()
	{
		return $this->_total;
	}

	/**
	 * Search for people
	 * @param query	string	people's name to seach for	
	 */	 
	public function searchPeople($query , $avatarOnly = '', $friendId = 0 )
	{
		$db			= &$this->getDBO();
		$config		= CFactory::getConfig();
		$filter		= array();
		$data		= array();
		$isEmail    = false;
		
		//select only non empty field
		foreach($query as $key => $value)
		{
			if(!empty($query[$key]))
			{
				$data[$key]=$value;
			}
		}
		
		// build where condition
		$filterField	= array();						
		if(isset($data['q']))
		{ 		
			$value			= $data['q'];

			CFactory::load( 'helpers' , 'validate' );
			if( CValidateHelper::email( JString::trim( $value ) ) )
			{
			    $isEmail    = true;
				if($config->get( 'privacy_search_email') != 2 )
				{
					$filter[]	= $db->nameQuote('email').'=' . $db->Quote( $value );
				}
			}
			else
			{
				$nameType	= $db->nameQuote( $config->get( 'displayname' ) );
				$filter[]	= 'UCASE(' . $nameType . ') LIKE UCASE(' . $db->Quote( '%' . $value . '%' ) . ')';
			}
		}
		
		$limit			= $this->getState('limit');
		$limitstart		= $this->getState('limitstart');	
	
		$finalResult	= array();
		$total			= 0;
		if(count($filter)> 0 || count($filterField > 0))
		{
			// Perform the simple search
			$basicResult = null;
			if(!empty($filter) && count($filter)>0)
			{
				if($friendId!=0){

				    $query = 'SELECT b.'.$db->nameQuote('friends')
						    .' FROM '.$db->nameQuote('#__community_users').' b';
				    $query .= ' WHERE b.'.$db->nameQuote('userid').' = '.$db->Quote($friendId);
				    $db->setQuery( $query );
				    $friendListId = $db->loadResult();

				    $friendListQuery = ' AND '.$db->nameQuote('id').' IN ('.$friendListId.')';

				}

				$query = 'SELECT distinct b.'.$db->nameQuote('id')
						.' FROM '.$db->nameQuote('#__users').' b';
				$query	.= ' INNER JOIN '.$db->nameQuote('#__community_users').' AS c ON b.'.$db->nameQuote('id').'=c.'.$db->nameQuote('userid');
				
				if(!empty($friendListQuery)){
				 $query.= $friendListQuery;
				}

				// @rule: Only fetch users that is configured to be searched via email.
				if( $isEmail && $config->get( 'privacy_search_email') == 1 )
				{
					$query  .= ' AND c.'.$db->nameQuote('search_email').'=' . $db->Quote( 1 );
				}

				if( $avatarOnly )
				{
					$query	.= ' AND c.'.$db->nameQuote('thumb').' != ' . $db->Quote( '' );
					$query	.= ' AND c.'.$db->nameQuote('thumb').' != ' . $db->Quote( 'components/com_community/assets/default_thumb.jpg' );
				}

				$query .= ' WHERE b.'.$db->nameQuote('block').' = '.$db->Quote('0').' AND '.implode(' AND ',$filter);

				$queryCnt	= 'SELECT COUNT(1) FROM ('.$query.') AS z';
				$db->setQuery($queryCnt);		
				$total	= $db->loadResult();
				
				$query .=  " LIMIT " . $limitstart . "," . $limit;
												
				$db->setQuery( $query );
				$finalResult = $db->loadResultArray();
				if($db->getErrorNum()) {
					JError::raiseError( 500, $db->stderr());
				}
			}
			
			// Appy pagination
			if (empty($this->_pagination))
			{		 	    
		 	    $this->_pagination = new JPagination($total, $limitstart, $limit);
		 	}
		} 				

		if(empty($finalResult))
			$finalResult = array(0);
			
		$id = implode(",",$finalResult);
		$where = array("`id` IN (".$id.")");
		$result = $this->getFiltered($where);
				
		return $result;
	}
	
	// @params $field, array with key[fieldcode] = value
	// just use 1 field for now
	public function searchByFieldCode($field)
	{			
		CError::assert($field , '', '!empty', __FILE__ , __LINE__ );
		
		$db			=& $this->getDBO();

		$keys = array_keys($field);
		$vals = array_values($field);		
		
		$fieldId = $this->_getFieldIdFromFieldCode($keys[0]);
	
		$sql = 'SELECT '.$db->nameQuote('user_id').' FROM '.$db->nameQuote('#__community_fields_values').' AS a'
		    .' INNER JOIN '.$db->nameQuote('#__community_users').' AS b'
		    .' ON a.'.$db->nameQuote('user_id').' = b.'.$db->nameQuote('userid')
			.' WHERE a.'.$db->nameQuote('value').'='. $db->Quote($vals[0]) 
			.' AND a.'.$db->nameQuote('field_id').'='. $db->Quote($fieldId);

		$sql	.= ' AND ((b.'.$db->nameQuote('profile_id').' = '.$db->Quote(0).')'
				. ' OR (b.'.$db->nameQuote('userid'). ' IN (
						SELECT d.'.$db->nameQuote('userid').' FROM '.$db->nameQuote('#__community_profiles_fields') . ' as c'
						.' INNER JOIN '.$db->nameQuote('#__community_users').' AS d'
						.' ON c.'.$db->nameQuote('parent').'=d.' . $db->nameQuote( 'profile_id' ) 
						.' AND c.'.$db->nameQuote('field_id').'=' . $db->Quote( $fieldId ) .')))';
		
		// Privacy
		$my		= CFactory::getUser();
		$sql	.= ' AND( ';
		
		// If privacy for this field is 0, then we just display it.
		$sql	.= ' (a.'.$db->nameQuote('access').' = '.$db->Quote('0').')';
		$sql	.= ' OR';
		
		// If privacy for this field is set to site members only, ensure that the user id is not empty.
		$sql	.= ' (a.'.$db->nameQuote('access').' = '.$db->Quote('20').' AND ' . $db->Quote( $my->id ) . '!='.$db->Quote('0').' )';
		$sql	.= ' OR';
		
		// If privacy for this field is set to friends only, ensure that the current user is a friend of the target.
		$sql	.= ' (a.'.$db->nameQuote('access').' = '.$db->Quote('30').' AND a.'.$db->nameQuote('user_id').' IN ( 
						SELECT c.'.$db->nameQuote('connect_to').' FROM '.$db->nameQuote('#__community_connection')
						.' AS c WHERE c.'.$db->nameQuote('connect_from').'=' . $db->Quote( $my->id ) 
						.' AND c.'.$db->nameQuote('status').'='.$db->Quote('1').'))';
		$sql	.= ' OR';
		
		// If privacy for this field is set to the owner only, ensure that the id matches.
		$sql	.= ' (a.'.$db->nameQuote('access').' = '.$db->Quote('40').' AND a.'.$db->nameQuote('user_id').'=' . $db->Quote( $my->id ) . ')';

		$sql	.= ')';

		$limit		= $this->getState('limit');
		$limitstart	= $this->getState('limitstart');
		$total		= 0;

		//getting result count.
		$queryCnt	= 'SELECT COUNT(1) FROM ('.$sql.') AS z';
		$db->setQuery($queryCnt);
		$total		= $db->loadResult();
				
		$sql .=  " LIMIT " . $limitstart . "," . $limit;			
		
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		if (empty($this->_pagination)) {
			$this->_pagination = new JPagination($total, $limitstart, $limit);
		}		
		
		// need to return user object
		// Pre-load multiple users at once
		$userids = array();
		foreach($result as $uid)
		{
			$userids[] = $uid->user_id;
		}

		CFactory::loadUsers($userids);

		$users = array();
		foreach($result as $row){
			$users [] = CFactory::getUser($row->user_id);
		}
		
		return $users;
	}
	
	
	public function _getFieldIdFromFieldCode($code)
	{
		CError::assert($code , '', '!empty', __FILE__ , __LINE__ );
		
		$db	=& $this->getDBO();
		$query	= 'SELECT' . $db->nameQuote( 'id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__community_fields' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'fieldcode' ) . '=' . $db->Quote( $code );
		$db->setQuery( $query );
		$id		= $db->loadResult();
		
		CError::assert($id , '', '!empty', __FILE__ , __LINE__ );
		return $id;
	}
	
	/**
	 * Method to get users list on this site
	 * 	 
	 **/	 	
	public function getPeople( $sorted = 'latest', $filter = 'all' )
	{
		$db			= &$this->getDBO();
		$limit		= $this->getState('limit');
		$limitstart = $this->getState('limitstart');
		$config		= CFactory::getConfig();
		
		$query		= 'SELECT distinct(a.'.$db->nameQuote('id').') FROM '.$db->nameQuote('#__users').' AS a '
					. ' LEFT JOIN '.$db->nameQuote('#__session').' AS b '
					. ' ON a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('userid')
					. ' WHERE a.'.$db->nameQuote('block').'=' . $db->Quote( 0 );

		if( !$config->get( 'privacy_show_admins') )
		{
		    $userModel		= CFactory::getModel( 'User' );
			$tmpAdmins		= $userModel->getSuperAdmins();
			$admins         = array();
			
			$query  .= ' AND a.'.$db->nameQuote('id').' NOT IN(';
			for( $i = 0; $i < count($tmpAdmins);$i++ )
			{
			    $admin  = $tmpAdmins[ $i ];
			    $query  .= $db->Quote( $admin->id );
			    $query  .= $i < count($tmpAdmins) - 1 ? ',' : '';
			}
			$query  .= ')';
		}
		
//		$db->setQuery($query);
//		$total		= $db->loadResult();
		
		$filterQuery	= '';
		
		switch( $filter )
		{
			case 'others':
				$filterQuery	.= ' AND a.'.$db->nameQuote('name').' REGEXP "^[^a-zA-Z]."';
			break;
			case 'all':
			break;
			default:
				$filterCount	= JString::strlen( $filter );
				$allowedFilters	= array('abc','def','ghi','jkl','mno','pqr','stu','vwx' , 'yz' );

				if( in_array( $filter , $allowedFilters ) )
				{
					$filterQuery	.= ' AND(';
					for( $i = 0; $i < $filterCount; $i++ )
					{
						$char			= $filter{$i};
						$filterQuery	.= $i != 0 ? ' OR ' : ' ';
						$field			= $config->get( 'displayname' );
						$filterQuery	.= 'a.'.$db->nameQuote($field).' LIKE '.$db->Quote(JString::strtoupper($char).'%').' OR a.'.$db->nameQuote($field) . ' LIKE '.$db->Quote(JString::strtolower($char) . '%'); 
					}
					$filterQuery	.= ')';
				}
			break;
		}

		$query	.= $filterQuery;

		switch( $sorted )
		{
			case 'online':
				$query	.= 'ORDER BY b.'.$db->nameQuote('userid').' DESC';
				break;
			case 'alphabetical':
				$config	= CFactory::getConfig();

				$query	.= ' ORDER BY a.'.$db->nameQuote($config->get('displayname')) .' ASC';
				break;
			default:
				$query	.= ' ORDER BY a.'.$db->nameQuote('registerDate').' DESC';
				break;
		}

		if( !$this->_pagination )
		{
			$pagingQuery	= CString::str_ireplace( 'distinct(a.'.$db->nameQuote('id').')' , 'COUNT(DISTINCT(a.'.$db->nameQuote('id').'))' , $query);
			$db->setQuery($pagingQuery);
			$total		= $db->loadResult();		
			$this->_pagination = new JPagination($total, $limitstart, $limit);
		}

		$query	.= ' LIMIT ' . $limitstart . ',' . $limit;
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		$cusers = array();

		// Pre-load multiple users at once
		$userids = array();
		
		// This should not happen at all since every Joomla installation has a single user added by default.
		// However, it would be nice if we don't throw any errors at all when we try to loop the results.
		if( !$result )
		{
			return;
		}
		
		foreach($result as $uid)
		{
			$userids[] = $uid->id;
		}
		CFactory::loadUsers($userids);

		for($i = 0; $i < count($result); $i++)
		{
			$usr = CFactory::getUser(	$result[$i]->id );
			$cusers[] = $usr;
		}
		return $cusers;
	}
	
	/**
	 * method to get the custom field options list.
	 * param - field id - int
	 * returm - array	 
	 */	 	 	
	
	public function getFieldList($fieldId)
	{
		$db	=& $this->getDBO();
		
		$query	= 'SELECT '.$db->nameQuote('options').' FROM '.$db->nameQuote('#__community_fields');
		$query	.= ' WHERE '.$db->nameQuote('id').' = ' . $db->Quote($fieldId);
		
		$db->setQuery($query);
		$result = $db->loadObject();
		$listOptions	= null;
		
		
		if(isset($result->options) && $result->options != '')
		{
			$listOptions	= $result->options;
			$listOptions	= explode("\n", $listOptions);
			array_walk($listOptions, array('JString' , 'trim') );
		}//end if
		
		return $listOptions;
	}
	


	/**
	* Advance search with temporary table
	* 
	*/
	public function getAdvanceSearch($filter = array(), $join='and' , $avatarOnly = '' , $sorting = '' )
	{
		$limit 		= $this->getState('limit');
		$limitstart = $this->getState('limitstart');
	
		$db	=& $this->getDBO();
		
		$query	= $this->_buildCustomQuery($filter, $join , $avatarOnly );
		
		//lets try temporary table here
		$tmptablename = 'tmpadv';
		$drop = 'DROP TEMPORARY TABLE IF EXISTS '.$tmptablename;
		$db->setQuery($drop);
		$db->query();
		
		$query = 'CREATE TEMPORARY TABLE '.$tmptablename.' '.$query;
		$db->setQuery($query);
		$db->query();
		$total = $db->getAffectedRows();
		
		//setting pagination object.
		$this->_pagination = new JPagination($total, $limitstart, $limit);

		$query = 'SELECT * FROM '.$tmptablename;
		
		// @rule: Sorting if required.
		if( !empty( $sorting ) )
		{
			$query  .= $this->_getSort($sorting);
		}


		// execution of master query
		$query	.= ' LIMIT ' . $limitstart . ',' . $limit;
		$db->setQuery($query);

		$result = $db->loadResultArray();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		// Preload CUser objects
		if(! empty($result))
		{
			CFactory::loadUsers($result);
		}
		$cusers = array();
		for($i = 0; $i < count($result); $i++)
		{			
			//$usr = CFactory::getUser(	$result[$i]->user_id );
			$usr = CFactory::getUser( $result[$i] );
			$cusers[] = $usr;
		}		
		
		return 	$cusers;
	}

	
	public function _buildCustomQuery($filter = array(), $join='and' , $avatarOnly = '')
	{
		$db	=& $this->getDBO();
		$query		= '';
		$itemCnt	= 0;
		$config		= CFactory::getConfig();
		CFactory::load('libraries', 'datetime');

		/**
		 * For the 'ALL' case, we use 'IN' whereas for 'ANY' case, we use UNION.
		 *
		 */
		if(! empty($filter))
		{
			$filterCnt	= count($filter);

			foreach($filter as $obj)
			{
				if($obj->field == 'username' || $obj->field == 'useremail')
				{
					$useArray	= array('username' => $config->get('displayname') , 'useremail' => 'email');
					
					if($itemCnt > 0 && $join == 'or')
					{
						$query	.= ' UNION ';
					}
					
					$query	.= ($join == 'or') ? ' (' : '';
					$query	.= ' SELECT DISTINCT( b.'.$db->nameQuote('userid').' ) as '.$db->nameQuote('user_id');

					if( $itemCnt == 0 || $join == 'or')
					{
					    $query  .= ', a.'.$db->nameQuote('username').' AS '.$db->nameQuote('username');
					    $query  .= ', a.'.$db->nameQuote('name').' AS '.$db->nameQuote('name');
						$query  .= ', a.'.$db->nameQuote('registerDate').' AS '.$db->nameQuote('registerDate');
						$query	.= ', CASE WHEN s.'.$db->nameQuote('userid').' IS NULL THEN 0 ELSE 1 END AS online';
					}

					$query  .= ' FROM '.$db->nameQuote('#__users').' AS a';

					if( $itemCnt == 0 || $join == 'or')
					{
						$query  .= ' LEFT JOIN '.$db->nameQuote('#__session').' AS s';
						$query  .= ' ON a.'.$db->nameQuote('id').'=s.'.$db->nameQuote('userid');
					}

					$query	.= ' INNER JOIN '.$db->nameQuote('#__community_users').' AS b';
					$query	.= ' ON a.'.$db->nameQuote('id').' = b.'.$db->nameQuote('userid');
					$query	.= ' AND a.'.$db->nameQuote('block').' = '.$db->Quote('0');
	
					// @rule: Only fetch users that is configured to be searched via email.
					if( $obj->field == 'useremail' && $config->get( 'privacy_search_email') == 1 )
					{
						$query  .= ' AND b.'.$db->nameQuote('search_email').'=' . $db->Quote( 1 );
					}
					
					// @rule: Fetch records with proper avatar only.
					if( !empty($avatarOnly) )
					{
						$query .= ' AND b.' . $db->nameQuote( 'thumb' ) . ' != ' . $db->Quote( 'components/com_community/assets/default_thumb.jpg' );
						$query .= ' AND b.' . $db->nameQuote( 'thumb' ) . ' != ' . $db->Quote( '' );
					}
					
					$query	.= ' WHERE ' . $this->_mapConditionKey($obj->condition, $obj->fieldType, $obj->value, $useArray[$obj->field]);

					$query	.= ($join == 'or') ? ' )' : '';
					
					if($itemCnt < ($filterCnt - 1) && $join == 'and')
					{
						$query	.= ' AND b.'.$db->nameQuote('userid').' IN (';
					}
					
				}
				else
				{
					if($itemCnt > 0 && $join == 'or')
					{
						$query	.= ' UNION ';
					}
					
					$query	.= ($join == 'or') ? ' (' : '';
					$query	.= ' SELECT DISTINCT( a.'.$db->nameQuote('user_id').' ) AS '.$db->nameQuote('user_id');
					
					// We cannot select additional columns for the subquery otherwise it will result in operand errors,
					if( $itemCnt == 0 || $join == 'or' )
					{
					    $query  .= ', u.'.$db->nameQuote('username').' AS '.$db->nameQuote('username');
					    $query  .= ', u.'.$db->nameQuote('name').' AS '.$db->nameQuote('name');
						$query  .= ', u.'.$db->nameQuote('registerDate').' AS '.$db->nameQuote('registerDate');
						$query	.= ', CASE WHEN s.'.$db->nameQuote('userid').' IS NULL THEN 0 ELSE 1 END AS online';
					}
					$query  .= ' FROM '.$db->nameQuote('#__community_fields_values').' AS a';

					// We cannot select additional columns for the subquery otherwise it will result in operand errors,
					if( $itemCnt == 0 || $join == 'or')
					{
						$query  .= ' LEFT JOIN '.$db->nameQuote('#__session').' AS s';
						$query  .= ' ON a.'.$db->nameQuote('id').'=s.'.$db->nameQuote('userid');
					}
					
					
     				$query	.= ' INNER JOIN '.$db->nameQuote('#__community_fields').' AS b';
					$query	.= ' ON a.'.$db->nameQuote('field_id').' = b.'.$db->nameQuote('id');
					$query	.= ' INNER JOIN '.$db->nameQuote('#__users').' AS u ON a.'.$db->nameQuote('user_id').' = u.'.$db->nameQuote('id');
					$query	.= ' AND u.'.$db->nameQuote('block').' ='.$db->Quote('0');

					// @rule: Fetch records with proper avatar only.
					if( !empty($avatarOnly) )
					{
						$query	.= ' INNER JOIN '.$db->nameQuote('#__community_users').' AS c ON a.'.$db->nameQuote('user_id').'=c.'.$db->nameQuote('userid');
						$query	.= ' AND c.'.$db->nameQuote('thumb').' != ' . $db->Quote( '' );
						$query  .= ' AND c.'.$db->nameQuote('thumb').' != ' . $db->Quote( 'components/com_community/assets/default_thumb.jpg' );

					}

					if($obj->fieldType == 'birthdate')
					{
						$this->_birthdateFieldHelper($obj);
					}
					
					$query	.= ' WHERE b.'.$db->nameQuote('fieldcode').' = ' . $db->Quote($obj->field);
					$query	.= ' AND ' . $this->_mapConditionKey($obj->condition, $obj->fieldType, $obj->value);

					// Privacy
					$my		= CFactory::getUser();
					$query	.= ' AND( ';
					
					// If privacy for this field is 0, then we just display it.
					$query	.= ' (a.'.$db->nameQuote('access').' = '.$db->Quote('0').')';
					$query	.= ' OR';
					
					// If privacy for this field is set to site members only, ensure that the user id is not empty.
					$query	.= ' (a.'.$db->nameQuote('access').' = '.$db->Quote('20').' AND ' . $db->Quote( $my->id ) . '!=0 )';
					$query	.= ' OR';
					
					// If privacy for this field is set to friends only, ensure that the current user is a friend of the target.
					$query	.= ' (a.'.$db->nameQuote('access').' = '.$db->Quote('30').' AND a.'.$db->nameQuote('user_id').' IN ( 
									SELECT c.'.$db->nameQuote('connect_to').' FROM '.$db->nameQuote('#__community_connection') .' AS c'
									.' WHERE c.'.$db->nameQuote('connect_from').'=' . $db->Quote( $my->id ) . ' AND c.'.$db->nameQuote('status').'='.$db->Quote('1').')	)';
					$query	.= ' OR';
					
					// If privacy for this field is set to the owner only, ensure that the id matches.
					$query	.= ' (a.'.$db->nameQuote('access').' = '.$db->Quote('40').' AND a.'.$db->nameQuote('user_id').'=' . $db->Quote( $my->id ) . ')';
			
					$query	.= ')';
					
					$query	.= ($join == 'or') ? ' )' : '';
					
					if($itemCnt < ($filterCnt - 1) && $join == 'and')
					{
						$query	.= ' AND '.$db->nameQuote('user_id').' IN (';
					}

				}
				$itemCnt++;
			}
			
			$closeTag	= '';
			if($itemCnt > 1)
			{
				for($i = 0; $i < ($itemCnt - 1); $i++)
				{
					$closeTag .= ' )';
				}
			}

			$query	= ($join == 'and') ? $query . $closeTag : $query;

		}
		
		return $query;
	}
	
	public function _mapConditionKey($condition, $fieldType='text', $value, $fieldname = '')
	{
		$db	=& $this->getDBO();
		//the date time format for birthdate field is stored incorrectly, force to format
		if($fieldType=='birthdate' || $fieldType=='date'){
			$condString	= (empty($fieldname)) ? ' DATE_FORMAT(a.'.$db->nameQuote('value') .",'%Y-%m-%d %H:%i:%s')" : ' a.'.$db->nameQuote($fieldname) ;
		} else {
			$condString	= (empty($fieldname)) ? ' a.'.$db->nameQuote('value') : ' a.'.$db->nameQuote($fieldname) ;
		}
				
		switch($condition)
		{
			case 'between':
				//for now assume the value is date.
				$startVal	= '';
				$endVal		= '';
				if(is_array($value))
				{
					$startVal	= $value[0];
					$endVal		= $value[1];
				}
				else
				{
					$startVal	= $value;
					$endVal		= $value;
				}				
				$condString	.= ' BETWEEN ' . $db->Quote($startVal) . ' AND ' . $db->Quote($endVal);
				break;
				
			case 'equal':
				if($fieldType != 'text' && $fieldType != 'select' && $fieldType != 'singleselect' && $fieldType != 'email' && $fieldType != 'radio') //this might be the list, select and etc. so we use like.
				{
					$chkOptionValue	= explode(',', $value);
					
					if($fieldType == 'checkbox' && count($chkOptionValue) > 1)
					{												
						$chkValue	= array_shift($chkOptionValue);						
						$condString = '(' . $condString;
						$condString	.= ' LIKE ' . $db->Quote('%'.$chkValue.'%');
						foreach($chkOptionValue as $chkValue)
						{
							$condString	.= (empty($fieldname)) ? ' OR a.'.$db->nameQuote('value') : ' OR a.'.$db->nameQuote($fieldname);
							$condString	.= ' LIKE ' . $db->Quote('%'.$chkValue.'%'); 
						}
						$condString	.= ')';
					}
					else
					{
						$condString	.= (empty($value))? ' = ' . $db->Quote($value) : ' LIKE ' . $db->Quote('%'.$value.'%');
					}	
				}
				else
				{
					$condString	.= ' = ' . $db->Quote($value);				
				}								
				break;
				
			case 'notequal':
				if($fieldType != 'text' && $fieldType != 'select' && $fieldType != 'singleselect' && $fieldType != 'radio') //this might be the list, select and etc. so we use like.
				{
					$chkOptionValue	= explode(',', $value);

					if($fieldType == 'checkbox' && count($chkOptionValue) > 1)
					{
						$chkValue	= array_shift($chkOptionValue);						
						$condString = '(' . $condString;
						$condString	.= ' NOT LIKE ' . $db->Quote('%'.$chkValue.'%');
						foreach($chkOptionValue as $chkValue)
						{
							$condString	.= (empty($fieldname)) ? ' AND a.'.$db->nameQuote('value') : ' AND a.'.$db->nameQuote($fieldname);
							$condString	.= ' NOT LIKE ' . $db->Quote('%'.$chkValue.'%'); 
						}
						$condString	.= ')';
					}
					else
					{
						$condString	.= ' NOT LIKE ' . $db->Quote('%'.$value.'%');
						//$condString	.= (empty($value))? ' != ' . $db->Quote($value) : ' NOT LIKE ' . $db->Quote('%'.$value.'%');
					}
				}
				else
				{			
					$condString	.= ' != ' . $db->Quote($value);
				}
				break;
				
			case 'lessthanorequal':
				$condString	.= ' <= ' . $db->Quote($value);
				break;
				
			case 'greaterthanorequal':
				$condString	.= ' >= ' . $db->Quote($value);
				break;
				
			case 'contain':
			default :
				$condString	.= ' LIKE ' . $db->Quote('%'.$value.'%');
				break;
		}
		$condString	.= (empty($join)) ? '' : ')';
		
		return $condString;
	}
	
	/**
	 * Simple video search to search the title and description
	 **/	 
	public function searchVideo( $searchText )
	{
		$db		=& $this->getDBO();
		
		$limit			= $this->getState('limit');
		$limitstart		= $this->getState('limitstart');
		
		$query	= 'SELECT *, ' . $db->nameQuote('created') . ' AS lastupdated ' 
				. 'FROM ' . $db->nameQuote( '#__community_videos' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'status' ) . '=' . $db->Quote( 'ready' ) . ' ' 
				. 'AND ' . $db->nameQuote('published') . '=' . $db->Quote( 1 ) . ' '
				. 'AND (' . $db->nameQuote( 'title' ) . ' LIKE ' . $db->Quote( '%' . $searchText . '%' ) . ' '
				. 'OR ' . $db->nameQuote( 'description' ) . ' LIKE ' . $db->Quote( '%' . $searchText . '%' ) . ') ';
		
		$queryCnt	= 'SELECT COUNT(1) FROM ('.$query.') AS z';
		$db->setQuery($queryCnt);
		$this->_total= $db->loadResult();
		
		$query	.= 'LIMIT ' . $limitstart . ',' . $limit;
		 
		$db->setQuery( $query );
		$result	= $db->loadObjectList();
		
		// Appy pagination
		if (empty($this->_pagination))
		{		 	    
	 	    $this->_pagination = new JPagination($this->_total, $limitstart, $limit);
	 	}
		
		return $result;
	}
	
	/**
	 * auto user suggest search
	 * @param query	string	people's name to seach for
	 * param - fieldName	: string - name of the input box
	 *       - fieldId		: string - id of the input box	 
	 */	 
	public function getAutoUserSuggest($searchName, $displayName)
	{
		$db	= &$this->getDBO();
		$filter = array();
		
		// build where condition
		$filterField = array();						
		if(isset($searchName))
		{	
	    	switch($displayName)
	    	{
	    		case 'name':
	    			$filter[] = 'UCASE('.$db->nameQuote('name').') like UCASE(' . $db->Quote('%'.$searchName.'%') . ')';
	    			break;
	    		case 'username':
	    		default :
					$filter[] = 'UCASE('.$db->nameQuote('username').') like UCASE(' . $db->Quote('%'.$searchName.'%') . ')';
	    			break;
			}
	    }
				
		$finalResult	= array();		
		if(count($filter)> 0 || count($filterField > 0))
		{
			// Perform the simple search
			$basicResult = null;
			if(!empty($filter) && count($filter)>0)
			{
				$query = 'SELECT distinct b.'.$db->nameQuote('id').' FROM '.$db->nameQuote('#__users').' b';						  	    			  
				$query .= ' WHERE b.'.$db->nameQuote('block').' = '.$db->Quote(0).' AND '.implode(' AND ',$filter);				
				//$query .=  " LIMIT " . $limitstart . "," . $limit;
												
				$db->setQuery( $query );
				$finalResult = $db->loadResultArray();
				if($db->getErrorNum()) {
					JError::raiseError( 500, $db->stderr());
				}
			}
		} 				

		if(empty($finalResult))
			$finalResult = array(0);
			
		$id = implode(",",$finalResult);
		$where = array("`id` IN (".$id.")");
		$result = $this->getFiltered($where);
				
		return $result;
	}
	
	// since the user input value is age which is interger,
	// we need to convert it into datetime
	private function _birthdateFieldHelper(&$obj)
	{
		$is_age = true;
		$obj->fieldType = 'birthdate';

		//If value is not array, pass it back as array
		//if(!is_array($obj->value)){
        //            $obj->value = explode(',',$obj->value);
        //        }


                //detecting search by age or date
		if((is_array($obj->value) && strtotime($obj->value[0]) !== false && strtotime($obj->value[1]) !== false) 
			|| (!is_array($obj->value) && strtotime($obj->value))) {
			$is_age = false;
		} else {
			//the input value must be unsign number, else return
			if(is_array($obj->value)){
				if (!is_numeric($obj->value[0]) || !is_numeric($obj->value[1]) || intval($obj->value[0]) < 0 || intval($obj->value[1]) < 0){
					//invalid range, reset to 0
					$obj->value[0] = 0;
					$obj->value[1] = 0;
					return ;
				}
				$obj->value[0]	= intval($obj->value[0]);
				$obj->value[1]	= intval($obj->value[1]);
			} else {
				if(!is_numeric($obj->value) || intval($obj->value) < 0){
					//invalid range, reset to 0
					$obj->value = 0;
					return;
				}
				$obj->value = intval($obj->value);
			}
		}
		
		// correct the age order
		if (is_array($obj->value) && ($obj->value[1] > $obj->value[0]))
		{
			$obj->value = array_reverse($obj->value);
		}
		
		// TODO: something is wrong with comparing the datetime value
		// in text type instead of datetime type, 
		// e.g. BETWEEN '1955-09-07 00:00:00' AND '1992-09-07 23:59:59'   
		// we can't find '1992-02-26 23:59:59' in the result.
		
		if ($obj->condition == 'between')
		{
			if($is_age){
				$year0 = $obj->value[0]+1;
				$year1 = $obj->value[1];
				
				$datetime0 = new Datetime();
				$datetime0->modify('-'.$year0 . ' year');
				$obj->value[0] = $datetime0->format('Y-m-d 00:00:00');

				$datetime1 = new Datetime();
				$datetime1->modify('-'.$year1 . ' year');
				$obj->value[1] = $datetime1->format('Y-m-d 23:59:59');
				
			} else {
				$value0 = new JDate($obj->value[0]);
				$obj->value[0] = $value0->toFormat('%Y-%m-%d 00:00:00');
				$value1 = new JDate($obj->value[1]);
				$obj->value[1] = $value1->toFormat('%Y-%m-%d 23:59:59');			
			}
		}
		
		if ($obj->condition == 'equal')
		{
			// equal to an age means the birthyear range is 1 year
			// so we make it become a range
			$obj->condition = 'between';
				
			if($is_age){
				$age	= $obj->value;
				unset($obj->value);
				$year0 = $age + 1;
				$year1 = $age;
				
				$datetime0 = new Datetime();
				$datetime0->modify('-'.$year0 . ' year');
				$obj->value[0] = $datetime0->format('Y-m-d 00:00:00');

				$datetime1 = new Datetime();
				$datetime1->modify('-'.$year1 . ' year');
				$obj->value[1] = $datetime1->format('Y-m-d 23:59:59');
				
				
			} else {
				$value0 = new JDate($obj->value);
				$value1 = new JDate($obj->value);
				unset($obj->value);
				$obj->value[0] = $value0->toFormat('%Y-%m-%d 00:00:00');
				$obj->value[1] = $value1->toFormat('%Y-%m-%d 23:59:59');
			}
			
		}
		
		if ($obj->condition == 'lessthanorequal')
		{
			if($is_age){
				$obj->condition = 'between';
				
				$year0 = $obj->value+1;
				unset($obj->value);
				$datetime0 = new Datetime();
				$datetime0->modify('-'.$year0 . ' year');
				$obj->value[0] = $datetime0->format('Y-m-d 00:00:00');

				$datetime1 = new Datetime();
				$obj->value[1] = $datetime1->format('Y-m-d 23:59:59');
				
			} else {
				$obj->condition = 'lessthanorequal'; 
				$value0 = new JDate($obj->value);
				$obj->value = $value0->toFormat('%Y-%m-%d 23:59:59');;
			}
		}
		
		if ($obj->condition == 'greaterthanorequal')
		{
			if($is_age){
				$obj->condition = 'lessthanorequal'; //the datetime logic is inversed
				$age	= $obj->value;
				unset($obj->value);

				$year0 = $age;
				
				$datetime0 = new Datetime();
				$datetime0->modify('-'.$year0 . ' year');
				$obj->value = $datetime0->format('Y-m-d 00:00:00');

			} else {
				$obj->condition = 'between';
				$value0 = new JDate($obj->value);
				unset($obj->value);
				
				$obj->value[0] = $value0->toFormat('%Y-%m-%d 00:00:00');
				$value1 = new JDate();
				$obj->value[1] = $value1->toFormat('%Y-%m-%d 23:59:59');
			}
		}
		
		// correct the date order
		if (is_array($obj->value) && ($obj->value[1] < $obj->value[0]))
		{
			$obj->value = array_reverse($obj->value);
		}
		
	}
        
        private function _getSort( $sorting )
        {
                $db	=& $this->getDBO();
                $query = '';
                switch( $sorting )
                {
                        case 'online':
                                $query	= 'ORDER BY '.$db->nameQuote('online').' DESC';
                                break;
                        case 'alphabetical':
                                $config	= CFactory::getConfig();
                                $query	= ' ORDER BY ' . $db->nameQuote($config->get('displayname')) . ' ASC';
                                break;
                        default:
                                $query	= ' ORDER BY '.$db->nameQuote('registerDate').' DESC';
                                break;
                }
                
                return $query;
        }
}