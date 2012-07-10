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
<div class="empty-message"><?php echo JText::_('COM_COMMUNITY_EVENTS_CREATED_DESCRIPTION');?></div>

<ul class="linklist">
	<li class="upload_avatar">
		<a href="<?php echo $linkUpload; ?>"><?php echo JText::_('COM_COMMUNITY_EVENTS_UPLOAD_AVATAR');?></a>
	</li>
	<li class="event_invite">
		<a href="<?php echo $linkInvite; ?>"><?php echo JText::_('COM_COMMUNITY_INVITE_FRIENDS');?></a>
	</li>
	<li class="event_edit">
		<a href="<?php echo $linkEdit;?>">
			<?php echo JText::_('COM_COMMUNITY_EVENTS_EDIT_DETAILS');?>
		</a>
	</li>
	<li class="event_view">
		<a href="<?php echo $link; ?>">
			<?php echo JText::_('COM_COMMUNITY_EVENTS_VIEW');?>
		</a>
	</li>
</ul>