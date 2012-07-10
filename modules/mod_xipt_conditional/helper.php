<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class XiptConditionalModHelper
{
	function displayModules($userId, $params, $attribs = array())
	{
		// if no user id return blank
		if(empty($userId) || $userId == '0' || $userId == 0)
			return "";
			
		//get profiletype id of user
		require_once (JPATH_BASE. DS.'components'.DS.'com_xipt'.DS.'api.xipt.php');
		$pID = XiptAPI::getUserProfiletype($userId);
		
		//get module params
		$modId  = $params->get('ximodule');
		$modPt  = $params->get('xiprofiletypes');
		$module = self::getModule($modId);
		
		//custom module name is given by the title field
		$file			= $module->module;
		$custom 		= substr( $file, 0, 4 ) == 'mod_' ?  0 : 1;
		$module->user  	= $custom;
		$module->name	= $custom ? $module->title : substr( $file, 4 );
		
		
		//check profiletype in module params, if its not an array, convert it
		if(!is_array($modPt))
			$modPt = array($modPt);
			
		//check user Pid exists in module params or not
		if(!in_array($pID, $modPt))
			return;
		
		// If style attributes are not given or set,
		// we enforce it to use the xhtml style
		// so the title will display correctly.
		if(!isset($attribs['style']))
			$attribs['style']	= 'xhtml';
			
		$contents 	= '';
		$document	= JFactory::getDocument();
		$renderer	= $document->loadRenderer( 'module' );
		$contents  .= $renderer->render($module, $attribs);
		
		return $contents;
	}
	
	function getModule($modId = 0)
	{
		$query = new XiptQuery();
		$query->select('*');
		$query->from('#__modules');
		$query->where("`id` = $modId");
			
		$module =$query->dbLoadQuery("","")->loadObject();			 	    	
		
		return $module;	
	}
}