<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptHelperUtils
{
	function isAdmin($id)
	{
		$my	= JFactory::getUser($id);
		if (XIPT_JOOMLA_15)
			return ( $my->usertype == 'Super Administrator');
		else
			return ( $my->usertype == 'deprecated' || $my->usertype == 'Super Users');
	}
	
	function getFonts()
	{
		$path	= JPATH_ROOT  . DS . 'components' . DS . 'com_xipt' . DS . 'assets' . DS . 'fonts';
	
		jimport( 'joomla.filesystem.file' );
		$fonts = array();
		if( $handle = @opendir($path) )
		{
			while( false !== ( $file = readdir( $handle ) ) )
			{
				if( JFile::getExt($file) === 'ttf')
					//$fonts[JFile::stripExt($file)]	= JFile::stripExt($file);
					$fonts[] = JHTML::_('select.option', JFile::stripExt($file), JFile::stripExt($file));
			}
		}
		return $fonts;
	}	
	
	function getUrlpathFromFilePath($filepath)
	{
		$urlpath = preg_replace('#[/\\\\]+#', '/', $filepath);
		return $urlpath;
	}
	
	static function changePluginState($plugin, $state=0)
	{
		$query = new XiptQuery();
		if (XIPT_JOOMLA_15){
			$result= $query->update('#__plugins')
					 ->set(" `published` = $state ")
	          		 ->where(" `element` = '$plugin' ")
	          		 ->dbLoadQuery("","")
	          		 ->query();
		}
		else{
			$result= $query->update('#__extensions')
					 ->set(" `enabled` = $state ")
	          		 ->where(" `element` = '$plugin' ")
	          		 ->dbLoadQuery("","")
	          		 ->query();
		}		
	       return $result;
	}
	
	
	static function getPluginStatus($plugin)
	{
		$query = new XiptQuery();
		if (XIPT_JOOMLA_15){
			return $query->select('*')
					 ->from('#__plugins' )
					 ->where(" `element` = '$plugin' ")
					 ->dbLoadQuery("","")
	          		 ->loadObject();
		}
		else{
			return $query->select('*')
					 ->from('#__extensions' )
					 ->where(" `element` = '$plugin' ")
					 ->dbLoadQuery("","")
	          		 ->loadObject();
		}
	}
/**
* Change filePath according to machine.
*/
	function getRealPath($filepath, $seprator = DS)
	{ 
		return JPath::clean($filepath, $seprator);
	
	}
	
/**
	get field value of $userId accordimg to $fieldCode
*/
	public function getInfo($userId, $fieldCode )
	{
		// Run Query to return 1 value
		$db		= JFactory::getDBO();
		$query	= 'SELECT b.* FROM ' . $db->nameQuote( '#__community_fields' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__community_fields_values' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'field_id' ) . '=a.' . $db->nameQuote( 'id' ) . ' '
				. 'AND b.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__community_users' ) . ' AS c '
				. 'ON c.' . $db->nameQuote( 'userid' ) . '= b.' . $db->nameQuote( 'user_id' ) 
				. 'WHERE a.' . $db->nameQuote( 'fieldcode' ) . ' =' . $db->Quote( $fieldCode ); 
		
		$db->setQuery( $query );
		$result	= $db->loadObject();

		$field	= JTable::getInstance( 'FieldValue' , 'CTable' );
		$field->bind( $result );
		
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
		
		$config	= CFactory::getConfig();

		// @rule: Only trigger 3rd party apps whenever they override extendeduserinfo configs
		if( $config->getBool( 'extendeduserinfo' ) )
		{
			CFactory::load( 'libraries' , 'apps' );
			$apps	= CAppPlugins::getInstance();
			$apps->loadApplications();
			
			$params		= array();
			$params[]	= $fieldCode;
			$params[]	=& $field->value;
			
			$apps->triggerEvent( 'onGetUserInfo' , $params );
		}

		// Respect privacy settings.
		if(!XIPT_JOOMLA_15){
			$my	= CFactory::getUser();
			CFactory::load( 'libraries' , 'privacy' );
			if( !CPrivacy::isAccessAllowed( $my->id , $userId , 'custom' , $field->access ) ){
				return false;
			}
		}
		
		return $field->value;
	}
}
