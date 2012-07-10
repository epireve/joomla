<?php
defined('_JEXEC') or die();
?>
<h2 style="text-decoration: underline;margin-bottom: 10px;"><?php echo JText::_('COM_COMMUNITY_EXISTING_SITE_MEMBER');?></h2>
<div style="margin-bottom: 5px;"><?php echo JText::_('COM_COMMUNITY_EXISTING_SITE_MEMBER_DESCRIPTION');?></div>
<table width="100%">
	<tr>
	    <td width="30%" valign="top"><label for="existingusername"><?php echo JText::_('COM_COMMUNITY_USERNAME');?></label></td>
	    <td><input type="text" id="existingusername" class="inputbox" size="30" /></td>
	</tr>
	<tr>
		<td valign="top"><label for="existingpassword"><?php echo JText::_('COM_COMMUNITY_PASSWORD');?></label></td>
		<td><input type="password" id="existingpassword" class="inputbox" size="30" /></td>
	</tr>
</table>
<div style="color: red;margin-top:20px;"><?php echo JText::_('COM_COMMUNITY_LINKING_NOTICE');?></div>