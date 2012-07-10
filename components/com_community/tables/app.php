<?php
/**
 * @category	Tables
 * @package		JomSocial
 * @subpackage	Activities 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CTableApp extends JTable 
{
	public $id 			= null;
	public $userid 		= null;
  	public $apps 		= null;
  	public $ordering 	= null;
	public $position 	= null;
	public $params 		= null;
	public $privacy 	= null;
	
	private $_iscore 	= null;
	private $_params 	= null;
	
	/**
	 * Constructor
	 */	 	
	public function __construct( &$db ) {
		parent::__construct( '#__community_apps', 'id', $db );
	}
	
	/**
	 * Laod the app given the username and appname
	 * If it doesn't exist, check if it is a core apps and create an entry	 
	 */ 	 	
	public function loadUserApp($userid, $element)
	{
		$db	=& JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__community_apps' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'apps' ) . '=' . $db->Quote( $element )
				. '	AND ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $userid );

		//echo $strSQL;
		$db->setQuery($query);
		$result	= $db->loadObject();

		if($result){
			$this->bind($result);
		}
		else
		{
			// if it doen's exist, it could be core apps. Look into current
			// dispatcher
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
						&& ($plgObj->params->get('coreapp', 0) == 1) 
						&& method_exists($plgObj, 'onProfileDisplay' ))
					{
						// Ok, now we confirm that this plugin is valid
						// The app should be displayed, just that it is not in the
						// db yet. 
						// @todo: Get the position and privacy from backend
						$this->apps		= $element;
						$this->userid	= $userid;
						$this->position = $plgObj->params->get('position', 'content'); 
						$this->privacy 	= $plgObj->params->get('privacy', 0);
						$this->store();
					}
				}
			}
		}
		
		// If id still not loaded. Something is messed up and this object is no good
		return !empty($this->id);
	}
	
	
	/**
	 * Return the app position. If this is a core app, return the position
	 * as defined in the system parameter	 
	 */	 	
	public function getPosition()
	{
		return $this->position;
	}

	
	/**
	 * Return the params object for user parameters
	 */	 	
	public function getUserParams()
	{
		if(!$this->_params)
		{
			$this->_params	= new CParameter( $this->params );
		}
		return $this->_params;
	}
	
	/**
	 * Return plugin global params object
	 */	 	
	public function getPluginParams()
	{
	}
}
