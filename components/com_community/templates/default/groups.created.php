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
<div class="empty-message"><?php echo JText::_('COM_COMMUNITY_GROUPS_CREATE_SUCCESS');?></div>

<ul class="linklist">
	<li class="upload_avatar">
		<a href="<?php echo $linkUpload; ?>"><?php echo JText::_('COM_COMMUNITY_GROUPS_UPLOAD_AVATAR');?></a>
	</li>
	<li class="add_news">
		<a href="<?php echo $linkBulletin; ?>"><?php echo JText::_('COM_COMMUNITY_GROUPS_BULLETIN_CREATE');?></a>
	</li>
	<li class="group_edit">
		<a href="<?php echo $linkEdit;?>">
			<?php echo JText::_('COM_COMMUNITY_GROUPS_EDIT_DESC');?>
		</a>
	</li>
	<li class="group_view">
		<a href="<?php echo $link; ?>">
			<?php echo JText::_('COM_COMMUNITY_GROUPS_GOTO');?>
		</a>
	</li>
</ul>