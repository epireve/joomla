<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
jimport('joomla.filesystem.file');

if(!JFile::exists(JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php'))
 	return false;

$includeXipt=require_once (JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'includes.php');

if($includeXipt === false)
	return false;

class plgSystemxipt_system extends JPlugin
{
	var $_debugMode = 1;
	var $_eventPreText = 'event_';
	var $_name	= 'xipt_system';
	private $_pluginHandler;

	function __construct( $subject, $params )
	{
		parent::__construct( $subject, $params );
		$this->_pluginHandler = XiptFactory::getPluginHandler();
	}


	function onAfterRoute()
	{
		$app = JFactory::getApplication();

		// get option, view and task
		$option 	= JRequest::getVar('option','BLANK');
		$view 		= JRequest::getVar('view','BLANK');
		$task 		= JRequest::getVar('task','BLANK');
		$component	= JRequest::getVar('component','BLANK');
		$jsconfig	= JRequest::getVar('jsconfiguration','BLANK');

		if($app->isAdmin() && $option == 'com_community' && $view == 'configuration' && $jsconfig == 'privacy')
		{
 			$this->_addResetPrivacyScript();
			return true;
 		}
 		
		if($app->isAdmin()){
 			return false;
 		}

 		if($option === 'com_community')
 		{
 			$userid = JFactory::getUser()->id;
 			$this->_pluginHandler->hideJSToolbar($userid);
 		}
 		
		/* When XiPT is integrated with subscription method and user does not pay or subscribe any plan,
         * till then XiPT apply default profile-type.
        */
        if($option == 'com_community' && $task == 'registerUpdateProfile' && ($view == 'register' || $view == 'registration'))
        {
        	$subscription = XiptFactory::getSettings('subscription_integrate', 0);
            if($subscription){
            	// Change post data (only profile-type and template field).
                $profiletypeId = XiptHelperJomsocial::getFieldId(PROFILETYPE_CUSTOM_FIELD_CODE);
                $templateId    = XiptHelperJomsocial::getFieldId(TEMPLATE_CUSTOM_FIELD_CODE);
                
                //set current PT in other variable for further use
                $defaultPT     = XiptLibProfiletypes::getDefaultProfiletype();
                $this->_pluginHandler->setDataInSession('sessionpt', JRequest::getVar("field$profiletypeId", $defaultPT));
                
                JRequest::setVar("field$profiletypeId", $defaultPT);
                JRequest::setVar("field$templateId", XiptLibProfiletypes::getProfiletypeData($defaultPT, 'template'));
            }
        }
 		       
		// perform all acl check from here
		XiptAclHelper::performACLCheck(false, false, false);

		//do routine works
		$eventName = $this->_eventPreText.strtolower($option).'_'.strtolower($view).'_'.strtolower($task);
		//call defined event to handle work
		$exist = method_exists($this,$eventName);
		if($exist)
			$this->$eventName();

		return false;
	}

	/**
	 * This function will store user's registration information
	 * in the tables, when User object is created
	 * @param $cuser
	 * @return true
	 */
	function onAfterStoreUser($properties,$isNew,$result,$error)
	{
		return $this->onUserAfterSave($properties,$isNew,$result,$error);
	}
	/**
	 * 
	 * @param unknown_type $properties : Holds the new user data.
	 * @param unknown_type $isNew: True if a new user is stored.
	 * @param unknown_type $result: True if user was succesfully stored in the database.
	 * @param unknown_type $error : Error Message
	 */
	function onUserAfterSave($properties,$isNew,$result,$error)
	{
		// we only store new users
		if($isNew == false || $result == false || $error == true) {
			$this->_pluginHandler->cleanRegistrationSession();
			return true;
		}

		$subscription = XiptFactory::getSettings('subscription_integrate', 0);
		if($subscription)
			$profiletypeID = XiptFactory::getSettings('defaultProfiletypeID',0);
		else
			$profiletypeID = $this->_pluginHandler->getRegistrationPType();
		// need to set everything
		XiptLibProfiletypes::updateUserProfiletypeData($properties['id'], $profiletypeID,'', 'ALL');

		//clean the session
		$this->_pluginHandler->cleanRegistrationSession();
		return true;
	}
	
	/**
	 * for Joomla 1.5
	 * @param unknown_type $properties
	 * @param unknown_type $result
	 * @param unknown_type $error
	 */
	function onAfterDeleteUser($properties,$result,$error)
	{
		if($result == false || $error == true){
			return true;
		}

		return XiptFactory::getInstance('users','model')->delete($properties['id']);
	}
	
	/**
	 * fo Joomla 1.6
	 * @param $properties
	 * @param $result
	 * @param $error
	 */
	function onUserAfterDelete($properties,$result,$error)
	{
		if($result == false || $error == true){
			return true;
		}

		return XiptFactory::getInstance('users','model')->delete($properties['id']);
	}

	// this is trigerred on registraion page of xipt
	function onBeforeProfileTypeSelection()
	{
		// if user comes from genaral registration link then return
		$ptypeid = JRequest::getVar('ptypeid',0);

		// if user comes from a direct link (with profile type selected)
		// the reset will be false or does not exist
		// if user comes for selecting profile type again then reset is true
		$reset = JRequest::getVar('reset',false);

		if($ptypeid == 0 || $reset == "true")
			return true;

		if(!XiptLibProfiletypes::validateProfiletype($ptypeid))
			return true;

		XiptHelperProfiletypes::setProfileTypeInSession($ptypeid);
		return true;
	}

	// this is trigerred on after post on registration page of xipt
	function onAfterProfileTypeSelection($ptypeid)
	{
		// set the profile type in session
		return XiptHelperProfiletypes::setProfileTypeInSession($ptypeid);
	}

	/*
	 * Events generated from the onAfterRoute
	 */

	//BLANK means task should be empty
	function event_com_community_register_blank()
	{
		return $this->_pluginHandler->integrateRegistrationWithPType();
	}

	//this event is for JS register menu link, where task is also defined
	function event_com_community_register_register()
	{
		return $this->_pluginHandler->integrateRegistrationWithPType();
	}

	function event_com_user_register_blank()
	{
	    return $this->_pluginHandler->integrateRegistrationWithPType();
	}
	
	/**
	 * for Joomla 1.6 
	 * replace (option) user to users and (view) register to registration.
	 */
	function event_com_users_registration_blank()
	{
	    return $this->event_com_user_register_blank();
	}

	function event_com_community_profile_removepicture(){
		return $this->event_com_community_profile_removeavatar();		
	}
	
	function event_com_community_profile_removeavatar()
	{
		return XiptLibAvatar::removeProfilePicture();
	}
	function event_com_community_profile_blank()
	{
		// Hide Privacy at Profile Page
		if($this->_pluginHandler->isPrivacyAllow()){
			$this->_pluginHandler->hidePrivacyElements();
		}
		
		if(!$this->_pluginHandler->getDataInSession('FROM_FACEBOOK',false))
			return true;

		// reset the session data of FROM_FACEBOOK
		$this->_pluginHandler->resetDataInSession('FROM_FACEBOOK');

		$subs_integrate 	= XiptFactory::getSettings('subscription_integrate', 0);
		$integrate_with		= XiptFactory::getSettings('integrate_with', 0);
		
		//when integrated with AEC, redirect to AEC
		if($subs_integrate == true && $integrate_with == 'aec')
			JFactory::getApplication()->redirect(XiptRoute::_('index.php?option=com_acctexp&task=subscribe',false));
			
		//when integrated with Payplans, redirect to Payplans
		if($subs_integrate == true && $integrate_with == 'payplans')
			JFactory::getApplication()->redirect(XiptRoute::_('index.php?option=com_payplans&view=plan',false));	
		
		return true;
	}

	/* get the plan id when the direct link of AEC are used */
	function event_com_acctexp_blank_subscribe()
	{
		$usage  = JRequest::getVar( 'usage', 0, 'REQUEST');
		//XiptError::assert($usage);
		$this->_pluginHandler->setDataInSession('AEC_REG_PLANID', $usage);
	}

	/* get the plan id when the direct link of Payplans are used */
	function onPayplansPlanAfterSelection($planid)
	{
		$this->_pluginHandler->setDataInSession('PAYPLANS_REG_PLANID', $planid);
	}
	
	// we are on xipt registration page
	function event_com_xipt_registration_blank()
	{
		$app	 			= JFactory::getApplication();
	    $subs_integrate     = XiptFactory::getSettings('subscription_integrate', 0);
		$integrate_with     = XiptFactory::getSettings('integrate_with', 0);
		
		if(!$subs_integrate)
			return false;
		
		//when integrated with AEC, set PT in session as per plan.	
		if($integrate_with == 'aec')
		{
			if(!XiptLibAec::isAecExists())
				return false;
				
		    // find selected profiletype from AEC
		    $aecData = XiptLibAec::getProfiletypeInfoFromAEC();
	
		    // as user want to integrate the AEC so a plan must be selected
	        // send user to profiletype selection page
		    if($aecData['planSelected'] == false)
		        $app->redirect(XiptRoute::_('index.php?option=com_acctexp&task=subscribe',false),XiptText::_('PLEASE_SELECT_AEC_PLAN_IT_IS_RQUIRED'));
	
		    // set selected profiletype in session
		    $this->_pluginHandler->mySess->set('SELECTED_PROFILETYPE_ID',$aecData['profiletype'], 'XIPT');
		}
		
		//when integrated with Payplans, no need to set PT in session
		//payplans itself set PT in session
		if($integrate_with == 'payplans')
		{
			if(!XiptLibPayplans::isPayplansExists())
				return false;
				
		    // find selected profiletype from Payplans
		    $payplansData = XiptLibPayplans::getProfiletypeInfoFromPayplans();
	
		    // as user want to integrate the Payplans so a plan must be selected
	        // send user to profiletype selection page
		    if($payplansData['planSelected'] == false)
		        $app->redirect(XiptRoute::_('index.php?option=com_payplans&view=plan',false),XiptText::_('PLEASE_SELECT_AEC_PLAN_IT_IS_RQUIRED'));
		}
		
	    $app->redirect(XiptHelperJomsocial::getReturnURL());

	    return true;
	}
	/**
	 * Hide Privacy At user ragistration time
	 */
	function event_com_community_register_registerprofile() {
		$pId= JFactory::getSession()->get("SELECTED_PROFILETYPE_ID", null, "XIPT");
		if($this->_pluginHandler->isPrivacyAllow($pId)){
			$this->_pluginHandler->hidePrivacyElements();
		}
	}
	// Hide Privacy at Home Page
	function event_com_community_frontpage_blank(){
		if($this->_pluginHandler->isPrivacyAllow()){
			$this->_pluginHandler->hidePrivacyElements();
		}
	}

	function onAfterDispatch()
    {
    	$app = JFactory::getApplication();
		
 		if($app->isAdmin() ){
 			if($this->_pluginHandler->checkSetupRequired())
 		 			$app->enqueueMessage(XiptText::_('JSPT_SETUP_SCREEN_IS_NOT_CLEAN_PLEASE_CLEAN_IT_STEP_BY_STEP'), 'error');
			return true;
		}

        // get option, view and task
        $option = JRequest::getVar('option');
        $view   = JRequest::getVar('view');
        $task   = JRequest::getVar('task');

        // Hide Privacy menus
        if($option == 'com_community'){
        	  self::_hidePrivacyMenus();
        }

        if($option != 'com_community' || $view != 'search' || $task != 'advancesearch')
            return true;

        $allTypes = XiptLibProfiletypes::getProfiletypeArray(array('published'=>1, 'visible'=>1));

        if (!$allTypes)
			return false;
		// when we are getting Html of select list(for Profile-Types) 
		//then  don't addd "\n" at end of line
		if(!XIPT_JOOMLA_15){
			JHtml::$formatOptions= array_merge(
								   JHtml::$formatOptions,
									array('format.eol' => ""));
		}

		$profileType = JHTML::_('select.genericlist',  $allTypes, 'profiletypes', 'class="inputbox"', 'id', 'name');

        ob_start();
        $this->_addXiptSearchScript($profileType);

        $content = ob_get_contents();
        ob_clean();
        $doc = JFactory::getDocument();
		if(XIPT_JOOMLA_15)
        	JHTML::script('jquery1.4.2.js','components/com_xipt/assets/js/', true);
        else
        	JHTML::script('components/com_xipt/assets/js/jquery1.4.2.js');
        
        $doc->addCustomTag( '<script type="text/javascript">jQuery.noConflict();</script>' );

        $doc->addScriptDeclaration($content);
        return true;
    }
    
    
    function _hidePrivacyMenus()
    {
    	if(false == $this->_pluginHandler->isPrivacyAllow()){
    		return;
    	}
    	//get Privacy menu
    	if(XIPT_JOOMLA_15){
			$menus = JSite::getMenu();
    	}
		else{
			//$menus = JApplication::getMenu('site');
			//XiTODO::Improve this code;
			include_once JPATH_ROOT.DS.'administrator/components/com_menus/models/items.php';
			$menus 		= new MenusModelItems;
		}

		$menusItems = $menus->getItems('menutype',CFactory::getConfig()->get( 'toolbar_menutype'));
		if(empty($menusItems))
			return ;

		foreach($menusItems as $menu)
		{
			if(JString::stristr($menu->link,'index.php?option=com_community&view=profile&task=privacy')){
				$hideMenu= XiptRoute::_("$menu->link");
				break;
			}
		}
		if(empty($hideMenu))
			return ;
			
    	ob_start();
        ?>
        joms.jQuery(document).ready(function(){	
			var menuUrl = "<?php echo $hideMenu; ?>".replace(/\&amp\;/gi, "&");
			joms.jQuery("a[href='" + menuUrl + "']").hide();	
		});	
        <?php 
        $content = ob_get_contents();
        ob_clean();
		JFactory::getDocument()->addScriptDeclaration($content);   	
    }

	// $userInfo ia an array and contains contains
	// userid
	// oldPtype
	// & newPtype
	function onBeforeProfileTypeChange($userInfo)
	{
		return false;
	}

	function onAfterProfileTypeChange($newPtype, $result)
	{
		return false;
	}

	function _addXiptSearchScript($profileType)
	{
	   //CAssets::attach(JURI::root().'components/com_community/assets/joms.jquery', 'js');
		?>
		$(document).ready(function(){
			 // find all select list object
			 var sel = document.getElementsByTagName("select");
			 var selLength =  sel.length;

		     for (i=0 ; i <= selLength; i++){
		        joms.jQuery.xipt.getProfileTypesFields(joms.jQuery, joms.jQuery(sel[i]).attr("id"));
		        }
		    });

			joms.jQuery(function($){
			// change on select list then attach our HTML
			$("select[id^='field']").live('change', function(){
				joms.jQuery.xipt.getProfileTypesFields($, $(this).attr("id"));
				});

			$("#profiletypes").live('change', function(){

			 		//set profileType value in  hidden textbox
					profileFieldValue= $(this).val();
					parentId = $(this).prev().attr("id");
					$("#"+ $("#" + parentId + ":first-child" ).attr("id")).val(profileFieldValue);
				});

			});

    	joms.jQuery.extend({
    		xipt:{
			  getProfileTypesFields : function($, id){
					var value = $('#'+id).val();

              		if(value != "XIPT_PROFILETYPE")
                      return true;

                    ptHtml = '<?php echo $profileType; ?>';
                    // valueinputId is parent id of valueId and  profiletype List
                    valueinputId = $('#'+id).attr("id").replace("field", "valueinput");

				    // find hidden text box
				    valueId = $('#'+id).attr("id").replace("field", "value");
				    $('#'+valueId).css('display', 'none');
				    $(ptHtml).appendTo('div#'+valueinputId);


				    // set profileType value in select list by hidden textbox
				    if($('#'+valueId).val())
				    	 $('#'+valueId).next().val($('#'+valueId).val());
				     else
				         $('#'+valueId).val($('#'+valueId).next().val());			//set default value of hidden textbox
				    }

				}
			});

		<?php
	}
	
	function _addResetPrivacyScript()
	{
		$document = JFactory::getDocument();
 		ob_start();
		?>
 		joms.jQuery().ready(function($){
 	
			$('input[onclick="azcommunity.resetprivacy();"]').attr('onclick', '').attr('id','resetPrivacy'); 

				$('#resetPrivacy').click(function(e){
		
				if(!confirm('Are you confirm to reset properties of all existing users')){
						e.preventDefault();
						return false;
				}
				return azcommunity.resetprivacy();
				});								
		});
		<?php
		$content = ob_get_contents();
		ob_clean(); 			
		$document->addScriptDeclaration($content);
	}
}
