<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	Notification
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

CFactory::load( 'libraries' , 'template' );

class CNotification
{
	/**
	 *	Adds notification data into the mailq table
	 **/
	public function add( $command , $actorId , $recipients , $subject , $body , $templateFile ='', $mailParams = '' , $sendEmail = true , $favicon = '' )
	{
		CFactory::load( 'helpers' , 'validate' );
		
		// Need to make sure actor is NULL, so default user will be returned
		// from getUser
		if(empty($actorId)){
			$actorId = null;
		}
		
		$mailq	= CFactory::getModel( 'Mailq' );
		$actor	= CFactory::getUser( $actorId );
		$config	= CFactory::getConfig();

		if(!is_array( $recipients ) )
		{
			$recipientsArray	= array();
			$recipientsArray[]	= $recipients;
		}
		else
		{
			$recipientsArray	= $recipients;
		}
		$contents	= '';
		
		// If template file is given, we shall extract the email from the template file.
		if( !empty($templateFile) )
		{
			$tmpl		= new CTemplate();
			preg_match( '/email/i' , $templateFile , $matches );
			
			if( empty( $matches ) )
			{
				$templateFile	= 'email.' . $templateFile;
				$templateFile	.= $config->get('htmlemail') ? '.html' : '.text';
			}

			if( is_object( $mailParams ) )
			{
				$dataArray	= $mailParams->toArray();
		
				foreach( $dataArray as $key => $value )
				{
					$tmpl->set( $key , $value );
				}
			}
			elseif( is_array( $mailParams ) )
			{
		    	foreach($mailParams as $key=> $val)
					$tmpl->set($key,  $val);
			}
			$contents	= $tmpl->fetch( $templateFile );
		}
		else
		{
			$contents	= $body;
		}

		$cmdData = explode( '_', $command );
		
		// check for privacy setting for each user
		foreach( $recipientsArray as $recipient )
		{
			//we process the receipient emails address differently from the receipient id.
			$recipientEmail	=   '';
			$recipientName	=   '';
			$sendIt		=   false;
			
			if( CValidateHelper::email($recipient) )
			{
				// Check if the recipient email same with actor email
				$self	=   self::filterActor( $actorId, $recipient ) ;

				// If same, skip to next email
				if( $self )
				{
					continue;
				}

				$recipientName	= '';
				$sendIt		= true;
				$recipientEmail	= $recipient;
			}
			else
			{
				$userTo	=   CFactory::getUser( $recipient );

				// Check if the recipient email same with actor email
				$self	=   self::filterActor( $actorId, $userTo->email ) ;

				// If same, skip to next email
				if( $self )
				{
					continue;
				}

				$params 	= $userTo->getParams();
				$recipientName	= $userTo->getDisplayName();
				$recipientEmail	= $userTo->email;
				$sendIt			= false;
				
				if(isset($cmdData[1])){
					switch($cmdData[0])
					{
						case 'inbox':
						case 'photos':
						case 'groups':
						case 'events':
						case 'friends':
						case 'profile':
//							$sendIt	= $params->get('notifyEmailSystem');
//							break;
						case 'system':
						default:
							$sendIt = true;
							break;
							
					}
				}
			}

			if($sendIt)
			{
				// Porcess the message and title
				$search 	= array('{actor}', '{target}');
				$replace 	= array( $actor->getDisplayName(), $recipientName );
				
				$subject 	= CString::str_ireplace($search, $replace, $subject);
				$body 		= CString::str_ireplace($search, $replace, $contents );
				
				$mailq->add( $recipientEmail , $subject , $body , $templateFile , $mailParams , 0, $command );
			}
		}
	}
	
	/**
	 * Return notification send to the given user
	 */	 	
	public function get($id){
		$mailqModel = CFactory::getModel( 'mailq' );
		$mailers = $mailqModel->get();
	}

	/**
	 * Filter actor from send notification email to self
	 * If the actor email and the recipient email is same return TRUE
	 */
	public function filterActor( $actorId, $recipientEmail )
	{
		$actor	= &JFactory::getUser( $actorId );
		return ( $actor->email == $recipientEmail ) ? true : false;
	}
}

/**
 * Maintain classname compatibility with JomSocial 1.6 below
 */ 
class CNotificationLibrary extends CNotification
{

}
