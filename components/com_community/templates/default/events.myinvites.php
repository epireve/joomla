<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 */
defined('_JEXEC') or die();
?>

<?php echo $sortings; ?>
<div id="community-events-wrap">
	<?php
	if( $events )
	{
	?>
	<div>
		<?php echo JText::sprintf( CStringHelper::isPlural( $count ) ? 'COM_COMMUNITY_EVENTS_INVITATION_COUNT_MANY' : 'COM_COMMUNITY_EVENTS_INVITATION_COUNT_SINGLE' , $count ); ?>
	</div>
	<?php
		for( $i = 0; $i < count( $events ); $i++ )
		{
			$event	=& $events[$i];
	?>
		<div class="community-events-results-item" id="events-invite-<?php echo $event->id;?>">
			<div class="community-events-results-left">
				<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id );?>"><img class="cAvatar-Large" src="<?php echo $event->getThumbAvatar();?>" border="0" alt="<?php echo $this->escape($event->title); ?>"/></a>
				<div class="eventDate"><?php echo CEventHelper::formatStartDate($event, '%b %d'); ?></div>
			</div>
			<div class="community-events-results-right">
				<h3 class="eventName">
					<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id );?>"><strong><?php echo $this->escape($event->title); ?></strong></a>
				</h3>
				<div class="eventLocation"><?php echo $this->escape($event->location); ?></div>
	            <div class="eventTime"><?php echo JText::sprintf('COM_COMMUNITY_EVENTS_DURATION', JHTML::_('date', $event->startdate, JText::_('DATE_FORMAT_LC2')), JHTML::_('date', $event->enddate, JText::_('DATE_FORMAT_LC2'))); ?></div>
	            <?php if( $config->get('eventshowtimezone') ) { ?>
				<?php } ?>
				<div class="eventActions">
					<span class="jsIcon1 icon-group" style="margin-right: 5px;">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=events&task=viewguest&eventid=' . $event->id . '&type='.COMMUNITY_EVENT_STATUS_ATTEND);?>"><?php echo JText::sprintf((cIsPlural($event->confirmedcount)) ? 'COM_COMMUNITY_EVENTS_MANY_GUEST_COUNT':'COM_COMMUNITY_EVENTS_GUEST_COUNT', $event->confirmedcount);?></a>
					</span>
				</div>
			</div>
			<div class="community-events-pending-actions">		
				<a class="jsIcon1 icon-add-friend" href="javascript:void(0);" onclick="joms.events.joinNow('<?php echo $event->id; ?>');"><?php echo JText::_('COM_COMMUNITY_EVENTS_ACCEPT'); ?></a>
				<a class="icon-remove" href="javascript:void(0);" onclick="joms.events.rejectNow('<?php echo $event->id; ?>');"><?php echo JText::_('COM_COMMUNITY_EVENTS_REJECT'); ?></a>
			</div>
	
			<?php if($event->isExpired() ) { ?>
			    <span class="icon-offline-overlay">&nbsp;<?php echo JText::_('COM_COMMUNITY_EVENTS_PAST'); ?>&nbsp;</span>
			<?php } else if(CEventHelper::isToday($event)) { ?>
				<span class="icon-online-overlay">&nbsp;<?php echo JText::_('COM_COMMUNITY_EVENTS_ONGOING'); ?>&nbsp;</span>
			<?php }?>
			<div style="clear: both;"></div>
		</div>
	<?php
		}
	}
	else
	{
	?>
		<div class="event-not-found"><?php echo JText::_('COM_COMMUNITY_EVENTS_NO_INVITATIONS'); ?></div>
	<?php } ?>
	<div class="pagination-container">
		<?php echo $pagination->getPagesLinks(); ?>
	</div>
</div>