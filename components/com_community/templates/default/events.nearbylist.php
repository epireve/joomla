<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die( 'Restricted Access' );
?>


<h3><?php echo JText::sprintf('COM_COMMUNITY_EVENTS_NEARBY_RADIUS', $location, $radius); ?></h3>
<div class="app-box-content">
	<ul class="cTextList">
		<?php if( $events ){ ?>
		<?php
		for( $i=0; $i<count( $events ); $i++ ){

		    $event	=&  $events[$i];
		    $creator	=   CFactory::getUser($event->creator);

		?>
		<li class="cDiscussion-list">
			<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $event->id );?>"><?php echo $this->escape($event->title); ?></a>
			<div class="small">
				<?php echo JText::sprintf('COM_COMMUNITY_ORGANIZED_BY', $event->location, $creator->getDisplayName(), CRoute::_('index.php?option=com_community&view=profile&userid=' . $creator->id)); ?>
			</div>
		</li>
		<?php } ?>
		<?php }else{ echo JText::_('COM_COMMUNITY_EVENTS_NO_NEARBY'); }?>
	</ul>
</div>


