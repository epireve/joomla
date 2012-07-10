<?php
/**
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class CMailq
{		
	/**
	 * Do a batch send
	 */	 	
	public function send( $total = 100 )
	{
		$mailqModel = CFactory::getModel( 'mailq' );
		$userModel	= CFactory::getModel( 'user' );
		$mails		= $mailqModel->get( $total, true );
		$jconfig	= JFactory::getConfig();
		$mailer		= JFactory::getMailer();
		$config		= CFactory::getConfig();
		
		$senderEmail  = $jconfig->getValue('mailfrom');
		$senderName   = $jconfig->getValue('fromname');

		$conf =& JFactory::getConfig();
		$sitename = $conf->getValue('config.sitename');


		if(empty($mails))
		{
			return;
		}

		CFactory::load( 'helpers' , 'string' );
		
		foreach( $mails as $row )
		{
			// @rule: only send emails that is valid.
			// @rule: make sure recipient is not blocked!
			$userid = $userModel->getUserFromEmail($row->recipient);
			$user = CFactory::getUser($userid);

			//verify user email list settting
			
			$user_params = $user->getParams();
			$validate = true;
			if (!empty($row->email_type)){
				$validate = ($user_params->get($row->email_type, $config->get($row->email_type)) == 1 ) ? true : false;
			}
			
			if( !$user->isBlocked() && !JString::stristr( $row->recipient , 'foo.bar') && $validate)
			{
				
				$mailer->setSender( array( $senderEmail, $senderName ) );
				$mailer->addRecipient($row->recipient);
				$mailer->setSubject($row->subject);

				$tmpl		= new CTemplate();
				$raw		= isset($row->params) ? $row->params : '';
				$params		= new CParameter( $row->params );
				$base		= $config->get('htmlemail') ? 'email.html' : 'email.text';
				
				if( $config->get('htmlemail') )
				{
					$row->body	= CString::str_ireplace(array("\r\n", "\r", "\n"), '<br />', $row->body );
					$mailer->IsHTML( true );
				}
				else
				{
					//@rule: Some content might contain 'html' tags. Strip them out since this mail should never contain html tags.
					$row->body	= CStringHelper::escape( strip_tags($row->body) );
				}
				$copyrightemail = JString::trim($config->get( 'copyrightemail' ));
								
				$tmpl->set( 'avatar', $user->getAvatar());
				$tmpl->set( 'thumbAvatar', $user->getThumbAvatar());
				$tmpl->set( 'name' , $user->getDisplayName());
				$tmpl->set( 'email' , $user->email);
				$tmpl->set( 'sitename' , $sitename);
				$tmpl->set( 'unsubscribeLink' , CRoute::getExternalURL('index.php?option=com_community&view=profile&task=privacy'),false);
				$tmpl->set( 'userid' , $userid);
				$tmpl->set( 'copyrightemail' , $copyrightemail);
				$tmpl->set( 'recepientemail' , $row->recipient);
				$tmpl->set( 'content' , $row->body );
				$tmpl->set( 'template', rtrim( JURI::root() , '/' ) . '/components/com_community/templates/' . $config->get('template') );
				$tmpl->set( 'sitename' , $config->get('sitename') );

				$row->body	= $tmpl->fetch( $base );
				
				// Replace any occurences of custom variables within the braces scoe { }
				if( !empty( $row->body ) )
				{
					preg_match_all("/{(.*?)}/", $row->body, $matches, PREG_SET_ORDER);
		
					foreach ($matches as $val) 
					{
						$replaceWith = $params->get($val[1], null);
						
						//if the replacement start with 'index.php', we can CRoute it
						if( strpos($replaceWith, 'index.php') === 0)
						{
							$replaceWith = CRoute::getExternalURL($replaceWith);
						}
						
						if( !is_null( $replaceWith ) ) 
						{
							$row->body	= CString::str_ireplace( $val[0] , $replaceWith , $row->body );
						}
					}
				}
				unset($tmpl);
			
				$mailer->setBody($row->body);					
				$mailer->send();
			}
			
			if(!$validate){ //email is blocked by user settings
				$mailqModel->markEmailStatus($row->id, 2);
			}
			else{
				$mailqModel->markSent($row->id);
			}
			$mailer->ClearAllRecipients();
		}
	}
}

/**
 * Maintain classname compatibility with JomSocial 1.6 below
 */ 
class CMailqLibrary extends CMailq
{}
