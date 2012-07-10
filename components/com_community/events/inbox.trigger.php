<?php
/**
 * @category	Events
 * @package		JomSocial
 * @copyright (C) 2010 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CInboxTrigger
{
	
	public function onMessageDisplay( $row )
	{
		CFactory::load( 'helpers' , 'string' );
		CError::assert( $row->body, '', '!empty', __FILE__ , __LINE__ );
		
		// @rule: Only nl2br text that doesn't contain html tags
		if( !CStringHelper::isHTML( $row->body ) )
		{			
			$row->body	= CStringHelper::nl2br( $row->body );
		}
	}
}