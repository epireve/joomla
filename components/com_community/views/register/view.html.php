<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');
jimport( 'joomla.utilities.arrayhelper');
jimport( 'joomla.html.html');

class CommunityViewRegister extends CommunityView
{
	public function register($data = null)
	{
		require_once (JPATH_COMPONENT.DS.'libraries'.DS.'profile.php');
				
		$mainframe	=& JFactory::getApplication();
		$my 		= CFactory::getUser();
		
		$config		= CFactory::getConfig();
		$document 	= JFactory::getDocument();
		$document->setTitle(JText::_('COM_COMMUNITY_REGISTER_NEW'));		
		
		// Hide this form for logged in user
		if($my->id) {
			$mainframe->enqueueMessage(JText::_('COM_COMMUNITY_REGISTER_ALREADY_USER'), 'warning');
			return;	
		}
		
		// If user registration is not allowed, show 403 not authorized.
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if ($usersConfig->get('allowUserRegistration') == '0')		
		{
			//show warning message						
			$this->addWarning(JText::_( 'COM_COMMUNITY_REGISTRATION_DISABLED' ));
			return;
		}

		$fields 	= array();	
		$empty_html = array();
		$post 		= JRequest::get('post');

		CFactory::load('helpers', 'user');
		$isUseFirstLastName	= CUserHelper::isUseFirstLastName();

		$data								= array();
		$data['fields']						= $fields;
		$data['html_field']['jsname'] 		= (empty($post['jsname'])) ? '' : $post['jsname'];
		$data['html_field']['jsusername']	= (empty($post['jsusername'])) ? '' : $post['jsusername'];
		$data['html_field']['jsemail'] 		= (empty($post['jsemail'])) ? '' : $post['jsemail'];
		$data['html_field']['jsfirstname']	= (empty($post['jsfirstname'])) ? '' : $post['jsfirstname'];
		$data['html_field']['jslastname']	= (empty($post['jslastname'])) ? '' : $post['jslastname'];

		$js = 'assets/validate-1.5';
		$js	.= ( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js';
		CAssets::attach($js, 'js');
		
		// @rule: Load recaptcha if required.
		CFactory::load( 'helpers' , 'recaptcha' );
		$recaptchaHTML	= getRecaptchaHTMLData();

		$fbHtml	= '';

		if( $config->get('fbconnectkey') && $config->get('fbconnectsecret') )
		{
			CFactory::load( 'libraries' , 'facebook' );
			$facebook	= new CFacebook();
			$fbHtml		= $facebook->getLoginHTML();
		}
		
		$tmpl	=   new CTemplate();
		$content = $tmpl->set( 'data'		    , $data )
						->set( 'recaptchaHTML'	    , $recaptchaHTML )
						->set( 'config'		    , $config )
						->set( 'isUseFirstLastName'    , $isUseFirstLastName )
						->set( 'fbHtml'		    , $fbHtml )
						->fetch( 'register.index' );
		
		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();
				
		$args		= array(&$content);
		$appsLib->triggerEvent( 'onUserRegisterFormDisplay' , $args );				
		
		echo $this->_getProgressBar(1);
		echo $content;
	}
	
	/**
	 * Displays the form where user selects their profile type.
	 **/	 	
	public function registerProfileType()
	{
		$mainframe	=& JFactory::getApplication();
		$document	= JFactory::getDocument();
		$document->setTitle( JText::_('COM_COMMUNITY_MULTIPROFILE_SELECT_TYPE') );

		$model	= CFactory::getModel( 'Profile' );
		$tmp	= $model->getProfileTypes();

		$profileTypes	= array();
		$showNotice		= false;
		foreach( $tmp as $profile )
		{
			$table	=& JTable::getInstance( 'MultiProfile' , 'CTable' );
			$table->load( $profile->id );
			
			if( $table->approvals )
				$showNotice	= true;
				
			$profileTypes[]	= $table;
		}
		
		$tmpl		= new CTemplate();
		echo $tmpl	->set( 'default'	, 0 )
					->set( 'profileTypes'	, $profileTypes )
					->set( 'showNotice'	, $showNotice )
					->set( 'message'	, JText::_('COM_COMMUNITY_MULTIPROFILE_INFO') )
					->fetch( 'register.profiletype' );
	}
	
	/**
	 * Display custom profiles registration form.
	 **/
	public function registerProfile( $fields )
	{
		CFactory::load( 'libraries' , 'profile' );
		jimport( 'joomla.utilities.arrayhelper' );
		jimport( 'joomla.utilities.date' );
	
		$mainframe	=& JFactory::getApplication();	
		$document 	= JFactory::getDocument();		
		$document->setTitle( JText::_('COM_COMMUNITY_REGISTER_NEW') );
		
		$model 			= CFactory::getModel('profile');
		$profileType	= JRequest::getVar( 'profileType' , 0 );
		$config			= CFactory::getConfig();
		$profileTypes	= $model->getProfileTypes();
		
		// @rule: When multiple profile is enabled, and profile type is not selected, we should trigger an error.
		if( $config->get('profile_multiprofile') && $profileType == COMMUNITY_DEFAULT_PROFILE && !empty( $profileTypes ) )
		{
			$mainframe->redirect( CRoute::_('index.php?option=com_community&view=register&task=registerProfileType' , false ) , JText::_('COM_COMMUNITY_NO_PROFILE_TYPE_SELECTED') , 'error' );
		}
				
		$empty_html = array();
		$post = JRequest::get('post');
		
		CFactory::load('helpers', 'user');
		$isUseFirstLastName	= CUserHelper::isUseFirstLastName();
		
		$firstName		= '';
		$lastName		= '';
		if ($isUseFirstLastName)
		{
			$fullname	= $this->_getFirstLastName();
			$firstName	= $fullname['first'];
			$lastName	= $fullname['last'];
		}
		
		// Bind result from previous post into the field object
		if(! empty($post))
		{
			foreach($fields as $group)
			{
			    $field = $group->fields;
			    for($i = 0; $i <count($field); $i++)
				{
	 				$fieldid    = $field[$i]->id;
	 				$fieldType  = $field[$i]->type;
	 				
					if(!empty($post['field'.$fieldid]))
					{
						if(is_array($post['field'.$fieldid]))
						{
						   if($fieldType != 'date'  && $fieldType != 'birthdate')
						   {
						        $values = $post['field'.$fieldid];
						        //$value  = '';
								/*foreach($values as $listValue)
								{
									$value	.= $listValue . ',';
								}*/
								$value = implode(',', $values);
						        $field[$i]->value = $value;
						   }
						   else 
						   {
						       $field[$i]->value = $post['field'.$fieldid];
						   }
						} 
						else 
						{
						    $field[$i]->value = $post['field'.$fieldid];						
						}
					}
                }
			}
		} 
		else 
		{
			if ($isUseFirstLastName)
			{
				foreach($fields as $group)
				{
				    $field = $group->fields;
				    for($i = 0; $i <count($field); $i++)
					{
		 				if ($field[$i]->fieldcode == 'FIELD_GIVENNAME')
		 					$field[$i]->value = $firstName;
		 				if ($field[$i]->fieldcode == 'FIELD_FAMILYNAME')
		 					$field[$i]->value = $lastName;
	                }
				}
			}
		}
		
		$config		= CFactory::getConfig();
		$js	= 'assets/validate-1.5'.(( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js');
		CAssets::attach($js, 'js');

		$profileType	= JRequest::getVar( 'profileType' , 0 , 'GET' );
		
		CFactory::load( 'libraries' , 'privacy' );
		$tmpl	= new CTemplate();

		echo $this->_getProgressBar(2);
		echo $tmpl	->set( 'fields' , $fields )
					->set( 'profileType' , $profileType )
					->fetch( 'register.profile' );
	}
	
	/**
	 * Display Upload avatar form for user
	 **/	 	
	public function registerAvatar()
	{
		$mainframe =& JFactory::getApplication();

		//retrive the current session.
		$mySess =& JFactory::getSession();
		$user		= CFactory::getUser($mySess->get('tmpUser','')->id);
		$firstLogin	= true;

		$config			= CFactory::getConfig();
		$uploadLimit	= (double) $config->get('maxuploadsize');
		$uploadLimit	.= 'MB';
		
		// Load the toolbar
		$this->showSubmenu();
		$document =  JFactory::getDocument ();
		$document->setTitle ( JText::_ ( 'COM_COMMUNITY_CHANGE_AVATAR' ) );
		$profileType	= JRequest::getVar( 'profileType' , 0 , 'GET' );
		$tmpl	  = new CTemplate();
		$skipLink = CRoute::_('index.php?option=com_community&view=register&task=registerSucess&profileType=' . $profileType );
		
		echo $this->_getProgressBar(3);
		echo $tmpl	->set( 'profileType'	, $profileType )
					->set( 'user' , $user )
					->set( 'uploadLimit' , $uploadLimit )
					->set( 'firstLogin' , $firstLogin )
					->set( 'skipLink' , $skipLink )
					->fetch( 'profile.uploadavatar' );
	}	
	
	public function registerSucess()
	{
		$document = JFactory::getDocument();				
		$document->setTitle(JText::_('COM_COMMUNITY_USER_REGISTERED'));
				
		$uri				= CRoute::_('index.php?option=com_community&view=frontpage');
		$usersConfig		= &JComponentHelper::getParams( 'com_users' );
		$useractivation		= $usersConfig->get( 'useractivation' );
		$profileType		= JRequest::getVar( 'profileType' , '' );
		$message			= JText::_( 'COM_COMMUNITY_REGISTER_COMPLETE' );
		$multiprofile	=& JTable::getInstance( 'MultiProfile' , 'CTable' );
		$multiprofile->load( $profileType );

		if( $multiprofile->approvals || $useractivation == 2)
		{
			$message	= JText::_( 'COM_COMMUNITY_REGISTRATION_COMPLETED_NEED_APPROVAL' );
		}
		
		if( $useractivation == 1 && !$multiprofile->approvals )
		{
			$message	= JText::_( 'COM_COMMUNITY_REGISTER_COMPLETE_ACTIVATE_REQUIRED' ); 
		}

		$tmpl	= new CTemplate();
		
		echo $this->_getProgressBar(4);
		echo $tmpl	->set( 'message'	, $message )
					->set( 'uri'		, $uri )
					->fetch( 'register.success' );    
	}
	
	public function activation()
	{
		$config		= CFactory::getConfig();
		$document 	= JFactory::getDocument ();
		$document->setTitle ( JText::_ ( 'COM_COMMUNITY_RESEND_ACTIVATION' ) );
						
		$js	= 'assets/validate-1.5'.(( $config->getBool('usepackedjavascript') ) ? '.pack.js' : '.js');
		CAssets::attach($js, 'js');
		
		$tmpl	  = new CTemplate();
		echo $tmpl->fetch( 'register.activation' );		
	}
	
	private function _getProgressBar($currstep=1){
		//count the number of registration steps new users hv to go thru
		$numstep = 2;
		$config		= CFactory::getConfig();
		$model			= CFactory::getModel( 'Profile' );
		$profileTypes	= $model->getProfileTypes();
		if(!$profileTypes || !$config->get('profile_multiprofile')){
			$numstep += 2;
		}
		
		CFactory::load('helpers', 'progressbar');
		$html = CProgressbarHelper::getHTML($numstep, $currstep);

		return $html;
	}

	private function _getFirstLastName()
	{
		$tmpUserModel	= CFactory::getModel('register');
		$mySess 		=& JFactory::getSession();
		$tmpUser		= $tmpUserModel->getTempUser($mySess->get('JS_REG_TOKEN',''));
		
		$fullname		= array();
		$fullname['first']	= $tmpUser->firstname;
		$fullname['last']	= $tmpUser->lastname;
		
		return $fullname;
	}
}
