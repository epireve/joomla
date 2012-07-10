<?php
/**
 * @category	Helper
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

function getJomSocialPoweredByLink()
{
	return " ";
	return '<div style="text-align:center;font-size:85%"><a title="JomSocial, Social Networking for Joomla! 1.5" href="http://www.jomsocial.com">Powered by JomSocial</a></div>';
}

function checkFolderExist( $folderLocation )
{
	if( JFolder::exists( $folderLocation ) ) 
	{
		return true;
	}
	
	return false;
}