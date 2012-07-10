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
<table width="100%" border="0">
	<tr>
		<td width="55%" valign="top">
			<div id="cpanel">
				<?php echo $this->addIcon('configuration.gif','index.php?option=com_community&view=configuration', JText::_('COM_COMMUNITY_CONFIGURATION'));?>
				<?php echo $this->addIcon('edit-user.gif','index.php?option=com_community&view=users', JText::_('COM_COMMUNITY_USERS'));?>
				<?php echo $this->addIcon('multiprofile.gif','index.php?option=com_community&view=multiprofile', JText::_('COM_COMMUNITY_CONFIGURATION_MULTIPROFILES'));?>
				<?php echo $this->addIcon('profiles.gif','index.php?option=com_community&view=profiles', JText::_('COM_COMMUNITY_CUSTOM_PROFILES'));?>
				<?php echo $this->addIcon('groups.gif','index.php?option=com_community&view=groups', JText::_('COM_COMMUNITY_GROUPS'));?>
				<?php echo $this->addIcon('groupcategories.gif','index.php?option=com_community&view=groupcategories', JText::_('COM_COMMUNITY_GROUP_CATEGORIES'));?>
				<?php echo $this->addIcon('videos.gif','index.php?option=com_community&view=videoscategories', JText::_('COM_COMMUNITY_VIDEO_CATEGORIES'));?>
				<?php echo $this->addIcon('templates.gif','index.php?option=com_community&view=templates', JText::_('COM_COMMUNITY_TEMPLATES'));?>
				<?php echo $this->addIcon('applications.gif','index.php?option=com_community&view=applications', JText::_('COM_COMMUNITY_APPLICATIONS'));?>
				<?php echo $this->addIcon('event.gif','index.php?option=com_community&view=events', JText::_('COM_COMMUNITY_EVENTS'));?>
				<?php echo $this->addIcon('eventcategories.gif','index.php?option=com_community&view=eventcategories', JText::_('COM_COMMUNITY_EVENT_CATEGORIES'));?>
				<?php echo $this->addIcon('mailq.gif','index.php?option=com_community&view=mailqueue', JText::_('COM_COMMUNITY_MAIL_QUEUE'));?>
				<?php echo $this->addIcon('reports.gif','index.php?option=com_community&view=reports', JText::_('COM_COMMUNITY_REPORTINGS')); ?>
				<?php echo $this->addIcon('userpoints.gif','index.php?option=com_community&view=userpoints', JText::_('COM_COMMUNITY_USERPOINTS')); ?>
				<?php echo $this->addIcon('message.gif','index.php?option=com_community&view=messaging', JText::_('COM_COMMUNITY_MASSMESSAGING')); ?>
				<?php echo $this->addIcon('activities.gif','index.php?option=com_community&view=activities', JText::_('COM_COMMUNITY_ACTIVITIES')); ?>
				<?php echo $this->addIcon('memberlist.gif','index.php?option=com_community&view=memberlist', JText::_('COM_COMMUNITY_MEMBERLIST')); ?>
				<?php echo $this->addIcon('about.gif','index.php?option=com_community&view=about', JText::_('COM_COMMUNITY_ABOUT')); ?>
				<?php echo $this->addIcon('help.gif','http://www.jomsocial.com/support/docs.html', JText::_('COM_COMMUNITY_HELP'), true ); ?>
			</div>
		</td>
		<td width="45%" valign="top">
			<?php
				echo $this->pane->startPane( 'stat-pane' );
				echo $this->pane->startPanel( JText::_('COM_COMMUNITY_WELCOME_TO_JOMSOCIAL') , 'welcome' );
			?>
			<table class="adminlist">
				<tr>
					<td>
						<div style="font-weight:700;">
							<?php echo JText::_('COM_COMMUNITY_GREAT_COMPONENT_MSG');?>
						</div>
						<p>
							If you require professional support just head on to the forums at 
							<a href="http://www.jomsocial.com/forum/" target="_blank">
							http://www.jomsocial.com/forum
							</a>
							For developers, you can browse through the documentations at 
							<a href="http://www.jomsocial.com/support/docs.html" target="_blank">http://www.jomsocial.com/support/docs.html</a>
						</p>
						<p>
							If you found any bugs, just drop us an email at bugs@azrul.com
						</p>
					</td>
				</tr>
			</table>
			<?php
				echo $this->pane->endPanel();
				echo $this->pane->startPanel( JText::_('COM_COMMUNITY_STATISTICS') , 'community' );
			?>
				<table class="adminlist">
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_TOTAL_USERS' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->community->total; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_TOTAL_BLOCKED_USERS' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->community->blocked; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_TOTAL_APPLICATIONS_INSTALLED' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->community->applications; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_TOTAL_ACTIVITY_UPDATES' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->community->updates; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_PHOTOS_TOTAL' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->community->photos; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_VIDEOS_TOTAL' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->community->videos; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_GROUPS_T0TAL_DISCUSSIONS' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->community->groupDiscussion; ?></strong>
						</td>
					</tr>
				</table>

			<?php
				echo $this->pane->endPanel();
				echo $this->pane->startPanel( JText::_('COM_COMMUNITY_GROUPS_STATISTICS'), 'groups' );
			?>
				<table class="adminlist">
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_GROUPS_PUBLISHED' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->groups->published; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_GROUPS_UNPUBLISHED' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->groups->unpublished; ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_( 'COM_COMMUNITY_GROUP_CATEGORIES' ).': '; ?>
						</td>
						<td align="center">
							<strong><?php echo $this->groups->categories; ?></strong>
						</td>
					</tr>
				</table>
			<?php
				echo $this->pane->endPanel();
				echo $this->pane->endPane();
			?>
		</td>
	</tr>
</table>
