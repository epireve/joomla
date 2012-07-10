<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();

if( !empty( $events ) )
{
?>
<h3><?php echo JText::_('COM_COMMUNITY_EVENTS_UPCOMING');?></h3>
<ul class="cResetList clrfix">
	<?php foreach( $events as $event ){ ?>
	
	<li <?php if(!empty($event->summary)): ?>class="jomNameTips" title="<?php echo $this->escape( $event->summary);?>" <?php endif; ?>>
		<div class="jsEvDate">
			<div class="jsMM"><?php echo CEventHelper::formatStartDate($event, JText::_('%b') ); ?></div>
			<div class="jsDD"><?php echo CEventHelper::formatStartDate($event, JText::_('%d') ); ?></div>
		</div>
		<div class="jsDetail" style="margin-left:45px">
			<div class="small">
				<b><a href="<?php echo $event->getLink();?>"><?php echo $this->escape( $event->title ); ?></a></b>
			</div>
			<div class="small">
				<?php echo $this->escape( $event->location );?>
			</div>
			<div class="small">
				<a href="<?php echo $event->getGuestLink( COMMUNITY_EVENT_STATUS_ATTEND );?>">
					<?php echo JText::sprintf((cIsPlural($event->confirmedcount)) ? 'COM_COMMUNITY_EVENTS_ATTANDEE_COUNT_MANY':'COM_COMMUNITY_EVENTS_ATTANDEE_COUNT', $event->confirmedcount);?>
				</a>
			</div>
		</div>
		<div class="clr"></div>
	</li>
	<?php } ?>
</ul>
<div class="app-box-footer">
	<a href="<?php echo CRoute::_('index.php?option=com_community&view=events'); ?>"><?php echo JText::_('COM_COMMUNITY_FRONTPAGE_VIEW_ALL_EVENTS'); ?></a>
</div>

<?php
} ?>