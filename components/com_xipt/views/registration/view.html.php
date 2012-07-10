<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// no direct access
if(!defined('_JEXEC')) die('Restricted access');

class XiptViewRegistration extends XiptView
{
	function display($tpl = null)
	{
		// if user is already register then return to different URL
		$userId = JFactory::getUser()->id;
		if($userId)
		{
			$redirectUrl	= XiptRoute::_('index.php?option=com_community&view=profile',false);
			$msg		    = XiptText::_('YOU_ARE_ALREADY_REGISTERED_NEED_NOT_TO_REGISTER_AGAIN');
			JFactory::getApplication()->redirect($redirectUrl,$msg);
		}

    	//   refine it, if empty will add default pType
    	$allProfileTypes 	= array();
	    $seletedPTypeID 	= JRequest::getVar('ptypeid','');

		//TODO : trigger an API Event to add something to templates, or modify $profiletypes array
		// e.g. : I want to patch description. with some extra information
		$filter = array('published'=>1,'visible'=>1);
	    $allProfileTypes = XiptLibProfiletypes::getProfiletypeArray($filter);


		$this->assign( 'allProfileTypes' , $allProfileTypes );
		$this->assign( 'selectedPT' , $seletedPTypeID );
		$params = XiptFactory::getSettings('', 0);
		$this->assign( 'showAsRadio' , $params->get('jspt_show_radio',true));

		parent::display( $tpl );
	}
}
