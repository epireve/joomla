<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @params	sortings	string	HTML code for the sorting
 * @params	groupsHTML	string HTML code for the group listings
 * @params	pagination	JPagination JPagination object 
 */
defined('_JEXEC') or die();
?>
<?php echo $sortings; ?>
<div class="cLayout clrfix">
	<!-- ALL MY GROUP LIST -->
	<div class="clrfix">
	<?php
	if( $groups )
	{
	?>
	<div>
		<?php echo JText::sprintf( CStringHelper::isPlural( $count ) ? 'COM_COMMUNITY_GROUPS_INVIT_COUNT_MANY' : 'COM_COMMUNITY_GROUPS_INVIT_COUNT' , $count ); ?>
	</div>
	<?php
		for( $i = 0; $i < count( $groups ); $i++ )
		{
			$group	=& $groups[$i]; 
	?>
	<div class="community-groups-results-item" id="groups-invite-<?php echo $group->id;?>">
		<div class="community-groups-results-left">
			<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );?>"><img class="cAvatar" src="<?php echo $group->getThumbAvatar();?>" alt="<?php echo $this->escape($group->name); ?>"/></a>
		</div>
		<div class="community-groups-results-right">
			<h3 class="groupName">
				<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );?>"><?php echo $group->name; ?></a>
			</h3>
			<div class="groupDescription"><?php echo $this->escape($group->description); ?></div>
			<div class="groupCreated small"><?php echo JText::sprintf('COM_COMMUNITY_GROUPS_CREATE_TIME_ON' , JHTML::_('date', $group->created, JText::_('DATE_FORMAT_LC')) );?></div>
            
			<div class="groupActions">
				<span class="jsIcon1 icon-group" style="margin-right: 5px;">
					<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=groups&task=viewmembers&groupid=' . $group->id ); ?>"><?php echo JText::sprintf((CStringHelper::isPlural($group->membercount)) ? 'COM_COMMUNITY_GROUPS_MEMBER_COUNT_MANY':'COM_COMMUNITY_GROUPS_MEMBER_COUNT', $group->membercount);?></a>
				</span>
				<span class="jsIcon1 icon-discuss" style="margin-right: 5px;">
					<?php echo JText::sprintf((CStringHelper::isPlural($group->discusscount)) ? 'COM_COMMUNITY_GROUPS_DISCUSSION_COUNT_MANY' :'COM_COMMUNITY_GROUPS_DISCUSSION_COUNT', $group->discusscount);?>
				</span>
				<span class="jsIcon1 icon-wall" style="margin-right: 5px;">
					<?php echo JText::sprintf((CStringHelper::isPlural($group->wallcount)) ? 'COM_COMMUNITY_GROUPS_WALL_COUNT_MANY' : 'COM_COMMUNITY_GROUPS_WALL_COUNT', $group->wallcount);?>
				</span>
			</div>
			
			<div class="community-groups-pending-actions">
				<a class="jsIcon1 icon-add-friend" href="javascript:void(0);" onclick="joms.groups.invitation.accept('<?php echo $group->id;?>');"><?php echo JText::_('COM_COMMUNITY_EVENTS_ACCEPT');?></a>
				<a class="icon-remove" href="javascript:void(0);" onclick="joms.groups.invitation.reject('<?php echo $group->id;?>');"><?php echo JText::_('COM_COMMUNITY_EVENTS_REJECT');?></a>
			</div>

			<div id="group-invite-notice"></div>
		</div>
		<div style="clear: both;"></div>
	</div>
	<?php
		}
	}
	else
	{
	?>
		<div class="group-not-found"><?php echo JText::_('COM_COMMUNITY_GROUPS_NO_INVITATIONS'); ?></div>
	<?php
	}
	?>
		<div class="pagination-container">
			<?php echo $pagination->getPagesLinks(); ?>
		</div>
	</div>
	<div class="clr"></div>
</div>