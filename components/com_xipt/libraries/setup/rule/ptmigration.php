<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptSetupRulePtmigration extends XiptSetupBase
{
	function isApplicable()
	{
		$xipt_ProfileTypes = XiptLibProfiletypes::getProfiletypeArray(array('published'=>1));
			
		jimport('joomla.application.component.model');
		JModel::addIncludePath(JPATH_BASE.DS.'components'.DS.'com_community'.DS.'models');
		$multiprofileModel = JModel::getInstance( 'MultiProfile', 'CommunityModel' );
		$js_Profiletypes   = $multiprofileModel->getMultiProfiles();
		
		$config	     	   = CFactory::getConfig();
		
		if(empty($xipt_ProfileTypes) && $js_Profiletypes && $config->get('profile_multiprofile'))
			return true;
			
		return false;
	}
	
	function doApply()
	{
		$db = JFactory::getDBO();
		
		// truncate xipt_profilefields table
		$query_fields = 'TRUNCATE `#__xipt_profilefields`';
		$db->setQuery($query_fields);
		$db->query();
		
		// truncate xipt_users table
		$query_users = 'TRUNCATE `#__xipt_users`';
		$db->setQuery($query_users);
		$db->query();
		
		$config	  = CFactory::getConfig();
		$template = $config->get('template');
		
		$this->_migrateProfiletype($template);
		$this->_migrateProfilefields();
		$this->_migrateUsers($template);
		
		return XiptText::_('MIGRATION_DONE_SUCCESSFULLY_NOW_PLEASE_DISABLE_MULTIPROFILE_IN_JSCONFIG');
	}
	
	function getMessage()
	{
		$requiredSetup = array();
		if($this->isRequired())
		{
			$link = XiptRoute::_("index.php?option=com_xipt&view=setup&task=doApply&name=ptmigration",false);
			$requiredSetup['message']  = '<a href="'.$link.'">'.XiptText::_('PLEASE_CLICK_HERE_TO_MIGRATE_JS_MULTIPROFILES').'</a>';
			$requiredSetup['done']  = false;
		}
			
		return $requiredSetup;
	}
	
	function _migrateProfiletype($template)
	{
		//migrate all profiletypes from JomSocial to JSPT				
		$db = JFactory::getDBO();
		
		$xipt_pt    = ' INSERT INTO `#__xipt_profiletypes`
					(`id`, `name`, `ordering`, `published`, `avatar`, `approve`, `template`)
					SELECT `id`, `name`, `ordering`, `published`, `avatar`, `approvals`,' ." ' $template '  "
					. ' FROM `#__community_profiles` ';
		$db->setQuery($xipt_pt);
		$db->query();
	}
	
	function _migrateProfilefields()
	{
		//migrate all profile fields from JomSocial to JSPT
		$db = JFactory::getDBO();
		
		$xipt_pf    = ' INSERT INTO `#__xipt_profilefields`
						(`id`, `pid`, `fid`)
						SELECT `id`, `parent`, `field_id`
						FROM `#__community_profiles_fields`';
		$db->setQuery($xipt_pf);
		$db->query();
	}
	
	function _migrateUsers($template)
	{
		//migrate all users data from JomSocial to JSPT
		$db = JFactory::getDBO();
		$xipt_users    = ' INSERT INTO `#__xipt_users`
							(`userid`, `profiletype`, `template`)
							SELECT `userid`, `profile_id`, ' . " ' $template '  " .
							' FROM `#__community_users`';
		$db->setQuery($xipt_users);
		$db->query();
	}
}