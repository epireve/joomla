<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
Joomla.submitbutton = function(action){
	submitbutton( action );
}

function submitbutton( action )
{
	if( action == 'save' )
	{
		sendMessage( joms.jQuery('#title').val() , jQuery('#message').val() , 1 );
	}
}

function sendMessage( title , message , limit )
{
	jax.call( 'community' , 'admin,messaging,ajaxSendMessage' , title , message, limit );
}
</script>
<form name="adminForm" method="post">
<div id="messaging-form">
<p><?php echo JText::_('COM_COMMUNITY_MESSAGING_ALLOWS_SEND_EMAIL');?></p>
<table class="admintable">
	<tr>
		<td class="key" valign="top"><?php echo JText::_('COM_COMMUNITY_TITLE');?></td>
		<td><input type="text" id="title" name="title" value="" size="120" /></td>
	</tr>
	<tr>
		<td class="key" valign="top"><?php echo JText::_('COM_COMMUNITY_MESSAGE');?></td>
		<td>
			<textarea name="message" id="message" rows="10" cols="80"></textarea>
		</td>
	</tr>
</table>
</div>
<div id="messaging-result" style="display: none;">
<fieldset style="width: 50%">
	<legend><?php echo JText::_('COM_COMMUNITY_MESSAGING_SENDING_MESSAGES');?></legend>
	<div><?php echo JText::_('COM_COMMUNITY_MESSAGING_DONT_REFRESH_PAGE');?></div>
	<div id="no-progress"><?php echo JText::_('COM_COMMUNITY_MESSAGING_NO_PROGRESS');?></div>
	<div id="progress-status" style="padding-top: 5px;"></div>
</fieldset>
</div>
</form>