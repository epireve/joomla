<?php
/**
 * @category	Model
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once ( JPATH_ROOT .DS.'components'.DS.'com_community'.DS.'models'.DS.'models.php');

// Deprecated since 1.8.x to support older modules / plugins
CFactory::load( 'tables' , 'app' );


class CommunityModelApps extends JCCModel
{

	  /**
	   * Items total
	   * @var integer
	   */
	  var $_total = null;
	
	  /**
	   * Pagination object
	   * @var object
	   */
	  var $_pagination = null;

	/**
	 *Constructor
	 *
	 */
 	 public function CommunityModelApps()
	 {
 	 	parent::JCCModel();

 	 	$mainframe = JFactory::getApplication();
 	 	
 	 	// Get pagination request variables
 	 	$limit		=10;
 	 	$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

		$this->setState('limit',$limit);
 	 	$this->setState('limitstart',$limitstart);
 	 }
	  	 	 	
	/**
	 * Gets the pagination Object
	 * 
	 *	return JPagination object	 	 
	 */
	public function &getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}


	/**
	 * Return the total number of applications that is installed on this Joomla site
	 * 
	 *	return int Total count of applications	 	 
	 **/	 	
	public function getTotal()
	{
		if (empty($this->_total))
		{
			$this->_total	= count( $this->getAvailableApps() );
		}
		return $this->_total;
	}
	
	// Return the title given its element name
	public function getAppTitle($appname){
		static $instances = array();
		
		if(empty($instances[$appname]))
		{
			$db	 = &$this->getDBO();
			$sql = 'SELECT ' . $db->nameQuote('name') 
					.' FROM ' . $db->nameQuote(PLUGIN_TABLE_NAME) 
					.' WHERE ' . $db->nameQuote('element') .'='. $db->Quote($appname)
                                        .' AND ' . $db->nameQuote('folder') .'='. $db->Quote('community');
			$db->setQuery($sql);
			$instances[$appname] = $db->loadResult();
		}
		
		return $instances[$appname];
	}
	
	
	public function setOrder( $userId, $newOrder )
	{
		$db	 = &$this->getDBO();

		foreach( $newOrder as $order )
		{
			// $order = 'appId,position,order'
			$order = explode(',', $order); 

			$query	= 'UPDATE ' . $db->nameQuote( '#__community_apps' ) . ' '
					. 'SET ' . $db->nameQuote( 'ordering' ) . '=' . $db->Quote( $order[2] ) . ', '
					.		$db->nameQuote( 'position' ) . '=' . $db->Quote( $order[1] ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $order[0] ) . ' '
					. 'AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userId );

			$db->setQuery( $query );
			$db->query();

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
			}
		}
		return $this;
	}
	
	/***
	 * Save the new application ordering in db. The caller should have called 
	 * 3 times in a row for all the 3 positions. Otherwise, old data might not
	 * be properly updated	 	 
	 */	 	
	public function setOrdering( $userId , $position, $orderings )
	{
		$db	 = &$this->getDBO();
		
		foreach( $orderings as $appId => $order )
		{
			$query	= 'UPDATE ' . $db->nameQuote( '#__community_apps' ) . ' '
					. 'SET ' . $db->nameQuote( 'ordering' ) . '=' . $db->Quote( $order ) . ', '
					.		$db->nameQuote( 'position' ) . '=' . $db->Quote( $position ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $appId ) . ' '
					. 'AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userId );

			$db->setQuery( $query );
			$db->query();
	
			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
			}
		}
		return $this;
	}
	
	/**
	 * Return the list of all user-apps, in proper order
	 * 
	 * @param	int		user id	
	 * @return	array	of objects	 
	 */	 	
	public function getUserApps($userid, $state = 0)
	{
		$db	 = &$this->getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote('element') 
					.' FROM '. $db->nameQuote(PLUGIN_TABLE_NAME)
					.' WHERE ' . $db->nameQuote(EXTENSION_ENABLE_COL_NAME).'=' . $db->Quote( 1 ) . ' '
					. 'AND ' . $db->nameQuote('folder') .'=' . $db->Quote( 'community' );
		$db->setQuery( $query );
		$elementsResult	= $db->loadResultArray();
		$elements		= "'" . implode( $elementsResult , "','" ) . "'";

		$query	= 'SELECT DISTINCT a.* FROM ' . $db->nameQuote('#__community_apps') .' AS a '
				. 'WHERE a.' . $db->nameQuote('userid') .'=' . $db->Quote( $userid ) . ' '
				. 'AND a.' . $db->nameQuote('apps') .'!=' . $db->Quote('news_feed')
				. 'AND a.' . $db->nameQuote('apps') .'!=' . $db->Quote('profile')
				. 'AND a.' . $db->nameQuote('apps') .'!=' . $db->Quote('friends');
		
		if( !empty( $elements ) )
		{
			$query	.= 'AND a.' . $db->nameQuote('apps') .' IN (' . $elements . ') ';
		}
				
		$query	.= 'ORDER BY a.' . $db->nameQuote('ordering');

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		// If no data yet, we load default apps
		// and add them to db
		if(empty($result))
		{
			$result = $this->getCoreApps();
			foreach($result as $row)
			{
				$row->userid = $userid;

				// We need to load the positions based on plugins default config
				$dispatcher = & CDispatcher::getInstanceStatic();
				$observers =& $dispatcher->getObservers();
				for( $i = 0; $i < count($observers); $i++ )
				{
					$plgObj = $observers[$i];
					if( is_object($plgObj) )
					{
						$plgObjWrapper = new CPluginWrapper($plgObj);	
						if( $plgObjWrapper->getPluginType() == 'community' 
							&& ($plgObj->params != null)
							&& ($plgObjWrapper->getPluginName()  == $row->apps))
						{
							$row->position	= $plgObj->params->get('position', 'content');
							$row->privacy 	= $plgObj->params->get('privacy', 0);
						}
					}
				}

				$db->insertObject('#__community_apps', $row);
				if($db->getErrorNum()) {
					JError::raiseError( 500, $db->stderr());
				}
			}
			
			// Reload the apps
			// @todo: potential duplicate code
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__community_apps')
				.' WHERE ' . $db->nameQuote('userid') .'=' . $db->Quote($userid)
				.' AND ' . $db->nameQuote('apps') .'!=' . $db->Quote('news_feed')
				.' AND ' . $db->nameQuote('apps') .'!=' . $db->Quote('profile')
				.' AND ' . $db->nameQuote('apps') .'!=' . $db->Quote('friends')
				.' ORDER BY ' . $db->nameQuote('ordering');
			$db->setQuery( $sql );
			$result = $db->loadObjectList();
			
			
			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
			}
		}
		
		// For 2.2 onwards, wall apps WILL NOT be displayed in the profile page, we need
		// to splice the array out!
		$offset = null;
		for($i = 0; $i < count($result); $i++)
		{
			if($result[$i]->apps == 'walls')
			{
				$offset = $i;
			}
		}
		
		if( !is_null($offset) )
		{
			array_splice($result, $offset, 1 );
		}

		return $result;
	}

	/**
	 * Get user privacy setting
	 */	 	
	public function getPrivacy($userid, $appname){
		static $privacy = array();
		
		if( empty($privacy[$userid]) )
		{
			// Preload all this user's privacy settings
			$db	 = &$this->getDBO();
			$sql = 'SELECT ' . $db->nameQuote('privacy') .', ' . $db->nameQuote('apps') 
					.' FROM ' . $db->nameQuote('#__community_apps') 
				  	.' WHERE ' . $db->nameQuote('userid') .'=' . $db->Quote($userid);
				
			$db->setQuery( $sql );
			$db->query();
			
			if($db->getErrorNum()) 
			{
				JError::raiseError( 500, $db->stderr());
			}
			
		    $result = $db->loadObjectList();
		    $privacy[$userid] = array();
		    
		    foreach($result as $row)
			{
				$privacy[$userid][$row->apps] = $row->privacy;
			}
	    }
	    
	    if(empty($privacy[$userid][$appname]))
	    	$privacy[$userid][$appname] = 0;
	    	
	    $result = $privacy[$userid][$appname];
	    
		return $result;	
	}

	
	/**
	 * Store user privacy setting
	 */	 	
	public function setPrivacy($userid, $appname, $val){
		$db	 = &$this->getDBO();
		$sql = 'UPDATE ' . $db->nameQuote('#__community_apps') .' SET ' . $db->nameQuote('privacy') .'=' . $db->Quote($val)
			.' WHERE ' . $db->nameQuote('userid').'=' . $db->Quote($userid) . ' AND ' . $db->nameQuote('apps') .'=' . $db->Quote($appname);
			
		$db->setQuery( $sql );
		$db->query();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		return $this;
	}
	
	/**
	 * Return the list of all available apps. 
	 * @todo: need to display apps that are only permitted for the user	 
	 * 	 
	 * @return	array	of objects	 
	 */
	public function getAvailableApps( $enableLimit = true )
	{
		$db		=& $this->getDBO();
	
		// This is bad, we load everything and slice them up
		$applications	= JPluginHelper::getPlugin('community');
		
		
		$apps	= array();
		
		// $applications are already filtered by the plugin helper.
		// where disabled applications are automatically filtered.
		for( $i = 0; $i < count( $applications ); $i++ )		
		{
			$row	= $applications[$i];
			$obj	= $this->getAppInfo( $row->name );
						
			//@rule: Application may be removed, so we need to test if the data really exists or not.
			if( isset( $obj->title ) )
			{
				$obj->title = JText::_($obj->title);
							
				if($obj->isApplication )
					$apps[]	= $obj;
			}
		}
		
		$totalApps = count($apps);
		
		if( $enableLimit )
		{
			$limitstart = $this->getState('limitstart');
			$limit		= $this->getState('limit');
			
			$apps = array_slice($apps, $limitstart, $limit);
		}
		
		// Appy pagination
		if(empty($this->_pagination))
		{
	 	    jimport('joomla.html.pagination');
	 	    $this->_pagination = new JPagination($totalApps, $this->getState('limitstart'), $this->getState('limit') );
	 	}
		return $apps;
	}
	
	public function getAppInfo($appname)
	{
		static $instances = array();
		
		if(empty($instances[$appname]))
		{
			$app = new stdClass();
			$parser =& JFactory::getXMLParser('Simple');
			
			$xmlPath	= CPluginHelper::getPluginPath('community',$appname)  . DS . $appname . '.xml';
			
			jimport('joomla.filesystem.file');
				
			if(!JFile::exists($xmlPath))
			{
				switch($appname)
				{
					case 'status':
						$app->title = 'Status';
						break;
					default:
						break;
				}
				return $app;
			}
			
			$parser->loadFile($xmlPath);
			$document =& $parser->document;
			
			// Get the title from db
			$app->title = JText::_($this->getAppTitle($appname));
			
			//$element =& $document->getElementByPath('name');
			//$app->title	= $element->data();
			
			$element =& $document->getElementByPath('description');
			$app->description = $element->data();
			
			$element =& $document->getElementByPath('author');
			$app->author = $element->data();
			
			$element =& $document->getElementByPath('version');
			$app->version = $element->data();
					
			$element =& $document->getElementByPath('creationdate');
			$app->creationDate = $element->data();
			
			// Determine whether the application is core application
			$params		= $this->getPluginParams( $this->getPluginId( $appname ) , null );
			$params		= new CParameter( $params );
			$app->coreapp		= $params->get( 'coreapp' );
			
			$element	=& $document->getElementByPath('isapplication');
			if($element)
				$app->isApplication	= ($element->data() == 'true');
			else
				$app->isApplication	= false;
	
			//$app->creationDate = 'test';
	
			$app->name = $appname;
			//$app->path = $appname;
			$instances[$appname] = $app;
		}
		
		return $instances[$appname];
	}
	
	/**
	 * Return list of core apps, as assigned by admin
	 */	 	
	public function getCoreApps()
	{
		$applications	= array();
		$enableLimit	= false;
		$availableApps	= $this->getAvailableApps( $enableLimit );
		
		for( $i = 0; $i < count( $availableApps ); $i++ )
		{
			$application	=& $availableApps[$i];
			
			$params			= $this->getPluginParams( $this->getPluginId( $application->name ) );
			$params			= new CParameter( $params );

			if($params->get( 'coreapp' ) )
			{
				$obj		= new stdClass();
				$obj->apps	= $application->name;
				$applications[]	= $obj;
			}
			
		}
		return $applications;
	}
	
	public function deleteApp($userid, $appid)
	{
		$db	 = &$this->getDBO();
		$sql = 'DELETE FROM ' . $db->nameQuote('#__community_apps') 
				.' WHERE ' . $db->nameQuote('userid') .'=' . $db->Quote($userid) 
				.' AND ' . $db->nameQuote('id') .'=' . $db->Quote($appid);
		$db->setQuery( $sql );
		$result = $db->query();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		return true;
	}
	
	/**
	 * Add new apps for this user
	 */	 	
	public function addApp($userid, $appName, $position='content')
	{
		$db	 = &$this->getDBO();
		
		// @todo: make sure this apps is not inserted yet
		$sql = 'SELECT count(*) FROM ' . $db->nameQuote('#__community_apps') 
				.' WHERE ' . $db->nameQuote('userid') .'=' . $db->Quote($userid) 
			 	.' AND ' . $db->nameQuote('apps') .'=' . $db->Quote($appName);
		$db->setQuery( $sql );
		$exist = $db->loadResult();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		
		if(!$exist){
			// Fix the position to the last spot
			// @todo: make sure this apps is not inserted yet
			$sql = 'SELECT count(*) FROM ' . $db->nameQuote('#__community_apps') 
					.' WHERE ' . $db->nameQuote('userid') .'=' . $db->Quote($userid) 
				 	. ' AND ' . $db->nameQuote('position').'=' . $db->Quote($position);
			$db->setQuery( $sql );
			$currentPost = $db->loadResult();
			
			
			$sql = 'INSERT INTO ' . $db->nameQuote('#__community_apps') .' SET ' . $db->nameQuote('userid') .'=' . $db->Quote($userid) . ", "
				 	. $db->nameQuote('apps') .'=' . $db->Quote($appName) . ", "
				 	. $db->nameQuote('position') .'=' . $db->Quote($position). ", "
				 	. $db->nameQuote('ordering') .'=' . $db->Quote($currentPost);

			$db->setQuery( $sql );
			$result = $db->query();
			
			if($db->getErrorNum()) {
				JError::raiseError( 500, $db->stderr());
			}
		}
		return $this;
	}

	/**
	 * Return parameter object of the given app
	 */		
	public function getUserAppParams( $id , $userId = null )
	{
		$db		=& $this->getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote( 'params' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__community_apps' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );

		if( !is_null( $userId ) )
		{
			$query	.= ' AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userId );
		}
		$db->setQuery($query);
		$result	= $db->loadResult();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		return $result;
	}
	
	/**
	 * Return parameter object of the given app
	 */	 	
	public function getPluginParams( $pluginId )
	{
		$db		= &$this->getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote( 'params' ) . ' '
				. 'FROM ' . $db->nameQuote( PLUGIN_TABLE_NAME ) . ' '
				. 'WHERE ' . $db->nameQuote( EXTENSION_ID_COL_NAME ) . '=' . $db->Quote( $pluginId );

		$db->setQuery( $query );

		$result = $db->loadResult();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		return $result;
	}
	
	/**
	 * Return default parameter of the given application from Joomla.
	 * 
	 * param	string	name	The element of the application
	 **/
// 	public function getDefaultParams( $element )
// 	{
// 		$db		=& $this->getDBO();
// 		
// 		$query	= 'SELECT ' . $db->nameQuote( 'params' ) . ' FROM ' 
// 				. $db->nameQuote( '#__plugins' ) . ' WHERE '
// 				. $db->nameQuote( 'element' )
// 	}
	/**
	 * Return parameter object of the given app
	 */	 	
	public function storeParams($id, $params){
		$db	 = &$this->getDBO();
		$sql = 'UPDATE ' . $db->nameQuote('#__community_apps') .' SET  ' . $db->nameQuote('params') .'=' . $db->Quote($params)
			  .' WHERE ' . $db->nameQuote('id') .'=' . $db->Quote($id);
			  
		$db->setQuery( $sql );
		$db->query();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		return true;
	}
	
	/**
	 * Return true if the user own the given appid
	 */	 	
	public function isOwned($userid, $appid)
	{
		$db	 = &$this->getDBO();
		
		$query	= 'SELECT COUNT(*) FROM '
				. $db->nameQuote( '#__community_apps' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $appid ) . ' '
				. 'AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userid );
		
		$db->setQuery( $query );
		$result = $db->loadResult();
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		return $result;
	}
	
	/**
	 * Return true if the user already enable
	 */	 	
	public function isAppUsed($userid, $apps){
		$db	 = &$this->getDBO();
		$sql = 'SELECT count(*) FROM ' . $db->nameQuote('#__community_apps') 
				.' WHERE ' . $db->nameQuote('apps') .'=' . $db->Quote($apps) 
				.' AND ' . $db->nameQuote('userid') .'=' . $db->Quote($userid);
		$db->setQuery( $sql );
		$result = ($db->loadResult() > 0) ? true : false;
		
		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		return $result;
	}
	
	/**
	 * Return the app name given the app id 
	 * @param	int		row id in __community_apps
	 */	 	
	public function getAppName($id){
		$db	 = &$this->getDBO();
		$sql = 'SELECT ' . $db->nameQuote('apps') 
				.' FROM ' . $db->nameQuote('#__community_apps') 
				.' WHERE ' . $db->nameQuote('id') .'=' . $db->Quote($id);
		$db->setQuery( $sql );
		$result = $db->loadResult();

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}
		return $result;
	}

	/**
	 * Return the application id in Joomla's plugin table.
	 *
	 * @param	string	Element of the plugin.
	 */	 
	public function getPluginId( $element )
	{
		$db		=& $this->getDBO();
		$query	= 'SELECT ' . $db->nameQuote( EXTENSION_ID_COL_NAME ) . ' ' 
				. 'FROM ' . $db->nameQuote( PLUGIN_TABLE_NAME ) . ' '
				. 'WHERE ' . $db->nameQuote( 'element' ) . '=' . $db->Quote( $element )
				. 'AND ' . $db->nameQuote('folder') . '=' . $db->Quote('community');

		$db->setQuery( $query );

		$result = $db->loadResult();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		return $result;
	}
	
	/**
	 * return the position of the given app.
	 */	 	
	public function getUserAppPosition($userid, $element)
	{
	
		$db		= &$this->getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'position' ) . ' FROM ' . $db->nameQuote( '#__community_apps' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'apps' ) . '=' . $db->Quote( $element )
				. '	AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userid );

		$db->setQuery( $query );

		$result = $db->loadResult();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		// if empty, then it is a core apps and its position is set by the admin
		if(empty($result))
			$result = 'content';
			
		return $result;
	}
	
	public function getUserApplicationId( $element , $userId = null )
	{
		$db		= &$this->getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__community_apps' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'apps' ) . '=' . $db->Quote( $element );

		if( !is_null($userId) )
			$query	.= ' AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userId );
		
		$db->setQuery( $query );

		$result = $db->loadResult();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		return $result;
	}
	
	public function checkObsoleteApp($obsoleteApp)
	{
		$db		= &$this->getDBO();
		$query	= 'SELECT ' . $db->nameQuote( EXTENSION_ENABLE_COL_NAME ) . ' FROM ' . $db->nameQuote( PLUGIN_TABLE_NAME ) . ' '
				. 'WHERE ' . $db->nameQuote( 'element' ) . '=' . $db->Quote( $obsoleteApp )		
				. 'AND ' . $db->nameQuote('folder') . '=' . $db->Quote('community');		
		$db->setQuery( $query );
		$result = $db->loadResult();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		return $result;
	}
	
	public function removeObsoleteApp($obsoleteApp)
	{
		$db		= &$this->getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__community_apps' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'apps' ) . '=' . $db->Quote( $obsoleteApp );		
		$db->setQuery( $query );
		$result = $db->query();
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		return $result;
	}

	public function hasConfig( $element )
	{
		jimport('joomla.filesystem.file' );
		
		return JFile::exists( CPluginHelper::getPluginPath('community',JString::trim( $element )) . DS . JString::trim( $element ) . DS . 'config.xml' ); 
	} 
}