<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Profile
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'models' . DS . 'models.php' );

class CommunityModelRegister extends JCCModel
{
	/* public array to retrieve return value */
	public $return_value = array();

	/*
	 * adding temporary user details
	 */ 			 
    public function addTempUser($data)
	{
	    $db    =& $this->getDBO();
		
		//get current session id.
		$mySess 	=& JFactory::getSession();
		$token		= $mySess->get('JS_REG_TOKEN','');
		
		$nowDate = JFactory::getDate();
		$nowDate = $nowDate->toMysql();
	    
	    // Combine firsname and last name as full name
		if (empty($data['jsname']))
		{
			$data['jsname'] =  $data['jsfirstname'] . ' ' . $data['jslastname'];
		}
	    
		$obj = new stdClass();
		$obj->name			= $data['jsname'];
		$obj->firstname		= isset( $data['jsfirstname'] ) ? $data['jsfirstname'] : '';
		$obj->lastname		= isset( $data['jslastname'] ) ? $data['jslastname'] : '';
		$obj->token			= $token;
		$obj->username		= $data['jsusername'];
		$obj->email			= $data['jsemail'];
		$obj->password		= $data['jspassword'];
		$obj->created		= $nowDate;
		$obj->ip			= isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		
		// no clear text password store in db
		jimport('joomla.user.helper');
		$salt			= JUserHelper::genRandomPassword(32);
		$crypt			= JUserHelper::getCryptedPassword($obj->password, $salt);
		$obj->password	= $crypt.':'.$salt;
		
		$db->insertObject('#__community_register', $obj);
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		$this->return_value[__FUNCTION__] = true;
		return $this;
	}

	/*
	 * Get temporary user details based on token string.
	 */		
	public function getTempUser($token) {
		$db    =& $this->getDBO();
		
		//the password2 is for JUser binding purpose.
		
		$query = 'SELECT *, '.$db->nameQuote('password').' as '.$db->nameQuote('password2')
				.' FROM '.$db->nameQuote('#__community_register');
		$query .= ' WHERE '.$db->nameQuote('token').' = '.$db->Quote($token);
		$db->setQuery($query);
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		$result = $db->loadObject();
		
		$user	= new JObject;
		$user->setProperties($result);
		
		return $user;
	}

	/*
	 * remove the temporary user from register table.
	 */		
	public function removeTempUser($token){
		$db    =& $this->getDBO();
		
		$query = 'DELETE FROM '.$db->nameQuote('#__community_register');
		$query .= ' WHERE '.$db->nameQuote('token').' = '.$db->Quote($token);
		
		$db->setQuery($query);
		$db->query();
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
	}
	
	public function cleanTempUser(){
		$nowDate		= JFactory::getDate();
		$nowDateMysql	= $nowDate->toMySQL();
		
		$jConfig	=& JFactory::getConfig();
		$lifetime	= $jConfig->getValue('lifetime');
		
		$db    =& $this->getDBO();
		
		$query = 'DELETE FROM '.$db->nameQuote('#__community_register');
		$query .= ' WHERE '.$db->nameQuote('created').' <= DATE_SUB('.$db->Quote($nowDateMysql).',  INTERVAL '.$lifetime.' MINUTE)';		
		
		$db->setQuery($query);
		$db->query();
		
		//
		$query = 'DELETE FROM '.$db->nameQuote('#__community_register_auth_token');
		$query .= ' WHERE '.$db->nameQuote('created').' <= DATE_SUB('.$db->Quote($nowDateMysql).',  INTERVAL '.$lifetime.' MINUTE)';
		
		$db->setQuery($query);
		$db->query();				
		
	}


	/**
	 * Adding user extra custom profile
	 */	 	
	public function addCustomProfile($data){
		    
		$db    =& $this->getDBO();
		
		$ok   = false;
		$user = $data['user'];
		$post = $data['post'];
		
		$query = "SELECT * FROM " . $db->nameQuote('#__community_fields') 
			. ' WHERE '.$db->nameQuote('published').'='.$db->Quote('1')
			. ' AND '.$db->nameQuote('type').' != '.$db->Quote('group')
			. ' ORDER BY '.$db->nameQuote('ordering');
		$db->setQuery($query);
		$fields = $db->loadObjectList();
		
// 		echo "<pre>";
// 		print_r($post);
// 		echo "</pre>";
// 		echo "<br/>";		
		
		// Bind result from previous post into the field object
		if(! empty($post)){
			for($i = 0; $i <count($fields); $i++){
				$fieldid = $fields[$i]->id;
				
// 				echo "<pre>";
// 				print_r($post['field'.$fieldid]);
// 				echo "</pre>";
// 				echo "<br/>";
				
				if(! empty($post['field'.$fieldid])){
					$fields[$i]->value = $post['field'.$fieldid];
				} else {
				    $fields[$i]->value = '';
				}
			}
			
			foreach ($fields as $field){
				$rcd = new stdClass();
				$rcd->user_id  = $user->id;
				$rcd->field_id = $field->id;
				
				if(is_array($field->value)){
				    $tmp	= '';
				
					// Now we need to test for 'date' specific fields as we need to convert the value
					// to unix timestamp
					$query	= 'SELECT ' . $db->nameQuote('type') . ' FROM ' . $db->nameQuote('#__community_fields') . ' '
							. 'WHERE ' . $db->nameQuote('id') . '=' . $db->Quote( $field->id );
					$db->setQuery( $query );
					$type	= $db->loadResult();
				
                	if( $type == 'date' )
					{
					    $values = $field->value;
						$day	= intval($values[0]);
						$month	= intval($values[1]);
						$year	= intval($values[2]);
						
						$day 	= !empty($day) 		? $day 		: 1;
						$month 	= !empty($month) 	? $month 	: 1;
	
						$tmp	= gmmktime( 0 , 0 , 0 , $month , $day , $year );						
					} else {
						foreach($field->value as $val)
						{
							$tmp .= $val . ',';
						}//end foreach
					}
					$rcd->value = $tmp;
				} else {				
				    $rcd->value	   = $field->value;
				}//end if
				
				$db->insertObject('#__community_fields_values', $rcd);
			}//end foreach
			
			$ok = true;
		}//end if
	    
	    return $ok;
	}
	
	/*
	 * 
     */
	public function isUserNameExists($filter = array()){
		$db			= &$this->getDBO();
		$found		= false;
		
// 		$query = "(SELECT `username`";
// 		$query .= " FROM #__users";
// 		$query .= " WHERE UCASE(`username`) = UCASE(".$db->Quote($filter['username'])."))";
// 		$query .= " UNION ";
// 		$query .= "(SELECT `username`";
// 		$query .= " FROM #__community_register";
// 		$query .= " WHERE UCASE(`username`) = UCASE(".$db->Quote($filter['username'])."))";

		/*
		 * DO NOT USE UNION. It will failed if the user joomla table's collation type was
		 * diferent from jomsocial tables's collation type
		 */		 		 

		$query = 'SELECT '.$db->nameQuote('username');
		$query .= ' FROM '.$db->nameQuote('#__users');
		$query .= ' WHERE UCASE('.$db->nameQuote('username').') = UCASE('.$db->Quote($filter['username']).')';
		
		$db->setQuery( $query );
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		$result = $db->loadObjectList();
		$found = (count($result) == 0) ? false : true;
		
		if(! $found && isset( $filter['ip'] ) ){
		
			$query = 'SELECT '.$db->nameQuote('username');
			$query .= ' FROM '.$db->nameQuote('#__community_register');
			$query .= ' WHERE UCASE('.$db->nameQuote('username').') = UCASE('.$db->Quote($filter['username']).')';
			$query .= ' AND '.$db->nameQuote('ip').' != '.$db->Quote($filter['ip']);		
		
			$db->setQuery( $query );
			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
			}
			$result = $db->loadObjectList();
			$found = (count($result) == 0) ? false : true;
		}
				
		return $found;
	
	}

	/*
	 * Method to check for exsisting email registered in jomsocial
     */	
	public function isEmailExists($filter = array()){
		$db			= &$this->getDBO();
		$found		= false;
		
// 		$query = "(SELECT `email`";
// 		$query .= " FROM #__users";
// 		$query .= " WHERE UCASE(`email`) = UCASE(".$db->Quote($filter['email'])."))";
// 		$query .= " UNION";
// 		$query .= "(SELECT `email`";
// 		$query .= " FROM #__community_register";
// 		$query .= " WHERE UCASE(`email`) = UCASE(".$db->Quote($filter['email'])."))";
		
		$query = 'SELECT '.$db->nameQuote('email');
		$query .= ' FROM '.$db->nameQuote('#__users');
		$query .= ' WHERE UCASE('.$db->nameQuote('email').') = UCASE('.$db->Quote($filter['email']).')';
		
		$db->setQuery( $query );
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		$result = $db->loadObjectList();
		$found = (count($result) == 0) ? false : true;
		
		if(! $found){
		
			$query = 'SELECT '.$db->nameQuote('email');
			$query .= ' FROM '.$db->nameQuote('#__community_register');
			$query .= ' WHERE UCASE('.$db->nameQuote('email').') = UCASE('.$db->Quote($filter['email']).')';
			if((isset($filter['ip'])) && (! empty($filter['ip'])))
				$query .= ' AND '.$db->nameQuote('ip').' != '.$db->Quote($filter['ip']);		
		
			$db->setQuery( $query );
			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
			}
			$result = $db->loadObjectList();
			$found = (count($result) == 0) ? false : true;
		}
				
		return $found;
	
	}
	/*
	 * Method to check for allowed email in jomsocial
     */	
	public function isEmailAllowed($email){
		$config	= CFactory::getConfig();
		CFactory::load( 'helpers' , 'validate' );
		$allowed_domains = $config->get('alloweddomains');
		if(!empty($allowed_domains)){
			$delimiter = ';';
			$allowed_list = explode($delimiter,$allowed_domains);
			$valid = false;
			if(count($allowed_list) > 0 ){
				foreach($allowed_list as $domain){
					if(CValidateHelper::domain( $email, $domain))
					{
						$valid = true;
					}
				}
			}
			if(!$valid){
				return false;
			}
		}
		return true;
	}
	/*
	 * Method to check for denied email in jomsocial
     */	
	public function isEmailDenied($email){
		$config	= CFactory::getConfig();
		CFactory::load( 'helpers' , 'validate' );
		$denied_domains = $config->get('denieddomains');
		if(!empty($denied_domains)){
			$delimiter = ';';
			$blacklists = explode($delimiter,$denied_domains);
			if(count($blacklists) > 0 ){
				foreach($blacklists as $domain){
					if(CValidateHelper::domain( $email, $domain))
					{
						return true;
					}
				}
			}
		}
		return false;
	}
	/**
	 * Function used to add new auth key
	 * param : new auth key - string
	 * return : boolean	 
	 */	
	public function addAuthKey ($authKey='')
	{
	    $db    =& $this->getDBO();
	    
		//get current session id.
		$mySess 	=& JFactory::getSession();
		$token		= $mySess->get('JS_REG_TOKEN','');
		
		$nowDate = JFactory::getDate();
		$nowDate = $nowDate->toMysql();
	    	    
		$obj = new stdClass();		
		$obj->token			= $token;
		$obj->auth_key		= $authKey;
		$obj->created		= $nowDate;
		$obj->ip			= isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		
		$db->insertObject('#__community_register_auth_token', $obj);
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		$this->return_value[__FUNCTION__] = true;
		return $this;
	}
	
	
	/**
	 * Function used to remove the assigned auth key.
	 *  param : current token - string	 
	 */	 	
	public function removeAuthKey ($token='')
	{
		$db    =& $this->getDBO();
		
		$query = 'DELETE FROM '.$db->nameQuote('#__community_register_auth_token');
		$query .= ' WHERE '.$db->nameQuote('token').' = '.$db->Quote($token);
		
		$db->setQuery($query);
		$db->query();
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}	
	}	
	
	/**
	 * Function used to get the valid auth key
	 * param : current token - string
	 *       : user ip address - string
	 * return : auth key - string      	 	 	 	 
	 */	
	public function getAuthKey ($token='', $ip='')
	{
		$authKey		= "";
		$curDate		= JFactory::getDate();
		$curDateMysql	= $curDate->toMySQL();
		
		$db    =& $this->getDBO();
		
		$config			= CFactory::getConfig();
		$expiryPeriod	= $config->get( 'sessionexpiryperiod' );			
	    $expiryPeriod	= (empty($expiryPeriod)) ? "600" : $expiryPeriod;
	    
		$query = 'SELECT '.$db->nameQuote('auth_key').' FROM '.$db->nameQuote('#__community_register_auth_token');
		$query .= ' WHERE '.$db->nameQuote('created').' >= DATE_SUB('.$db->Quote($curDateMysql).', INTERVAL '. $expiryPeriod . ' SECOND)';
		$query .= ' AND '.$db->nameQuote('token') .' = ' . $db->Quote($token);
		$query .= ' AND '.$db->nameQuote('ip').' = ' . $db->Quote($ip);
		
		$db->setQuery($query);								
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		$authKey	= $db->loadResult();
		return $authKey;
	}
	
	/**
	 * Function used to get the existing assigned auth key.
	 * param : current token - string
	 *       : user ip address - string
	 * return : auth key - string      	 	 	 	 
	 */	
	public function getAssignedAuthKey ($token='', $ip='')
	{
		$authKey		= "";
		$curDate		= JFactory::getDate();
		$curDateMysql	= $curDate->toMySQL();	
	
	    $db    =& $this->getDBO();

		$query = 'SELECT '.$db->nameQuote('auth_key').' FROM '.$db->nameQuote('#__community_register_auth_token');		
		$query .= ' WHERE '.$db->nameQuote('token').' = ' . $db->Quote($token);
		$query .= ' AND '.$db->nameQuote('ip').' = ' . $db->Quote($ip);
		
		$db->setQuery($query);								
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		$authKey	= $db->loadResult();
		return $authKey;		
	}	
	
	
	/**
	 * Function used to extend the auth key life span. Current set to 180 second.
	 * param : current token - string
	 *       : current authentication key - string
	 *       : user ip address - string
	 * return : boolean
	 */
	public function updateAuthKey ($token='', $authKey='',$ip='')
	{
		$authKey	= "";	
		$db    		=& $this->getDBO();

		$config			= CFactory::getConfig();
		$expiryPeriod	= $config->get( 'sessionexpiryperiod' );			
		$expiryPeriod	= (empty($expiryPeriod)) ? "600" : $expiryPeriod;	    

		$query = 'UPDATE '.$db->nameQuote('#__community_register_auth_token');
		$query .= ' SET '.$db->nameQuote('created').' = DATE_ADD('.$db->nameQuote('created').', INTERVAL '. $expiryPeriod . ' SECOND)';
		$query .= ' WHERE '.$db->nameQuote('token').' = ' . $db->Quote($token);
		$query .= ' AND '.$db->nameQuote('auth_key').' = ' . $db->Quote($authKey);
		$query .= ' AND '.$db->nameQuote('ip').' = ' . $db->Quote($ip);		

		$db->setQuery($query);
		$db->query();								

		if($db->getErrorNum()) 
		{
			JError::raiseError( 500, $db->stderr());
		}

		return $this;		
	}
	
	public function getUserByEmail($email)
	{
		$db    		=& $this->getDBO();
		
		$query	= 'SELECT * FROM '.$db->nameQuote('#__users');
		$query	.= ' WHERE '.$db->nameQuote('email').' = ' . $db->Quote($email);
		$db->setQuery($query);
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}		
		
		$result = $db->loadObject();		
		return $result;		
		
	}
	
	/**
	 * Return administrators emails
	 */	 	
	public function getSuperAdministratorEmail()
	{
		$db    		=& $this->getDBO();
		
		$query = 'SELECT '.$db->nameQuote('name').', '.$db->nameQuote('email').', '.$db->nameQuote('sendEmail')
				.' FROM '.$db->nameQuote('#__users')
				.' WHERE LOWER( '.$db->nameQuote('usertype').' ) = '.$db->Quote('super administrator');

		if(C_JOOMLA_15){
			$query		= 'SELECT ' . $db->nameQuote('name').', '.$db->nameQuote('email').', '.$db->nameQuote('sendEmail')
						. ' FROM ' . $db->nameQuote('#__users')
						. ' WHERE ' . $db->nameQuote( 'gid' ) . '=' . $db->Quote( 25 );
		} else {
			$query		= 'SELECT a.' . $db->nameQuote('name').', a.'.$db->nameQuote('email').', a.'.$db->nameQuote('sendEmail')
						. ' FROM ' . $db->nameQuote('#__users') . ' as a, '
						. $db->nameQuote('#__user_usergroup_map') . ' as b'
						. ' WHERE a.' . $db->nameQuote('id') . '= b.' . $db->nameQuote('user_id') 
						. ' AND b.' . $db->nameQuote( 'group_id' ) . '=' . $db->Quote( 8 ) ;
		}
		$db->setQuery( $query );
			
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}		
		
		$result = $db->loadObjectList();		
		return $result;		
		
	}
}
