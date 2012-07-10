 <?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<div id="community-event-join">
	<?php if($isMember){ ?>
		<p><?php echo JText::_('COM_COMMUNITY_EVENTS_ALREADY_MEMBER'); ?></p>
	<?php }else{ ?>
		<p><?php echo JText::sprintf('COM_COMMUNITY_EVENTS_CONFIRM_INVITATION_REQUEST', $event->title );?></p>
	<?php } ?>
</div>