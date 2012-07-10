<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	$groupId	The current group id.
 */
defined('_JEXEC') or die();
?>
<div class="cModule">
	<p class="info"><?php echo JText::_('COM_COMMUNITY_GROUPS_AVATAR_UPLOAD_DESC');?></p>
	<form name="jsform-groups-uploadavatar" action="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=uploadavatar');?>" method="post" enctype="multipart/form-data">
	    <?php echo $beforeFormDisplay;?>
		<input type="file" name="filedata" size="40" class="button" />
		<?php echo $afterFormDisplay;?>
	    <input type="submit" value="<?php echo JText::_('COM_COMMUNITY_UPLOAD_BUTTON');?>" class="button" />
	    <input type="hidden" name="groupid" value="<?php echo $groupId; ?>" />
	    <input type="hidden" name="action" value="avatar"/>
	</form>
	<p class="info"><?php echo JText::sprintf('COM_COMMUNITY_MAX_FILE_SIZE_FOR_UPLOAD' , $uploadLimit ); ?></p>
</div>

<div class="cModule avatarPreview leftside">
	<h3><?php echo JText::_('COM_COMMUNITY_GROUPS_AVATAR_LARGE');?></h3>
	<p><?php echo JText::_('COM_COMMUNITY_LARGE_PICTURE_DESCRIPTION');?></p>
	<img src="<?php echo $avatar;?>" alt="<?php echo JText::_('COM_COMMUNITY_GROUPS_AVATAR_LARGE');?>" border="0" />
</div>

<div class="cModule avatarPreview rightside">
	<h3><?php echo JText::_('COM_COMMUNITY_GROUPS_AVATAR');?></h3>
	<img src="<?php echo $thumbnail;?>" alt="<?php echo JText::_('COM_COMMUNITY_AVATAR_THUMBNAIL_TITLE');?>" border="0" />
</div>