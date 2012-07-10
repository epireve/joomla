<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 * @param	{target}	string The name of the target
 * @param	$url		string	The URL to the specific group
 * @param	$user		string	The name of the user
 * @param	$group		string	The name of the group
 */
defined('_JEXEC') or die();
?>
<?php echo JText::sprintf( 'COM_COMMUNITY_EMAIL_EMAIL_BOOKMARKS' , $uri ); ?>

<?php
if( !empty($message) )
{
?>
<?php echo JText::_('COM_COMMUNITY_EMAIL_MESSAGE_HEADING'); ?>

===============================================================================

<?php echo $message; ?>


===============================================================================
<?php
}
?>