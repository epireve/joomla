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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="350" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enablegroups' , null , $this->config->get('enablegroups') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="350" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_ALLOW_GUEST_SEARCH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_ALLOW_GUEST_SEARCH_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_ALLOW_GUEST_SEARCH' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enableguestsearchgroups' , null , $this->config->get('enableguestsearchgroups') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_MODERATION' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_MODERATION_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_MODERATION' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'moderategroupcreation' , null , $this->config->get('moderategroupcreation') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_ALLOW_CREATION' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_ALLOW_CREATION_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_ALLOW_CREATION' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'creategroups' , null , $this->config->get('creategroups') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_CREATION_LIMIT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_CREATION_LIMIT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_CREATION_LIMIT' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="groupcreatelimit" value="<?php echo $this->config->get('groupcreatelimit' );?>" size="10" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_PHOTO_UPLOAD_LIMIT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_PHOTO_UPLOAD_LIMIT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_PHOTO_UPLOAD_LIMIT' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="groupphotouploadlimit" value="<?php echo $this->config->get('groupphotouploadlimit' );?>" size="10" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_VIDEO_UPLOAD_LIMIT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_VIDEO_UPLOAD_LIMIT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_VIDEO_UPLOAD_LIMIT' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="groupvideouploadlimit" value="<?php echo $this->config->get('groupvideouploadlimit' );?>" size="10" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_ANNOUNCEMENTS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_ANNOUNCEMENTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_ANNOUNCEMENTS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'createannouncement' , null , $this->config->get('createannouncement') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_DISCUSSIONS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_DISCUSSIONS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_DISCUSSIONS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'creatediscussion' , null , $this->config->get('creatediscussion') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_PHOTOS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_PHOTOS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_PHOTOS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'groupphotos' , null , $this->config->get('groupphotos') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_VIDEOS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_VIDEOS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_VIDEOS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'groupvideos' , null , $this->config->get('groupvideos') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_EVENTS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_EVENTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_EVENTS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'group_events' , null , $this->config->get('group_events') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_DISCUSSION_NOTIFICATIONS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_GROUPS_DISCUSSION_NOTIFICATIONS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_DISCUSSION_NOTIFICATIONS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'groupdiscussnotification' , null , $this->config->get('groupdiscussnotification') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>