<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

define('XIPT_VERSION','3.3.745');
define('XIPT_NOT_DEFINED','XIPT_NOT_DEFINED');
define('XIPT_NONE','XIPT_NONE');

if (!defined('XIPT_TEST_MODE'))
define('XIPT_TEST_MODE', false);


define('PROFILETYPE_FIELD_TYPE_NAME','profiletypes');
define('TEMPLATE_FIELD_TYPE_NAME','templates');
define('JOOMLA_USER_TYPE_NONE','None');

define('PROFILETYPE_FIELD_IN_USER_TABLE','profiletype');
define('TEMPLATE_FIELD_IN_USER_TABLE','template');

// we will refer our custom fields this way. this will be unique
define('TEMPLATE_CUSTOM_FIELD_CODE','XIPT_TEMPLATE');
define('PROFILETYPE_CUSTOM_FIELD_CODE','XIPT_PROFILETYPE');

//define watermark and thumb size
if(!defined('WATERMARK_HEIGHT')) define('WATERMARK_HEIGHT',40);
	
if(!defined('WATERMARK_WIDTH')) define('WATERMARK_WIDTH',40);
	
if(!defined('WATERMARK_HEIGHT_THUMB')) define('WATERMARK_HEIGHT_THUMB',16);
	
if(!defined('WATERMARK_WIDTH_THUMB')) define('WATERMARK_WIDTH_THUMB',16);

//define avatar sizes
if(!defined('AVATAR_HEIGHT')) define('AVATAR_HEIGHT',160);
	
if(!defined('AVATAR_WIDTH')) define('AVATAR_WIDTH',160);
	
if(!defined('AVATAR_HEIGHT_THUMB')) define('AVATAR_HEIGHT_THUMB',64);
	
if(!defined('AVATAR_WIDTH_THUMB')) define('AVATAR_WIDTH_THUMB',64);

if(!defined('REG_PROFILETYPE_AVATAR_HEIGHT')) define('REG_PROFILETYPE_AVATAR_HEIGHT',120);
	
if(!defined('REG_PROFILETYPE_AVATAR_WIDTH')) define('REG_PROFILETYPE_AVATAR_WIDTH',120);

if(!defined('PTYPE_POPUP_WINDOW_WIDTH_RADIO')) define('PTYPE_POPUP_WINDOW_WIDTH_RADIO',240);
	
if(!defined('PTYPE_POPUP_WINDOW_HEIGHT_RADIO')) define('PTYPE_POPUP_WINDOW_HEIGHT_RADIO',250);
	
if(!defined('PTYPE_POPUP_WINDOW_WIDTH_SELECT')) define('PTYPE_POPUP_WINDOW_WIDTH_SELECT',160);
	
if(!defined('PTYPE_POPUP_WINDOW_HEIGHT_SELECT')) define('PTYPE_POPUP_WINDOW_HEIGHT_SELECT',300);

if(!defined('SYNCUP_USER_LIMIT')) define('SYNCUP_USER_LIMIT',1000);
if(!defined('RESETALL_USER_LIMIT')) define('RESETALL_USER_LIMIT',100);

define('DEFAULT_AVATAR','components'.DS.'com_community'.DS.'assets'.DS.'user.png');
define('DEFAULT_AVATAR_THUMB','components'.DS.'com_community'.DS.'assets'.DS.'user_thumb.png');

define('DEFAULT_IMAGEWATERMRK','components'.DS.'com_xipt'.DS.'assets'.DS.'images'.DS.'demo_watermrk.png');
define('DEFAULT_IMAGEWATERMRK_THUMB','components'.DS.'com_xipt'.DS.'assets'.DS.'images'.DS.'demo_watermrk_thumb.png');

define('DEFAULT_DEMOAVATAR','components'.DS.'com_xipt'.DS.'assets'.DS.'images'.DS.'default_avatar.png');
define('DEFAULT_DEMOAVATAR_THUMB','components'.DS.'com_xipt'.DS.'assets'.DS.'images'.DS.'default_thumb.png');

//where to store profiletype avatars
define('PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH', 'images'.DS.'profiletype');
define('PROFILETYPE_AVATAR_STORAGE_PATH', JPATH_ROOT .DS. PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH);
define('USER_AVATAR_BACKUP', JPATH_ROOT .DS. PROFILETYPE_AVATAR_STORAGE_REFERENCE_PATH.DS.'useravatar');

if(JFolder::exists(PROFILETYPE_AVATAR_STORAGE_PATH)==false
		&& JFolder::create(PROFILETYPE_AVATAR_STORAGE_PATH)===false){
	XiptError::raiseError("XIPT-ERROR","Folder [".PROFILETYPE_AVATAR_STORAGE_PATH."] does not exist. Even we are not able to create it. Please check file permission.");
	return false;
}

//if(JFolder::exists(PROFILETYPE_AVATAR_STORAGE_PATH))
//	chmod(PROFILETYPE_AVATAR_STORAGE_PATH, 0755);

if(JFolder::exists(USER_AVATAR_BACKUP)==false
		&& JFolder::create(USER_AVATAR_BACKUP)===false){
	XiptError::raiseError("XIPT-ERROR","Folder [".USER_AVATAR_BACKUP."] does not exist. Even we are not able to create it. Please check file permission.");
	return false;
}

//if(JFolder::exists(USER_AVATAR_BACKUP))
//	chmod(USER_AVATAR_BACKUP, 0755);

// define constants for category of Profile Fields
define('PROFILE_FIELD_CATEGORY_ALLOWED',0);
define('PROFILE_FIELD_CATEGORY_REQUIRED',1);
define('PROFILE_FIELD_CATEGORY_VISIBLE',2);
define('PROFILE_FIELD_CATEGORY_EDITABLE_AFTER_REG',3);
define('PROFILE_FIELD_CATEGORY_EDITABLE_DURING_REG',4);
define('PROFILE_FIELD_CATEGORY_ADVANCE_SEARCHABLE',5);


//define constants for blocking display application according to owner,visitor and both
define('BLOCK_DISPLAY_APP_OF_OWNER',0);
define('BLOCK_DISPLAY_APP_OF_VISITOR',1);
define('BLOCK_DISPLAY_APP_OF_BOTH',2);

//define some constant
define('ALL',-1); //required in admin in ACL rules only ,
// it's not -1 at all places , we have used this as 0

define('XIPT_ADMIN_PATH_VIEWS',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xipt'.DS.'views');
define('XIPT_ADMIN_PATH_CONTROLLERS',JPATH_ADMINISTRATOR.DS.'components'.DS.'com_xipt'.DS.'controllers');

define('XIPT_FRONT_PATH_CONTROLLERS',JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'controllers');
define('XIPT_FRONT_PATH_VIEWS', JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'views');
define('XIPT_FRONT_PATH_LIBRARY',JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'libraries');
define('XIPT_FRONT_PATH_HELPER', JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'helpers');
define('XIPT_FRONT_PATH_ASSETS',JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'assets');
define('XIPT_FRONT_PATH_LIBRARY_BASE',XIPT_FRONT_PATH_LIBRARY.DS.'base');
define('XIPT_FRONT_PATH_LIBRARY_LIB',XIPT_FRONT_PATH_LIBRARY.DS.'lib');
define('XIPT_FRONT_PATH_LIBRARY_ACL',XIPT_FRONT_PATH_LIBRARY.DS.'acl');
define('XIPT_FRONT_PATH_LIBRARY_SETUP',XIPT_FRONT_PATH_LIBRARY.DS.'setup');
define('XIPT_FRONT_PATH_ELEMENTS',JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'elements');

// define constant for privacy value
define('XI_PRIVACY_PUBLIC',10);
define('XI_PRIVACY_MEMBERS',20);
define('XI_PRIVACY_FRIENDS',30);

define('XIPT_PROFILETYPE_ALL',0);
define('XIPT_PROFILETYPE_NONE',-1);

// define constant for none group
define('NONE', 0);

// define contants for type of setup rule
define('XIPT_SETUP_ERROR','error');
define('XIPT_SETUP_WARNING','warning');
$version = new JVersion();
define('XIPT_JOOMLA_16',($version->RELEASE === '1.6'));
define('XIPT_JOOMLA_15',($version->RELEASE === '1.5'));
if (XIPT_JOOMLA_15){
define('XIPT_JOOMLA_EXT_ID','id');
define('XIPT_JOOMLA_MENU_COMP_ID','componentid');
}
else{
define('XIPT_JOOMLA_EXT_ID','extension_id');
define('XIPT_JOOMLA_MENU_COMP_ID','component_id');
}
