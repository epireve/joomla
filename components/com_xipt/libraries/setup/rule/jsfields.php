<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupRuleJsfields extends XiptSetupBase
{
	function isRequired()
	{
		$fields = self::_checkExistance();
		if(!$fields || count($fields)!= 2)
			return true;

		$tmpField = $fields[TEMPLATE_CUSTOM_FIELD_CODE];
		$ptField  = $fields[PROFILETYPE_CUSTOM_FIELD_CODE];

		return (!($tmpField->published && $ptField->published));
	}
	
	function doApply()
	{
		if(self::isRequired()== false)
			return XiptText::_("CUSTOM_FIELD_ALREADY_CREATED_AND_ENABLED_SUCCESSFULLY");
			
		$fields = self::_checkExistance();
			
		$tFieldCreated = true;
		if(isset($fields[TEMPLATE_CUSTOM_FIELD_CODE])===false)
			$tFieldCreated = self::createCustomField(TEMPLATE_CUSTOM_FIELD_CODE);
		
		$pFieldCreated  = true;
		if(isset($fields[PROFILETYPE_CUSTOM_FIELD_CODE])===false)
			$pFieldCreated  = self::createCustomField(PROFILETYPE_CUSTOM_FIELD_CODE);
			
		$fieldEnabled = self::_switchFieldState(1);

				
		if($pFieldCreated && $tFieldCreated && $fieldEnabled)
		return  XiptText::_("CUSTOM_FIELD_CREATED_AND_ENABLED_SUCCESSFULLY");
	
		return XiptText::_("CUSTOM_FIELDS_ARE_NOT_CREATED_OR_ENABLED");
	}
	
	function doRevert()
	{
		return self::_switchFieldState(0);
	}
	
	//check existance of custom fields profiletype and template
	function _checkExistance()
	{
		$query  = new XiptQuery();
		return $query->select('*')
					 ->from('#__community_fields')
					 ->where(" fieldcode = '".PROFILETYPE_CUSTOM_FIELD_CODE."' ", 'OR')
					 ->where(" fieldcode = '".TEMPLATE_CUSTOM_FIELD_CODE."' ")
					 ->dbLoadQuery()
					 ->loadObjectList('fieldcode');
	}
	
	//create custome field
	function createCustomField($what)
	{
		// Load the JTable Object.
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_community'.DS.'tables');
		$row	= JTable::getInstance( 'profiles' , 'CommunityTable' );
		$row->load(0);
		switch($what) {
			case PROFILETYPE_CUSTOM_FIELD_CODE:
						$data['type']			= PROFILETYPE_FIELD_TYPE_NAME;
						$data['name']			= 'Profiletype';
						$data['tips']			= 'Profiletype Of User';
						break;
			case TEMPLATE_CUSTOM_FIELD_CODE:
						$data['type']			= TEMPLATE_FIELD_TYPE_NAME;
						$data['name']			= 'Template';
						$data['tips']			= 'Template Of User';
						break;
			default :
						XiptError::assert(0);
						break;
		}
		
		$data['published'] =  1;								
		$data['fieldcode'] = $what;
		
		return $row->bind($data) && $row->store();		
	}
	
	
	
	//enable template & profiletype fields in community_fields table
	function _switchFieldState($state)
	{
		$query = new XiptQuery();
		return $query->update('#__community_fields')
					 ->set(" published = '$state' ")
					 ->where(" type = 'profiletypes' ", 'OR')
					 ->where(" type = 'templates' ")
					 ->dbLoadQuery()
					 ->query();					 
	}
	
	
	function getMessage()
	{
		$requiredSetup = array();
		if(self::isRequired())
		{
			$link = XiptRoute::_("index.php?option=com_xipt&view=setup&task=doApply&name=jsfields",false);
			$requiredSetup['message']  = '<a href="'.$link.'">'.XiptText::_("PLEASE_CLICK_HERE_TO_CREATE_AND_ENABLE_CUSTOM_FIELDS").'</a>';
			$requiredSetup['done']  = false;
		}
		
		else
		{
			$requiredSetup['message']  = XiptText::_("CUSTOM_FIELDS_EXIST");
			$requiredSetup['done']  = true;
		}
			
			
		return $requiredSetup;
	}
}