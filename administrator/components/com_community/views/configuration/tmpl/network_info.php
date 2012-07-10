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
	<legend><?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_INFO');?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_NAME' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_NAME_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_NAME' ); ?>
					</span>
				</td>
				<td><?php echo $this->JSNInfo['network_site_name']; ?></td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_URL' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_URL_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_URL' ); ?>
					</span>
				</td>
				<td><a href="<?php echo $this->JSNInfo['network_site_url'] ?>" target="_blank"><?php echo $this->JSNInfo['network_site_url'] ?></a></td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_LANGUAGE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_LANGUAGE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_LANGUAGE' ); ?>
					</span>
				</td>
				<td><?php echo $this->JSNInfo['network_language']; ?></td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_MEMBERS_COUNT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_MEMBERS_COUNT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_MEMBERS_COUNT' ); ?>
					</span>
				</td>
				<td><?php echo $this->JSNInfo['network_member_count']; ?></td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_GROUPS_COUNT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_GROUPS_COUNT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_JSNETWORK_SITE_GROUPS_COUNT' ); ?>
					</span>
				</td>
				<td><?php echo $this->JSNInfo['network_group_count']; ?></td>
			</tr>
		</tbody>
	</table>
</fieldset>