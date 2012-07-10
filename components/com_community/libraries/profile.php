<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
CFactory::load( 'libraries' , 'comment' );

class CProfile implements CCommentInterface
{	
	static public function sendCommentNotification( CTableWall $wall , $message )
	{
		CFactory::load( 'libraries' , 'notification' );

		$my				= CFactory::getUser();
		$targetUser		= CFactory::getUser( $wall->post_by );
		$url			= 'index.php?option=com_community&view=profile&userid=' . $wall->contentid;
		$userParams 	= $targetUser->getParams();

		$params		= new CParameter( '' );
		$params->set( 'url' , $url );
		$params->set( 'message' , $message );

		if( $my->id != $targetUser->id && $userParams->get('notifyWallComment') )
		{
			CNotificationLibrary::add( 'etype_profile_submit_wall_comment' , $my->id , $targetUser->id , JText::sprintf('PLG_WALLS_WALL_COMMENT_EMAIL_SUBJECT' , $my->getDisplayName() ) , '' , 'profile.wallcomment' , $params );
			return true;
		}
		return false;
	}
	
	public function getFieldData( $field )
	{
		$fieldType	= strtolower( $field['type'] );
		$value		= $field['value'];
		
		CFactory::load( 'libraries' . DS . 'fields' , $fieldType );
		
		$class		= 'CFields' . ucfirst( $fieldType );
		
		if( class_exists( $class ) )
		{
			$object		= new $class();
			
			if( method_exists( $object , 'getFieldData' ) )
			{
				return $object->getFieldData( $field );
			}
		}
		if($fieldType == 'select' || $fieldType == 'singleselect' || $fieldType == 'radio')
		{
			return JText::_( $value );
		}
		else if($fieldType == 'textarea')
		{
			return nl2br($value);
		}
		else
		{		
			return $value;
		}
	}
	
	/**
	 * Method to get the HTML output for specific fields
	 **/	 	
	public function getFieldHTML( $field , $showRequired = '&nbsp; *' )
	{
		$fieldType	= strtolower( $field->type);
		
		if(is_array($field))
		{
			jimport( 'joomla.utilities.arrayhelper');
			$field = JArrayHelper::toObject($field);
		}
		
		CFactory::load( 'libraries' . DS . 'fields' , $fieldType );

		$class	= 'CFields' . ucfirst( $fieldType );

		if(is_object($field->options))
		{
			$field->options = JArrayHelper::fromObject($field->options);
		}
		
		// Clean the options
		if( !empty( $field->options ) && !is_array( $field->options ) )
		{
			array_walk( $field->options , array( 'JString' , 'trim' ) );
		}                                        
		 
		// Escape the field name                                    
		$field->name	= $this->escape($field->name);

		if( !isset($field->value) )
		{
			$field->value	= '';
		}

		if( class_exists( $class ) )
		{
			$object	= new $class($field->id);
			
			if( method_exists( $object, 'getFieldHTML' ) )
			{
				$html	= $object->getFieldHTML( $field , $showRequired );
				return $html;
			}
		}
		return JText::sprintf('COM_COMMUNITY_UNKNOWN_USER_PROFILE_TYPE' , $class , $fieldType );
	}

	/**
	 * Method to validate any custom field in PHP. Javascript validation is not sufficient enough.
	 * We also need to validate fields in PHP since if the user knows how to send POST data, then they
	 * will bypass javascript validations.
	 **/	 	 	 	
	public function validateField( $fieldId, $fieldType , $value , $required )
	{
		$fieldType	= strtolower( $fieldType );
				
		CFactory::load( 'libraries' . DS . 'fields' , $fieldType );

		$class	= 'CFields' . ucfirst( $fieldType );

		$default_status = true;
		/* === extra validations for fields based on field params === */
		/*
		$profilemodel	= CFactory::getModel('profile');
		$raw_param = $profilemodel->getFieldParams($fieldId);
		
		$params = new CParameter($raw_param);
		
		//validate the extra param first
		CFactory::load( 'helpers' , 'validate' );
		
		
		//only check if there is any parameter in the param field of that field
		if(is_object($params)){
			//check for string limit
			if($params->get('min_char') != '' && $params->get('max_char') != '' && $params->get('min_char') >= 0 && $params->get('max_char') >= 0){
				$default_status = CValidateHelper::characterLength( $params->get('min_char'), $params->get('max_char'), $value);
			}
			
			//additional checking here:
		}*/
		/* === End of extra validation === */
	
		if( class_exists( $class ) && $default_status)
		{
			$object	= new $class($fieldId);
			$object->fieldId = $fieldId;
			if( method_exists( $object, 'isValid' ) )
			{
				$default_status = $object->isValid( $value , $required );
			}
		}
		
		// Assuming there is no need for validation in these subclasses.
		return $default_status;
	}

	public function formatData( $fieldType , $value )
	{
		$fieldType	= strtolower( $fieldType );
				
		CFactory::load( 'libraries' . DS . 'fields' , $fieldType );

		$class	= 'CFields' . ucfirst( $fieldType );

		if( class_exists( $class ) )
		{
			$object	= new $class();
			
			if( method_exists( $object, 'formatData' ) )
			{
				return $object->formatData( $value );
			}
		}
		// Assuming there is no need for formatting in subclasses.
		return $value;
	}
}

/**
 * Maintain classname compatibility with JomSocial 1.6 below
 */ 
class CProfileLibrary extends CProfile
{}
