<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 * @param	$appName	Current application name
 * @param	$showCheck0
 * @param	$showCheck1
 * @param	$showCheck2 
 */
defined('_JEXEC') or die();
?>
<form name="privacyForm" action="">
	<table class="cWindowForm" cellspacing="1" cellpadding="0">
		<tr>
			<td><input type="radio" value="0" name="privacy"<?php echo $showCheck0;?> /></td>
			<td width="100%">
				<strong><?php echo JText::_('COM_COMMUNITY_APPS_PRIVACY_EVERYONE');?></strong>
				<p><?php echo JText::_('COM_COMMUNITY_APPS_PRIVACY_EVERYONE_DESC');?></p>
			</td>
		</tr>
		<tr>
			<td><input type="radio" value="10" name="privacy"<?php echo $showCheck1;?> /></td>
			<td>
				<strong><?php echo JText::_('COM_COMMUNITY_APPS_PRIVACY_FRIENDS');?></strong>
				<p><?php echo JText::_('COM_COMMUNITY_APPS_PRIVACY_FRIENDS_DESC');?></p>
			</td>
		</tr>
		<tr>
			<td><input type="radio" value="20" name="privacy"<?php echo $showCheck2;?> /></td>
			<td>
				<strong><?php echo JText::_('COM_COMMUNITY_PRIVACY_ME');?></strong>
				<p><?php echo JText::_('COM_COMMUNITY_APPS_PRIVACY_ME_DESC');?></p>
			</td>
		</tr>
	</table>
	<input type="hidden" name="appname" value="<?php echo $appName;?>" />
</form>