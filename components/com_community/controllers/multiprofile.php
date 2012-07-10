<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class CommunityMultiprofileController extends CommunityBaseController
{
	/**
	 * Defines whether the multiprofile environment is enabled or not.
	 *
	 * @return  boolean True when enabled.
	 **/
	public function _isEnabled()
	{
		$config	= CFactory::getConfig();
		return $config->get( 'profile_multiprofile' );
	}
	
	public function display()
	{
		$this->changeProfile();
	}
	
	/**
	 * Displays the profile updated message
	 **/
	public function profileUpdated()
	{
		$document 	= JFactory::getDocument();
		$viewType	= $document->getType();
 		$viewName	= JRequest::getCmd( 'view', $this->getName() );
 		$view		=& $this->getView( $viewName , '' , $viewType);
 		
 		echo $view->get( __FUNCTION__ );
	}
	
	public function changeProfile()
	{
		$document 	= JFactory::getDocument();
		$viewType	= $document->getType();
 		$viewName	= JRequest::getCmd( 'view', $this->getName() );
 		$view		=& $this->getView( $viewName , '' , $viewType);
		$my			= CFactory::getUser();
		
		if( !$this->_isEnabled() )
		{
			echo JText::_('COM_COMMUNITY_MULTIPROFILE_IS_CURRENTLY_DISABLED');
			return;
		}
		
		if( JRequest::getMethod() == 'POST' )
		{
			$profileType	= JRequest::getVar( 'profileType' , '' );
			$mainframe		=& JFactory::getApplication();
			if( empty($profileType) )
			{
				$mainframe->enqueueMessage( JText::_('COM_COMMUNITY_NO_PROFILE_TYPE_SELECTED') , 'error' );
			}
			else
			{
				
				$url			= CRoute::_('index.php?option=com_community&view=multiprofile&task=updateProfile&profileType=' . $profileType , false );
				
				if( $my->getProfileType() == $profileType )
				{
					$url		= CRoute::_('index.php?option=com_community&view=multiprofile&task=changeProfile' , false );
					$mainframe->redirect( $url , JText::_('COM_COMMUNITY_ALREADY_USING_THIS_PROFILE_TYPE') , 'error');
				}
	
				$mainframe->redirect( $url );
			}
		}
		echo $view->get(__FUNCTION__);
	}
	
	/**
	 * Updates user profile
	 **/	 	
	public function updateProfile()
	{
		$document 	= JFactory::getDocument();
		$viewType	= $document->getType();
 		$viewName	= JRequest::getCmd( 'view', $this->getName() );
 		$view		=& $this->getView( $viewName , '' , $viewType);

		if( !$this->_isEnabled() )
		{
			echo JText::_('COM_COMMUNITY_MULTIPROFILE_IS_CURRENTLY_DISABLED');
			return;
		}

		$appsLib	=& CAppPlugins::getInstance();
		$appsLib->loadApplications();
		$mainframe	=& JFactory::getApplication();
		$profileType	= JRequest::getInt( 'profileType' , 0 );
		$model	= $this->getModel( 'Profile' );
		$my		= CFactory::getUser();
		$data	= $model->getEditableProfile( $my->id , $profileType );
		$oldProfileType	= $my->getProfileType();

		// If there is nothing to edit, we should just redirect 
		if( empty( $data['fields'] ) )
		{
			$multiprofile		=& JTable::getInstance( 'MultiProfile' , 'CTable' );
			$multiprofile->load( $profileType );
			
			$my->_profile_id	= $multiprofile->id;
			
			// Trigger before onProfileTypeUpdate
			$args 	= array();
			$args[]	= $my->id;
			$args[]	= $oldProfileType;
			$args[]	= $multiprofile->id;
			$result = $appsLib->triggerEvent( 'onProfileTypeUpdate' , $args );
			
			CFactory::load( 'helpers' , 'owner' );
			
			// @rule: If profile requires approval, logout user and update block status. This is not 
			// applicable to site administrators.
			if( $multiprofile->approvals && !COwnerHelper::isCommunityAdmin( $my->id ) )
			{
				$my->set( 'block' , 1 );
				
				CFactory::load( 'helpers' , 'owner' );
				$subject	= JText::sprintf( 'COM_COMMUNITY_USER_NEEDS_APPROVAL_SUBJECT' , $my->name );
				$message	= JText::sprintf( 'COM_COMMUNITY_USER_PROFILE_CHANGED_NEEDS_APPROVAL' , $my->name, $my->email, $my->username , $multiprofile->name , CRoute::getExternalURL('index.php?option=com_community&view=profile&userid=' . $my->id ) );

				COwnerHelper::emailCommunityAdmins( $subject , $message );

				// @rule: Logout user.
				$mainframe->logout();
			}
			$my->save();
			$mainframe->redirect( CRoute::_('index.php?option=com_community&view=multiprofile&task=profileupdated&profileType=' . $multiprofile->id , false ) );
		}
		
		if( JRequest::getMethod() == 'POST' )
		{
			$model			= $this->getModel( 'Profile' );
			$values			= array();
			$profileType	= JRequest::getInt( 'profileType' , 0  , 'POST');
			
			CFactory::load( 'libraries' , 'profile' );
			
			$profiles	= $model->getAllFields( array('published'=>'1') , $profileType );
			$errors		= array();

			// Delete all user's existing profile values and re-add the new ones
			// @rule: Bind the user data

			foreach( $profiles as $key => $groups )
			{
				foreach( $groups->fields as $data )
				{
					// Get value from posted data and map it to the field.
					// Here we need to prepend the 'field' before the id because in the form, the 'field' is prepended to the id.
					$postData				= JRequest::getVar( 'field' . $data->id , '' , 'POST' );
					$values[ $data->id ]	= CProfileLibrary::formatData( $data->type  , $postData );
					
					// @rule: Validate custom profile if necessary
					if( !CProfileLibrary::validateField( $data->id, $data->type , $values[ $data->id ] , $data->required) )
					{
						// If there are errors on the form, display to the user.
						$message	= JText::sprintf('COM_COMMUNITY_FIELD_CONTAIN_IMPROPER_VALUES' ,  $data->name );
						$mainframe->enqueueMessage( $message , 'error' );
						$errors[]	= true;
					}
				}
			}

			// Rebuild new $values with field code
			$valuesCode = array();
			
			foreach( $values as $key => $val )
			{
				$fieldCode = $model->getFieldCode($key);

				if( $fieldCode )
				{
					// For backward compatibility, we can't pass in an object. We need it to behave
					// like 1.8.x where we only pass values.
					$valuesCode[$fieldCode] = $val;
				}
			}


			$args		= array();
			$args[]		= $my->id;
			$args[]		= $valuesCode;
			$saveSuccess	= false;
			$result 	= $appsLib->triggerEvent( 'onBeforeProfileUpdate' , $args );
			
			// make sure none of the $result is false
			if(!$result || ( !in_array(false, $result) ) )
			{
				$saveSuccess = true;
				$model->saveProfile( $my->id, $values );
			}
			
			$mainframe	=& JFactory::getApplication();
			
			if( !$saveSuccess )
			{
				$mainframe->redirect( CRoute::_( 'index.php?option=com_community&view=multiprofile&task=updateProfile&profileType=' . $profileType , false ) ,  JText::_('COM_COMMUNITY_PROFILE_NOT_SAVED') , 'error' ); 
			}

			// Trigger before onAfterUserProfileUpdate
			$args 	= array();
			$args[]	= $my->id;
			$args[]	= $saveSuccess; 
			$result = $appsLib->triggerEvent( 'onAfterProfileUpdate' , $args );
			
			$multiprofile		=& JTable::getInstance( 'MultiProfile' , 'CTable' );
			$multiprofile->load( $profileType );
			$my->_profile_id	= $multiprofile->id;
			
			
			CFactory::load( 'helpers' , 'owner' );
			
			// @rule: If profile requires approval, logout user and update block status. This is not 
			// applicable to site administrators.
			if( $multiprofile->approvals && !COwnerHelper::isCommunityAdmin( $my->id ) )
			{
				$my->set( 'block' , 1 );
				
				CFactory::load( 'helpers' , 'owner' );
				$subject	= JText::sprintf( 'COM_COMMUNITY_USER_NEEDS_APPROVAL_SUBJECT' , $my->name );
				$message	= JText::sprintf( 'COM_COMMUNITY_USER_PROFILE_CHANGED_NEEDS_APPROVAL' , $my->name, $my->email, $my->username , $multiprofile->name , CRoute::getExternalURL('index.php?option=com_community&view=profile&userid=' . $my->id ) );

				COwnerHelper::emailCommunityAdmins( $subject , $message );

				// @rule: Logout user.
				$mainframe->logout();
			}
			$my->save();

			// Trigger before onProfileTypeUpdate
			$args 	= array();
			$args[]	= $my->id;
			$args[]	= $oldProfileType;
			$args[]	= $multiprofile->id;
			$result = $appsLib->triggerEvent( 'onProfileTypeUpdate' , $args );
			
			if( !in_array( true , $errors ) )
			{
				$mainframe->redirect( CRoute::_('index.php?option=com_community&view=multiprofile&task=profileupdated&profileType=' . $multiprofile->id , false ) );
			}
		}
 		echo $view->get( __FUNCTION__ );
	}
}
