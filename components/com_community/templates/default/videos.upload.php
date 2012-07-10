<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>


<form name="uploadVideo" id="uploadVideo" method="post" action="<?php echo CRoute::_('index.php?option=com_community&view=videos&task=upload');?>" enctype="multipart/form-data">


<table class="cWindowForm" cellspacing="1" cellpadding="0">

<!-- video file -->
<tr>
	<td class="cWindowFormKey">
		<label for="file" class="label title">
			*<?php echo JText::_('COM_COMMUNITY_VIDEOS_SELECT_VIDEO_FILE');?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<input type="file" name="videoFile" id="file" class="inputbox required" />
		<div class="hints"><?php echo JText::sprintf('COM_COMMUNITY_MAXIMUM_UPLOAD_LIMIT', $uploadLimit); ?></div>
	</td>
</tr>


<!-- video title -->
<tr>
	<td class="cWindowFormKey">
		<label for="videoTitle" class="label title">
			*<?php echo JText::_('COM_COMMUNITY_VIDEOS_TITLE'); ?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<input type="text" id="videoTitle" name="title" class="inputbox required" size="35" />
	</td>
</tr>


<!-- video description -->
<tr>
	<td class="cWindowFormKey">
		<label for="description" class="label title">
			<?php echo JText::_('COM_COMMUNITY_VIDEOS_DESCRIPTION'); ?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<textarea id="description" name="description" class="inputbox fullwidth"></textarea>
	</td>
</tr>

<!-- location -->
<?php if ($enableLocation) { ?>
<tr>
	<td class="cWindowFormKey">
		<label for="location" class="label title">
			<?php echo JText::_('COM_COMMUNITY_VIDEOS_LOCATION');?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<input name="location" id="location" type="text" size="35" value ="" class="inputbox"/>
		<div class="small"><?php echo JText::_('COM_COMMUNITY_VIDEOS_LOCATION_DESCRIPTION'); ?></div>
	</td>
</tr>
<?php } ?>

<!-- video category -->
<tr>
	<td class="cWindowFormKey">
		<label for="category" class="label title">
			<?php echo JText::_('COM_COMMUNITY_VIDEOS_CATEGORY'); ?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<?php echo $list['category']; ?>
	</td>
</tr>


<?php if ($creatorType != VIDEO_GROUP_TYPE) { ?>
<!-- video privacy -->
<tr>
	<td class="cWindowFormKey">
		<label for="category" class="label title">
			<?php echo JText::_('COM_COMMUNITY_VIDEOS_WHO_CAN_SEE'); ?>
		</label>
	</td>
	<td class="cWindowFormVal">
		<?php echo CPrivacy::getHTML( 'permissions', $permissions, COMMUNITY_PRIVACY_BUTTON_LARGE ); ?>
	</td>
</tr>
<?php } ?>

<tr>
	<td class="key"></td>
	<td class="value"><span class="hints"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></span></td>
</tr>
<tr>
	<td class="key"></td>
	<?php if($videoUploadLimit > 0 && $videoUploaded/$videoUploadLimit>=COMMUNITY_SHOW_LIMIT) { ?>
	<td class="value"><div class="hints"><?php echo JText::sprintf('COM_COMMUNITY_VIDEOS_UPLOAD_LIMIT_STATUS', $videoUploaded, $videoUploadLimit ); ?></div></td>
	<?php } ?>
</tr>
</table>

<input type="hidden" name="creatortype" value="<?php echo $creatorType; ?>" />
<input type="hidden" name="groupid" value="<?php echo $groupid; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

</form>