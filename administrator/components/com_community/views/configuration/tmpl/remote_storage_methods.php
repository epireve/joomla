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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_METHODS' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_PHOTOS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_PHOTOS_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_PHOTOS' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="photostorage">
						<option <?php echo ( $this->config->get('photostorage') == 'file' ) ? 'selected="true"' : ''; ?> value="file"><?php echo JText::_('COM_COMMUNITY_LOCALSERVER_OPTION');?></option>
						<option <?php echo ( $this->config->get('photostorage') == 's3' ) ? 'selected="true"' : ''; ?> value="s3"><?php echo JText::_('COM_COMMUNITY_AMAZONS3_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_VIDEOS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_VIDEOS_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_VIDEOS' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="videostorage">
						<option <?php echo ( $this->config->get('videostorage') == 'file' ) ? 'selected="true"' : ''; ?> value="file"><?php echo JText::_('COM_COMMUNITY_LOCALSERVER_OPTION');?></option>
						<option <?php echo ( $this->config->get('videostorage') == 's3' ) ? 'selected="true"' : ''; ?> value="s3"><?php echo JText::_('COM_COMMUNITY_AMAZONS3_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_PROFILEAVATARS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_PROFILEAVATARS_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_PROFILEAVATARS' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="user_avatar_storage">
						<option <?php echo ( $this->config->get('user_avatar_storage') == 'file' ) ? 'selected="true"' : ''; ?> value="file"><?php echo JText::_('COM_COMMUNITY_LOCALSERVER_OPTION');?></option>
						<option <?php echo ( $this->config->get('user_avatar_storage') == 's3' ) ? 'selected="true"' : ''; ?> value="s3"><?php echo JText::_('COM_COMMUNITY_AMAZONS3_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_GROUPAVATARS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_GROUPAVATARS_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_GROUPAVATARS' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="groups_avatar_storage">
						<option <?php echo ( $this->config->get('groups_avatar_storage') == 'file' ) ? 'selected="true"' : ''; ?> value="file"><?php echo JText::_('COM_COMMUNITY_LOCALSERVER_OPTION');?></option>
						<option <?php echo ( $this->config->get('groups_avatar_storage') == 's3' ) ? 'selected="true"' : ''; ?> value="s3"><?php echo JText::_('COM_COMMUNITY_AMAZONS3_OPTION');?></option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>