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
	<legend><?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK');?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_ENABLE' ); ?>
					</span>
				</td>
				<td><?php echo $this->lists['enable']; ?></td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_DESCRIPTION' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_DESCRIPTION_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_DESCRIPTION' ); ?>
					</span>
				</td>
				<td><input type="text" class="inputbox" name="network_description" id="description" size="80" value="<?php echo $this->JSNInfo['network_description']; ?>"></td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_TAGS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_TAGS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_TAGS' ); ?>
					</span>
				</td>
				<td><input type="text" class="inputbox" name="network_keywords" id="keywords" size="80" value="<?php echo $this->JSNInfo['network_keywords']; ?>"></td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_JOIN_URL' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_JOIN_URL_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_JOIN_URL' ); ?>
					</span>
				</td>
				<td>
					<input type="text" class="inputbox" name="network_join_url" id="join_url" size="80" value="<?php echo $this->JSNInfo['network_join_url'] ?>">
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_UPDATE_INTERVAL' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_UPDATE_INTERVAL_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_UPDATE_INTERVAL' ); ?>
					</span>
				</td>
				<td><input type="text" class="inputbox" name="network_cron_freq" id="cron_freq" value="<?php echo $this->JSNInfo['network_cron_freq']; ?>"> (<?php echo JText::_('COM_COMMUNITY_HOURS');?>)</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_UPLOAD_LOGO' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_UPLOAD_LOGO_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_UPLOAD_LOGO' ); ?>
					</span>
				</td>
				<td>
					<input class="inputbox" type="file" id="file-upload" name="network_Filedata" style="color: #666;" />
					<input type="checkbox" class="inputbox" name="network_replace_image" id="replace_image" value="1">
					<label for="replace_image"><?php echo JText::_('COM_COMMUNITY_REPLACE_IMAGE');?></label>
				</td>
			</tr>
			<?php if( $this->JSNInfo['network_logo_url'] ) { ?>
			<tr>
				<td valign="top"  width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_CURRENT_LOGO' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_CURRENT_LOGO_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_CURRENT_LOGO' ); ?>
					</span>
				</td>
				<td>
					<?php echo JHTML::_('image', $this->JSNInfo['network_logo_url'], '', ''); ?>
					<input type="hidden" name="network_logo_url" value="<?php echo $this->JSNInfo['network_logo_url'] ?>" />
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<input type="hidden" name="network_cron_last_run" value="<?php echo $this->JSNInfo['network_cron_last_run'] ?>" />
</fieldset>