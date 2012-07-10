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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_TITLE' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ENABLE_AKISMET' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_ENABLE_AKISMET_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ENABLE_AKISMET' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'antispam_enable' , null , $this->config->get('antispam_enable') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_KEY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_AKISMET_KEY_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_KEY' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="antispam_akismet_key" value="<?php echo $this->config->get( 'antispam_akismet_key' );?>" size="50" />
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'antispam_akismet_messages' , null , $this->config->get('antispam_akismet_messages') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_FRIEND_REQUESTS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_FRIEND_REQUESTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_FRIEND_REQUESTS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'antispam_akismet_friends' , null , $this->config->get('antispam_akismet_friends') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_WALL_POSTS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_WALL_POSTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_WALL_POSTS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'antispam_akismet_walls' , null , $this->config->get('antispam_akismet_walls') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_STATUS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_STATUS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_STATUS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'antispam_akismet_status' , null , $this->config->get('antispam_akismet_status') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_DISCUSSIONS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_DISCUSSIONS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_AKISMET_FILTER_DISCUSSIONS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'antispam_akismet_discussions' , null , $this->config->get('antispam_akismet_discussions') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>