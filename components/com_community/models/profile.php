<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Profile
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

class CommunityModelProfile extends JCCModel
{
	var $_data = null;
	var $_profile;

	var $_user	= '';
	var $_allField = null;
	
	public function _getUngroup()
	{
		$obj = new stdClass();
		$obj->id = 0;
		$obj->type =  'group';
		$obj->ordering =  2;
		$obj->published =  1;
		$obj->min =  0;
		$obj->max =  0;
		$obj->name =  'ungrouped';
		$obj->tips =  '';
		$obj->visible =  1;
		$obj->required =  1;
		$obj->searchable =  1 ;
		$obj->fields = array();
		
		return $obj;
	}
	
	public function aliasExists( $alias , $userId = '' )
	{
		// For backward compatibility, prior to 2.0, this method only has 1 parameter.
		if( empty($userId ) )
		{
			$my		= CFactory::getUser();
			$userId	= $my->id;
		}
		
		$db		= JFactory::getDBO();
		$config	= CFactory::getConfig();
		
		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote( '#__users' ) . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote( '#__community_users' ) . ' AS b '
				. ' ON b.'.$db->nameQuote('userid').'=a.'.$db->nameQuote('id')
				. ' WHERE ( CONCAT_WS("-", a.'.$db->nameQuote('id').' , LOWER( REPLACE(a.' . $db->nameQuote( $config->get('displayname') ) . ', " " , "-") ) )=LOWER(' . $db->Quote( $alias ) . ') '
				. 'OR b.'.$db->nameQuote('alias').'=' . $db->Quote( $alias ) . ') '
				. 'AND b.'.$db->nameQuote('userid').'!=' . $db->Quote( $userId );
		$db->setQuery( $query );
		$exists	= $db->loadResult() > 0 ? true : false;
		
		return $exists;
	}
	
	public function getGroup( $fieldId )
	{
		$db		=& $this->getDBO();
		$field	=& JTable::getInstance( 'ProfileField' , 'CTable' );
		$field->load( $fieldId );
		
		$query	= 'SELECT * '
				. 'FROM ' . $db->nameQuote( '#__community_fields' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'ordering' ) . '<' . $field->ordering . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' ) . ' '
				. 'ORDER BY '.$db->nameQuote('ordering').' DESC';
		$db->setQuery( $query );
		
		$result	= $db->loadObject();
		
		return $db->loadObject();
	}
	
	public function _loadAllFields($filter = array() , $type = COMMUNITY_DEFAULT_PROFILE )
	{
		if($this->_allField == null)
		{
			$this->_allField = array();
			$db		= JFactory::getDBO();
			
			//setting up the search condition is there is any
			$wheres = array();
			if(! empty($filter))
			{
				foreach($filter as $column => $value)
				{
					$wheres[] = $db->nameQuote($column) .' = ' . $db->Quote($value); 	
				}
			}
			
			if( $type != COMMUNITY_DEFAULT_PROFILE )
			{
				$query	= 'SELECT '.$db->nameQuote('field_id').' FROM ' . $db->nameQuote( '#__community_profiles_fields' )
						. ' WHERE ' . $db->nameQuote( 'parent' ) . '=' . $db->Quote( $type );
				$db->setQuery( $query );
				$filterIds	= $db->loadResultArray();
				
			  if(empty($filterIds)){
					$filterIds = array(0);					
				}
				$wheres[]	= $db->nameQuote('id').' IN (' . implode( ',' , $filterIds ) . ')';
			}

			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_fields' );	

			if(! empty($wheres))
			{
			   $query .= " WHERE ".implode(' AND ', $wheres);
			}
			
			$query .= ' ORDER BY '.$db->nameQuote('ordering');

			$db->setQuery($query);

			
			$fields = $db->loadObjectList();
			$group	= 'ungrouped';
			
			for($i = 0; $i < count($fields); $i++)
			{
				if($fields[$i]->type == 'group')
				{
					$group	= $fields[$i]->name;
					$this->_allField[$group] = $fields[$i];
					$this->_allField[$group]->fields = array();
				}
				else
				{
					// Re-arrange options to be an array by splitting them into an array
					if(isset($fields[$i]->options) && $fields[$i]->options != '')
					{
						$options	= $fields[$i]->options;
						$options	= explode("\n", $options);

						array_walk($options, array('JString' , 'trim') );
						$fields[$i]->options	= $options;
					}
					
					if($group == 'ungrouped' && empty($this->_allField[$group]))
					{
						$this->_allField[$group] = $this->_getUngroup();
					} 

					$this->_allField[$group]->fields[] =	$fields[$i];
				}
			}
		}
	}
	
	/**
	 * Return the complete (but empty) profile structure
	 */	 	
	public function &getAllFields($filter = array() , $profileType = COMMUNITY_DEFAULT_PROFILE )
	{
		$this->_loadAllFields($filter , $profileType );
		return $this->_allField;
	}	
	
	public function _bind($data){
	}
	
	/**
	 * Returns an object of user's data
	 * 	 	
	 * @access	public
	 * @param	none
	 * @returns object  An object that is related to user's data	 
	 */	 
	public function &getData()
	{
		$db	= &$this->getDBO();
		
		$wheres	  = array();
		$wheres[] = $db->nameQuote('block').' = '.$db->Quote('0');
		$wheres[] = $db->nameQuote('id').' = '. $db->Quote($this->getState('id'));
		
		$query = "SELECT *"
			. ' FROM '.$db->nameQuote('#__users')
			. ' WHERE ' . implode( ' AND ', $wheres )
			. ' ORDER BY '.$db->nameQuote('id').' DESC ';

		$db->setQuery( $query );
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		$result = $db->loadObject();

		return $result;
	}

	public function getProfileName( $fieldCode )
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote('name') . ' FROM '
				. $db->nameQuote( '#__community_fields') . ' WHERE '
				. $db->nameQuote( 'fieldcode') . '=' . $db->Quote( $fieldCode );
		
		$db->setQuery( $query );
		$name	= $db->loadResult();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		return $name;
	}
	
	/**
	 * Wrapper method
	 */	 	
	public function getProfile( $userId = null )
	{
		return $this->getViewableProfile( $userId );
	}
	
	/**
	 * Returns an array of custom profiles which are created from the back end.
	 * 	 	
	 * @access	public
	 * @param	string 	User's id.
	 * @returns array  An objects of custom fields.	 
	 */	 		
	public function getViewableProfile( $userId	= null , $profileType = COMMUNITY_DEFAULT_PROFILE )
	{
		$db			=& $this->getDBO();
		$data		= array();
		
		// Return with empty data
		if($userId == null || $userId == '')
		{
			//return false;
		}

		$user		=& JFactory::getUser($userId);
		
		if($user->id == null){
			//return false;
		}
		
		$data['id']		= $user->id;
		$data['name']	= $user->name;
		$data['email']	= $user->email;

		// Attach custom fields into the user object
		$query	= 'SELECT field.*, value.'.$db->nameQuote('value').',value.'.$db->nameQuote('access')
				. ' FROM ' . $db->nameQuote('#__community_fields') . ' AS field '
				. ' LEFT JOIN ' . $db->nameQuote('#__community_fields_values') . ' AS value '
 				. ' ON field.'.$db->nameQuote('id').'=value.'.$db->nameQuote('field_id').' AND value.'.$db->nameQuote('user_id').'=' . $db->Quote($userId)
				. ' WHERE field.'.$db->nameQuote('published').'=' . $db->Quote('1') . ' AND '
 				. ' field.'.$db->nameQuote('visible').'>=' . $db->Quote('1');
 		
 		// Build proper query for multiple profile types.
		if( $profileType != COMMUNITY_DEFAULT_PROFILE )
		{
			$query2	= 'SELECT '.$db->nameQuote('field_id').' FROM ' . $db->nameQuote( '#__community_profiles_fields' )
					. ' WHERE ' . $db->nameQuote( 'parent' ) . '=' . $db->Quote( $profileType );
			$db->setQuery( $query2 );
			$filterIds	= $db->loadResultArray();

			if( empty( $filterIds ) )
			{
				$data['fields']	= array();
				return $data;
			}
			
			$query	.= ' AND field.'.$db->nameQuote('id').' IN (' . implode( ',' , $filterIds ) . ')';
		}
		
		$query	.= ' ORDER BY field.'.$db->nameQuote('ordering');
		
		$db->setQuery( $query );

		$result	= $db->loadAssocList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		//let's check the viewer's relation to the profile he/she's about to see
		$visitor = CFactory::getUser();
		$access_limit = 0;
		
    		$isfriend = $visitor->isFriendWith($user->id);
		
		//let's set the maximum access limit viewer can go
		if($visitor->id > 0){
			$access_limit = PRIVACY_MEMBERS;
		}
		
		if($isfriend){
			$access_limit = PRIVACY_FRIENDS;
		}
		
		if($visitor->id == $user->id && $visitor->id != 0){ 
			$access_limit = PRIVACY_PRIVATE;
		}
		//=====================================

		$data['fields']	= array();
		for($i = 0; $i < count($result); $i++){

			// We know that the groups will definitely be correct in ordering.			
			if($result[$i]['type'] == 'group') {
				$group	= $result[$i]['name'];
				$this->_getResultData($data, $result, $i, $group);
			}
			
			// Re-arrange options to be an array by splitting them into an array
			$this->_getArrangedOptions($result, $i);

			// Only append non group type into the returning data as we don't
			// allow users to edit or change the group stuffs.
			if($result[$i]['type'] != 'group'){
				if($result[$i]['access'] <= $access_limit){ //check privacy access here
					if(!isset($group))
						$data['fields']['ungrouped'][]	= $result[$i];
					else
						$data['fields'][$group][]	= $result[$i];
				}
			}
		}
		//$this->_dump($data);
		return $data;
	}

	/**
	 * Returns an array of custom profiles which are created from the back end.
	 * 	 	
	 * @access	public
	 * @param	string 	User's id.
	 * @returns array  An objects of custom fields.	 
	 */	 		
	public function getEditableProfile($userId	= null , $profileType = COMMUNITY_DEFAULT_PROFILE )
	{
		$db			=& $this->getDBO();
		$data		= array();
		
		$user		=& JFactory::getUser($userId);
		
		$data['id']		= $user->id;
		$data['name']	= $user->name;
		$data['email']	= $user->email;

		// Attach custom fields into the user object		
		$query	= 'SELECT field.*, value.'.$db->nameQuote('value').' , value.'.$db->nameQuote('access')
				. 'FROM ' . $db->nameQuote('#__community_fields') . ' AS field '
				. 'LEFT JOIN ' . $db->nameQuote('#__community_fields_values') . ' AS value '
 				. 'ON field.'.$db->nameQuote('id').'=value.'.$db->nameQuote('field_id').' AND value.'.$db->nameQuote('user_id').'=' . $db->Quote($userId);
 
 		// Build proper query for multiple profile types.
		if( $profileType != COMMUNITY_DEFAULT_PROFILE )
		{
			$query2	= 'SELECT '.$db->nameQuote('field_id').' FROM ' . $db->nameQuote( '#__community_profiles_fields' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'parent' ) . '=' . $db->Quote( $profileType );
			$db->setQuery( $query2 );
			$filterIds	= $db->loadResultArray();

			if( empty( $filterIds ) )
			{
				$data['fields']	= array();
				return $data;
			}
						
			$query	.= ' WHERE field.'.$db->nameQuote('id').' IN (' . implode( ',' , $filterIds ) . ')';
			$query	.= ' AND field.'.$db->nameQuote('published').'=' . $db->Quote( '1' );
		}
		else
		{
			$query	.= ' WHERE field.'.$db->nameQuote('published').'=' . $db->Quote('1');
		}

		$query	.= ' ORDER BY field.'.$db->nameQuote('ordering');

		$db->setQuery( $query );

		$result	= $db->loadAssocList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		$data['fields']	= array();
		for($i = 0; $i < count($result); $i++)
		{

			if($result[$i]['type'] == 'group'){
			    $group	= $result[$i]['name'];
			    $this->_getResultData($data, $result, $i, $group);
			}

			// Re-arrange options to be an array by splitting them into an array
			$this->_getArrangedOptions($result, $i);

			// Only append non group type into the returning data as we don't
			// allow users to edit or change the group stuffs.
			if($result[$i]['type'] != 'group'){
				if(!isset($group))
					$data['fields']['ungrouped'][]	= $result[$i];
				else
					$data['fields'][$group][]	= $result[$i];
			}
		}
		//$this->_dump($data);
		return $data;
	}
	
	/**
	 * Returns an array of custom profiles which are created from the back end.
	 * 	 	
	 * @access	public
	 * @param	string 	User's id.
	 */	 
	public function _dump(& $data){
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		exit;
	}
		
	public function saveProfile($userId, $fields)
	{
		jimport('joomla.utilities.date');
		$db		=& $this->getDBO();

		foreach($fields as $id => $value)
		{
			$table	=& JTable::getInstance( 'FieldValue' , 'CTable' );

			if( !$table->load( $userId , $id ) )
			{
				$table->user_id		= $userId;
				$table->field_id	= $id;
			}
			
			if( is_object( $value ) )
			{
				$table->value	= $value->value;
				$table->access	= $value->access;
			}
			
			if( is_string( $value ) )
			{
				$table->value	= $value;
			}
			$table->store();
		}
	}
	
	/**
	 * Update user location with user's address information
	 * @param type $userid 
	 */
	public function updateLocationData($userid)
	{
		$usermodel	= CFactory::getModel('user');
		$user		= CFactory::getUser($userid);
		CFactory::load('libraries', 'mapping');
			 
		// Build the address string 
		$address = $user->getAddress();

		// Store the location
		$data = CMapping::getAddressData($address);

		// reset it to null;
		$latitude 	= COMMUNITY_LOCATION_NULL;
		$longitude	= COMMUNITY_LOCATION_NULL;

		if($data){
			if($data->status == 'OK')
			{
				$latitude  = $data->results[0]->geometry->location->lat;
				$longitude = $data->results[0]->geometry->location->lng; 
			}
		}

		$usermodel->storeLocation($user->id, $latitude, $longitude);
		
		return $this;
	}


	public function setProfile($v)
	{
		$this->_profile = $v;
		return $this;
	}
	
	/**
	 * Method to test if a specific field for a user exists
	 * 
	 * @param	String	$fieldCode	Field Code
	 * @param	String	$userId		Userid
	 * 
	 *	return boolean	True if exists and false otherwise.
	 **/
	public function _fieldValueExists( $fieldCode , $userId )
	{
		$db		= JFactory::getDBO();
		
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__community_fields' ) . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote( '#__community_fields_values' ) . ' AS b '
				. ' ON a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('field_id')
				. ' WHERE a.'.$db->nameQuote('fieldcode').'=' . $db->Quote( $fieldCode )
				. ' AND b.'.$db->nameQuote('user_id').'=' . $db->Quote( $userId );

		$db->setQuery( $query );

		$result	= ( $db->loadResult() >= 1 ) ? true : false;
		
		return $result;
	}
	
	/**
	 * Method to retrieve a field's id with a given field code
	 *
	 * @param	String	$fieldCode	Field code for the specific field.
	 **/	 
	public function getFieldId( $fieldCode )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM '
				. $db->nameQuote( '#__community_fields' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'fieldcode' ) . '=' . $db->Quote( $fieldCode );
		
		$db->setQuery( $query );
		
		$result	= $db->loadResult();
		
		return $result; 
	}
	
	/**
	 * Method to retrieve a field's id with a given field code
	 *
	 * @param	String	$fieldCode	Field code for the specific field.
	 **/	 
	public function getFieldCode( $fieldId )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'fieldcode' ) . ' FROM '
				. $db->nameQuote( '#__community_fields' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $fieldId );
		
		$db->setQuery( $query );
		
		$result	= $db->loadResult();
		
		return $result; 
	}
	
	/**
	 * Method to retrieve a field's params with a given field code
	 *
	 * @param	String	$fieldCode	Field code for the specific field.
	 **/	 
	public function getFieldParams( $fieldId )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'params' ) . ' FROM '
				. $db->nameQuote( '#__community_fields' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $fieldId );
		
		$db->setQuery( $query );
		
		$result	= $db->loadResult();
		
		return $result; 
	}
	
	public function updateUserData( $fieldCode , $userId , $value )
	{
		$db		= JFactory::getDBO();
		
		$data	= new stdClass();
		
		$fieldId	= $this->getFieldId( $fieldCode );

		if( $this->_fieldValueExists( $fieldCode , $userId ) )
		{
			// For existing record we just update it.
			$query	= 'UPDATE ' . $db->nameQuote( '#__community_fields_values' ) . ' '
					. 'SET ' . $db->nameQuote( 'value' ) . '=' . $db->Quote( $value ) . ' '
					. 'WHERE ' . $db->nameQuote( 'field_id' ) . '=' . $db->Quote( $fieldId ) . ' '
					. 'AND ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );

			$db->setQuery( $query );
			$db->query();
			return;
		}
		else
		{
			// For new records, we need to add it.
			$data			= new stdClass();
			$data->field_id	= $fieldId;
			$data->user_id	= $userId;
			$data->value	= $value;
			
			return $db->insertObject( '#__community_fields_values' ,  $data );
		}
	}
	
	public function formatDate($value, $format='')
	{
		$db		=& $this->getDBO();
		$config	= CFactory::getConfig();
		if(empty($format)){
			$format	= $config->get( 'profileDateFormat' );
		}
		
		$query	= 'SELECT DATE_FORMAT('.$db->Quote($value).', '.$db->Quote($format).') AS FORMATED_DATE';
		$db->setQuery($query);
		$result	= $db->loadResult();
		return $result; 
	}
	
	public function getAdminEmails()
	{
		$emails		= '';
		$db			=& $this->getDBO();

		if(C_JOOMLA_15){
			$query		= 'SELECT ' . $db->nameQuote('email')
						. ' FROM ' . $db->nameQuote('#__users')
						. ' WHERE ' . $db->nameQuote('gid') . '=' . $db->quote(24) 
						. ' OR ' . $db->nameQuote( 'gid' ) . '=' . $db->Quote( 25 );
		} else {
			$query		= 'SELECT a.' . $db->nameQuote('email')
						. ' FROM ' . $db->nameQuote('#__users') . ' as a, '
						. $db->nameQuote('#__user_usergroup_map') . ' as b'
						. ' WHERE a.' . $db->nameQuote('id') . '= b.' . $db->nameQuote('user_id') 
						. ' AND ( b.' . $db->nameQuote( 'group_id' ) . '=' . $db->Quote( 7 ) 
						. ' OR b.' . $db->nameQuote( 'group_id' ) . '=' . $db->Quote( 8 ) .')';
		}
							
		$db->setQuery($query);
		$emails		= $db->loadResultArray();
		
		return $emails; 	
	}
	
	/**
	 * Retrieves profile types available throughout the site.
	 * 
	 * @returns	Array	An array of objects.
	 **/	 	 	 	
	public function getProfileTypes()
	{
		$db		=& $this->getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_profiles' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
				. 'ORDER BY ' . $db->nameQuote( 'ordering' );
		$db->setQuery( $query );
		return $db->loadObjectList();
	}
	
	public function reset()
	{
		$this->_allField	= null;
	}

         private function _getResultData(&$data, &$result, &$i, &$group){
				
				if(!isset($data['fields'][$group])){
					// Initialize the groups.
					$data['fields'][$group]	= array();
				}
	                  
         }

          private function _getArrangedOptions(&$result, &$i){

            if(isset($result[$i]['options']) && $result[$i]['options'] != '')
            {
                $options	= $result[$i]['options'];
                $options	= explode("\n", $options);

                array_walk($options, array( 'JString' , 'trim' ) );

                $result[$i]['options']	= $options;
            }

          }
}
