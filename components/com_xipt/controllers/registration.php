<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptControllerRegistration extends XiptController {

	function __construct($config = array())
	{
		$this->mySess 	= JFactory::getSession();
		parent::__construct($config);
	}

    function display($save='', $pt = 0)
	{
		//trigger event
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'onBeforeProfileTypeSelection');

		// 	check for session
        //  if does not exist redirect user to community page
		XiptHelperProfiletypes::checkSessionForProfileType();

		//If not allowed to select PT for user then return
		if(XiptFactory::getSettings('show_ptype_during_reg') == 0){
			$selectedProfiletypeID = XiptLibProfiletypes::getDefaultProfiletype();
			XiptHelperProfiletypes::setProfileTypeInSession($selectedProfiletypeID);
		}

		// do some validation for visibility and publish of ptype
		if(JRequest::getVar('save', $save, 'POST') != ''){
			$selectedProfiletypeID = JRequest::getVar( 'profiletypes' , $pt , 'POST' );
			if(XiptLibProfiletypes::validateProfiletype($selectedProfiletypeID,array('published'=>1,'visible'=>1)) == false)
			{
				$msg  = XiptText::_('INVALID_PROFILE_TYPE_SELECTED');
				$link = XiptRoute::_('index.php?option=com_xipt&view=registration', false);
				$this->setRedirect($link, $msg);	
			}
			$dispatcher->trigger( 'onAfterProfileTypeSelection',array(&$selectedProfiletypeID));			
			return true;
		}

		// Get the view
		$this->getView()->display();
    }
}
