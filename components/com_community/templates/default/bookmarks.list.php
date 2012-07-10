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
<div style="margin-bottom: 5px; font-weight: 700;"><?php echo JText::_('COM_COMMUNITY_SHARE_THIS_VIA_LINK');?></div>
<ul class="bookmarks-list">
	<?php
	foreach($bookmarks as $bookmark)
	{
	?>
	<li><a href="<?php echo $bookmark->link;?>" target="_blank" class="<?php echo $bookmark->className;?>"><?php echo $this->escape($bookmark->name); ?></a></li>
	<?php
	}
	?>
</ul>
<div class="clr"></div>
<form id="bookmarks-email">
<div style="margin-bottom: 5px; font-weight: 700;"><?php echo JText::_('COM_COMMUNITY_SHARE_THIS_VIA_EMAIL');?></div>
<div><input type="text" id="bookmarks-email" name="bookmarks-email" class="bookmarks-email" /></div>
<div style="margin-bottom: 5px;"><?php echo JText::_('COM_COMMUNITY_SHARE_THIS_VIA_EMAIL_INFO');?></div>
<div style="margin-bottom: 5px; font-weight: 700;"><?php echo JText::_('COM_COMMUNITY_SHARE_THIS_MESSAGE');?></div>
<div><textarea rows="3" class="bookmarks-message" id="bookmarks-message" name="bookmarks-message"></textarea></div>
</form>