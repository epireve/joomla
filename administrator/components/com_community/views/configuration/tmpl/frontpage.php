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
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FRONTPAGE' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FRONTPAGE_SITE_TITLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FRONTPAGE_SITE_TITLE_DESC'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FRONTPAGE_SITE_TITLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="sitename" value="<?php echo $this->config->get('sitename');?>" size="40" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FRONTPAGE_REDIRECT_LOGIN' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FRONTPAGE_REDIRECT_LOGIN_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FRONTPAGE_REDIRECT_LOGIN' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="redirect_login">
						<option value="profile"<?php echo $this->config->get('redirect_login') == 'profile' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_PROFILE_OPTION');?></option>
						<option value="frontpage"<?php echo $this->config->get('redirect_login') == 'frontpage' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_FRONTPAGE_OPTION');?></option>
						<option value="videos"<?php echo $this->config->get('redirect_login') == 'videos' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_VIDEOS_OPTION');?></option>
						<option value="photos"<?php echo $this->config->get('redirect_login') == 'photos' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_PHOTOS_OPTION');?></option>
						<option value="friends"<?php echo $this->config->get('redirect_login') == 'friends' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_FRIENDS_OPTION');?></option>
						<option value="apps"<?php echo $this->config->get('redirect_login') == 'apps' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_APPLICATIONS_OPTION');?></option>
						<option value="inbox"<?php echo $this->config->get('redirect_login') == 'inbox' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_INBOX_OPTION');?></option>
						<option value="groups"<?php echo $this->config->get('redirect_login') == 'groups' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_GROUPS');?></option>
						<option value="events"<?php echo $this->config->get('redirect_login') == 'events' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_EVENTS_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FRONTPAGE_REDIRECT_LOGOUT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FRONTPAGE_REDIRECT_LOGOUT_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FRONTPAGE_REDIRECT_LOGOUT' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="redirect_logout">
						<option value="profile"<?php echo $this->config->get('redirect_logout') == 'profile' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_PROFILE_OPTION');?></option>
						<option value="frontpage"<?php echo $this->config->get('redirect_logout') == 'frontpage' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_FRONTPAGE_OPTION');?></option>
						<option value="videos"<?php echo $this->config->get('redirect_logout') == 'videos' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_VIDEOS_OPTION');?></option>
						<option value="photos"<?php echo $this->config->get('redirect_logout') == 'photos' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_PHOTOS_OPTION');?></option>
						<option value="friends"<?php echo $this->config->get('redirect_logout') == 'friends' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_FRIENDS_OPTION');?></option>
						<option value="apps"<?php echo $this->config->get('redirect_logout') == 'apps' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_APPLICATIONS_OPTION');?></option>
						<option value="inbox"<?php echo $this->config->get('redirect_logout') == 'inbox' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_INBOX_OPTION');?></option>
						<option value="groups"<?php echo $this->config->get('redirect_logout') == 'groups' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_GROUPS');?></option>
						<option value="events"<?php echo $this->config->get('redirect_logout') == 'events' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_EVENTS_OPTION');?></option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>