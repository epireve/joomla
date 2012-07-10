<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptHelperJomsocial
{
	function getTemplatesList()
	{	
		$path	= JPATH_ROOT. DS . 'components' . DS . 'com_community' . DS . 'templates';
		
		return $templates = JFolder::folders($path);
	}
	
 	function getReturnURL()
    {
    	$regType = XiptFactory::getSettings('user_reg');
        
        if($regType === 'jomsocial')
           return XiPTRoute::_('index.php?option=com_community&view=register', false);
         
           if(!XIPT_JOOMLA_15){
           		return XiPTRoute::_('index.php?option=com_users&view=registration', false);
           }
        return XiPTRoute::_('index.php?option=com_user&view=register', false);
    }

    function isSupportedJS()
	{
		$inValid = array('1.1','1.2','1.5','1.6');
		$ver = self::get_js_version();		 
		return  !in_array(JString::substr($ver,0,3), $inValid);
 	}
 	
	function get_js_version()
	{	
		$CMP_PATH_ADMIN	= JPATH_ROOT . DS. 'administrator' .DS.'components' . DS . 'com_community';
	
		$parser		= JFactory::getXMLParser('Simple');
		$xml		= $CMP_PATH_ADMIN . DS . 'community.xml';
	
		$parser->loadFile( $xml );
	
		$doc		=& $parser->document;
		$element	=& $doc->getElementByPath( 'version' );
		$version	= $element->data();
	
		return $version;
	}
	
	function getFieldId($fieldcode)
	{
		static $results = array();
		
		$reset = XiptLibJomsocial::cleanStaticCache();
		if(isset($results[$fieldcode]) && $reset == false)
			return $results[$fieldcode]['id'];
		
		$query = new XiptQuery();
		$results = $query->select('*')
						 ->from('#__community_fields')
						 ->dbLoadQuery()
						 ->loadAssocList('fieldcode');
						 
		if(array_key_exists($fieldcode, $results))
			return $results[$fieldcode]['id'];		
	}
}
