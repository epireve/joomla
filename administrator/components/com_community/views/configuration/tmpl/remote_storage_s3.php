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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="350" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_BUCKET_PATH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_BUCKET_PATH_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_BUCKET_PATH' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="storages3bucket" value="<?php echo $this->config->get('storages3bucket' , '' );?>" size="50" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_ACCESS_KEY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_ACCESS_KEY_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_ACCESS_KEY' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="storages3accesskey" value="<?php echo $this->config->get('storages3accesskey' , '' );?>" size="50" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_SECRET_KEY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_SECRET_KEY_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_SECRET_KEY' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="storages3secretkey" value="<?php echo $this->config->get('storages3secretkey' , '' );?>" size="50" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_CLASS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_CLASS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_STORAGE_AMAZONS3_CLASS' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="amazon_storage_class">
						<option <?php echo ( $this->config->get('amazon_storage_class') == 'STANDARD' ) ? 'selected="true"' : ''; ?> value="STANDARD"><?php echo JText::_('COM_COMMUNITY_STANDARD_OPTION');?></option>
						<option <?php echo ( $this->config->get('amazon_storage_class') == 'REDUCED_REDUNDANCY' ) ? 'selected="true"' : ''; ?> value="REDUCED_REDUNDANCY"><?php echo JText::_('COM_COMMUNITY_REDUCED_REDUNDANCY_OPTION');?></option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>