<?php
/**
 * @category 	Library
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Callback methods must return a CInvitationMail object. If CInvitationEmail object is not returned,
 * the invitations will not be invoked. 
 **/ 
class CInvitationMail
{
	public $html	= '';
	public $text	= '';
	public $title	= '';
	public $params	= '';
	private $_error	= false;
	private $_errorMessage	= '';
	
	public function __construct( $html , $text = '' , $title , $params )
	{
		$this->html		= $html;
		$this->text		= $text;
		$this->title	= $title;
		$this->params	= $params;
	}

	/**
	 * Returns the title of the email
	 * 
	 * @return	String	Title of the email
	 **/
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * Returns the params for the email
	 * 
	 * @return	JParameter	Params for the email.
	 **/
	public function getParams()
	{
		return $this->params;
	}
	
	/**
	 * Set an error message
	 * 
	 * @param	$message	String	An error message of the current invite process.	 	 
	 **/	 	
	public function setError( $message )
	{
		$this->_error			= true;
		$this->_errorMessage	= $message;
	}

	/**
	 * Set an error message
	 * 
	 * @return	String	Error message.	 	 
	 **/
	public function getError()
	{
		return $this->_errorMessage;
	}

	/**
	 * Determines if there is an error.
	 * 
	 * @return	bool	Whether the current invite has any errors.
	 **/
	public function hasError()
	{
		return $this->_error;
	}


	/**
	 * Returns a text representation of the invite body. Determins whether
	 * to use html or text mode.	 
	 * 
	 * @return	String	HTML or raw text for the email content.
	 **/
	public function getContent()
	{
		$config	= CFactory::getConfig();
		
		if( $config->get( 'htmlemail' ) )
			return $this->html;
			
		return $this->text;
	}
}

class CInvitation
{
	const SHOW_FRIENDS	= true;
	const HIDE_FRIENDS	= false;
	const SHOW_EMAIL	= true;
	const HIDE_EMAIL	= false;

	/**
	 * Responsible to output html codes for the invitation popup.
	 * 
	 * @param	$users	Array	An array that contains a list of user ids.
	 * @param	$callbackMethod		String	A string that identifies a specific caller
	 * @param	$cid	int		An integer that identifies a specific unique item
	 * @param	$showFriends	bool	Determines whether to display friends list or not.
	 * 
	 * @return	$content	Html content.	 	 	 	 	 	 	 
	 **/
	public function getHTML( $users , $callbackMethod , $cid , $showFriends = true , $showEmail = true )
	{
		$displayFriends	= $showFriends ? 1 : 0;
		$displayEmail	= $showEmail ? 1 : 0;
		
		$userIds	= '';
		$i			= 0;
		
		if( $users )
		{
			foreach( $users as $user )
			{
				if( $user instanceof CUser )
				{
					$userIds	.= $user->id;
				}
				else
				{
					$userIds	.= $user;
				}
				
				if( ( $i + 1 ) != count( $users ) )
				{
					$userIds	.= ',';
				}
				$i++;
			}
		}
		
		$tmpl	= new CTemplate();
		$tmpl->set( 'userIds'	, $userIds );
		$tmpl->set( 'callbackMethod'	, $callbackMethod );
		$tmpl->set( 'displayFriends'	, $displayFriends );
		$tmpl->set( 'displayEmail'		, $displayEmail );
		$tmpl->set( 'cid'		, $cid );
		
		$html	= $tmpl->fetch( 'invitation.html' );
		return $html;
	}
}