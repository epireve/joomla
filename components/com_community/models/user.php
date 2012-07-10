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

class CommunityModelUser extends JCCModel
{
	var $_data = null;
	var $_userpref = array();

	/**
	 * Returns the total number of users registered for specific months.
	 * @param int	$month	Month in integer
	 * @return int  Total number of users.
	 *
	 **/
	public function getTotalRegisteredByMonth( $month )
	{
		$db		=& $this->getDBO();
		$start	= $month . '-01';
		$end	= $month . '-31';

		$query	= 'SELECT COUNT(1) FROM '.$db->nameQuote('#__users')
				. ' WHERE '.$db->nameQuote('registerDate').' >= ' . $db->Quote( $start ) 
				. ' AND '.$db->nameQuote('registerDate').' <= ' . $db->Quote( $end );
		$db->setQuery( $query );
		$total	= $db->loadResult();

		return $total;
	}
	
	/**
	 * Return the username given its userid
	 * @param int	userid	 
	 */	 	
	public function getUsername($id){
		$db	 = &$this->getDBO();
		$sql = 'SELECT '.$db->nameQuote('username').' FROM '.$db->nameQuote('#__users')
				.' WHERE '.$db->nameQuote('id').'=' . $db->Quote($id);
		$db->setQuery($sql);
		
		$result = $db->loadResult();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		return $result; 
	}
	
	/**
	 * Return the user fullname given its userid
	 * @param int	userid
	 */
	public function getUserFullname($id){
		$db	 = &$this->getDBO();
		$sql = 'SELECT '.$db->nameQuote('name').' FROM '.$db->nameQuote('#__users')
				.' WHERE '.$db->nameQuote('id').'=' .  $db->Quote($id);
		$db->setQuery($sql);

		$result = $db->loadResult();

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}
	
	/**
	 * Return the userid given its name
	 */	 	
	public function getUserId($username, $useRealName	= false){
		$db	 = &$this->getDBO();
		
		$param	= 'username';
		
		if($useRealName)
			$param = 'name';
			
		$sql = 'SELECT '.$db->nameQuote('id').' FROM '.$db->nameQuote('#__users')
				.' WHERE ' . $db->nameQuote($param) . '=' . $db->Quote($username);
			
		$db->setQuery($sql);
		$result = $db->loadResult();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		return $result; 
	}

	/**
	 * Return the user's email given its id
	 */	 	
	public function getUserEmail($id){
		$db	 = &$this->getDBO();
		
		$query = 'SELECT '.$db->nameQuote('email').' FROM '.$db->nameQuote('#__users')
				.' WHERE '.$db->nameQuote('id').'=' . $db->Quote($id);
		$db->setQuery($query);
		$result = $db->loadResult();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		return $result; 
	}
	
	public function getMembersCount()
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__users' )
				. ' WHERE ' . $db->nameQuote( 'block' ) . '=' . $db->Quote( 0 );
				
		$db->setQuery( $query );
		
		$result	= $db->loadResult();

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		return $result;
	}
	
	/**
	 * Return the basic user profile
	 */	 	
	public function getLatestMember($limit = 15)
	{
		if ($limit == 0) return array();
		$limit = ($limit < 0) ? 0 : $limit;
                
		$config		= CFactory::getConfig();

                $db	 = &$this->getDBO();

		$filterquery = '';
		$config		= CFactory::getConfig();
		if( !$config->get( 'privacy_show_admins') )
		{
		    $userModel		= CFactory::getModel( 'User' );
			$tmpAdmins		= $userModel->getSuperAdmins();

			$admins         = array();
			
			$filterquery  .= ' AND '.$db->nameQuote('id').' NOT IN(';
			for( $i = 0; $i < count($tmpAdmins);$i++ )
			{
			    $admin  = $tmpAdmins[ $i ];
			    $filterquery  .= $db->Quote( $admin->id );
			    $filterquery  .= $i < count($tmpAdmins) - 1 ? ',' : '';
			}
			$filterquery  .= ')';
		}
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__users' ) . ' ' 
				. ' WHERE ' . $db->nameQuote( 'block' ) . '=' . $db->Quote( 0 ) . ' '
				. $filterquery
				. ' ORDER BY ' . $db->nameQuote( 'registerDate' ) . ' '
				. ' DESC LIMIT ' . $limit;
                
		$db->setQuery( $query );
		
		$result = $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		
		$latestMembers = array();
		
		$uids = array();
		foreach($result as $m)
		{
			$uids[] = $m->id;
		}
		CFactory::loadUsers($uids);
		
		foreach( $result as $row )
		{
			$latestMembers[] = CFactory::getUser($row->id);
		}
		return $latestMembers;
	}
	
	public function getActiveMember($limit = 15)
	{
		$uid = array();
		$limit	= (int) $limit;
		$uid_str = "";
		$db	 = &$this->getDBO();		

		$filterquery = '';
		$config		= CFactory::getConfig();
		if( !$config->get( 'privacy_show_admins') )
		{
		    $userModel		= CFactory::getModel( 'User' );
			$tmpAdmins		= $userModel->getSuperAdmins();
			$admins         = array();
			
			$filterquery  .= ' AND b.'.$db->nameQuote('id').' NOT IN(';
			for( $i = 0; $i < count($tmpAdmins);$i++ )
			{
			    $admin  = $tmpAdmins[ $i ];
			    $filterquery  .= $db->Quote( $admin->id );
			    $filterquery  .= $i < count($tmpAdmins) - 1 ? ',' : '';
			}
			$filterquery  .= ')';
		}
		$query = " 	 SELECT 
							b.*,
							a.".$db->nameQuote('actor').", 
							COUNT(a.".$db->nameQuote('id').") AS ".$db->nameQuote('count')." 
					   FROM 
							".$db->nameQuote('#__community_activities')." a
				 INNER JOIN	".$db->nameQuote('#__users')." b
					  WHERE 
							a.".$db->nameQuote('app')." != ".$db->quote('groups')." AND
							b.".$db->nameQuote('block')." = ".$db->quote('0')." AND
							a.".$db->nameQuote('archived')." = ".$db->quote('0')." AND
							a.".$db->nameQuote('actor')." = b.".$db->nameQuote('id').
							$filterquery ."  
				   GROUP BY a.".$db->nameQuote('actor')."
				   ORDER BY ".$db->nameQuote('count')." DESC
				   LIMIT ".$limit;
		$db->setQuery( $query );
		$result = $db->loadObjectList();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		$latestMembers = array();
		
		foreach( $result as $row )
		{
			$latestMembers[] = CFactory::getUser($row->id);
		}
		return $latestMembers;
	}
	
	public function getPopularMember($limit = 15)
	{
		$uid = array();
		$uid_str = "";
		$db	 = &$this->getDBO();		
			
		$filterquery = '';
		$config		= CFactory::getConfig();
		if( !$config->get( 'privacy_show_admins') )
		{
		    $userModel		= CFactory::getModel( 'User' );
			$tmpAdmins		= $userModel->getSuperAdmins();
			$admins         = array();
			
			$filterquery  .= ' AND b.'.$db->nameQuote('id').' NOT IN(';
			for( $i = 0; $i < count($tmpAdmins);$i++ )
			{
			    $admin  = $tmpAdmins[ $i ];
			    $filterquery  .= $db->Quote( $admin->id );
			    $filterquery  .= $i < count($tmpAdmins) - 1 ? ',' : '';
			}
			$filterquery  .= ')';
		}

		$query = " 	 SELECT b.*
					   FROM 
							".$db->nameQuote('#__community_users')." a
				 INNER JOIN	".$db->nameQuote('#__users')." b
					  WHERE 
							b.".$db->nameQuote('block')." = ".$db->quote('0')." AND
							a.".$db->nameQuote('userid')." = b.".$db->nameQuote('id').
							$filterquery."  
				   ORDER BY a.".$db->nameQuote('view')." DESC
				   LIMIT ".$limit;
		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		$latestMembers = array();
		
		foreach( $result as $row )
		{
			$latestMembers[] = CFactory::getUser($row->id);
		}
		return $latestMembers;
	}
	
	// Return JDate object of last login date
	public function lastLogin($userid){
	}

	/**
	 * is the email exits 
	 */	 	
	public function userExistsbyEmail( $email ) {
		$db	= &$this->getDBO();
		$sql = 'SELECT count(*) from '.$db->nameQuote('#__users')
				.' WHERE '.$db->nameQuote('email').'= ' . $db->Quote($email);
			
			$db->setQuery($sql);
			$result = $db->loadResult();
			return $result;
	}
	
	/**
	 * Save user data. 
	 */	 	
	public function updateUser( $obj )
	{
		$db	= &$this->getDBO();
		return $db->updateObject( '#__community_users', $obj, 'userid');
	}
	
	public function removeProfilePicture( $id , $type = 'thumb' )
	{
		$db		= $this->getDBO();
		$type	= JString::strtolower( $type );
		
		// Test if the record exists.
		$query		= 'SELECT ' . $db->nameQuote( $type ) . ' FROM ' . $db->nameQuote( '#__community_users' )
					. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $id );
		
		$db->setQuery( $query );
		$oldFile	= $db->loadResult();
		
		$query	=   'UPDATE ' . $db->nameQuote( '#__community_users' ) . ' '
			    . 'SET ' . $db->nameQuote( $type ) . '=' . $db->Quote( '' ) . ' '
			    . 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $id );
		
		$db->setQuery( $query );
		$db->query( $query );
		    	
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	    
		// If old file is default_thumb or default, we should not remove it.
		// Need proper way to test it
		if(!JString::stristr( $oldFile , 'components/com_community/assets/default.jpg' ) && !JString::stristr( $oldFile , 'components/com_community/assets/default_thumb.jpg' ) )
		{
			// File exists, try to remove old files first.
			$oldFile	= CString::str_ireplace( '/' , DS , $oldFile );	

			if( JFile::exists( $oldFile ) )
			{	
				JFile::delete($oldFile);
			}
		}
		
		return true;
	}

	/**
	 *	Set the avatar for specific application. Caller must have a database table
	 *	that is named after the appType. E.g, users should have jos_community_users	 
	 *	
	 * @param	appType		Application type. ( users , groups )
	 * @param	path		The relative path to the avatars.
	 * @param	type		The type of Image, thumb or avatar.
	 *
	 **/	 	 
	public function setImage(  $id , $path , $type = 'thumb' , $removeOldImage = true )
	{
		CError::assert( $id , '' , '!empty' , __FILE__ , __LINE__ );
		CError::assert( $path , '' , '!empty' , __FILE__ , __LINE__ );
		
		$db			=& $this->getDBO();
		
		// Fix the back quotes
		$path	=   CString::str_ireplace( '\\' , '/' , $path );
		$type	=   JString::strtolower( $type );
		
		// Test if the record exists.
		$query	=   'SELECT ' . $db->nameQuote( $type ) . ' FROM ' . $db->nameQuote( '#__community_users' )
			    . 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $id );
		
		$db->setQuery( $query );
		$oldFile	= $db->loadResult();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	    
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();		
		$args 	= array();
		$args[]	= &$id;			// userid
		$args[]	= &$oldFile;	// old path
		$args[]	= &$path;		// new path
		$appsLib->triggerEvent( 'onProfileAvatarUpdate' , $args );
	    

		if( !$oldFile )
		{
	    	$query	= 'UPDATE ' . $db->nameQuote( '#__community_users' ) . ' '
	    			. 'SET ' . $db->nameQuote( $type ) . '=' . $db->Quote( $path ) . ' '
	    			. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $id );

	    	$db->setQuery( $query );
	    	$db->query( $query );

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
		    }
		}
		else
		{
	    	$query	= 'UPDATE ' . $db->nameQuote( '#__community_users' ) . ' '
	    			. 'SET ' . $db->nameQuote( $type ) . '=' . $db->Quote( $path ) . ' '
	    			. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $id );

	    	$db->setQuery( $query );
	    	$db->Query();

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
		    }
		    
			// If old file is default_thumb or default, we should not remove it.
			// Need proper way to test it
			if(!JString::stristr( $oldFile , 'components/com_community/assets/default.jpg' ) && !JString::stristr( $oldFile , 'components/com_community/assets/default_thumb.jpg' ) && $removeOldImage )
			{
				// File exists, try to remove old files first.
				$oldFile	= CString::str_ireplace( '/' , DS , $oldFile );	
				
				if( JFile::exists( $oldFile ) )
				{	
					JFile::delete($oldFile);
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * Return array of profile variables 
	 */	 	
	public function getProfile(){
	}
	
	/**
	 * Store user latitude/longitude data
	 */	 	
	public function storeLocation($userid, $lat = null, $long=null)
	{
		$db		=& $this->getDBO();
		$query 	= "UPDATE ". $db->nameQuote('#__community_users')
				  . " SET ". $db->nameQuote( 'latitude') ."=". $db->Quote( $lat ). " , "
				  . $db->nameQuote( 'longitude') ."=". $db->Quote( $long )
				  . " WHERE "
				  . $db->nameQuote( 'userid') ."=". $db->Quote( $userid );
		$db->setQuery($query);
		$db->query();
	}
	
	public function getOnlineUsers( $limit = 15 , $backendUsers = false )
	{
		$db		=& $this->getDBO();
		

		$query	= 'SELECT DISTINCT(a.'.$db->nameQuote('id').')'
				. ' FROM ' . $db->nameQuote( '#__users' ) . ' AS a '
				. ' INNER JOIN ' . $db->nameQuote( '#__session') . ' AS b '
				. ' ON a.'.$db->nameQuote('id').'=b.'.$db->nameQuote('userid')
				. ' WHERE a.'.$db->nameQuote('block').'=' . $db->Quote( '0' ) . ' ';
				
		if( !$backendUsers )
		{
			$query	.= 'AND '.$db->nameQuote('client_id').' != ' . $db->Quote( 1 );
		}
		
		$query	.= 'ORDER BY b.'.$db->nameQuote('time').' DESC '
				. 'LIMIT ' . $limit;

		$db->setQuery( $query );
		$result	= $db->loadObjectList();
				
		return $result;
	}

	public function getSuperAdmins()
	{
		$db		=& $this->getDBO();
		
		if(C_JOOMLA_15){
			$query		= 'SELECT * ' 
						. ' FROM ' . $db->nameQuote('#__users')
						. ' WHERE ' . $db->nameQuote( 'gid' ) . '=' . $db->Quote( 25 );
		} else {
			$query		= 'SELECT a.*' 
						. ' FROM ' . $db->nameQuote('#__users') . ' as a, '
						. $db->nameQuote('#__user_usergroup_map') . ' as b'
						. ' WHERE a.' . $db->nameQuote('id') . '= b.' . $db->nameQuote('user_id') 
						. ' AND b.' . $db->nameQuote( 'group_id' ) . '=' . $db->Quote( 8 ) ;
		}
		$db->setQuery( $query );
		$users	= $db->loadObjectList();
		return $users;
		
	}
	
	/**
	 * Return userid from the given email. If none is found, return 0
	 */	 	
	public function getUserFromEmail($email)
	{
		$db		=& $this->getDBO();
		$email = strtolower($email);
		
		$query = 'SELECT '.$db->nameQuote('id') .' FROM '.$db->nameQuote('#__users')
				.' WHERE LOWER( '.$db->nameQuote('email').' ) = '.  $db->Quote( $email );
				
		$db->setQuery( $query );
		$userid	= $db->loadResult();
		
		return $userid;
	}
	
	/**
	 * Get the list of users from the site.
	 * 
	 * @return	Array	An array of CUser objects.	 	 
	 **/	 	
	public function getUsers()
	{
		$db	= JFactory::getDBO();
		$query	= 'SELECT '.$db->nameQuote('id').' FROM '.$db->nameQuote('#__users');
		
		$db->setQuery( $query );
		$db->Query();
		
		$ids	= $db->loadResultArray();

		CFactory::loadUsers( $ids );
		
		$users	= array();
		foreach( $ids as $id )
		{
			$users[]	= CFactory::getUser( $id );	
		}
		
		return $users;
	}
	
	/**
	 * Return true if the user exist. Can't test using JFactory::getUser
	 * since it will throw a user-error	 
	 */	 	
	public function exists($userid)
	{
		$db	= JFactory::getDBO();
		$query	= 'SELECT COUNT(*) FROM '.$db->nameQuote('#__users') 
				. ' WHERE ' .$db->nameQuote('id').'=' . $db->Quote($userid);
		
		$db->setQuery( $query );
		$total	= $db->loadResult();
		
		return ($total > 0);
	}
	
	/**
	 * Return true if update successful
	 * since 2.4
	 */	 	
	public function setDefProfileToUser ($profileid, $default = COMMUNITY_DEFAULT_PROFILE)
	{
		$db	= JFactory::getDBO();
		$query	= 'UPDATE ' . $db->nameQuote('#__community_users') 
				. ' SET ' . $db->nameQuote('profile_id') . '=' . $db->Quote($default)
				. ' WHERE ' . $db->nameQuote('profile_id') . '=' . $db->Quote($profileid);
		
		$db->setQuery( $query );
		$db->Query();
		
		if($db->getErrorNum())
		{
			return false;
	    }
	    
	    return true;
	}
}
