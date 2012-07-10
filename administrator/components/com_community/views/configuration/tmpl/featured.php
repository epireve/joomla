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
	<legend><?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FEATURED_LIMITS'); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_USERS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_USERS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_USERS' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="featureduserslimit" value="<?php echo $this->config->get('featureduserslimit' );?>" size="4" /> <?php echo JText::_('COM_COMMUNITY_USERS');?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_VIDEOS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_VIDEOS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_VIDEOS' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="featuredvideoslimit" value="<?php echo $this->config->get('featuredvideoslimit');?>" size="4" /> <?php echo JText::_('COM_COMMUNITY_VIDEOS');?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_GROUPS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_GROUPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_GROUPS' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="featuredgroupslimit" value="<?php echo $this->config->get('featuredgroupslimit' );?>" size="4" /> <?php echo JText::_('COM_COMMUNITY_GROUPS');?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_ALBUMS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_ALBUMS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FEATURED_MAXIMUM_ALBUMS' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="featuredalbumslimit" value="<?php echo $this->config->get('featuredalbumslimit' );?>" size="4" /> <?php echo JText::_('COM_COMMUNITY_ALBUMS');?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>