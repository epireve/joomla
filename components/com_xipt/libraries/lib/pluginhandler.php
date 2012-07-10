<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
/**
 */

// no direct access
if(!defined('_JEXEC')) die('Restricted access');

// include files, as we are here from plugin
// so files might not be included for non-component events
require_once JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php';

//@TODO: Write language file
//@TODO : check ptypeid in session in registerProfile fn also

//This class should be only access from it's static object.
class XiptLibPluginhandler
{
	public $mySess ;
	public $app;


	function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->mySess    =  JFactory::getSession();
	}

	//if value exist in session then return ptype else return false
	function isPTypeExistInSession()
	{
		$aecExists 		  = XiptLibAec::isAecExists();
		$payplansExists	  = XiptLibPayplans::isPayplansExists();
		$subs_integrate   = XiptFactory::getSettings('subscription_integrate',0);
		$integrate_with   = XiptFactory::getSettings('integrate_with',0);
		
		//if JSPT is integrated with AEC
		if($aecExists && $subs_integrate && $integrate_with == 'aec')
		{
			$data  = XiptLibAec::getProfiletypeInfoFromAEC() ;
			return $data['profiletype'];
		}
		
		//if JSPT is integrated with Payplans
		if($payplansExists && $subs_integrate && $integrate_with == 'payplans')
		{
			$data  = XiptLibPayplans::getProfiletypeInfoFromPayplans();
			return $data['profiletype'];
		}
		
		if($this->mySess->has('SELECTED_PROFILETYPE_ID', 'XIPT') == false)
		    return 0;

		return $this->mySess->get('SELECTED_PROFILETYPE_ID', 0, 'XIPT');
	}

	/**
	 * Collect profileType from session,
	 * is session does not have profiletype, return default one
	 *
	 * @return int
	 */
	function getRegistrationPType()
	{
		//get ptype from session
		$selectedProfiletypeID = $this->isPTypeExistInSession();

		// pType exist in session
		if($selectedProfiletypeID)
			return $selectedProfiletypeID;

		//no pType in session, return default value
		$defaultProfiletypeID = XiptLibProfiletypes::getDefaultProfiletype();

		return $defaultProfiletypeID;
	}


	function setDataInSession($what,$value)
	{
		$this->mySess->set($what,$value, 'XIPT');
		return true;
	}

	function getDataInSession($what,$defaultValue)
	{
		if($this->mySess->has($what,'XIPT'))
			return $this->mySess->get($what,$defaultValue, 'XIPT');
		else
			return null;
	}
	
	function resetDataInSession($what)
	{
		$this->mySess->clear($what,'XIPT');
		return true;
	}
	
	function cleanRegistrationSession()
	{
	    $this->mySess->clear('SELECTED_PROFILETYPE_ID','XIPT');
	}

	//============================ Community Events=========================

	function onAjaxCall(&$func, &$args , &$response)
	{
		$callArray	= explode(',', $func);

		//perform Access checks
		$ajax = true;
		XiptAclHelper::performACLCheck($ajax, $callArray, $args);

		// If we come here means ACL Check was passed
		$controller	=	$callArray[0];
		$function	=	$callArray[1];
	
		switch($controller.'_'.$function)
		{
			//before creating new account, validate email and username
			case 'connect_ajaxCreateNewAccount' :
				return XiptHelperRegistration::ajaxCreateNewAccountFacebook($args,$response);

			case 'connect_ajaxCheckEmail' 	 :
				return XiptHelperRegistration::ajaxCheckEmailDuringFacebook($args,$response);

			case 'connect_ajaxCheckUsername' :
				return XiptHelperRegistration::ajaxCheckUsernameDuringFacebook($args,$response);
			
			case 'connect_ajaxShowNewUserForm' :
				return XiptHelperRegistration::ajaxShowNewUserForm($args,$response);
			
			case 'connect_ajaxUpdate' :
				return XiptHelperRegistration::ajaxUpdate($args,$response);

			// when controller == register
			case 'register_ajaxCheckEmail' 	 :

			case 'register_ajaxCheckUserName' :
					return XiptHelperRegistration::$function($args,$response);
					
			//when controller == apps
			case 'apps_ajaxAddApp' : 
			case 'apps_ajaxAdd' : 
					$my	= JFactory::getUser();

				    //XITODO : Remove it and add assert
				    if(0 == $my->id) return true;

		    		$profiletype = XiptLibProfiletypes::getUserData($my->id,'PROFILETYPE');
		    		return XiptLibApps::filterAjaxAddApps($args[0],$profiletype,$response);
		    		
			case 'profile_ajaxConfirmRemoveAvatar':
			//case 'profile_ajaxConfirmRemovePicture': 
			case 'profile_ajaxRemovePicture' : // This case use for Admin panel
						return XiptLibAvatar::removeAvatar($args, $response);
				
			default :
				// 	we do not want to interfere, go ahead JomSocial
					return true;
		}
	}
	
	/*Get decision to show ptype on registration session or not */
	function integrateRegistrationWithPType()
	{
	    $aecExists 		  = XiptLibAec::isAecExists();
		$payplansExists   = XiptLibPayplans::isPayplansExists();
		$subs_integrate   = XiptFactory::getSettings('subscription_integrate',0);
		$integrate_with   = XiptFactory::getSettings('integrate_with',0);

		$show_ptype_during_reg = XiptFactory::getSettings('show_ptype_during_reg', 0);
		$selectedProfiletypeID = $this->isPTypeExistInSession();

		if($show_ptype_during_reg){
			$link 	= "index.php?option=com_xipt&view=registration";
			
			// pType not selected : send to select profiletype
			if(!$selectedProfiletypeID){
				$this->app->redirect(XiptRoute::_("index.php?option=com_xipt&view=registration",false));
				return;
			}
		
			// pType already selected
			if($aecExists && $subs_integrate && $integrate_with == 'aec')
			{
			    $url = XiptRoute::_('index.php?option=com_acctexp&task=subscribe',false);
			    $msg = XiptLibAec::getAecMessage();
			    
			    if($msg != false)
			    {
			    	$link = '<a id="xipt_back_link" href='.$url.'>'. XiptText::_("CLICK_HERE").'</a>';
					$this->app->enqueueMessage($msg.' '.$link);
			    }
			    return;
			}
			else
			{
			    $url               = XiptRoute::_('index.php?option=com_xipt&view=registration&ptypeid='.$selectedProfiletypeID.'&reset=true',false);
			    $selectedpTypeName = XiptLibProfiletypes::getProfiletypeName($selectedProfiletypeID);
			    $msg 			   = sprintf(XiptText::_('CURRENT_PTYPE_AND_CHANGE_PTYPE_OPTION'),$selectedpTypeName);
				$link = '<a id="xipt_back_link" href='.$url.'>'. XiptText::_("CLICK_HERE").'</a>';
				$this->app->enqueueMessage($msg.' '.$link);
				return;
			}
		}
		else if($subs_integrate)
		{
			if($payplansExists && $integrate_with == 'payplans')
			{
			    $url = XiptRoute::_('index.php?option=com_payplans&view=plan',false);
			    $msg = XiptLibPayplans::getPayplansMessage();
		
				if($msg != false)
			    {
			    	$link = '<a id="xipt_back_link" href='.$url.'>'. XiptText::_("CLICK_HERE").'</a>';
					$this->app->enqueueMessage($msg.' '.$link);
			    }
			    return;
			}
		}
		
		
		// if pType is not set, collect default pType
		// set it in session
		if(!$selectedProfiletypeID) {
			$pType = $this->getRegistrationPType();
			$this->setDataInSession('SELECTED_PROFILETYPE_ID',$pType);
			return;
		}

		return;
	}

	/**
	 * Filter the fields, which are allowed to user.
	 * @param $userid
	 * @param $fields
	 * @return true
	 */
	function onProfileLoad(&$userid, &$fields, $from)
	{
		$none 			 			 = false;
		$args['triggerForEvents']    = 'onprofileload';
		$args['field']   			 =  &$fields      ;
		XiptAclHelper::performACLCheck($none,$none, $args);

		// field according to profiletype
		$view	= JRequest::getVar('view','');
		$task 	= JRequest::getVar('task','');
		//dont apply field privacy on admin approval plugin
		if(JRequest::getVar('option','') == 'com_user' && $task == 'activate')
		{
			$activation =JRequest::getVar('activation',null);
			if(!empty($activation)) 
				return true;
		}
		
		if($view === 'search' && $task === 'advancesearch')
		{
			$userid = JFactory::getUser()->id;
		}

	    XiptLibProfiletypes::filterCommunityFields($userid, $fields, $from);
	    return true;
	}
	
	function checkSetupRequired()
	{
		//XITODO : check is setup required 
		$mysess =  JFactory::getSession();
		if($mysess->has('requireSetupCleanUp') == true && $mysess->get('requireSetupCleanUp',false) == true)
 				return true;

//		//get all files required for setup
//		$setupNames = XiptSetupHelper::getOrder();
//		
//		foreach($setupNames as $setup)
//		{
//			//get object of class
//			$setupObject = XiptFactory::getSetupRule($setup);
//			
//			$setupObject->isRequired();
//		}	 

		$mysess->get('requireSetupCleanUp',false);
		return false;
	}
	

    function isPrivacyAllow($myProfileID=null)
     {
     	static $result = null;

     	if($result != null)
     		return $result;
    	
     	$modelObj 		= XiptFactory::getInstance('profiletypes','model');
     	if(empty($myProfileID))
    		$myProfileID	= XiptLibProfiletypes::getUserData(JFactory::getUser()->id, 'PROFILETYPE');
    	$privacyParams  = $modelObj->loadParams($myProfileID,'privacy');
    	
    	// jsPrivacyController == 1 means privacy handle by admin
    	$result = (1 == $privacyParams->get('jsPrivacyController')) ? true : false;
    	return $result;
    }

	static function hidePrivacyElements()
	{
		ob_start();
       
		/* hide all privacy seting from front-end
		 * label[for=privacy]:: Label of Privacy. 
		 * .js_PriContainerLarge:: class name that contain privacy select list. (In Photos)
		 * .privacy :: class which have privacy of all custom-profile fields 
		 */ 
			?>
			joms.jQuery(document).ready( function($){ 
				$("label[for=privacy],.js_PriContainerLarge,.privacy,.js_PriContainer").hide();		
				});	
			<?php 	
		$script = ob_get_contents();
        ob_clean();
        JFactory::getDocument()->addScriptDeclaration($script);
	}
	
	// hide JS Toolbar as per profiletype
	function hideJSToolbar($userid)
	{	
		// the user is admin, return true
		if(XiptHelperUtils::isAdmin($userid))
			return true;
		
		XiptHelperJSToolbar::getMenusToHide($userid);
	}
}

