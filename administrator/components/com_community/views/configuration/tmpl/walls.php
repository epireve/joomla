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
	<legend><?php echo JText::_('COM_COMMUNITY_CONFIGURATION_WALLS'); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EDIT_WALLS_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EDIT_WALLS_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EDIT_WALLS_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'wallediting' , null , $this->config->get('wallediting') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_LOCK_WALLS_TO_FRIENDS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_LOCK_WALLS_TO_FRIENDS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_LOCK_WALLS_TO_FRIENDS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'lockprofilewalls' , null , $this->config->get('lockprofilewalls') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_LOCK_VIDEO_WALLS_TO_FRIENDS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_LOCK_VIDEO_WALLS_TO_FRIENDS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_LOCK_VIDEO_WALLS_TO_FRIENDS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'lockvideoswalls' , null , $this->config->get('lockvideoswalls') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_LOCK_GROUP_WALLS_TO_MEMBERS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_LOCK_GROUP_WALLS_TO_MEMBERS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_LOCK_GROUP_WALLS_TO_MEMBERS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'lockgroupwalls' , null , $this->config->get('lockgroupwalls') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_LOCK_EVENT_WALLS_TO_RECIPIENTS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_LOCK_EVENT_WALLS_TO_RECIPIENTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_LOCK_EVENT_WALLS_TO_RECIPIENTS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'lockeventwalls' , null , $this->config->get('lockeventwalls') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_WALLS_AUTO_REFRESH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_WALLS_AUTO_REFRESH_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_WALLS_AUTO_REFRESH' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enable_refresh' , null , $this->config->get('enable_refresh') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
            <tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_WALLS_INTERVAL_TIME' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_WALLS_INTERVAL_TIME_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_WALLS_INTERVAL_TIME' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="stream_refresh_interval" value="<?php echo $this->config->get('stream_refresh_interval' );?>" size="4" />
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>