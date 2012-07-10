<?php
/**
 * @package	    JomSocial
 * @subpackage	    Template 
 * @copyright	    (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license	    GNU/GPL, see LICENSE.php
 *
 */
defined('_JEXEC') or die();
?>

<div class="cModule cPending">
    <h3><?php echo JText::_('COM_COMMUNITY_EVENTS_PENDING_INVITATIONS');?></h3>
        <ul class="cResetList clrfix">
	<?php
	if( $events )
	{
	    
		for( $i = 0; $i < count( $events ); $i++ )
		{
			$event	=&  $events[$i];
	?>
	<li class="jomNameTips" original-title="<?php echo $event->summary ?>">
	    <div class="list-right">
		<div class="small"><a href="<?php echo CRoute::_( 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id ); ?>" class="response"><?php echo JText::_('COM_COMMUNITY_GROUPS_INVITATION_RESPONSE');?></a></div>
	    </div>
	    <div class="list-left">
		<div class="small"><a href="<?php echo CRoute::_( 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id ); ?>"><?php echo $this->escape($event->title); ?></a></div>
		<div class="small"><?php echo JText::sprintf((cIsPlural($event->confirmedcount)) ? 'COM_COMMUNITY_EVENTS_MANY_GUEST_COUNT':'COM_COMMUNITY_EVENTS_GUEST_COUNT', $event->confirmedcount);?></div>
	    </div>
	</li>
	<?php
		}
	}else{
	?>
	<li class="small"><?php echo JText::_('COM_COMMUNITY_EVENTS_NO_INVITATIONS'); ?></li>
	<?php
	}
	?>
    </ul>
    <div class="app-box-footer">
	<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=events' ); ?>"><?php echo JText::_('COM_COMMUNITY_FRONTPAGE_VIEW_ALL_EVENTS'); ?></a>
    </div>
</div>