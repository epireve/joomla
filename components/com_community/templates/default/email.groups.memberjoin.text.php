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
<?php echo JText::_('COM_COMMUNITY_GREETING');?>

<?php
if( $approved )
{
	echo JText::sprintf( 'COM_COMMUNITY_GROUPS_EMAIL_NEW_MEMBER_JOINED' , $user , $group , '{url}' );
}
else
{
	echo JText::sprintf( 'COM_COMMUNITY_NEW_MEMBER_REQUESTED_TO_JOIN_GROUP_EMAIL' , $user , $group , '{url}' );
}
