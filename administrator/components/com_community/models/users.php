<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class CommunityModelUsers extends JModel
{
	/**
	 * Configuration data
	 * 
	 * @var object	JPagination object
	 **/	 	 	 
	var $_pagination;
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$mainframe	=& JFactory::getApplication();

		// Call the parents constructor
		parent::__construct();

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'com_community.users.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( 'com_community.users.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
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
	public function setImage(  $id , $path , $type = 'thumb' )
	{
		CError::assert( $id , '' , '!empty' , __FILE__ , __LINE__ );
		CError::assert( $path , '' , '!empty' , __FILE__ , __LINE__ );
		
		$db			=& $this->getDBO();
		
		// Fix the back quotes
		$path		= CString::str_ireplace( '\\' , '/' , $path );
		$type		= JString::strtolower( $type );
		
		// Test if the record exists.
		$query		= 'SELECT ' . $db->nameQuote( $type ) . ' FROM ' . $db->nameQuote( '#__community_users' )
					. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $id );
		
		$db->setQuery( $query );
		$oldFile	= $db->loadResult();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
	    }
	    
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
	    	$db->query( $query );
	    	
			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
		    }
		    
			// If old file is default_thumb or default, we should not remove it.
			// Need proper way to test it
			if(!Jstring::stristr( $oldFile , 'components/com_community/assets/default.jpg' ) && !Jstring::stristr( $oldFile , 'components/com_community/assets/default_thumb.jpg' ) )
			{
				// File exists, try to remove old files first.
				$oldFile	= CString::str_ireplace( '/' , DS , $oldFile );			
				JFile::delete($oldFile);	
			}
		}
	}
	
	/**
	 * Retrieves the JPagination object
	 *
	 * @return object	JPagination object	 	 
	 **/	 	
	public function &getPagination()
	{
		return $this->_pagination;
	}

	public function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
	}

	public function _buildQuery()
	{		
		$db			=& JFactory::getDBO();
		$category	= JRequest::getInt( 'category' , 0 );
		$condition	= '';
		
		if( $category != 0 )
		{
			$condition	= ' WHERE a.categoryid=' . $db->Quote( $category );
		}
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'block' ) . '=' . $db->Quote( '0' ) . ' '
				. 'ORDER BY ' . $db->nameQuote( 'name' );
					
		return $query;
	}
	
	public function getAllUsers( $useLimit = true , $useSearch = true )
	{
		if(empty($this->_data))
		{
			$db			=& JFactory::getDBO();
			$mainframe	=& JFactory::getApplication();
			
            $limit			= $this->getState('limit');
            $limitstart 	= $this->getState('limitstart');
			$search			= $mainframe->getUserStateFromRequest( "com_community.users.search", 'search', '', 'string' );
			$usertype		= $mainframe->getUserStateFromRequest( "com_community.users.usertype", 'usertype', 'joomla', 'string' );
			$profileType	= $mainframe->getUserStateFromRequest( "com_community.users.usertype", 'profiletype', '', 'int' );
			$ordering		= $mainframe->getUserStateFromRequest( "com_community.users.filter_order",		'filter_order',		'name',	'cmd' );
			$orderDirection	= $mainframe->getUserStateFromRequest( "com_community.users.filter_order_Dir",	'filter_order_Dir',	'',			'word' );

			$searchQuery	= '';
			$joinQuery		= '';
			$orderby = 'ORDER BY '. $ordering .' '. $orderDirection;
			
			if( !empty( $search ) && $useSearch )
			{
				$searchQuery	= 'WHERE name LIKE ' . $db->Quote( '%' . $search . '%' ) . ' '
								. 'OR username LIKE ' . $db->Quote( '%' . $search . '%' ); 
			}

			if( !empty( $usertype ) )
			{
				if( $usertype == 'facebook' )
				{
					$joinQuery		= 'INNER JOIN ' . $db->nameQuote( '#__community_connect_users' ) . ' AS b '
									. 'ON a.id=b.userid ';
				}
				
				if($usertype == 'joomla')
				{
					$joinQuery		= 'LEFT JOIN ' . $db->nameQuote( '#__community_connect_users' ) . ' AS b '
									. 'ON a.id = b.userid ';
					
					if( !empty( $search) )
						$searchQuery	.= ' AND b.userid IS NULL ';
					else
						$searchQuery	.= ' WHERE b.userid IS NULL ';
				}
			}
			
			if( !empty( $profileType ) )
			{
				$joinQuery	.= 'INNER JOIN ' . $db->nameQuote( '#__community_users' ) . ' AS c '
							. 'ON a.id = c.userid ';

				if( !empty( $search ) )
				{
					$searchQuery	.= ' AND b.profile_id=' . $db->Quote( $profileType ) . ' ';
				}
				else
				{
					$searchQuery	.= 'WHERE c.profile_id=' . $db->Quote( $profileType ) . ' ';		
				}
			}
			$query	= 'SELECT * FROM ' . $db->nameQuote( '#__users' ) . ' AS a '
					. $joinQuery
					. $searchQuery
					. $orderby;

			if( $useLimit )
			{
	            // Appy pagination
	            if ( empty($this->_pagination))
	            {
	                jimport('joomla.html.pagination');
	                $this->_pagination = new JPagination( $this->_getListCount( $query ) , $limitstart, $limit);
	            }

				$this->_data	= $this->_getList( $query , $this->getState('limitstart'), $this->getState('limit') );
			}
			else
			{
				$db->setQuery($query);
				$this->_data	= $db->loadObjectList();
			}
		}
		return $this->_data;
	}
	
	public function getUsers()
	{
		if(empty($this->_data))
		{

			$query = $this->_buildQuery( );

			$this->_data	= $this->_getList( $this->_buildQuery() , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	public function getCommunityUser()
	{
		$db		=& JFactory::getDBO();
		
		$query	= "SELECT * FROM " . $db->nameQuote( '#__community_users');
		
		$db->setQuery( $query );
		$result	= $db->loadObjectList();
		
		return $result;
	}	
	
	public function getAllCommunityUsers()
	{
		$db		=& JFactory::getDBO();
		
		$query	= "SELECT `userid` FROM " . $db->nameQuote( '#__community_users');
		
		$db->setQuery( $query );
		$result	= $db->loadObjectList();
		
		return $result;
	}

	/**
	 * Method to retrieve all user's id from the site.
	 */	 
	public function getSiteUsers( $limitstart , $limit )
	{
		$db		=& JFactory::getDBO();
	
		$query	= 'SELECT id FROM ' . $db->nameQuote( '#__users' ) . ' '
				. 'LIMIT ' . $limitstart . ',' . $limit;

		$db->setQuery( $query );
		
		$result	= $db->loadResult();

		return $result;
	}
	
	public function isLatestTable()
	{
		$fields	= $this->_getFields();

		if(!array_key_exists( 'friendcount' , $fields ) )
		{
			return false;
		}
		
		return true;
	}	
	
	public function _getFields( $table = '#__community_users' )
	{
		$result	= array();
		$db		=& JFactory::getDBO();
		
		$query	= 'SHOW FIELDS FROM ' . $db->nameQuote( $table );

		$db->setQuery( $query );
		
		$fields	= $db->loadObjectList();

		foreach( $fields as $field )
		{
			$result[ $field->Field ]	= preg_replace( '/[(0-9)]/' , '' , $field->Type );
		}

		return $result;
	}
	
	/**
	 *	Return connect type of specific user
	 **/	 
	public function getUserConnectType( $userId )
	{
		$db		=& JFactory::getDBO();
		
		$query	= 'SELECT `type` FROM ' . $db->nameQuote( '#__community_connect_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->quote( $userId );
		
		$db->setQuery( $query );
		
		$type	= $db->loadResult();
		
		if( !$type )
		{
			$type	= 'joomla';
		}
		
		return $type;
	}
}